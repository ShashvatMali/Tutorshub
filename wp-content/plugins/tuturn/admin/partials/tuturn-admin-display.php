<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://codecanyon.net/user/amentotech/portfolio
 * @since      1.0.0
 *
 * @package    Tuturn
 * @subpackage Tuturn/admin/partials
 */
if( !function_exists('tuturn_manage_user_columns')){
    add_filter( 'manage_users_columns', 'tuturn_manage_user_columns');
    function tuturn_manage_user_columns($column) {
        $column['tu_varifiled']	= esc_html__('Profile verification', 'tuturn');
        $column['tu_user_type']	= esc_html__('User type', 'tuturn');
		return $column;
    }
}

if( !function_exists('tuturn_manage_user_column_row')){
    add_filter( 'manage_users_custom_column', 'tuturn_manage_user_column_row', 10, 3);
    function tuturn_manage_user_column_row($val, $column_name, $user_id) {
        global $tuturn_settings;
        $resubmit_verification  = !empty($tuturn_settings['resubmit_verification']) ? $tuturn_settings['resubmit_verification'] : false;
        switch ($column_name) {
            case 'tu_user_type' :
                $is_verified 	    = get_user_meta($user_id, '_is_verified',true);
                $linked_profile 	= get_user_meta($user_id, '_linked_profile',true);
                $pending_posts_args     = array(
                    'posts_per_page'    => -1,
                    'post_status'       => array('pending','draft','publish'),
                    'author'            => $user_id,
                );

                $pending_posts              = tuturn_get_post_count_by_metadata('user-verification',$pending_posts_args);
                //for admin only
                $user_meta	= get_userdata($user_id);
    
                if ( in_array( 'administrator', (array) $user_meta->roles ) ) {
                    return;
                }

                if ( in_array( 'subscriber', (array) $user_meta->roles ) ) {
                    $user_type = get_user_meta($user_id,'_user_type',true);			
                    if (!empty($user_type) && $user_type === 'instructor') {
                        $val = esc_html__('Tutor','tuturn');
                    } else if (!empty($user_type) && $user_type === 'student') {
                        $val = esc_html__('Student','tuturn');
                    } 
                }

                return $val;

            case 'tu_varifiled' :
                $is_verified 	    = get_user_meta($user_id, '_is_verified',true);
                $linked_profile 	= get_user_meta($user_id, '_linked_profile',true);
                $pending_posts_args     = array(
                    'posts_per_page'    => -1,
                    'post_status'       => array('pending','draft','publish'),
                    'author'            => $user_id,
                );

                $pending_posts              = tuturn_get_post_count_by_metadata('user-verification',$pending_posts_args);
                //for admin only
                $user_meta	= get_userdata($user_id);
    
                if ( in_array( 'administrator', (array) $user_meta->roles ) ) {
                    return;
                }
    
                $identity_verification	= !empty($tuturn_settings['identity_verification']) ? $tuturn_settings['identity_verification'] : false;
                $status					= (isset($is_verified) && $is_verified === 'yes') ? 'reject' : 'approve';
                $status_text			= (isset($is_verified) && $is_verified === 'yes') ? esc_html__('Reject','tuturn') : esc_html__('Approve','tuturn');
    
                $val .= "<a title='".ucfirst($status).' '.esc_html__('user','tuturn')."'
                            class='do_verify_user_confirm dashicons-before " . (!empty($is_verified) && $is_verified === 'yes' ? 'tu-icon-color-green' : 'tu-icon-color-red') . "'
                            data-type='".esc_attr($status)."'
                            data-user_id='".intval( $user_id )."'
                            href='javascript:void(0);'>
                            <span class='dashicons dashicons-admin-users woocommerce-help-tip' data-tip='".esc_attr($status_text)."'></span>
                        </a>";

                $val .= '<div id="approve-user-confirm-'.intval( $user_id ).'" class="tu-approve-user" style="display:none;">';
                    $val .= '<h4>'.wp_sprintf('%s %s',esc_html__('Are you sure you want to %s user?', 'tuturn'), $status).'</h4>';
                        $val .= '<div class="tu-action-links">';
                            $val .= "<a title='".esc_html__('Approve user','tuturn')."'
                                        class='do_verify_user dashicons-before " . (!empty($is_verified) && $is_verified === 'yes' ? 'tu-icon-color-green' : 'tu-icon-color-red') . "'
                                        data-type='".esc_attr($status)."'
                                        data-user_id='".intval( $user_id )."'
                                        href='javascript:void(0);'>
                                            <span class='dashicons dashicons-admin-users woocommerce-help-tip' data-tip='".esc_attr($status_text)."'></span>
                                            
                                    </a>";
                        $val .= '</div>';
                    $val .= '</div>';
                $val .= '</div>';
    
                if ( in_array( 'subscriber', (array) $user_meta->roles ) ) {
                    if(!empty($identity_verification) ){
                        $identity_verified  		= get_user_meta($user_id, 'identity_verified', true);
                        $verification_attachments   = get_user_meta($user_id, 'verification_attachments', true);
                        $identity_status			= !empty($identity_verified) ? 'approved' : 'inprogress';
                        
                        if(!empty($resubmit_verification) ){
                            $val .= "<a title='".esc_html__('Identity verification','tuturn')."' class='dashicons-before " . ((!empty($identity_verified) ) ? 'tu-icon-color-green' : 'tu-icon-color-red') . " ' data-type='".$identity_status."' data-id='".intval( $user_id )."' href='#' ><span class='dashicons dashicons-id'></span></a>";
                        } else {
                            $val .= "<a title='".esc_html__('Identity verification','tuturn')."' class='do_verify_identity dashicons-before " . ((!empty($identity_verified) ) ? 'tu-icon-color-green' : 'tu-icon-color-red') . " ' data-type='".$identity_status."' data-id='".intval( $user_id )."' href='#' ><span class='dashicons dashicons-id'></span></a>";
                        }

                    }

                    if(!empty($linked_profile)){
                        $val .= "<a target='_blank' title='".esc_html__('Linked profile','tuturn')."' class='dashicons-before linked-user-wrapper'  href='".get_edit_post_link($linked_profile)."' ><span class='dashicons dashicons-admin-links'></span></a>";
                    }
                }
                if( !empty($pending_posts) && !empty($resubmit_verification) ){
                    $request_url    = add_query_arg( array( 
                        'author_id' => $user_id, 
                        'post_type' => 'user-verification', 
                    ), admin_url( 'edit.php' ) );
                    $val .= "<a class='tu-linked-data' title='".esc_html__('View user identity verification','tuturn')."' href='".esc_url($request_url)."'>".esc_html__('View detail','tuturn')."</span></a>";
                }
                return $val;
    
                break;
            default:
        }
    }
}

