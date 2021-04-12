<?php
/**
 * The template for displaying all page user control.
 */

defined( 'ABSPATH' ) || exit;

get_header();

$mainClass = "page-control";
$layoutClass = "site-layout";
if( is_page('new-place') ){
    $mainClass = "content-page";
    $layoutClass = "container form-page";
}
?>

    <div class="main-content <?= $mainClass; ?>">

        <div class="<?= $layoutClass; ?>">

            <?php
            if( !golo_page_shortcode('[golo_submit_place]') && !golo_page_shortcode('[golo_country]') ) :
                golo_get_template('place/nav-dashboard.php');
            endif;
            ?>

            <div id="primary" class="content-area">

                <main id="main" class="site-main">
                    <?php
                    if( is_page('beome-a-partner') ){
                       echo '<div class="become-partner">';
                    }
                    ?>
                    <?php
                    /* Start the Loop */
                    while ( have_posts() ) : the_post();

                        the_content();

                        wp_link_pages( array(
                            'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'golo' ),
                            'after'  => '</div>',
                        ) );

                        // If comments are open or we have at least one comment, load up the comment template.
                        if ( ( comments_open() || get_comments_number() ) && empty($elementor_page) ) {
                            comments_template();
                        }

                    endwhile; // End of the loop.
                    ?>

                    <?php
                    if( is_page('beome-a-partner') ){
                        echo '</div>';
                    }
                    ?>
                </main>

            </div>

        </div>

    </div>

<?php
get_footer();
