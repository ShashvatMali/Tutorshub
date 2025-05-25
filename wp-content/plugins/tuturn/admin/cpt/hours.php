<?php

/**
 * 
 * Class 'TuturnSubmitHours' defines the cusotm post type
 * 
 * @package     Tuturn
 * @subpackage  Tuturn/admin/cpt
 * @author      Amentotech <info@amentotech.com>
 * @link        http://amentotech.com/
 * @version     1.0
 * @since       1.0
 */

if (!class_exists('TuturnSubmitHours')) {
    class TuturnSubmitHours {

        /**
         * @access  public
         * @Init Hooks in Constructor
         */
        public function __construct() {
            
            add_action('init', array(&$this, 'init_post_type'));
            add_filter('manage_volunteer-hours_posts_columns', array(&$this, 'user_volunteer_hours_columns_add'));
		    add_action('manage_volunteer-hours_posts_custom_column', array(&$this, 'user_volunteer_hours_columns'),10, 2);
            add_action('restrict_manage_posts', array(&$this, 'tuturn_restrict_manage_authors'),10, 2);
            add_filter( 'post_row_actions', array(&$this,'tuturn_modify_list_row_actions'), 10, 2 );
            add_action('manage_posts_extra_tablenav', array(&$this,'tuturn_add_extra_button'));
        }

        public function tuturn_add_extra_button($where)
        {
            global $post_type_object;
            $user_id    = !empty($_GET['author_id']) ? intval($_GET['author_id']) : 0;
            if ($post_type_object->name === 'volunteer-hours' && !empty($user_id) && $where === 'top' ) {
                $role           = get_user_meta($user_id,'_user_type',true);
                $hours_array    = array();
                if( !empty($role) && $role === 'student' ){
                    $hours_array    = tuturn_hours_data_by_meta(array(array('key' => 'student_id', 'value' => $user_id )));
                } else if( !empty($role) && $role === 'instructor' ){
                    $hours_array    = tuturn_hours_data_by_meta(array(array('key' => 'instructor_id', 'value' => $user_id )));
                }
                if( !empty($hours_array) ){ ?>
                    <ul class="tu-hours-status">
                        <?php if( isset($hours_array['total']) ){?>
                            <li>
                                <div class="tu-hours-status-items tu-listinginfo">
                                    <span class="tu-total-hours"><i class="icon icon-clock"></i></span>
                                    <p><?php esc_html_e('Total hours','tuturn');?></p>
                                    <h5><?php echo sprintf( esc_html__('%s hrs','tuturn'),$hours_array['total']);?></h5>
                                </div>
                            </li>
                        <?php } ?>
                        <?php if( isset($hours_array['completed']) ){?>
                            <li>
                                <div class="tu-hours-status-items tu-listinginfo">
                                    <span class="tu-approved-hours"><i class="icon icon-check-circle"></i></span>
                                    <p><?php esc_html_e('Approved hours','tuturn');?></p>
                                    <h5><?php echo sprintf( esc_html__('%s hrs','tuturn'),$hours_array['completed']);?></h5>
                                </div>
                            </li>
                        <?php } ?>
                        <?php if( isset($hours_array['pending']) ){?>
                            <li>
                                <div class="tu-hours-status-items tu-listinginfo">
                                    <span class="tu-pending-hours"><i class="icon icon-pie-chart"></i></span>
                                    <p><?php esc_html_e('Pending/decline hours','tuturn');?></p>
                                    <h5><?php echo sprintf( esc_html__('%s hrs','tuturn'),$hours_array['pending']);?></h5>
                                </div>
                            </li>
                        <?php } ?>
                    </ul>
                <?php }
            }
        }
        /**
         * @Prepare Columns
         * @return {post}
         */
        public function tuturn_modify_list_row_actions( $actions, $post) {
            if ( $post->post_type == "volunteer-hours" ) {
                unset($actions['view']);
                unset($actions['preview']);
                unset($actions['edit']);
            }
            return $actions;
        }
        
        /**
         * @Prepare Columns
         * @return {post}
         */
        public function tuturn_restrict_manage_authors($post_type,$which) {
            if (isset($post_type) && post_type_exists($post_type) && in_array(strtolower($post_type), array('volunteer-hours'))) {
                wp_dropdown_users(array(
                    'show_option_all'   => esc_html__('Show all users','tuturn'),
                    'show_option_none'  => false,
                    'name'              => 'author_id',
                    'selected'          => !empty($_GET['author_id']) ? intval($_GET['author_id']) : 0,
                    'role__in'          => array('subscriber'),
                    'include_selected'  => false
                ));
            }
        }

        /**
         * @Prepare Columns
         * @return {post}
         */
        public function user_volunteer_hours_columns_add($columns) {
            unset($columns['author']);
            unset($columns['date']);
            
            $columns['instructor'] 		= esc_html__('Instructor','tuturn');
            $columns['student'] 		= esc_html__('Student','tuturn');
            $columns['submit_date'] 	= esc_html__('Work date','tuturn');
            $columns['start_time'] 		= esc_html__('Start time','tuturn');
            $columns['end_time'] 		= esc_html__('End end','tuturn');
            $columns['total_hours'] 	= esc_html__('Hours','tuturn');
            $columns['post_status'] 	= esc_html__('Status','tuturn');
            $columns['send_reminder'] 	= esc_html__('Parent reminder','tuturn');
            $columns['options'] 	    = esc_html__('Action','tuturn');
            return $columns;
        }

        /**
         * @Get Columns
         * @return {}
         */
        public function user_volunteer_hours_columns($case) {
            global $post;
            $date_format    = get_option( 'date_format' );
            $time_format    = get_option( 'time_format' );
            $send_reminder  = get_post_meta( $post->ID, '_send_reminder',true );
            $hourly_data    = get_post_meta( $post->ID, 'hourly_data',true );
            $send_reminder  = isset($send_reminder) && !empty($send_reminder) && $send_reminder == 1 ? '<span class="dashicons dashicons-yes parent-reminder-sent"></span>' : '<span class="dashicons dashicons-no parent-reminder-pending"></span>';
            $hourly_data    = !empty($hourly_data) ? $hourly_data : array();
            $date           = !empty($hourly_data['date']) ? strtotime($hourly_data['date']) : 0;
            $total_hours    = isset($hourly_data['total_hours']) ? $hourly_data['total_hours'] : 0;
            $decline_reason = isset($hourly_data['decline_reason']) ? $hourly_data['decline_reason'] : '';
            $decline_date   = isset($hourly_data['decline_date']) ? strtotime($hourly_data['decline_date']) : 0;
            $start_time     = isset($hourly_data['start_time']) ? $hourly_data['start_time'] : 0;
            $end_time       = isset($hourly_data['end_time']) ? $hourly_data['end_time'] : 0;
            $post_status    = !empty($post->post_status) ? $post->post_status : '';
            $start_time     = !empty($start_time) ? date_i18n($time_format,strtotime($start_time)) : 0; 
            $end_time       = !empty($end_time) ? date_i18n($time_format,strtotime($end_time)) : 0; 
            $student_id     = isset($hourly_data['student_id']) ? $hourly_data['student_id'] : 0;

            $student_proflie        = tuturn_get_linked_profile_id($student_id,'users','student');
            $avatar_url             = apply_filters( 'tuturn_avatar_fallback', tuturn_get_user_avatar(array('width' => 50, 'height' => 50), $student_proflie), array('width' => 50, 'height' => 50));
            $user_name              = tuturn_get_username($student_proflie);
            $student_image_html     = '<div class="tu-user-img"><img width="50" height="50"  src="'.esc_url($avatar_url).'" alt="'.esc_attr($user_name).'"/></div>';
            $instructor_id          = get_post_meta( $post->ID, 'instructor_id',true );
            $instructor_id          = !empty($instructor_id) ? intval($instructor_id) : 0;
            $instructor_proflie     = tuturn_get_linked_profile_id($instructor_id,'users','instructor');
            $instructo_url          = apply_filters( 'tuturn_avatar_fallback', tuturn_get_user_avatar(array('width' => 50, 'height' => 50), $instructor_proflie), array('width' => 50, 'height' => 50));
            $instructor_name        = tuturn_get_username($instructor_proflie);
            $instructor_image_html  = '<div class="tu-user-img"><img width="50" height="50" src="'.esc_url($instructo_url).'" alt="'.esc_attr($user_name).'"/></div>';
            wp_enqueue_style( 'tuturn-lightbox');
            wp_enqueue_script( 'tuturn-lightbox');
            $download_html          = '';
            $view_details           = '';
            
            if( !empty($hourly_data['documents']['attachments']) ){
                $download_html      = '<a href="javascript:;" data-post_id="'.intval($post->ID).'" class="tu-secbtn tu_download_zip_file">'.esc_html__('Download attachments','tuturn').'</a>';

                $download_html .= '<ul class="attachment-wrapper">';
                foreach($hourly_data['documents']['attachments'] as $at_key => $at_val){
                                      
                    $filetype       = wp_check_filetype($at_val['name']);
                    $allowed_types	= array('png','jpg','jpeg','gif','jfif');

                    if(!empty($filetype['ext']) && in_array($filetype['ext'],$allowed_types)){
                        $attachment_id = $at_val['attachment_id'];
                        
                        $download_html .= '<li class="tu-attachment-listing tu-user-img"><a data-lightbox="example-'.$post->ID.'" data-title="'.get_the_title($post->ID).'" href="'.$at_val['url'].'">'.wp_get_attachment_image( $attachment_id, 'tu_profile_thumbnail' ).'</a></li>';
                    }else{
                        $download_html .= '<li class="tu-attachment-listing tu-user-img"><a target="_blank" href="'.$at_val['url'].'"><i class="dashicons dashicons-media-document"></i></a></li>';
                    }
                    
                }
                $download_html .= '</ul>';
            }
            
            $post_content           = '';
            if( !empty($post->post_content) ){
                $post_content       .= '<b>'.esc_html__( 'Description:','tuturn').'</b><p>'.esc_html($post->post_content).'</p>';
            }

            if( !empty($decline_reason) && $post_status === 'decline' ){
                $post_content       .= '<b>'.esc_html__( 'Decline reason:','tuturn').'</b><p>'.esc_html($decline_reason).'</p>';
                if( !empty($decline_date) ){
                    $post_content   .= '<b>'.esc_html__( 'Decline date:','tuturn').'</b><p>'.wp_date($date_format,$decline_date).'</p>';
                }
            }
            
            if( !empty($post_content) ){
                $view_details   = '<a href="#TB_inline?width=600&height=550&inlineId=tu-model'.intval($post->ID).'" class="thickbox tu-view-details" title="'.esc_attr($instructor_name).' 9999">'.esc_html__('View detail','tuturn').'</a><div id="tu-model'.intval($post->ID).'" style="display:none;"><div class="content-wrapper-log">'.do_shortcode($post_content).'</div></div>';
            }

            switch ($case) {
                case 'send_reminder':
                    echo do_shortcode($send_reminder);
                break;
                case 'submit_date':
                    echo wp_date($date_format,$date);
                break;
                case 'start_time':
                    echo esc_html($start_time);
                break;
                case 'end_time':
                    echo esc_html($end_time);
                break;
                case 'student':
                    echo do_shortcode( $student_image_html);
                    echo esc_html($user_name);
                break;
                case 'instructor':
                    echo do_shortcode( $instructor_image_html);
                    echo esc_html($instructor_name);
                break;
                case 'total_hours':
                    echo esc_html($total_hours);
                break;
                case 'post_status':
                    do_action('tuturn_hourly_post_status',$post_status );
                break;
                case 'options':
                    echo do_shortcode( $view_details );
                    echo do_shortcode( $download_html );
                break;
            }
        }

        /**
         * @Init Post Type
         * @return {post}
         */
        public function init_post_type() {
            global $tuturn_settings;
            
            $instructor_hours_submission    = !empty($tuturn_settings['instructor_hours_submission']) ? $tuturn_settings['instructor_hours_submission'] : false;
            $labels = array(
                'name'                  => esc_html__( 'Volunteer hours ', 'tuturn' ),
                'singular_name'         => esc_html__( 'Volunteer hours', 'tuturn' ),
                'menu_name'             => esc_html__( 'Volunteer hours', 'tuturn' ),
                'name_admin_bar'        => esc_html__( 'Volunteer hours', 'tuturn' ),
                'archives'              => esc_html__( 'Volunteer hours Archives', 'tuturn' ),
                'attributes'            => esc_html__( 'Volunteer hours Attributes', 'tuturn' ),
                'parent_item_colon'     => esc_html__( 'Parent Volunteer hours:', 'tuturn' ),
                'all_items'             => esc_html__( 'Volunteer hours', 'tuturn' ),
                'add_new_item'          => esc_html__( 'Add New Volunteer hours', 'tuturn' ),
                'add_new'               => esc_html__( 'Add New Volunteer hours', 'tuturn' ),
                'new_item'              => esc_html__( 'New Volunteer hours', 'tuturn' ),
                'edit_item'             => esc_html__( 'Edit Volunteer hours', 'tuturn' ),
                'update_item'           => esc_html__( 'Update Volunteer hours', 'tuturn' ),
                'view_item'             => esc_html__( 'View Volunteer hours', 'tuturn' ),
                'view_items'            => esc_html__( 'View Volunteer hours', 'tuturn' ),
                'search_items'          => esc_html__( 'Search Volunteer hours', 'tuturn' ),
                'not_found'             => esc_html__( 'Not found', 'tuturn' ),
                'not_found_in_trash'    => esc_html__( 'Not found in Trash', 'tuturn' ),
                'featured_image'        => esc_html__( 'Personal photo', 'tuturn' ),
                'set_featured_image'    => esc_html__( 'Set personal photo', 'tuturn' ),
                'remove_featured_image' => esc_html__( 'Remove personal photo', 'tuturn' ),
                'use_featured_image'    => esc_html__( 'Use as personal photo', 'tuturn' ),
                'insert_into_item'      => esc_html__( 'Insert into Profile', 'tuturn' ),
                'uploaded_to_this_item' => esc_html__( 'Uploaded to this Profile', 'tuturn' ),
                'items_list'            => esc_html__( 'Volunteer hours list', 'tuturn' ),
                'items_list_navigation' => esc_html__( 'Volunteer hours list navigation', 'tuturn' ),
                'filter_items_list'     => esc_html__( 'Filter Volunteer hours list', 'tuturn' ),
            );
    
            $args = array(
                'label'                 => esc_html__( 'Volunteer hours', 'tuturn' ),
                'description'           => esc_html__( 'Volunteer hours', 'tuturn' ),
                'labels'                => apply_filters('tuturn_volunteer_hours_cpt_labels', $labels),
                'supports'              => array( 'title','author','excerpt','thumbnail'),
                'hierarchical'          => false,
                'public'                => false,
                'show_ui'               => true,
                'capability_type' 		=> 'post',
                'menu_position'         => 6,
                'menu_icon'             => 'dashicons-admin-users',
                'show_in_admin_bar'     => true,
                'show_in_nav_menus'     => true,
                'can_export'            => true,
                'has_archive'           => true,
                'exclude_from_search'   => false,
                'publicly_queryable'    => true,
                'show_in_rest'          => true,
                'show_in_menu'			=> 'edit.php?post_type=tuturn-instructor',
                'rest_base'             => 'volunteer-hours',
                'rest_controller_class' => 'WP_REST_Posts_Controller',
                
            );
            if( !empty($instructor_hours_submission) ){
                register_post_type( apply_filters('tuturn_volunteer_hours_post_type_name', 'volunteer-hours'), $args );
                register_post_status( 'decline',
                array(
                    'label'                     => esc_html__('Decline', 'tuturn'),
                    'public'                    => false,
                    'exclude_from_search'       => false,
                    'show_in_admin_all_list'    => true,
                    'show_in_admin_status_list' => true,
                    'label_count'               => _n_noop( 'Decline <span class="count">(%s)</span>', 'Decline <span class="count">(%s)</span>', 'tuturn' ),
                )
            );
            }
        }

    }

	new TuturnSubmitHours();
}

