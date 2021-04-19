<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 */

defined( 'ABSPATH' ) || exit;

$archive_place_items_amount = golo_get_option('archive_place_items_amount', '16');
$content_place              = golo_get_option('layout_content_place', 'layout-01');
$archive_place_image_size   = golo_get_option('archive_place_image_size', '540x480' );
$enable_archive_filter      = golo_get_option('enable_archive_filter', '1');
$enable_archive_map         = golo_get_option('enable_archive_map', '1');
$default_map                = golo_get_option('default_map', '1');
$archive_place_columns      = golo_get_option('archive_place_columns', '3');
$archive_place_columns_lg   = golo_get_option('archive_place_columns_lg', '3');
$archive_place_columns_md   = golo_get_option('archive_place_columns_md', '2');
$archive_place_columns_sm   = golo_get_option('archive_place_columns_sm', '2');
$archive_place_columns_xs   = golo_get_option('archive_place_columns_xs', '1');
$listing_view_place         = golo_get_option('listing_view_place', 'layout-02');
$class_view = '';
$grid_view = $content_place;
if( $listing_view_place == 'layout-list' ) {
    $content_place = $listing_view_place;
    $class_view = 'list-view';
}

$current_city = isset( $_GET['city'] ) ? golo_clean(wp_unslash($_GET['city'])) : '';
$current_term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
$key          = isset( $_GET['s'] ) ? golo_clean(wp_unslash($_GET['s'])) : '';

$class_inner     = array();
$archive_class   = array();
$archive_class[] = 'area-places';
$archive_class[] = 'grid';
$archive_class[] = $class_view;
$archive_class[] = 'columns-'. $archive_place_columns;
$archive_class[] = 'columns-lg-'. $archive_place_columns_lg;
$archive_class[] = 'columns-md-'. $archive_place_columns_md;
$archive_class[] = 'columns-sm-'. $archive_place_columns_sm;
$archive_class[] = 'columns-xs-'. $archive_place_columns_xs;


$location_check_id = get_term_by('name', $key, 'place-city');

$tax_query = array();
$args = array(
    'posts_per_page'      => -1,
    'post_type'           => 'place',
    'ignore_sticky_posts' => 1,
    'post_status'         => 'publish',
    'tax_query'           => $tax_query,
    's'                   => $key,
    'meta_key'            => 'golo-place_featured',
    'orderby'             => 'meta_value',
);


if ($location_check_id) {
    $args['tax_query'][] = array(
        'taxonomy' => 'place-city',
        'field'    => 'term_id',
        'terms'    => $location_check_id->term_id
    );
    $args['s'] = '';
}

$category = isset($_GET['category']) ? golo_clean(wp_unslash($_GET['category'])) : '';
if ( !empty($category) ) {
    $tax_query[] = array(
        'taxonomy' => 'place-categories',
        'field'    => 'slug',
        'terms'    => $category
    );
}

$location = isset($_GET['location']) ? golo_clean(wp_unslash($_GET['location'])) : '';
if( !empty($location) ) {
    $location_id = get_term_by('name', $location, 'place-city');

    $tax_query[] = array(
        'taxonomy' => 'place-city',
        'field'    => 'term_id',
        'terms'    => $location_id->term_id
    );
}

$place_type = isset($_GET['place_type']) ? golo_clean(wp_unslash($_GET['place_type'])) : '';
if( !empty($place_type) ) {
    $type_check_id = get_term_by('name', $place_type, 'place-type');
    $tax_query[] = array(
        'taxonomy' => 'place-type',
        'field'    => 'term_id',
        'terms'    => $type_check_id->term_id
    );
}

