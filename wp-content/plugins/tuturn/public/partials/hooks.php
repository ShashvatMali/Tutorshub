<?php
/**
 * Provide a public-facing hooks
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://themeforest.net/user/amentotech/portfolio
 * @since      1.0.0
 *
 * @package    Tuturn
 * @subpackage Tuturn/public/partials
 */
 /**
 * Show products from specific product types on the Shop page.
 */
if (class_exists('WooCommerce')) {
	if (!function_exists('tuturn_shop_filter_product_type')) {
		add_action( 'woocommerce_product_query', 'tuturn_shop_filter_product_type' );		
		function tuturn_shop_filter_product_type( $q ) {
			$tax_query = (array) $q->get( 'tax_query' );
			$tax_query[] = array(
				'taxonomy' => 'product_type',
				'field' => 'slug',
				'terms' => array( 'packages', 'service' ),
				'operator' => 'NOT IN'
			);
			$q->set( 'tax_query', $tax_query );	
		}
	}
}

/**
 * Download tutoring log
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if (!function_exists('download_tutoring_log_csv')) {
	add_action('download_tutoring_log_csv','download_tutoring_log_csv',10,1);
	function download_tutoring_log_csv($user_identity){
		global $current_user, $wpdb;
		$time_format    = get_option('time_format');
		$date_format    = get_option('date_format');
		if(!empty($_POST['download']) ){
			$file_name	= 'tutoring_log';
			header('Content-Type: text/csv');
			header('Content-Disposition: attachment; filename="'.$file_name.'.csv"');
		
			ob_end_clean();
		
			$output_handle 		= fopen('php://output', 'w');
			$filename           = "log_data_" . date('Ymd') . ".xls";

			$withdraw_titles	= array(
				esc_html__('Student name','tuturn'),
				esc_html__('Student email','tuturn'),
				esc_html__('Total time','tuturn'),
				esc_html__('Start time','tuturn'),
				esc_html__('End time','tuturn'),
				esc_html__('Date','tuturn'),
				esc_html__('Status','tuturn'),
				esc_html__('Logo title','tuturn'),
				esc_html__('Logo description','tuturn'),
			);
		
			$args = array(
				'posts_per_page' => -1,
				'post_type'      => 'volunteer-hours',
				'post_status'    => array('any'),
				'orderby'        => 'date',
				'author__in'     => $user_identity,
			);
			
			$post_data		= get_posts($args);
			$csv_fields     = array();
		
			foreach($withdraw_titles as $title){
				$csv_fields[] = $title;
			}
		
			fputcsv($output_handle, $csv_fields);
		
			if( !empty($post_data) ){
				foreach($post_data as $row){

					$hourly_data        = get_post_meta($row->ID,'hourly_data',true );
					$post_title    		= $row->post_title;
					$post_content  		= $row->post_content;

					$student_id         = !empty($hourly_data['student_id']) ? intval($hourly_data['student_id']) : 0; 
					$student_proflie    = !empty($student_id) ? get_user_meta($student_id,'_linked_profile',true ) : 0;
					$user_name          = tuturn_get_username($student_proflie);
					$user_email			= !empty($student_id) ? get_userdata($student_id)->user_email : '';

					$hourly_date        = !empty($hourly_data['date']) ? ($hourly_data['date']) : 0; 
					$start_time         = !empty($hourly_data['start_time']) ? date_i18n($time_format,strtotime($hourly_data['start_time'])) : 0; 
					$end_time           = !empty($hourly_data['end_time']) ? date_i18n($time_format,strtotime($hourly_data['end_time'])) : 0; 
					$hourly_date        = date_i18n($date_format, strtotime($hourly_date));
					$total_hours        = isset($hourly_data['total_hours']) ? $hourly_data['total_hours'] : 0;
					$post_status        = !empty($row->post_status) ? $row->post_status : '';

					$row_data['student_name']			= $user_name;
					$row_data['student_email']			= $user_email;
					$row_data['total_time']				= $total_hours;
					$row_data['start_time']				= $start_time;
					$row_data['end_time']				= $end_time;
					$row_data['date']					= $hourly_date;
					$row_data['status']					= $post_status;
					$row_data['log_title']				= $post_title;
					$row_data['log_description']		= $post_content;

					$OutputRecord = $row_data;
					fputcsv($output_handle, $OutputRecord);
				}
			}
		
			fclose( $output_handle );
			exit;
		}
	}
}

/**
 * Order options
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if (class_exists('WooCommerce')) {
	if (!function_exists('tuturn_order_option')) {
		add_action( 'init', 'tuturn_order_option' );
		function tuturn_order_option(){
			add_filter( 'woocommerce_cod_process_payment_order_status','tuturn_update_order_status', 10, 2 );
			add_filter( 'woocommerce_cheque_process_payment_order_status','tuturn_update_order_status', 10, 2 );
			add_filter( 'woocommerce_bacs_process_payment_order_status','tuturn_update_order_status', 10, 2 );
			if( is_admin() ){
				add_action( 'woocommerce_order_status_completed','tuturn_payment_complete',10,1 );
			}
		}
	}
}

/**
 * change status for offline payment gateway
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if (!function_exists('tuturn_update_order_status')) {
	function tuturn_update_order_status( $status,$order  ) {
		return 'on-hold';
	}
}

/**
 * Display order detail
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if (!function_exists('tuturn_display_order_data_success')) {
	add_action( 'woocommerce_thankyou', 'tuturn_display_order_data_success', 20 );
	function tuturn_display_order_data_success( $order_id ) {
        global $woocommerce,$current_user;
		$order = new WC_Order( $order_id );
		$items = $order->get_items();
		$order_detail 	= get_post_meta( $order_id, 'cus_woo_product_data', true );
		$payment_type 	= get_post_meta( $order_id, 'payment_type', true );
		$userType 	        = apply_filters('tuturnGetUserType', $current_user->ID );

        if( !empty( $payment_type ) && $payment_type == 'package' ) {
			$product_id 	= get_post_meta( $order_id, 'package_id', true );
			$product_id		= !empty($product_id) ? $product_id : 0;
			$package		= wc_get_product($product_id); ?>
			<div class="row">
				<div class="col-md-12">
					<div class="cart-data-wrap">
						<div class="selection-wrap">
							<div class="tu-haslayout">
								<div class="cart-data-wrap">
									<h3><?php esc_html_e('Summary','tuturn');?></h3>
									<div class="selection-wrap">
										<?php tuturn_get_template_part('dashboard/'.$userType.'/package', 'item',array('package_id'=>$product_id,'buy_btn' => ''));?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
		} elseif( !empty( $order_detail ) && !empty( $payment_type ) && $payment_type == 'booking' ) {
            $booked_data		= !empty($order_detail['booked_data']) ? $order_detail['booked_data'] : array();
            ?>
            <div class="tu-haslayout">
                <div class="cart-data-wrap">
                    <h3><?php esc_html_e('Summary','tuturn');?></h3>
                    <div class="selection-wrap">
                    <?php tuturn_get_template_part('dashboard/dashboard', 'booking-details',array('booked_data'=>$booked_data));?>
                    </div>
                </div>
            </div>
        	<?php
        }
    }
}

/**
 * Complete order
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if (!function_exists('tuturn_payment_complete')) {
    add_action('woocommerce_payment_complete', 'tuturn_payment_complete',10,1 );
    function tuturn_payment_complete($order_id) {
		global $current_user, $wpdb;

		if (class_exists('WooCommerce')) {
			$order 			= wc_get_order($order_id);
			$user 			= $order->get_user_id( );
			$items 			= $order->get_items();
			$current_date 	= current_time('mysql');
			//Update order status
			$order->update_status( 'completed' );
			$order->save();

			if(!empty($items)){
				foreach ($items as $key => $item) {
					if ($user) {
						$order_detail 	= wc_get_order_item_meta( $key, 'cus_woo_product_data', true );
						if( !empty($order_detail['payment_type']) && $order_detail['payment_type'] == 'package' ){
							tuturn_update_packages_data( $order_id,$order_detail,$current_user->ID);
						} else if( !empty($order_detail['payment_type']) && $order_detail['payment_type'] == 'booking' ){
							tuturn_update_services_data( $order_id,$order_detail,$current_user->ID);
						}
						
						update_post_meta($order_id,'tu_order_date',date('Y-m-d H:i:s', strtotime($current_date)));
						update_post_meta($order_id,'tu_order_date_gmt',strtotime($current_date));
					}
				}
			}
		}
    }
}

/**
 * Update User Hiring payment
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */

if (!function_exists('tuturn_update_services_data')) {
    function tuturn_update_services_data( $order_id,$order_detail,$user_id) {
        $instructor_id    = !empty($order_detail['instructor_id']) ? $order_detail['instructor_id'] : 0;
        $student_id      = !empty($order_detail['student_id']) ? $order_detail['student_id'] : 0;
		update_post_meta( $order_id, 'student_id',$student_id );
    	update_post_meta( $order_id, 'instructor_id',$instructor_id );
		update_post_meta( $order_id, 'booking_status', 'publish' );
		$order          = wc_get_order($order_id);
		$order_total	= $order->get_total();
		$order_total  	= !empty($order_total) ? $order_total : 0.0;
		$order_total_tax	= $order->get_total_tax();
		$order_total_tax  	= !empty($order_total_tax) ? $order_total_tax : 0.0;

		if(!empty($order_total_tax)){
			$order_total	= $order_total	- $order_total_tax;
		}
		$service_fee	= tuturn_commission_fee($order_total);
		$admin_share   	= !empty($service_fee['admin_shares']) ? $service_fee['admin_shares'] : 0.0;
		$instructor_shares	= !empty($service_fee['instructor_shares']) ? $service_fee['instructor_shares'] : 0.0;
		update_post_meta( $order_id, 'admin_share', $admin_share );
        update_post_meta( $order_id, 'instructor_shares', $instructor_shares );
	}
}

