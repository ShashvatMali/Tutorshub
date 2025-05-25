<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://codecanyon.net/user/amentotech/portfolio
 * @since      1.0.0
 *
 * @package    Tuturn
 * @subpackage Tuturn/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Tuturn
 * @subpackage Tuturn/admin
 * @author     Amento Tech Pvt ltd <info@amentotech.com>
 */
class Tuturn_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		/**
		 * The classes used to register custom pos types
		*/
		foreach ( glob( plugin_dir_path( __FILE__ ) . "cpt/*.php" ) as $file ) {
			include_once $file;
		}
		
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-user-purchase-verify.php';
		require plugin_dir_path( __FILE__ )  . 'partials/tuturn-admin-display.php';
		require plugin_dir_path( __FILE__ )  . 'partials/tuturn-page-layout.php';
		require plugin_dir_path( __FILE__ )  . 'partials/tuturn-order-modifiy.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-tuturn-cron-jobs.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-admin-menus-options.php';

		//Set custom product type  
		add_filter( 'product_type_selector', array($this, 'tuturn_add__custom_product_types') );
		add_filter( 'product_type_options', array($this, 'tuturn_service_product_type_options') );
	}

	/**
	* Add service downloadable, virtual
	*/
	public function tuturn_service_product_type_options($options){
		$options['downloadable']['wrapper_class'] = 'show_if_simple show_if_service';
		$options['virtual']['wrapper_class'] = 'show_if_simple show_if_service';
		return $options;
	}

	/**
	* Add package to product type drop down.
	*/
	function tuturn_add__custom_product_types( $product_types ){
		$product_types[ 'packages' ]	= apply_filters('tuturn_product_type_package_title', esc_html__('Packages', 'tuturn'));
		$product_types[ 'service' ]		= apply_filters('tuturn_product_type_service_title', esc_html__('Service', 'tuturn'));
		return $product_types;
	}	

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_register_style( 'tuturn-lightbox',  TUTURN_DIRECTORY_URI . 'public/css/lightbox.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'jquery-confirm',  plugin_dir_url( __FILE__ ) . 'css/jquery-confirm.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'tuturn-admin', plugin_dir_url( __FILE__ ) . 'css/tuturn-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		//wp_enqueue_script( 'jquery-fancybox', TUTURN_DIRECTORY_URI . 'public/js/vendor/jquery.fancybox-1.3.4.pack.js', array(), $this->version, true );
		wp_register_script( 'tuturn-lightbox', TUTURN_DIRECTORY_URI . 'public/js/lightbox.js', array(), $this->version, false );
		wp_enqueue_script( 'jquery-confirm', TUTURN_DIRECTORY_URI . 'public/js/vendor/jquery-confirm.min.js', array(), $this->version, true );
		wp_enqueue_script( 'tuturn-admin', plugin_dir_url( __FILE__ ) . 'js/tuturn-admin.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_media();
		$data = array(
			'ajax_nonce'					=> wp_create_nonce('ajax_nonce'),
			'ajaxurl'						=> admin_url( 'admin-ajax.php' ),
			'import_message' 				=> esc_html__('Are you sure, you want to import users?', 'tuturn'),
			'import'						=> esc_html__('Import users', 'tuturn'),
			'deactivate_account'			=> esc_html__('Uh-Oh!', 'tuturn'),
			'deactivate_account_message'	=> esc_html__('Are you sure, you want to deactivate this account?', 'tuturn'),
			'reject_account'				=> esc_html__('Reject user account', 'tuturn'),
			'reject_account_message'		=> esc_html__('Are you sure, you want to reject this account? After reject, this account will no longer visible in the search listing', 'tuturn'),
			'account_verification'			=> esc_html__('Account verification', 'tuturn'),
			'reason' 						=> esc_html__('Please add reason why you want to reject user uploaded documents?', 'tuturn'),
			'approve_identity'				=> esc_html__('Identity verification', 'tuturn'),
			'approve_identity_message'		=> esc_html__('Are you sure, you want to verify identity of this user?', 'tuturn'),
			'reject_identity'				=> esc_html__('Identity verification', 'tuturn'),
			'reject_identity_message'		=> esc_html__('Are you sure, you want to reject the user identity document submmission?', 'tuturn'),
			'reject_reason_text'			=> esc_html__('Please add reason why you want to reject?', 'tuturn'),
			'approve_account'				=> esc_html__('Approve user account', 'tuturn'),
			'approve_account_message'		=> esc_html__('Are you sure, you want to approve this account? An email will be sent to this user.', 'tuturn'),
			'withdraw_title'				=> esc_html__('Withdraw!', 'tuturn'),
			'withdraw_desc'					=> esc_html__('Are you sure you want to approve withdraw request?', 'tuturn'),
			'order_status_title'			=> esc_html__('Complete order!', 'tuturn'),
			'order_status_message'			=> esc_html__('Are you sure you want to complete this order.', 'tuturn'),
			'yes'			=> esc_html__('Yes', 'tuturn'),
			'close'			=> esc_html__('Close', 'tuturn'),
			'no' 			=> esc_html__('No', 'tuturn'),			
			'accept' 		=> esc_html__('Accept', 'tuturn'),
			'reject' 		=> esc_html__('Reject', 'tuturn'),
			'select_option'	=> esc_html__('Select an option', 'tuturn'),
			'select_file'	=> esc_html__('Select File', 'tuturn'),
			'add_file'		=> esc_html__('Add File', 'tuturn'),
			'reason'		=> esc_html__('Please add reason why you want to reject user uploaded documents?', 'tuturn'),
			'circle_loader'	=> TUTURN_GlobalSettings::get_plugin_url().'public/images/circle-loader.png',
		);

		wp_localize_script('tuturn-admin', 'admin_scripts_vars', $data );
	}

}
