# Bootiq

Bootiq is a premium storefront package for Drupal Commerce Kickstart. It installs the theme, reusable Layout Builder blocks, and the installer that connects them.

Bootiq does not create products, articles, stores, users, or other demo content.

## Requirements

- Commerce Kickstart 5.1.
- Drupal 10 or 11.
- PHP and extensions required by Commerce Kickstart.
- Composer 2.
- Drush supplied by the target project.

The metapackage installs all direct Drupal and Bootiq dependencies.

## Install

After the packages are published:

```bash
composer require bootiga/bootiq:^1.0
./vendor/drush/drush/drush en bootiq_installer -y
./vendor/drush/drush/drush bootiq:install
```

Use `bootiq:install --force` on an existing Kickstart demo when Bootiq should replace its storefront configuration and Layout Builder displays. Without `--force`, existing storefront configuration is preserved.

## What the installer configures

- Bootiq as the default frontend theme.
- Header, navigation, search, cart, account, footer, and payment blocks.
- Editable frontpage sections: Hero, product grid, blog view, and The Look.
- Editable similar-product blocks on the default, physical, and media product displays.
- Product catalog, cart, login, legal-page, and responsive storefront styling.
- Image upload fields for editable Hero and The Look blocks.

## Package layout

- `bootiga/bootiq`: metapackage.
- `bootiga/bootiq-theme`: frontend theme.
- `bootiga/bootiq-product-blocks`: Layout Builder storefront blocks.
- `bootiga/bootiq-installer`: dependencies, configuration, and Drush installer command.

## Local development

Add these repositories to the target projects `composer.json`, replacing
`/absolute/path` with the real path. Keep the paths absolute.

```json
{
  "repositories": [
    {
      "type": "path",
      "url": "/absolute/path/bootiq-installable/repositories/bootiq-theme",
      "options": {
        "symlink": false,
        "versions": {"bootiga/bootiq-theme": "1.0.0"}
      }
    },
    {
      "type": "path",
      "url": "/absolute/path/bootiq-installable/repositories/bootiq-product-blocks",
      "options": {
        "symlink": false,
        "versions": {"bootiga/bootiq-product-blocks": "1.0.0"}
      }
    },
    {
      "type": "path",
      "url": "/absolute/path/bootiq-installable/repositories/bootiq-installer",
      "options": {
        "symlink": false,
        "versions": {"bootiga/bootiq-installer": "1.0.0"}
      }
    },
    {
      "type": "path",
      "url": "/absolute/path/bootiq-installable/repositories/bootiq",
      "options": {
        "symlink": false,
        "versions": {"bootiga/bootiq": "1.0.0"}
      }
    }
  ]
}
```

Then install the stable package:

```bash
composer require bootiga/bootiq:^1.0
./vendor/drush/drush/drush en bootiq_installer -y
./vendor/drush/drush/drush bootiq:install
```

Use `bootiq:install --force` when Bootiq should replace an existing Kickstart
frontpage and product Layout Builder configuration.

## License

GPL-2.0-or-later.