/**
 * View verification details
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return 
 */
if( !function_exists(  'tuturn_view_identity_detail' ) ) {
	function tuturn_view_identity_detail(){
		$json       = array();  
		$user_id    = !empty($_POST['user_id']) ? intval( $_POST['user_id'] ) : '';
		
		//security check
		$do_check = check_ajax_referer('ajax_nonce', 'security', false);
		if ( $do_check == false ) {
			$json['type'] = 'error';
			$json['message'] = esc_html__('Security check failed, this could be because of your browser cache. Please clear the cache and check it again', 'tuturn');
			wp_send_json( $json );
		}
		
		$verification  = get_user_meta($user_id, 'verification_attachments', true);
		
		if(empty($verification)){
			$json['type']	= 'error';
			$json['message']	= esc_html__('No verification user details found','tuturn' );
			wp_send_json($json);
		}
        
		$user_info	    = !empty($verification['info']) ? $verification['info'] : array();
		$attachments	= !empty($verification['attachments']) ? $verification['attachments'] : array();
      
		$required = array(
			'name'   				=> esc_html__('Name', 'tuturn'),
			'contact_number'  		=> esc_html__('Contact number', 'tuturn'),
            'email_address'  		=> esc_html__('Email address', 'tuturn'),
			'verification_number'   => esc_html__('Verification number', 'tuturn'),
			'address'   			=> esc_html__('Address', 'tuturn'),
		);

		if( !empty($verification['info'] ) ) {
			unset( $verification['info'] );
		}

		ob_start();
		?>
		<div class="cus-modal-bodywrap">
			<div class="cus-form cus-form-change-settings">
				<div class="edit-type-wrap">
					<?php if(!empty($user_info)){
						foreach($user_info as $key => $item){
							if(!empty($required[$key])){
						?>
						<div class="cus-options-data">
							<label><span><strong><?php echo esc_html( $required[$key] );?></strong></span></label>
							<div class="step-value">
								<span><?php echo esc_html( $item );?></span>
							</div>
						</div>
					<?php }}}?>
					
					<?php if(!empty($attachments)){
						foreach($attachments as $key => $item){
                            $item_url   = !empty($item['url']) ? $item['url'] : '';
                            $item_name   = !empty($item['name']) ? $item['name'] : '';
                            ?>
                            <div class="cus-options-data cus-options-files">
                                <div class="step-value">
                                    <span><a target="_blank" href="<?php echo esc_url( $item_url );?>"><?php echo esc_html( $item_name );?></a></span>
                                </div>
                            </div>
                        <?php }
                    }?>
				</div>
			</div>
		</div>
		<?php
		
		$data	            = ob_get_clean();
		$json['type']	    = 'success';
		$json['html']	    = $data;
		$json['message']	= esc_html__('Verification user details','tuturn' );
		wp_send_json($json);
	}
	add_action('wp_ajax_tuturn_view_identity_detail', 'tuturn_view_identity_detail');	
}

