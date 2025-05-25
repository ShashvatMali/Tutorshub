<?php

/**
 * Provide a admin area view for the woocomerce order pages
 *
 * Class 'Tuturn_order_status_update' defines the page layout settings
 *
 * @link       https://codecanyon.net/user/amentotech/portfolio
 * @since      1.0.0
 *
 * @package    Tuturn
 * @subpackage Tuturn/admin/partials
 */
class Tuturn_order_status_update{

	/**
     * constructor
     *
     * @since    1.0.0
     * @access   public
     */
    public function __construct() {
		add_action('init', array(&$this, 'init_order_page_customiztion'));
		add_action('wp_ajax_tuturn_admin_order_status_update', array(&$this, 'tuturn_admin_order_status_update'));
    }
 	 
	public function init_order_page_customiztion(){
 		add_action( 'add_meta_boxes', array(&$this, 'woocommerce_status_update') );
	}

	function woocommerce_status_update(){
		add_meta_box('custom_order_meta_box', __( 'Complete order', 'tuturn' ), array(&$this, 'woocommerce_status_update_button'), 'shop_order', 'side', 'low');
	}

	function woocommerce_status_update_button(){
		$post_id = isset($_GET['post']) ? $_GET['post'] : false;
		$disable = '';
		$button_text = 'Complete order';
		if($post_id){
			$booking_status = get_post_meta( $post_id, 'booking_status',true );
			if(!empty($booking_status) && $booking_status != 'publish'){
				$disable	= 'disabled';
			}
			if(!empty($booking_status && $booking_status === 'completed')){
				$button_text	=  'Order completed';
			}?>
			<button type="button" id="tu-order-status-complete" data-order_id="<?php echo intval($post_id);?>"  class="button button-primary button-large" <?php echo esc_html($disable)?> ><?php echo esc_html($button_text);?></button>
			<?php
		} else {
			return;
		}
	}
 

	/**
	 * @Import Users
	 * @return {}
	 */
	public function tuturn_admin_order_status_update(){
		//security check
		$do_check = check_ajax_referer('ajax_nonce', 'security', false);
		if ( $do_check == false ) {
			$json['type'] = 'error';
			$json['message'] = esc_html__('Security check failed, this could be because of your browser cache. Please clear the cache and check it again', 'tuturn');
			wp_send_json( $json );
		}
		$json		= array();
		$order_id	= !empty($_POST['order_id']) ? $_POST['order_id'] : '';
		if(empty($order_id)){
			$json['type']		= 'error';	
			$json['title']		= esc_html__('Failed','tuturn');	
			$json['message']	= esc_html__('Something went wrong','tuturn' );
			wp_send_json($json);	
		}

		$booking_status = get_post_meta( $order_id, 'booking_status',true );
		$booking_detail 	= get_post_meta( $order_id, 'cus_woo_product_data',true );
		$booked_data    	= !empty($booking_detail['booked_data']) ? $booking_detail['booked_data'] : array();
		$booked_slots   	= !empty($booked_data['booked_slots']) ? $booked_data['booked_slots'] : array();
		$booking_start_time = '';
        $booking_end_time   = '';
		$gmt_time			= current_time( 'mysql', 1 );
		$gmt_time			= date('Y-m-d H:i:s', strtotime($gmt_time));

		
		if(!empty($booking_status) && $booking_status == 'publish'){
			$booking_label   = esc_html__('Ongoing', 'tuturn');
		} elseif(!empty($booking_status) && $booking_status == 'completed'){
			$booking_label   = esc_html__('Completed', 'tuturn');
		} elseif(!empty($booking_status) && $booking_status == 'declined'){
			$booking_label   = esc_html__('Declined', 'tuturn');
		} elseif(!empty($booking_status) && $booking_status == 'cancelled'){
			$booking_label   = esc_html__('Cancelled', 'tuturn');
		} else {
			$booking_label   = esc_html__('Pending', 'tuturn');
		}

		if(!empty($booked_data['booked_slots'])){ 
			$start_appointment_date = array_key_first($booked_data['booked_slots']);
			$start_appointment_date = date('Y-m-d H:i:s', strtotime($start_appointment_date));
			if(!empty($booking_status) && $booking_status == 'publish' && (strtotime($start_appointment_date) < strtotime($gmt_time))){

				if(!empty($booking_status) && $booking_status === 'publish' && !empty($booking_label) && $booking_label === 'Ongoing') {
					$update_status	= update_post_meta( $order_id, 'booking_status', 'completed' );  
					if ( ! is_wp_error( $update_status ) ) {
						$json['type']		= 'success';	
						$json['title']		=  esc_html__('Success','tuturn');	
						$json['message']	=  esc_html__('Order status has been updated.','tuturn');
						wp_send_json( $json );
					} else {
						$json['type']		= 'error';	
						$json['title']		= esc_html__('Failed','tuturn');	
						$json['message']	= esc_html__('Something went wrong','tuturn' );
						wp_send_json($json);
					}
		
				}
			}  else {
				$json['type']		= 'error';	
				$json['title']		= esc_html__('Failed','tuturn');	
				$json['message']	= esc_html__('You can not perform this action at this time.','tuturn' );
				wp_send_json($json);
			}
		} else {
			$json['type']		= 'error';	
			$json['title']		= esc_html__('Failed','tuturn');	
			$json['message']	= esc_html__('Something went wrong','tuturn' );
			wp_send_json($json);
		}
	}
}
new Tuturn_order_status_update();
