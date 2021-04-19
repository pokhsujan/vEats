<?php
/**
 * The Template for displaying taxonomy place categories
 */

defined( 'ABSPATH' ) || exit;

get_header( 'golo' );

$archive_place_items_amount = golo_get_option('archive_place_items_amount', '12');
$custom_place_image_size  	= golo_get_option('custom_place_image_size', '540x480' );

$archive_class 	  = array();
$archive_class[]  = 'archive-place';
$archive_class[]  = 'area-places';
$archive_class[]  = 'grid';
$archive_class[]  = 'columns-4 columns-md-3 columns-sm-2 columns-xs-1';

$current_city   = isset( $_GET['city'] ) ? golo_clean(wp_unslash($_GET['city'])) : '';
$current_term   = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
$term_id        = $current_term->term_id;
$taxonomy_title = $current_term->name;
$taxonomy_name  = get_query_var('taxonomy');

$categories = array();
$tax_query  = array();

$args = array(
	'posts_per_page' 	  => $archive_place_items_amount,
	'post_type'      	  => 'place',
	'ignore_sticky_posts' => 1,
    'post_status'         => 'publish',
    'orderby' 			  => array(
        'menu_order' => 'ASC',
        'date'       => 'ASC',
    ),
);

//tax query place city
if (!empty($current_city)) {
    $tax_query[] = array(
        'taxonomy' => 'place-city',
        'field'    => 'slug',
        'terms'    => $current_city
    );
}

//tax query place categories
if (!empty($current_term)) {
    $tax_query[] = array(
        'taxonomy' => $taxonomy_name,
        'field'    => 'slug',
        'terms'    => $current_term->slug
    );
}

$tax_count = count($tax_query);
if ($tax_count > 0) {
    $args['tax_query'] = array(
        'relation' => 'AND',
        $tax_query
    );
}

$data       = new WP_Query($args);
$total_post = $data->found_posts;
?>

<?php
/**
* @Hook: golo_tax_categories_before
*
* @hooked archive_page_title - 5
* @hooked archive_information - 10
* @hooked archive_categories - 20
*/
do_action( 'golo_tax_categories_before' ); 
?>

<?php
	/**
	 * @Hook: golo_layout_wrapper_start
	 * 
	 * @hooked layout_wrapper_start
	 */
	do_action( 'golo_layout_wrapper_start' );
?>

	<?php
		/**
		 * @Hook: golo_output_content_wrapper_start
		 * 
		 * @hooked output_content_wrapper_start
		 */
		do_action( 'golo_output_content_wrapper_start' ); 
	?>

	    <div class="<?php echo join(' ', $archive_class); ?>">

		    <?php if ( $data->have_posts() ) { ?>

		        <?php while ( $data->have_posts() ) : $data->the_post(); ?>

		            <?php golo_get_template('content-place.php', array(
		                'custom_place_image_size' => $custom_place_image_size
		            )); ?>

		        <?php endwhile; ?>

		    <?php } else { ?>

	        	<div class="item-not-found"><?php esc_html_e('No item found', 'golo-framework'); ?></div>

		    <?php } ?>

		</div>

		<?php
	        $max_num_pages = $data->max_num_pages;
	        golo_get_template('global/pagination.php', array('max_num_pages' => $max_num_pages, 'type' => 'ajax-call'));
	        wp_reset_postdata();
	    ?>
    
	<?php
		/**
		 * @Hook: golo_output_content_wrapper_end
		 * 
		 * @hooked output_content_wrapper_end
		 */
		do_action( 'golo_output_content_wrapper_end' );
	?>

<?php
	/**
	 * @Hook: golo_layout_wrapper_end
	 * 
	 * @hooked layout_wrapper_end
	 */
	do_action( 'golo_layout_wrapper_end' );
?>

<?php

/**
* @Hook: golo_tax_categories_after
*
* @hooked archive_related_city
*/
do_action( 'golo_tax_categories_after' ); 

get_footer( 'golo' );
