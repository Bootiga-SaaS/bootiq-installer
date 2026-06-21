# Bootiq 1.0.0 Release Audit

Date: 2026-06-21

## Scope

The audited source is the Bootiq 1.0.0 package. It contains the Bootiq metapackage, theme, product-block module, and installer. No products, articles, users, stores, SaaS settings, legal content, or payment credentials are shipped.
Logo/favicon administration and Store Appearance presets were intentionally excluded because they are Bootiga SaaS features rather than requirements of the standalone storefront.

## Clean installation

Bootiq was installed from copied Composer path packages on a fresh Commerce Kickstart 5.1 demo running Drupal 11.3.9, PHP 8.3.31, and Commerce 3.

The validation installation used:

```bash
composer require bootiga/bootiq:^1.0
drush en bootiq_installer -y
drush bootiq:install --force
```

Composer mirrored the packages into the target project; no symlinks to the development source were used.
The final local repository test resolved all four packages as `1.0.0` through explicit path-repository version mappings. The metapackage does not lower the target projects minimum stability.

## Results

- Bootiq became the default frontend theme.
- All three Bootiq components installed successfully.
- The installer imported 44 owned configuration objects.
- Frontpage Layout Builder contains Hero, product grid, blog, and The Look.
- Default, physical, and media product displays contain the similar-products block.
- `/`, `/products`, `/product/1`, `/blog`, `/cart`, and `/user/login` returned HTTP 200.
- The homepage rendered the Bootiq header, logo, hero, product grid, blog section, The Look, and footer.
- Static theme assets and generated product image styles returned HTTP 200.
- Packaged PHP passed syntax validation.
- Composer manifests passed validation after removing embedded version fields.
- All theme CSS files have balanced braces and no empty rules.
- Removed 1,305 lines of obsolete and empty CSS and consolidated three competing token blocks into one.
- Frontend classes and JavaScript behaviors use the Bootiq namespace; no legacy frontend class prefix remains.
- Searches found no obsolete public Bootiga block plugin IDs or namespaces in the packaged API.

## Environment notes

The clean lab needed normal writable permissions on `sites/default/files/styles`. With correct ownership, Drupal generated product thumbnails successfully. The lab also reported missing sendmail and Trusted Host configuration; both belong to the temporary environment and are not package defects.

The upstream Kickstart demo recipe required an unlimited PHP CLI memory limit in this lab. This affects creation of the test site, not Bootiq installation.

## Release decision

Bootiq 1.0.0 is ready to package and publish. Public release should use Git tags for versions and separate Composer repositories for the metapackage, theme, product-block module, and installer.
