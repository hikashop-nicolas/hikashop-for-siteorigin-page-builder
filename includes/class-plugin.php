<?php
/**
 * Bootstrap: checks the requirements and hooks everything in.
 *
 * @package HikaShopSiteOrigin
 */

namespace HikaShopSiteOrigin;

defined( 'ABSPATH' ) || exit;

/**
 * Main plugin class.
 */
final class Plugin {

	const MIN_SITEORIGIN = '2.29.0';
	const MIN_HIKASHOP   = '6.8.2';

	/**
	 * HikaShop's own widgets listed in the HikaShop tab; its Products and Entry widgets stay in "All Widgets".
	 */
	const CORE_WIDGETS = array( 'HikaShop_Widget_Cart', 'HikaShop_Widget_Wishlist', 'HikaShop_Widget_Currency', 'HikaShop_Widget_Filter' );

	/**
	 * Hooks the plugin in once every plugin is loaded.
	 */
	public static function boot() {
		add_action( 'plugins_loaded', array( __CLASS__, 'init' ), 20 );
	}

	/**
	 * Whether HikaShop is installed, active and recent enough.
	 *
	 * @return bool
	 */
	public static function hikashop_ready() {
		// The 6.8.2 packages built since 12 November 2026 carry the product page hook and the SEO of add-on pages
		return defined( 'HIKASHOP_COMPONENT' ) && defined( 'HIKASHOP_WP_VERSION' ) && function_exists( 'hikashop_get' )
			&& version_compare( HIKASHOP_WP_VERSION, self::MIN_HIKASHOP, '>=' ) && function_exists( 'hikashop_siteRendering' );
	}

	/**
	 * Whether SiteOrigin Page Builder is active and recent enough.
	 *
	 * @return bool
	 */
	public static function siteorigin_ready() {
		return class_exists( '\SiteOrigin_Panels' ) && defined( 'SITEORIGIN_PANELS_VERSION' )
			&& version_compare( SITEORIGIN_PANELS_VERSION, self::MIN_SITEORIGIN, '>=' );
	}

	/**
	 * Registers the hooks, or the admin notice saying what is missing.
	 */
	public static function init() {
		if ( ! self::hikashop_ready() || ! self::siteorigin_ready() ) {
			add_action( 'admin_notices', array( __CLASS__, 'requirements_notice' ) );
			return;
		}

		require_once HIKASHOP_SO_DIR . 'includes/class-render.php';
		require_once HIKASHOP_SO_DIR . 'includes/class-listing.php';
		require_once HIKASHOP_SO_DIR . 'includes/class-ajax.php';
		require_once HIKASHOP_SO_DIR . 'includes/class-layouts.php';

		add_action( 'widgets_init', array( __CLASS__, 'register_widgets' ) );
		add_filter( 'siteorigin_panels_widgets', array( __CLASS__, 'panel_widgets' ) );
		add_filter( 'siteorigin_panels_widget_dialog_tabs', array( __CLASS__, 'dialog_tabs' ), 20 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'preview_assets' ) );
		add_action( 'admin_notices', array( __CLASS__, 'widget_ids_notice' ) );

