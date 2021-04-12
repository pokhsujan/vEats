<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin installation and activation for WordPress themes
 *
 * @package Golo
 */
if ( ! class_exists( 'Golo_Register_Plugins' ) ) {

	class Golo_Register_Plugins {

		public static $plugins;

		/**
		 * Golo_Register_Plugins constructor.
		 */
		public function __construct() {
			add_filter( 'golo_tgm_plugins', array( $this, 'plugin_list' ) );
			add_action( 'tgmpa_register', array( $this, 'register_plugins' ), 11, 1 );
		}

		/**
		 * Register required plugins
		 *
		 * @return array
		 */
		public function plugin_list() {
			$plugins = array(
				array(
					'name'     			 => 'Golo Framework',
					'slug'     			 => 'golo-framework',
					'thumb'     		 => 'https://getgolo.com/files/thumb-golo.png',
					'source'   			 => get_template_directory() . '/plugins/golo-framework.zip',
					'version'  			 => '1.4.3',
					'required' 			 => true,
					'force_activation'   => false,
					'force_deactivation' => false,
					'external_url'       => '',
				),

				array(
					'name'     			 => 'Elementor',
					'slug'     			 => 'elementor',
					'thumb'     		 => 'https://getgolo.com/files/thumb-elementor.png',
					'required' 			 => true,
				),

				array(
					'name'     			 => 'Elementor Pro',
					'slug'     			 => 'elementor-pro',
					'thumb'     		 => 'https://getgolo.com/files/thumb-elementor.png',
					'source'   			 => get_template_directory() .  '/plugins/elementor-pro.zip',
					'version'  			 => '3.0.6',
					'required' 			 => true,
					'force_activation'   => false,
					'force_deactivation' => false,
					'external_url'       => '',
				),

				array(
					'name'     => 'WooCommerce',
					'slug'     => 'woocommerce',
					'thumb'    => 'https://getgolo.com/files/thumb-woo.png',
					'required' => true,
				),

				array(
					'name'     => 'Contact Form 7',
					'slug'     => 'contact-form-7',
					'thumb'    => 'https://getgolo.com/files/thumb-cf7.png',
					'required' => false,
				),

				array(
					'name'     => 'Mailchimp For WP',
					'slug'     => 'mailchimp-for-wp',
					'thumb'    => 'https://getgolo.com/files/thumb-mailchimp.png',
					'required' => false,
				),
			);

			return $plugins;
		}

		function register_plugins() {
			$plugins = array();
			$plugins = apply_filters( 'golo_tgm_plugins', $plugins );
			$config  = array(
				'id'           => 'tgmpa',
				// Unique ID for hashing notices for multiple instances of TGMPA.
				'default_path' => '',
				// Default absolute path to pre-packaged plugins.
				'menu'         => 'tgmpa-install-plugins',
				// Menu slug.
				'parent_slug'  => 'themes.php',
				// Parent menu slug.
				'capability'   => 'edit_theme_options',
				// Capability needed to view plugin install page, should be a capability associated with the parent menu used.
				'has_notices'  => true,
				// Show admin notices or not.
				'dismissable'  => true,
				// If false, a user cannot dismiss the nag message.
				'dismiss_msg'  => '',
				// If 'dismissable' is false, this message will be output at top of nag.
				'is_automatic' => true,
				// Automatically activate plugins after installation or not.
				'message'      => '',
				// Message to output right before the plugins table.
				'strings'      => array(
					'page_title'                      => esc_html__( 'Install Required Plugins', 'golo' ),
					'menu_title'                      => esc_html__( 'Install Plugins', 'golo' ),
					'installing'                      => esc_html__( 'Installing Plugin: %s', 'golo' ),
					// %s = plugin name.
					'oops'                            => esc_html__( 'Something went wrong with the plugin API.',
						'golo' ),
					'notice_can_install_required'     => _n_noop( 'This theme requires the following plugin: %1$s.',
						'This theme requires the following plugins: %1$s.',
						'golo' ),
					// %1$s = plugin name(s).
					'notice_can_install_recommended'  => _n_noop( 'This theme recommends the following plugin: %1$s.',
						'This theme recommends the following plugins: %1$s.',
						'golo' ),
					// %1$s = plugin name(s).
					'notice_cannot_install'           => _n_noop( 'Sorry, but you do not have the correct permissions to install the %1$s plugin.',
						'Sorry, but you do not have the correct permissions to install the %1$s plugins.',
						'golo' ),
					// %1$s = plugin name(s).
					'notice_ask_to_update'            => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.',
						'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.',
						'golo' ),
					// %1$s = plugin name(s).
					'notice_ask_to_update_maybe'      => _n_noop( 'There is an update available for: %1$s.',
						'There are updates available for the following plugins: %1$s.',
						'golo' ),
					// %1$s = plugin name(s).
					'notice_cannot_update'            => _n_noop( 'Sorry, but you do not have the correct permissions to update the %1$s plugin.',
						'Sorry, but you do not have the correct permissions to update the %1$s plugins.',
						'golo' ),
					// %1$s = plugin name(s).
					'notice_can_activate_required'    => _n_noop( 'The following required plugin is currently inactive: %1$s.',
						'The following required plugins are currently inactive: %1$s.',
						'golo' ),
					// %1$s = plugin name(s).
					'notice_can_activate_recommended' => _n_noop( 'The following recommended plugin is currently inactive: %1$s.',
						'The following recommended plugins are currently inactive: %1$s.',
						'golo' ),
					// %1$s = plugin name(s).
					'notice_cannot_activate'          => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %1$s plugin.',
						'Sorry, but you do not have the correct permissions to activate the %1$s plugins.',
						'golo' ),
					// %1$s = plugin name(s).
					'install_link'                    => _n_noop( 'Begin installing plugin',
						'Begin installing plugins',
						'golo' ),
					'update_link'                     => _n_noop( 'Begin updating plugin',
						'Begin updating plugins',
						'golo' ),
					'activate_link'                   => _n_noop( 'Begin activating plugin',
						'Begin activating plugins',
						'golo' ),
					'return'                          => esc_html__( 'Return to Required Plugins Installer', 'golo' ),
					'plugin_activated'                => esc_html__( 'Plugin activated successfully.', 'golo' ),
					'activated_successfully'          => esc_html__( 'The following plugin was activated successfully:',
						'golo' ),
					'plugin_already_active'           => esc_html__( 'No action taken. Plugin %1$s was already active.',
						'golo' ),
					// %1$s = plugin name(s).
					'plugin_needs_higher_version'     => esc_html__( 'Plugin not activated. A higher version of %s is needed for this theme. Please update the plugin.',
						'golo' ),
					// %1$s = plugin name(s).
					'complete'                        => esc_html__( 'All plugins installed and activated successfully. %1$s',
						'golo' ),
					// %s = dashboard link.
					'contact_admin'                   => esc_html__( 'Please contact the administrator of this site for help.',
						'golo' ),
					'nag_type'                        => 'updated',
					// Determines admin notice type - can only be 'updated', 'update-nag' or 'error'.
				),
			);

			tgmpa( $plugins, $config );

		}
	}

	new Golo_Register_Plugins();
}
