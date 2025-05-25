<?php
/**
 * 
 * Class 'Tuturn_Admin_Student' defines the cusotm post type
 * 
 * @package     Tuturn
 * @subpackage  Tuturn/admin/cpt
 * @author      Amentotech <info@amentotech.com>
 * @link        http://amentotech.com/
 * @version     1.0
 * @since       1.0
 */
class Tuturn_Admin_Student{

	/**
	 * Profiles post type
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function __construct() {
		add_action('init', array(&$this, 'init_post_type'));
		add_action('init', array(&$this, 'download_tutoring_log_csv'));
		add_action('views_edit-tuturn-student', array(&$this, 'tuturn_add_admin_quick_link'));
		add_filter('manage_tuturn-student_posts_columns', array(&$this, 'volunteers_columns_add'));
        add_action('manage_tuturn-student_posts_custom_column', array(&$this, 'volunteers_columns'),10, 2);
		add_action( 'add_meta_boxes',	array( $this, 'tuturn_student_add_meta_box'));
	}

	/**
	 * @Init post type
	*/
	public function init_post_type() {
		$this->register_posttype();
		$this->register_student_relation();
		add_action( 'wp_ajax_tuturn_student_assign_package',	array( $this, 'tuturn_student_assign_package'));
	}	

	function tuturn_add_admin_quick_link( $views ) {
		$post_type = 'tuturn-student';
	
		if ( ! isset( $_GET['post_type'] ) || $post_type !== $_GET['post_type'] ) {
			return $views;
		}

		$hours_array    = tuturn_hours_data_by_meta();
		$total_hours	= !empty($hours_array['total']) ? $hours_array['total'] : 0;
		$approved_hours	= !empty($hours_array['completed']) ? $hours_array['completed'] : 0;
		$declined_hours	= !empty($hours_array['pending']) ? $hours_array['pending'] : 0;
	
		$views['tuturn-instructor_total_hours'] = sprintf( '<span class="total-hours">%s <em class="count">(%d)</em></span>',
				esc_html__( 'Total hours', 'tuturn' ),
				$total_hours
		);

		$views['tuturn-instructor_approved_hours'] = sprintf( '<span class="approved-hours">%s <em class="count">(%d)</em></span>',
				esc_html__( 'Approved hours', 'tuturn' ),
				$approved_hours
		);

		$views['tuturn-instructor_pending_hours'] = sprintf( '<span class="declined-hours">%s <em class="count">(%d)</em></span>',
				esc_html__( 'Declined hours', 'tuturn' ),
				$declined_hours
		);

		$views['tuturn-download-hours'] = sprintf( '<a href="%s" class="tuturn-download-hours">%s</a>',
			admin_url( "edit.php?post_type={$post_type}&download_student_hours=true" ),
			esc_html__( 'Download hours log', 'tuturn' )
		);	

		return $views;
	}	

	public function download_tutoring_log_csv(){
		global $current_user, $wpdb;
		$time_format    = get_option('time_format');
		$date_format    = get_option('date_format');

		if(!empty($_GET['download_student_hours']) ){
			$file_name	= 'tutoring_log';
			$file_name	= "student-hours-" . date('Ymd') . ".xls";
			header('Content-Type: text/csv');
			header('Content-Disposition: attachment; filename="'.$file_name.'.csv"');
		
			ob_end_clean();
		
			$output_handle 		= fopen('php://output', 'w');
			$withdraw_titles	= array(
				esc_html__('Name','tuturn'),
				esc_html__('Email','tuturn'),
				esc_html__('Total hours','tuturn'),
				esc_html__('Completed hours','tuturn'),
				esc_html__('Declined hours','tuturn'),
			);

			$csv_fields     = array();
		
			foreach($withdraw_titles as $title){
				$csv_fields[] = $title;
			}
		
			fputcsv($output_handle, $csv_fields);

			$users = get_users(array(
				'meta_key' => '_user_type',
				'meta_value' => 'student'
			));

			foreach($users as $user){
				$hours_array    = tuturn_hours_data_by_meta(array(array('key' => 'student_id', 'value' => $user->ID )));
				$total_hours	= !empty($hours_array['total']) ? $hours_array['total'] : 0;
				$approved_hours	= !empty($hours_array['completed']) ? $hours_array['completed'] : 0;
				$declined_hours	= !empty($hours_array['pending']) ? $hours_array['pending'] : 0;

				$name		= $user->display_name;
				$user_email	= $user->user_email;

				$row_data	= array();

				$row_data['name']			= $name;
				$row_data['email']			= $user_email;
				$row_data['total_hours']	= $total_hours;
				$row_data['completed_hours']	= $approved_hours;
				$row_data['pending_hours']		= $declined_hours;

				$OutputRecord = $row_data;
				fputcsv($output_handle, $OutputRecord);
			}
		
			fclose( $output_handle );
			exit;
		}
	}
	
