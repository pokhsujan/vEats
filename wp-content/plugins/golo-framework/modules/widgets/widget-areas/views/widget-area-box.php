<?php
/**
 * The template for displaying widget-area-box.php
 *
 * @package WordPress
 * @subpackage emo
 * @since emo 1.0
 */
$nonce =  wp_create_nonce('bin-delete-widget-area-nonce');
?>
<script type="text/html" id="bin-add-widget-template">
	<div id="bin-add-widget" class="widgets-holder-wrap">
		<input type="hidden" name="bin-widget-areas-nonce" value="<?php echo esc_attr($nonce) ?>" />
		<div class="sidebar-name">
			<h3><?php esc_html_e('Create Widget Area', 'golo-framework'); ?></h3>
		</div>
		<div class="sidebar-description">
			<form id="addWidgetAreaForm" action="#" method="post">
				<div class="widget-content">
					<input id="bin-add-widget-input" name="bin-add-widget-input" type="text" class="regular-text" title="<?php echo esc_attr(esc_html__('Name','golo-framework')); ?>" placeholder="<?php echo esc_attr(esc_html__('Name','golo-framework')); ?>" />
				</div>
				<div class="widget-control-actions">
					<input class="addWidgetArea-button button-primary" type="submit" value="<?php echo esc_attr(esc_html__('Create Widget Area', 'golo-framework')); ?>" />
				</div>
			</form>
		</div>
	</div>
</script>