/**
 * Update User Hiring payment
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if (!function_exists('tuturn_update_packages_data')) {
    function tuturn_update_packages_data( $order_id = 0,$order_detail = array(),$user_id = 0) {
		$package_id		    = !empty($order_detail['package_id']) ? $order_detail['package_id'] : 0;
		$package_id		    = !empty($package_id) ? $package_id : 0;
		$profile_id     	= tuturn_get_linked_profile_id( $user_id );
		$userType 	        = apply_filters('tuturnGetUserType', $user_id );

        $tuturn_package		= get_post_meta($package_id, 'tuturn_package_detail', true);
        $tuturn_package		= !empty($tuturn_package) ? $tuturn_package : array();
		$package_type    	= !empty($tuturn_package['package_type']) ? $tuturn_package['package_type'] : '';
		$package_duration   = !empty($tuturn_package['package_duration']) ? intval($tuturn_package['package_duration']) : 1;
	
		$featured_profile_days	= !empty($tuturn_package['featured_profile']) ? $tuturn_package['featured_profile'] : 0;

		$featured_profile		= 'no';
		if($featured_profile){
			$featured_profile	= 'yes';
		}		     
		
        if( !empty($package_type) && $package_type == 'month'){
            $package_duration   = $package_duration * 30;
        } else if( !empty($package_type) && $package_type == 'year'){
            $package_duration   = $package_duration * 365;
        }

		if(!empty($userType) && $userType === 'student'){
			$package_details		= array();
		}else{
			$package_details		= $tuturn_package;
		}
		$type	                = tuturn_price_plans_duration($package_type);
		
		if($package_duration > 1){
			$type	= 'days';
		}

		$add_date_time			= $package_duration.' '.$type;
		$current_date_time		= date("Y-m-d H:i:s");

		$featured_profile_expriy_date	= date("Y-m-d H:i:s", strtotime($current_date_time. ' + '.$featured_profile_days.' days'));

		$package_expriy_date	= date("Y-m-d H:i:s", strtotime($current_date_time. ' + '.$add_date_time));
		$package_details['package_create_date']	= $current_date_time;
		$package_details['package_expriy_date']	= $package_expriy_date;

		
		$package_expriy_date	= date("Y-m-d H:i:s", strtotime($current_date_time. ' + '.$add_date_time));

		if(!empty($userType) && $userType === 'student'){
			update_post_meta( $order_id, 'student_id',$user_id );
		}else{
			$featured_profile_expriy_date	= date("Y-m-d H:i:s", strtotime($current_date_time. ' + '.$featured_profile_days.' days'));
			update_post_meta( $order_id, 'instructor_id',$user_id );

			update_post_meta( $profile_id, 'featured_expriy_date', $featured_profile_expriy_date );
			update_post_meta( $profile_id, 'featured_profile', $featured_profile );
		}


		//Update order
		update_post_meta( $order_id, 'package_details',$package_details );
		update_post_meta( $order_id, 'package_id',$package_id );
		update_post_meta( $order_id, '_linked_profile',$profile_id );
		update_post_meta( $order_id, 'order_type','package' );
		update_post_meta( $order_id, 'payment_type','package' );

		//wp user
		update_user_meta( $user_id, 'package_details',$package_details );
		update_user_meta( $user_id, 'package_create_date', $current_date_time );
		update_user_meta( $user_id, 'package_expriy_date', $package_expriy_date );
		update_user_meta( $user_id, 'package_order_id', $order_id );
		//Linked profile
		update_post_meta( $profile_id, 'package_expriy_date', $package_expriy_date );
	}
}

/**
 * Price override
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if (!function_exists('tuturn_apply_custom_price_to_cart_item')) {
	add_action( 'woocommerce_before_calculate_totals', 'tuturn_apply_custom_price_to_cart_item', 99 );
	function tuturn_apply_custom_price_to_cart_item( $cart_object ) {
		if( !WC()->session->__isset( "reload_checkout" )) {
			foreach ( $cart_object->cart_contents as $key => $value ) {
				$product 		= $value['data'];
				$product_id		= !empty($value['product_id']) ? $value['product_id'] : 0;

				if( !empty( $value['payment_type'] ) && $value['payment_type'] == 'booking' ){
					if( isset( $value['cart_data']['price'] ) ){
						$bk_price = floatval( $value['cart_data']['price'] );
						$value['data']->set_price($bk_price);
					}
				}
			}
		}
	}
}

/**
 * Add meta on order item
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if (!function_exists('tuturn_woo_convert_item_session_to_order_meta')) {
	add_action( 'woocommerce_new_order_item', 'tuturn_woo_convert_item_session_to_order_meta',  1, 3 );
	function tuturn_woo_convert_item_session_to_order_meta( $item_id, $item, $order_id ) {
		global $tuturn_settings;
		$order          	= wc_get_order($order_id);
		$order_total		= $order->get_total();
		$order_total  		= !empty($order_total) ? $order_total : 0.0;
		$order_total_tax	= $order->get_total_tax();
		$order_total_tax  	= !empty($order_total_tax) ? $order_total_tax : 0.0;
        $payment_type		= !empty($item->legacy_values['payment_type']) ? $item->legacy_values['payment_type'] : '';


		if( !empty($payment_type) && $payment_type === 'package' ){
			if ( !empty( $item->legacy_values['cart_data'] ) ) {
				wc_add_order_item_meta( $item_id, 'cus_woo_product_data', $item->legacy_values['cart_data'] );
				update_post_meta( $order_id, 'cus_woo_product_data', $item->legacy_values['cart_data'] );
			}

			if ( !empty( $item->legacy_values['package_id'] ) ) {
				update_post_meta( $order_id, 'package_id', $item->legacy_values['package_id'] );
			}

			if ( !empty( $item->legacy_values['payment_type'] ) ) {
				update_post_meta( $order_id, 'payment_type', $item->legacy_values['payment_type'] );
			}

			$instructor_id    = !empty($item->legacy_values['cart_data']['instructor_id']) ? $item->legacy_values['cart_data']['instructor_id'] : '';
			update_post_meta( $order_id, 'instructor_id', $instructor_id );			
			/* email to instructor on package buy */
			if (class_exists('Tuturn_Email_Helper')) {
				$emailData	= array();
				if (class_exists('TuturnPackagesEmail')) {
					$package_id = $item->legacy_values['package_id'];
					$product    					= wc_get_product( $package_id );
					$package_name  					= $product->get_title();
					$order_price					= $product->get_price();					
					$instructor_data				= get_userdata($instructor_id);
					$instructorprofile_name			= !empty($instructor_data->display_name) ? $instructor_data->display_name : '';
					$instructorprofile_email		= !empty($instructor_data->user_email) ? $instructor_data->user_email : '';
					$instructor_profile_id 			= tuturn_get_linked_profile_id( $instructor_id);
					/* Instructor details */
					$instructor_profileData   		= get_post_meta($instructor_profile_id, 'profile_details', true);
					$instructor_name				= !empty($instructor_profileData['first_name']) ? $instructor_profileData['first_name'] : '';
					$instructor_contact_detail		= !empty($instructor_profileData['contact_info']) ? $instructor_profileData['contact_info'] : array();
					$email_helper 					= new TuturnPackagesEmail();
					$emailData['instructor_name'] 	= !empty($instructor_name) ? $instructor_name : $instructorprofile_name;
					$emailData['instructor_email'] 	= !empty($instructor_contact_detail['email']) ? $instructor_contact_detail['email'] : $instructorprofile_email;
					$emailData['order_id'] 			= !empty($order_id) ? $order_id : 0;
					$emailData['order_amount'] 		= !empty($order_price) ? $order_price : 0;
					$emailData['login_url'] 		= '';
					$emailData['package_name'] 		= $package_name;

					if ( !empty($tuturn_settings['email_package_instructor'])) {
						$email_helper->package_purchase_instructor_email($emailData);
					}
					
				}
			}
		} else if( !empty($payment_type) && $payment_type === 'booking' ){
            if ( !empty( $item->legacy_values['cart_data'] ) ) {
                $admin_share        	= !empty($item->legacy_values['cart_data']['admin_shares']) ? $item->legacy_values['cart_data']['admin_shares'] : 0.0;
				$instructor_shares		= !empty($item->legacy_values['cart_data']['instructor_shares']) ? $item->legacy_values['cart_data']['instructor_shares'] : 0.0;
                $price    				= !empty($item->legacy_values['cart_data']['price']) ? $item->legacy_values['cart_data']['price'] : 0.00;

				wc_add_order_item_meta( $item_id, 'cus_woo_product_data', $item->legacy_values['cart_data'] );
				update_post_meta( $order_id, 'cus_woo_product_data', $item->legacy_values['cart_data'] );
                update_post_meta( $order_id, 'payment_type', $item->legacy_values['payment_type'] );
				
				$order_total  = !empty($order_total) ? $order_total : $price;
				if(!empty($order_total_tax)){
					$order_total	= $order_total	- $order_total_tax;
				}
				$service_fee		= tuturn_commission_fee($order_total);
				$admin_share   		= !empty($service_fee['admin_shares']) ? $service_fee['admin_shares'] : $admin_share;
				$instructor_shares	= !empty($service_fee['instructor_shares']) ? $service_fee['instructor_shares'] : $instructor_shares;

                if ( !empty( $item->legacy_values['cart_data']['booked_data'] ) ) {
                    $booked_data    	= !empty($item->legacy_values['cart_data']['booked_data']) ? $item->legacy_values['cart_data']['booked_data'] : array();
                    $student_id    		= !empty($item->legacy_values['cart_data']['student_id']) ? $item->legacy_values['cart_data']['student_id'] : '';
                    $instructor_id    	= !empty($item->legacy_values['cart_data']['instructor_id']) ? $item->legacy_values['cart_data']['instructor_id'] : '';
                    $booked_slots   	= !empty($booked_data['booked_slots']) ? $booked_data['booked_slots'] : array();
                    $booking_user   	= !empty($booked_data['information']['info_someone_else']) ? 1 : 0;
					if(!empty($booked_data['product']) && is_array($booked_data['product']) && count($booked_data['product'])>0){
						$service_names	= array_column($booked_data['product'], 'name');
						update_post_meta( $order_id, 'service_names', $service_names );
					}

					$booked_slots_dates	= array_keys($booked_slots);
					update_post_meta( $order_id, '_booking_date', $booked_slots_dates );

                    update_post_meta( $order_id, 'is_user_booking', $booking_user );
                    update_post_meta( $order_id, 'booking_status', 'pending' );
					if(!empty($booked_slots)){
						update_post_meta( $order_id, '_booking_slots', $booked_slots );	
					}
					
					update_post_meta( $order_id, 'student_id',$student_id );
    				update_post_meta( $order_id, 'instructor_id',$instructor_id );
					$instructor_profile_id 		= tuturn_get_linked_profile_id( $instructor_id );
					$student_profile_id 		= tuturn_get_linked_profile_id( $student_id );
					update_post_meta( $order_id, '_linked_profile',$student_profile_id );
					update_post_meta( $order_id, 'student_profile_id',$student_profile_id );
    				update_post_meta( $order_id, 'instructor_profile_id',$instructor_profile_id );

					/* save extra data for booking slots */
					do_action('tuturn_add_extra_slots_meta', $order_id, $booked_data);

					/* email to instructor and student on booking */
					if (class_exists('Tuturn_Email_helper')) {
						$emailData	= array();
						if (class_exists('TuturnOrderStatuses')) {
							$default_chat_mesage	= wp_kses(__('Congratulations! You have hired for the booking "{{booking_name}}".<br/> with order link: {{order_link}}.', 'tuturn'),
								array(
									'a' => array(
									'href' => array(),
									'title' => array()
									),
									'br' => array(),
									'em' => array(),
									'strong' => array(),
								));
							$instructor_profile_id 			= tuturn_get_linked_profile_id( $instructor_id, 'instructor' );
							$student_profile_id 			= tuturn_get_linked_profile_id( $student_id,'', 'student' );
							/* instructor details */
							$instructor_profileData   		= get_post_meta($instructor_profile_id, 'profile_details', true);
							$instructor_name				= !empty($instructor_profileData['first_name']) ? $instructor_profileData['first_name'] : '';
							$instructor_contact_detail		= !empty($instructor_profileData['contact_info']) ? $instructor_profileData['contact_info'] : array();
							$instructor_data				= get_userdata($instructor_id);
							$instructorprofile_name			= !empty($instructor_data->display_name) ? $instructor_data->display_name : '';
							$instructorprofile_email		= !empty($instructor_data->user_email) ? $instructor_data->user_email : '';
							/* student details */
							$student_profileData   			= get_post_meta($student_profile_id, 'profile_details', true);
							$student_name					= !empty($student_profileData['first_name']) ? $student_profileData['first_name'] : '';
							$student_contact_detail			= !empty($student_profileData['contact_info']) ? $student_profileData['contact_info'] : array();
							$student_data					= get_userdata($student_id);
							$studentprofile_name			= !empty($student_data->display_name) ? $student_data->display_name : '';
							$studentprofile_email			= !empty($student_data->user_email) ? $student_data->user_email : '';
							$email_helper 					= new TuturnOrderStatuses();
							$emailData['instructor_name'] 	= !empty($instructor_name) ? $instructor_name : $instructorprofile_name;
							$emailData['instructor_email'] 	= !empty($instructor_contact_detail['email']) ? $instructor_contact_detail['email'] : $instructorprofile_email;
							$emailData['student_name'] 		= !empty($student_name) ? $student_name : $studentprofile_name;
							$emailData['student_email']		= !empty($student_contact_detail['email']) ? $student_contact_detail['email'] : $studentprofile_email;
							$emailData['order_id'] 			= !empty($order_id) ? $order_id : 0;
							$emailData['order_amount'] 		= $order->get_total();
							$emailData['sender_id']         = $instructor_id; //instructor id
							$emailData['receiver_id']       = $student_id; //student id
							$emailData['login_url'] 		= Tuturn_Profile_Menu::tuturn_profile_menu_link('booking', $instructor_id, true, 'listings');
 							$service_names					= esc_html__('Tutor Service', 'tuturn');
							$current_page_link  			= get_permalink().'profile-settings/';
							$invoice_url  					= add_query_arg(array('tab' => 'invoices','mode' => 'detail', 'id'=>intval( $order_id)), $current_page_link);
							if( apply_filters( 'tuturn_chat_solution_guppy',false ) === true && $tuturn_settings['hire_instructor_chat_switch'] == true){
								$message 		= !empty($tuturn_settings['hire_instructor_chat_mesage']) ? $tuturn_settings['hire_instructor_chat_mesage'] : $default_chat_mesage;
								$chat_mesage  	= str_replace("{{booking_name}}", $service_names, $message);
								$chat_mesage  	= str_replace("{{order_link}}", $invoice_url, $chat_mesage);
								do_action('wpguppy_send_message_to_user', $student_id, $instructor_id, $chat_mesage);
							}

							if (!empty($tuturn_settings['email_new_booking_instructor'])) {
								$email_helper->new_booking_instructor_email($emailData);
							}

							if ( !empty($tuturn_settings['email_new_booking_student'])) {
								$email_helper->new_booking_student_email($emailData);
							}

							do_action('noty_push_notification', $emailData);
						}
					}
                }
                update_post_meta( $order_id, 'admin_share', $admin_share );
                update_post_meta( $order_id, 'instructor_shares', $instructor_shares );	
			}
        }
    }
}