	/**
	 * @Prepare Columns
	 * @return {post}
	 */
	public function volunteers_columns_add($columns) {
		$columns['total_hours']		= esc_html__('Total hours','tuturn');
		$columns['approved_hours']	= esc_html__('Approved  hours','tuturn');
		$columns['declined_hours']	= esc_html__('Declined hours','tuturn');
		return $columns;
	}

	/**
	 * @Get Columns
	 * @return {}
	 */
	public function volunteers_columns($case) {
		global $post;
		$user_identity	= get_post_meta($post->ID, '_linked_profile',true);

		$hours_array    = tuturn_hours_data_by_meta(array(array('key' => 'student_id', 'value' => $user_identity )));
		$total_hours	= !empty($hours_array['total']) ? $hours_array['total'] : 0;
		$approved_hours	= !empty($hours_array['completed']) ? $hours_array['completed'] : 0;
		$declined_hours	= !empty($hours_array['pending']) ? $hours_array['pending'] : 0;
		
		switch ($case) {
			case 'total_hours':
				echo intval($total_hours);
			break;
			case 'approved_hours':
				echo intval($approved_hours);
			break;
			case 'declined_hours':
				echo intval($declined_hours);
			break;            
		}
	}

	/**
	 *Regirster profiles post type
	*/
	public function register_posttype() {
		$labels = array(
			'name'                  => esc_html__( 'Students', 'tuturn' ),
			'singular_name'         => esc_html__( 'Student','tuturn' ),
			'menu_name'             => esc_html__( 'Student', 'tuturn' ),
			'name_admin_bar'        => esc_html__( 'Student', 'tuturn' ),
			'all_items'             => esc_html__( 'All students', 'tuturn' ),
			'add_new_item'          => esc_html__( 'Add new student', 'tuturn' ),
			'add_new'               => esc_html__( 'Add new student', 'tuturn' ),
			'new_item'              => esc_html__( 'New student', 'tuturn' ),
			'edit_item'             => esc_html__( 'Edit student', 'tuturn' ),
			'update_item'           => esc_html__( 'Update student', 'tuturn' ),
			'view_item'             => esc_html__( 'View student', 'tuturn' ),
			'view_items'            => esc_html__( 'View student', 'tuturn' ),
			'search_items'          => esc_html__( 'Search student', 'tuturn' ),
		);

		$args = array(
			'label'                 => esc_html__( 'Students', 'tuturn' ),
			'description'           => esc_html__( 'All students.', 'tuturn' ),
			'labels'                => apply_filters('tuturn_product_taxonomy_duration_labels', $labels),
			'supports'              => array( 'title','editor','author','excerpt','thumbnail' ),
			'taxonomies'            => array( 'product_cat','languages'),
			'public' 				=> true,
			'supports' 				=> array('title','editor','author','excerpt','thumbnail'),
			'show_ui' 				=> true,
			'capability_type' 		=> 'post',
			'map_meta_cap' 			=> true,
			'publicly_queryable' 	=> true,
			'exclude_from_search' 	=> true,
			'hierarchical' 			=> false,
			'menu_position' 		=> 90,
			'rewrite' 				=> array('slug' => 'student', 'with_front' => true),
			'query_var' 			=> false,
			'has_archive' 			=> false,
			'capabilities' 			=> array(
										'create_posts' => false
									),
			'rest_base'             => 'tuturn-student',
			'show_in_menu'			=> 'edit.php?post_type=tuturn-instructor',
			'rest_controller_class' => 'WP_REST_Posts_Controller',
		);
		
		register_post_type( apply_filters('tuturn_profiles_post_type_name', 'tuturn-student'), $args );
	}

