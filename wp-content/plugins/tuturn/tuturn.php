<?php

/**
 * The plugin init file
 *
 *
 * @link              https://codecanyon.net/user/amentotech/portfolio
 * @since             1.0.0
 * @package           Tuturn
 *
 * @Tuturn
 * Plugin Name:       Tuturn - Online Tuition & Tutor Marketplace
 * Plugin URI:        https://codecanyon.net/user/amentotech/portfolio
 * Description:       Tuturn is tutors Marketplace, It has been designed after thorough research to cater to the requirements of people interested in building online tutions centers and tutors marketplace. Students can find available online tutors and unlock any tutor profile to get online tutuion.
 * Version:           2.9
 * Author:            Amento Tech Pvt ltd
 * Author URI:        https://codecanyon.net/user/amentotech/portfolio
 * Text Domain:       tuturn
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

if (!function_exists('tuturn_load_last')) {
	function tuturn_load_last()
	{
		$tuturn_file_path 		= preg_replace('/(.*)plugins\/(.*)$/', WP_PLUGIN_DIR . "/$2", __FILE__);
		$tuturn_plugin 			= plugin_basename(trim($tuturn_file_path));
		$tuturn_active_plugins 	= get_option('active_plugins');
		$tuturn_plugin_key 		= array_search($tuturn_plugin, $tuturn_active_plugins);
		array_splice($tuturn_active_plugins, $tuturn_plugin_key, 1);
		array_push($tuturn_active_plugins, $tuturn_plugin);
		update_option('active_plugins', $tuturn_active_plugins);
	}
	add_action("activated_plugin", "tuturn_load_last");
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('TUTURN_VERSION', '2.9');
define('TUTURN_DIRECTORY', plugin_dir_path(__FILE__));
define('TUTURN_DIRECTORY_URI', plugin_dir_url(__FILE__));
define('TUTURN_ACTIVE_THEME_DIRECTORY', get_stylesheet_directory());
define('TUTURN_BASENAME', plugin_basename(__FILE__));
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-tuturn-activator.php
 */
function tuturn_activate()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-tuturn-activator.php';
	Tuturn_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-tuturn-deactivator.php
 */
function tuturn_deactivate()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-tuturn-deactivator.php';
	Tuturn_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'tuturn_activate');
register_deactivation_hook(__FILE__, 'tuturn_deactivate');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'helpers/email-register-files.php';
require plugin_dir_path(__FILE__) . 'libraries/mailchimp/class-mailchimp-oath.php';
require plugin_dir_path(__FILE__) . 'config.php';
require plugin_dir_path(__FILE__) . './libraries/tcpdf/tcpdf.php'; //tcpdf
require plugin_dir_path(__FILE__) . 'includes/class-tuturn.php';
require plugin_dir_path(__FILE__) . 'elementor/base.php';
require plugin_dir_path(__FILE__) . 'elementor/config.php';
require tuturn_load_template('public/partials/template-loader');
require plugin_dir_path(__FILE__) . 'public/partials/funtions.php';
require plugin_dir_path(__FILE__) . '/admin/plugin-settings/init.php';
require plugin_dir_path(__FILE__) . 'public/partials/class-dashboard-menu.php';
require plugin_dir_path(__FILE__) . 'includes/tgmp/init.php';
require plugin_dir_path(__FILE__) . 'public/partials/hooks.php';
require tuturn_load_template('templates/registration');
require tuturn_load_template('templates/login');
require tuturn_load_template('templates/forgot');
require plugin_dir_path(__FILE__) . 'public/partials/template-hooks.php';
require plugin_dir_path(__FILE__) . 'public/partials/template-hooks.php';
require plugin_dir_path(__FILE__) . 'public/partials/ajax-hooks.php';
require plugin_dir_path(__FILE__) . 'includes/migration.php';
include tuturn_load_template('import-users/class-import-user');
include tuturn_load_template('libraries/mailchimp/class-mailchimp');
require plugin_dir_path(__FILE__) . 'public/partials/radius-search.php';
require tuturn_load_template('widgets/class-contact-info-footer');
require tuturn_load_template('widgets/class-get-mobile-app');
require tuturn_load_template('widgets/class-news-letters');
require tuturn_load_template('widgets/class-nav-menu-widget');
require tuturn_load_template('widgets/class-recent-posts');

/**
 * Get template from plugin or theme.
 *
 * @param string $file  Templat`e file name.
 * @param array  $param Params to add to template.
 *
 * @return string
 */
function tuturn_load_template($file, $param = array())
{
	extract($param);

	if (is_dir(TUTURN_ACTIVE_THEME_DIRECTORY . '/extend/')) {
		if (file_exists(TUTURN_ACTIVE_THEME_DIRECTORY . '/extend/' . $file . '.php')) {
			$template_load = TUTURN_ACTIVE_THEME_DIRECTORY . '/extend/' . $file . '.php';
		} else {
			$template_load = TUTURN_DIRECTORY . '/' . $file . '.php';
		}
	} else {
		$template_load = TUTURN_DIRECTORY . '/' . $file . '.php';
	}
	return $template_load;
}

/**
 * Add http from URL
 */
if (!function_exists('tuturn_add_http_protcol')) {

	function tuturn_add_http_protcol($url)
	{
		$url = set_url_scheme($url);
		return $url;
	}
}
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function tuturn_run()
{

	$plugin = new Tuturn();
	$plugin->run();
}
tuturn_run();

/**
 * Load plugin textdomain.
 *
 * @since 1.0.0
 */
add_action('plugins_loaded', 'tuturn_load_textdomain');
add_action('init', 'tuturn_load_textdomain');
function tuturn_load_textdomain()
{
	load_plugin_textdomain('tuturn', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