/**
 * Display order detail
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if (!function_exists('tuturn_display_order_data')) {
	add_action( 'tuturn_display_order_data', 'tuturn_display_order_data', 20 );
	add_action( 'woocommerce_view_order', 'tuturn_display_order_data', 20 );
	function tuturn_display_order_data( $order_id ) {
        global $woocommerce,$current_user;
		if (class_exists('WooCommerce')) {
			$order = new WC_Order( $order_id );
			$items = $order->get_items();
			$order_detail 	= get_post_meta( $order_id, 'cus_woo_product_data', true );
			$payment_type 	= get_post_meta( $order_id, 'payment_type', true );
			$userType 	        = apply_filters('tuturnGetUserType', $current_user->ID );
			
			if( !empty( $order_detail ) && !empty( $payment_type ) && $payment_type == 'package' ) {
				$product_id 	= get_post_meta( $order_id, 'package_id', true );
				$product_id		= !empty($product_id) ? $product_id : 0;
				$package		= wc_get_product($product_id); ?>
				<div class="row">
					<div class="col-md-12">
						<div class="cart-data-wrap">
							<div class="selection-wrap">
								<div class="tu-haslayout">
									<div class="cart-data-wrap">
										<h3><?php esc_html_e('Summary','tuturn');?></h3>
										<div class="selection-wrap">
											<?php tuturn_get_template_part('dashboard/'.$userType.'/package', 'item',array('package_id'=>$product_id,'buy_btn' => 'yes'));?>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php
			} elseif( !empty( $order_detail ) && !empty( $payment_type ) && $payment_type == 'booking' ) {
				$booked_data		= !empty($order_detail['booked_data']) ? $order_detail['booked_data'] : array();
				?>
				<div class="tu-haslayout">
					<div class="cart-data-wrap">
						<h3><?php esc_html_e('Summary','tuturn');?></h3>
						<div class="selection-wrap">
						<?php tuturn_get_template_part('dashboard/dashboard', 'booking-details',array('booked_data'=>$booked_data));?>
						</div>
					</div>
				</div>
			<?php
			}
		}
    }
}

/**
 * Add data in checkout
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if (!function_exists('tuturn_checkout_after_customer_details')) {
	add_filter( 'woocommerce_checkout_after_customer_details', 'tuturn_checkout_after_customer_details', 10, 1 );
	function tuturn_checkout_after_customer_details() {
        global $product,$woocommerce,$current_user;
		$cart_data = WC()->session->get( 'cart', null );
		$userType 	        = apply_filters('tuturnGetUserType', $current_user->ID );

		/* delete transient */
		delete_transient( 'tu_booked_appointment_data' );
        if( !empty( $cart_data ) ) {
			foreach( $cart_data as $key => $cart_items ){
                if( !empty( $cart_items['payment_type'] )  && $cart_items['payment_type'] == 'package' ) {
                    $product_id		= !empty($cart_items['package_id']) ? $cart_items['package_id'] : 0;
                    $package		= wc_get_product($product_id); ?>
                    <div class="tu-haslayout">
                        <div class="cart-data-wrap">
                            <h3><?php esc_html_e('Summary','tuturn');?></h3>
                            <div class="selection-wrap">
								<?php tuturn_get_template_part('dashboard/'.$userType.'/package', 'item',array('package_id'=>$product_id,'buy_btn' => ''));?>
                            </div>
                        </div>
                    </div>
                <?php
                } else if( !empty( $cart_items['payment_type'] )  && $cart_items['payment_type'] == 'booking' ) {
                    $booked_data		= !empty($cart_items['cart_data']['booked_data']) ? $cart_items['cart_data']['booked_data'] : array();
                    ?>
                    <div class="tu-haslayout">
                        <div class="cart-data-wrap">
                            <h3><?php esc_html_e('Summary','tuturn');?></h3>
                            <div class="selection-wrap">
                            <?php tuturn_get_template_part('dashboard/dashboard', 'booking-details',array('booked_data'=>$booked_data));?>
                            </div>
                        </div>
                    </div>
                <?php
                }
            }
        }
    }
}


/**
 * @Rename Menu
 * @return {}
 */
if (!function_exists('tuturn_rename_admin_menus')) {
	add_action( 'admin_menu', 'tuturn_rename_admin_menus');
	function tuturn_rename_admin_menus() {
		global $menu,$submenu;

		if(!empty( $menu )){
			foreach( $menu as $key => $menu_item ) {

				if( $menu_item[2] == 'edit.php?post_type=tuturn-instructor' ){
					$menu[$key][0] = esc_html__('Tuturn','tuturn');
				}
			}
		}

		if(taxonomy_exists('faq_categories')){
			add_submenu_page(
				'edit.php?post_type=tuturn-instructor', 
				esc_html__('FAQ categories','tuturn'), 
				esc_html__('FAQ categories','tuturn'), 
				'manage_options', 
				'edit-tags.php?taxonomy=faq_categories&post_type=tuturn-instructor'
			);
		}

		if(taxonomy_exists('relations')){
			add_submenu_page(
				'edit.php?post_type=tuturn-instructor', 
				esc_html__('Relations','tuturn'), 
				esc_html__('Relations','tuturn'), 
				'manage_options', 
				'edit-tags.php?taxonomy=relations&post_type=tuturn-instructor'
			);
		}
		
        $fw_active_extensions   = get_option( 'fw_active_extensions' );
        $backups_demo           = !empty($fw_active_extensions) && isset($fw_active_extensions['backups-demo']) ? true : false;
        
		if( defined('FW') && !empty($backups_demo) ){
            add_submenu_page(
                'edit.php?post_type=tuturn-instructor', 
                esc_html__('Demo content install','tuturn'), 
                esc_html__('Demo content install','tuturn'), 
                'manage_options',
                'tools.php?page=fw-backups-demo-content'
            );
        }

        add_submenu_page(
            'edit.php?post_type=tuturn-instructor', 
            esc_html__('Import user','tuturn'), 
            esc_html__('Import user','tuturn'), 
            'manage_options', 
            'import_users',
            'tuturn_import_users_template'
        );        
    }
}


/**
 * Demo content unyson import path
 *
 * @since    1.0.0
*/
if (!function_exists('tuturn_filter_plugin_fw_ext_backups_demos')) {
	add_filter( 'fw_ext_backups_demo_dirs', 'tuturn_filter_plugin_fw_ext_backups_demos');
	function tuturn_filter_plugin_fw_ext_backups_demos($demo_path	= array()){
		if (!defined('FW')) return $demo_path;
		$demo_path	= array(
			fw_fix_path(TUTURN_DIRECTORY) .'/demo-content'
			=>
			TUTURN_DIRECTORY_URI .'demo-content',
		);		
		return $demo_path;
	}
}

/**
 * @init            Bulk import Users
 * @package         Tuturn
 * @subpackage      Tuturn/Public/Partials
 * @since           1.0
 */
if (!function_exists('tuturn_import_users_template')) {
	function  tuturn_import_users_template(){
		$permalink = add_query_arg(
			array(
				'&type=file',
			)
		);

		//Import users via file
		if ( !empty( $_FILES['users_csv']['tmp_name'] ) ) {
			$import_user	= new TuturnImportUser();
			$import_user->Tuturn_import_user();
			?>
			<div class="notice notice-success is-dismissible">
				<p><?php esc_html_e('User imported successfully','tuturn');?></p>
			</div>
			<?php
		}
	   ?>
       <h3 class="theme-name"><?php esc_html_e('Import instructors/students','tuturn');?></h3>
       <div id="import-users" class="import-users">
            <div class="theme-screenshot">
                <img alt="<?php esc_attr_e('Import Users','tuturn');?>" src="<?php echo esc_url(TUTURN_DIRECTORY_URI . 'public/images/users.jpg');?>">
            </div>
			<h3 class="theme-name"><?php esc_html_e('Import users','tuturn');?></h3>
            <div class="user-actions">
                <a href="javascript:void(0);"  class="button button-primary doc-import-users"><?php esc_html_e('Import dummy','tuturn');?></a>
            </div>
	   </div>
       <div id="import-users" class="import-users custom-import">
            <form method="post" action="<?php echo tuturn_prepare_final_url('file','import_users'); ?>"  enctype="multipart/form-data">
				<div class="theme-screenshot">
					<img alt="<?php esc_attr_e('Import users','tuturn');?>" src="<?php echo esc_url(TUTURN_DIRECTORY_URI . 'public/images/excel.jpg');?>">
				</div>
				<h3 class="theme-name">
					<input id="upload-dummy-csv" type="file" name="users_csv" >
					<label for="upload-dummy-csv" class="button button-primary upload-dummy-csv"><?php esc_html_e('Choose file','tuturn');?></lable>
				</h3>
				<div class="user-actions">
					<input type="submit" class="button button-primary" value="<?php esc_attr_e('Import from file','tuturn');?>">
				</div>
            </form>
		</div>
        <?php
	}
}

/**
 * @init            tab url
 * @package         Tuturn
 * @subpackage      Tuturn/Public/Partials
 * @since           1.0
 * @desc            Display The Tab System URL
 */
if (!function_exists('tuturn_prepare_final_url')) {
    function tuturn_prepare_final_url($tab='',$page='import_users') {
		$permalink = '';
		$permalink = add_query_arg(
			array(
				'?page'	=>   urlencode( $page ) ,
				'tab'	=>   urlencode( $tab ) ,
			)
		);
		return esc_url( $permalink );
	}
}

/**
 * @init            Import user
 * @package         Tuturn
 * @subpackage      Tuturn/Public/Partials
 * @since           1.0
 */
if (!function_exists('tuturn_import_users')) {
	function  tuturn_import_users(){
		$import_user	= new TuturnImportUser();
		$import_user->tuturn_import_user();
		//security check
		$do_check = check_ajax_referer('ajax_nonce', 'security', false);

		$json	= array();
		if ( $do_check == false ) {
			$json['type'] = 'error';
			$json['title'] = esc_html__('Failed!','tuturn' );
			$json['message'] = esc_html__('Security check failed, this could be because of your browser cache. Please clear the cache and check it again', 'tuturn');
			wp_send_json( $json );
		}

		$json['type']		= 'success';
		$json['title']		= esc_html__('Success!','tuturn' );
		$json['message']	= esc_html__('Users have been imported successfully','tuturn' );
		wp_send_json( $json );
	}
	add_action('wp_ajax_tuturn_import_users', 'tuturn_import_users');
}

/**
 * @ User profile fields
 * 
 */
if (!function_exists('tuturn_custom_user_profile_fields')) {
	function tuturn_custom_user_profile_fields($user){?>
		<h3><?php esc_html_e('Extra profile information','tuturn');?></h3>
		<table class="form-table">
			<tr>
				<th><label for="company"><?php esc_html_e('User type','tuturn');?></label></th>
				<td>
					<select name="registration[user_type]" id="tuturn-type">
						<option value=""><?php esc_html_e('Select user type','tuturn');?></option>		
						<option value="instructor"><?php esc_html_e('Instructor','tuturn');?></option>		
						<option value="student"><?php esc_html_e('Student','tuturn');?></option>
				   </select><br>
					<span class="description"><?php esc_html_e('User role should be subscriber to create user type post','tuturn');?></span>
				</td>
			</tr>
		</table>
	  <?php
	}
	add_action( "user_new_form", "tuturn_custom_user_profile_fields" );
}

