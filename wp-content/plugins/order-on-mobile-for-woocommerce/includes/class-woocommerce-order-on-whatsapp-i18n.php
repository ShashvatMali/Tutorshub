<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://www.intolap.com/
 * @since    1.0.0
 *
 * @package    Woocommerce_Order_On_Whatsapp
 * @subpackage Woocommerce_Order_On_Whatsapp/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since    1.0.0
 * @package    Woocommerce_Order_On_Whatsapp
 * @subpackage Woocommerce_Order_On_Whatsapp/includes
 * @author     INTOLAP <info@intolap.com>
 */
class Woocommerce_Order_On_Whatsapp_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'woocommerce-order-on-whatsapp',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
