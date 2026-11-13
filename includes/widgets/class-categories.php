<?php
/**
 * HikaShop Categories widget.
 *
 * @package HikaShopSiteOrigin
 */

namespace HikaShopSiteOrigin\Widgets;

use HikaShopSiteOrigin\Listing;
use HikaShopSiteOrigin\Render;

defined( 'ABSPATH' ) || exit;

/**
 * Category listing.
 */
class Categories extends Widget {

	/**
	 * Widget details.
	 */
	public function __construct() {
		parent::__construct(
			'hikashop-so-categories',
			__( 'HikaShop Categories', 'hikashop-for-siteorigin-page-builder' ),
			__( 'A listing of HikaShop categories.', 'hikashop-for-siteorigin-page-builder' )
		);
	}

	/**
	 * Settings form fields.
	 *
	 * @return array
	 */
	protected function fields() {
		return array(
			'category'      => array(
				'type'    => 'category',
				'label'   => __( 'Parent category', 'hikashop-for-siteorigin-page-builder' ),
				'default' => '0',
			),
			'subcategories' => $this->yes_no_field( __( 'Include all levels below', 'hikashop-for-siteorigin-page-builder' ), '0' ),
			'hide_empty'    => $this->yes_no_field( __( 'Hide empty categories', 'hikashop-for-siteorigin-page-builder' ), '1' ),
			'order'         => array(
				'type'    => 'select',
				'label'   => __( 'Order by', 'hikashop-for-siteorigin-page-builder' ),
				'default' => 'category_ordering',
				'options' => array(
					'category_ordering' => __( 'HikaShop ordering', 'hikashop-for-siteorigin-page-builder' ),
					'category_name'     => __( 'Name', 'hikashop-for-siteorigin-page-builder' ),
					'category_created'  => __( 'Creation date', 'hikashop-for-siteorigin-page-builder' ),
					'category_modified' => __( 'Modification date', 'hikashop-for-siteorigin-page-builder' ),
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
				'label'   => __( 'Number of categories', 'hikashop-for-siteorigin-page-builder' ),
				'default' => '8',
			),
			'columns'       => array(
				'type'    => 'number',
				'label'   => __( 'Columns', 'hikashop-for-siteorigin-page-builder' ),
				'default' => '4',
			),
			'item_layout'   => array(
				'type'    => 'select',
				'label'   => __( 'Category layout', 'hikashop-for-siteorigin-page-builder' ),
				'default' => 'inherit',
				'options' => $this->item_layout_options(),
			),
			'count'         => $this->yes_no_field( __( 'Number of products', 'hikashop-for-siteorigin-page-builder' ), '0' ),
		);
	}

	/**
	 * Output.
	 *
	 * @param array $settings Widget settings.
	 */
	protected function render_html( $settings ) {
		$html = Listing::categories( $settings, $this->div_name( 'hikashop_so_categories' ) );
		if ( self::is_empty( $html ) ) {
			Render::placeholder( $this->name, __( 'No category to show with these settings.', 'hikashop-for-siteorigin-page-builder' ) );
			return;
		}
		Render::output( $html );
	}
}