/**
 * @User profile fields
 *  on edit screen
 */
if(!function_exists('tuturn_custom_user_type_profile_fields')){
	function tuturn_custom_user_type_profile_fields( $user ){
		$user_id	= !empty($user->ID) ? $user->ID : 0;

		if(!empty($user_id)){
			$profile_id			= tuturn_get_linked_profile_id( $user_id );
			$profile_status		= get_post_status ( $profile_id );

			if(empty($profile_status)){?>
				<h3><?php esc_html_e('Extra profile information','tuturn');?></h3>
				<table class="form-table">
					<tr>
						<th><label for="company"><?php esc_html_e('User type','tuturn');?></label></th>
						<td>
							<select name="registration[user_type]" id="tuturn-type">
								<option value=""><?php esc_html_e('Choose user type','tuturn');?></option>
								<option value="instructor"><?php esc_html_e('Instructor','tuturn');?></option>		
								<option value="student"><?php esc_html_e('Student','tuturn');?></option>
							</select><br>
							<span class="description"><?php esc_html_e('User role should be subscriber to create user type post','tuturn');?></span>
						</td>
					</tr>
				</table>
				<?php
			}
		}
	}
	add_action( 'show_user_profile', 'tuturn_custom_user_type_profile_fields' );
	add_action( 'edit_user_profile', 'tuturn_custom_user_type_profile_fields' );
}

/**
 * @Update user profile fields
 *  
 */
if(!function_exists('tuturn_update_user_type_profile_fields')){
	function tuturn_update_user_type_profile_fields( $user_id  ){
		/* Check to see if user can edit this profile */
		if ( !current_user_can( 'edit_user', $user_id ) || empty($user_id) ){
			return false;
		}

		if( !empty( $user_id ) ) {
			$user_meta			= get_userdata($user_id);
			$title				= $user_meta->first_name.' '.$user_meta->last_name;
			$post_type			= !empty($_POST['tuturn_user_type']) ? sanitize_text_field($_POST['tuturn_user_type']) : '';
			$linked_profile   	= tuturn_get_linked_profile_id( $user_id );			
			$profile_status		= get_post_status ( $linked_profile );			

			if(empty($profile_status)){
				if( !empty($post_type) && ( $post_type === 'tuturn-student' || $post_type	=== 'tuturn-instructor' ) ){
					$post_data	= array(
						'post_title'	=> wp_strip_all_tags($title),
						'post_author'	=> $user_id,
						'post_status'   => 'publish',
						'post_type'		=> $post_type,
					);

					$post_id	= wp_insert_post( $post_data );
					
					if( !empty( $post_id ) ) {
						/* Update user linked profile */
						update_post_meta($post_id, '_linked_profile',intval($user_id));
						update_user_meta( $user_id, '_linked_profile', $post_id );

						if( !empty($post_type) && ( $post_type === 'tuturn-student' ) ){
							update_user_meta( $user_id, '_user_type', 'student' );
							do_action('tuturn_student_profile_crate', $post_id, $user_id);
						}else{
							update_user_meta( $user_id, '_user_type', 'instructor' );
							do_action('tuturn_instructor_profile_crate', $post_id, $user_id);							
						}

						update_post_meta( $post_id, '_is_verified', 'yes' );
						/* add extra fields as a null */
						update_post_meta($post_id, '_tag_line', '');
						update_post_meta($post_id, '_address', '');
						update_post_meta($post_id, '_latitude', 0.0);
						update_post_meta($post_id, '_longitude', 0.0);
					}
				}
			}
		}
	}
	add_action( 'personal_options_update', 'tuturn_update_user_type_profile_fields' );
	add_action( 'edit_user_profile_update', 'tuturn_update_user_type_profile_fields' );
}

/**
 * @Create profile from admin create user
 * @type create
 */
if (!function_exists('tuturn_create_wp_user')) {
	//add_action( 'user_register', 'tuturn_create_wp_user',10,2 );
    function tuturn_create_wp_user($user_id,$userdata) {
		global $tuturn_settings;
        $shortname_option  =  !empty($tuturn_settings['shortname_option']) ? $tuturn_settings['shortname_option'] : '';

		if( !empty( $user_id )  ) {
			$user_data_set	= get_userdata($user_id);
            $roles		    = !empty($user_data_set->roles) ? $user_data_set->roles : '';
            $email		    = !empty($user_data_set->user_email) ? $user_data_set->user_email : '';

			$linked_profile   	= tuturn_get_linked_profile_id($user_id);
			if(!empty( $linked_profile )){
				if ( 'publish' == get_post_status( $linked_profile ) ) {
					return true;
				}
			}

			if( !empty($roles) && in_array('subscriber',$roles)){
				$user_type          = !empty($_REQUEST['registration']['user_type']) ? $_REQUEST['registration']['user_type'] : '';
                $post_data          = !empty($_REQUEST['data']) ? $_REQUEST['data'] : array();

				$first_name          = !empty($_REQUEST['registration']['fname']) ? $_REQUEST['registration']['fname'] : '';
				$last_name           = !empty($_REQUEST['registration']['lname']) ? $_REQUEST['registration']['lname'] : '';
				$phone_number        = !empty($_REQUEST['registration']['phone_number']) ? $_REQUEST['registration']['phone_number'] : '';

                if(empty($user_type) && !empty($post_data) ){
					if( !empty($post_data) && is_array($post_data)){
						$user_type   	= !empty($post_data['user_type']) ? $post_data['user_type'] : 'student';
						$first_name   	= !empty($post_data['fname']) ? $post_data['fname'] : '';
						$last_name   	= !empty($post_data['lname']) ? $post_data['lname'] : '';
						$phone_number        = !empty($post_data['phone_number']) ? $post_data['phone_number'] : '';
					} else {
						parse_str($post_data, $output);
						$user_type   	= !empty($output['registration']['user_type']) ? $output['registration']['user_type'] : 'student';
						$first_name   	= !empty($output['registration']['fname']) ? $output['registration']['fname'] : '';
						$last_name   	= !empty($output['registration']['lname']) ? $output['registration']['lname'] : '';
						$phone_number        = !empty($output['registration']['phone_number']) ? $output['registration']['phone_number'] : '';
					}
                    
                }

				if(!empty($userdata['first_name'])){
					$first_name          = !empty($userdata['first_name']) ? $userdata['first_name'] : '';
				}

				if(!empty($userdata['last_name'])){
					$last_name          = !empty($userdata['last_name']) ? $userdata['last_name'] : '';
				}

                //If no role is assigned then assign default role
                if(empty($user_type )){
                    $user_type       = !empty($tuturn_settings['defult_register_type']) ? $tuturn_settings['defult_register_type'] : 'student';
                }
				
				$display_name   =  $first_name .  " " . $last_name;
                $display_name   = !empty($userdata['display_name']) ? $userdata['display_name'] : $display_name;

				update_user_meta($user_id, 'first_name', $first_name);
				update_user_meta($user_id, 'last_name', $last_name);
				update_user_meta($user_id, '_user_type', $user_type );
				update_user_meta($user_id, 'termsconditions', true);
				update_user_meta($user_id, 'show_admin_bar_front', false);
				update_user_meta($user_id, '_is_verified', 'no');
				update_user_meta($user_id, 'identity_verified', 0);

				$verify_link = '';
				$verify_new_user = !empty($tuturn_settings['email_user_registration']) ? $tuturn_settings['email_user_registration'] : '';

				if (!empty($verify_new_user) && $verify_new_user == 'verify_by_link' && empty($tuturn_settings['user_account_approve'])) {
					/* verification link */
					$key_hash     = md5(uniqid(openssl_random_pseudo_bytes(32)));
					update_user_meta($user_id, 'confirmation_key', $key_hash);
					$protocol     = is_ssl() ? 'https' : 'http';
					$verify_link  = esc_url(add_query_arg(array('key' => $key_hash . '&verifyemail=' . $email), home_url('/', $protocol)));
				}

				$post_type = ($user_type == 'instructor') ? 'tuturn-instructor' : 'tuturn-student';

				$user_post = array(
					'post_title'    => wp_strip_all_tags($display_name),
					'post_status'   => 'publish',
					'post_author'   => $user_id,
					'post_type'     => apply_filters('tuturn_profiles_post_type_name', $post_type),
				);

				$post_id = wp_insert_post($user_post);

				if (!is_wp_error($post_id)) {
					$dir_latitude       = !empty($tuturn_settings['dir_latitude']) ? $tuturn_settings['dir_latitude'] : 0.0;
					$dir_longitude      = !empty($tuturn_settings['dir_longitude']) ? $tuturn_settings['dir_longitude'] : 0.0;
					$verify_user        = !empty($tuturn_settings['user_account_approve']) ? $tuturn_settings['user_account_approve'] : '';
					$post_meta          = array();
					
					//add extra fields as a null
					update_post_meta($post_id, '_tag_line', '');
					update_post_meta($post_id, '_address', '');
					update_post_meta($post_id, 'hourly_rate', 0);
					update_post_meta($post_id, '_latitude', $dir_latitude);
					update_post_meta($post_id, '_longitude', $dir_longitude);
					update_post_meta($post_id, '_linked_profile', $user_id);
					update_post_meta($post_id, '_is_verified', 'no');
					update_user_meta($user_id, '_linked_profile', $post_id );
					
					$post_meta['first_name']    = $first_name;
					$post_meta['last_name']     = $last_name;
					$post_meta['contact_info']['phone']     = $phone_number;
					$post_meta['name']          = wp_strip_all_tags($display_name);
					$post_meta['tagline']       = '';

					update_post_meta($post_id, 'profile_details', $post_meta);
					
					//Update slug
					if(!empty($shortname_option) && $shortname_option == 'yes'){
						$user_name  = tuturn_get_username($post_id);
						$post_name_update = array(
							'ID'            => intval($post_id),
							'post_name'    => sanitize_title($user_name)
						);
						wp_update_post( $post_name_update );
					}

					/* account approved */
					if(!empty($tuturn_settings['user_account_approve'])){
						update_user_meta($user_id, '_is_verified', 'yes');
						update_post_meta($post_id, '_is_verified', 'yes');
					}

					/* identity verification approved */
					$identity_verification          = !empty($tuturn_settings['identity_verification']) ? $tuturn_settings['identity_verification'] : '';
					update_user_meta($user_id, 'identity_verified', 1);
					update_post_meta($post_id,'identity_verified','yes');
					
					if(!empty($post_type) && $post_type === 'tuturn-student' && ($identity_verification === 'students' || $identity_verification === 'both')){
						update_user_meta($user_id, 'identity_verified', 0);
						update_post_meta($post_id,'identity_verified','no');
					}
					
					if(!empty($post_type) && ($post_type === 'tuturn-instructor') && ($identity_verification === 'tutors' || $identity_verification === 'both')){
						update_user_meta($user_id, 'identity_verified', 0);
						update_post_meta($post_id,'identity_verified','no');
					}
					
					$login_url    = !empty( $tuturn_settings['tpl_login'] ) ? get_permalink($tuturn_settings['tpl_login']) : wp_login_url();

					/* Send email to users & admin */
					if (class_exists('Tuturn_Email_Helper')) {
						$blogname                       = get_option('blogname');
						$emailData                      = array();
						$emailData['name']              = $display_name;
						$emailData['email']             = $email;
						$emailData['verification_link'] = $verify_link;
						$emailData['site']              = $blogname;
						$emailData['login_url']         = $login_url;
						
						/* Welcome Email */
						if (class_exists('TuturnRegistrationEmail')) {
							$email_helper = new TuturnRegistrationEmail();

							if(!empty($tuturn_settings['user_account_approve'])){
								//email to user on auto approve
								$email_helper->registration_user_auto_approve_email($emailData);
							} elseif (!empty($verify_new_user) && $verify_new_user == 'verify_by_link' && empty($tuturn_settings['user_account_approve'])) {
								//email to user verify by link
								$email_helper->registration_user_email($emailData);
							} else{
								/* to user approved by admin */
								$email_helper->registration_account_approval_request($emailData);
								/* to admin if verify by admin */
								$email_helper->registration_verify_by_admin_email($emailData);
							}
							
							$email_helper->new_user_register_admin_email($emailData);
						}
					}
				}
			} 
		}
	}
}

