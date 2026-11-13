<?php
/**
 * The product page parts.
 *
 * @package HikaShopSiteOrigin
 */

namespace HikaShopSiteOrigin\Widgets;

defined( 'ABSPATH' ) || exit;

/**
 * Adds the "Inherit / Show / Hide" selects HikaShop's product blocks take.
 */
trait Block_Switches {

	/**
	 * One select per block attribute.
	 *
	 * @param array $switches Block attribute => label.
	 * @return array Fields.
	 */
	protected function block_switch_fields( $switches ) {
		$fields = array();
		foreach ( $switches as $name => $label ) {
			$fields[ $name ] = array(
				'type'    => 'select',
				'label'   => $label,
				'default' => '',
				'options' => array(
					''        => __( 'Inherit from HikaShop', 'hikashop-for-siteorigin-page-builder' ),
					'show'    => __( 'Show', 'hikashop-for-siteorigin-page-builder' ),
					'hide'    => __( 'Hide', 'hikashop-for-siteorigin-page-builder' ),
				),
			);
		}
		return $fields;
	}

	/**
	 * Block attributes from the selects.
	 *
	 * @param array $settings Widget settings.
	 * @param array $names    Block attribute names.
	 * @return array
	 */
	protected function block_switches( $settings, $names ) {
		$attributes = array();
		foreach ( $names as $name ) {
			$value = $settings[ $name ] ?? '';
			if ( in_array( $value, array( 'show', 'hide' ), true ) ) {
				$attributes[ $name ] = $value;
			}
		}
		return $attributes;
	}
}

/**
 * Product name.
 */
class Product_Title extends Product_Part {
	/**
	 * Style target.
	 *
	 * @var string
	 */

	/**
	 * Widget details.
	 */
	public function __construct() {
		parent::__construct( 'title', __( 'Product Title', 'hikashop-for-siteorigin-page-builder' ), 'heading' );
	}

	/**
	 * Settings of the part.
	 *
	 * @return array
	 */
	protected function part_fields() {
		return array(
			'heading_level' => array(
				'type'    => 'select',
				'label'   => __( 'HTML tag', 'hikashop-for-siteorigin-page-builder' ),
				'default' => '1',
				'options' => array(
					'1' => 'H1',
					'2' => 'H2',
					'3' => 'H3',
					'4' => 'H4',
					'5' => 'H5',
					'6' => 'H6',
				),
			),
			'show_code'     => $this->yes_no_field( __( 'Product code', 'hikashop-for-siteorigin-page-builder' ), '0' ),
		);
	}

	/**
	 * Block attributes.
	 *
	 * @param array $settings Widget settings.
	 * @return array
	 */
	protected function block_attributes( $settings ) {
		return array(
			'headingLevel' => max( 1, min( 6, (int) ( $settings['heading_level'] ?? 1 ) ) ),
			'showCode'     => ! empty( $settings['show_code'] ),
		);
	}
}

/**
 * Product images.
 */
class Product_Gallery extends Product_Part {
	/**
	 * Widget details.
	 */
	public function __construct() {
		parent::__construct( 'gallery', __( 'Product Images', 'hikashop-for-siteorigin-page-builder' ), 'format-gallery' );
	}
}

/**
 * Product price.
 */
class Product_Price extends Product_Part {
	use Block_Switches;

	/**
	 * Style target.
	 *
	 * @var string
	 */

	/**
	 * Widget details.
	 */
	public function __construct() {
		parent::__construct( 'price', __( 'Product Price', 'hikashop-for-siteorigin-page-builder' ), 'tag' );
	}

	/**
	 * Settings of the part.
	 *
	 * @return array
	 */
	protected function part_fields() {
		return $this->block_switch_fields(
			array(
				'showOriginalPrice' => __( 'Price before discount', 'hikashop-for-siteorigin-page-builder' ),
				'showDiscount'      => __( 'Discount', 'hikashop-for-siteorigin-page-builder' ),
			)
		);
	}

	/**
	 * Block attributes.
	 *
	 * @param array $settings Widget settings.
	 * @return array
	 */
	protected function block_attributes( $settings ) {
		return $this->block_switches( $settings, array( 'showOriginalPrice', 'showDiscount' ) );
	}
}

/**
 * Quantity and add to cart button.
 */
class Product_Add_To_Cart extends Product_Part {
	use Block_Switches;

