<?php 

if ( !defined( 'ABSPATH' ) ){
	exit;
}

if ( !class_exists('Golo_Ajax_Include') ){

	/**
     *  Class Golo_Ajax
     */
	class Golo_Ajax_Include
	{

		/**
		 * The constructor.
		 */
		public function __construct() {
			add_action('wp_ajax_get_login_user', array( $this, 'get_login_user' ) );
			add_action('wp_ajax_nopriv_get_login_user', array( $this, 'get_login_user' ) );

			add_action('wp_ajax_get_register_user', array( $this, 'get_register_user' ) );
			add_action('wp_ajax_nopriv_get_register_user', array( $this, 'get_register_user' ) );

			add_action( 'wp_ajax_fb_ajax_login_or_register', array( $this, 'fb_ajax_login_or_register' ) );
			add_action( 'wp_ajax_nopriv_fb_ajax_login_or_register', array( $this, 'fb_ajax_login_or_register' ) );

			add_action( 'wp_ajax_google_ajax_login_or_register', array( $this, 'google_ajax_login_or_register' ) );
			add_action( 'wp_ajax_nopriv_google_ajax_login_or_register', array( $this, 'google_ajax_login_or_register' ) );

			// Reset password
            add_action( 'wp_ajax_golo_reset_password_ajax', array( $this, 'reset_password_ajax') );
            add_action( 'wp_ajax_nopriv_golo_reset_password_ajax', array( $this, 'reset_password_ajax') );
		}

		//////////////////////////////////////////////////////////////////
		// Ajax Login
		//////////////////////////////////////////////////////////////////
		function get_login_user() {
			$email    = $_POST['email'];
			$password = $_POST['password'];

			$user_login = $email;

			if( is_email($email) ) {
				$current_user = get_user_by( 'email', $email );
				$user_login   = $current_user->user_login;
			}
			
			$array = array();
			$array['user_login']    = $user_login;
			$array['user_password'] = $password;
			$array['remember']      = true;
			$user = wp_signon( $array, false );
			if ( !is_wp_error($user) ) {
				$msg = esc_html__('Login success', 'golo');
				echo json_encode( array( 'success' => true, 'messages' => $msg, 'class' => 'text-success' ) );
			}else{
				$msg = esc_html__('Username or password is wrong. Please try again', 'golo');
				echo json_encode( array( 'success' => false, 'messages' => $msg, 'class' => 'text-error' ) );
			}
			wp_die();
		}

		//////////////////////////////////////////////////////////////////
		// Ajax Register
		//////////////////////////////////////////////////////////////////
		function get_register_user() {
			$firstname  = $_POST['firstname'];
			$lastname   = $_POST['lastname'];
			$email      = $_POST['email'];
			$password   = $_POST['password'];
			$user_login = $firstname.$lastname;
			$userdata = array(
				'user_login' => $user_login,
				'first_name' => $firstname,
				'last_name'  => $lastname,
				'user_email' => $email,
				'user_pass'  => $password
			);
			$user_id = wp_insert_user( $userdata );
			$msg     = '';
			if( !is_wp_error($user_id) ) {
				$creds = array();
				$creds['user_login']    = $user_login;
				$creds['user_email']    = $email;
				$creds['user_password'] = $password;
				$creds['remember']      = true;
				$user = wp_signon( $creds, false );
				$msg  = esc_html__('Register success', 'golo');
				
				$args = array(
                    'user_login_register' => $email,
                    'user_pass_register' => $password
                );

				Golo_Helper::golo_send_email($email, 'mail_register_user', $args);

				echo json_encode( array( 'success' => true, 'messages' => $msg, 'class' => 'text-success' ) );
				
			}else{
				$msg = esc_html__('Username/Email address is existing', 'golo');
				echo json_encode( array( 'success' => false, 'messages' => $msg, 'class' => 'text-error' ) );
			}
			wp_die();
		}

		//////////////////////////////////////////////////////////////////
		// Ajax fb login or register
		//////////////////////////////////////////////////////////////////
		function fb_ajax_login_or_register(){
		  	$id    = $_POST['id'];
		  	$email = $_POST['email'];
		  	$name  = $_POST['name'];
		  	$userdata = array(
				'user_login'   => $id,
				'user_pass'    => $id,
				'user_email'   => $email,
				'display_name' => $name,
			);
			$user_id = wp_insert_user( $userdata );
			if( is_wp_error($user_id) ){
				$creds = array();
				$creds['user_login']    = $id;
				$creds['user_password'] = $id;
				$creds['remember']      = true;
				$user = wp_signon( $creds, false );

				$msg = '';
				if ( !is_wp_error($user) ) {
					$msg = esc_html__('Login success', 'golo');
					echo json_encode( array( 'success' => true, 'messages' => $msg, 'class' => 'text-success' ) );
				}else{
					$msg = esc_html__('This email has been used to register', 'golo');
					echo json_encode( array( 'success' => false, 'messages' => $msg, 'class' => 'text-error' ) );
				}
				wp_die();
			}else{
				wp_set_current_user($user_id);
				wp_set_auth_cookie($user_id, true );
			}
		  	echo json_encode(array('success' => true, 'class' => 'text-success', 'message' => esc_html__('Login success', 'golo')));
		  	wp_die();
		}

		//////////////////////////////////////////////////////////////////
		// Ajax reset password
		//////////////////////////////////////////////////////////////////
        public function reset_password_ajax() {
            check_ajax_referer('golo_reset_password_ajax_nonce', 'golo_security_reset_password');
            $allowed_html = array();
            $user_login = wp_kses( $_POST['user_login'], $allowed_html );

            if ( empty( $user_login ) ) {
                echo json_encode(array( 'success' => false, 'class' => 'text-warning', 'message' => esc_html__('Enter a username or email address.', 'golo') ) );
                wp_die();
            }

            if ( strpos( $user_login, '@' ) ) {
                $user_data = get_user_by( 'email', trim( $user_login ) );
                if ( empty( $user_data ) ) {
                    echo json_encode(array('success' => false, 'class' => 'text-error', 'message' => esc_html__('There is no user registered with that email address.', 'golo')));
                    wp_die();
                }
            } else {
                $login = trim( $user_login );
                $user_data = get_user_by('login', $login);

                if ( !$user_data ) {
                    echo json_encode(array( 'success' => false, 'class' => 'text-error', 'message' => esc_html__('Invalid username', 'golo') ) );
                    wp_die();
                }
            }
            $user_login = $user_data->user_login;
            $user_email = $user_data->user_email;
            $key = get_password_reset_key( $user_data );

            if ( is_wp_error( $key ) ) {
                echo json_encode(array( 'success' => false, 'message' => $key ) );
                wp_die();
            }

            $message = esc_html__('Someone has requested a password reset for the following account:', 'golo' ) . "\r\n\r\n";
            $message .= network_home_url( '/' ) . "\r\n\r\n";
            $message .= sprintf(esc_html__('Username: %s', 'golo'), $user_login) . "\r\n\r\n";
            $message .= esc_html__('If this was a mistake, just ignore this email and nothing will happen.', 'golo') . "\r\n\r\n";
            $message .= esc_html__('To reset your password, visit the following address:', 'golo') . "\r\n\r\n";
            $message .= '<' . network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login') . ">\r\n";

            if ( is_multisite() )
                $blogname = $GLOBALS['current_site']->site_name;
            else
                $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

            $title = sprintf( esc_html__('[%s] Password Reset', 'golo'), $blogname );
            $title = apply_filters( 'retrieve_password_title', $title, $user_login, $user_data );
            $message = apply_filters( 'retrieve_password_message', $message, $key, $user_login, $user_data );
            if ( $message && !wp_mail( $user_email, wp_specialchars_decode( $title ), $message ) ) {
                echo json_encode(array('success' => false, 'class' => 'text-error', 'message' => esc_html__('The email could not be sent.', 'golo') . "<br />\n" . esc_html__('Possible reason: your host may have disabled the mail() function.', 'golo')));
                wp_die();
            } else {
                echo json_encode(array('success' => true, 'class' => 'text-success', 'message' => esc_html__('Please, Check your email to get new password', 'golo') ));
                wp_die();
            }
        }

		//////////////////////////////////////////////////////////////////
		// Ajax fb login or register
		//////////////////////////////////////////////////////////////////
		function google_ajax_login_or_register(){
			$id     = $_POST['id'];
			$email  = $_POST['email'];
			$name   = $_POST['name'];
			$avatar = $_POST['avatar'];
		  	$userdata = array(
				'user_login'   => $id,
				'user_pass'    => $id,
				'user_email'   => $email,
				'display_name' => $name,
			);
			$user_id = wp_insert_user( $userdata );
			if( is_wp_error($user_id) ){
				$creds = array();
				$creds['user_login']    = $id;
				$creds['user_password'] = $id;
				$creds['remember']      = true;
				$user = wp_signon( $creds, false );

				$msg = '';
				if ( !is_wp_error($user) ) {
					$msg = esc_html__('Login success', 'golo');
					echo json_encode( array( 'success' => true, 'messages' => $msg, 'class' => 'text-success' ) );
				}else{
					$msg = esc_html__('This email has been used to register', 'golo');
					echo json_encode( array( 'success' => false, 'messages' => $msg, 'class' => 'text-error' ) );
				}
				wp_die();
			}else{
	            wp_set_current_user($user_id);
				wp_set_auth_cookie($user_id, true );
			}
		  	echo json_encode(array('success' => true, 'class' => 'text-success', 'message' => esc_html__('Login success', 'golo')));
		  	wp_die();
		}

	}

}