/**
 * Register packages product type
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if (class_exists('WC_Product') && !function_exists('register_packages_product_type')) {
    function register_packages_product_type()
    {
        class WC_Product_Packages extends WC_Product
        {
            public function __construct($product)
            {
                $this->product_type = 'packages';
                parent::__construct($product);
            }

            public function get_type()
            {
                return 'packages';
            }
        }
    }
    add_action('init', 'register_packages_product_type');
}

/**
 * Register service product type
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if (class_exists('WC_Product') && !function_exists('register_services_product_type')) {
    function register_services_product_type()
    {
        class WC_Product_Service extends WC_Product
        {
            public function __construct($product)
            {
                $this->product_type = 'service';
                parent::__construct($product);
            }

            public function get_type()
            {
                return 'service';
            }
        }
    }
    add_action('init', 'register_services_product_type');
}

/**
 * geocode info
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */

if(!function_exists('tuturn_process_geocode_info')) {
    function tuturn_process_geocode_info ( $zipcode = "", $country_region = "" ) {
        global $tuturn_settings;
        $json			=  array();
        $google_key 	= !empty($tuturn_settings['google_map']) ? $tuturn_settings['google_map'] : '';
		
		if( empty($google_key)){
			$json['type'] = 'api_key_error';
            return $json;
		}
		
        $geo_request 	= wp_remote_get( 'https://maps.googleapis.com/maps/api/geocode/json?address='.$zipcode.'&region='.$country_region.'&key='.$google_key );

		if( is_wp_error( $geo_request ) ) {
			$json['type'] = 'error';
            return $json;
        }
		
        $body = wp_remote_retrieve_body( $geo_request );

        if($body) {
			$response	= json_decode($body, true);
			if ($response['status'] == 'OK') {
                $json['type']       = 'success';
                $json['message'] 	= esc_html__("Geo zip code data successfully found", 'tuturn');
                $json['geo_data']   = $response['results'][0];
            } else {
				$json['type'] 		= 'error';
				$json['message'] 	= !empty($response['error_message']) ? $response['error_message'] : esc_html__("Please make sure if you have setup your google map API key and required API are enabled. Also google require the billing should be enabled", 'tuturn');
            }
			return $json;
        }
    }

	add_filter('tuturn_process_geocode_info', 'tuturn_process_geocode_info', 10, 2);
}

/**
 * Get woocommmerce currency settings
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if (!function_exists('tuturn_get_current_currency')) {
    function tuturn_get_current_currency()
    {
        $currency  = array();
        if (class_exists('WooCommerce')) {
            $currency['code']  = get_woocommerce_currency();
            $currency['symbol']  = get_woocommerce_currency_symbol();
        } else {
            $currency['code']  = 'USD';
            $currency['symbol']  = '$';
        }
        return $currency;
    }
}


/**
 * Custom user menu
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if (!function_exists('tuturn_custom_user_menu')) {
    function tuturn_custom_user_menu($items='', $args='')
    {
        $term_id = !empty($args->menu->term_id) ? intval($args->menu->term_id) : 0;
		$header_menu	= get_term_meta($term_id, 'main_header_menu', true);

		if (!empty($header_menu) && $header_menu == 'yes') {
			if (is_user_logged_in()) {
				$items .= apply_filters('tuturn_process_user_profile_menu', $items, $term_id);
			} else {
				$login = tuturn_get_page_uri('login');
				$items .= '<div class="tu-navbarbtn"><a href="'.esc_url($login).'" class="tu-btn tu-login">'.esc_html__('Get started','tuturn').'</a></div>';
			}
		}
        return $items;
    }
	add_filter('wp_nav_menu', 'tuturn_custom_user_menu', 10, 2);
}

/**
 * Dashboard menu in case of 
 * Elementor header menu use
 */
if (!function_exists('tuturn_user_menu_elementor_case')) {
	add_filter('wp_nav_menu_items', 'tuturn_user_menu_elementor_case', 10, 2);
	function tuturn_user_menu_elementor_case($items, $args)
	{
		$term_id = !empty($args->menu->term_id) ? intval($args->menu->term_id) : 0;
		if (empty($term_id) && !empty($args->menu)) {
			$menudata = wp_get_nav_menu_object($args->menu);
			if (!empty($menudata->term_id)) {
				$term_id = $menudata->term_id;
			}
		}

		$header_menu	= get_term_meta($term_id, 'elementor_header_menu', true);

		if (!empty($header_menu) && $header_menu == 'yes') {
			if (is_user_logged_in()) {
				$items .= '<li class="tu-elementor-dash-menu">';
				$items .= apply_filters('tuturn_process_user_profile_menu', $items, $term_id);
				$items .= '</li>';
			}
		}

		return $items;
	}
}

/**
 * Get user menu details
 * @return
 */
if (!function_exists('tuturn_login_user_menu_details')) {
    function tuturn_login_user_menu_details($items='', $term_id='')
    {
        global $current_user;
        ob_start();
        $tuturn_profile_menu_list  = Tuturn_Profile_Menu::tuturn_get_dashboard_profile_menu();
        $sortorder                  = array_column($tuturn_profile_menu_list, 'sortorder');
        array_multisort($sortorder, SORT_ASC, $tuturn_profile_menu_list);
        $user_identity              = intval($current_user->ID);
		$tuturn_user_role			= apply_filters('tuturnGetUserType', $user_identity );
        $user_profile_id            = tuturn_get_linked_profile_id($current_user->ID, '', $tuturn_user_role);
		$user_name                  = tuturn_get_username($user_profile_id);
		if (is_user_logged_in()) {
			if (!empty($tuturn_user_role) && ($tuturn_user_role === 'instructor' || $tuturn_user_role === 'student') ) {
				?>
				<div class="tu-headerwrap__right">
					<div class="tu-navbarbtn sub-menu-holder">
						<a href="javascript:void(0);" id="profile-avatar-menue-icon" class="tu-nav-signin">
							<?php Tuturn_Profile_Menu::tuturn_get_avatar(); ?>
						</a>				
						<ul class="sub-menu">
							<?php
							if (!empty($tuturn_profile_menu_list)) {
								foreach ($tuturn_profile_menu_list as $key => $menu_item) {
									if (!empty($menu_item['type']) && ($menu_item['type'] == $tuturn_user_role || $menu_item['type'] == 'none')) {
										$menu_item['id'] = $key;
										tuturn_get_template_part('dashboard/menus/menu', 'avatar-items', $menu_item);
									}
								}
							}							
							?>
						</ul>
					</div>
				</div>
				<?php
			}
		} else {
			$login = tuturn_get_page_uri('login');
			?>
			<div class="tu-navbarbtn"><a href="<?php echo esc_url($login);?>" class="tu-btn tu-login"><?php esc_html_e('Get started','tuturn');?></a></div>
			<?php
		} ?>
		<?php
        return ob_get_clean();
    }
	add_filter('tuturn_process_user_profile_menu', 'tuturn_login_user_menu_details', 10, 2);
}