		Listing::init();
		Ajax::init();
		Layouts::init();
	}

	/**
	 * Tells the administrator which requirement is missing.
	 */
	public static function requirements_notice() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( ! $screen || 'plugins' !== $screen->base ) {
			return;
		}

		if ( ! self::hikashop_ready() ) {
			$message = sprintf(
				/* translators: 1: minimum HikaShop version, 2: link to the HikaShop download page */
				__( 'HikaShop for SiteOrigin Page Builder needs HikaShop %1$s or newer (a package downloaded since 16 November 2026), installed and active. HikaShop Starter is free: %2$s', 'hikashop-for-siteorigin-page-builder' ),
				self::MIN_HIKASHOP,
				'<a href="https://www.hikashop.com/download" target="_blank" rel="noopener noreferrer">' . esc_html__( 'download HikaShop', 'hikashop-for-siteorigin-page-builder' ) . '</a>'
			);
		} else {
			$message = sprintf(
				/* translators: %s: minimum SiteOrigin Page Builder version */
				__( 'HikaShop for SiteOrigin Page Builder needs SiteOrigin Page Builder %s or newer, installed and active.', 'hikashop-for-siteorigin-page-builder' ),
				self::MIN_SITEORIGIN
			);
		}

		echo '<div class="notice notice-warning"><p>' . wp_kses(
			$message,
			array(
				'a' => array(
					'href'   => true,
					'target' => true,
					'rel'    => true,
				),
			)
		) . '</p></div>';
	}

	/**
	 * HikaShop packages older than 13 November 2026 share one settings record between their widgets placed with SiteOrigin.
	 */
	public static function widget_ids_notice() {
		if ( ! current_user_can( 'activate_plugins' ) || ! class_exists( '\HikaShop_WP_Widget' ) || function_exists( 'hikashop_wp_siteorigin_widget_ids' ) ) {
			return;
		}
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( ! $screen || ! in_array( $screen->base, array( 'plugins', 'settings_page_hikashop-for-siteorigin-page-builder' ), true ) ) {
			return;
		}
		echo '<div class="notice notice-info"><p>' . esc_html__( 'Reinstall HikaShop with a package downloaded since 13 November 2026 so that each HikaShop Cart, Wishlist, Currency Switcher and Filters widget placed with SiteOrigin Page Builder keeps its own settings.', 'hikashop-for-siteorigin-page-builder' ) . '</p></div>';
	}

	/**
	 * Widget classes of the plugin.
	 *
	 * @return string[]
	 */
	private static function widget_classes() {
		return array_merge(
			array( Widgets\Products::class, Widgets\Categories::class ),
			Widgets\Product_Part::classes()
		);
	}

	/**
	 * Registers the widgets.
	 */
	public static function register_widgets() {
		$dir = HIKASHOP_SO_DIR . 'includes/widgets/';
		require_once $dir . 'class-widget.php';
		require_once $dir . 'class-products.php';
		require_once $dir . 'class-categories.php';
		require_once $dir . 'class-product-part.php';
		require_once $dir . 'class-product-parts.php';

		foreach ( self::widget_classes() as $class ) {
			register_widget( $class );
		}
	}

	/**
	 * Puts the HikaShop widgets in the HikaShop tab of the "Add Widget" dialog, with their icons.
	 *
	 * @param array $widgets Widgets of the dialog, by class.
	 * @return array
	 */
	public static function panel_widgets( $widgets ) {
		global $wp_widget_factory;
		$icons = array(
			Widgets\Products::class      => 'dashicons dashicons-products',
			Widgets\Categories::class    => 'dashicons dashicons-category',
			'HikaShop_Widget_Cart'       => 'dashicons dashicons-cart',
			'HikaShop_Widget_Wishlist'   => 'dashicons dashicons-heart',
			'HikaShop_Widget_Currency'   => 'dashicons dashicons-money-alt',
			'HikaShop_Widget_Filter'     => 'dashicons dashicons-filter',
		);
		foreach ( Widgets\Product_Part::classes() as $class ) {
			$widget          = $wp_widget_factory->widgets[ $class ] ?? null;
			$icons[ $class ] = 'dashicons dashicons-' . ( $widget instanceof Widgets\Product_Part ? $widget->icon : 'cart' );
		}
		foreach ( $icons as $class => $icon ) {
			if ( ! isset( $widgets[ $class ] ) ) {
				continue;
			}
			$groups                     = isset( $widgets[ $class ]['groups'] ) ? (array) $widgets[ $class ]['groups'] : array();
			$widgets[ $class ]['groups'] = array_values( array_unique( array_merge( $groups, array( 'hikashop' ) ) ) );
			$widgets[ $class ]['icon']   = $icon;
		}
		return $widgets;
	}

	/**
	 * The HikaShop tab of the "Add Widget" dialog.
	 *
	 * @param array $tabs Dialog tabs.
	 * @return array
	 */
	public static function dialog_tabs( $tabs ) {
		$tabs['hikashop'] = array(
			'title'  => __( 'HikaShop', 'hikashop-for-siteorigin-page-builder' ),
			'filter' => array( 'groups' => array( 'hikashop' ) ),
		);
		return $tabs;
	}

	/**
	 * The product picker and the conditional fields, where widget forms are edited.
	 *
	 * @param string $hook Admin page.
	 */
	public static function admin_scripts( $hook ) {
		if ( ! in_array( $hook, array( 'post.php', 'post-new.php', 'widgets.php', 'appearance_page_so_panels_home_page' ), true ) ) {
			return;
		}
		self::form_scripts();
	}

	/**
	 * Enqueues the form script and style.
	 */
	public static function form_scripts() {
		wp_enqueue_style( 'hikashop-so-admin', HIKASHOP_SO_URL . 'assets/css/admin.css', array(), HIKASHOP_SO_VERSION );
		wp_enqueue_script( 'hikashop-so-admin', HIKASHOP_SO_URL . 'assets/js/admin.js', array( 'jquery' ), HIKASHOP_SO_VERSION, true );
		wp_localize_script(
			'hikashop-so-admin',
			'hikashopSiteOrigin',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'hikashop_so_search' ),
				'remove'  => __( 'Remove', 'hikashop-for-siteorigin-page-builder' ),
				'up'      => __( 'Move up', 'hikashop-for-siteorigin-page-builder' ),
				'down'    => __( 'Move down', 'hikashop-for-siteorigin-page-builder' ),
				'none'    => __( 'No product found.', 'hikashop-for-siteorigin-page-builder' ),
			)
		);
	}

	/**
	 * HikaShop's CSS and JavaScript in SiteOrigin's previews.
	 */
	public static function preview_assets() {
		if ( ! Render::is_editor() ) {
			return;
		}
		Render::enqueue_shop_assets();
		if ( Render::is_page_preview() ) {
			// HikaShop chose its backend stylesheets when it loaded for this admin request; the page needs the site ones
			$css = function_exists( 'hikashop_config' ) ? sanitize_key( (string) hikashop_config()->get( 'css_frontend', 'default' ) ) : 'default';
			if ( '' !== $css && defined( 'HIKASHOP_CSS' ) ) {
				wp_enqueue_style( 'hikashop-so-frontend-' . $css, HIKASHOP_CSS . 'frontend_' . $css . '.css', array(), HIKASHOP_SO_VERSION );
			}
			add_filter( 'style_loader_tag', array( __CLASS__, 'drop_backend_styles' ), 10, 3 );
		}
	}

	/**
	 * Leaves HikaShop's backend stylesheets out of the Live Editor preview.
	 *
	 * @param string $tag    Link tag.
	 * @param string $handle Style handle.
	 * @param string $href   Stylesheet URL.
	 * @return string
	 */
	public static function drop_backend_styles( $tag, $handle, $href ) {
		unset( $handle );
		return preg_match( '#/media/css/backend_[a-z0-9_-]+\.css#i', (string) $href ) ? '' : $tag;
	}
}
