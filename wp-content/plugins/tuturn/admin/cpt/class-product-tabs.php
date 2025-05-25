<?php
namespace PackageTabs;
/**
 * 
 * Class 'Tuturn_Product_Tabs' defines to remove the product data default tabs
 *
 * @package     Tuturn
 * @subpackage  Tuturn/admin/CPT
 * @author      Amentotech <info@amentotech.com>
 * @link        http://amentotech.com/
 * @version     1.0
 * @since       1.0
*/
class Tuturn_Product_Tabs {

	/**
	 * Add woocommerce filter 'woocommerce_product_data_tabs' to remove default tabs.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function __construct() {
		add_filter( 'woocommerce_product_data_tabs', array($this, 'tuturn_remove_proudct_data_default_tabs') );
		add_action( 'woocommerce_product_data_panels', array($this, 'tuturn_product_custom_data_fields') );
		add_action( 'woocommerce_product_class', array(&$this, 'tuturn_woocommerce_product_class'), 10, 2 );
		add_action( 'save_post', array($this, 'tuturn_products_package_meta_save'), 9999 );
		add_action( 'woocommerce_order_status_changed', array($this, 'tuturn_order_custom_status'), 10, 4 );
	}

	/**
	* Add to product data custom tabs.
	 * @since    1.0.0
	 * @access   public
	*/
	public function tuturn_product_get_data_tabs(){
		$product_data_tabs = array(						
			'package_fields'	=> array(
				'label'		=> esc_html__( 'Package fields', 'tuturn' ),
				'target'	=> 'tuturn_package_product_data',
				'class'		=> array('show_if_packages'),
				'priority'	=> 40,
			),					

		);
		return apply_filters('tuturn_product_get_data_tabs', $product_data_tabs);
	}	

	/**
	* Remove default product data tabs 
	* @since    1.0.0
	* @access   public
	*/
	public function tuturn_order_custom_status($order_id, $old_status, $new_status, $order){
		if ( $new_status == 'cancelled' || $new_status == 'failed' || $new_status == 'refunded' ){
			$status	= 'declined';
		} elseif ( $new_status == 'processing' || $new_status == 'on-hold' || $new_status == 'pending' ){
			$status	= 'pending';
		} elseif ( $new_status == 'completed' ){
			$status	= 'publish';
		}

		update_post_meta( $order_id, 'order_status' , $status );
	}

	/**
	* Remove default product data tabs 
	* Set custom tabs
	* @since    1.0.0
	* @access   public
	*/
	public function tuturn_remove_proudct_data_default_tabs( $tabs ){
		$product_data_tabs = $this->tuturn_product_get_data_tabs();
		$tabs = array_merge($tabs, $product_data_tabs);
		return apply_filters('tuturn_product_tabs', $tabs);
	}

	/**
	* Product type body class
	 * @since    1.0.0
	 * @access   public
	*/
	public function tuturn_product_type_in_body_class( $classes ){
		global $product;
 		// get the current product in the loop
		$product = wc_get_product();
		$classes[] = '  product-type-' . $product->get_type();	 
		return $classes;	 
	}
	
	/**	
	 * Custom product types
	 * @since    1.0.0
	 * @access   public
	*/
	public function tuturn_woocommerce_product_class( $classname, $product_type ) {

		if ( $product_type == 'packages' ) {
			$classname = 'WC_Product_Packages';
		} elseif ( $product_type == 'service' ) {
			$classname = 'WC_Product_Service';
		}
		return $classname;
	}

	/**
	* Add to product data plan custom tab panel.
	* @since    1.0.0
	* @access   public	
	*/
	public function tuturn_product_custom_data_fields() {
		global $woocommerce, $post;
		?>		
		<div id="tuturn_package_product_data" class="panel woocommerce_options_panel">
			<?php include TUTURN_DIRECTORY.'admin/partials/product-data-package.php'; ?>
		</div>		
		<?php	
	}

	/**
	* Save product package meta
	* @since    1.0.0
	* @access   public	
	*/
	public function tuturn_products_package_meta_save($post_id){
		global $post;
		// Autosave, do nothing
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// AJAX? Not used here
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		// Check user permissions
		if ( ! current_user_can( 'edit_post', $post_id ) ){
			return;
		}

		// Only set for post_type
		if ( !isset($post->post_type) ) {
			return;
		}

		// Only set for post_type = post!
		if ( 'product' !== $post->post_type ) {
			return;
		}

		// Return if it's a post revision
		if ( false !== wp_is_post_revision( $post_id ) ){
			return;
		}

		// Return if this product type is not packages
		if(!empty($_POST['product-type']) && $_POST['product-type'] !== 'packages'){
			return;
		}	

		//Save package fields
		if(isset($_POST['package'])){
			$package = isset( $_POST['package'] ) ? $_POST['package'] : array();

			$package['contact_info'] = isset( $package['contact_info'] ) ? esc_html( $package['contact_info'] ) : 'no';
			$package['languages'] = isset( $package['languages'] ) ? esc_html( $package['languages'] ) : 'no';
			$package['team_members'] = isset( $package['team_members'] ) ? esc_html( $package['team_members'] ) : 'no';
			$package['gallery'] = isset( $package['gallery'] ) ? esc_html( $package['gallery'] ) : 'no';
			$package['education'] = isset( $package['education'] ) ? esc_html( $package['education'] ) : 'no';
			$package['teaching'] = isset( $package['teaching'] ) ? esc_html( $package['teaching'] ) : 'no';
			$package['user_type'] = isset( $package['user_type'] ) ? esc_html( $package['user_type'] ) : 'tutor';

			if(isset($package['package_type'])){
				$package_type = isset( $package['package_type'] ) ? esc_html( $package['package_type'] ) : 'days';
				update_post_meta($post_id, 'package_type', $package_type);
			}

			if(isset($package['user_type'])){
				$user_type = isset( $package['user_type'] ) ? esc_html( $package['user_type'] ) : 'tutor';
				update_post_meta($post_id, 'user_type', $user_type);
			}

			if(isset($package['package_duration'])){
				$package_duration = isset( $package['package_duration'] ) ? intval( $package['package_duration'] ) : 0;
				update_post_meta($post_id, 'package_duration', $package_duration);
			}

			update_post_meta($post_id, 'tuturn_package_detail', $package);
		}
	}

}
new Tuturn_Product_Tabs();
