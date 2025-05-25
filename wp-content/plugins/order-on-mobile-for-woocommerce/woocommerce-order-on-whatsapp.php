<?php
/**
 * @link              http://www.intolap.com/
 * @since             1.0.0
 * @package           Woocommerce_Order_On_Whatsapp
 *
 * @wordpress-plugin
 * Plugin Name:       Order On Mobile for WooCommerce
 * Plugin URI:        https://www.intolap.com/product/order-on-mobile-for-woocommerce-pro/
 * Description:       This plugin enables store owners to receive orders on WhatsApp. It enables a Order on WhatsApp button which is displayed on the Shop, Single product, Cart pages.
 * Version:           2.2
 * WC requires at least: 9
 * WC tested up to: 9
 * Author:            INTOLAP
 * Author URI:        http://www.intolap.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woocommerce-order-on-whatsapp
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 */
define( 'WOOCOMMERCE_ORDER_ON_WHATSAPP_VERSION', '2.2' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-woocommerce-order-on-whatsapp-activator.php
 */
function activate_woocommerce_order_on_whatsapp() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-order-on-whatsapp-activator.php';
	Woocommerce_Order_On_Whatsapp_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-woocommerce-order-on-whatsapp-deactivator.php
 */
function deactivate_woocommerce_order_on_whatsapp() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-order-on-whatsapp-deactivator.php';
	Woocommerce_Order_On_Whatsapp_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_woocommerce_order_on_whatsapp' );
register_deactivation_hook( __FILE__, 'deactivate_woocommerce_order_on_whatsapp' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-order-on-whatsapp.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since 1.0.0
 */
function run_woocommerce_order_on_whatsapp() {

	$plugin = new Woocommerce_Order_On_Whatsapp();
	$plugin->run();

}
run_woocommerce_order_on_whatsapp();
