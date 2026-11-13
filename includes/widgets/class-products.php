<?php
/**
 * HikaShop Products widget.
 *
 * @package HikaShopSiteOrigin
 */

namespace HikaShopSiteOrigin\Widgets;

use HikaShopSiteOrigin\Listing;
use HikaShopSiteOrigin\Render;

defined( 'ABSPATH' ) || exit;

/**
 * Product listing.
 */
class Products extends Widget {

	/**
	 * Widget details.
	 */
	public function __construct() {
		parent::__construct(
			'hikashop-so-products',
			__( 'HikaShop Products', 'hikashop-for-siteorigin-page-builder' ),
			__( 'A listing of HikaShop products.', 'hikashop-for-siteorigin-page-builder' )
		);
	}

	/**
	 * Settings form fields.
	 *
	 * @return array
	 */
	protected function fields() {
		$query = array( 'source', array( 'category', 'sale', 'recent' ) );
		return array(
			'source'        => array(
				'type'    => 'select',
				'label'   => __( 'Source', 'hikashop-for-siteorigin-page-builder' ),
				'default' => 'category',
				'options' => array(
					'category' => __( 'Products of a category', 'hikashop-for-siteorigin-page-builder' ),
					'manual'   => __( 'Products chosen by hand', 'hikashop-for-siteorigin-page-builder' ),
					'sale'     => __( 'Products on sale', 'hikashop-for-siteorigin-page-builder' ),
					'recent'   => __( 'Recently viewed by the visitor', 'hikashop-for-siteorigin-page-builder' ),
					'related'  => __( 'Related to the product shown', 'hikashop-for-siteorigin-page-builder' ),
				),
			),
			'category'      => array(
				'type'    => 'category',
				'label'   => __( 'Category', 'hikashop-for-siteorigin-page-builder' ),
				'default' => '0',
				'show'    => $query,
			),
			'subcategories' => $this->yes_no_field( __( 'Include subcategories', 'hikashop-for-siteorigin-page-builder' ), '1' ) + array( 'show' => $query ),
			'products'      => array(
				'type'  => 'products',
				'label' => __( 'Products', 'hikashop-for-siteorigin-page-builder' ),
				'show'  => array( 'source', array( 'manual' ) ),
			),
			'related_note'  => array(
				'type'  => 'note',
				'label' => __( 'Place this listing in the product page layout: it shows the related products of the product being viewed.', 'hikashop-for-siteorigin-page-builder' ),
				'show'  => array( 'source', array( 'related' ) ),
			),
			'order'         => array(
				'type'    => 'select',
				'label'   => __( 'Order by', 'hikashop-for-siteorigin-page-builder' ),
				'default' => 'ordering',
				'options' => array(
					'ordering'              => __( 'HikaShop ordering', 'hikashop-for-siteorigin-page-builder' ),
					'manual'                => __( 'Order chosen above', 'hikashop-for-siteorigin-page-builder' ),
					'product_name'          => __( 'Name', 'hikashop-for-siteorigin-page-builder' ),
					'product_sort_price'    => __( 'Price', 'hikashop-for-siteorigin-page-builder' ),
					'product_created'       => __( 'Creation date', 'hikashop-for-siteorigin-page-builder' ),
					'product_modified'      => __( 'Modification date', 'hikashop-for-siteorigin-page-builder' ),
					'product_sales'         => __( 'Sales', 'hikashop-for-siteorigin-page-builder' ),
					'product_hit'           => __( 'Views', 'hikashop-for-siteorigin-page-builder' ),
					'product_average_score' => __( 'Rating', 'hikashop-for-siteorigin-page-builder' ),
					'random'                => __( 'Random', 'hikashop-for-siteorigin-page-builder' ),
				),
			),
			'order_dir'     => array(
				'type'    => 'select',
				'label'   => __( 'Direction', 'hikashop-for-siteorigin-page-builder' ),
				'default' => 'ASC',
				'options' => array(
					'ASC'  => __( 'Ascending', 'hikashop-for-siteorigin-page-builder' ),
					'DESC' => __( 'Descending', 'hikashop-for-siteorigin-page-builder' ),
				),
			),
			'limit'         => array(
				'type'    => 'number',
				'label'   => __( 'Number of products', 'hikashop-for-siteorigin-page-builder' ),
				'default' => '8',
				'show'    => array( 'source', array( 'category', 'sale', 'recent', 'related' ) ),
			),
			'columns'       => array(
				'type'    => 'number',
				'label'   => __( 'Columns', 'hikashop-for-siteorigin-page-builder' ),
				'default' => '4',
			),
			'item_layout'   => array(
				'type'    => 'select',
				'label'   => __( 'Product layout', 'hikashop-for-siteorigin-page-builder' ),
				'default' => 'inherit',
				'options' => $this->item_layout_options(),
			),
			'show_price'    => $this->switch_field( __( 'Price', 'hikashop-for-siteorigin-page-builder' ) ),
			'add_to_cart'   => $this->switch_field( __( 'Add to cart button', 'hikashop-for-siteorigin-page-builder' ) ),
			'quantity'      => $this->switch_field( __( 'Quantity field', 'hikashop-for-siteorigin-page-builder' ) ),
			'wishlist'      => $this->switch_field( __( 'Add to wishlist button', 'hikashop-for-siteorigin-page-builder' ) ),
			'rating'        => $this->switch_field( __( 'Rating', 'hikashop-for-siteorigin-page-builder' ) ),
			'badges'        => $this->switch_field( __( 'Badges', 'hikashop-for-siteorigin-page-builder' ) ),
			'product_links' => $this->switch_field( __( 'Links to the product page', 'hikashop-for-siteorigin-page-builder' ) ),
		);
	}

	/**
	 * Output.
	 *
	 * @param array $settings Widget settings.
	 */
	protected function render_html( $settings ) {
		$html = Listing::products( $settings, $this->div_name( 'hikashop_so_products' ) );
		if ( self::is_empty( $html ) ) {
			Render::placeholder( $this->name, __( 'No product to show with these settings.', 'hikashop-for-siteorigin-page-builder' ) );
			return;
		}
		Render::output( $html );
	}
}
