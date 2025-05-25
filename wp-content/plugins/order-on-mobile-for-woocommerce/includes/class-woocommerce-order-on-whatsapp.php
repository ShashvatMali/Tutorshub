<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://www.intolap.com/
 * @since    1.0.0
 *
 * @package    Woocommerce_Order_On_Whatsapp
 * @subpackage Woocommerce_Order_On_Whatsapp/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since    1.0.0
 * @package    Woocommerce_Order_On_Whatsapp
 * @subpackage Woocommerce_Order_On_Whatsapp/includes
 * @author     INTOLAP <info@intolap.com>
 */
class Woocommerce_Order_On_Whatsapp {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Woocommerce_Order_On_Whatsapp_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'WOOCOMMERCE_ORDER_ON_WHATSAPP_VERSION' ) ) {
			$this->version = WOOCOMMERCE_ORDER_ON_WHATSAPP_VERSION;
		} else {
			$this->version = '2.0';
		}
		$this->plugin_name = 'woocommerce-order-on-whatsapp';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Woocommerce_Order_On_Whatsapp_Loader. Orchestrates the hooks of the plugin.
	 * - Woocommerce_Order_On_Whatsapp_i18n. Defines internationalization functionality.
	 * - Woocommerce_Order_On_Whatsapp_Admin. Defines all hooks for the admin area.
	 * - Woocommerce_Order_On_Whatsapp_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woocommerce-order-on-whatsapp-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woocommerce-order-on-whatsapp-i18n.php';


		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-woocommerce-order-on-whatsapp-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-woocommerce-order-on-whatsapp-public.php';

		$this->loader = new Woocommerce_Order_On_Whatsapp_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Woocommerce_Order_On_Whatsapp_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Woocommerce_Order_On_Whatsapp_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Woocommerce_Order_On_Whatsapp_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		
		/** Action to display error for empty plugin settings **/	
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'woow_admin_notice_error' );

		$this->loader->add_action('admin_notices', $plugin_admin, 'woow_admin_notice_review');

		/** Action hooks for Woocommerce setting tab  */ 
		$this->loader->add_action( 'woocommerce_settings_tabs', $plugin_admin, 'woow_add_settings_tab' );
		$this->loader->add_action( 'woocommerce_settings_tabs_woow_settings_tab', $plugin_admin, 'woow_settings_tab' );
		$this->loader->add_action( 'woocommerce_update_options_woow_settings_tab', $plugin_admin, 'woow_update_settings' );

		$this->loader->add_action('admin_footer', $plugin_admin, 'woow_uninstall_feedback');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Woocommerce_Order_On_Whatsapp_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		
		// Button on Shop page
		$this->loader->add_action( 'woocommerce_after_shop_loop_item', $plugin_public, 'woow_whatsapp_button_shop_single' );		

		// Button on Single product page
		$this->loader->add_action( 'woocommerce_share', $plugin_public, 'woow_whatsapp_button_shop_single' );
		
		// Button on Cart page
		$this->loader->add_action( 'woocommerce_after_cart_totals', $plugin_public, 'woow_whatsapp_button_cart' );
		$this->loader->add_action( 'wp_ajax_get_cart_contents', $plugin_public, 'woow_get_cart_contents_callback' );
		$this->loader->add_action( 'wp_ajax_nopriv_get_cart_contents', $plugin_public, 'woow_get_cart_contents_callback' );


		$this->loader->add_action( 'woocommerce_after_shop_loop_item', $plugin_public, 'woow_remove_add_to_cart_buttons', 1 );
		$this->loader->add_action( 'woocommerce_single_product_summary', $plugin_public, 'woow_remove_add_to_cart_buttons', 2  );

		$this->loader->add_action( 'woocommerce_proceed_to_checkout', $plugin_public, 'woow_remove_proceed_to_checkout_buttons');
		$this->loader->add_action( 'woocommerce_widget_shopping_cart_buttons', $plugin_public, 'woow_remove_proceed_to_checkout_buttons');
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     2.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     2.0
	 * @return    Woocommerce_Order_On_Whatsapp_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     2.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