	/**
	 *Regirster relations
	*/
	public function register_student_relation(){
		$relation_labels = array(
			'name' 				=> esc_html__('Relations', 'tuturn'),
			'singular_name' 	=> esc_html__('Relation','tuturn'),
			'search_items'		=> esc_html__('Search Relation', 'tuturn'),
			'all_items' 		=> esc_html__('All Relation', 'tuturn'),
			'parent_item' 		=> esc_html__('Parent Relation', 'tuturn'),
			'parent_item_colon'	=> esc_html__('Parent Relation:', 'tuturn'),
			'edit_item' 		=> esc_html__('Edit Relation', 'tuturn'),
			'update_item' 		=> esc_html__('Update Relation', 'tuturn'),
			'add_new_item' 		=> esc_html__('Add New Relation', 'tuturn'),
			'new_item_name' 	=> esc_html__('New Relation Name', 'tuturn'),
			'menu_name' 		=> esc_html__('Relations', 'tuturn'),
		  );
	  
		  $relation_args = array(
			'hierarchical'              => true,
			'labels'			    	=> apply_filters('tuturn_relation_taxonomy_labels', $relation_labels),
			'show_ui'                   => true,
			'show_in_nav_menus' 		=> true,
			'show_admin_column'         => true,
			'query_var'                 => true,
			'rewrite'                   => array('slug' => 'relations'),
			'show_in_rest'              => true,
			'rest_base'                 => 'relations',
			'show_in_menu'				=> 'edit-tags.php?taxonomy=relations&post_type=tuturn-student',
			'rest_controller_class'     => 'WP_REST_Terms_Controller'
		  );		 
		  register_taxonomy('relations', array('tuturn-student'), $relation_args);
	}

