=== HikaShop for SiteOrigin Page Builder ===
Contributors: hikarisoftware
Tags: hikashop, siteorigin, page builder, ecommerce, shop
Requires at least: 6.8
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.0.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Design your HikaShop store with SiteOrigin Page Builder: product listings, product page parts, cart, filters, and product pages built from a layout.

== Description ==

HikaShop for SiteOrigin Page Builder adds a HikaShop tab to the Add Widget dialog of SiteOrigin Page Builder. Every widget is a WordPress widget, so it also works in your widget areas, and each one gets the style settings of SiteOrigin Page Builder (colors, spacing, borders).

= Listings =

* **HikaShop Products**: the products of a category, products chosen by hand (with a search of your catalog, in the order you choose), products on sale, the products the visitor viewed recently, or the products related to the product being viewed. Choose the order, the number of products, the columns, the product layout, and whether to show the price, the add to cart button, the quantity field, the wishlist button, the rating and the badges.
* **HikaShop Categories**: the subcategories of a category, with the number of products in each, empty categories hidden if you want.

= Product page =

Create a layout in Settings > HikaShop for SiteOrigin Page Builder, design it with the product widgets, and pick it as the product page layout: every HikaShop product page is then shown with it. The product widgets show the product being viewed:

* Product title, images, price, add to cart (with the quantity field), description
* Variants (the characteristic selectors), options, custom fields
* Specifications, dimensions and weight, bundled products, files, rating, sharing buttons

In the Live Editor, the latest product of your shop stands in so you can see the result while you design. The page title, meta description, canonical URL and structured data of the product still come from HikaShop.

= Cart and filters =

HikaShop's own Cart, Wishlist, Currency Switcher and Filters widgets are listed in the same HikaShop tab, with their full HikaShop settings.

Prices, taxes, discounts, stock, access rights and the cart all follow your HikaShop configuration: the widgets render with HikaShop's own layouts, so your theme overrides of HikaShop layouts keep working.

= Requirements =

This plugin needs HikaShop 6.8.2 or newer on WordPress (a package downloaded since 16 November 2026: reinstall it over an older 6.8.2), and SiteOrigin Page Builder 2.29 or newer. HikaShop is not hosted on WordPress.org: its free Starter edition and the Essential and Business editions are available on [hikashop.com](https://www.hikashop.com/download).

== Installation ==

1. Install and activate HikaShop and SiteOrigin Page Builder.
2. Install and activate HikaShop for SiteOrigin Page Builder from Plugins > Add New, or upload the zip file.
3. Edit a page with SiteOrigin Page Builder and click Add Widget: the widgets are in the HikaShop tab.
4. To design the product pages, go to Settings > HikaShop for SiteOrigin Page Builder, add a layout, place the product widgets in it with SiteOrigin Page Builder, publish it, then choose it as the product page layout.

== Frequently Asked Questions ==

= Does it need the SiteOrigin Widgets Bundle? =

No. Only SiteOrigin Page Builder is needed.

= Why is a product missing from a listing? =

The listings follow the HikaShop configuration: an unpublished product, a product the visitor's user group cannot see, or a product out of stock while "Display out of stock products" is off in HikaShop are left out, as in your shop pages.

= Where are the settings of the cart widget? =

Open the widget and click "Configure display options": the HikaShop cart module settings open in a new window. Each cart widget keeps its own settings, and a duplicated widget gets a copy of them.

= Can visitors open a layout on its own? =

No. A layout's own address is only open to the people who can edit it, and search engines are told not to index it.

= Can I change the HTML of the widgets? =

Yes, with HikaShop's layout overrides: copy the HikaShop layout file into the hikashop folder of your theme, as you would for the HikaShop pages.

== Screenshots ==

1. A product listing and its settings in SiteOrigin Page Builder.
2. Products chosen by hand, with a search of the HikaShop catalog.
3. A product page layout designed with the product widgets of the HikaShop tab, in the Live Editor.
4. A HikaShop product page shown with that layout.
5. A shop page built with the category and product listing widgets.
6. The product page layout setting.

== Changelog ==

= 1.0.2 =
* The notice saying HikaShop or SiteOrigin Page Builder is missing or too old shows on the Plugins page only, instead of on every admin page.

= 1.0.1 =
* The scripts and styles of the HikaShop layouts shown in the widgets are loaded by HikaShop itself. This needs a HikaShop 6.8.2 package downloaded since 16 November 2026; with an older one, the widgets show without those scripts.

= 1.0.0 =
* First release: product and category listings, the product page parts and the product page layout, with HikaShop's cart, wishlist, currency and filter widgets in the same tab.
