<?php

namespace Golo_Elementor;

defined( 'ABSPATH' ) || exit;

class WPML_Translatable_Nodes {

	private static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public function initialize() {
		add_action( 'init', [ $this, 'wp_init' ] );
	}

	public function wp_init() {
		add_filter( 'wpml_elementor_widgets_to_translate', [ $this, 'wpml_widgets_to_translate_filter' ] );
	}

	public function get_translatable_node() {
		require_once GOLO_ELEMENTOR_DIR . '/wpml/class-translate-widget-google-map.php';
		require_once GOLO_ELEMENTOR_DIR . '/wpml/class-translate-widget-list.php';
		require_once GOLO_ELEMENTOR_DIR . '/wpml/class-translate-widget-attribute-list.php';
		require_once GOLO_ELEMENTOR_DIR . '/wpml/class-translate-widget-pricing-table.php';
		require_once GOLO_ELEMENTOR_DIR . '/wpml/class-translate-widget-table.php';
		require_once GOLO_ELEMENTOR_DIR . '/wpml/class-translate-widget-modern-carousel.php';
		require_once GOLO_ELEMENTOR_DIR . '/wpml/class-translate-widget-modern-slider.php';
		require_once GOLO_ELEMENTOR_DIR . '/wpml/class-translate-widget-team-member-carousel.php';
		require_once GOLO_ELEMENTOR_DIR . '/wpml/class-translate-widget-testimonial-carousel.php';

		$widgets['golo-attribute-list'] = [
			'fields'            => [],
			'integration-class' => '\Golo_Elementor\Translate_Widget_Attribute_List',
		];

		$widgets['golo-heading'] = [
			'fields' => [
				[
					'field'       => 'title',
					'type'        => esc_html__( 'Modern Heading: Primary', 'golo' ),
					'editor_type' => 'AREA',
				],
				'title_link' => [
					'field'       => 'url',
					'type'        => esc_html__( 'Modern Heading: Link', 'golo' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'description',
					'type'        => esc_html__( 'Modern Heading: Description', 'golo' ),
					'editor_type' => 'AREA',
				],
				[
					'field'       => 'sub_title_text',
					'type'        => esc_html__( 'Modern Heading: Secondary', 'golo' ),
					'editor_type' => 'AREA',
				],
			],
		];

		$widgets['golo-button'] = [
			'fields' => [
				[
					'field'       => 'text',
					'type'        => esc_html__( 'Button: Text', 'golo' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'badge_text',
					'type'        => esc_html__( 'Button: Badge', 'golo' ),
					'editor_type' => 'LINE',
				],
				'link' => [
					'field'       => 'url',
					'type'        => esc_html__( 'Button: Link', 'golo' ),
					'editor_type' => 'LINK',
				],
			],
		];

		$widgets['golo-banner'] = [
			'fields' => [
				[
					'field'       => 'title_text',
					'type'        => esc_html__( 'Banner: Title', 'golo' ),
					'editor_type' => 'LINE',
				],
				'link' => [
					'field'       => 'url',
					'type'        => esc_html__( 'Banner: Link', 'golo' ),
					'editor_type' => 'LINK',
				],
			],
		];

		$widgets['golo-circle-progress-chart'] = [
			'fields' => [
				[
					'field'       => 'inner_content_text',
					'type'        => esc_html__( 'Circle Chart: Text', 'golo' ),
					'editor_type' => 'LINE',
				],
			],
		];

		$widgets['golo-flip-box'] = [
			'fields' => [
				[
					'field'       => 'title_text_a',
					'type'        => esc_html__( 'Flip Box: Front Title', 'golo' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'description_text_a',
					'type'        => esc_html__( 'Flip Box: Front Description', 'golo' ),
					'editor_type' => 'AREA',
				],
				[
					'field'       => 'title_text_b',
					'type'        => esc_html__( 'Flip Box: Back Title', 'golo' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'description_text_b',
					'type'        => esc_html__( 'Flip Box: Back Description', 'golo' ),
					'editor_type' => 'AREA',
				],
				[
					'field'       => 'button_text',
					'type'        => esc_html__( 'Flip Box: Button Text', 'golo' ),
					'editor_type' => 'LINE',
				],
				'link' => [
					'field'       => 'url',
					'type'        => esc_html__( 'Flip Box: Link', 'golo' ),
					'editor_type' => 'LINK',
				],
			],
		];

		$widgets['golo-google-map'] = [
			'fields'            => [],
			'integration-class' => '\Golo_Elementor\Translate_Widget_Google_Map',
		];

		$widgets['golo-icon'] = [
			'fields' => [
				'link' => [
					'field'       => 'url',
					'type'        => esc_html__( 'Icon: Link', 'golo' ),
					'editor_type' => 'LINK',
				],
			],
		];

		$widgets['golo-icon-box'] = [
			'fields' => [
				[
					'field'       => 'title_text',
					'type'        => esc_html__( 'Icon Box: Title', 'golo' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'description_text',
					'type'        => esc_html__( 'Icon Box: Description', 'golo' ),
					'editor_type' => 'AREA',
				],
				'link'        => [
					'field'       => 'url',
					'type'        => esc_html__( 'Icon Box: Link', 'golo' ),
					'editor_type' => 'LINK',
				],
				[
					'field'       => 'button_text',
					'type'        => esc_html__( 'Icon Box: Button', 'golo' ),
					'editor_type' => 'LINE',
				],
				'button_link' => [
					'field'       => 'url',
					'type'        => esc_html__( 'Icon Box: Button Link', 'golo' ),
					'editor_type' => 'LINK',
				],
			],
		];

		$widgets['golo-image-box'] = [
			'fields' => [
				[
					'field'       => 'title_text',
					'type'        => esc_html__( 'Image Box: Title', 'golo' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'description_text',
					'type'        => esc_html__( 'Image Box: Content', 'golo' ),
					'editor_type' => 'AREA',
				],
				'link' => [
					'field'       => 'url',
					'type'        => esc_html__( 'Image Box: Link', 'golo' ),
					'editor_type' => 'LINK',
				],
				[
					'field'       => 'button_text',
					'type'        => esc_html__( 'Image Box: Button', 'golo' ),
					'editor_type' => 'LINE',
				],
			],
		];

		$widgets['golo-list'] = [
			'fields'            => [],
			'integration-class' => '\Golo_Elementor\Translate_Widget_List',
		];

		$widgets['golo-popup-video'] = [
			'fields' => [
				[
					'field'       => 'video_text',
					'type'        => esc_html__( 'Popup Video: Text', 'golo' ),
					'editor_type' => 'LINE',
				],
				'video_url' => [
					'field'       => 'url',
					'type'        => esc_html__( 'Popup Video: Link', 'golo' ),
					'editor_type' => 'LINK',
				],
				[
					'field'       => 'poster_caption',
					'type'        => esc_html__( 'Popup Video: Caption', 'golo' ),
					'editor_type' => 'AREA',
				],
			],
		];

		$widgets['golo-pricing-table'] = [
			'fields'            => [
				[
					'field'       => 'heading',
					'type'        => esc_html__( 'Pricing Table: Heading', 'golo' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'sub_heading',
					'type'        => esc_html__( 'Pricing Table: Description', 'golo' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'currency',
					'type'        => esc_html__( 'Pricing Table: Currency', 'golo' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'price',
					'type'        => esc_html__( 'Pricing Table: Price', 'golo' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'period',
					'type'        => esc_html__( 'Pricing Table: Period', 'golo' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'button_text',
					'type'        => esc_html__( 'Pricing Table: Button', 'golo' ),
					'editor_type' => 'LINE',
				],
				'button_link' => [
					'field'       => 'url',
					'type'        => esc_html__( 'Pricing Table: Button Link', 'golo' ),
					'editor_type' => 'LINK',
				],
			],
			'integration-class' => '\Golo_Elementor\Translate_Widget_Pricing_Table',
		];

		$widgets['golo-table'] = [
			'fields'            => [],
			'integration-class' => [
				'\Golo_Elementor\Translate_Widget_Pricing_Table_Head',
				'\Golo_Elementor\Translate_Widget_Pricing_Table_Body',
			],
		];

		$widgets['golo-team-member'] = [
			'fields' => [
				[
					'field'       => 'name',
					'type'        => esc_html__( 'Team Member: Name', 'golo' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'content',
					'type'        => esc_html__( 'Team Member: Content', 'golo' ),
					'editor_type' => 'AREA',
				],
				[
					'field'       => 'position',
					'type'        => esc_html__( 'Team Member: Position', 'golo' ),
					'editor_type' => 'LINE',
				],
				'profile' => [
					'field'       => 'url',
					'type'        => esc_html__( 'Team Member: Profile', 'golo' ),
					'editor_type' => 'LINK',
				],
			],
		];

		$widgets['golo-modern-carousel'] = [
			'fields'            => [],
			'integration-class' => '\Golo_Elementor\Translate_Widget_Modern_Carousel',
		];

		$widgets['golo-modern-slider'] = [
			'fields'            => [],
			'integration-class' => '\Golo_Elementor\Translate_Widget_Modern_Slider',
		];

		$widgets['golo-team-member-carousel'] = [
			'fields'            => [],
			'integration-class' => '\Golo_Elementor\Translate_Widget_Team_Member_Carousel',
		];

		$widgets['golo-testimonial-carousel'] = [
			'fields'            => [],
			'integration-class' => '\Golo_Elementor\Translate_Widget_Testimonial_Carousel',
		];

		return $widgets;
	}

	public function wpml_widgets_to_translate_filter( $widgets ) {
		$golo_widgets = $this->get_translatable_node();

		foreach ( $golo_widgets as $widget_name => $widget ) {
			$widgets[ $widget_name ]               = $widget;
			$widgets[ $widget_name ]['conditions'] = [
				'widgetType' => $widget_name,
			];
		}

		return $widgets;
	}
}

WPML_Translatable_Nodes::instance()->initialize();
