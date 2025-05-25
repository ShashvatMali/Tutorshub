<?php

/**
 * 
 * Class 'Tuturn_Admin_CPT_Seller' defines the cusotm post type
 * 
 * @package     Tuturn
 * @subpackage  Tuturn/admin/cpt
 * @author      Amentotech <info@amentotech.com>
 * @link        http://amentotech.com/
 * @version     1.0
 * @since       1.0
 */

if (!class_exists('Tuturn_Withdraw')) {

    class Tuturn_Withdraw {

        /**
         * @access  public
         * @Init Hooks in Constructor
         */
        public function __construct() {
            add_action('init', array(&$this, 'init_post_type'));
            add_filter('manage_withdraw_posts_columns', array(&$this, 'withdraw_columns_add'));
            add_action('manage_withdraw_posts_custom_column', array(&$this, 'withdraw_columns'),10, 2);
            add_filter('post_row_actions',array(&$this, 'Tuturn_Withdraw_action_row'), 10, 2);
            add_action('init', array(&$this, 'withdraw_custom_post_status'));
            add_action('admin_footer-post.php', array(&$this, 'withdraw_append_post_status_list'));
        }

        /**
         * @Remove row actions
         * @return {post}
         */
        public function Tuturn_Withdraw_action_row($actions, $post){
            if ($post->post_type === "withdraw"){
                unset($actions['edit']);
                unset($actions['inline hide-if-no-js']);
            }
            return $actions;
        }


        /**
         * @Prepare Columns
         * @return {post}
         */
        public function withdraw_columns_add($columns) {
            $columns['price'] 				    = esc_html__('Price','tuturn');
            $columns['account_type'] 		    = esc_html__('Account type','tuturn');
            $columns['acount_details']          = esc_html__('Account details','tuturn');
            $columns['status'] 				    = esc_html__('Status','tuturn');

            return $columns;
        }

        /**
         * @Get Columns
         * @return {}
         */
        public function withdraw_columns($case) {
            global $post;
            $price	          	= get_post_meta( $post->ID, '_withdraw_amount', true );
            $price	          	= !empty($price) ? $price : '';
            $account_type		= get_post_meta( $post->ID, '_payment_method', true );
            $account_type		= !empty($account_type) ? $account_type : '';
            $status				= get_post_status( $post->ID );
            $status_data		= !empty($status) ? esc_html($status) : esc_html__('Processed','tuturn');
            $account_details	= get_post_meta($post->ID, '_account_details',true);
            
            switch ($case) {
            case 'price':
                tuturn_price_format($price);
            break;
            case 'acount_details':
                $payrols	= tuturn_get_payouts_lists();
                ?>
                <div class="order-edit-wrap">
                    <div class="cus-modal" id="cus-order-modal-<?php echo esc_attr( $post->ID );?>">
                        <div class="cus-modal-dialog">
                            <div class="cus-modal-content">
                                <div class="cus-modal-body">
                                    <div class="cus-form cus-form-change-settings">
                                        <div class="edit-type-wrap">
                                            <?php
                                            $db_saved	= maybe_unserialize( $account_details );
                                            foreach ($payrols[$account_type]['fields'] as $key => $field) {

                                                if(!empty($field['show_this']) && $field['show_this'] == true){
                                                    $current_val	= !empty($db_saved[$key]) ? $db_saved[$key] : 0;
                                                    ?>
                                                    <div class="cus-options-data">
                                                        <label><span><?php echo esc_html($field['title']);?></span></label>
                                                        <div class="step-value">
                                                            <span><?php echo esc_html($current_val);?></span>
                                                        </div>
                                                    </div>
                                                <?php }
                                            }?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            break;
            case 'account_type':
                echo esc_html( $account_type );
            break;
            case 'status':
                ?>
                <div class="order-edit-wrap">
                    <div class="view-order-detail">
                        <?php 
                        if($status_data == 'pending'){
                            ?>
                            <div class="tu-bordertags">
                                <a href="javascript:void(0);" data-status="publish" data-id="<?php echo intval($post->ID);?>" class="tu-update-earning bordr-green"><?php esc_html_e( 'Approve', 'tuturn' );?></a>
                            </div>
                            <?php
                        } elseif( !empty($status_data) && $status_data === 'publish'){?>
                            <div class="tu-bordertags">
                                <a href="javascript:void(0);"><?php esc_html_e( 'Processed', 'tuturn' );?></a>
                            </div>
                            <?php
                        }?>
                    </div>
                </div>
                <?php
            break;
            }
        }

        /**
         * @Init Post Type
         * @return {post}
         */
        public function init_post_type() {
            $this->prepare_post_type();
            add_action( 'wp_ajax_tuturn_update_earning_withdraw',	array( $this, 'tuturnUpdateWithdrawStatus'));
        }


        /**
		 * Approve withdraw request
		 *
		 * @since    1.0.0
		*/
		public function tuturnUpdateWithdrawStatus($data){

            $json		= array();
            $do_check = check_ajax_referer('ajax_nonce', 'security', false);

            if ( $do_check == false ) {
                $json['type'] = 'error';
                $json['title'] = esc_html__('Failed!','tuturn' );
                $json['message'] = esc_html__('Security check failed, this could be because of your browser cache. Please clear the cache and check it again', 'tuturn');
                wp_send_json( $json );
            }

            $validation_fields  = array(		
				'id'		=> esc_html__('Something went wrong','tuturn'),			
				'status'	=> esc_html__('Something went wrong','tuturn'),			
			);

        	$json['type']	= 'error';
        	$json['title'] 	= esc_html__('Failed.!','tuturn');
			foreach($validation_fields as $key => $validation_field ){
				if( empty($_POST[$key]) ){
					$json['message'] 		= $validation_field;
                    wp_send_json( $json );
				}
			}	

            $post_id = !empty($_POST['id']) ? intval($_POST['id']) : 0;
            $post_status	= !empty($_POST['status']) ? sanitize_text_field($_POST['status'])  : 'pending';
            $post_author    = get_post_field ('post_author', $post_id);
            $user_data      = get_userdata($post_author);
            
            if( is_admin() ){			
                $result	= wp_update_post(array(
                    'ID'    	    =>  $post_id,
                    'post_status'   =>  $post_status
                ));

                if ( ! is_wp_error( $result ) ) {
                    $withdraw_amount    = !empty(get_post_meta($post_id, '_withdraw_amount', true)) ? get_post_meta($post_id, '_withdraw_amount', true) : 0;
                    $login_url    	    =  Tuturn_Profile_Menu::tuturn_profile_menu_link('booking', $user_data->ID, true, '');
                    /* email to instructor on withdraw approved */
                    if (class_exists('Tuturn_Email_helper')) {
                        $emailData	= array();
                        if (class_exists('TuturnWithDrawStatuses')) {
                            $email_helper               = new TuturnWithDrawStatuses();
                            $emailData['user_email']    = !empty($user_data->user_email) ? $user_data->user_email : 'abc@gamil.com';
                            $emailData['user_name']     = !empty($user_data->display_name) ? $user_data->display_name : '';
                            $emailData['user_link']     = $login_url;
                            $emailData['amount'] 		= $withdraw_amount;
                            $email_helper->withdraw_approved_user_email($emailData);
                        }
                    }

                    $json['type'] 	 	= 'success';
                    $json['title']		= esc_html__('Updated!', 'tuturn');
                    $json['message']	= esc_html__('Earning withdraw status has been updated successfully','tuturn');
                    wp_send_json( $json );
                } else {
                    $json['type'] 	 	= 'error';
                    $json['title']		= esc_html__('Failed!', 'tuturn');
                    $json['message']	= esc_html__('Something went wrong','tuturn');
                    wp_send_json( $json );
                }
            } else {
                $json['type'] 	 	= 'error';
                $json['title']		= esc_html__('Failed!','tuturn');
                $json['message']	= esc_html__('You are not allowed to update record','tuturn');
                wp_send_json( $json );
            }
		}

        /**
         * @Prepare Post Type Category
         * @return post type
         */
        public function prepare_post_type() {
            $labels = array(
                'name'              => esc_html__('Withdraw', 'tuturn'),
                'all_items'         => esc_html__('Withdraw', 'tuturn'),
                'singular_name'     => esc_html__('Withdraw', 'tuturn'),
                'add_new'           => esc_html__('Add Withdraw', 'tuturn'),
                'add_new_item'      => esc_html__('Add New Withdraw', 'tuturn'),
                'edit'              => esc_html__('Edit', 'tuturn'),
                'edit_item'         => esc_html__('Edit Withdraw', 'tuturn'),
                'new_item'          => esc_html__('New Withdraw', 'tuturn'),
                'view'              => esc_html__('View Withdraw', 'tuturn'),
                'view_item'         => esc_html__('View Withdraw', 'tuturn'),
                'search_items'      => esc_html__('Search Withdraw', 'tuturn'),
                'not_found'         => esc_html__('No Withdraw found', 'tuturn'),
                'not_found_in_trash'    => esc_html__('No Withdraw found in trash', 'tuturn'),
                'parent'                => esc_html__('Parent Withdraw', 'tuturn'),
            );
            $args = array(
                'labels'                => $labels,
                'description'           => esc_html__('This is where you can add new withdraw', 'tuturn'),
                'public'                => false,
                'supports'              => array('title','author'),
                'show_ui'               => true,
                'capability_type'       => 'post',
                'map_meta_cap' 		    => true,
                'menu_position' 	    => 10,
                'publicly_queryable'    => false,
                'query_var'             => false,
                'menu_icon'             => 'dashicons-money-alt',
                'rewrite'               => array('slug' => 'withdraw', 'with_front' => true),
                'capabilities'          => array('create_posts'   => false,),
            );
            register_post_type('withdraw', $args);
        }

        /**
         * Add 'rejected' post status.
         */
        public function withdraw_custom_post_status(){
            register_post_status( 'rejected',
                array(
                    'label'                     => esc_html__('Rejected', 'tuturn'),
                    'public'                    => false,
                    'exclude_from_search'       => false,
                    'show_in_admin_all_list'    => true,
                    'show_in_admin_status_list' => true,
                    'label_count'               => _n_noop( 'Rejected <span class="count">(%s)</span>', 'Rejected <span class="count">(%s)</span>', 'tuturn' ),
                )
            );
        }

         /**
         * Append post status.
         */
        public function withdraw_append_post_status_list(){
            global $post;
            $complete = '';
            $label = '';

            if($post->post_type == 'withdraw'){
                $status = $post->post_status;

                if($post->post_status == 'rejected'){
                    $complete = ' selected=\"selected\"';
                    $label = '<span id=\"post-status-display\"> '.esc_html__('Rejected', 'tuturn').'</span>';
                }

                $complete   = '';
                echo do_shortcode('<script>
                    jQuery(document).ready(function($){
                        jQuery("select#post_status").append("<option value=\"rejected\" '.$complete.'> '.esc_html__('Rejected', 'tuturn').'</option>");
                        jQuery(".misc-pub-section label").append("'.$label.'");
                        jQuery("#post-status-display").append("'.$label.'");
                        $("select#post_status option[value='.$status.']").attr("selected", true);
                    });
                    </script>');
            }
        }

    }

	new Tuturn_Withdraw();
}
