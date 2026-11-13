<p align="center">
  <a href="https://github.com/hikashop-nicolas/hikashop-for-siteorigin-page-builder/actions/workflows/ci.yml"><img src="https://github.com/hikashop-nicolas/hikashop-for-siteorigin-page-builder/actions/workflows/ci.yml/badge.svg" alt="CI"></a>
  <img src="https://img.shields.io/badge/WordPress-6.8%2B-21759b" alt="WordPress 6.8+">
  <img src="https://img.shields.io/badge/PHP-7.4%2B-777bb4" alt="PHP 7.4+">
  <img src="https://img.shields.io/badge/SiteOrigin%20Page%20Builder-2.29%2B-2ea44f" alt="SiteOrigin Page Builder 2.29+">
  <img src="https://img.shields.io/badge/license-GPLv2%2B-ea580c" alt="GPLv2 or later">
</p>

# HikaShop for SiteOrigin Page Builder

Design a [HikaShop](https://www.hikashop.com) store with [SiteOrigin Page Builder](https://siteorigin.com/page-builder/). Product listings, category listings and every part of the product page become widgets in a HikaShop tab of the Add Widget dialog, next to HikaShop's own cart, wishlist, currency and filter widgets, and HikaShop product pages can be shown with a SiteOrigin layout.

Every widget is a plain WordPress widget: no SiteOrigin Widgets Bundle needed, and the widgets also work in widget areas.

## What you get

| Widget | What it shows |
|---|---|
| **HikaShop Products** | Products of a category (with or without its subcategories), products chosen by hand, products on sale, the visitor's recently viewed products, or the products related to the product being viewed. Order, number, columns, product layout, and whether to show the price, add to cart, quantity, wishlist, rating and badges. |
| **HikaShop Categories** | The subcategories of a category, with their product counts, empty ones hidden if you want. |
| **Product Title, Images, Price, Add to Cart, Description, Variants, Options, Custom Fields, Specifications, Dimensions, Bundled Products, Files, Rating, Sharing** | One part each of the product being viewed, to place anywhere in the product page layout. |
| **HikaShop Cart, Wishlist, Currency Switcher, Filters** | HikaShop's own widgets, listed in the same tab, with their full settings one click away. |

SiteOrigin Page Builder's own style settings (colors, spacing, borders, CSS class) apply to every widget, on top of HikaShop's own CSS.

### Products chosen by hand

The product picker searches the HikaShop catalog by name, code or id, and keeps the order you give.

### Product pages designed with SiteOrigin Page Builder

Free SiteOrigin Page Builder has no saved templates, so the plugin adds its own: in *Settings > HikaShop for SiteOrigin Page Builder*, add a layout, build it with the product widgets, publish it and pick it as the product page layout. Every HikaShop product page is then shown with it. In the Live Editor the latest product of the shop stands in, so you see real content while you design.

A layout's own address answers 404 to anyone who cannot edit it, and is never indexed.

The variant selectors, the add to cart button, the image gallery and a link to a given variant (`?hikashop_char=`) behave as on HikaShop's own product page; unpublished products, products hidden from the visitor's user group or language still get HikaShop's own answer.

## Requirements

| | |
|---|---|
| WordPress | 6.8 or newer |
| PHP | 7.4 or newer |
| SiteOrigin Page Builder | 2.29 or newer |
| HikaShop | 6.8.2 or newer on WordPress, a package downloaded since 13 November 2026 (Starter, the free edition, works: [download it](https://www.hikashop.com/download)) |

If a requirement is missing, the plugin stays inactive and says which one in the admin.

## Installation

1. Install and activate HikaShop and SiteOrigin Page Builder.
2. Install HikaShop for SiteOrigin Page Builder from *Plugins > Add New* (once it is on WordPress.org), or upload the zip of the [latest release](https://github.com/hikashop-nicolas/hikashop-for-siteorigin-page-builder/releases).
3. Edit a page with SiteOrigin Page Builder and click *Add Widget*: the widgets are in the HikaShop tab.
4. For the product pages, add a layout in *Settings > HikaShop for SiteOrigin Page Builder*, design it and choose it there.

## How it works

The plugin reimplements no shop logic, so prices, taxes, discounts, stock, access rights, the cart and the HikaShop layout overrides of your theme all apply as they do on HikaShop's own pages.

- **Listings** go through `hikashop_getLayout()` with module settings, the way HikaShop's own modules render. Their HTML ids come from SiteOrigin's id of the placement.
- **Product parts** render HikaShop's product blocks (the ones of the WordPress site editor) for the product of the page.
- **Cart, wishlist, currency and filters** are HikaShop's own WordPress widgets; HikaShop keeps each placement's settings under its own id.
- **The product page layout** is a `hikashop_so_layout` post built with SiteOrigin Page Builder, rendered through the `hikashop_wp_page_output` filter of HikaShop with SiteOrigin's renderer; its CSS is printed in the head. The page's title, meta description, canonical URL, Yoast SEO and Rank Math data and the product's structured data come from HikaShop, as on its own product page.
- **Previews**: SiteOrigin renders the Live Editor and the layout block from admin requests; the widgets render there as on the site.
- **Output** is escaped with `wp_kses()` and an allowlist for HikaShop's forms; HikaShop's inline scripts and styles go through `wp_add_inline_script()` and `wp_add_inline_style()`.

## Development

```sh
bin/build.sh    # build/hikashop-for-siteorigin-page-builder/ and its zip, without the dev files
```

```
hikashop-for-siteorigin-page-builder.php   bootstrap
includes/class-plugin.php                  requirements, widget registration, the HikaShop tab
includes/class-listing.php                 product and category listings
includes/class-render.php                  escaping, scripts and styles, site rendering scope
includes/class-layouts.php                 layout post type, product page layout and its setting
includes/class-ajax.php                    catalog search of the product picker
includes/widgets/                          the widgets
assets/                                    form script and CSS, frontend CSS
```

The listing, render and ajax classes are the ones of [HikaShop for Elementor](https://github.com/hikashop-nicolas/hikashop-for-elementor) and [HikaShop for Beaver Builder](https://github.com/hikashop-nicolas/hikashop-for-beaver-builder), under their own namespace, with the same file and method names: port a fix to all three.

CI lints the PHP on 7.4, 8.1 and 8.4 and runs [Plugin Check](https://wordpress.org/plugins/plugin-check/) on the build: every category, experimental and low-severity checks included, in strict mode, so any error or warning fails the build. A release cannot be deployed without passing it.

## Release

1. Bump `Version` in `hikashop-for-siteorigin-page-builder.php`, `HIKASHOP_SO_VERSION`, `Stable tag` in `readme.txt`, and add the changelog entry to `readme.txt`.
2. Tag the commit with the version (`1.0.1`) and push the tag. The deploy workflow checks the versions match, runs Plugin Check, creates the GitHub release with the zip, and commits the release and the `.wordpress-org/` assets to the WordPress.org SVN.

The SVN credentials are the `SVN_USERNAME` and `SVN_PASSWORD` repository secrets; without them the workflow only makes the GitHub release. To send a version already released on GitHub to WordPress.org later, run the deploy workflow by hand on its tag.

## Support

Questions and bug reports: [GitHub issues](https://github.com/hikashop-nicolas/hikashop-for-siteorigin-page-builder/issues) or the [HikaShop forum](https://www.hikashop.com/forum).

## License

GPLv2 or later. SiteOrigin is a trademark of its owner; this plugin is not made or endorsed by SiteOrigin.