/**
 * @Import Users
 * @return {}
 */
if (!function_exists('tuturn_identity_verification')) {
	function  tuturn_identity_verification(){
        global $tuturn_settings;
		//security check
		$do_check = check_ajax_referer('ajax_nonce', 'security', false);
		if ( $do_check == false ) {
			$json['type']       = 'error';
			$json['message']    = esc_html__('Security check failed, this could be because of your browser cache. Please clear the cache and check it again', 'tuturn');
			wp_send_json( $json );
		}

		$json		= array();
		$type		= !empty($_POST['type']) ? $_POST['type'] : '';
		$user_id	= !empty($_POST['user_id']) ? $_POST['user_id'] : 0;
        $post_id	= !empty($_POST['post_id']) ? $_POST['post_id'] : 0;
        $profile_id = tuturn_get_linked_profile_id($user_id);

        if(!empty($profile_id)){

            /* Email to user on approved identity verification */
            $user_info          = get_userdata($user_id);
            $username           = $user_info->display_name;
            $user_email         = $user_info->user_email;

            $emailData                      = array();
            $emailData['user_name']         = $username;
            $emailData['user_email']        = $user_email;
            $emailData['get_logged_in']     = tuturn_dashboard_page_uri($user_id);

            if(!empty($type) && $type === 'approve'){
                update_user_meta($user_id,'identity_verified',1);
                update_post_meta($profile_id,'identity_verified','yes');
                if( !empty($post_id) ){
                    wp_update_post(array('ID' => $post_id,'post_status' => 'publish'));
                }

                /* approved identity verified email */
                if (class_exists('Tuturn_Email_helper')) {
                    if(class_exists('TuturnParentalEmails')){
                        $email_helper   = new TuturnParentalEmails();
                        if ( !empty($tuturn_settings['user_profile_approved_switch'])){
                            $email_helper->user_email_approved_identification($emailData);
                        }
                    }
                }

            } else {
                $reason	= !empty($_POST['reason']) ? $_POST['reason'] : '';
                update_user_meta($user_id,'identity_verified',0);
                update_user_meta($user_id,'verification_attachments','');
                update_post_meta($profile_id,'identity_verified','no');
                
                if( !empty($post_id) ){
                    wp_update_post(array('ID' => $post_id,'post_status' => 'pending'));
                    update_user_meta($user_id,'identity_decline_id',$post_id);
                    update_post_meta($post_id,'verification_decline_reason',$reason);
                }

                /* rejected identity verified email */
                $emailData['reject_reason']     = $reason;
                if (class_exists('Tuturn_Email_helper')) {
                    if(class_exists('TuturnParentalEmails')){
                        $email_helper   = new TuturnParentalEmails();
                        if ( !empty($tuturn_settings['user_profile_rejected_switch'])){
                            $email_helper->user_email_reject_identification($emailData);
                        }
                    }
                }

            }
            $json['type']		= 'success';	
            $json['message']	= esc_html__('Settings have been updated','tuturn' );
            wp_send_json( $json );
        } else {
            $json['type']		= 'error';	
            $json['message']	= esc_html__('Something went wrong.','tuturn' );
            wp_send_json( $json );
        }
	}
	add_action('wp_ajax_tuturn_identity_verification', 'tuturn_identity_verification');	
}

