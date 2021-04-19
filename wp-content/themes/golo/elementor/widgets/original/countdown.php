<?php

namespace Golo_Elementor;

use Elementor\Controls_Manager;

defined( 'ABSPATH' ) || exit;

class Modify_Widget_Countdown extends Modify_Base {

	private static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public function initialize() {
		add_action( 'elementor/element/countdown/section_countdown/after_section_start', [
			$this,
			'section_countdown',
		] );
	}

	/**
	 * @param \Elementor\Widget_Base $element The edited element.
	 */
	public function section_countdown( $element ) {
		$element->add_control( 'style', [
			'label'        => esc_html__( 'Style', 'golo' ),
			'type'         => Controls_Manager::SELECT,
			'options'      => [
				''   => esc_html__( 'Normal', 'golo' ),
				'01' => esc_html__( 'Style 01', 'golo' ),
			],
			'default'      => '',
			'prefix_class' => 'golo-countdown-style-',
		] );
	}
}

Modify_Widget_Countdown::instance()->initialize();
