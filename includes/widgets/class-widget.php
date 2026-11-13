<?php
/**
 * Common base of the HikaShop widgets.
 *
 * @package HikaShopSiteOrigin
 */

namespace HikaShopSiteOrigin\Widgets;

use HikaShopSiteOrigin\Listing;
use HikaShopSiteOrigin\Render;

defined( 'ABSPATH' ) || exit;

/**
 * A widget whose settings form is built from fields(), and whose output comes from render_html().
 */
abstract class Widget extends \WP_Widget {

	/**
	 * Placement id of the instance being rendered.
	 *
	 * @var string
	 */
	protected $placement = '';

	/**
	 * Sets the widget up.
	 *
	 * @param string $id_base     Widget id base.
	 * @param string $name        Widget name.
	 * @param string $description Widget description.
	 */
	public function __construct( $id_base, $name, $description ) {
		parent::__construct(
			$id_base,
			$name,
			array(
				'description'                 => $description,
				'classname'                   => 'hikashop-so-widget ' . $id_base,
				'customize_selective_refresh' => true,
			)
		);
	}

	/**
	 * Fields of the settings form: name => array( type, label, default, options, show, description ).
	 *
	 * @return array
	 */
	abstract protected function fields();

	/**
	 * Prints the widget output.
	 *
	 * @param array $settings Widget settings.
	 */
	abstract protected function render_html( $settings );

	/**
	 * Settings with their defaults.
	 *
	 * @param array $instance Saved settings.
	 * @return array
	 */
	protected function settings( $instance ) {
		$settings = array();
		foreach ( $this->fields() as $name => $field ) {
			if ( 'note' === $field['type'] ) {
				continue;
			}
			$default           = isset( $field['default'] ) ? (string) $field['default'] : '';
			$settings[ $name ] = isset( $instance[ $name ] ) && is_scalar( $instance[ $name ] ) ? (string) $instance[ $name ] : $default;
		}
		return $settings;
	}

