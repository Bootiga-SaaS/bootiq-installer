<?php

namespace Drupal\bootiq_installer\Commands;

use Drupal\Component\Serialization\Yaml;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\ModuleExtensionList;
use Drupal\Core\Extension\ThemeInstallerInterface;
use Drupal\layout_builder\Section;
use Drupal\layout_builder\SectionComponent;
use Drush\Commands\DrushCommands;

/**
 * Drush commands for installing Bootiq.
 */
final class BootiqInstallerCommands extends DrushCommands {

  /**
   * Constructs a Bootiq installer command handler.
   */
  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly ModuleExtensionList $moduleList,
    private readonly ThemeInstallerInterface $themeInstaller,
  ) {
    parent::__construct();
  }

  /**
   * Installs Bootiq theme and base configuration.
   *
   * @command bootiq:install
   * @aliases bootiq-install
   * @option force Overwrite Bootiq configuration and reset the frontpage layout.
   * @usage drush bootiq:install
   *   Installs Bootiq without demo content.
   * @usage drush bootiq:install --force
   *   Replaces an existing Kickstart frontpage layout with Bootiq sections.
   */
  public function install(array $options = ['force' => FALSE]): void {
    $force = (bool) $options['force'];

    $this->themeInstaller->install(['bootiq']);
    $this->configFactory->getEditable('system.theme')
      ->set('default', 'bootiq')
      ->save(TRUE);

    $imported = $this->importOptionalConfig($force);
    $this->configureFrontpageLayout($force);
    drupal_flush_all_caches();

    $this->logger()->success(dt('Bootiq is installed as the default theme.'));
    $this->logger()->notice(dt('Imported @count configuration objects. No demo content was created.', ['@count' => $imported]));
  }

  /**
   * Imports optional Bootiq configuration.
   */
  private function importOptionalConfig(bool $force): int {
    $directory = $this->moduleList->getPath('bootiq_installer') . '/config/optional';
    $files = glob($directory . '/*.yml') ?: [];
    $count = 0;

    foreach ($files as $file) {
      $name = basename($file, '.yml');
      $config = $this->configFactory->getEditable($name);
      if (!$force && !$config->isNew()) {
        continue;
      }

      $data = Yaml::decode(file_get_contents($file));
      if (!is_array($data)) {
        continue;
      }
      unset($data['uuid'], $data['_core']);

      $config->setData($data)->save(TRUE);
      $count++;
    }

    return $count;
  }

  /**
   * Installs the editable Bootiq frontpage layout.
   */
  private function configureFrontpageLayout(bool $reset_override): void {
    $display = \Drupal::entityTypeManager()
      ->getStorage('entity_view_display')
      ->load('node.cklb_landing_page.default');

    if (!$display || !method_exists($display, 'removeAllSections')) {
      return;
    }

    $base_settings = [];
    if ($display->getSections()) {
      $base_settings = $display->getSection(0)->getLayoutSettings();
    }

    $definitions = [
      [
        'label' => 'Bootiq hero',
        'plugin' => 'bootiq_hero',
        'container' => 'container-fluid',
        'remove_gutters' => '1',
        'configuration' => [],
      ],
      [
        'label' => 'Recent products',
        'plugin' => 'bootiq_product_grid',
        'container' => 'container',
        'remove_gutters' => '0',
        'configuration' => [
          'title' => 'Recent products',
          'sort' => 'recent',
          'rows' => 2,
        ],
      ],
      [
        'label' => 'Latest from our blog',
        'label_display' => 'visible',
        'plugin' => 'views_block:blog-latest_blog_block',
        'container' => 'container',
        'remove_gutters' => '0',
        'configuration' => [
          'views_label' => '',
          'items_per_page' => 'none',
        ],
      ],
      [
        'label' => 'The look',
        'plugin' => 'bootiq_the_look',
        'container' => 'container-fluid',
        'remove_gutters' => '1',
        'configuration' => [],
      ],
    ];

    $display->removeAllSections();
    foreach ($definitions as $definition) {
      $uuid = \Drupal::service('uuid')->generate();
      $settings = $base_settings;
      $settings['label'] = $definition['label'];
      $settings['container'] = $definition['container'] ?? 'container';
      $settings['remove_gutters'] = $definition['remove_gutters'] ?? '0';

      $configuration = $definition['configuration'] + [
        'id' => $definition['plugin'],
        'label' => $definition['label'],
        'label_display' => $definition['label_display'] ?? '0',
        'provider' => str_starts_with($definition['plugin'], 'views_block:') ? 'views' : 'bootiq_product_blocks',
        'context_mapping' => [],
      ];

      $component = new SectionComponent($uuid, 'blb_region_col_1', $configuration);
      $display->appendSection(new Section(
        'bootstrap_layout_builder:blb_col_1',
        $settings,
        [$uuid => $component],
      ));
    }
    $display->save();

    if ($reset_override) {
      $this->resetFrontpageOverride();
    }
  }

  /**
   * Removes only the current frontpage entity override.
   */
  private function resetFrontpageOverride(): void {
    $front = (string) $this->configFactory->get('system.site')->get('page.front');
    $internal_path = \Drupal::service('path_alias.manager')->getPathByAlias($front);
    if (!preg_match('#^/node/(\d+)$#', $internal_path, $matches)) {
      return;
    }

    $node = \Drupal::entityTypeManager()->getStorage('node')->load((int) $matches[1]);
    if (!$node || $node->bundle() !== 'cklb_landing_page' || !$node->hasField('layout_builder__layout')) {
      return;
    }

    $node->set('layout_builder__layout', []);
    $node->save();
    $tempstore_name = 'node.' . $node->id() . '.default.' . $node->language()->getId();
    foreach (['key_value', 'key_value_expire'] as $table) {
      if (!\Drupal::database()->schema()->tableExists($table)) {
        continue;
      }
      \Drupal::database()->delete($table)
        ->condition('collection', 'tempstore.shared.layout_builder.section_storage.overrides')
        ->condition('name', $tempstore_name)
        ->execute();
    }
  }

}