/**
 * Dashboard guppy inbox URL
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if ( !function_exists( 'tuturn_guppy_inbox_url' ) ) {
	function tuturn_guppy_inbox_url( $friend_id = '' ) {
		if( apply_filters( 'tuturn_chat_solution_guppy',false ) === true ){
			$inbox_url	= tuturn_get_page_uri('inbox');
			if(!empty($friend_id)){				
				$inbox_url	= add_query_arg(
					array(
						'chat_type'	=> 1,
						'chat_id'	=> $friend_id.'_'.'1',
						'type'	    => 'messanger',
					),
					$inbox_url
				);
				
			}
			return esc_url($inbox_url);
		}
	}
	add_filter( 'tuturn_guppy_inbox_url', 'tuturn_guppy_inbox_url');
}


/**
 * Advance Wild card search for taxonomy
 * $Where
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if( !function_exists( 'tuturn_advance_search_where_instructors' ) ) {
	function tuturn_advance_search_where_instructors($where){
        global $wpdb;
        $exact_match = false;
		$keyword 		= !empty( $_GET['keyword']) ? esc_html($_GET['keyword']) : '';
		$available_time = !empty( $_GET['available_time']) ? ($_GET['available_time']) : array();
        $keyword		= esc_sql($wpdb->esc_like($keyword)); 
		
        if($exact_match) {
            $where  .= " AND (( p1.meta_key = 'profile_details' AND p1.meta_value LIKE '".$keyword."' ) OR ({$wpdb->posts}.post_title LIKE '".$keyword."') OR ({$wpdb->posts}.post_content LIKE '".$keyword."') OR ({$wpdb->posts}.post_excerpt LIKE '".$keyword."'))"; 
        }else {
            $where  .= " AND (( p1.meta_key = 'profile_details' AND p1.meta_value LIKE '%".$keyword."%' ) OR ({$wpdb->posts}.post_title LIKE '%".$keyword."%') OR ({$wpdb->posts}.post_content LIKE '%".$keyword."%') OR ({$wpdb->posts}.post_excerpt LIKE '%".$keyword."%'))"; 
        }

        return $where;
	}	
}

/**
 * add mac images mime type
 * $Where
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if( !function_exists( 'tuturn_add_mac_images_ext' ) ) {
	add_filter('upload_mimes','tuturn_add_mac_images_ext',10,2);
	function tuturn_add_mac_images_ext($list=array(),$user=array()){
		$list['jfif']	= 'image/jfif';
		$list['heic']	= 'image/heic';
		return $list;
	}
}

if( !function_exists( 'tuturn_advance_search_available_time' ) ) {
	function tuturn_advance_search_available_time($where){
        global $wpdb;
		$available_time 	= !empty( $_GET['available_time']) ? ($_GET['available_time']) : array();
		$weekdays			= !empty( $_GET['available_days']) ? ($_GET['available_days']) : array();
		
		if(!empty($available_time) ){

			$where .= ' AND (';
			$counter = 0;
			$weekdays_counter = 0;
			foreach($available_time as $time){

				$condition = '';
				if($counter>0){
					$condition = 'OR';
				}

				$week_count	= !empty($weekdays) && is_array($weekdays) ? count($weekdays) : 0;
				if( !empty($weekdays) ){
					
					foreach($weekdays as $key){
						if( !empty($week_count) && $week_count > 1 ){
							if($weekdays_counter>0){
								$condition = 'OR';
							}
							$where  .= " $condition ( p1.meta_key = '".$key."' AND p1.meta_value LIKE '%".$time."%') "; 
						} else {
							if($weekdays_counter>0){
								$condition = 'OR';
							}
							$where  .= " $condition p1.meta_key = '".$key."' AND p1.meta_value LIKE '%".$time."%' "; 
						}
						$weekdays_counter++;
					}
				} else{
					if($counter>0){
						$condition = 'OR';
					}
					$where  .= " $condition  p1.meta_key = 'profile_details' AND p1.meta_value LIKE '%".$time."%' ";
				}
				$counter++;
			}

			$where .= ')';
		}

        return $where;
	}	
}

/**
 * Advance Wild card search for taxonomy
 * $join
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if( !function_exists( 'tuturn_advance_search_join' ) ) {
	function tuturn_advance_search_join($join){
        global $wpdb;
        $join .=" INNER JOIN {$wpdb->postmeta} p1 ON {$wpdb->posts}.ID= p1.post_id ";
		return $join;
	}
}

/**
 * Advance Wild card search for taxonomy
 * $groupby
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if( !function_exists( 'tuturn_advance_search_groupby' ) ) {
	function tuturn_advance_search_groupby($groupby){
		global $wpdb;

		// we need to group on post ID
		$groupby_id = "{$wpdb->posts}.ID";
		if(!is_search() || strpos($groupby, $groupby_id) !== false) return $groupby;

		// groupby was empty, use ours
		if(!strlen(trim($groupby))) return $groupby_id;

		// wasn't empty, append ours
		return $groupby.", ".$groupby_id;
	}
}

/**
 * Buy package notification
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if ( !function_exists( 'tuturn_profile_settings_buy_package_notice' ) ) {
	function tuturn_profile_settings_buy_package_notice($args=array()) {
		global $tuturn_settings;
		if (!empty($args) && is_array($args)) {
			extract($args);
		}
		$package_url	= tuturn_get_page_uri('package_page');		
		$sub_title	= !empty($tuturn_settings['pkg_page_sub_title']) ? $tuturn_settings['pkg_page_sub_title'] : '';
		$details	= !empty($tuturn_settings['pkg_page_details']) ? $tuturn_settings['pkg_page_details'] : '';
		if($userType == 'instructor' && empty($args['package_info']['allowed'])){?>
			<div class="tu-boxitem">
				<div class="tu-alertcontent">
					<?php if( !empty($sub_title) ){?>
						<h4><?php echo esc_html($sub_title) ?></h4>
					<?php } ?>
					<?php if( !empty($details) ){?>
						<p> <?php echo do_shortcode($details); ?></p>
					<?php } ?>
				</div>
				<div class="tu-btnrea">
					<a href="<?php echo esc_url($package_url);?>" class="tu-primbtn tu-btngreen"><span><?php esc_html_e('Buy a new package', 'tuturn');?></span><i class="icon icon-lock"></i></a>
				</div>
			</div>
			<?php
		}
	}
	add_action( 'tuturn_profile_settings_notice', 'tuturn_profile_settings_buy_package_notice');
}

/**
 * Identity verification notification
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if ( !function_exists( 'tuturn_profile_identity_verification_notice' ) ) {
	function tuturn_profile_identity_verification_notice($args=array()) {
		global $tuturn_settings,$current_user;
		if (!empty($args) && is_array($args)) {
			extract($args);
		}

		$identity_verified  	    = get_user_meta($user_identity, 'identity_verified', true);
		$identity_verified		    = !empty($identity_verified) ? $identity_verified : 0;

		$identity_verification  = !empty($tuturn_settings['identity_verification']) ? $tuturn_settings['identity_verification'] : 'none';
		$student_fields     	= !empty($tuturn_settings['student_fields']) ? $tuturn_settings['student_fields'] : array();

		if( !empty($identity_verification) && ($identity_verification != 'none') ){
			$userType 	        = apply_filters('tuturnGetUserType', $current_user->ID);
			$args = array(
				'posts_per_page' => -1,
				'post_type'      => 'user-verification',
				'post_status'    => 'any',
				'author__in'     => $user_identity,
			);

			$user_verfication_list  = new WP_Query($args);
			$total_posts            = $user_verfication_list->found_posts;
				if(!empty($userType ) && $userType === 'student'){
					$parental_consent  = !empty($tuturn_settings['parental_consent']) ? $tuturn_settings['parental_consent'] : 'no';

					if($identity_verification != 'students' && $identity_verification != 'both' && $identity_verification != 'none' ){
						return;
					}

					if(empty($identity_verified) && empty($total_posts) ){
						$title      = esc_html__('Verification required','tuturn');
						if($parental_consent === 'yes' && in_array('parent_email', $student_fields)){
							$content    = esc_html__('ID Verification with parental consent is required for safety measures. It is not visible to the public. Please check the parent email to complete Parental Consent after filling this.','tuturn');
						} else {
							$content	= esc_html__('You must verify your identity, please submit the required documents to get verified.', 'tuturn');
						}
						do_action( 'tuturn_alert_notification', $title,$content);
					} elseif( empty($identity_verified) && !empty($total_posts)){
						$title      = esc_html__('Woohoo!','tuturn');
						if($parental_consent === 'yes' && in_array('parent_email', $student_fields)){
							$content    = esc_html__('Thank you so much for submitting the verification documents. We have received your information, upon the parents confirmation, your account will be approved.','tuturn');
						} else {
							$content	= esc_html__('Thank you so much for submitting the verification documents. Buckle up We will verify and respond to your request very soon.', 'tuturn');
						}
						do_action( 'tuturn_alert_notification', $title,$content,'tu-alertitembg tu-document-cancelled','',$user_identity);
					} elseif( !empty($identity_verified) && $identity_verified === '1' ){
						$title      = esc_html__('Woohoo!','tuturn');
						$content    = esc_html__("We have successfully completed your indentity verification. you’re now ready to use full features.","tuturn");
						do_action( 'tuturn_alert_notification', $title,$content,'alert alert-dismissible fade show');
					}

				} else {
					if(empty($identity_verified) && empty($total_posts) ){
						$title      = esc_html__('Verification required','tuturn');
						$content    = esc_html__('You must verify your identity, please submit the required documents to get verified. As soon as you will be verified then you will be able to get online appointments and other site features.','tuturn');
						do_action( 'tuturn_alert_notification', $title,$content);
					} elseif(empty($identity_verified) && !empty($total_posts)){
						$title      = esc_html__('Woohoo!','tuturn');
						$content    = esc_html__('You have successfully submitted your documents. Buckle up We will verify and respond to your request very soon.','tuturn');
						do_action( 'tuturn_alert_notification', $title,$content,'tu-alertitembg tu-document-cancelled','',$user_identity);
					} elseif( !empty($identity_verified) && $identity_verified === '1' ){
						$title      = esc_html__('Woohoo!','tuturn');
						$content    = esc_html__("We have successfully completed your indentity verification. you’re now ready to use full features.","tuturn");
						do_action( 'tuturn_alert_notification', $title,$content,'alert alert-dismissible fade show');
					}
				}
		}
			
	}
	add_action( 'tuturn_profile_identity_verification_notice', 'tuturn_profile_identity_verification_notice');
}

/**
 * Advance Wild card search for taxonomy
 * $Where
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if( !function_exists( 'tuturn_advance_search_where_instructor' ) ) {
	function tuturn_advance_search_where_instructor($where){
        global $wpdb;
        $exact_match = false;
		$keyword 		= !empty( $_GET['keyword']) ? esc_html($_GET['keyword']) : '';
        $keyword = esc_sql($wpdb->esc_like($keyword)); 
		
        if($exact_match) {
            $where  .= " AND (( p1.meta_key = 'profile_details' AND p1.meta_value LIKE '".$keyword."' ) OR ({$wpdb->posts}.post_title LIKE '".$keyword."') OR ({$wpdb->posts}.post_content LIKE '".$keyword."') OR ({$wpdb->posts}.post_excerpt LIKE '".$keyword."'))"; 
        }else {
            $where  .= " AND (( p1.meta_key = 'profile_details' AND p1.meta_value LIKE '%".$keyword."%' ) OR ({$wpdb->posts}.post_title LIKE '%".$keyword."%') OR ({$wpdb->posts}.post_content LIKE '%".$keyword."%') OR ({$wpdb->posts}.post_excerpt LIKE '%".$keyword."%'))"; 
        }
        return $where;
	}	
}

/**
 * get week days
 *
 * @since    1.0.0
*/
if(!function_exists('tuturnGetWeekDays')){
	function tuturnGetWeekDays(){
		$week_list = array(
			'monday' 	=> esc_html__('Mon', 'tuturn'),
			'tuesday' 	=> esc_html__('Tue', 'tuturn'),
			'wednesday' => esc_html__('Wed', 'tuturn'),
			'thursday' 	=> esc_html__('Thu', 'tuturn'),
			'friday' 	=> esc_html__('Fri', 'tuturn'),
			'saturday' 	=> esc_html__('Sat', 'tuturn'),
			'sunday' 	=> esc_html__('Sun', 'tuturn')
		);
		return apply_filters('tuturnWeekDays', $week_list);
	}
	add_filter('tuturnGetWeekDays', 'tuturnGetWeekDays');
}

/**
 * get appointment duration
 *
 * @since    1.0.0
*/
if(!function_exists('tuturnAppointmentDuration')){
	function tuturnAppointmentDuration(){
		$duration_list	= array(
			'5'		=> esc_html__('5 minutes','tuturn'),
			'10'	=> esc_html__('10 minutes','tuturn'),
			'15'	=> esc_html__('15 minutes','tuturn'),
			'20'	=> esc_html__('20 minutes','tuturn'),
			'30'	=> esc_html__('30 minutes','tuturn'),
			'45'	=> esc_html__('45 minutes','tuturn'),
			'60'	=> esc_html__('1 hours','tuturn'),
			'90'	=> esc_html__('1 hours, 30 minutes','tuturn'),
			'120'	=> esc_html__('2 hours','tuturn'),
			'180'	=> esc_html__('3 hours','tuturn'),
			'240'	=> esc_html__('4 hours','tuturn'),
			'300'	=> esc_html__('5 hours','tuturn'),
			'360'	=> esc_html__('6 hours','tuturn'),
			'420'	=> esc_html__('7 hours','tuturn'),
			'480'	=> esc_html__('8 hours','tuturn')
		);
		$duration_list 	= apply_filters('tuturn_filter_duration_time', $duration_list);
		return $duration_list;
	}
	add_filter('tuturnAppointmentDuration', 'tuturnAppointmentDuration');
}

/**
 * get appointment interval
 *
 * @since    1.0.0
*/
if(!function_exists('tuturnAppointmentInterval')){
	function tuturnAppointmentInterval(){
		$interval_list	= array(
			'0'		=> esc_html__('0 minutes','tuturn'),
			'5'		=> esc_html__('5 minutes','tuturn'),
			'10'	=> esc_html__('10 minutes','tuturn'),
			'15'	=> esc_html__('15 minutes','tuturn'),
			'20'	=> esc_html__('20 minutes','tuturn'),
			'30'	=> esc_html__('30 minutes','tuturn'),
			'45'	=> esc_html__('45 minutes','tuturn'),
			'60'	=> esc_html__('1 hours','tuturn'),
			'90'	=> esc_html__('1 hours, 30 minutes','tuturn'),
			'120'	=> esc_html__('2 hours','tuturn'),
		);

		$interval_list 	= apply_filters('tuturn_filter_interval_time',$interval_list);
		return $interval_list;
	}
	add_filter('tuturnAppointmentInterval', 'tuturnAppointmentInterval');
}

/**
 * get appointment time
 *
 * @since    1.0.0
*/
if(!function_exists('tuturnAppointmentTime')){
	function tuturnAppointmentTime(){
		global $tuturn_settings;
		$booking_interval	= !empty($tuturn_settings['booking_interval']) ? intval($tuturn_settings['booking_interval']) : 60;
		
		if( !empty($booking_interval) && $booking_interval != 60 ){
			$start_time     = new DateTime("00:00");
			$end_time       = new DateTime("24:00");
			$interval   	= DateInterval::createFromDateString($booking_interval.' min');
			$times   		= new DatePeriod($start_time, $interval, $end_time);
			if( !empty($times) ){
				$time_list	= array();
				foreach ($times as $time) {
					$time_list[$time->format('Hi')] = $time->format('h:i a');
				}
				$time_list['2400']	= esc_html__('11:59 pm','tuturn');
			}
		} else {
			$time_list	= array(		
				'0000'	=> esc_html__('12:00 am','tuturn'),
				'0100'	=> esc_html__('1:00 am','tuturn'),
				'0200'	=> esc_html__('2:00 am','tuturn'),
				'0300'	=> esc_html__('3:00 am','tuturn'),
				'0400'	=> esc_html__('4:00 am','tuturn'),
				'0500'	=> esc_html__('5:00 am','tuturn'),
				'0600'	=> esc_html__('6:00 am','tuturn'),
				'0700'	=> esc_html__('7:00 am','tuturn'),
				'0800'	=> esc_html__('8:00 am','tuturn'),
				'0900'	=> esc_html__('9:00 am','tuturn'),
				'1000'	=> esc_html__('10:00 am','tuturn'),
				'1100'	=> esc_html__('11:00 am','tuturn'),
				'1200'	=> esc_html__('12:00 pm','tuturn'),
				'1300'	=> esc_html__('1:00 pm','tuturn'),
				'1400'	=> esc_html__('2:00 pm','tuturn'),
				'1500'	=> esc_html__('3:00 pm','tuturn'),
				'1600'	=> esc_html__('4:00 pm','tuturn'),
				'1700'	=> esc_html__('5:00 pm','tuturn'),
				'1800'	=> esc_html__('6:00 pm','tuturn'),
				'1900'	=> esc_html__('7:00 pm','tuturn'),
				'2000'	=> esc_html__('8:00 pm','tuturn'),
				'2100'	=> esc_html__('9:00 pm','tuturn'),
				'2200'	=> esc_html__('10:00 pm','tuturn'),
				'2300'	=> esc_html__('11:00 pm','tuturn'),
				'2400'	=> esc_html__('12:00 pm (night)','tuturn')			
			);
		}
		$time_list 	= apply_filters('tuturn_filter_time',$time_list);
		return $time_list;
	}
	add_filter('tuturnAppointmentTime', 'tuturnAppointmentTime');
}


