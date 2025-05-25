<?php

/**
 * Fired during plugin activation
 *
 * @link       http://www.intolap.com/
 * @since    1.0.0
 *
 * @package    Woocommerce_Order_On_Whatsapp
 * @subpackage Woocommerce_Order_On_Whatsapp/includes
 */

/**
 * Fired during plugin activation.
 *
 * @since    1.0.0
 * @package    Woocommerce_Order_On_Whatsapp
 * @subpackage Woocommerce_Order_On_Whatsapp/includes
 * @author     INTOLAP <info@intolap.com>
 */
class Woocommerce_Order_On_Whatsapp_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		if (is_plugin_active('order-on-mobile-for-woocommerce-pro/order-on-mobile-for-woocommerce-pro.php') ){
	        deactivate_plugins('order-on-mobile-for-woocommerce-pro/order-on-mobile-for-woocommerce-pro.php');
	    }
	}
}
