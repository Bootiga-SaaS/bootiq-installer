# Bootiq Installer

Bootiq Installer connects the theme, storefront blocks, dependencies, and configuration supplied by the [Bootiq storefront package](https://github.com/Bootiga-SaaS/bootiq).

It enables required modules, installs Bootiq as the default frontend theme, imports storefront configuration, places navigation and footer blocks, and configures editable Layout Builder sections. It does not create products, articles, stores, users, or other demo content.

## Install

The installer is normally installed through the Bootiq metapackage:

~~~bash
composer require bootiga/bootiq:^1.0
./vendor/drush/drush/drush en bootiq_installer -y
./vendor/drush/drush/drush bootiq:install
~~~

Use **bootiq:install --force** only when Bootiq should replace an existing Kickstart storefront and its Layout Builder displays. Without **--force**, existing storefront configuration is preserved.

## Requirements

- Commerce Kickstart 5.1.
- Drupal 10 or 11.
- Composer 2.
- Drush supplied by the target project.

## About Bootiga

Bootiq is developed and maintained by [Bootiga](https://www.bootiga.com), a hosted Drupal Commerce platform built around open-source ownership, portable stores, and no additional platform commission on store sales.

- [Bootiga](https://www.bootiga.com)
- [Documentation](https://www.bootiga.com/docs)
- [Complete Bootiq package](https://github.com/Bootiga-SaaS/bootiq)

## Contributing

Report installation issues in [GitHub Issues](https://github.com/Bootiga-SaaS/bootiq-installer/issues).

## License

GPL-2.0-or-later.