/**
 * update order query var
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if ( !function_exists( 'tuturn_custom_query_var' ) ) {
	function tuturn_custom_query_var( $query, $query_vars ) {
		if ( ! empty( $query_vars['student_id'] ) ) {
			$query['meta_query'][] = array(
				'key' 	=> 'student_id',
				'value' => intval( $query_vars['student_id'] ),
			);
		}

		if ( ! empty( $query_vars['instructor_id'] ) ) {
			$query['meta_query'][] = array(
				'key' 	=> 'instructor_id',
				'value' => intval( $query_vars['instructor_id'] ),
			);
		}

		if ( ! empty( $query_vars['payment_method'] ) && ($query_vars['payment_method'] == 'payoneer' || $query_vars['payment_method'] == 'stripe') ) {
			$query['meta_query'][] = array(
				'key' 	=> '_payment_method',
				'value' => esc_html( $query_vars['payment_method'] ),
			);
		}

		if ( ! empty( $query_vars['payment_type'] ) ) {
			$query['meta_query'][] = array(
				'key' 	=> 'payment_type',
				'value' => esc_html( $query_vars['payment_type'] ),
			);
		}

		if ( ! empty( $query_vars['booking_status'] ) ) {
			$query['meta_query'][] = array(
				'key' 	=> 'booking_status',
				'value' => esc_html( $query_vars['booking_status'] ),
			);
		}

		if ( ! empty( $query_vars['booking_date'] ) ) {
			$query['meta_query'][] = array(
				'key' 		=> '_booking_date',
				'value' 	=> esc_html( $query_vars['booking_date'] ),
				'compare'	=> 'LIKE'
			);
		}

		if ( ! empty( $query_vars['booking_service'] ) ) {
			$query['meta_query'][] = array(
				'key'		=> 'service_names',
				'value'		=> '"'.esc_html( $query_vars['booking_service'] ).'"',
				'compare'	=> 'LIKE'
			);
		}
		return $query;
	}
	add_filter( 'woocommerce_order_data_store_cpt_get_orders_query', 'tuturn_custom_query_var', 10, 2 );
}

/**
 * Dashboard booking status
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if ( !function_exists( 'tuturn_booking_status' ) ) {
	function tuturn_booking_status( $order = '' ) {
		if(!empty($order)){
			$status	= $order->get_status();
			switch ($status) {
				case "completed":
					$class	= " tu-taggreen";
					$label	= esc_html__('Completed', 'tuturn');			 
					break;
				case "pending":
					$class	= "";
					$label	= esc_html__('Pending', 'tuturn');
					break;		
				case "processing":
					$class	= "";
					$label	= esc_html__('Pending', 'tuturn');	
					break;	
				case "on-hold":
					$class	= " tu-tagdenied";
					$label	= esc_html__('Pending', 'tuturn');
					break;		
				case "cancelled":
					$class	= " tu-tagdenied";
					$label	= esc_html__('Declined', 'tuturn');		
					break;
				case "refunded":
					$class	= " tu-tagdenied";
					$label	= esc_html__('Declined', 'tuturn');		
					break;
				case "failed":
					$class	= " tu-tagdenied";
					$label	= esc_html__('Declined', 'tuturn');		
					break;
			}

			echo do_shortcode('<span class="tu-tagstatus'.$class.'">'.$label.'</span>');
		}
	}
	add_filter( 'tuturn_booking_status', 'tuturn_booking_status');
}


/**
 * Alert notification
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if (!function_exists('tuturn_alert_notification')) {
 	function tuturn_alert_notification($title='',$content='',$class='',$btn_text='',$identity=''){?>
		<div class="tu-boxitem <?php echo esc_attr($class);?>">
            <div class="tu-alertcontent">
				<?php if( !empty($title) ){?>
                	<h4><?php echo esc_html($title);?></h4>
				<?php } ?>
				<?php if( !empty($content) ){?>
                	<p><?php echo esc_html($content);?></p>
				<?php } ?>
            </div>
			<?php if( !empty($btn_text) ){?>
				<div class="tu-btnrea">
					<?php if( !empty($identity)){?>
						<a href="javascript:void(0);" data-user_id ="<?php echo esc_html($identity)?>"class="tu-pb-lg tu-primbtn">
							<?php echo esc_html($btn_text);?>
						</a>
					<?php } ?>
				</div>
			<?php } ?>
        </div>
	  <?php
	}
	add_action( "tuturn_alert_notification", "tuturn_alert_notification",10,5 );
}

/**
 * User Verfication
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if (!function_exists('tuturn_user_verfication')) {
	function tuturn_user_verfication($user_identity){
 
	   	$identity_verified	= get_user_meta($user_identity, 'identity_verified', true);
		$identity_verified	= !empty($identity_verified) ? $identity_verified : 0;
 		$drafted_posts_args     = array(
										'posts_per_page'    => -1,
										'post_status'       => array('draft'),
										'author'            => $user_identity
								);
		$drafted_posts          = tuturn_get_post_count_by_metadata('user-verification',$drafted_posts_args);
		$pending_posts_args     = array(
			'posts_per_page'    => -1,
			'post_status'       => array('pending'),
			'author'            => $user_identity
		);
		$pending_posts          = tuturn_get_post_count_by_metadata('user-verification',$pending_posts_args); ?>
		<div class="sv-alertswrapper">
			<?php 
				if(empty($identity_verified) && empty($drafted_posts) && empty($pending_posts) ){
					$title      = esc_html__('Verification required','tuturn');
					$content    = esc_html__('You must verify your identity, please submit the required documents to get verified.
					As soon as you will be verified then you will be able to get online appointments and other site features.','tuturn');
					do_action( 'tuturn_alert_notification', $title,$content);
				} else if(!empty($identity_verified) && $identity_verified === '1'){
					$title      = esc_html__('Woohoo!','tuturn');
					$content    = esc_html__("We have successfully completed your indentity verification. you’re now ready to use full features.","tuturn");
					do_action( 'tuturn_alert_notification', $title,$content,'alert alert-dismissible fade show');
				} elseif(empty($identity_verified)  && !empty($pending_posts) || !empty($drafted_posts) ){
					$title      = esc_html__('Woohoo!','tuturn');
					$content    = esc_html__('You have successfully submitted your documents. buckle up We will verify and respond to your request very soon.','tuturn');
					$btn_text   = '';
					do_action( 'tuturn_alert_notification', $title,$content,'tu-alertitembg',$btn_text,$user_identity);
				}
			?>
		</div>
	 <?php
   }
   add_action( "tuturn_user_verfication", "tuturn_user_verfication");
}

/**
* CSV export bookings
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if (!function_exists('tuturn_bookings_csvdownloads')) {
	function tuturn_bookings_csvdownloads( ) { 
		global $current_user;	
		if(!empty($_GET['csvexport']) && !empty($_GET['tab']) && $_GET['tab'] == 'booking-listings'){
			$booking_status	= !empty($_GET['booking_status']) ? esc_html($_GET['booking_status']) : '';
			$booking_date	= !empty($_GET['date']) ? esc_html($_GET['date']) : '';
			$service		= !empty($_GET['service']) ? esc_html($_GET['service']) : '';
			$user_identity  = intval($current_user->ID);
			$profile_id     = tuturn_get_linked_profile_id( $user_identity );
			$user_type 	    = apply_filters('tuturnGetUserType', $user_identity );
			$order_arg  = array(
				'orderby'       => 'date',
				'paginate'      => true,
				'payment_type'  => 'booking',
			);

			if (!empty($user_type) && $user_type === 'student') {
				$order_arg['student_id']  = $user_identity;
			} elseif (!empty($user_type) && $user_type === 'instructor') {
				$order_arg['instructor_id']  = $user_identity;
			}

			if( !empty($booking_date) ){
				$order_arg['booking_date']  = date('d-m-Y', strtotime($booking_date));
			}

			$customer_orders = wc_get_orders( $order_arg );

			$gmt_time		= current_time( 'mysql', 1 );
			$file_name	= 'bookings-'.$gmt_time;
			 // disable caching
			$now = gmdate("D, d M Y H:i:s");
			header("Expires: Tue, 01 Jan 2001 00:00:01 GMT");
			header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
			header("Last-Modified: {$now} GMT");
			// force download  
			header("Content-Type: application/force-download");
			header("Content-Type: application/octet-stream");
			header("Content-Type: application/download");
			header('Content-Type: text/x-csv');
			header('Content-Disposition: attachment; filename="'.$file_name.'.csv"');
			header("Content-Transfer-Encoding: binary");
    		header("Connection: close");
			ob_end_clean();

			$output_handle 		= fopen('php://output', 'w');			
			$bookings_titles	= array(
				esc_html__('Booking ID #','tuturn'),
				esc_html__('Name','tuturn'),
				esc_html__('Appointment date','tuturn'),
				esc_html__('Appointment time','tuturn'),
				esc_html__('Price','tuturn'),
				esc_html__('Status','tuturn'),
				esc_html__('Profile URL','tuturn'),
			);
			foreach($bookings_titles as $title){
				$csv_fields[] = $title;
			}
			fputcsv($output_handle, $csv_fields);

			if (!empty($customer_orders->orders)) {
				foreach ($customer_orders->orders as $order) {
					$booking_detail = get_post_meta( $order->get_id(), 'cus_woo_product_data',true );
					$booking_status = get_post_meta( $order->get_id(), 'booking_status',true );
					$booking_date	= get_post_meta( $order->get_id(), '_booking_date',true );

					$booked_data    = !empty($booking_detail['booked_data']) ? $booking_detail['booked_data'] : array();

					$appointment_date_time	= '';
					if(!empty($booked_data['booked_slots'])){
						foreach($booked_data['booked_slots'] as $date=>$slots){
							$time_slots	= '';
							foreach($slots as $key=>$timeslot){
                                $Hour                = date("H:i", strtotime($timeslot));  
                                $values              = explode("-",$timeslot);
                                $booking_start_time  = $values[0];
                                $booking_end_time    = $values[1];
                                $booking_start_time  = substr($booking_start_time, 0, 2).':'.substr($booking_start_time, -2);
                                $booking_end_time    = substr($booking_end_time, 0, 2).':'.substr($booking_end_time, -2);
								$time_slots			 = '('.esc_html($booking_start_time).'-'.esc_html($booking_end_time).')';                            
                            }

							$appointment_date_time	.= $date. $time_slots.', ';
						}
					}

					$booking_dates	= '';
					if(!empty($booking_date) && is_array($booking_date)){
						$booking_dates	= implode(", ",$booking_date);
					}

					$gmt_time		= current_time( 'mysql', 1 );
					$gmt_time		= date('Y-m-d', strtotime($gmt_time));

					if(!empty($booking_status) && $booking_status == 'publish'){
						$status_label   = esc_html__('Ongoing', 'tuturn');
					} elseif(!empty($booking_status) && $booking_status == 'declined'){
						$status_label   = esc_html__('Declined', 'tuturn');
					} elseif(!empty($booking_status) && $booking_status == 'cancelled'){
						$status_label   = esc_html__('Cancelled', 'tuturn');
					} else {
						$status_label   = esc_html__('Pending', 'tuturn');
					}					

					if(!empty($user_type) && $user_type === 'instructor'){
						$student_id      = get_post_meta( $order->get_id(), 'student_id',true );
						$profile_id     = tuturn_get_linked_profile_id( $student_id );
					} else {
						$instructor_id      = get_post_meta( $order->get_id(), 'instructor_id',true );
						$profile_id     = tuturn_get_linked_profile_id( $instructor_id );
					}
					
					$order_total    = !empty($user_type) && $user_type === 'instructor' ? get_post_meta($order->get_id(),'instructor_shares',true  ): $order->get_total();			
					if(!empty($user_type) && $user_type === 'instructor' && !empty($payment_type) && $payment_type === 'package'){
						$order_total  = $order->get_total();
					}
					$order_total  = !empty($order_total) ? $order_total : 0;
					
					$row_data	= array();
					$row_data['booking_id']		= esc_html($order->get_id());
					$row_data['seller_name']	= esc_html(tuturn_get_username($profile_id));
					$row_data['booking_date']	= $booking_dates;
					$row_data['booking_time']	= $appointment_date_time;
					$row_data['Price']			= html_entity_decode(tuturn_price_format($order_total, 'return'));
					$row_data['status']			= $status_label;
					$row_data['profile_url']	= get_permalink($profile_id);
					$OutputRecord				= $row_data;
					fputcsv($output_handle, $OutputRecord);
				}
			}
			fclose( $output_handle );
			exit;
		}
	}
	add_action('tuturn_dashboard_head', 'tuturn_bookings_csvdownloads');
}

/**
 * Dashboard guppy message chat icon
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if ( !function_exists( 'tuturn_guppy_message' ) ) {
	function tuturn_guppy_message( $args = '' ) {
		if(apply_filters( 'tuturn_chat_solution_guppy',false ) === true){
			if(!empty($args)){
				if (!empty($args) && is_array($args)) {
					extract($args);
				}

				$inbox_url	= tuturn_get_page_uri('inbox');
				
				if (!empty($userType) && $userType === 'student') {
					$friend_id  = $instructor_id;
				} elseif (!empty($userType) && $userType === 'instructor') {
					$friend_id  = $student_id;
				}

				$inbox_url	= add_query_arg(
					array(
						'chat_type'	=> 1,
						'chat_id'	=> $friend_id.'_'.'1',
						'type'	    => 'messanger',
					),
					$inbox_url
				);

				ob_start(); ?>
					<a class="tu-pb tu-btnorangesm wpguppy_start_chat" data-receiver_id="<?php echo esc_attr($friend_id);?>" href="<?php echo esc_url($inbox_url);?>"><i class="icon icon-message-square"></i></a>
				<?php ob_end_flush();
			}
		}
	}
	add_action( 'tuturn_guppy_message', 'tuturn_guppy_message');
}

/**
 * @init            Check if chat solution installed
 * @package         Amentotech
 * @since           1.0
 * @desc            If plugin activated return true else false
 */