	/**
	 * Frontend output.
	 *
	 * @param array $args     Widget area arguments.
	 * @param array $instance Saved settings.
	 */
	public function widget( $args, $instance ) {
		$instance = is_array( $instance ) ? $instance : array();
		if ( ! empty( $instance['panels_info']['widget_id'] ) ) {
			$this->placement = (string) $instance['panels_info']['widget_id'];
		} elseif ( ! empty( $args['widget_id'] ) ) {
			$this->placement = (string) $args['widget_id'];
		} else {
			$this->placement = (string) $this->id;
		}

		$settings = $this->settings( $instance );
		ob_start();
		Render::as_site(
			function () use ( $settings ) {
				$this->render_html( $settings );
			}
		);
		$body = (string) ob_get_clean();
		if ( '' === $body ) {
			return;
		}

		// The wrappers come from the widget area or the page builder, as for WordPress's own widgets
		// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $args['before_widget'] ?? '';
		if ( ! empty( $instance['title'] ) ) {
			echo ( $args['before_title'] ?? '' ) . esc_html( apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) ) . ( $args['after_title'] ?? '' );
		}
		// Already escaped by Render::output() and Render::placeholder()
		echo $body;
		echo $args['after_widget'] ?? '';
		// phpcs:enable
	}

	/**
	 * Whether a listing has nothing to show: HikaShop prints an empty listing frame when nothing matches.
	 *
	 * @param string $html Listing HTML.
	 * @return bool
	 */
	protected static function is_empty( $html ) {
		return '' === trim( wp_strip_all_tags( $html ) ) && ! preg_match( '#<(img|input|select|button)\b#i', $html );
	}

	/**
	 * Unique id of this placement, for the HTML ids of HikaShop's listing.
	 *
	 * @param string $prefix Prefix of the id.
	 * @return string
	 */
	protected function div_name( $prefix ) {
		$id = strtolower( preg_replace( '/[^a-z0-9]/i', '', $this->placement ) );
		return $prefix . '_' . ( '' === $id ? 'widget' : substr( $id, 0, 16 ) );
	}

	/**
	 * Settings form.
	 *
	 * @param array $instance Saved settings.
	 * @return string
	 */
	public function form( $instance ) {
		$instance = is_array( $instance ) ? $instance : array();
		$title    = isset( $instance['title'] ) ? (string) $instance['title'] : '';
		echo '<div class="hikashop-so-form">';
		$this->field_row(
			'title',
			array(
				'type'  => 'text',
				'label' => __( 'Title', 'hikashop-for-siteorigin-page-builder' ),
			),
			$title
		);
		$settings = $this->settings( $instance );
		foreach ( $this->fields() as $name => $field ) {
			$this->field_row( $name, $field, $settings[ $name ] ?? '' );
		}
		echo '</div>';
		return '';
	}

	/**
	 * One field of the form.
	 *
	 * @param string $name  Setting name.
	 * @param array  $field Field definition.
	 * @param string $value Current value.
	 */
	private function field_row( $name, $field, $value ) {
		$id   = $this->get_field_id( $name );
		$attr = '';
		if ( ! empty( $field['show'] ) ) {
			// Shown only for some values of another field, see assets/js/admin.js
			$attr = ' data-show-field="' . esc_attr( $field['show'][0] ) . '" data-show-values="' . esc_attr( implode( ',', $field['show'][1] ) ) . '"';
		}
		echo '<div class="hikashop-so-field hikashop-so-field-' . esc_attr( $name ) . '" data-field="' . esc_attr( $name ) . '"' . $attr . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		switch ( $field['type'] ) {
			case 'note':
				echo '<span class="description">' . esc_html( $field['label'] ) . '</span>';
				break;
			case 'select':
			case 'category':
				$options = 'category' === $field['type'] ? Listing::category_options() : $field['options'];
				echo '<label for="' . esc_attr( $id ) . '">' . esc_html( $field['label'] ) . '</label>';
				echo '<select class="widefat" id="' . esc_attr( $id ) . '" name="' . esc_attr( $this->get_field_name( $name ) ) . '">';
				foreach ( $options as $option => $label ) {
					echo '<option value="' . esc_attr( $option ) . '"' . selected( (string) $option, $value, false ) . '>' . esc_html( $label ) . '</option>';
				}
				echo '</select>';
				break;
			case 'number':
				echo '<label for="' . esc_attr( $id ) . '">' . esc_html( $field['label'] ) . '</label>';
				echo '<input class="tiny-text" type="number" min="0" step="1" id="' . esc_attr( $id ) . '" name="' . esc_attr( $this->get_field_name( $name ) ) . '" value="' . esc_attr( $value ) . '">';
				break;
			case 'products':
				echo '<label for="' . esc_attr( $id ) . '-search">' . esc_html( $field['label'] ) . '</label>';
				echo '<div class="hikashop-so-picker">';
				echo '<input type="hidden" class="hikashop-so-picker-value" id="' . esc_attr( $id ) . '" name="' . esc_attr( $this->get_field_name( $name ) ) . '" value="' . esc_attr( $value ) . '">';
				echo '<ol class="hikashop-so-picker-list"></ol>';
				echo '<input type="search" class="widefat hikashop-so-picker-search" id="' . esc_attr( $id ) . '-search" placeholder="' . esc_attr__( 'Search by name, code or id', 'hikashop-for-siteorigin-page-builder' ) . '" autocomplete="off">';
				echo '<ul class="hikashop-so-picker-results"></ul>';
				echo '</div>';
				break;
			default:
				echo '<label for="' . esc_attr( $id ) . '">' . esc_html( $field['label'] ) . '</label>';
				echo '<input class="widefat" type="text" id="' . esc_attr( $id ) . '" name="' . esc_attr( $this->get_field_name( $name ) ) . '" value="' . esc_attr( $value ) . '">';
		}
		echo '</div>';
	}

	/**
	 * Cleans the settings before they are saved.
	 *
	 * @param array $new_instance Submitted settings.
	 * @param array $old_instance Previous settings.
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		unset( $old_instance );
		$instance = array( 'title' => isset( $new_instance['title'] ) ? sanitize_text_field( $new_instance['title'] ) : '' );
		foreach ( $this->fields() as $name => $field ) {
			if ( 'note' === $field['type'] || ! isset( $new_instance[ $name ] ) || ! is_scalar( $new_instance[ $name ] ) ) {
				continue;
			}
			$value = (string) $new_instance[ $name ];
			switch ( $field['type'] ) {
				case 'number':
					$value = '' === $value ? '' : (string) absint( $value );
					break;
				case 'category':
					$value = (string) absint( $value );
					break;
				case 'products':
					$value = implode( ',', Listing::ids( $value ) );
					break;
				case 'select':
					$value = array_key_exists( $value, $field['options'] ) ? $value : (string) ( $field['default'] ?? '' );
					break;
				default:
					$value = sanitize_text_field( $value );
			}
			$instance[ $name ] = $value;
		}
		return $instance;
	}

	/**
	 * An "Inherit / Show / Hide" select of HikaShop listings.
	 *
	 * @param string $label Label.
	 * @return array
	 */
	protected function switch_field( $label ) {
		return array(
			'type'    => 'select',
			'label'   => $label,
			'default' => '',
			'options' => array(
				''  => __( 'Inherit from HikaShop', 'hikashop-for-siteorigin-page-builder' ),
				'1' => __( 'Show', 'hikashop-for-siteorigin-page-builder' ),
				'0' => __( 'Hide', 'hikashop-for-siteorigin-page-builder' ),
			),
		);
	}

	/**
	 * A "Yes / No" select.
	 *
	 * @param string $label   Label.
	 * @param string $default Default value, '1' or '0'.
	 * @return array
	 */
	protected function yes_no_field( $label, $default ) {
		return array(
			'type'    => 'select',
			'label'   => $label,
			'default' => $default,
			'options' => array(
				'1' => __( 'Yes', 'hikashop-for-siteorigin-page-builder' ),
				'0' => __( 'No', 'hikashop-for-siteorigin-page-builder' ),
			),
		);
	}

	/**
	 * The item layouts of HikaShop listings.
	 *
	 * @return array
	 */
	protected function item_layout_options() {
		return array(
			'inherit'           => __( 'Inherit from HikaShop', 'hikashop-for-siteorigin-page-builder' ),
			'img_title'         => __( 'Image and title', 'hikashop-for-siteorigin-page-builder' ),
			'img'               => __( 'Image', 'hikashop-for-siteorigin-page-builder' ),
			'title'             => __( 'Title', 'hikashop-for-siteorigin-page-builder' ),
			'img_desc'          => __( 'Image and description', 'hikashop-for-siteorigin-page-builder' ),
			'img_description'   => __( 'Image, title and description', 'hikashop-for-siteorigin-page-builder' ),
			'img_pane'          => __( 'Image with a sliding pane', 'hikashop-for-siteorigin-page-builder' ),
			'fade'              => __( 'Fade', 'hikashop-for-siteorigin-page-builder' ),
			'hover_x_img_title' => __( 'Image swap on hover', 'hikashop-for-siteorigin-page-builder' ),
			'slider_horizontal' => __( 'Horizontal slider', 'hikashop-for-siteorigin-page-builder' ),
			'slider_vertical'   => __( 'Vertical slider', 'hikashop-for-siteorigin-page-builder' ),
		);
	}
}
