<?php 
if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

$ajax_search = true;

$layout = Golo_Helper::get_setting('layout_search');

$classes = array( 'block-search', 'search-form-wrapper', 'canvas-search', 'golo-ajax-search', $layout );

if ( $ajax_search ) {
	$classes[] = ' ajax-search-form';
}

$post_type   = 'post';
$place_holder = esc_html__( 'Search posts...', 'golo' );

if ( class_exists('WooCommerce') ) {
	$post_type   = 'product';
	$place_holder = esc_html__( 'Search products...', 'golo' );
}

if ( class_exists('Golo_Framework') ) {
	$post_type   = 'place';
	$place_holder = esc_html__( 'Search places, cities', 'golo' );
}

?>
<div class="<?php echo join( ' ', $classes ); ?>">
	<div class="bg-overlay"></div>
	<a href="#" class="btn-close"><i class="la la-times"></i></a>
	<form action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" class="search-form">
		<div class="area-search form-field">
			<?php if( $layout == 'layout-01' ) : ?>

				<div class="icon-search">
					<i class="la la-search large"></i>
				</div>
				
				<div class="form-field input-field">
					<input name="s" class="input-search" type="text" value="<?php echo get_search_query(); ?>" placeholder="<?php echo esc_attr( $place_holder ); ?>" autocomplete="off" />
					<input type="hidden" name="post_type" class="post-type" value="<?php echo esc_attr( $post_type ); ?>"/>

					<div class="search-result area-result"></div>

					<div class="golo-loading-effect"><span class="golo-dual-ring"></span></div>

					<?php 
						$place_categories = get_categories(array(
                            'taxonomy'   => 'place-categories',
                            'hide_empty' => 1,
                            'orderby'    => 'term_id',
                            'order'      => 'ASC'
                        ));
					?>
					<?php if($place_categories) : ?>
					<div class="list-categories">
						<ul>
							<?php
							$image_src = GOLO_PLUGIN_URL . 'assets/images/no-image.jpg';
							$default_image = golo_get_option('default_place_image','');
							foreach ($place_categories as $cate) {
								$cate_id   = $cate->term_id;
				                $cate_name = $cate->name;
				                $cate_slug = $cate->slug;
				                $cate      = get_term_by( 'id', $cate_id, 'place-categories');
				                $cate_icon = get_term_meta( $cate_id, 'place_categories_icon_marker', true );
				                $link      = home_url('/') . '?s=&post_type=place&category=' . $cate_slug;
				                if ($cate_icon) {
				                	$cate_icon_url = $cate_icon['url'];
				                } else {
				                    if($default_image != '')
                        			    {
                        			        if(is_array($default_image) && $default_image['url'] != '')
                        			        {
                        			            $cate_icon_url = $default_image['url'];
                        			        }
                        			    } else {
                        			        $cate_icon_url = $image_src;
                        			    }
				                }
                            ?>
                                <li>
                                    <a class="entry-category" href="<?php echo esc_url($link); ?>">
				                        <span><?php echo esc_html($cate_name); ?></span>
				                    </a>
                                </li>
                            <?php } ?>
						</ul>
					</div>
					<?php endif; ?>
				</div>

			<?php endif; ?>

			<?php if( $layout == 'layout-02' ) : ?>

				<div class="form-field input-field">
					<label class="input-area" for="find_input_mobile">
						<span><?php esc_html_e('Find', 'golo-framework'); ?></span>
						<input id="find_input_mobile" name="s" class="input-search" type="text" placeholder="<?php esc_attr_e( 'Ex: fastfood, beer' ); ?>" autocomplete="off" />
						<div class="golo-loading-effect"><span class="golo-dual-ring"></span></div>
					</label>
					<div class="search-result area-result"></div>

					<?php 
						$place_categories = get_categories(array(
                            'taxonomy'   => 'place-categories',
                            'hide_empty' => 1,
                            'orderby'    => 'term_id',
                            'order'      => 'ASC'
                        ));
					?>
					<?php if($place_categories) : ?>
					<div class="list-categories focus-result">
						<ul>
							<?php
							$image_src = GOLO_PLUGIN_URL . 'assets/images/no-image.jpg';
							$default_image = golo_get_option('default_place_image','');
							foreach ($place_categories as $cate) {
								$cate_id   = $cate->term_id;
				                $cate_name = $cate->name;
				                $cate_slug = $cate->slug;
				                $cate      = get_term_by( 'id', $cate_id, 'place-categories');
				                $cate_icon = get_term_meta( $cate_id, 'place_categories_icon_marker', true );
				                $link      = home_url('/') . '?s=&post_type=place&category=' . $cate_slug;
				                if ($cate_icon) {
				                	$cate_icon_url = $cate_icon['url'];
				                } else {
				                    if($default_image != '')
                        			    {
                        			        if(is_array($default_image) && $default_image['url'] != '')
                        			        {
                        			            $cate_icon_url = $default_image['url'];
                        			        }
                        			    } else {
                        			        $cate_icon_url = $image_src;
                        			    }
				                }
                            ?>
                                <li>
                                    <a class="entry-category" href="<?php echo esc_url($link); ?>">
				                        <img src="<?php echo esc_url($cate_icon_url) ?>" alt="<?php echo esc_attr($cate_name); ?>">
				                        <span><?php echo esc_html($cate_name); ?></span>
				                    </a>
                                </li>
                            <?php } ?>
						</ul>
					</div>
					<?php endif; ?>
				</div>

				<div class="form-field location-field">
					<label class="location-area" for="find_city_mobile">
						<span><?php esc_html_e('Where', 'golo-framework'); ?></span>
						<input name="location" id="find_city_mobile" class="location-search" type="text" placeholder="<?php esc_attr_e( 'Your city', 'golo-framework' ); ?>" autocomplete="off" />
					</label>

					<button type="submit" class="icon-search">
						<i class="la la-search large"></i>
					</button>

					<div class="location-result area-result"></div>

					<?php 
						$place_cities = get_categories(array(
                            'taxonomy'   => 'place-city',
                            'hide_empty' => 1,
                            'orderby'    => 'term_id',
                            'order'      => 'ASC'
                        ));
					?>
					<?php if($place_cities) : ?>
					<div class="location-result focus-result">
						<ul>
							<?php
							foreach ($place_cities as $cate) {
								$cate_id   = $cate->term_id;
				                $cate_name = $cate->name;
				                $cate_slug = $cate->slug;
				                $cate      = get_term_by( 'id', $cate_id, 'place-city');
                            ?>
                                <li>
                                    <a class="entry-city" href="<?php echo esc_url($link); ?>">
				                        <?php echo esc_html($cate_name); ?>
				                    </a>
                                </li>
                            <?php } ?>
						</ul>
					</div>
					<?php endif; ?>
				</div>

				<input type="hidden" name="post_type" class="post-type" value="<?php echo esc_attr( $post_type ); ?>"/>

			<?php endif; ?>

			<?php if( $layout == 'layout-03' ) : ?>

				<div class="form-field location-field">
					<label class="location-area" for="find_city">
						<span><?php esc_html_e('Where', 'golo-framework'); ?></span>
						<input name="location" id="find_city" class="location-search" type="text" placeholder="<?php esc_attr_e( 'Your city', 'golo-framework' ); ?>" autocomplete="off" />
					</label>

					<div class="location-result"></div>

					<?php 
						$place_cities = get_categories(array(
                            'taxonomy'   => 'place-city',
                            'hide_empty' => 1,
                            'orderby'    => 'term_id',
                            'order'      => 'ASC'
                        ));
					?>
					<?php if($place_cities) : ?>
					<div class="location-result focus-result">
						<ul>
							<?php
							foreach ($place_cities as $cate) {
								$cate_id   = $cate->term_id;
				                $cate_name = $cate->name;
				                $cate_slug = $cate->slug;
				                $cate      = get_term_by( 'id', $cate_id, 'place-city');
                            ?>
                                <li>
                                    <a class="entry-city" href="<?php echo esc_url($link); ?>">
				                        <?php echo esc_html($cate_name); ?>
				                    </a>
                                </li>
                            <?php } ?>
						</ul>
					</div>
					<?php endif; ?>
				</div>

				<div class="form-field type-field">
					<label class="type-area" for="find_type">
						<span><?php esc_html_e('Type', 'golo-framework'); ?></span>
						<input name="place_type" id="find_type" class="type-search" type="text" placeholder="<?php esc_attr_e( 'Place type', 'golo-framework' ); ?>" autocomplete="off" />
					</label>

					<div class="type-result"></div>

					<?php 
						$place_cities = get_categories(array(
                            'taxonomy'   => 'place-type',
                            'hide_empty' => 1,
                            'orderby'    => 'term_id',
                            'order'      => 'ASC'
                        ));
					?>
					<?php if($place_cities) : ?>
					<div class="type-result focus-result">
						<ul>
							<?php
							foreach ($place_cities as $cate) {
								$cate_id   = $cate->term_id;
				                $cate_name = $cate->name;
				                $cate_slug = $cate->slug;
				                $cate      = get_term_by( 'id', $cate_id, 'place-type');
                            ?>
                                <li>
                                    <a class="entry-city" href="<?php echo esc_url($link); ?>">
				                        <?php echo esc_html($cate_name); ?>
				                    </a>
                                </li>
                            <?php } ?>
						</ul>
					</div>
					<?php endif; ?>
					<input type="hidden" name="s">
					<button type="submit" class="icon-search">
						<i class="la la-search large"></i>
					</button>
				</div>

				<input type="hidden" name="post_type" class="post-type" value="<?php echo esc_attr( $post_type ); ?>"/>

			<?php endif; ?>
		</div>
	</form>
</div>