/**
 * Approve and disapprove users
 *
 * @since    1.0.0
 * @return	void
 */
if (!function_exists('tuturn_approve_profile')) {
    add_action('wp_ajax_tuturn_approve_profile', 'tuturn_approve_profile');	
    function tuturn_approve_profile(){

        $json           = array();
        $notifyData     = array();
		$notifyDetails  = array();
        //security check
        if (!wp_verify_nonce($_POST['security'], 'ajax_nonce')) {
            $json['type']           = 'error';
            $json['message']        = esc_html__('Oops!', 'tuturn');
            $json['message_desc']   = esc_html__('Security check failed, this could be because of your browser cache. Please clear the cache and check it again', 'tuturn');
            wp_send_json( $json );
        }

        // get post data
        $user_profile_id	= !empty( $_POST['id'] ) ? intval($_POST['id']) : '';
        $type				= !empty( $_POST['type'] ) ? sanitize_text_field($_POST['type']) : '';
        $user_id			= !empty( $_POST['user_id'] ) ? intval($_POST['user_id']) : '';

        // validate post data
        if ( empty( $type ) || empty( $user_id ) ){
            $json['type']           = 'error';
            $json['message']        = esc_html__('Oops!', 'tuturn');
            $json['message_desc']   = esc_html__('Some data has ben lost, please try again', 'tuturn');
            wp_send_json($json);
        }

        // validate user
        $user_meta	= get_userdata($user_id);
        if( empty( $user_meta ) ){
            $json['type']           = 'error';
            $json['message']        = esc_html__('Oops!', 'tuturn');
            $json['message_desc']   = esc_html__('User not exists', 'tuturn');
            wp_send_json($json);
        }

        $is_user_verify = ($type == 'reject' ? 'no' : 'yes');
        update_user_meta($user_id, '_is_verified', $is_user_verify);
        update_user_meta($user_id, 'confirmation_key', '');
        $linked_profile_id  = get_user_meta($user_id, '_linked_profile', true);
        if (!empty($linked_profile_id)){
            update_post_meta($linked_profile_id, '_is_verified', $is_user_verify);
        }

        $notifyData['receiver_id']  = $user_id;
		if(!empty($type) && $type == 'reject'){
			$notifyData['type']     = 'reject_account_request';
		} else {
			$notifyData['type']     = 'approved_account_request';
		}
        $user_type		                = apply_filters('tuturn_get_user_type', $user_id);
        $notifyData['linked_profile']	= $linked_profile_id;
        $notifyData['user_type']		= $user_type;
        $notifyData['post_data']		= $notifyDetails;

        do_action('tuturn_notification_message', $notifyData );

        $full_name  = !empty($user_meta->display_name) ? $user_meta->display_name : 'Subscriber';
        $email      = !empty($user_meta->user_email) ? $user_meta->user_email : '';

        //Send email to user on account approved/reject
        if (class_exists('tuturn_Email_helper')) {
            $blogname               = get_option('blogname');
            $emailData              = array();
            $emailData['name']      = $full_name;
            $emailData['email']     = $email;
            $emailData['sitename']  = $blogname;
            if (class_exists('TuturnAccount')) {
                $emailHelper = new TuturnAccount();
                if($type != 'reject'){
                    $emailHelper->user_approve_account($emailData);
                } else{
                    $emailHelper->user_reject_account($emailData);
                }
            }
        }

        $current_state = ($type == 'reject' ? esc_html__('unverified', 'tuturn') : esc_html__('approved', 'tuturn'));
        $json['type'] 			= 'success';
        $json['message']		= esc_html__('Woohoo!', 'tuturn');
        $json['message_desc']	= wp_sprintf(esc_html__('Account has been %s and email has been sent to user.', 'tuturn'), $current_state);
        wp_send_json($json);
    }
}

/**
 * @Get WP Guppy Pro
 * @type load
 */