if (!function_exists('tuturn_chat_solution_guppy')) {
    add_filter('tuturn_chat_solution_guppy', 'tuturn_chat_solution_guppy',10,1);
	function tuturn_chat_solution_guppy($chat=false){
		if( in_array('wp-guppy/wp-guppy.php', apply_filters('active_plugins', get_option('active_plugins'))) 
            || in_array('wpguppy-lite/wpguppy-lite.php', apply_filters('active_plugins', get_option('active_plugins')))
        ){ 
            $chat = true;
        }

        return $chat;
	}
}

/**
 * Dashboard guppy message chat icon
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if ( !function_exists( 'tuturn_woocommerce_install_notice' ) ) {
	function tuturn_woocommerce_install_notice( $args = '' ) {
		?>
		<div class="tu-freelanceremptylist">
			<div class="tu-freelanemptytitle">
				<h4><?php esc_html_e('WooCommerce required', 'tuturn');?></h4>
				<p><?php esc_html_e('You need to install WooCommerce plugin.', 'tuturn');?></p>
			</div>
		</div>
		<?php
	}
	add_action( 'tuturn_woocommerce_install_notice', 'tuturn_woocommerce_install_notice');
}

/**
 * Post views
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if ( !function_exists( 'tuturn_post_views' ) ) {
	function tuturn_post_views( $post_id = 0 ) {
		if( !empty($post_id) ){
		$total_views		= get_post_meta( $post_id, 'tuturn_post_views', true );
		$total_views		= !empty($total_views) ? intval($total_views) : 0;
		if( !empty($total_views) ){
			ob_start();
			?>
			<li>
				<i class="icon icon-eye">
					<span><?php echo sprintf( _n( '%s View', '%s Views', $total_views, 'tuturn' ), number_format_i18n( $total_views ) ) ?></span></i>
			</li>
		<?php
			 echo ob_get_clean();
			}
		}
	}
	add_action( 'tuturn_post_views', 'tuturn_post_views');
}

/* remove order again button */
remove_action( 'woocommerce_order_details_after_order_table', 'woocommerce_order_again_button' );


/**
 * Account verify by verify email
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if (!function_exists('tuturn_user_account_verify_email')) {
    add_action('init', 'tuturn_user_account_verify_email');
    function tuturn_user_account_verify_email()
    {
        if (!empty($_GET['key']) && !empty($_GET['verifyemail'])) {
            do_action('tuturn_verify_user_account');
        }
    }
}

/**
 * Account verification
 * @return
 */
if (!function_exists('tuturn_verify_user_account')) {
    function tuturn_verify_user_account()
    {
        if (!empty($_GET['key']) && !empty($_GET['verifyemail'])) {
            $verify_key = esc_html($_GET['key']);
            $user_email = esc_html($_GET['verifyemail']);
            $user_email = !empty($user_email) ? str_replace(' ', '+', $user_email) : '';
            $user_data = get_user_by('email', $user_email);
            $user_identity = !empty($user_data) ? $user_data->ID : 0;
            $user_type = apply_filters('tuturnGetUserType', $user_identity);

            if (!empty($user_identity)) {
                $confirmation_key = get_user_meta(intval($user_identity), 'confirmation_key', true);
                if ($confirmation_key === $verify_key) {
					$linkedprofile_id = get_user_meta($user_identity, '_linked_profile', true);
                    update_user_meta(intval($user_identity), 'confirmation_key', '');
                    update_user_meta(intval($user_identity), '_is_verified', 'yes');
                    update_post_meta(intval($linkedprofile_id), '_is_verified', 'yes'); 
                    if (!empty($user_type) && ($user_type == 'instructor' || $user_type == 'student')) {
                        $redirect = tuturn_dashboard_page_uri($user_data->ID);
                    } else {
                        $redirect = home_url('/');
                    }

                    if (!is_user_logged_in()) {
                        if (!is_wp_error($user_data) && isset($user_data->ID) && !empty($user_data->ID)) {
                            wp_clear_auth_cookie();
                            wp_set_current_user($user_data->ID, $user_data->user_login);
                            wp_set_auth_cookie($user_data->ID, true);
                            update_user_caches($user_data);
                            do_action('wp_login', $user_data->user_login, $user_data);
                            wp_redirect($redirect);
                            exit();
                        }
                    } else {
                        wp_redirect($redirect);
                        exit();
                    }
                }
            }
        }
    }
    add_action('tuturn_verify_user_account', 'tuturn_verify_user_account');
}

/**
 * User login authenticate
 * @since    1.0.0
*/
if(!function_exists('tuturn_user_authenticate_by_email')){
    function tuturn_user_authenticate_by_email( &$username ) {
        global $tuturn_settings;
        $user = get_user_by( 'email', $username );       
		$userType 	        = apply_filters('tuturnGetUserType', $user->ID );
		$roles		    = !empty($user_data_set->roles) ? $user_data_set->roles : '';

        if ( $user 
			&& !empty($roles) 
			&& in_array('subscriber',$roles ) 
			&& ( !empty($user_type) && ($user_type == 'instructor' || $user_type == 'student') )
		) {
            $is_verified 			= get_user_meta($user->ID, '_is_verified', true);
            $is_verified			= !empty($is_verified) ? $is_verified : 'no';

            if($is_verified == 'no'){
                if (empty($tuturn_settings['user_account_approve'])) {
                    $json['type'] 		= 'error';
                    $json['title']      = esc_html__("Oops!", 'tuturn');
                    $verify_new_user = !empty($tuturn_settings['email_user_registration']) ? $tuturn_settings['email_user_registration'] : '';
					
                    if (!empty($verify_new_user) && $verify_new_user == 'verify_by_link' && empty($tuturn_settings['user_account_approve'])) {
                        $message = esc_html__("Please verify your account through the email that has been sent to your email address.", 'tuturn');

						$json['message'] 	= $message;
						wp_send_json($json, 203);
                    } elseif (!empty($verify_new_user) && $verify_new_user == 'verify_by_admin' && empty($tuturn_settings['user_account_approve'])) {
                        $message = esc_html__("Please wait while your account is verified by the admin.", 'tuturn');
						$json['message'] 	= $message;
						wp_send_json($json, 203);
                    }
                }
            }
        }
    }
    //add_action( 'wp_authenticate', 'tuturn_user_authenticate_by_email' );
}

/**
 * Hourly post status
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if ( !function_exists( 'tuturn_hourly_post_status' ) ) {
	function tuturn_hourly_post_status( $status = '' ) {
		switch ($status) {
			case "pending":
				$class	= "tu-pending-status";
				$label	= esc_html__('Pending', 'tuturn');			 
				break;
			case "decline":
				$class	= "tu-statusdeclined";
				$label	= esc_html__('Decline', 'tuturn');
				break;		
			case "publish":
				$class	= "tu-statusapproved";
				$label	= esc_html__('Approved', 'tuturn');	
				break;
		}

		echo do_shortcode('<label class="tu-listing-status '.$class.'">'.$label.'</label>');
	}
	add_action( 'tuturn_hourly_post_status', 'tuturn_hourly_post_status');
}


/**
 * featured price
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if (!function_exists('tuturn_display_tutor_price')) {
	add_action( 'tuturn_display_tutor_price', 'tuturn_display_tutor_price' );
	function tuturn_display_tutor_price( $profile_id=0 ) {
		global $tuturn_settings;
		$profile_hourlyprice    = !empty($tuturn_settings['profile_hourlyprice']) ? $tuturn_settings['profile_hourlyprice'] : false;
		$hourly_rate 		 	= get_post_meta( $profile_id, 'hourly_rate', true);
		?>
		<div class="form-group form-group-half">
			<label class="tu-label"><?php esc_html_e('Hourly fee', 'tuturn'); ?></label>
			<div class="tu-placeholderholder">
				<input type="text" name="hourly_rate" value="<?php echo esc_attr($hourly_rate);?>" class="form-control"  required placeholder=" ">
				<div class="tu-placeholder">
					<span><?php esc_html_e('Your hourly fee (only numeric value)', 'tuturn'); ?></span>
					<?php if(empty($profile_hourlyprice) ){?>
						<em>*</em>
					<?php } ?>
				</div>
			</div>
		</div>
		<?php
        
    }
}

/**
 * booking button
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if (!function_exists('tuturn_booking_button')) {
	add_action( 'tuturn_booking_button', 'tuturn_booking_button',10,2 );
	function tuturn_booking_button( $student_id=0,$instructor_profile_id=0 ) {
		?>
		<a href="javascript:void(0)" id="tu-book-appointment" data-student_id="<?php echo esc_attr($student_id); ?>" data-instructor_profile_id="<?php echo esc_attr($instructor_profile_id); ?>" class="tu-primbtn"><?php esc_html_e('Book a tution','tuturn') ?></a>
		<?php
        
    }
}

/**
 * Redirect to search page from cart
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if (!function_exists('tuturn_redirection_cart_page')) {
	add_action("template_redirect", 'tuturn_redirection_cart_page');
	function tuturn_redirection_cart_page(){
		global $woocommerce,$tuturn_settings;
		if( function_exists('is_cart') && is_cart() && WC()->cart->cart_contents_count == 0){
			$enable_cart_redirect    = !empty($tuturn_settings['enable_cart_redirect']) ? $tuturn_settings['enable_cart_redirect'] : false; 
			if(!empty($enable_cart_redirect)){
				$instructor_search_url   = tuturn_get_page_uri('instructor_search');
				wp_safe_redirect($instructor_search_url);
			}
		}
	}
}


/**
 * Custom title(short/full name) for only instructor
 * with favicon title
 * */
function tuturn_custom_title_tag($title)
{
	if (is_singular("tuturn-instructor")) {
		$tutor_profile_id 	= 0;
		$post_slug 			= get_post_field('post_name', get_post());

		if ($post = get_page_by_path($post_slug, OBJECT, 'tuturn-instructor')) {
			$tutor_profile_id = $post->ID;
		}

		$name = !empty($tutor_profile_id) ? tuturn_get_username($tutor_profile_id) : '';

		if (!empty($name)) {
			return sprintf("%s - %s", get_bloginfo('name'), $name);
		}
	}

	return $title;
}
add_filter('pre_get_document_title', 'tuturn_custom_title_tag', 10);