	/**
     * Assign student package
     */
	public function tuturn_student_assign_package(){
		$json		= array();
		$do_check = check_ajax_referer('ajax_nonce', 'security', false);

		if ( $do_check == false ) {
			$json['type'] = 'error';
			$json['title'] = esc_html__('Failed!','tuturn' );
			$json['message'] = esc_html__('Security check failed, this could be because of your browser cache. Please clear the cache and check it again', 'tuturn');
			wp_send_json( $json );
		}

		$validations = array(
			'package_id'	=> esc_html__('Please select package', 'tuturn'),
			'user_id'		=> esc_html__('Something wrong, please try again.', 'tuturn'),
			'profile_id'	=> esc_html__('Something wrong, please try again.', 'tuturn'),
		);

		foreach ($validations as $key => $value) {
			if (isset($_POST[$key]) && empty($_POST[$key])) {
				$json['title']      = esc_html__("Oops!", 'tuturn');
				$json['type']         = 'error';
				$json['message']  = $value;
				wp_send_json($json);
			}
		}

		$package_id = !empty($_POST['package_id']) ? intval($_POST['package_id']) : 0;
		$profile_id = !empty($_POST['profile_id']) ? intval($_POST['profile_id']) : 0;
		$user_id	= !empty($_POST['user_id']) ? intval($_POST['user_id']) : 0;

		$customer = new WC_Customer( $user_id );
		$username     = $customer->get_username(); // Get username
		$user_email   = $customer->get_email(); // Get account email
		$first_name   = $customer->get_first_name();
		$last_name    = $customer->get_last_name();
		$display_name = $customer->get_display_name();

		// Customer billing information details (from account)
		$billing_first_name = $customer->get_billing_first_name();
		$billing_last_name  = $customer->get_billing_last_name();
		$billing_company    = $customer->get_billing_company();
		$billing_address_1  = $customer->get_billing_address_1();
		$billing_address_2  = $customer->get_billing_address_2();
		$billing_phone		= $customer->get_billing_phone();
		$billing_city       = $customer->get_billing_city();
		$billing_state      = $customer->get_billing_state();
		$billing_postcode   = $customer->get_billing_postcode();
		$billing_country    = $customer->get_billing_country();	

		$billing_first_name = !empty($billing_first_name) ? esc_html($billing_first_name) : $first_name;
		$billing_last_name = !empty($billing_last_name) ? esc_html($billing_last_name) : $last_name;

		$address = array(
			'first_name' => $billing_first_name,
			'last_name'  => $billing_last_name,
			'company'    => $billing_company,
			'email'      => $user_email,
			'phone'      => $billing_phone,
			'address_1'  => $billing_address_1,
			'address_2'  => $billing_address_2, 
			'city'       => $billing_city,
			'state'      => $billing_state,
			'postcode'   => $billing_postcode,
			'country'    => $billing_country
		);

		$package	= wc_get_product( $package_id );
		$product 	= wc_get_product( $package_id );
		

		$order = wc_create_order();
		$order->add_product($package, 1 ); //(get_product with id and next is for quantity)
		$order->set_address( $address, 'billing' );
		$order->calculate_totals();
		$order->update_status("completed", 'Package order', TRUE);
		$order_id	= $order->get_id();

		update_post_meta( $order_id, '_payment_method', 'cod' );
		update_post_meta( $order_id, '_payment_method_title', 'cod' );
		wp_set_object_terms( $order_id, 'completed', 'shop_order_status' );

		$order_detail	= array(
			'package_id' 	=> $package_id,
			'student_id' 	=> $user_id,
			'product_name' 	=> $product->get_name(),
			'price' 		=> $product->get_price(),
			'payment_type' 	=> 'package',
		);

		update_post_meta( $order_id, '_customer_user', $user_id );
		update_post_meta( $order_id, 'cus_woo_product_data', $order_detail );
		update_post_meta( $order_id, 'package_id', $package_id );
		update_post_meta( $order_id, 'payment_type', 'package' );
		update_post_meta( $order_id, 'student_id', $user_id );

		$current_date 	= current_time('mysql');
		if (function_exists('tuturn_update_packages_data')) {
			tuturn_update_packages_data( $order_id, $order_detail, $user_id);
		}

		update_post_meta($order_id,'tu_order_date',date('Y-m-d H:i:s', strtotime($current_date)));
		update_post_meta($order_id,'tu_order_date_gmt',strtotime($current_date));
		
		$json['type']	= 'success';	
		$json['message']	= esc_html__('Package assigned to user','tuturn' );
		wp_send_json($json);
	}

	/**
     * Adds the meta box container.
     */
    public function tuturn_student_add_meta_box( $post_type ) {
		add_meta_box(
			'tuturn_assign_package',
			esc_html__( 'Assign Package', 'tuturn' ),
			array( $this, 'tuturn_assign_package_fields' ),
			'tuturn-student',
			'advanced',
			'high'
		);
    }

