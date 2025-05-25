<?php

/**
 * 
 * Class 'Tuturn_Identity_Verification' defines the cusotm post type
 * 
 * @package     Tuturn
 * @subpackage  Tuturn/admin/cpt
 * @author      Amentotech <info@amentotech.com>
 * @link        http://amentotech.com/
 * @version     1.0
 * @since       1.0
 */

if (!class_exists('Tuturn_Identity_Verification')) {
    class Tuturn_Identity_Verification
    {

        /**
         * @access  public
         * @Init Hooks in Constructor
         */
        public function __construct()
        {
            add_action('init', array(&$this, 'init_post_type'));
            add_action('add_meta_boxes',    array($this, 'tuturn_identity_verification_add_meta_box'));
            add_filter('manage_user-verification_posts_columns', array(&$this, 'user_verification_columns_add'));
            add_action('manage_user-verification_posts_custom_column', array(&$this, 'user_verification_columns'), 10, 2);
        }

        /**
         * @Prepare Columns
         * @return {post}
         */
        public function user_verification_columns_add($columns)
        {
            $columns['parent_request_status']   = esc_html__('Parent request status', 'tuturn');
            $columns['tu_varifiled']            = esc_html__('Identity verification', 'tuturn');
            return $columns;
        }

        /**
         * @Get Columns
         * @return {}
         */
        public function user_verification_columns($case)
        {
            global $post;
            $parent_verification    = get_post_meta($post->ID, 'parent_verification', true);
            $parent_verification    = !empty($parent_verification) ? $parent_verification : '';
            $user_id                = !empty($post->post_author) ? $post->post_author : 0;
            $status_text            = '';

            if (empty($parent_verification)) {
                $status_text    = '--';
            } elseif ($parent_verification === 'no') {
                $status_text    = esc_html__('Pending', 'tuturn');
            } elseif ($parent_verification === 'yes') {
                $status_text    = esc_html__('Approved', 'tuturn');
            }

            /* identity verification */
            $identity_verified          = get_user_meta($user_id, 'identity_verified', true);
            $identity_status            = !empty($identity_verified) ? 'approved' : 'inprogress';
            $post_status                = !empty($post->post_status) ? $post->post_status : '';

            if (!empty($post_status)) {
                if ($post_status === 'draft') {
                    $post_status_text   = esc_html__('Pending', 'tuturn');
                    $icon_class         = 'tu-icon-color-red';
                } elseif ($post_status === 'pending' || $post_status === 'rejected') {
                    $post_status_text  = esc_html__('Rejected', 'tuturn');
                    $icon_class         = 'tu-icon-color-red';
                } elseif ($post_status === 'publish') {
                    $post_status_text  = esc_html__('Approved', 'tuturn');
                    $icon_class         = 'tu-icon-color-green';
                }
            }
            $verify_link = '<a title="Identity verification" data-post_id="' . intval($post->ID) . '" class="do_verify_identity dashicons-before '. esc_attr($icon_class) .'" data-type="' . $identity_status . '" data-id="' . intval($user_id) . '" href="#"><span class="dashicons dashicons-id"></span>' . $post_status_text . '</a>';

            switch ($case) {
                case 'parent_request_status':
                    echo esc_html($status_text);
                    break;
                case 'tu_varifiled':
                    echo do_shortcode($verify_link);
                    break;
            }
        }

        /**
         * Adds the meta box container.
         */
        public function tuturn_identity_verification_add_meta_box($post_type)
        {
            add_meta_box(
                'tuturn_identity_verification',
                esc_html__('Identity verification form content', 'tuturn'),
                array($this, 'tuturn_identity_verification_fields'),
                'user-verification',
                'advanced',
                'high'
            );
            add_meta_box(
                'tuturn_update_identity_verification',
                esc_html__('Identity verification', 'tuturn'),
                array($this, 'tuturn_update_identity_verification_fields'),
                'user-verification',
                'side',
                'high'
            );
        }

        public function tuturn_update_identity_verification_fields($post)
        {
            global $tuturn_settings;
            $val            = '';
            $post_status    = !empty($post->post_status) ? $post->post_status : '';
            $user_id        = !empty($post->post_author) ? $post->post_author : 0;
            $post_id        = !empty($post->ID) ? $post->ID : 0;
            $is_verified     = get_user_meta($user_id, '_is_verified', true);

            $identity_verification    = !empty($tuturn_settings['identity_verification']) ? $tuturn_settings['identity_verification'] : false;
            $status                    = (isset($is_verified) && $is_verified === 'yes') ? 'reject' : 'approve';
            $status_text            = (isset($is_verified) && $is_verified === 'yes') ? esc_html__('Reject', 'tuturn') : esc_html__('Approve', 'tuturn');
            if (!empty($identity_verification)) {
                $identity_verified          = get_user_meta($user_id, 'identity_verified', true);
                $identity_status            = !empty($identity_verified) ? 'approved' : 'inprogress';
                $identity_text              = '';
                if (!empty($post_status)) {
                    if ($post_status === 'draft') {
                        $identity_text  = esc_html__('Pending', 'tuturn');
                    } elseif ($post_status === 'pending' || $post_status === 'rejected') {
                        $identity_text  = esc_html__('Rejected', 'tuturn');
                    } elseif ($post_status === 'publish') {
                        $identity_text  = esc_html__('Approved', 'tuturn');
                    }
                }
                $val .= "<a title='" . esc_html__('Identity verification', 'tuturn') . "'   data-post_id='" . intval($post_id) . "' class='do_verify_identity dashicons-before " . ((!empty($identity_verified)) ? 'tu-icon-color-green' : 'tu-icon-color-red') . " ' data-type='" . $identity_status . "' data-id='" . intval($user_id) . "' href='#' ><span class='dashicons dashicons-id'></span>" . esc_html($identity_text) . "</a>";
            }
            echo do_shortcode($val);
        }
        /**
         * Render Meta Box content.
         *
         * @param WP_Post $post The post object.
         */
        public function tuturn_identity_verification_fields($post)
        {
            $verification_info  = !empty($post->ID) ? get_post_meta($post->ID, 'verification_info', true) : array();
            $verification_info  = !empty($verification_info) ? $verification_info : array();
            $basic              = !empty($verification_info['info']) ? $verification_info['info'] : array();
            $post_status        = !empty($post->post_status) ? $post->post_status : '';

            $parent_verification    = get_post_meta($post->ID, 'parent_verification', true);
            $parent_verification    = !empty($parent_verification) ? $parent_verification : '';
            $status_text            = '';
            if (empty($parent_verification)) {
                $status_text    = '--';
            } else if ($parent_verification === 'no') {
                $status_text    = esc_html__('Pending', 'tuturn');
            } else if ($parent_verification === 'yes') {
                $status_text    = esc_html__('Approved', 'tuturn');
            }
        ?>
            <div class="col-sm-12">
                <ul class="tu-package-list">
                    <?php if (isset($status_text)) { ?>
                        <li>
                            <h6><?php esc_html_e('Parent confirmation status', 'tuturn'); ?></h6>
                            <span><?php echo esc_html($status_text); ?></span>
                        </li>
                    <?php } ?>
                    <?php if (!empty($basic['name'])) { ?>
                        <li>
                            <h6><?php esc_html_e('Name', 'tuturn'); ?></h6>
                            <span><?php echo esc_html($basic['name']); ?></span>
                        </li>
                    <?php } ?>
                    <?php if (!empty($basic['phone_number'])) { ?>
                        <li>
                            <h6><?php esc_html_e('Phone', 'tuturn'); ?></h6>
                            <span><?php echo esc_html($basic['phone_number']); ?></span>
                        </li>
                    <?php } ?>
                    <?php if (!empty($basic['email_address'])) { ?>
                        <li>
                            <h6><?php esc_html_e('Email address', 'tuturn'); ?></h6>
                            <span><?php echo do_shortcode($basic['email_address']); ?></span>
                        </li>
                    <?php } ?>
                    <?php if (!empty($basic['contact_number'])) { ?>
                        <li>
                            <h6><?php esc_html_e('Contact number', 'tuturn'); ?></h6>
                            <span><?php echo esc_html($basic['contact_number']); ?></span>
                        </li>
                    <?php } ?>
                    <?php if (!empty($basic['student_number'])) { ?>
                        <li>
                            <h6><?php esc_html_e('Student number', 'tuturn'); ?></h6>
                            <span><?php echo esc_html($basic['student_number']); ?></span>
                        </li>
                    <?php } ?>
                    <?php if (!empty($basic['parent_name'])) { ?>
                        <li>
                            <h6><?php esc_html_e('Parent name', 'tuturn'); ?></h6>
                            <span><?php echo esc_html($basic['parent_name']); ?></span>
                        </li>
                    <?php } ?>
                    <?php if (!empty($basic['parent_phone'])) { ?>
                        <li>
                            <h6><?php esc_html_e('Parent phone', 'tuturn'); ?></h6>
                            <span><?php echo esc_html($basic['parent_phone']); ?></span>
                        </li>
                    <?php } ?>
                    <?php if (!empty($basic['parent_email'])) { ?>
                        <li>
                            <h6><?php esc_html_e('Parent email', 'tuturn'); ?></h6>
                            <span><?php echo esc_html($basic['parent_email']); ?></span>
                        </li>
                    <?php } ?>
                    <?php if (!empty($basic['school'])) { ?>
                        <li>
                            <h6><?php esc_html_e('School', 'tuturn'); ?></h6>
                            <span><?php echo esc_html($basic['school']); ?></span>
                        </li>
                    <?php } ?>
                    <?php if (!empty($basic['gender'])) { ?>
                        <li>
                            <h6><?php esc_html_e('Gender', 'tuturn'); ?></h6>
                            <span><?php echo ucfirst($basic['gender']); ?></span>
                        </li>
                    <?php } ?>
                    
                    <?php if (!empty($basic['verification_number'])) { ?>
                        <li>
                            <h6><?php esc_html_e('CNIC/Passport/NIN/SSN', 'tuturn'); ?></h6>
                            <span><?php echo ($basic['verification_number']); ?></span>
                        </li>
                    <?php } ?>

                    <?php if (!empty($basic['address'])) { ?>
                        <li>
                            <h6><?php esc_html_e('Address', 'tuturn'); ?></h6>
                            <span><?php echo esc_html($basic['address']); ?></span>
                        </li>
                    <?php } ?>
                    <?php if (!empty($basic['other_introduction'])) { ?>
                        <li>
                            <h6><?php esc_html_e('Other introduction', 'tuturn'); ?></h6>
                            <span><?php echo esc_html($basic['other_introduction']); ?></span>
                        </li>
                    <?php } ?>
                    <?php if (!empty($verification_info['personal_photo'])) { ?>
                        <li>
                            <h6><?php esc_html_e('Profile photo', 'tuturn'); ?></h6>
                            <?php foreach ($verification_info['personal_photo'] as $personal_photo) {
                            ?>
                                <span>
                                    <?php if (!empty($personal_photo['attachment_id'])) { ?>
                                        <a href="javascript:" class="wt-download-file-attachment" data-post_id="<?php echo intval($post->ID); ?>" data-attachment_id="<?php echo intval($personal_photo['attachment_id']); ?>"><?php esc_html_e('Download profile photo', 'tuturn'); ?></a>
                                    <?php } ?>
                                </span>
                            <?php } ?>
                        </li>
                    <?php } ?>
                    <?php if (!empty($verification_info['attachments'])) { ?>
                        <li>
                            <h6><?php esc_html_e('Attachments', 'tuturn'); ?></h6>
                            <span>
                                <a href="javascript:" class="wt-download-attachments" data-post_id="<?php echo intval($post->ID); ?>"><?php esc_html_e('Download attachments', 'tuturn'); ?></a>
                            </span>
                        </li>
                    <?php } ?>
                    <?php if (!empty($post_status) && $post_status === 'pending') {
                        $decline_reason = get_post_meta($post->ID, 'verification_decline_reason', true);
                        $decline_reason = !empty($decline_reason) ? $decline_reason : '';
                        if (!empty($decline_reason)) { ?>
                            <li>
                                <h6><?php esc_html_e('Decline reason', 'tuturn'); ?></h6>
                                <span><?php echo esc_html($decline_reason); ?></span>
                            </li>
                    <?php }
                    } ?>
                </ul>
            </div>

<?php
        }

        /**
         * @Init Post Type
         * @return {post}
         */
        public function init_post_type()
        {
            global $tuturn_settings;
            $labels = array(
                'name'                  => esc_html__('Identity verification ', 'tuturn'),
                'singular_name'         => esc_html__('Identity verification', 'tuturn'),
                'menu_name'             => esc_html__('Identity verification', 'tuturn'),
                'name_admin_bar'        => esc_html__('Identity verification', 'tuturn'),
                'archives'              => esc_html__('Identity verification Archives', 'tuturn'),
                'attributes'            => esc_html__('Identity verification Attributes', 'tuturn'),
                'parent_item_colon'     => esc_html__('Parent Identity verification:', 'tuturn'),
                'all_items'             => esc_html__('Identity verification', 'tuturn'),
                'add_new_item'          => esc_html__('Add New Identity verification', 'tuturn'),
                'add_new'               => esc_html__('Add New Identity verification', 'tuturn'),
                'new_item'              => esc_html__('New Identity verification', 'tuturn'),
                'edit_item'             => esc_html__('Edit Identity verification', 'tuturn'),
                'update_item'           => esc_html__('Update Identity verification', 'tuturn'),
                'view_item'             => esc_html__('View Identity verification', 'tuturn'),
                'view_items'            => esc_html__('View Identity verification', 'tuturn'),
                'search_items'          => esc_html__('Search Identity verification', 'tuturn'),
                'not_found'             => esc_html__('Not found', 'tuturn'),
                'not_found_in_trash'    => esc_html__('Not found in Trash', 'tuturn'),
                'featured_image'        => esc_html__('Personal photo', 'tuturn'),
                'set_featured_image'    => esc_html__('Set personal photo', 'tuturn'),
                'remove_featured_image' => esc_html__('Remove personal photo', 'tuturn'),
                'use_featured_image'    => esc_html__('Use as personal photo', 'tuturn'),
                'insert_into_item'      => esc_html__('Insert into Profile', 'tuturn'),
                'uploaded_to_this_item' => esc_html__('Uploaded to this Profile', 'tuturn'),
                'items_list'            => esc_html__('Identity verification list', 'tuturn'),
                'items_list_navigation' => esc_html__('Identity verification list navigation', 'tuturn'),
                'filter_items_list'     => esc_html__('Filter Identity verification list', 'tuturn'),
            );

            $args = array(
                'label'                 => esc_html__('Identity verification', 'tuturn'),
                'description'           => esc_html__('Identity verification', 'tuturn'),
                'labels'                => apply_filters('tuturn_user_verification_cpt_labels', $labels),
                'supports'              => array('title', 'author'),
                'hierarchical'          => false,
                'public'                => false,
                'show_ui'               => true,
                'menu_position'         => 6,
                'menu_icon'             => 'dashicons-admin-users',
                'show_in_admin_bar'     => true,
                'show_in_nav_menus'     => true,
                'can_export'            => true,
                'has_archive'           => true,
                'exclude_from_search'   => false,
                'publicly_queryable'    => false,
                'show_in_rest'          => true,
                'show_in_menu'            => 'edit.php?post_type=tuturn-instructor',
                'rest_base'             => 'user_verification',
                'rest_controller_class' => 'WP_REST_Posts_Controller',
            );

            register_post_type(apply_filters('tuturn_user_verification_post_type_name', 'user-verification'), $args);
        }
    }

    new Tuturn_Identity_Verification();
}
