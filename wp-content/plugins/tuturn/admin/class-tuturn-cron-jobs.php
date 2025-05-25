<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://codecanyon.net/user/amentotech/portfolio
 * @since      1.0.0
 *
 * @package    Tuturn
 * @subpackage Tuturn/admin
 */

add_filter('cron_schedules', 'tuturn_expired_booking_cron_schedules');
if (!function_exists('tuturn_expired_booking_cron_schedules')) {
    function tuturn_expired_booking_cron_schedules($schedules = array())
    {
        global $tuturn_settings;
        $expired_booking_interval = !empty($tuturn_settings['expired_booking_complete_days']) ? intval($tuturn_settings['expired_booking_complete_days']) : 1;
        if (!empty($expired_booking_interval)) {
            $schedules['expired_booking_cron'] = array(
                'interval' => $expired_booking_interval * 86400,
                'display' => sprintf(__('Every %d days', 'tuturn'), $expired_booking_interval)
            );
        }

        return $schedules;
    }
}

add_action('admin_init', 'tuturn_add_scheduled_event');
if (!function_exists('tuturn_add_scheduled_event')) {
    function tuturn_add_scheduled_event()
    {
        // Schedule the event if it is not scheduled.
        if (!wp_next_scheduled('tuturn_expired_booking_cron_hook')) {
            wp_schedule_event(time(), 'expired_booking_cron', 'tuturn_expired_booking_cron_hook');
        }
    }
}

add_action('tuturn_expired_booking_cron_hook', 'tuturn_expired_booking_cron_task');
if (!function_exists('tuturn_expired_booking_cron_task')) {
    function tuturn_expired_booking_cron_task()
    {
        global $tuturn_settings;
        $expired_booking_interval = !empty($tuturn_settings['expired_booking_complete_days']) ? intval($tuturn_settings['expired_booking_complete_days']) : 2;

        $order_args = array(
            'post_type'         => 'shop_order',
            'posts_per_page'    => -1,
            'post_status'       => array('wc-completed'),
            'fields'            => 'ids',
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key'       => 'payment_type',
                    'value'     => 'booking',
                    'compare'   => '=',
                ),
                array(
                    'key'       => 'booking_status',
                    'value'     => 'publish',
                    'compare'   => '=',
                )
            ),
        );

        $order_query = get_posts($order_args);
        $total_order = count($order_query);

        if (!empty($order_query) && $total_order > 0) {
            $gmt_time_str       = current_time('mysql', 1);
            foreach ($order_query as $order_id) {
                if (!empty($order_id)) {
                    $booking_detail     = get_post_meta($order_id, 'cus_woo_product_data', true);
                    $booked_data        = !empty($booking_detail['booked_data']) ? $booking_detail['booked_data'] : array();
                    $booked_slots       = !empty($booked_data['booked_slots']) ? $booked_data['booked_slots'] : array();
                    $student_id         = !empty($booking_detail['student_id']) ? $booking_detail['student_id'] : 0;
                    $student_profile    = tuturn_get_linked_profile_id($student_id);
                    $instructor_id      = !empty($booking_detail['instructor_id']) ? $booking_detail['instructor_id'] : 0;
                    $instructor_profile = tuturn_get_linked_profile_id($instructor_id);

                    if (!empty($booked_slots)) {
                        end($booked_slots);
                        $key = key($booked_slots);
                        $booking_additional_date = strtotime($key . " + $expired_booking_interval days");
                        if ($booking_additional_date <= strtotime($gmt_time_str)) {
                            /* update booking status publish to completed */
                            update_post_meta($order_id, 'booking_status', 'completed');

                            /* ratings */
                            $content        = esc_html__('Thank you for the lesson.', 'tuturn');
                            $instructor_profileId = $instructor_profile;
                            $user_id        = $student_id; //student user id
                            $profile_id     = $student_profile; //student profile id
                            $rating         = 5;
                            $userdata       = !empty($user_id)  ? get_userdata($user_id) : array();
                            $user_email     = !empty($userdata) ? $userdata->user_email : '';
                            $user_name      = !empty($userdata) ? $userdata->display_name : '';
                            $time           = current_time('mysql');
                            $comment_data   = array(
                                'comment_post_ID'           => $instructor_profileId,
                                'comment_author'            => $user_name,
                                'comment_author_email'      => $user_email,
                                'comment_author_url'        => 'http://',
                                'comment_content'           => $content,
                                'comment_type'              => 'instructor_reviews',
                                'comment_parent'            => 0,
                                'user_id'                   => $user_id,
                                'comment_date'              => $time,
                                'comment_approved'          => 1,
                            );
                            // insert data
                            $comment_id = wp_insert_comment($comment_data);
                            if (!empty($comment_id)) {
                                update_comment_meta($comment_id, 'rating', $rating);
                                $tu_total_rating        = get_post_meta($instructor_profileId, 'tu_total_rating', true);
                                $tu_total_rating        = !empty($tu_total_rating) ? $tu_total_rating : 0;
                                $tu_review_users        = get_post_meta($instructor_profileId, 'tu_review_users', true);
                                $tu_review_users        = !empty($tu_review_users) ? $tu_review_users : 0;
                                $tu_total_rating        = $tu_total_rating + $rating;
                                $tu_review_users++;
                                $tu_average_rating      = ($tu_total_rating / $tu_review_users);
                                update_post_meta($instructor_profileId, 'tu_average_rating', $tu_average_rating);
                                update_post_meta($instructor_profileId, 'tu_total_rating', $tu_total_rating);
                                update_post_meta($instructor_profileId, 'tu_review_users', $tu_review_users);
                            }
                        }
                    }
                }
            }
        }
        //=
    }
}
