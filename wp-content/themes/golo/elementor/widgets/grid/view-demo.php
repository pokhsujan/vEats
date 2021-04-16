<?php

namespace Golo_Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Utils;

defined( 'ABSPATH' ) || exit;

class Widget_View_Demo extends Static_Grid {

	public function get_name() {
		return 'golo-view-demo';
	}

	public function get_title() {
		return esc_html__( 'View Demo', 'golo' );
	}

	public function get_icon_part() {
		return 'eicon-gallery-grid';
	}

	public function get_keywords() {
		return [ 'demo' ];
	}

	protected function _register_controls() {
		$this->add_layout_section();

		parent::_register_controls();

		$this->add_content_style_section();

		$this->update_controls();
	}

	private function update_controls() {
		$this->update_control( 'items', [
			'title_field' => '{{{ text }}}',
		] );
	}

	private function add_layout_section() {
		$this->start_controls_section( 'layout_section', [
			'label' => esc_html__( 'Layout', 'golo' ),
		] );

		$this->add_control( 'hover_effect', [
			'label'        => esc_html__( 'Hover Effect', 'golo' ),
			'type'         => Controls_Manager::SELECT,
			'options'      => [
				''                    => esc_html__( 'None', 'golo' ),
				'zoom-in'             => esc_html__( 'Zoom In', 'golo' ),
				'zoom-out'            => esc_html__( 'Zoom Out', 'golo' ),
				'move-up'             => esc_html__( 'Move Up', 'golo' ),
				'move-up-drop-shadow' => esc_html__( 'Move Up - Drop Shadow', 'golo' ),
			],
			'default'      => '',
			'prefix_class' => 'golo-animation-',
		] );

		$this->end_controls_section();
	}

	private function add_content_style_section() {
		$this->start_controls_section( 'content_style_section', [
			'label' => esc_html__( 'Content', 'golo' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_responsive_control( 'text_align', [
			'label'     => esc_html__( 'Text Align', 'golo' ),
			'type'      => Controls_Manager::CHOOSE,
			'options'   => Widget_Utils::get_control_options_text_align_full(),
			'selectors' => [
				'{{WRAPPER}} .golo-box' => 'text-align: {{VALUE}};',
			],
		] );

		$this->add_control( 'title_heading', [
			'label'     => esc_html__( 'Title', 'golo' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		] );

		$this->add_control( 'title_color', [
			'label'     => esc_html__( 'Color', 'golo' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .heading' => 'color: {{VALUE}};',
			],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'title_typography',
			'label'    => esc_html__( 'Typography', 'golo' ),
			'selector' => '{{WRAPPER}} .heading',
		] );

		$this->end_controls_section();
	}

	protected function add_repeater_controls( Repeater $repeater ) {
		$repeater->add_control( 'image', [
			'label'   => esc_html__( 'Image', 'golo' ),
			'type'    => Controls_Manager::MEDIA,
			'default' => [
				'url' => Utils::get_placeholder_image_src(),
			],
		] );

		$repeater->add_control( 'text', [
			'label'   => esc_html__( 'Text', 'golo' ),
			'type'    => Controls_Manager::TEXT,
			'dynamic' => [
				'active' => true,
			],
		] );

		$repeater->add_control( 'link', [
			'label'         => esc_html__( 'Link', 'golo' ),
			'type'          => Controls_Manager::URL,
			'placeholder'   => esc_html__( 'https://your-link.com', 'golo' ),
			'show_external' => true,
			'default'       => [
				'url'         => '',
				'is_external' => false,
				'nofollow'    => false,
			],
		] );

		$repeater->add_control( 'badge', [
			'label'   => esc_html__( 'Badge', 'golo' ),
			'type'    => Controls_Manager::SELECT,
			'default' => '',
			'options' => [
				''       => esc_html__( 'None', 'golo' ),
				'new'    => esc_html__( 'New', 'golo' ),
				'hot'    => esc_html__( 'Hot', 'golo' ),
				'coming' => esc_html__( 'Coming Soon', 'golo' ),
			],
		] );

		$repeater->add_control( 'text_badge', [
			'label' => __( 'Text', 'golo-framework' ),
			'type' => Controls_Manager::TEXT,
			'dynamic' => [
				'active' => true,
			],
			'placeholder' => __( 'Text', 'golo-framework' ),
			'condition' => [
				'badge!' => '',
			],
		] );
	}

	protected function get_repeater_defaults() {
		$placeholder_image_src = Utils::get_placeholder_image_src();

		return [
			[
				'image' => [ 'url' => $placeholder_image_src ],
			],
			[
				'image' => [ 'url' => $placeholder_image_src ],
			],
		];
	}

	protected function print_grid_item() {
		$item     = $this->get_current_item();
		$item_key = $this->get_current_key();

		$box_tag = 'div';
		$box_key = $item_key . '_box';

		$this->add_render_attribute( $box_key, 'class', 'golo-box' );

		if ( ! empty( $item['link']['url'] ) ) {
			$box_tag = 'a';

			$this->add_render_attribute( $box_key, 'class', 'link-secret' );
			$this->add_link_attributes( $box_key, $item['link'] );
		}
		?>
		<?php printf( '<%1$s %2$s>', $box_tag, $this->get_render_attribute_string( $box_key ) ); ?>

		<div class="golo-image image">
			<?php echo \Golo_Image::get_elementor_attachment( [
				'settings' => $item,
			] ); ?>
		</div>

		<?php if ( ! empty( $item['badge'] ) ) : ?>
			<?php if ( 'coming' === $item['badge'] ) { ?>
				<div class="badge coming">
					<span><?php echo esc_html($item['text_badge']); ?></span>
				</div>
			<?php } elseif ( 'new' === $item['badge'] ) { ?>
				<div class="badge new">
					<?php echo esc_html($item['text_badge']); ?>
				</div>
			<?php } elseif ( 'hot' === $item['badge'] ) { ?>
				<div class="badge hot">
					<?php echo esc_html($item['text_badge']); ?>
				</div>
			<?php } ?>
		<?php endif; ?>

		<?php if ( ! empty( $item['text'] ) ): ?>
			<div class="info">
				<h3 class="heading">
					<?php echo esc_html( $item['text'] ); ?>
				</h3>
			</div>
		<?php endif; ?>

		<?php printf( '</%1$s>', $box_tag ); ?>
		<?php
	}

	protected function before_grid() {
		$this->add_render_attribute( 'wrapper', 'class', 'golo-view-demo' );
	}
}
