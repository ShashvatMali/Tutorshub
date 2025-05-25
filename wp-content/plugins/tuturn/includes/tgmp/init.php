<?php
/**
 * Plugin installation and activation for WordPress themes.
 *
 * Please note that this is a drop-in library for a theme or plugin.
 * The authors of this library (Thomas, Gary and Juliette) are NOT responsible
 * for the support of your plugin or theme. Please contact the plugin
 * or theme author for support.
 *
 * @package   TGM-Plugin-Activation
 * @version   2.6.1
 * @link      http://tgmpluginactivation.com/
 * @author    Thomas Griffin, Gary Jones, Juliette Reinders Folmer
 * @copyright Copyright (c) 2011, Thomas Griffin
 * @license   GPL-2.0+
 */
/**
 * Include the TGM_Plugin_Activation class.
 */
require_once plugin_dir_path( __FILE__ ) . 'class-tgm-plugin-activation.php';
/**
 * @init            TGM plugins config
 * Register the required plugins for this plugin.
 * @package         Tuturn
 * @subpackage      Tuturn/Public/Partials
 * @since           1.0
 */
if (!function_exists('tuturn_register_required_plugins')) {    
	$paid_theme = apply_filters('tuturn_paid_theme', false);
    if($paid_theme == 'free'){
        add_action( 'tgmpa_register', 'tuturn_register_required_plugins' );
    }
	/**
	 * Register the required plugins for this theme.
	 *
	 * This function is hooked into `tgmpa_register`, which is fired on the WP `init` action on priority 10.
	 */
	function tuturn_register_required_plugins() {
		/*
		* Array of plugin arrays. Required keys are name and slug.
		* If the source is NOT from the .org repo, then source is also required.
		*/
		$protocol = is_ssl() ? 'https' : 'http';
		$unyson_core    = $protocol.'://amentotech.com/plugins/unyson.zip';

		$plugins = array(
			array(
				'name'      => esc_html__('WooCommerce','tuturn'),
				'slug'      => 'woocommerce',
				'required'  => true,
			),
			array(
				'name'      => esc_html__('Elementor','tuturn'),
				'slug'      => 'elementor',
				'required'  => true,
			),
			array(
				'name'      => esc_html__('Unyson ( One Click Demo Import )', 'tuturn'),
				'slug'      => 'unyson',
				'source' 	=> $unyson_core,
				'required'  => false,
			),
			array(
				'name'      => esc_html__('Redux','tuturn'),
				'slug'      => 'redux-framework',
				'required'  => true,
			),
			array(
				'name'      => esc_html__('Contact Form 7','tuturn'),
				'slug'      => 'contact-form-7',
				'required'  => false,
			),
			array(
				'name'      => esc_html__('Easy Forms for Mailchimp','tuturn'),
				'slug'      => 'yikes-inc-easy-mailchimp-extender',
				'required'  => false,
			),
		);

		/*
		* Array of configuration settings. Amend each line as needed.
		*
		* TGMPA will start providing localized text strings soon. If you already have translations of our standard
		* strings available, please help us make TGMPA even better by giving us access to these translations or by
		* sending in a pull-request with .po file(s) with the translations.
		*
		* Only uncomment the strings in the config array if you want to customize the strings.
		*/
		$config = array(
			'id'           => 'tuturn',				// Unique ID for hashing notices for multiple instances of TGMPA.
			'default_path' => '',						// Default absolute path to bundled plugins.
			'menu'         => 'tgmpa-install-plugins',	// Menu slug.
			'parent_slug'  => 'edit.php?post_type=tuturn-instructor',			// Parent menu slug.
			'capability'   => 'manage_options',			// Capability needed to view plugin install page, should be a capability associated with the parent menu used.
			'has_notices'  => true,						// Show admin notices or not.
			'dismissable'  => true,						// If false, a user cannot dismiss the nag message.
			'dismiss_msg'  => '',						// If 'dismissable' is false, this message will be output at top of nag.
			'is_automatic' => false,					// Automatically activate plugins after installation or not.
			'message'      => '',						// Message to output right before the plugins table.		
			'strings'      => array(
				'page_title'                      => esc_html__( 'Install Required Plugins', 'tuturn' ),
				'menu_title'                      => esc_html__( 'Install Plugins', 'tuturn' ),
				/* translators: %s: plugin name. */
				'installing'                      => esc_html__( 'Installing Plugin: %s', 'tuturn' ),
				/* translators: %s: plugin name. */
				'updating'                        => esc_html__( 'Updating Plugin: %s', 'tuturn' ),
				'oops'                            => esc_html__( 'Something went wrong with the plugin API.', 'tuturn' ),
				'notice_can_install_required'     => _n_noop(
					/* translators: 1: plugin name(s). */
					'This theme requires the following plugin: %1$s.',
					'This theme requires the following plugins: %1$s.',
					'tuturn'
				),
				'notice_can_install_recommended'  => _n_noop(
					/* translators: 1: plugin name(s). */
					'This theme recommends the following plugin: %1$s.',
					'This theme recommends the following plugins: %1$s.',
					'tuturn'
				),
				'notice_ask_to_update'            => _n_noop(
					/* translators: 1: plugin name(s). */
					'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.',
					'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.',
					'tuturn'
				),
				'notice_ask_to_update_maybe'      => _n_noop(
					/* translators: 1: plugin name(s). */
					'There is an update available for: %1$s.',
					'There are updates available for the following plugins: %1$s.',
					'tuturn'
				),
				'notice_can_activate_required'    => _n_noop(
					/* translators: 1: plugin name(s). */
					'The following required plugin is currently inactive: %1$s.',
					'The following required plugins are currently inactive: %1$s.',
					'tuturn'
				),
				'notice_can_activate_recommended' => _n_noop(
					/* translators: 1: plugin name(s). */
					'The following recommended plugin is currently inactive: %1$s.',
					'The following recommended plugins are currently inactive: %1$s.',
					'tuturn'
				),
				'install_link'                    => _n_noop(
					'Begin installing plugin',
					'Begin installing plugins',
					'tuturn'
				),
				'update_link' 					  => _n_noop(
					'Begin updating plugin',
					'Begin updating plugins',
					'tuturn'
				),
				'activate_link'                   => _n_noop(
					'Begin activating plugin',
					'Begin activating plugins',
					'tuturn'
				),
				'return'                          => esc_html__( 'Return to Required Plugins Installer', 'tuturn' ),
				'plugin_activated'                => esc_html__( 'Plugin activated successfully.', 'tuturn' ),
				'activated_successfully'          => esc_html__( 'The following plugin was activated successfully:', 'tuturn' ),
				/* translators: 1: plugin name. */
				'plugin_already_active'           => esc_html__( 'No action taken. Plugin %1$s was already active.', 'tuturn' ),
				/* translators: 1: plugin name. */
				'plugin_needs_higher_version'     => esc_html__( 'Plugin not activated. A higher version of %s is needed for this theme. Please update the plugin.', 'tuturn' ),
				/* translators: 1: dashboard link. */
				'complete'                        => esc_html__( 'All plugins installed and activated successfully. %1$s', 'tuturn' ),
				'dismiss'                         => esc_html__( 'Dismiss this notice', 'tuturn' ),
				'notice_cannot_install_activate'  => esc_html__( 'There are one or more required or recommended plugins to install, update or activate.', 'tuturn' ),
				'contact_admin'                   => esc_html__( 'Please contact the administrator of this site for help.', 'tuturn' ),
				'nag_type'                        => '', // Determines admin notice type - can only be one of the typical WP notice classes, such as 'updated', 'update-nag', 'notice-warning', 'notice-info' or 'error'. Some of which may not work as expected in older WP versions.
			),			
		);
		tgmpa( $plugins, $config );
	}
}
