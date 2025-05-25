<?php
/**
 *
 * Class 'Tuturn_Packages_Plan' file upload with permissions
 *
 * @package     Tuturn
 * @subpackage  Tuturn/includes
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
*/
if (!class_exists('Tuturn_Packages_Feilds')){
    class Tuturn_Packages_Feilds{
  
        private static $instance = null;
        public function __construct(){
           //default action
        }

        /**
         * Returns the *Singleton* instance of this class.
         *
         * @return
         * @throws error
         * @author Amentotech <theamentotech@gmail.com>
         */
        public static function getInstance(){
            if (self::$instance==null){
                self::$instance = new Tuturn_Packages_Feilds();
            }
            return self::$instance;
        }

        /**
         * Returns price plan fields.
         *
         * @return
         * @throws error
         * @author Amentotech <theamentotech@gmail.com>
         */
        public static function package_fields(){
            $price_plan_feilds  = array(
                'user_type'  => array(
                    'type'      => 'select',
                    'label'     => esc_html__('User type', 'tuturn'),
                    'options'   => array( 
                        'tutor'     => esc_html__('Tutor', 'tuturn'), 
                        'student'   => esc_html__('Student', 'tuturn'), 
                    ),
                    'description'   => '',
                    'classes'   => 'select-package-type',
                    'front_display' => false,
                    'sort_order'    => 1,
                    'caption_text' => '',   
                ),
                'package_type'  => array(
                    'type'      => 'select',
                    'classes'   => 'tu-package-type',
                    'label'     => esc_html__('Package type', 'tuturn'),
                    'options'   => array( 'days' => esc_html__('Day(s)', 'tuturn'), 'month' => esc_html__('Month(s)', 'tuturn'), 'year' => esc_html__('Year(s)', 'tuturn')),
                    'description'   => '',
                    'front_display' => false,
                    'sort_order'    => 1,
                    'caption_text'  => '',
                    'user_type'     => 'both',   
                ),
                'package_duration'  => array(
                    'type'  => 'text',
                    'label' => esc_html__('Package duration', 'tuturn'),
                    'description'   => '',
                    'front_display' => false,                    
                    'sort_order'    => 20,
                    'caption_text' => '',   
                    'user_type'     => 'both',   
                ),
                'contact_info'  => array(
                    'type'  => 'checkbox',
                    'classes'   => 'tu-tutor-package tu-tutor-pkg',
                    'label' => esc_html__('Contact info', 'tuturn'),
                    'description'   => esc_html__('Allowed to add contact information.', 'tuturn'),
                    'sort_order'    => 30,
                    'front_display' => true,   
                    'caption_text' => '',
                    'user_type'     => 'tutor',            
                ),
                'languages'  => array(
                    'type'  => 'checkbox',
                    'label' => esc_html__('Languages', 'tuturn'),
                    'classes'   => 'tu-tutor-package tu-tutor-pkg',
                    'description'   => esc_html__('Allowed to add languages.', 'tuturn'),
                    'sort_order'    => 40,
                    'front_display' => true,   
                    'caption_text' => '',     
                    'user_type'     => 'tutor',       
                ),
                'featured_profile'  => array(
                    'type'  => 'text',
                    'label' => esc_html__('Featured profile', 'tuturn'),
                    'classes'   => 'tu-tutor-package tu-tutor-pkg',
                    'description'   => esc_html__('Profile will be featured number of days.', 'tuturn'),
                    'sort_order'    => 50,
                    'front_display' => true,       
                    'caption_text' => esc_html__('days', 'tuturn'),    
                    'user_type'     => 'tutor',    
                ),               
                'gallery'  => array(
                    'type'  => 'checkbox',
                    'label' => esc_html__('Gallery', 'tuturn'),
                    'classes'   => 'tu-tutor-package tu-tutor-pkg',
                    'description'   => esc_html__('Allowed to add gallery.', 'tuturn'),
                    'sort_order'    => 60,
                    'front_display' => true,  
                    'caption_text' => '',  
                    'user_type'     => 'tutor',    
                ),
                'education'  => array(
                    'type'  => 'checkbox',
                    'label' => esc_html__('Education', 'tuturn'),
                    'classes'   => 'tu-tutor-package tu-tutor-pkg',
                    'description'   => esc_html__('Allowed to add education.', 'tuturn'),
                    'sort_order'    => 70,
                    'front_display' => true,  
                    'user_type'     => 'tutor',   
                    'caption_text' => '',   
                ),
                'teaching'  => array(
                    'type'  => 'checkbox',
                    'label' => esc_html__('I can teach', 'tuturn'),
                    'classes'   => 'tu-tutor-package tu-tutor-pkg',
                    'description'   => esc_html__('Allowed to teach.', 'tuturn'),
                    'sort_order'    => 80,
                    'front_display' => true, 
                    'caption_text' => '',    
                    'user_type'     => 'tutor',   
                ),
            );            
            return apply_filters('package_fields_filter', $price_plan_feilds);
        }
    }
}
