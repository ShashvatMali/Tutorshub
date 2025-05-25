<?php
/**
 * 
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://themeforest.net/user/amentotech/portfolio
 * @since      1.0.0
 *
 * @package    Tuturn
 * @subpackage Tuturn
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die('No kiddies please!');
}

if( !class_exists( 'TUTURN_GlobalSettings' ) ) {

	abstract class TUTURN_GlobalSettings{

		/**
	 * Getter for Plugin Path
	 *
	 * @since    1.0.0
	 * @access   static
	 * @var      string    get_plugin_path    The ID of this plugin.
	 */
		public static function get_plugin_path(){
			return plugin_dir_path( __FILE__ );
		}
		
		/**
	 * Getter for Plugin URL
	 *
	 * @since    1.0.0
	 * @access   static
	 * @var      string    get_plugin_url    The ID of this plugin.
	 */
		public static function get_plugin_url(){
			return plugin_dir_url( __FILE__ );	
		}
		 
	}
}