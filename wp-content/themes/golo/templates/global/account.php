<?php 
if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>
<div class="popup popup-account" id="popup-form">
	<div class="bg-overlay"></div>
	<div class="inner-popup">
		<a href="#" class="btn-close">
			<i class="la la-times large"></i>
		</a>
		<div class="head-popup">
			<div class="tabs-form">
				<a class="btn-login active" href="#golo-login"><?php esc_html_e('Log in', 'golo'); ?></a>
				<a class="btn-register" href="#golo-register"><?php esc_html_e('Sign Up', 'golo'); ?></a>
				<div class="loading-effect"><span class="golo-dual-ring"></span></div>
			</div>
			
			<?php 
			$enable_social_login = Golo_Helper::golo_get_option('enable_social_login', '1');
			if( class_exists('Golo_Framework') && $enable_social_login ) { 
			?>
				
			<div class="addon-login">
				<?php printf( esc_html__( 'Continue with %1$s or %2$s', 'golo' ), '<a class="facebook-login" href="#">' . esc_html__('Facebook', 'golo') . '</a>', '<a class="google-login" href="#">' . esc_html__('Google', 'golo') . '</a>' ); ?>
			</div>
			
			<p>
				<span><?php esc_html_e('Or', 'golo'); ?></span>
			</p>

			<?php } ?>
		</div>

		<div class="body-popup">
			<form action="#" id="golo-login" class="form-account active" method="post">

				<div class="form-group">
					<label for="ip_email" class="label-field"><?php esc_html_e('Account or Email', 'golo'); ?></label>
					<input type="text" id="ip_email" class="form-control input-field" name="email">
				</div>
				<div class="form-group">
					<label for="ip_password" class="label-field"><?php esc_html_e('Password', 'golo'); ?></label>
					<input type="password" id="ip_password" class="form-control input-field" name="password">
				</div>
				<div class="form-group">
					<div class="forgot-password">
						<span><?php esc_html_e('Forgot your password? ', 'golo'); ?></span>
						<a class="btn-reset-password" href="#"><?php esc_html_e('Reset password.', 'golo'); ?></a>
					</div>
				</div>

				<p class="msg"><?php esc_html_e('Sending login info,please wait...', 'golo'); ?></p>

				<div class="form-group">
					<button type="submit" class="gl-button btn button" value="<?php esc_attr_e( 'Sign in', 'golo' ); ?>"><?php esc_attr_e( 'Sign in', 'golo' ); ?></button>
				</div>
			</form>

			<div class="golo-reset-password-wrap form-account">
			    <div id="golo_messages_reset_password" class="golo_messages message"></div>
			    <form method="post" enctype="multipart/form-data">
			        <div class="form-group control-username">
			            <input name="user_login" id="user_login" class="form-control control-icon" placeholder="<?php esc_html_e('Enter your username or email', 'golo'); ?>">
			            <?php wp_nonce_field('golo_reset_password_ajax_nonce', 'golo_security_reset_password'); ?>
			            <input type="hidden" name="action" id="reset_password_action" value="golo_reset_password_ajax">
			            <p class="msg"><?php esc_html_e('Sending info,please wait...', 'golo'); ?></p>
			            <button type="submit" id="golo_forgetpass" class="btn gl-button"><?php esc_html_e('Get new password', 'golo'); ?></button>
			        </div>
			    </form>
			    <a class="back-to-login" href="#"><i class="las la-arrow-left"></i><?php esc_html_e('Back to login', 'golo'); ?></a>
			</div>

			<form action="#" id="golo-register" class="form-account" method="post">

				<div class="form-group">
					<div class="row">
						<div class="col-6">
							<div class="col-group">
								<label for="ip_reg_firstname" class="label-field"><?php esc_html_e('First Name', 'golo'); ?></label>
								<input type="text" id="ip_reg_firstname" class="form-control input-field" name="reg_firstname">
							</div>
						</div>
						<div class="col-6">
							<div class="col-group">
								<label for="ip_reg_lastname" class="label-field"><?php esc_html_e('Last Name', 'golo'); ?></label>
								<input type="text" id="ip_reg_lastname" class="form-control input-field" name="reg_lastname">
							</div>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="ip_reg_email" class="label-field"><?php esc_html_e('Email', 'golo'); ?></label>
					<input type="email" id="ip_reg_email" class="form-control input-field" name="reg_email">
				</div>
				<div class="form-group">
					<label for="ip_reg_password" class="label-field"><?php esc_html_e('Password', 'golo'); ?></label>
					<input type="password" id="ip_reg_password" class="form-control input-field" name="reg_password">
				</div>
				<div class="form-group accept-account">
					<?php 
					$terms_login 	= Golo_Helper::golo_get_option('terms_login');
					$privacy_policy = Golo_Helper::golo_get_option('privacy_policy_login');
					?>
					<input type="checkbox" id="ip_accept_account" class="form-control custom-checkbox" name="accept_account">
					<label for="ip_accept_account"><?php printf( esc_html__( 'Accept the %1$s and %2$s', 'golo' ), '<a href="' . get_permalink($terms_login) . '">' . esc_html__('Terms', 'golo') . '</a>', '<a href="' . get_permalink($privacy_policy) . '">' . esc_html__('Privacy Policy', 'golo') . '</a>' ); ?></label>
				</div>

				<p class="msg"><?php esc_html_e('Sending register info,please wait...', 'golo'); ?></p>

				<div class="form-group">
					<button type="submit" class="gl-button btn button" value="<?php esc_attr_e( 'Sign in', 'golo' ); ?>"><?php esc_attr_e( 'Sign up', 'golo' ); ?></button>
				</div>
			</form>
		</div>
	</div>
</div>