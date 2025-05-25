<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://codecanyon.net/user/amentotech/portfolio
 * @since      1.0.0
 *
 * @package    Tuturn
 * @subpackage Tuturn/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Tuturn
 * @subpackage Tuturn/public
 * @author     Amento Tech Pvt ltd <info@amentotech.com>
 */
class Tuturn_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		add_filter( 'tuturnGetUserType', array(&$this,'tuturnGetUserType'));		
		add_filter('allowEditProfile', array(&$this, 'allowEditProfile'),10,2);
		add_filter( 'tuturnGetCategories', array(&$this, 'tuturnGetCategories'));
		add_filter( 'tuturnGetSubCategories', array(&$this, 'tuturnGetSubCategories'), 10, 3 );
		add_action('tu_embeded_video', array(&$this, 'embededVideo'),10,2);
		add_action('tuturn_get_user_types', array(&$this, 'tuturn_get_user_types'));
		add_action('tuturn_user_package', array(&$this, 'tuturn_user_package'));
		add_action('wp_head', array($this, 'tuturnCustomCSS'));
		add_action('wp_footer', array($this, 'tuturnCustomJS'));

		if(!empty($_GET['tab']) && $_GET['tab'] == 'invoices' && !empty($_GET['mode']) && $_GET['mode'] == 'detail' ){
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'libraries/dompdf/vendor/autoload.php';
		}
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_register_style( 'tuturn-lightbox',  plugin_dir_url( __FILE__ ) . 'css/lightbox.css', array(), $this->version, 'all' );
		wp_register_style( 'bootstrap', plugin_dir_url( __FILE__ ) . 'css/bootstrap.min.css', array(), $this->version, 'all' );
		wp_register_style( 'feather-icons', plugin_dir_url( __FILE__ ) . 'css/feather.css', array(), $this->version, 'all' );
		wp_register_style( 'fontawesome', plugin_dir_url( __FILE__ ) . 'css/fontawesome/fontawesome.css', array(), $this->version, 'all' );
		wp_register_style( 'jquery-confirm', plugin_dir_url( __FILE__ ) . 'css/jquery-confirm.min.css', array(), $this->version, 'all' );
		wp_register_style( 'select2', plugin_dir_url( __FILE__ ) . 'css/select2.min.css', array(), $this->version, 'all');
		wp_register_style( 'splide', plugin_dir_url( __FILE__ ) . 'css/splide.min.css', array(), $this->version, 'all' );
		wp_register_style( 'mCustomScrollbar', plugin_dir_url( __FILE__ ) . 'css/jquery.mCustomScrollbar.min.css', array(), $this->version, 'all' );
		wp_register_style( 'nouislider', plugin_dir_url( __FILE__ ) . 'css/nouislider.min.css', array(), $this->version, 'all' );
		wp_register_style( 'croppie', 'https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.css', array(), $this->version, 'all' );
		wp_register_style( 'lightpick', plugin_dir_url( __FILE__ ) . 'css/lightpick.css', array(), $this->version, 'all');
		wp_register_style( 'tuturn-styles', plugin_dir_url( __FILE__ ) . 'css/main.css', array(), $this->version, 'all' );	
		wp_register_style( 'tuturn-plugin-rtl', plugin_dir_url( __FILE__ ) . 'css/rtl.css', array(), $this->version, 'all' );	
		wp_enqueue_style( 'bootstrap' );
		wp_enqueue_style( 'feather-icons' );
		wp_enqueue_style( 'fontawesome' );
		wp_enqueue_style( 'jquery-confirm' );		
		wp_enqueue_style( 'select2' );
		wp_enqueue_style( 'splide' );
		wp_enqueue_style( 'mCustomScrollbar' );
		wp_enqueue_style( 'lightpick' );	
		wp_enqueue_style( 'nouislider' );
		wp_enqueue_style( 'tuturn-styles' );
		if(is_rtl()){ wp_enqueue_style( 'tuturn-plugin-rtl' );}
		
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		global $tuturn_settings;

		/* Profile Image */
		$profile_min_img_width			= !empty( $tuturn_settings['prof_min_image_width'] ) ? $tuturn_settings['prof_min_image_width'] : 400;
		$profile_min_img_height			= !empty( $tuturn_settings['prof_min_image_height'] ) ? $tuturn_settings['prof_min_image_height'] : 400;
		$profile_max_image_width		= !empty( $tuturn_settings['prof_mx_image_width'] ) ? $tuturn_settings['prof_mx_image_width'] : 1000;
		$prof_mx_image_height			= !empty( $tuturn_settings['prof_mx_image_height'] ) ? $tuturn_settings['prof_mx_image_height'] : 1000;
		/* Gallery Image */
		$gallery_max_image_width		= !empty( $tuturn_settings['media_gallery_image_max_width'] ) ? $tuturn_settings['media_gallery_image_max_width'] : 1200;
		$gallery_max_image_height		= !empty( $tuturn_settings['media_gallery_image_max_height'] ) ? $tuturn_settings['media_gallery_image_max_height'] : 1200;

		wp_register_script( 'tuturn-lightbox', plugin_dir_url( __FILE__ ) . 'js/lightbox.js', array(), $this->version, false );	
		if(!is_user_logged_in()){
			$enable_social_connect	= !empty($tuturn_settings['enable_social_connect']) ? $tuturn_settings['enable_social_connect'] : '';
			if (!empty($enable_social_connect)) {
				//wp_register_script('google-signin-api-js', 'https://apis.google.com/js/api:client.js?', array('jquery'), $this->version, true);
				wp_register_script('google-signin-api-js', 'https://accounts.google.com/gsi/client', array('jquery'), $this->version, true);
				wp_enqueue_script('google-signin-api-js');
			}
		}

		$google_api_key	= !empty($tuturn_settings['google_map']) ? $tuturn_settings['google_map'] : '';
		if (!empty($google_api_key)) {
            wp_register_script('googleapis', 'https://maps.googleapis.com/maps/api/js?key=' . trim($google_api_key) . '&libraries=places', '', '', true);
        } else {
            wp_register_script('googleapis', 'https://maps.googleapis.com/maps/api/js?sensor=false&libraries=places', '', '', true);
		}
		$gclient_id = '';
		if (!empty($tuturn_settings['enable_social_connect']) && $tuturn_settings['enable_social_connect'] == '1'){
			$gclient_id    = !empty($tuturn_settings['google_client_id']) ? $tuturn_settings['google_client_id'] : '';
		}

		wp_register_script( 'bootstrap', plugin_dir_url( __FILE__ ) . 'js/vendor/bootstrap.min.js', array( 'jquery' ), $this->version, true );
		wp_register_script( 'jquery-confirm', plugin_dir_url( __FILE__ ) . 'js/vendor/jquery-confirm.min.js', array(), $this->version, true );
		wp_register_script( 'appear', plugin_dir_url( __FILE__ ) . 'js/vendor/appear.js', array(), $this->version, true);
		wp_register_script( 'countTo', plugin_dir_url( __FILE__ ) . 'js/vendor/countTo.js', array( 'jquery' ), $this->version, true );
		wp_register_script( 'readmore', plugin_dir_url( __FILE__ ) . 'js/vendor/readmore.js', array( 'jquery' ), $this->version, true );
		wp_register_script( 'splide', plugin_dir_url( __FILE__ ) . 'js/vendor/splide.min.js', array( 'jquery' ), $this->version, true );
		wp_register_script( 'select2', plugin_dir_url( __FILE__ ) . 'js/vendor/select2.min.js', array(), $this->version, true);
		wp_register_script( 'popper', plugin_dir_url( __FILE__ ) . 'js/vendor/popper-core.js', array( 'jquery' ), $this->version, true );
		wp_register_script( 'tippy', plugin_dir_url( __FILE__ ) . 'js/vendor/tippy.js', array( 'jquery' ), $this->version, true );
		wp_register_script( 'mCustomScrollbar', plugin_dir_url( __FILE__ ) . 'js/vendor/jquery.mCustomScrollbar.concat.min.js', array( 'jquery' ), $this->version, true );
		wp_register_script( 'particles', plugin_dir_url( __FILE__ ) . 'js/vendor/particles.min.js', array(), $this->version, true);
		wp_register_script( 'wNumb', plugin_dir_url( __FILE__ ) . 'js/vendor/wNumb.js', array(), $this->version, true);
		wp_register_script( 'nouislider', plugin_dir_url( __FILE__ ) . 'js/vendor/nouislider.min.js', array(), $this->version, true);
		wp_register_script( 'sortable-ui', plugin_dir_url( __FILE__ ) . 'js/vendor/sortable.min.js', array('jquery'), $this->version, true);
		wp_register_script( 'chart', plugin_dir_url( __FILE__ ) . 'js/chart.min.js', array( 'jquery' ), $this->version, true );
		wp_register_script( 'utils-chart', plugin_dir_url( __FILE__ ) . 'js/utils.js', array(), $this->version, true );
		wp_register_script('croppie', 'https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.js', array('jquery', 'wp-util'), $this->version, true);
		wp_register_script( 'moment', plugin_dir_url( __FILE__ ) . 'js/moment.min.js', array( 'jquery' ), $this->version, true );
		wp_register_script( 'lightpick', plugin_dir_url( __FILE__ ) . 'js/lightpick.js', array( 'jquery' ), $this->version, true );
		wp_register_script( 'tuturn-profile-settings', plugin_dir_url( __FILE__ ) . 'js/tuturn-profile-settings.js', array( 'jquery' ), $this->version, true );
		wp_register_script( 'tuturn-public', plugin_dir_url( __FILE__ ) . 'js/tuturn-public.js', array( 'jquery', 'wp-util' ), $this->version, true );
		wp_register_script( 'typed', plugin_dir_url( __FILE__ ) . 'js/typed.min.js', array( 'jquery' ), $this->version, true );

		wp_enqueue_script('googleapis');
		wp_enqueue_script('bootstrap');
		wp_enqueue_script('jquery-confirm');
		wp_enqueue_script('select2');
		wp_enqueue_script('appear');
		wp_enqueue_script('countTo');
		wp_enqueue_script('splide');
		wp_enqueue_script('lightpick');
		wp_enqueue_script('typed');
		wp_enqueue_script('wNumb');
		wp_enqueue_script('nouislider');
		wp_enqueue_script('mCustomScrollbar');
		wp_enqueue_script('popper');
		wp_enqueue_script('tippy');
		wp_enqueue_script('moment');
		wp_enqueue_script('particles');

		if(is_singular( array( 'tuturn-instructor', 'tuturn-student' ) )){
			wp_enqueue_script('particles');
		}

		if(is_page_template( 'templates/profile-settings.php')){
			wp_enqueue_script('sortable-ui');
			wp_enqueue_script('plupload');
			wp_enqueue_style('croppie');
			wp_enqueue_script('croppie');
		}		
		$loginedUser = $profile_id = 0;
		if(is_user_logged_in()){
			$loginedUser	= get_current_user_id();
		}
		$lang = get_locale();
		if ( strlen( $lang ) > 0 ) {
			$lang = explode( '_', $lang )[0];
		}

		$slider_direction   = 'ltr';
		if ( is_rtl() ) {
			$slider_direction   = 'rtl';
		}

		$data = array(
			'ajax_nonce'    => wp_create_nonce('ajax_nonce'),
			'home_url'    	=> home_url( '/' ),
			'userId'		=> $loginedUser,
			'direction'		=> $slider_direction,
			'ajaxurl'       => admin_url( 'admin-ajax.php' ),
			'invalid_image'	=> esc_html__('Something wrong with uploaded image', 'tuturn'),
			'copied'		=> esc_html__('Copied!', 'tuturn'),
			'username'      => esc_html__('Username required.', 'tuturn'),
			'valid_email'   => esc_html__('Valid email required', 'tuturn'),
			'first_name'    => esc_html__('First Name is required', 'tuturn'),
			'last_name'     => esc_html__('Last Name is required', 'tuturn'),
			'user_password' => esc_html__('Password is required', 'tuturn'),
			'user_password_confirm_match'	=> esc_html__('Password and confirm password should be same', 'tuturn'),
			'user_agree_terms'              => esc_html__('You must agree our terms & conditions before signup.', 'tuturn'),
			'upload_size'                   => '50mb',
			'gclient_id'         			=> $gclient_id,
			'select_option'                 => esc_html__('Select an option', 'tuturn'),
			'file_size_error'               => esc_html__('Uh-Oh!', 'tuturn'),
			'error_title'                   => esc_html__('Uh-Oh!', 'tuturn'),
			'file_size_error_title'         => esc_html__('Uh-Oh!', 'tuturn'),			
			'edu_date_error_title'          => esc_html__('Education','tuturn'),
			'load_more'                     => esc_html__('Load more','tuturn'),
			'show_less'                     => esc_html__('Show Less','tuturn'),
			'edu_date_error'                => esc_html__('Please add a vaild dates','tuturn'),
			'upload_max_images'             => esc_html__('Please upload files up to ','tuturn'),
			/* Profile imgae */
			'prof_min_image_width'			=> $profile_min_img_width,
			'prof_min_image_width_msg'		=> wp_sprintf( esc_html__( 'Image width should not be less than %s pixels.', 'tuturn' ), $profile_min_img_width ),
			'prof_min_image_height'			=> $profile_min_img_height,
			'prof_min_image_height_msg'		=> wp_sprintf( esc_html__( 'Image height should not be less than %s pixels.', 'tuturn' ), $profile_min_img_height ),
			'prof_mx_image_width'			=> $profile_max_image_width,
			'prof_mx_image_width_msg'		=> wp_sprintf( esc_html__( 'Image width should not be more than %s pixels.', 'tuturn' ), $profile_max_image_width ),
			'prof_mx_image_height'			=> $prof_mx_image_height,
			'prof_mx_image_height_msg'		=> wp_sprintf( esc_html__( 'Image height should not be more than %s pixels.', 'tuturn' ), $prof_mx_image_height ),
			
			'remove_education'              => esc_html__('Remove education', 'tuturn'),
			'remove_education_message'		=> esc_html__('Are you sure, you want to remove this education?', 'tuturn'),
			'remove_subject'              	=> esc_html__('Remove subject', 'tuturn'),
			'remove_subject_message'		=> esc_html__('Are you sure, you want to remove this subject?', 'tuturn'),
			'remove_subcategory'       		=> esc_html__('Remove subcategory', 'tuturn'),
			'remove_subcat_subject'       	=> esc_html__('Remove subcategory of subject', 'tuturn'),
			'remove_subcat_subject_message'		=> esc_html__('Are you sure, you want to remove this subcategory of subject?', 'tuturn'),
			'approve_hours'              	=> esc_html__('Approve hours', 'tuturn'),
			'cancel_reupload'              	=> esc_html__('Are you sure to cancel and re-upload the verification', 'tuturn'),
			'approve_hours_message'			=> esc_html__('Are you sure, you want to approve these hours?', 'tuturn'),
			'remove_faq'				    => esc_html__('Remove FAQ', 'tuturn'),
			'remove_faq_message'            => esc_html__('Are you sure, you want to remove this FAQ?', 'tuturn'),
			'active_account'    		    => esc_html__('Active account', 'tuturn'),
			'active_account_message'        => esc_html__('Are you sure you want active your account?', 'tuturn'),
			'yes_btntext'    		        => esc_html__('Yes', 'tuturn'),
			'cancel_verification'    		=> esc_html__('Cancel Verfication', 'tuturn'),
			'btntext_cancelled'    		    => esc_html__('Cancel', 'tuturn'),
			'cancel_verification_message'   => esc_html__('Are you sure you want cancel your identity verification?', 'tuturn'),
			'default_image_extensions'      => !empty( $tuturn_settings['default_image_extensions'] ) 		? $tuturn_settings['default_image_extensions'] 		: 'jpg,jpeg,png',
			'default_file_extensions'       => !empty( $tuturn_settings['default_file_extensions'] ) 		? $tuturn_settings['default_file_extensions'] 		: 'pdf,doc,docx',
			'media_gallery_items_limit'		=> !empty($tuturn_settings['media_gallery_items_limit'] ) 	? $tuturn_settings['media_gallery_items_limit'] 	: 5,

			/* gallery images */
			'gallery_max_image_width'		=> $gallery_max_image_width,
			'gallery_max_image_width_msg'	=> wp_sprintf( esc_html__( 'Image width should not be more than %s pixels.', 'tuturn' ), $gallery_max_image_width ),
			'gallery_max_image_height'		=> $gallery_max_image_height,
			'gallery_max_image_height_msg'	=> wp_sprintf( esc_html__( 'Image width should not be more than %s pixels.', 'tuturn' ), $gallery_max_image_height ),

			'upload_max_file_size'			=> !empty($tuturn_settings['upload_max_file_size'] ) 	? $tuturn_settings['upload_max_file_size'] 	: 5,
			'post_author_option'            => esc_html__('You are not allowed to perform this action', 'tuturn'),
			'select_category'               => esc_html__('Select category', 'tuturn'),
			'select_sub_category'           => esc_html__('Select sub category', 'tuturn'),
			'withdraw_agree_term_title'     => esc_html__('Terms & condition', 'tuturn'),
			'withdraw_agree_term_dec'     	=> esc_html__('You must agree our terms & conditions', 'tuturn'),
			'select_sub_category'           => esc_html__('Select sub category', 'tuturn'),
			'required_fields'           	=> esc_html__('Required fields are empty', 'tuturn'),
			'week_days'						=> apply_filters( 'tuturnGetWeekDays',''),
			'close_text'					=> esc_html__('Close', 'tuturn'),
			'remove_hour_log_title'			=> esc_html__('Remove', 'tuturn'),
			'remove_hour_log_desc'			=> esc_html__('Are you sure to remove this log?', 'tuturn'),
			'site_lang'						=> $lang,
			'week_days'						=> tuturnListWeekDays(),

		);

		wp_localize_script('tuturn-public', 'scripts_vars', $data );
		wp_enqueue_script('tuturn-public');
		if(is_page_template( 'templates/profile-settings.php')){
			wp_enqueue_script('tuturn-profile-settings');
		}
	}

	/**
	 * tuturn video render
	 *
	 * @throws error
	 * @author Amentotech <theamentotech@gmail.com>
	 * @return 
	 */
	public function embededVideo( $video = '' ) {
		$vid_width		= 1100;
		$vid_height		= 600;
		$url 			= parse_url( $video );
	
		if( ! empty( $url['host'] ) && ( $url['host'] == 'vimeo.com' || $url['host'] == 'player.vimeo.com') ) {
			$content_exp  = explode("/" , $video);
			$content_vimo = array_pop($content_exp);
			echo '<iframe width="' . esc_attr( $vid_width ) . '" height="' . esc_attr( $vid_height ) . '" src="https://player.vimeo.com/video/' . $content_vimo . '"></iframe>';
		} elseif (!empty($url['host']) && $url['host'] == 'soundcloud.com') {
			$video  = wp_oembed_get($video , array ('height' => $vid_height ));
			$search = array ('webkitallowfullscreen' ,'mozallowfullscreen' ,'frameborder="0"' );
			echo str_replace($search , '' , $video);			
		} else if(!empty($url['host']) && $url['host'] == 'youtu.be') {
			$path	= str_replace('/','',$url['path']);
			echo preg_replace(
				"/\s*[a-zA-Z\/\/:\.]*youtu(be.com\/watch\?v=|.be\/)([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i",
				"<iframe class='tu-slider-video' width='" . esc_attr( $vid_width ) ."' height='" . esc_attr( $vid_height ) . "' src=\"//www.youtube.com/embed/$2\" frameborder='0' webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>",
				$video
			);			
		} else {			
			$content = str_replace(array ( 'watch?v=' ,'http://www.dailymotion.com/' ) , array ( 'embed/', '//www.dailymotion.com/embed/' ) , $video);
			echo '<iframe class="tu-slider-video" width="' . esc_attr( $vid_width ) . '" height="' . esc_attr( $vid_height ) . '" src="' . esc_url( $content ) . '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
		}
	 }


	/**
	 * tuturn user registration types
	 *
	 * @throws error
	 * @author Amentotech <theamentotech@gmail.com>
	 * @return 
	 */
	public function tuturn_get_user_types($defult_role='') {
		$list   = array('tuturn-instructor' => esc_html__('Instructor','tuturn'), 'tuturn-student' => esc_html__('Students','tuturn'));
		$list   = apply_filters('tuturn_filter_get_user_types', $list);
		return $list;
	}
	

	/**
	 * get tuturn user type
	 *
	 * @since    1.0.0
	*/
	public function tuturnGetUserType( $userId ){
		if (!empty($userId)) {
            $user_type = get_user_meta($userId,'_user_type',true);			
            if (!empty($user_type) && $user_type === 'instructor') {
                return 'instructor';
            } else if (!empty($user_type) && $user_type === 'student') {
               return 'student';
            } else if (empty($user_type)) {
				$data = get_userdata( $userId );
				if ( !empty( $data->roles[0] ) && $data->roles[0] == 'administrator') {
					return 'administrator';
				}
			}
        }
        return '';
	}

	/**
	 * verify user
	 *
	 * @since    1.0.0
	*/
	public function allowEditProfile($userId = '', $profileId = '') {
		global $current_user,$post;
		$edit_option		= false;
		if(!empty($post) || !empty($profileId)){
			$profileId			= !empty($profileId) ? $profileId : $post->ID;
			$userId				= !empty($userId) ? $userId : $current_user->ID;
			$userType			= apply_filters('tuturnGetUserType', $userId );
			if(!empty($userType)) {
				if($userType === 'administrator'){
					$edit_option	= true;
				} elseif($userType === 'instructor' && $profileId == tuturn_get_linked_profile_id( $userId )){
					$edit_option	= true;
				} elseif($userType === 'student' && $profileId == tuturn_get_linked_profile_id( $userId )){
					$edit_option	= true;
				}
			}
		}
		return $edit_option;
	}

	/**
	 * Custom CSS
	 *
	 * @since    1.0.0
	 */
	public function tuturnCustomCSS() {
		global $tuturn_settings;
		$custom_css	= !empty($tuturn_settings['custom_css']) ? $tuturn_settings['custom_css'] : '';
		if(!empty($custom_css)){?>
			<style>
				<?php echo do_shortcode($custom_css);?>
			</style>
		<?php }
	}

	/**
	 * Custom CSS
	 *
	 * @since    1.0.0
	 */
	public function tuturnCustomJS() {
		global $tuturn_settings;
		$custom_js	= !empty($tuturn_settings['custom_js']) ? $tuturn_settings['custom_js'] : '';
		if(!empty($custom_js)){?>
			<script>
				<?php echo do_shortcode($custom_js);?>
			</script>
		<?php }
	}

	/**
	 * get sub-categories
	 *
	 * @since    1.0.0
	*/
	public function tuturnGetSubCategories($parent_cat_id = '', $sub_cat_id = '', $id = '')	{  
		if( !empty($parent_cat_id) ){
            $tuturn_args   = array(
                'show_option_none'  => esc_html__('Select sub-category from list', 'tuturn'),
                'show_count'    => false,
                'hide_empty'    => false,
                'name'          => 'sub_categories',
                'class'         => 'form-control',
                'taxonomy'      => 'product_cat',
                'id'            =>  $id,
                'value_field'   => 'slug',
                'orderby'       => 'name',
                'option_none_value' => '',
                'parent'        => $parent_cat_id,
                'hide_if_empty' => false,
                'echo'          => false,
                'required'      => false,
            );
            if(!empty($sub_cat_id)) {
                $tuturn_args['selected']   = $sub_cat_id;
            }
            $sub_categories = wp_dropdown_categories( $tuturn_args );
			return $sub_categories;
        }
  
	}  

	/**
	 * get categories
	 *
	 * @since    1.0.0
	*/
	public function tuturnGetCategories($category_args = array()) {
		$defaults = array(
			'orderby'           => 'id',
			'order'             => 'ASC',
			'show_count'        => 0,
			'hide_empty'        => 0,
			'child_of'          => 0,
			'exclude'           => '',
			'echo'              => 1,
			'selected'          => 0,
			'hierarchical'      => 0,
			'name'              => 'category',
			'id'                => '',
			'class'             => '',
			'depth'             => 0,
			'tab_index'         => 0,
			'taxonomy'          => 'product_cat',
			'hide_if_empty'     => false,
			'option_none_value' => -1,	
			'value_field'       => 'term_id',
			'required'          => false,
		);
		$tuturn_args = wp_parse_args( $category_args, $defaults );
		$categories = wp_dropdown_categories( $tuturn_args );
		return $categories;		
	}

	/**
	 * Package detail
	 *
	 * @return
	 * @throws error
	 * @author Amentotech <theamentotech@gmail.com>
	 */
	function tuturn_user_package($user_id = '')
    {
        global $tuturn_settings;        
        $user_package_details   = array (
            'languages' 	=> true,
            'type' 			=> 'free',
            'gallery' 		=> true,
            'contact_info' 	=> true,
            'education' 	=> true,
            'teaching' 		=> true,
            'education' 	=> true,
			'expired' 		=> false,
			'allowed' 		=> true,
        );

		if (!class_exists('WooCommerce')) {return $user_package_details;}
		
        $package_option  = !empty($tuturn_settings['package_option']) ? 'paid' : 'free';
		
        $user_package_details['type']   = $package_option;

        if($package_option == 'paid'){
            $user_package_details   = array (
                'type' 			=> $package_option,
                'languages'		=> false,
                'contact_info' 	=> false,
                'gallery' 		=> false,
                'education' 	=> false,
                'teaching' 		=> false,
                'expired' 		=> false,
				'allowed' 		=> false,                
            );
            $order_id = get_user_meta($user_id, 'package_order_id', true);

            if (!empty($order_id)) {
                $order = wc_get_order($order_id);

                if ( !empty($order) && 'completed' == $order->get_status()) {
                    $package_details = get_post_meta($order_id, 'package_details', true);
                    $user_package_details['package_create_date']  = !empty($package_details['package_create_date']) ? $package_details['package_create_date'] : '';
                    $expiry_date  = !empty($package_details['package_expriy_date']) ? $package_details['package_expriy_date'] : '';
                    $user_package_details['package_create_date']  = $expiry_date;
                    $current_time       = time();
                    if (strtotime($expiry_date) < $current_time) {
                        $user_package_details['expired']  = true;
                        $user_package_details['allowed']  = false;
                        return $user_package_details;
                    } else {
						$user_package_details['allowed']  = true;

                        if(!empty($package_details['languages']) && $package_details['languages'] == 'yes'){
                            $user_package_details['languages']  = true;
                        }
                        
                        if(!empty($package_details['gallery']) && $package_details['gallery'] == 'yes'){
                            $user_package_details['gallery']  = true;
                        }

                        if(!empty($package_details['education']) && $package_details['education'] == 'yes'){
                            $user_package_details['education']  = true;
                        }

                        if(!empty($package_details['contact_info']) && $package_details['contact_info'] == 'yes'){
                            $user_package_details['contact_info']  = true;
                        }

                        if(!empty($package_details['teaching']) && $package_details['teaching'] == 'yes'){
                            $user_package_details['teaching']  = true;
                        }
                    }
                }
            }
        }
        return apply_filters('tuturn_instructor_package_info', $user_package_details);
    }
}
