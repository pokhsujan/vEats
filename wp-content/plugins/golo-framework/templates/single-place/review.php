<?php
global $wpdb;

$rating = $total_reviews = $total_stars = 0;

$no_avatar_src = '';

$current_user = wp_get_current_user();
$user_id      = $current_user->ID;
$place_id     = get_the_ID();
$place_rating = get_post_meta($place_id, GOLO_METABOX_PREFIX . 'place_rating', true);

$comments_query = "SELECT * FROM $wpdb->comments as comment INNER JOIN $wpdb->commentmeta AS meta WHERE comment.comment_post_ID = $place_id AND meta.meta_key = 'place_rating' AND meta.comment_id = comment.comment_ID AND ( comment.comment_approved = 1 OR comment.user_id = $user_id )";
$get_comments   = $wpdb->get_results($comments_query);
$my_review      = $wpdb->get_row("SELECT * FROM $wpdb->comments as comment INNER JOIN $wpdb->commentmeta AS meta WHERE comment.comment_post_ID = $place_id AND comment.user_id = $user_id AND meta.meta_key = 'place_rating' AND meta.comment_id = comment.comment_ID ORDER BY comment.comment_ID DESC");

if (!is_null($get_comments)) {
    foreach ($get_comments as $comment) {
        if ($comment->comment_approved == 1) {
            if( !empty($comment->meta_value) ){
                $total_reviews++;
            }
            if( $comment->meta_value > 0 ){
                $total_stars += $comment->meta_value;
            }
        }
    }

    if ($total_reviews != 0) {
        $rating = number_format($total_stars / $total_reviews, 1);
    }
}
?>
<div class="place-reviews place-area">
    <div class="entry-heading">
        <h3 class="entry-title"><?php esc_html_e('Review', 'golo-framework'); ?></h3>
        <span class="rating-count">
            <span><?php echo esc_html($rating); ?></span>
            <i class="la la-star medium"></i>
        </span>
        <span class="review-count"><?php printf(_n('Base on %s Review', 'Base on %s Reviews', $total_reviews, 'golo-framework'), $total_reviews); ?></span>
    </div>
    <div class="entry-detail">
        <ul class="reviews-list">
            <?php if (!is_null($get_comments)) {
                foreach ($get_comments as $comment) {
                    $comment_id        = $comment->comment_ID;
                    $author_avatar_url = get_avatar_url($comment->user_id, ['size' => '50']);
                    $author_avatar_image_url = get_the_author_meta('author_avatar_image_url', $comment->user_id);
                    if( !empty($author_avatar_image_url) ){
                        $author_avatar_url = $author_avatar_image_url;
                    }
                    $user_link = get_author_posts_url($comment->user_id);
                    ?>
                    <li class="author-review">
                        <div class="entry-head">
                            <div class="entry-avatar">
                                <figure>
                                    <?php
                                    if (!empty($author_avatar_url)) {
                                        ?>
                                        <a href="<?php echo esc_url($user_link); ?>">
                                            <img src="<?php echo esc_url($author_avatar_url); ?>" alt="<?php the_author_meta('display_name', $comment->user_id); ?>">
                                         </a>
                                        <?php
                                    } else {
                                        ?>
                                        <a href="<?php echo esc_url($user_link); ?>">
                                            <img src="<?php echo esc_url($no_avatar_src); ?>" alt="<?php the_author_meta('display_name', $comment->user_id); ?>"></a>
                                        <?php
                                    }
                                    ?>
                                </figure>
                            </div>
                            <div class="entry-info">
                                <div class="entry-name">
                                    <h4 class="author-name"><a href="<?php echo esc_url($user_link); ?>"><?php the_author_meta('display_name', $comment->user_id); ?></a></h4>
                                    <?php if( $comment->meta_value > 0 ) : ?>
                                    <div class="author-rating">
                                        <span class="star <?php if( $comment->meta_value >= 1 ) : echo 'checked';endif; ?>">
                                            <i class="la la-star"></i>
                                        </span>
                                        <span class="star <?php if( $comment->meta_value >= 2 ) : echo 'checked';endif; ?>">
                                            <i class="la la-star"></i>
                                        </span>
                                        <span class="star <?php if( $comment->meta_value >= 3 ) : echo 'checked';endif; ?>">
                                            <i class="la la-star"></i>
                                        </span>
                                        <span class="star <?php if( $comment->meta_value >= 4 ) : echo 'checked';endif; ?>">
                                            <i class="la la-star"></i>
                                        </span>
                                        <span class="star <?php if( $comment->meta_value == 5 ) : echo 'checked';endif; ?>">
                                            <i class="la la-star"></i>
                                        </span>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <span class="review-date"><?php echo golo_get_comment_time($comment->comment_ID); ?></span>
                            </div>
                        </div>

                        <div class="entry-comment">
                            <p class="review-content"><?php echo wp_kses_post($comment->comment_content); ?></p>
                        </div>
                        
                        <?php if( is_user_logged_in() ){ ?>
                        <div class="entry-nav">
                            <div class="reply">
                                <a href="#">                           
                                    <i class="la la-comment medium"></i>
                                    <span><?php esc_html_e('Reply', 'golo-framework'); ?></span>
                                </a>
                            </div>

                            <?php if ($comment->comment_approved == 0) { ?>
                                <span class="waiting-for-approval"> <?php esc_html_e('Waiting for approval', 'golo-framework'); ?> </span>
                            <?php } ?>
                        </div>
                        <?php } ?>
                        
                        <?php 
                            $args = array(
                                'status' => 'approve', 
                                'number' => '',
                                'order'  => 'ASC',
                                'parent' => $comment->comment_ID
                            );
                            $child_comments = get_comments($args);
                        ?>
                        <?php if($child_comments) : ?>
                        <ol class="children">
                            <?php foreach($child_comments as $child_comment) { ?>
                                <?php 
                                    $child_avatar_url       = get_avatar_url($child_comment->user_id, ['size' => '50']);
                                    $child_link             = get_author_posts_url($child_comment->user_id);
                                    $child_avatar_image_url = get_the_author_meta('author_avatar_image_url', $child_comment->user_id);
                                    if( isset($child_avatar_image_url) ){
                                        $child_avatar_url = $child_avatar_image_url;
                                    }
                                ?>
                                <li class="author-review">
                                    <div class="entry-head">
                                        <div class="entry-avatar">
                                            <figure>
                                                <?php
                                                if (!empty($child_avatar_url)) {
                                                    ?>
                                                    <a href="<?php echo esc_url($child_link); ?>">
                                                        <img src="<?php echo esc_url($child_avatar_url); ?>" alt="<?php the_author_meta('display_name', $comment->user_id); ?>">
                                                     </a>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <a href="<?php echo esc_url($child_link); ?>">
                                                        <img src="<?php echo esc_url($no_avatar_src); ?>" alt="<?php the_author_meta('display_name', $comment->user_id); ?>"></a>
                                                    <?php
                                                }
                                                ?>
                                            </figure>
                                        </div>
                                        <div class="entry-info">
                                            <div class="entry-name">
                                                <h4 class="author-name"><a href="<?php echo esc_url($child_link); ?>"><?php the_author_meta('display_name', $child_comment->user_id); ?></a></h4>
                                            </div>
                                            <span class="review-date"><?php echo golo_get_comment_time($child_comment->comment_ID); ?></span>
                                        </div>
                                    </div>

                                    <div class="entry-comment">
                                        <p class="review-content"><?php echo esc_html($child_comment->comment_content); ?></p>
                                    </div>
                                    
                                    <?php if ($child_comment->comment_approved == 0) { ?>
                                    <div class="entry-nav">
                                        <span class="waiting-for-approval"> <?php esc_html_e('Waiting for approval', 'golo-framework'); ?> </span>
                                    </div>
                                    <?php } ?>
                                </li>
                            <?php } ?>
                        </ol>
                        <?php endif; ?>
                        
                        <div class="form-reply" data-id="<?php echo esc_attr($comment->comment_ID); ?>"></div>
                    </li>
                    <?php
                }
            }
            ?>
        </ul>
        <div class="add-new-review">
            <?php
            if( !is_user_logged_in() ){
                ?>
                <div class="login-for-review account logged-out">
                    <a href="#popup-form" class="btn-login"><?php esc_html_e('Login', 'golo-framework'); ?></a>
                    <span><?php esc_html_e('to review', 'golo-framework'); ?></span>
                </div>
                <?php
            }else{
                ?>
                <h4 class="review-title"><?php esc_html_e('Write a Review', 'golo-framework'); ?></h4>
                <?php
                $current_user = wp_get_current_user();
                $user_name    = $current_user->display_name;
                $avatar_url   = get_avatar_url($current_user->ID);
                $author_avatar_image_url = get_the_author_meta('author_avatar_image_url', $current_user->ID);
                if( !empty($author_avatar_image_url) ){
                    $avatar_url = $author_avatar_image_url;
                }
                if (is_null($my_review)) {
                    ?>
                    <form method="post" action="#">
                        <div class="form-group star-rating">
                            <span><?php esc_html_e('Rate This Place:', 'golo-framework'); ?></span>
                            <fieldset class="rate">
                                <input type="radio" id="rating5" name="rating" value="5"/><label for="rating5" title="5 stars"></label>
                                <input type="radio" id="rating4" name="rating" value="4"/><label for="rating4" title="4 stars"></label>
                                <input type="radio" id="rating3" name="rating" value="3"/><label for="rating3" title="3 stars"></label>
                                <input type="radio" id="rating2" name="rating" value="2"/><label for="rating2" title="2 stars"></label>
                                <input type="radio" id="rating1" name="rating" value="1"/><label for="rating1" title="1 star"></label>
                            </fieldset>
                        </div>
                        <div class="form-group custom-area">
                            <textarea class="form-control" name="message" placeholder="<?php esc_attr_e('Your review...', 'golo-framework'); ?>"></textarea>
                            <?php if( isset($avatar_url) ) : ?>
                            <div class="current-user-avatar">
                                <img src="<?php echo esc_url($avatar_url); ?>" alt="<?php echo esc_attr($user_name); ?>">
                            </div>
                            <?php endif; ?>
                        </div>
                        <button type="submit" class="golo-submit-place-rating btn-golo btn btn-default"><?php esc_html_e('Submit Review', 'golo-framework'); ?></button>
                        <?php wp_nonce_field('golo_submit_review_ajax_nonce', 'golo_security_submit_review'); ?>
                        <input type="hidden" name="action" value="golo_place_submit_review_ajax">
                        <input type="hidden" name="place_id" value="<?php the_ID(); ?>">
                    </form>
                    <?php
                } else {
                    ?>
                    <form method="post" action="#">
                        <div class="form-group star-rating">
                            <span><?php esc_html_e('Rate This Place:', 'golo-framework'); ?></span>
                            <fieldset class="rate">
                                <input type="radio" id="rating5" name="rating" value="5"/><label for="rating5" title="5 stars"></label>
                                <input type="radio" id="rating4" name="rating" value="4"/><label for="rating4" title="4 stars"></label>
                                <input type="radio" id="rating3" name="rating" value="3"/><label for="rating3" title="3 stars"></label>
                                <input type="radio" id="rating2" name="rating" value="2"/><label for="rating2" title="2 stars"></label>
                                <input type="radio" id="rating1" name="rating" value="1"/><label for="rating1" title="1 star"></label>
                            </fieldset>
                        </div>
                        <div class="form-group custom-area">
                            <textarea class="form-control" rows="6" name="message" placeholder="<?php esc_attr_e('Your review...', 'golo-framework'); ?>"><?php echo wp_kses_post($my_review->comment_content); ?></textarea>
                            <?php if( isset($avatar_url) ) : ?>
                            <div class="current-user-avatar">
                                <img src="<?php echo esc_url($avatar_url); ?>" alt="<?php echo esc_attr($user_name); ?>">
                            </div>
                            <?php endif; ?>
                        </div>
                        <button type="submit" class="golo-submit-place-rating btn-golo btn btn-default"><?php esc_html_e('Update Review', 'golo-framework'); ?></button>
                        <?php wp_nonce_field('golo_submit_review_ajax_nonce', 'golo_security_submit_review'); ?>
                        <input type="hidden" name="action" value="golo_place_submit_review_ajax">
                        <input type="hidden" name="place_id" value="<?php the_ID(); ?>">
                    </form>
                    <?php
                }
            }
            ?>
        </div>
    </div>

    <div class="duplicate-form-reply hide none">
        <div class="entry-head">
            <h4 class="review-title"><?php esc_html_e('Reply', 'golo-framework'); ?></h4>
            <a href="#" class="cancel-reply">
                <i class="la la-times"></i>
                <span><?php esc_html_e('Cancel reply', 'golo-framework'); ?></span>   
            </a>
        </div>
        <?php 
        $current_user = wp_get_current_user();
        $user_name    = $current_user->display_name;
        $avatar_url   = get_avatar_url($current_user->ID);
        $author_avatar_image_url = get_the_author_meta('author_avatar_image_url', $current_user->ID);
        if( !empty($author_avatar_image_url) ){
            $avatar_url = $author_avatar_image_url;
        }
        ?>
        <form method="post" action="#">
            <div class="form-group custom-area">
                <textarea class="form-control" rows="5" name="message" placeholder="<?php esc_attr_e('Add a comment...', 'golo-framework'); ?>"></textarea>
                <?php if( isset($avatar_url) ) : ?>
                <div class="current-user-avatar">
                    <img src="<?php echo esc_url($avatar_url); ?>" alt="<?php echo esc_attr($user_name); ?>">
                </div>
                <?php endif; ?>
            </div>
            <button type="submit" class="golo-submit-place-reply btn-golo btn btn-default"><?php esc_html_e('Send', 'golo-framework'); ?></button>
            <?php wp_nonce_field('golo_submit_reply_ajax_nonce', 'golo_security_submit_reply'); ?>
            <input type="hidden" name="action" value="golo_place_submit_reply_ajax">
            <input type="hidden" name="place_id" value="<?php the_ID(); ?>">
            <input type="hidden" name="comment_id" value="">
        </form>
    </div>

    <script>
        jQuery(document).ready(function($) {
            $('.entry-nav .reply').on('click', function(e) {
                e.preventDefault();
                $('.author-review').removeClass('active');
                $('.author-review .form-reply').html('');
                var $this      = $(this);
                var form_reply = $('.duplicate-form-reply').html();
                var comment_id = $this.parents('.author-review').find('.form-reply').data('id');
                $('.add-new-review').hide();
                $this.parents('.author-review').addClass('active');
                $this.parents('.author-review').find('.form-reply').html(form_reply);
                $this.parents('.author-review').find('.form-reply input[name="comment_id"]').val(comment_id);
            });

            $('body').on('click', '.cancel-reply', function(e) {
                e.preventDefault();
                $('.author-review').removeClass('active');
                $('.author-review .form-reply').html('');
                $('.add-new-review').show();
            });

            $('body').on('click', '.form-reply .golo-submit-place-reply', function(e) {
                e.preventDefault();
                var $this = $(this);
                var $form = $this.parents('form');
                $.ajax({
                    type: 'POST',
                    url: '<?php echo esc_url(GOLO_AJAX_URL); ?>',
                    data: $form.serialize(),
                    dataType: 'json',
                    beforeSend: function() {
                        $this.attr('disabled', true);
                        $this.children('i').remove();
                        $this.append('<i class="fa-left la la-circle-notch la-spin large"></i>');
                    },
                    success: function() {
                        window.location.reload();
                    },
                    complete: function() {
                        $this.children('i').removeClass('la la-circle-notch la-spin large');
                        $this.children('i').addClass('fa fa-check');
                    }
                });
            });

            $('body').on('click', '.golo-submit-place-rating', function(e) {
                e.preventDefault();
                var $this = $(this);
                var $form = $this.parents('form');
                $.ajax({
                    type: 'POST',
                    url: '<?php echo esc_url(GOLO_AJAX_URL); ?>',
                    data: $form.serialize(),
                    dataType: 'json',
                    beforeSend: function() {
                        $this.children('i').remove();
                        $this.append('<i class="fa-left la la-circle-notch la-spin large"></i>');
                    },
                    success: function() {
                        window.location.reload();
                    },
                    complete: function() {
                        $this.children('i').removeClass('la la-circle-notch la-spin large');
                        $this.children('i').addClass('fa fa-check');
                    }
                });
            });
        });
    </script>
</div>