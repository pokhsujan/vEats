<?php

namespace Golo_Elementor;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;

defined( 'ABSPATH' ) || exit;

class Widget_Table extends Base {

	public function get_name() {
		return 'golo-table';
	}

	public function get_title() {
		return esc_html__( 'Table', 'golo' );
	}

	public function get_icon_part() {
		return 'eicon-table';
	}

	public function get_keywords() {
		return [ 'table' ];
	}

	protected function _register_controls() {
		$this->start_controls_section( 'layout_section', [
			'label' => esc_html__( 'Layout', 'golo' ),
		] );

		$this->add_control( 'style', [
			'label'   => esc_html__( 'Style', 'golo' ),
			'type'    => Controls_Manager::SELECT,
			'default' => '01',
			'options' => [
				'01' => '01',
			],
		] );

		$this->end_controls_section();

		$this->start_controls_section( 'table_head_section', [
			'label' => esc_html__( 'Table Header', 'golo' ),
		] );

		$table_header = new Repeater();

		$table_header->add_control( 'action', [
			'label'       => esc_html__( 'Action', 'golo' ),
			'description' => esc_html__( 'You have started a new row. Please add new cells in your row by clicking Add Item button below.', 'golo' ),
			'type'        => Controls_Manager::SELECT,
			'default'     => 'Cell',
			'options'     => [
				'Row'  => esc_html__( 'Start New Row', 'golo' ),
				'Cell' => esc_html__( 'Add New Cell', 'golo' ),
			],
		] );

		$table_header->add_control( 'text', [
			'label'     => esc_html__( 'Text', 'golo' ),
			'type'      => Controls_Manager::TEXTAREA,
			'default'   => esc_html__( 'Sample', 'golo' ),
			'condition' => [
				'action' => 'Cell',
			],
		] );

		$this->add_control( 'table_head', [
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $table_header->get_controls(),
			'default'     => [
				[
					'action' => 'Row',
				],
				[
					'action' => 'Cell',
					'text'   => 'Sample #1',
				],
				[
					'action' => 'Cell',
					'text'   => 'Sample #2',
				],
				[
					'action' => 'Cell',
					'text'   => 'Sample #3',
				],
			],
			'title_field' => '{{{ action }}}',
		] );

		$this->end_controls_section();

		$this->start_controls_section( 'table_body_section', [
			'label' => esc_html__( 'Table Content', 'golo' ),
		] );

		$table_content = new Repeater();

		$table_content->add_control( 'action', [
			'label'       => esc_html__( 'Action', 'golo' ),
			'description' => esc_html__( 'You have started a new row. Please add new cells in your row by clicking Add Item button below.', 'golo' ),
			'type'        => Controls_Manager::SELECT,
			'default'     => 'Cell',
			'options'     => [
				'Row'  => esc_html__( 'Start New Row', 'golo' ),
				'Cell' => esc_html__( 'Add New Cell', 'golo' ),
			],
		] );

		$table_content->add_control( 'text', [
			'label'     => esc_html__( 'Text', 'golo' ),
			'type'      => Controls_Manager::TEXTAREA,
			'default'   => esc_html__( 'Sample', 'golo' ),
			'condition' => [
				'action' => 'Cell',
			],
		] );

		$this->add_control( 'table_body', [
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $table_content->get_controls(),
			'default'     => [
				[
					'action' => 'Row',
				],
				[
					'action' => 'Cell',
					'text'   => 'Sample #1',
				],
				[
					'action' => 'Cell',
					'text'   => 'Sample #2',
				],
				[
					'action' => 'Cell',
					'text'   => 'Sample #3',
				],
			],
			'title_field' => '{{{ action }}}',
		] );

		$this->end_controls_section();

		$this->start_controls_section( 'table_head_style_section', [
			'label' => esc_html__( 'Table Header', 'golo' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'      => 'table_head_typography',
			'selector'  => '{{WRAPPER}} th',
			'separator' => 'after',
		] );

		$this->add_responsive_control( 'table_head_padding', [
			'label'      => esc_html__( 'Padding', 'golo' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%', 'em' ],
			'selectors'  => [
				'{{WRAPPER}} th' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
			'separator'  => 'after',
		] );

		$this->add_responsive_control( 'table_head_align', [
			'label'     => esc_html__( 'Text Align', 'golo' ),
			'type'      => Controls_Manager::CHOOSE,
			'options'   => Widget_Utils::get_control_options_text_align(),
			'selectors' => [
				'{{WRAPPER}} th' => 'text-align: {{VALUE}};',
			],
		] );

		$this->end_controls_section();

		$this->start_controls_section( 'table_body_style_section', [
			'label' => esc_html__( 'Table Content', 'golo' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'table_body_typography',
			'selector' => '{{WRAPPER}} td',
		] );

		$this->add_responsive_control( 'table_body_padding', [
			'label'      => esc_html__( 'Padding', 'golo' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%', 'em' ],
			'selectors'  => [
				'{{WRAPPER}} td' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
			'separator'  => 'after',
		] );

		$this->add_responsive_control( 'table_body_align', [
			'label'     => esc_html__( 'Text Align', 'golo' ),
			'type'      => Controls_Manager::CHOOSE,
			'options'   => Widget_Utils::get_control_options_text_align(),
			'selectors' => [
				'{{WRAPPER}} td' => 'text-align: {{VALUE}};',
			],
		] );

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'wrapper', 'class', 'golo-table' );
		$this->add_render_attribute( 'wrapper', 'class', 'style-' . $settings['style'] );
		?>
		<div <?php $this->print_attributes_string( 'wrapper' ); ?>>
			<table>
				<?php if ( ! empty( $settings['table_head'] ) ) { ?>
					<thead>
					<?php
					$th_count  = count( $settings['table_head'] );
					$thl_count = 0;
					?>
					<?php foreach ( $settings['table_head'] as $item ) : ?>
						<?php
						$thl_count += 1;
						if ( $item['action'] === 'Row' ) :
							echo '<tr>';
						else:
							echo '<th>' . $item['text'] . '</th>';
						endif;

						if ( $thl_count === $th_count ) {
							echo '</tr>';
						}
						?>
					<?php endforeach; ?>
					</thead>
				<?php } ?>

				<?php if ( ! empty( $settings['table_body'] ) ) { ?>
					<tbody>
					<?php
					$tb_count  = count( $settings['table_body'] );
					$tbl_count = 0;
					?>
					<?php foreach ( $settings['table_body'] as $item ) : ?>
						<?php
						$tbl_count += 1;
						if ( $item['action'] === 'Row' ) :
							echo '<tr>';
						else:
							echo '<td>' . $item['text'] . '</td>';
						endif;

						if ( $tbl_count === $tb_count ) {
							echo '</tr>';
						}
						?>
					<?php endforeach; ?>
					</tbody>
				<?php } ?>
			</table>
		</div>
		<?php
	}

	protected function _content_template() {
		// @formatter:off
		?>
		<#
		view.addRenderAttribute( 'wrapper', 'class', 'golo-table' );
		view.addRenderAttribute( 'wrapper', 'class', 'style-' + settings.style );
		#>
		<div <# {{{ view.getRenderAttributeString( 'wrapper' ) }}} #> >
			<table>
				<# if ( settings.table_head ) { #>
				<thead>
					<#
					var th_count = settings.table_head.length;
					var thl_count = 0;

					_.each( settings.table_head, function( item, index ) {
					thl_count += 1;
					#>
					<# if ( item.action === 'Row' ) { #>
						<tr>
							<# } else { #>
							<th>{{{ item.text }}}</th>
							<# } #>

							<# if ( thl_count === th_count ) { #>
						</tr>
						<# } #>
					<# }); #>
				</thead>
				<# } #>

				<# if ( settings.table_body ) { #>
					<tbody>
					<#
					var tb_count = settings.table_body.length;
					var tbl_count = 0;

						_.each( settings.table_body, function( item, index ) {
						tbl_count += 1;
						#>
						<# if ( item.action === 'Row' ) { #>
							<tr>
						<# } else { #>
							<td>{{{ item.text }}}</td>
						<# } #>

						<# if ( tbl_count === tb_count ) { #>
							</tr>
						<# } #>
					<# }); #>
					</tbody>
				<# } #>
			</table>
		</div>
		<?php
		// @formatter:off
	}
}
