<?php
/**
 * Removes the settings of the plugin. The layouts stay: they are the site's content.
 *
 * @package HikaShopSiteOrigin
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

delete_option( 'hikashop_so_product_layout' );
