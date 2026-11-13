<?php
/**
 * Common base of the product page part widgets.
 *
 * @package HikaShopSiteOrigin
 */

namespace HikaShopSiteOrigin\Widgets;

use HikaShopSiteOrigin\Render;

defined( 'ABSPATH' ) || exit;

/**
 * A part of the product page, rendered with HikaShop's product block of the same name.
 */
abstract class Product_Part extends Widget {

	/**
	 * Block name after "hikashop/product-".
	 *
	 * @var string
	 */
	protected $part = '';

	/**
	 * Dashicon of the widget in the page builder.
	 *
	 * @var string
	 */
	public $icon = '';

	/**
	 * Every product part widget.
	 *
	 * @return string[]
	 */
	public static function classes() {
		return array(
			Product_Title::class,
			Product_Gallery::class,
			Product_Price::class,
			Product_Add_To_Cart::class,
			Product_Description::class,
			Product_Variants::class,
			Product_Options::class,
			Product_Specifications::class,
			Product_Custom_Fields::class,
			Product_Dimensions::class,
			Product_Bundled::class,
			Product_Files::class,
			Product_Rating::class,
			Product_Social::class,
		);
	}

	/**
	 * Sets the widget up.
	 *
	 * @param string $part Block name after "hikashop/product-".
	 * @param string $name Widget name.
	 * @param string $icon Dashicon name.
	 */
	public function __construct( $part, $name, $icon ) {
		$this->part = $part;
		$this->icon = $icon;
		parent::__construct(
			'hikashop-so-product-' . $part,
			$name,
			__( 'A part of the product page, for the product being viewed.', 'hikashop-for-siteorigin-page-builder' )
		);
	}

	/**
	 * Settings of this part.
	 *
	 * @return array
	 */
	protected function part_fields() {
		return array();
	}

	/**
	 * Block attributes from the settings.
	 *
	 * @param array $settings Widget settings.
	 * @return array
	 */
	protected function block_attributes( $settings ) {
		unset( $settings );
		return array();
	}

	/**
	 * Settings form fields.
	 *
	 * @return array
	 */
	protected function fields() {
		return array_merge(
			array(
				'part_note' => array(
					'type'  => 'note',
					'label' => __( 'Shows the product being viewed: place it in the product page layout chosen in Settings > HikaShop for SiteOrigin Page Builder. In the builder, the latest product stands in.', 'hikashop-for-siteorigin-page-builder' ),
				),
			),
			$this->part_fields()
		);
	}

	/**
	 * In the builder, the latest product stands in for the product of the page.
	 */
	protected function prepare_product() {
		$input = \hikaInput::get();
		if ( $input->getInt( 'cid', 0 ) || $input->getInt( 'product_id', 0 ) || ! Render::is_editor() ) {
			return;
		}
		$product_id = self::preview_product_id();
		if ( $product_id ) {
			$input->set( 'cid', $product_id );
		}
	}

	/**
	 * Id of the product shown in the builder.
	 *
	 * @return int
	 */
	public static function preview_product_id() {
		static $product_id = null;
		if ( null === $product_id ) {
			$db = \JFactory::getDBO();
			$db->setQuery(
				'SELECT product_id FROM ' . hikashop_table( 'product' )
				. ' WHERE product_published = 1 AND product_type = ' . $db->quote( 'main' )
				. ' ORDER BY product_id DESC',
				0,
				1
			);
			$product_id = (int) $db->loadResult();
			$product_id = (int) apply_filters( 'hikashop_so_preview_product', $product_id );
		}
		return $product_id;
	}

	/**
	 * Output.
	 *
	 * @param array $settings Widget settings.
	 */
	protected function render_html( $settings ) {
		if ( ! function_exists( 'render_block' ) || ! \WP_Block_Type_Registry::get_instance()->is_registered( 'hikashop/product-' . $this->part ) ) {
			return;
		}
		Render::prepare();
		$this->prepare_product();

		$html = render_block(
			array(
				'blockName'    => 'hikashop/product-' . $this->part,
				'attrs'        => $this->block_attributes( $settings ),
				'innerBlocks'  => array(),
				'innerHTML'    => '',
				'innerContent' => array(),
			)
		);

		if ( '' === trim( wp_strip_all_tags( $html ) ) && ! preg_match( '#<(img|input|select|button|iframe)\b#i', $html ) ) {
			Render::placeholder( $this->name, __( 'Nothing to show for this product.', 'hikashop-for-siteorigin-page-builder' ) );
			return;
		}
		Render::output( $html );
	}
}