if ( is_tax() ) {
    $current_term   = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
    $taxonomy_title = $current_term->name;
    $taxonomy_name  = get_query_var('taxonomy');
    if (!empty($taxonomy_name)) {
        $tax_query[] = array(
            'taxonomy' => $taxonomy_name,
            'field'    => 'slug',
            'terms'    => $current_term->slug
        );
    }
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



if( $default_map ){
    $class_inner[] = 'no-map';
} else {
   if( !empty($enable_archive_map) ) {
        $class_inner[] = 'has-map';
    }else{
        $class_inner[] = 'no-map';
    } 
}

?>

<div class="nav-bar <?php echo join(' ', $class_inner); ?>">
    <div class="container">

        <div class="inner-nav-bar">

            <div class="left">
                <div class="hidden-md-down">
                    <?php do_action( 'golo_archive_heading_filter', $current_city, $current_term, $total_post); ?>
                </div>

                <?php if( !empty($enable_archive_filter) ) { ?>
                <div class="btn-canvas-filter hidden-md-up">
                    <a href="#"><?php esc_html_e('Filter', 'golo-framework'); ?></a>
                    <i class="las la-filter"></i>
                </div>
                <?php } ?>
                
                <?php
                    $search_fields = golo_get_option( 'search_fields' );
                    if( in_array('sort_by', $search_fields ) ) {
                ?>

                <select name="sort_by" class="sort-by filter-control nice-select hidden-md-up">
                    <option value=""><?php esc_html_e('Sort by', 'golo-framework'); ?></option>
                    <option value="newest"><?php esc_html_e('Newest', 'golo-framework'); ?></option>
                    <option value="rating"><?php esc_html_e('Average rating', 'golo-framework'); ?></option>
                    <option value="featured"><?php esc_html_e('Featured', 'golo-framework'); ?></option>
                </select>
                
                <?php } ?>
            </div>
            
            <?php if( !empty($enable_archive_map) ) { ?>
            <div class="right">
                <div class="btn-maps-filter golo-button hidden-md-up">
                    <a href="#">
                        <i class="las la-map-marked-alt"></i>
                        <?php esc_html_e('Maps view', 'golo-framework'); ?>
                    </a>
                </div>

                <div class="btn-control btn-switch btn-hide-map hidden-md-down">
                    <span><?php esc_html_e('Show Map', 'golo-framework'); ?></span>
                    <label class="switch">
                        <input type="checkbox" value="hide_map" <?php if( !$default_map ) { echo "checked"; } ?>>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
            <?php } ?>

        </div>
    </div>
</div>

<div class="inner-content <?php echo join(' ', $class_inner); ?>">

    <div class="col-left">

        <?php do_action( 'golo_archive_heading_filter', $current_city, $current_term, $total_post); ?>

        <?php
            /**
             * @Hook: golo_output_content_wrapper_start
             * 
             * @hooked output_content_wrapper_start
             */
            do_action( 'golo_output_content_wrapper_start' ); 
        ?> 

            <div class="top-area">

                <div class="entry-left">
                    <span class="result-count">
                        <?php printf( _n( '%s Result', '%s Results', $total_post, 'golo-framework' ), '<span class="count">' . esc_html( $total_post ) . '</span>' ); ?>
                    </span>
                </div>

                <div class="entry-center place-layout">
                    <a class="<?php if( $listing_view_place != 'layout-list' ) : echo 'active';endif; ?>" href="#" data-layout="<?php echo esc_attr($grid_view); ?>"><i class="las la-border-all icon-large"></i></a>
                    <a class="<?php if( $listing_view_place == 'layout-list' ) : echo 'active';endif; ?>" href="#" data-layout="layout-list"><i class="las la-list icon-large"></i></a>
                </div>

                <?php
                    $search_fields = golo_get_option( 'search_fields' );
                    if( in_array('sort_by', $search_fields ) ) {
                ?>

                <div class="entry-right hidden-md-down">
                    <select name="sort_by" class="sort-by filter-control nice-select right">
                        <option value=""><?php esc_html_e('Sort by', 'golo-framework'); ?></option>
                        <option value="newest"><?php esc_html_e('Newest', 'golo-framework'); ?></option>
                        <option value="rating"><?php esc_html_e('Average rating', 'golo-framework'); ?></option>
                        <option value="featured"><?php esc_html_e('Featured', 'golo-framework'); ?></option>
                    </select>
                </div>

                <?php } ?>
                
            </div>

            <div class="<?php echo join(' ', $archive_class); ?>">

                <?php if ( $data->have_posts() ) { ?>

                    <?php while ( $data->have_posts() ) : $data->the_post(); ?>

                        <?php golo_get_template('content-place.php', array(
                            'place_layout' => $content_place,
                            'custom_place_image_size' => $archive_place_image_size
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
    </div>
    
    <?php if( !empty($enable_archive_map) ) { ?>
    <div class="col-right">
        <?php
            /**
             * @Hook: golo_archive_map_filter
             * 
             * @hooked archive_map_filter
             */
            do_action( 'golo_archive_map_filter');
        ?>
    </div>
    <?php } ?>

</div>