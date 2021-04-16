<?php

namespace Golo_Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;

defined( 'ABSPATH' ) || exit;

class Widget_Instagram extends Base {

	public function get_name() {
		return 'golo-instagram';
	}

	public function get_title() {
		return esc_html__( 'Instagram', 'golo' );
	}

	public function get_icon_part() {
		return 'eicon-instagram-gallery';
	}

	public function get_keywords() {
		return [ 'media', 'instagram' ];
	}

	protected function _register_controls() {
		$this->add_content_section();

		$this->add_image_style_section();

		$this->add_overlay_style_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		// Do nothing if username is null.
		if ( empty( $settings['username'] ) ) {
			return;
		}

		$this->add_render_attribute( 'wrapper', 'class', 'golo-instagram' );

		$media_array = $this->scrape_instagram( $settings['username'], $settings['number_items'] );
		if ( is_wp_error( $media_array ) ) {
			?>
			<div class="golo-instagram--error">
				<?php echo '<p>' . $media_array->get_error_message() . '</p>'; ?>
			</div>
			<?php
		} else {
			?>
			<div <?php $this->print_attributes_string( 'wrapper' ); ?>>
				<div class="modern-grid">
					<?php foreach ( $media_array as $item ) { ?>
						<div class="grid-item golo-box">
							<div class="image golo-image">
								<a href="<?php echo esc_url( $item['link'] ); ?>"
									<?php if ( 'yes' === $settings['link_target'] ) : ?>
										target="_blank"
									<?php endif; ?>
                                   rel="nofollow"
								>
									<img src="<?php echo esc_url( $item[ $settings['size'] ] ); ?>"
									     alt="<?php esc_attr_e( 'Instagram Image', 'golo' ); ?>"/>

									<?php if ( 'yes' === $settings['show_likes_comments'] ) : ?>
										<div class="overlay">
											<div class="image-meta">
												<span class="likes"><?php echo esc_html( $item['likes'] ); ?></span>
												<span
													class="comments"><?php echo esc_html( $item['comments'] ); ?></span>
											</div>
										</div>
									<?php endif; ?>
								</a>
							</div>
						</div>
					<?php } ?>
				</div>

				<?php if ( 'yes' === $settings['show_user_name'] ) : ?>
					<div class="instagram-user-name">
						<?php echo '@' . esc_html( $settings['username'] ); ?>
					</div>
				<?php endif; ?>

			</div>
			<?php
		}
	}

