<?php 
if ( !defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}

$country = $city_id = $city_name = $city_slug = $country_name = '';

$place_id    = get_the_ID();
$place_title = get_the_title();

$place_city 	  = get_the_terms( $place_id, 'place-city');
$place_amenities  = get_the_terms( $place_id, 'place-amenities');
$place_categories = get_the_terms( $place_id, 'place-categories');

if( $place_city ) {
	$city_id      = $place_city[0]->term_id;
	$city_name    = $place_city[0]->name;
	$city_slug    = $place_city[0]->slug;
	$country      = get_term_meta( $city_id, 'place_city_country', true );
	$country_name = golo_get_country_by_code($country);
}

?>
<!-- Title/ Price -->
<div class="place-heading place-area">
	<?php if( $country || $place_city || $place_categories ) : ?>
	<div class="entry-categories">
		<?php if( $place_city ) : ?>
		<div class="place-city">
			<a href="<?php echo get_term_link( $city_slug, 'place-city'); ?>"><?php echo esc_html($city_name); ?></a>
		</div>
		<?php endif; ?>

		<?php if( $place_categories ) : ?>
        <div class="place-cate">
			<?php 
            foreach ($place_categories as $cate) {
                $cate_link = get_term_link($cate, 'place-categories');
                ?>
                    <a href="<?php echo esc_url($cate_link); ?>?city=<?php echo esc_attr($city_slug); ?>"><?php echo esc_html($cate->name); ?></a>
                <?php
            }
            ?>
		</div>
        <?php endif; ?>
	</div>
	<?php endif; ?>

	<?php if( !empty($place_title) ) : ?>
	<div class="place-title">
		<h1 class="entry-title"><?php the_title(); ?></h1>
	</div>
	<?php endif; ?>
</div>