if (!function_exists('tuturn_wp_uppy_pro_admin_notices_list')) {
    add_action( 'admin_notices', 'tuturn_wp_uppy_pro_admin_notices_list' );
    function tuturn_wp_uppy_pro_admin_notices_list() {
        if ( isset( $_GET['dismiss-guppy'] ) && check_admin_referer( 'guppy-dismiss-' . get_current_user_id() ) ) {
			update_user_meta( get_current_user_id(), 'guppy_dismissed_notice', 1 );
		}

		if(!is_plugin_active('wp-guppy/wp-guppy.php') && get_user_meta(get_current_user_id(), 'guppy_dismissed_notice', true) == false){?>
            <div class="notice notice-success wp-guppy-admin-notice">
                <p><strong><?php esc_html_e( 'WP Guppy Pro - A live chat plugin is compatible with Tuturn - Online tuition and tutor marketplace', 'tuturn' ); ?></strong></p>
                <p><a class="button button-primary" target="_blank" href="https://codecanyon.net/item/wpguppy-a-live-chat-plugin-for-wordpress/34619534?s_rank=1"><?php esc_html_e( 'Get WP Guppy Pro', 'tuturn' ); ?></a>
                    <?php echo '<a href="' . esc_url( wp_nonce_url( add_query_arg( 'dismiss-guppy', 'dismiss_admin_notices' ), 'guppy-dismiss-' . get_current_user_id() ) ) . '" class="notice dismiss-notice button-secondary" >'.esc_html__('Dismiss','tuturn').'</a>';?>
                </p>
            </div>
            <?php
        }
    }
}


/**
 * @Rewrite sligs
 * @type load
 */
if( !class_exists('TuturnCustomSetting')){
    class TuturnCustomSetting {

        function __construct() {	
        
            add_action( 'load-options-permalink.php', array($this,'tuturn_save_settings') ); 
            add_action( 'admin_init', array($this,'tuturn_setting_init') );
            add_action('init', array($this,'tuturn_set_custom_rewrite_rule'));
        }

        //Setup post types
        function tuturn_get_post_types(  ) {
            $list	= array(
                'tuturn-instructor'	    => esc_html__('Instructors','tuturn'),
                'tuturn-student'	    => esc_html__('Students','tuturn'),
            );

            $list 	= apply_filters('tuturn_filter_get_post_types',$list);
            return $list;
        }
        
        //Rewrite post types
        function tuturn_set_custom_rewrite_rule() {
            global $wp_rewrite;
            $settings 				= $this->tuturn_get_post_types();
            $tuturn_rewrit_url      = get_option( 'tuturn_rewrit_url' );
            if( !empty( $settings ) ){
                foreach ( $settings as $post_type => $name ) {
                    $db_slug	= !empty($tuturn_rewrit_url[$post_type]) ? $tuturn_rewrit_url[$post_type] : '';
                    if(!empty( $post_type ) && !empty($db_slug) ){
                        $args = get_post_type_object($post_type);
                        if( !empty( $args ) ){
                            $args->rewrite["slug"] = $db_slug;
                            register_post_type($args->name, $args);
                        }
                    }
                }
            }
            $wp_rewrite->flush_rules();
        } 

        //Save settings 
        function tuturn_save_settings() {
            if( isset( $_POST['tuturn_rewrit_url'] ) ) {
                update_option( 'tuturn_rewrit_url', ( $_POST['tuturn_rewrit_url'] ) );
            }

        }


        function tuturn_settings_field_callback($arg=array()) {
            $tuturn_rewrit_url     = get_option( 'tuturn_rewrit_url' );	
            $name                   = !empty($arg['name']) ? $arg['name'] : '';
            $value                  = !empty($tuturn_rewrit_url[$name]) ? $tuturn_rewrit_url[$name] : '';
            echo do_shortcode('<input type="text" value="' . esc_attr( $value ) . '" name="tuturn_rewrit_url['.do_shortcode($name).']" id="tb-'.esc_attr($name).'" class="regular-text" />');
        }

        function tuturn_custom_setting_section_form(){}
        
        function tuturn_setting_init(){
            add_settings_section(
                'tuturn_custom_setting_section',
                esc_html__('Rewrite tuturn post type URL(s)','tuturn'),
                array($this,'tuturn_custom_setting_section_form'),
                'permalink'
            );
            $post_types = $this->tuturn_get_post_types();
            if( !empty($post_types) ){
                foreach($post_types as $key => $value ){
                    add_settings_field(
                        $key, 
                        $value, 
                        array($this,'tuturn_settings_field_callback'), 
                        'permalink', 
                        'tuturn_custom_setting_section',
                        array(
                            'name'      => $key
                        )
                    );
                }
            }
            
        }
    }
    new TuturnCustomSetting();
}