	private function add_content_section() {
		$this->start_controls_section( 'content_section', [
			'label' => esc_html__( 'Content', 'golo' ),
		] );

		$this->add_control( 'hover_effect', [
			'label'        => esc_html__( 'Hover Effect', 'golo' ),
			'type'         => Controls_Manager::SELECT,
			'options'      => [
				''         => esc_html__( 'None', 'golo' ),
				'zoom-in'  => esc_html__( 'Zoom In', 'golo' ),
				'zoom-out' => esc_html__( 'Zoom Out', 'golo' ),
			],
			'default'      => '',
			'prefix_class' => 'golo-animation-',
		] );

		$this->add_control( 'username', [
			'label'   => esc_html__( 'User Name', 'golo' ),
			'type'    => Controls_Manager::TEXT,
			'default' => 'unsplash',
		] );

		$this->add_control( 'size', [
			'label'     => esc_html__( 'Image Size', 'golo' ),
			'type'      => Controls_Manager::SELECT,
			'options'   => [
				'thumbnail'   => esc_html__( 'Thumbnail 150x150', 'golo' ),
				'small'       => esc_html__( 'Small 240x240', 'golo' ),
				'medium'      => esc_html__( 'Medium 320x320', 'golo' ),
				'large'       => esc_html__( 'Large 480x480', 'golo' ),
				'extra_large' => esc_html__( 'Extra Large 640x640', 'golo' ),
				'original'    => esc_html__( 'Original', 'golo' ),
			],
			'default'   => 'large',
			'separator' => 'after',
		] );

		$this->add_control( 'number_items', [
			'label'   => esc_html__( 'Number Items', 'golo' ),
			'type'    => Controls_Manager::NUMBER,
			'min'     => 1,
			'max'     => 12,
			'step'    => 1,
			'default' => 6,
		] );

		$this->add_responsive_control( 'columns', [
			'label'          => esc_html__( 'Columns', 'golo' ),
			'type'           => Controls_Manager::NUMBER,
			'min'            => 1,
			'max'            => 12,
			'step'           => 1,
			'default'        => 6,
			'tablet_default' => 3,
			'mobile_default' => 2,
			'selectors'      => [
				'{{WRAPPER}} .modern-grid' => 'grid-template-columns: repeat( {{VALUE}}, 1fr);',
			],
		] );

		$this->add_responsive_control( 'column_gutter', [
			'label'     => esc_html__( 'Column Gutter', 'golo' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [
				'px' => [
					'min'  => 0,
					'max'  => 200,
					'step' => 5,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .modern-grid' => 'grid-column-gap: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->add_responsive_control( 'row_gutter', [
			'label'     => esc_html__( 'Row Gutter', 'golo' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [
				'px' => [
					'min'  => 0,
					'max'  => 200,
					'step' => 5,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .modern-grid' => 'grid-row-gap: {{SIZE}}{{UNIT}};',
			],
			'separator' => 'after',
		] );

		$this->add_control( 'show_user_name', [
			'label'     => esc_html__( 'User Name', 'golo' ),
			'type'      => Controls_Manager::SWITCHER,
			'label_on'  => esc_html__( 'Show', 'golo' ),
			'label_off' => esc_html__( 'Hide', 'golo' ),
		] );

		$this->add_control( 'show_likes_comments', [
			'label'     => esc_html__( 'Likes & Comments', 'golo' ),
			'type'      => Controls_Manager::SWITCHER,
			'label_on'  => esc_html__( 'Show', 'golo' ),
			'label_off' => esc_html__( 'Hide', 'golo' ),
		] );

		$this->add_control( 'link_target', [
			'label' => esc_html__( 'Open links in a new tab.', 'golo' ),
			'type'  => Controls_Manager::SWITCHER,
		] );

		$this->end_controls_section();
	}

	private function add_image_style_section() {
		$this->start_controls_section( 'image_style_section', [
			'label' => esc_html__( 'Image', 'golo' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'image_border_radius', [
			'label'      => esc_html__( 'Border Radius', 'golo' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', '%' ],
			'range'      => [
				'px' => [
					'min' => 0,
					'max' => 200,
				],
				'%'  => [
					'min' => 0,
					'max' => 100,
				],
			],
			'selectors'  => [
				'{{WRAPPER}} .golo-image, {{WRAPPER}} .golo-image img' => 'border-radius: {{SIZE}}{{UNIT}}',
			],
		] );

		$this->start_controls_tabs( 'image_effects_tabs' );

		$this->start_controls_tab( 'image_effects_normal_tab', [
			'label' => esc_html__( 'Normal', 'golo' ),
		] );

		$this->add_group_control( Group_Control_Css_Filter::get_type(), [
			'name'     => 'css_filters',
			'selector' => '{{WRAPPER}} .image img',
		] );

		$this->add_control( 'image_opacity', [
			'label'     => esc_html__( 'Opacity', 'golo' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [
				'px' => [
					'max'  => 1,
					'min'  => 0.10,
					'step' => 0.01,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .image img' => 'opacity: {{SIZE}};',
			],
		] );

		$this->end_controls_tab();

		$this->start_controls_tab( 'image_effects_hover_tab', [
			'label' => esc_html__( 'Hover', 'golo' ),
		] );

		$this->add_group_control( Group_Control_Css_Filter::get_type(), [
			'name'     => 'css_filters_hover',
			'selector' => '{{WRAPPER}} .grid-item:hover .image img',
		] );

		$this->add_control( 'image_opacity_hover', [
			'label'     => esc_html__( 'Opacity', 'golo' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [
				'px' => [
					'max'  => 1,
					'min'  => 0.10,
					'step' => 0.01,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .grid-item:hover .image img' => 'opacity: {{SIZE}};',
			],
		] );

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	private function add_overlay_style_section() {
		$this->start_controls_section( 'overlay_style_section', [
			'label' => esc_html__( 'Overlay', 'golo' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_group_control( Group_Control_Background::get_type(), [
			'name'     => 'overlay_background',
			'selector' => '{{WRAPPER}} .overlay',
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'overlay_typography',
			'label'    => esc_html__( 'Typography', 'golo' ),
			'selector' => '{{WRAPPER}} .image-meta',
		] );

		$this->end_controls_section();
	}

	/**
	 * Quick-and-dirty Instagram web scrape
	 * based on https://gist.github.com/cosmocatalano/4544576
	 *
	 * @param string $username Instagram username that get on url.
	 * @param int    $slice    Number of images want to get. Max up to 12 images.
	 *
	 * @return array|\WP_Error
	 */
	private function scrape_instagram( $username, $slice ) {

		$username = trim( strtolower( $username ) );

		switch ( substr( $username, 0, 1 ) ) {
			case '#':
				$url              = 'https://instagram.com/explore/tags/' . str_replace( '#', '', $username );
				$transient_prefix = 'h';
				break;
			default:
				$url              = 'https://instagram.com/' . str_replace( '@', '', $username );
				$transient_prefix = 'u';
				break;
		}

		$transient_name = "instagram-media-{$transient_prefix}-" . sanitize_title_with_dashes( $username );

		$cached_images = get_transient( $transient_name );

		// If the photos not cached, then get from server.
		if ( false === $cached_images ) {
			$remote = wp_remote_get( $url );

			if ( is_wp_error( $remote ) ) {
				return new \WP_Error( 'site_down', esc_html__( 'Unable to communicate with Instagram.', 'golo' ) );
			}

			if ( 200 != wp_remote_retrieve_response_code( $remote ) ) {
				return new \WP_Error( 'invalid_response', esc_html__( 'Instagram did not return a 200.', 'golo' ) );
			}

			$shards      = explode( 'window._sharedData = ', $remote['body'] );
			$insta_json  = explode( ';</script>', $shards[1] );
			$insta_array = json_decode( $insta_json[0], true );

			if ( ! $insta_array ) {
				return new \WP_Error( 'bad_json', esc_html__( 'Instagram has returned invalid data.', 'golo' ) );
			}

			if ( isset( $insta_array['entry_data']['ProfilePage'][0]['graphql']['user']['edge_owner_to_timeline_media']['edges'] ) ) {
				$images = $insta_array['entry_data']['ProfilePage'][0]['graphql']['user']['edge_owner_to_timeline_media']['edges'];
			} elseif ( isset( $insta_array['entry_data']['TagPage'][0]['graphql']['hashtag']['edge_hashtag_to_media']['edges'] ) ) {
				$images = $insta_array['entry_data']['TagPage'][0]['graphql']['hashtag']['edge_hashtag_to_media']['edges'];
			} else {
				return new \WP_Error( 'bad_json_2', esc_html__( 'Instagram has returned invalid data.', 'golo' ) );
			}

			if ( ! is_array( $images ) ) {
				return new \WP_Error( 'bad_array', esc_html__( 'Instagram has returned invalid data.', 'golo' ) );
			}

			$cached_images = array();

			foreach ( $images as $image ) {
				$image = $image['node'];

				if ( true === $image['is_video'] ) {
					$type = 'video';
				} else {
					$type = 'image';
				}

				$caption = esc_html__( 'Instagram Image', 'golo' );
				if ( ! empty( $image['edge_media_to_caption']['edges'][0]['node']['text'] ) ) {
					$caption = wp_kses( $image['edge_media_to_caption']['edges'][0]['node']['text'], array() );
				}

				$image['thumbnail_src'] = preg_replace( '/^https?\:/i', '', $image['thumbnail_src'] );
				$image['display_url']   = preg_replace( '/^https?\:/i', '', $image['display_url'] );

				if ( isset( $image['thumbnail_resources'] ) && ! empty( $image['thumbnail_resources'] ) ) {
					foreach ( $image['thumbnail_resources'] as $photo ) {
						switch ( $photo['config_width'] ) {
							case 150 :
								$image['thumbnail'] = $photo['src'];
								break;
							case 240 :
								$image['small'] = $photo['src'];
								break;
							case 320 :
								$image['medium'] = $photo['src'];
								break;
							case 480 :
								$image['large'] = $photo['src'];
								break;
							case 640 :
								$image['extra_large'] = $photo['src'];
								break;
						}
					}
				}

				$cached_images[] = array(
					'description' => $caption,
					'link'        => trailingslashit( '//instagram.com/p/' . $image['shortcode'] ),
					'time'        => $image['taken_at_timestamp'],
					'comments'    => $this->round_number( $image['edge_media_to_comment']['count'] ),
					'likes'       => $this->round_number( $image['edge_liked_by']['count'] ),
					'thumbnail'   => $image['thumbnail'],
					'small'       => $image['small'],
					'medium'      => $image['medium'],
					'large'       => $image['large'],
					'extra_large' => $image['extra_large'],
					'original'    => $image['display_url'],
					'type'        => $type,
				);
			}

			if ( empty( $cached_images ) ) {
				return new \WP_Error( 'no_images', esc_html__( 'Instagram did not return any images.', 'golo' ) );
			}

			set_transient( $transient_name, $cached_images, apply_filters( 'golo_instagram_cache_time', DAY_IN_SECONDS * 2 ) );
		}

		$photos_array = array_slice( $cached_images, 0, $slice );

		return $photos_array;
	}

	/**
	 * Generate rounded number
	 * Example: 11200 --> 11K
	 *
	 * @param $number
	 *
	 * @return string
	 */
	private function round_number( $number ) {
		if ( $number > 999 && $number <= 999999 ) {
			$result = floor( $number / 1000 ) . ' K';
		} elseif ( $number > 999999 ) {
			$result = floor( $number / 1000000 ) . ' M';
		} else {
			$result = $number;
		}

		return $result;
	}
}
