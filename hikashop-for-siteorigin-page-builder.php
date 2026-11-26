<?php
/**
 * Plugin Name:       HikaShop for SiteOrigin Page Builder
 * Plugin URI:        https://github.com/hikashop-nicolas/hikashop-for-siteorigin-page-builder
 * Description:       HikaShop product listings, product page parts, cart and filters as SiteOrigin Page Builder widgets, and HikaShop product pages designed with SiteOrigin Page Builder.
 * Version:           1.0.3
 * Requires at least: 6.8
 * Requires PHP:      7.4
 * Requires Plugins:  siteorigin-panels
 * Author:            Hikari Software
 * Author URI:        https://www.hikashop.com
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       hikashop-for-siteorigin-page-builder
 *
 * @package HikaShopSiteOrigin
 */

defined( 'ABSPATH' ) || exit;

define( 'HIKASHOP_SO_VERSION', '1.0.3' );
define( 'HIKASHOP_SO_FILE', __FILE__ );
define( 'HIKASHOP_SO_DIR', plugin_dir_path( __FILE__ ) );
define( 'HIKASHOP_SO_URL', plugin_dir_url( __FILE__ ) );

require_once HIKASHOP_SO_DIR . 'includes/class-plugin.php';

\HikaShopSiteOrigin\Plugin::boot();