	/**
     * Render Meta Box content.
     *
     * @param WP_Post $post The post object.
     */
    public function tuturn_assign_package_fields( $post ) {

		if(class_exists('WooCommerce')){
			$package_create	= true;
			$date_format	= get_option('date_format');
			$user_id 		= get_post_meta($post->ID, '_linked_profile', true);
			$order_id 		= get_user_meta($user_id, 'package_order_id', true);
			$order_id		= !empty($order_id) ? intval($order_id) : 0;
			if(!empty($order_id)){?>
				<div class="col-sm-12">
					<div class="tu-package-plan">
						<?php 
							$order 			= wc_get_order($order_id);
							
							if ( !empty($order ) && !empty($order->get_status()) ) {
								$remaing_days			= 0;
								$current_time			= time();
								$package_id				= get_post_meta($order_id, 'package_id', true);
								$package_details		= get_post_meta($order_id, 'package_details', true);
								$package_details		= !empty($package_details) ? $package_details : array();
								$package_create_date	= !empty($package_details['package_create_date']) ? $package_details['package_create_date'] : 0;
								$package_expriy_date	= !empty($package_details['package_expriy_date']) ? $package_details['package_expriy_date'] : 0;
								$package_create_date	= !empty($package_create_date) ? strtotime($package_create_date) : 0;
								$package_expriy_date	= !empty($package_expriy_date) ? strtotime($package_expriy_date) : 0;
								$remaining_featured		= empty($featured_allowed) || ( !empty($featured_allowed) && $featured_allowed < $featured_task)	? 0 : $featured_allowed - $featured_task;
								$package_id				= !empty($package_id) ? intval($package_id) : 0;
								$product_instant		= !empty($package_id)	? get_post( $package_id ) : '';
								$product_title			= !empty($product_instant) ? sanitize_text_field($product_instant->post_title) : '';
								$pkg_content			= !empty($product_instant) ? sanitize_text_field($product_instant->post_content) : '';
								
								if($package_expriy_date >= $current_time ){
									$remaing_days	= $package_expriy_date-$current_time;
									$remaing_days	= round((($remaing_days/24)/60)/60); 
								}
								?>
								<div class="tu-package-heading">									
									<span class="tu-onging">
										<?php esc_html_e('Order#','tuturn');?>
										<?php echo intval($order_id);?>
									</span>
									<div class="tu-package-tags">
										<?php if( !empty($remaing_days)){?>
											<span class="tu-onging">
												<?php esc_html_e('Ongoing','tuturn');?>
											</span>
										<?php } else { ?> 
											<span class="tu-onging tu-expire"><?php esc_html_e('Expired','tuturn');?></span>
										<?php } ?>
										<?php if( !empty($product_title) ){?>
											<h4><?php echo esc_html($product_title);?></h4>
										<?php } ?>
									</div>
								</div>
								<?php if( !empty($pkg_content) ){?>
									<div class="tu-description">
										<p><?php echo esc_html($pkg_content);?></p>
									</div>
								<?php } ?>
								<ul class="tu-package-list"> 
									<?php if( !empty($package_create_date) ){?>
										<li>
											<h6><?php esc_html_e('Purchased on','tuturn');?></h6>
											<span><?php echo date_i18n( $date_format, $package_create_date );?></span>
										</li>
									<?php } ?>
									<?php if( !empty($package_expriy_date) ){?>
										<li>
											<h6><?php esc_html_e('Expiry date','tuturn');?></h6>
											<span><?php echo date_i18n( $date_format, $package_expriy_date );?></span>
										</li>
									<?php } ?>
									<?php if( isset($remaing_days) ){?>
										<li>
											<h6><?php esc_html_e('Package duration','tuturn');?></h6>
											<span><?php echo wp_sprintf( esc_html__('%s days left', 'tuturn'), $remaing_days );?></span>
										</li>
									<?php } ?>
								</ul>
							<?php 
							}
						?>		
					</div>
				</div>
			<?php }

			$args = array(
				'limit'     => -1,
				'status'    => 'publish',
				'type'      => array('packages'),
				'orderby'   => 'date',
				'order'     => 'ASC',
				'user_type'	=> 'student'
			);
			$tuturn_packages = wc_get_products( $args );

			if(isset($tuturn_packages) && is_array($tuturn_packages) && count($tuturn_packages)>0){?>
				<div class="tu-package-plan">
					<label for="myplugin_new_field">
						<h3><?php esc_html_e( 'Select package', 'tuturn' ); ?></h3>
					</label><br>
					<select name="package_id" id="package_id">
						<option value=""><?php esc_html_e('Select package', 'tuturn');?></option>
						<?php
						foreach($tuturn_packages as $package){
							$package_id         = $package->get_id();?>
							<option value="<?php echo intval($package_id);?>"><?php echo esc_html($package->get_name());?></option>
							<?php
						}?>
					</select>
					<button type="button" id="tu-assign-package-student" data-profile_id="<?php echo intval($post->ID);?>" data-user_id="<?php echo intval($user_id);?>" class="button button-primary button-large"><?php esc_html_e('Assign package', 'tuturn');?></button>
				</div>
				<?php
			}
		}
       
    }
}
new Tuturn_Admin_Student();
