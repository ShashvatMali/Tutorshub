<?php
/**
 * @package    Tuturn
 * @subpackage Tuturn/public/partials *
 * @link       https://codecanyon.net/user/amentotech/portfolio
 * @since      1.0.0
 *
*/
if (!class_exists('Tuturn_Profile_Menu')) {

    class Tuturn_Profile_Menu {

        protected static $instance = null;
        
        public function __construct() {
        }

		/**
		 * Returns the *Singleton* instance of this class.
		 *
		 * @throws error
		 * @author Amentotech <theamentotech@gmail.com>
		 * @return
		 */
        public static function getInstance() {			
            if (is_null(self::$instance)) {
                self::$instance = new self();
            }
            return self::$instance;
        }

		/**
		 * Returns user profile avatar menu
		 *
		 * @throws error
		 * @author Amentotech <theamentotech@gmail.com>
		 * @return
		 */
		public static function tuturn_get_dashboard_profile_menu() {
			global $tuturn_settings, $current_user;
			$user_identity  	= $current_user->ID;
            $user_type      	= apply_filters('tuturnGetUserType', $user_identity );
			$profile_id			= tuturn_get_linked_profile_id( $user_identity );
			$settings_page		= tuturn_get_page_uri('dashboard');
			$inbox_page			= tuturn_get_page_uri('inbox');
			$package_page		= tuturn_get_page_uri('package_page');
			$profile_settings	= add_query_arg(array('useridentity' => $user_identity, 'tab' => 'personal_details'), $settings_page);	
			$saved_items_page	= add_query_arg(array('useridentity' => $user_identity, 'tab' => 'saved'), $settings_page);			
			$bookings_page		= add_query_arg(array('useridentity' => $user_identity, 'tab' => 'booking-listings'), $settings_page);			
			$invoices_page		= add_query_arg(array('useridentity' => $user_identity, 'tab' => 'invoices'), $settings_page);		
			$booking_option     = !empty($tuturn_settings['booking_option']) ? $tuturn_settings['booking_option'] : 'yes';
			$earing_page_hide   = !empty($tuturn_settings['earing_page_hide']) ? $tuturn_settings['earing_page_hide'] : 'show';
			$invoice_page_hide  = !empty($tuturn_settings['invoice_page_hide']) ? $tuturn_settings['invoice_page_hide'] : 'show';	

			$tuturn_menu_list 	= array(
				'profile'	=> array(
					'title' 	=> esc_html__('View profile', 'tuturn'),
					'class'		=> 'tu-dashboard',
					'icon'		=> 'icon icon-external-link',
					'ref'		=> 'dashboard',
					'mode'		=> 'insights',
					'sortorder'	=> 1,
					'type'		=> 'none',
					'url'		=> get_permalink($profile_id),
				),
				'profile-settings'	=> array(
					'title' 	=> esc_html__('Dashboard', 'tuturn'),
					'class'		=> 'tu-dashboard',
					'icon'		=> 'icon icon-layers',
					'ref'		=> 'dashboard',
					'mode'		=> 'insights',
					'sortorder'	=> 10,
					'type'		=> 'none',
					'url'		=> esc_url($profile_settings),				
				),
				'saveditems'	=> array(
					'title' 	=> esc_html__('Saved items', 'tuturn'),
					'class'		=> 'tu-saveditems',
					'icon'		=> 'icon icon-heart',
					'ref'		=> 'saved',
					'mode'		=> 'listings',
					'sortorder'	=> 50,
					'type'		=> 'student',
					'url'		=> esc_url($saved_items_page),	
				),
				'logout'		=> array(
					'title' 	=> esc_html__('Logout', 'tuturn'),
					'class'		=> 'tu-logout',
					'icon'		=> 'icon icon-power',
					'ref'		=> 'logout',
					'mode'		=> '',
					'sortorder'	=> 90,
					'type'		=> 'none',
					'url'		=> esc_url(wp_logout_url(home_url('/'))),
				),
			);

			if($booking_option !== 'disable'){
				$tuturn_menu_list['bookings']	= array(
					'title' 	=> esc_html__('Bookings', 'tuturn'),
					'class'		=> 'tu-bookings',
					'icon'		=> 'icon icon-calendar',
					'ref'		=> 'price-plans',
					'mode'		=> '',
					'sortorder'	=> 30,
					'type'		=> 'none',
					'url'		=> esc_url($bookings_page),	
				);
			}

			if(!empty($invoice_page_hide) && $invoice_page_hide == 'show'){
				$tuturn_menu_list['invoices']	= array(
					'title' 	=> esc_html__('Invoices', 'tuturn'),
					'class'		=> 'tu-invoices',
					'icon'		=> 'icon icon-credit-card',
					'ref'		=> 'price-plans',
					'mode'		=> '',
					'sortorder'	=> 30,
					'type'		=> 'none',
					'url'		=> esc_url($invoices_page),	
				);
			}

			$package_option			= !empty($tuturn_settings['package_option']) ? $tuturn_settings['package_option'] : false;
			$student_package_option	= !empty($tuturn_settings['student_package_option']) ? $tuturn_settings['student_package_option'] : false;
			
			if(!empty($user_type) && $user_type === 'student'){
				if( !empty($student_package_option) ){				
					$tuturn_menu_list['packages']	= array(
						'title' 	=> esc_html__('Packages', 'tuturn'),
						'class'		=> 'tu-earnings',
						'icon'		=> 'icon icon-package',
						'ref'		=> 'price-plans',
						'mode'		=> '',
						'sortorder'	=> 30,
						'type'		=> 'student',
						'url'		=> esc_url($package_page),
					);
				}
			}else{
				if( !empty($package_option) ){				
					$tuturn_menu_list['packages']	= array(
						'title' 	=> esc_html__('Packages', 'tuturn'),
						'class'		=> 'tu-earnings',
						'icon'		=> 'icon icon-package',
						'ref'		=> 'price-plans',
						'mode'		=> '',
						'sortorder'	=> 30,
						'type'		=> 'instructor',
						'url'		=> esc_url($package_page),
					);
				}
			}
			

			if( apply_filters( 'tuturn_chat_solution_guppy',false ) === true ){
				$tuturn_menu_list['inbox']	= array(
					'title' 	=> esc_html__('Inbox', 'tuturn'),
					'class'		=> 'tu-inbox',
					'icon'		=> 'icon icon-message-square',
					'type'		=> 'none',
					'ref'		=> 'inbox',
					'mode'		=> '',
					'sortorder'	=> 20,
					'url'		=> esc_url($inbox_page),
				);
			}

			$tuturn_menu_list 	= apply_filters('tuturn_filter_dashboard_profile_menu', $tuturn_menu_list);

			return $tuturn_menu_list;
		}

		/**
		 * Generate Menu Link
		 *
		 * @throws error
		 * @author Amentotech <theamentotech@gmail.com>
		 * @return
		 */
        public static function tuturn_profile_menu_link($ref = '', $user_identity = '', $return = false, $mode = '', $id = '') {

			$profile_page	= tuturn_get_page_uri('dashboard');

            if(empty($profile_page)){
                $pages = get_pages(array(
                    'meta_key'		=> '_wp_page_template',
                    'meta_value'	=> 'templates/dashboard.php'
                ));
              
                foreach($pages as $page){
                    $profile_page 	= !empty($page->ID) ? get_permalink((int) $page->ID) : '';
                    break;
                }
            }           

            if ( empty( $profile_page ) ) {
                $permalink = home_url('/');
            } else {
                $query_arg['tab'] = urlencode($ref);
                //mode
                if (!empty($mode)) {
                    $query_arg['mode'] = urlencode($mode);
                }
                //id for edit record
                if (!empty($id)) {
                    $query_arg['id'] = urlencode($id);
                }
                $query_arg['useridentity']	= urlencode($user_identity);
                $permalink	= add_query_arg(
                    $query_arg, esc_url( $profile_page  )
                );
            }

            if ($return) {
                return esc_url_raw($permalink);
            } else {
                echo esc_url_raw($permalink);
            }
        }

		/**
		 * Generate Profile Avatar Image Link
		 *
		 * @throws error
		 * @author Amentotech <theamentotech@gmail.com>
		 * @return
		*/
        public static function tuturn_get_avatar() {
        	global $current_user, $wp_roles, $userdata, $post;
          	$user_identity  = $current_user->ID;
            $user_type      = apply_filters('tuturnGetUserType', $user_identity );
			$profile_id		= tuturn_get_linked_profile_id( $user_identity );
			$avatar			= apply_filters(
				'tuturn_avatar_fallback', tuturn_get_user_avatar(array('width' => 100, 'height' => 100), $profile_id), array('width' => 50, 'height' => 50)
			);

			if (empty($avatar)){				
                $user_dp = TUTURN_DIRECTORY_URI . 'public/images/default-avatar.jpg';
				if($user_type == 'instructor'){

					if(!empty($tuturn_settings['defaul_instructor_profile']['id'])){
						$placeholder_id	    = !empty($tuturn_settings['defaul_instructor_profile']['id']) ? $tuturn_settings['defaul_instructor_profile']['id'] : '';
						$img_atts = wp_get_attachment_image_src($placeholder_id, 'tu_profile_thumbnail');
						$avatar    = !empty($img_atts['0']) ? $img_atts['0'] : '';
					} else {
						$avatar	    = !empty($tuturn_settings['defaul_instructor_profile']['url']) ? $tuturn_settings['defaul_instructor_profile']['url'] : $user_dp;
					}
					
				} elseif($user_type == 'student'){
					if(!empty($tuturn_settings['defaul_student_profile']['id'])){
						$placeholder_id	    = !empty($tuturn_settings['defaul_student_profile']['id']) ? $tuturn_settings['defaul_student_profile']['id'] : '';
						$img_atts = wp_get_attachment_image_src($placeholder_id, 'tu_profile_thumbnail');
						$avatar    = !empty($img_atts['0']) ? $img_atts['0'] : '';
					} else {
						$avatar	    = !empty($tuturn_settings['defaul_student_profile']['url']) ? $tuturn_settings['defaul_student_profile']['url'] : $user_dp;
					}
				}
			}

			if( !empty($user_type) && $user_type === 'administrator'){
				$avatar	= get_avatar_url($user_identity,array('size' => 100));
			}
			
			if (!empty($avatar)){				?>
					<img src="<?php echo esc_url( $avatar );?>" alt="<?php esc_attr_e('User profile', 'tuturn'); ?>">
					<span class="tu-avatar-name"><?php echo esc_html(tuturn_get_username($profile_id));?></span>
				<?php
			}
        }
    }
    new Tuturn_Profile_Menu();
}