	/**
	 * Widget details.
	 */
	public function __construct() {
		parent::__construct( 'add-to-cart', __( 'Add to Cart', 'hikashop-for-siteorigin-page-builder' ), 'cart' );
	}

	/**
	 * Settings of the part.
	 *
	 * @return array
	 */
	protected function part_fields() {
		return $this->block_switch_fields(
			array(
				'showQuantity' => __( 'Quantity field', 'hikashop-for-siteorigin-page-builder' ),
			)
		);
	}

	/**
	 * Block attributes.
	 *
	 * @param array $settings Widget settings.
	 * @return array
	 */
	protected function block_attributes( $settings ) {
		return $this->block_switches( $settings, array( 'showQuantity' ) );
	}
}

/**
 * Product description.
 */
class Product_Description extends Product_Part {
	/**
	 * Widget details.
	 */
	public function __construct() {
		parent::__construct( 'description', __( 'Product Description', 'hikashop-for-siteorigin-page-builder' ), 'text' );
	}
}

/**
 * Characteristic selectors of a product with variants.
 */
class Product_Variants extends Product_Part {
	/**
	 * Widget details.
	 */
	public function __construct() {
		parent::__construct( 'variants', __( 'Product Variants', 'hikashop-for-siteorigin-page-builder' ), 'screenoptions' );
	}
}

/**
 * Product options.
 */
class Product_Options extends Product_Part {
	/**
	 * Widget details.
	 */
	public function __construct() {
		parent::__construct( 'options', __( 'Product Options', 'hikashop-for-siteorigin-page-builder' ), 'list-view' );
	}
}

/**
 * Product specifications (custom product fields shown as a table).
 */
class Product_Specifications extends Product_Part {
	/**
	 * Widget details.
	 */
	public function __construct() {
		parent::__construct( 'specifications', __( 'Product Specifications', 'hikashop-for-siteorigin-page-builder' ), 'editor-table' );
	}
}

/**
 * Item custom fields the customer fills before adding to the cart.
 */
class Product_Custom_Fields extends Product_Part {
	/**
	 * Widget details.
	 */
	public function __construct() {
		parent::__construct( 'custom-fields', __( 'Product Custom Fields', 'hikashop-for-siteorigin-page-builder' ), 'forms' );
	}
}

/**
 * Weight and dimensions.
 */
class Product_Dimensions extends Product_Part {
	use Block_Switches;

	/**
	 * Widget details.
	 */
	public function __construct() {
		parent::__construct( 'dimensions', __( 'Product Dimensions', 'hikashop-for-siteorigin-page-builder' ), 'image-crop' );
	}

	/**
	 * Settings of the part.
	 *
	 * @return array
	 */
	protected function part_fields() {
		return $this->block_switch_fields(
			array(
				'showWeight'     => __( 'Weight', 'hikashop-for-siteorigin-page-builder' ),
				'showDimensions' => __( 'Dimensions', 'hikashop-for-siteorigin-page-builder' ),
			)
		);
	}

	/**
	 * Block attributes.
	 *
	 * @param array $settings Widget settings.
	 * @return array
	 */
	protected function block_attributes( $settings ) {
		return $this->block_switches( $settings, array( 'showWeight', 'showDimensions' ) );
	}
}

/**
 * Products bundled with the product.
 */
class Product_Bundled extends Product_Part {
	/**
	 * Widget details.
	 */
	public function __construct() {
		parent::__construct( 'bundled', __( 'Bundled Products', 'hikashop-for-siteorigin-page-builder' ), 'archive' );
	}
}

/**
 * Files attached to the product.
 */
class Product_Files extends Product_Part {
	/**
	 * Widget details.
	 */
	public function __construct() {
		parent::__construct( 'files', __( 'Product Files', 'hikashop-for-siteorigin-page-builder' ), 'download' );
	}
}

/**
 * Rating stars.
 */
class Product_Rating extends Product_Part {
	/**
	 * Widget details.
	 */
	public function __construct() {
		parent::__construct( 'rating', __( 'Product Rating', 'hikashop-for-siteorigin-page-builder' ), 'star-filled' );
	}
}

/**
 * Social sharing buttons.
 */
class Product_Social extends Product_Part {
	/**
	 * Widget details.
	 */
	public function __construct() {
		parent::__construct( 'social', __( 'Product Sharing', 'hikashop-for-siteorigin-page-builder' ), 'share' );
	}
}
