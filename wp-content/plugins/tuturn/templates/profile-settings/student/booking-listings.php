<?php

/**
 * Student booking listings
 *
 * @package     Tuturn
 * @subpackage  Tuturn/Templates/Profile_Settings/student
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
 */
if (!class_exists('WooCommerce')) {
    do_action('tuturn_woocommerce_install_notice');
    return;
}

if (!empty($args) && is_array($args)) {
    extract($args);
}

$booking_status = !empty($_GET['sort_by']) ? esc_html($_GET['sort_by']) : '';
$booking_date   = !empty($_GET['date']) ? esc_html($_GET['date']) : '';
$service        = !empty($_GET['service']) ? esc_html($_GET['service']) : '';

$args['service']        = $service;
$args['booking_date']   = $booking_date;
$args['booking_status'] = $booking_status;
$posts_per_page = get_option('posts_per_page') ? get_option('posts_per_page') : 10;
$pg_page        = get_query_var('page') ? get_query_var('page') : 1;
$pg_paged       = get_query_var('paged') ? get_query_var('paged') : 1;
$paged          = max($pg_page, $pg_paged);

$order_arg  = array(
    'page'          => $paged,
    'orderby'       => 'date',
    'paginate'      => true,
    'limit'         => $posts_per_page,
    'payment_type'  => 'booking',
);

if (!empty($userType) && $userType === 'student') {
    $order_arg['student_id']  = $user_identity;
} elseif (!empty($user_type) && $userType === 'instructor') {
    $order_arg['instructor_id']  = $user_identity;
}

if (!empty($booking_date)) {
    $order_arg['booking_date']  = date('d-m-Y', strtotime($booking_date));
}

if (!empty($service)) {
    $order_arg['booking_service']  = $service;
}

if ($booking_status == 'pending' || $booking_status == 'completed' || $booking_status == 'declined' || $booking_status == 'publish') {
    $order_arg['booking_status']  = $booking_status;
}

$customer_orders = wc_get_orders($order_arg);

if (!empty($customer_orders->orders)) {
    $args['total_orders']        = count($customer_orders->orders);
}

?>
<div class="tu-bookings">
    <div class="tu-dbwrapper">
        <?php tuturn_get_template('profile-settings/booking-search-form.php', $args); ?>
        <?php if (!empty($customer_orders->orders)) {
            $count_post = count($customer_orders->orders);
            foreach ($customer_orders->orders as $order) {
                $booking_detail = get_post_meta($order->get_id(), 'cus_woo_product_data', true);
                $booking_status = get_post_meta($order->get_id(), 'booking_status', true);
                $booking_date   = get_post_meta($order->get_id(), '_booking_date', true);
                $meeting_detail = get_post_meta( $order->get_id(), 'meeting_detail', true );
                $booking_type   = get_post_meta( $order->get_id(), 'booking_type', true );
                $gmt_time       = current_time('mysql', 1);
                $gmt_time       = date('Y-m-d H:i:s', strtotime($gmt_time));
                $class          = '';

                if (!empty($booking_status) && $booking_status == 'publish') {
                    $status_label   = esc_html__('Ongoing', 'tuturn');
                    $class          = " tu-tagongoing";
                } elseif (!empty($booking_status) && $booking_status == 'completed') {
                    $status_label   = esc_html__('Completed', 'tuturn');
                    $class          = " tu-taggreen";
                } elseif (!empty($booking_status) && $booking_status == 'declined') {
                    $status_label   = esc_html__('Declined', 'tuturn');
                    $class           = " tu-tagdenied";
                } elseif (!empty($booking_status) && $booking_status == 'cancelled') {
                    $status_label   = esc_html__('Cancelled', 'tuturn');
                    $class          = " tu-tagdenied";
                } else {
                    $status_label   = esc_html__('Pending', 'tuturn');
                    $class    = "";
                }

                $booked_data        = !empty($booking_detail['booked_data']) ? $booking_detail['booked_data'] : array();
                $user_information   = !empty($booked_data['information']) ? $booked_data['information'] : array();

                $booking_start_time    = '';
                $booking_end_time    = '';
                $booking_start_lastday_time    = '';
                $booking_end_lastday_time    = '';
                $booking_start_lastday_time    = '';
                $booking_end_lastday_time    = '';
                if(!empty($booked_data['booked_slots'])){                   
                    $booking_start_timeslots    = tuturn_get_appointment_start_time($booked_data['booked_slots'], 'key_first');                   
                    $first_appointment_date     = array_key_first($booked_data['booked_slots']);
                    $booking_start_time         = !empty($booking_start_timeslots['start_time']) ? $first_appointment_date.' '.$booking_start_timeslots['start_time'] : '';
                    $booking_end_time           = !empty($booking_start_timeslots['end_time']) ? $first_appointment_date.' '.$booking_start_timeslots['end_time'] : '';
                    $booking_start_time			= date('Y-m-d H:i:s', strtotime($booking_start_time));
                    $booking_last_timeslots     = tuturn_get_appointment_start_time($booked_data['booked_slots'], 'key_last');                   
                    $last_appointment_date      = array_key_last($booked_data['booked_slots']);
                    $booking_start_lastday_time = !empty($booking_last_timeslots['start_time']) ? $last_appointment_date.' '.$booking_last_timeslots['start_time'] : '';
                    $booking_end_lastday_time   = !empty($booking_last_timeslots['end_time']) ? $last_appointment_date.' '.$booking_last_timeslots['end_time'] : '';
                    $booking_end_lastday_time   = date('Y-m-d H:i:s', strtotime($booking_end_lastday_time));
                } 

                $student_id     = get_post_meta( $order->get_id(), 'student_id',true );
                $instructor_id  = get_post_meta( $order->get_id(), 'instructor_id',true );
                $profile_id     = tuturn_get_linked_profile_id( $instructor_id );
                $profile_id_student     = tuturn_get_linked_profile_id( $student_id );
                $order_total    = !empty($user_type) && $user_type === 'instructor' ? get_post_meta($order->get_id(),'instructor_shares',true  ): $order->get_total();
                     
                $args['student_id']     = $student_id;
                $args['instructor_id']   = $instructor_id;

                if (!empty($user_type) && $user_type === 'instructor' && !empty($payment_type) && $payment_type === 'package') {
                    $order_total  = $order->get_total();
                }
                $order_total  = !empty($order_total) ? $order_total : 0;
                $avatar = apply_filters(
                    'tuturn_avatar_fallback',
                    tuturn_get_user_avatar(array('width' => 100, 'height' => 100), $profile_id),
                    array('width' => 100, 'height' => 100)
                );

                $username   = tuturn_get_username($profile_id);
                ?>
                <div class="tu-bookingwrapper">
                    <div class="tu-bookingperson">
                        <?php if (!empty($avatar)) { ?>
                            <figure>
                                <img src="<?php echo esc_url($avatar); ?>" alt="<?php echo esc_attr(tuturn_get_username($profile_id)); ?>">
                            </figure>
                        <?php } ?>
                        <div class="tu-bookername">
                            <h4><a href="<?php echo esc_url(get_permalink($profile_id)); ?>" target="_blank"><?php echo esc_html(tuturn_get_username($profile_id)); ?></a><span class="tu-tagstatus<?php echo esc_attr($class); ?>"><?php echo esc_html($status_label); ?></span></h4>
                            <?php if($booking_type !== 'without_checkout'){?>
                                <span><?php echo tuturn_price_format($order_total); ?></span>
                            <?php }?>
                        </div>
                    </div>
                    <ul class="tu-bookingonfo">
                        <?php if (!empty($booked_data['booked_slots'])) {
                            foreach ($booked_data['booked_slots'] as $key => $value) { ?>
                                <li>
                                    <span><?php esc_html_e('Appointment date', 'tuturn'); ?>:</span>
                                    <h6><?php echo date_i18n('D j F, Y', strtotime($key)); ?></h6>
                                    <a href="javascript:void(0);" class="tu-bookingdetails" data-user_id="<?php echo intval($instructor_id); ?>" data-booking_id="<?php echo (int)$order->get_id(); ?>"><?php esc_html_e('See booking details', 'tuturn'); ?></a>
                                </li>
                            <?php
                                break;
                            }
                        }
                        if (!empty($user_information)) { ?>
                            <li>
                                <span><?php esc_html_e('Booked for', 'tuturn'); ?>:</span>
                                <ul class="tu-bookedinfo">
                                    <?php if (!empty($user_information['info_full_name'])) { ?>
                                        <li>
                                            <?php echo esc_html($user_information['info_full_name']); ?>
                                            <?php if (!empty($user_information['info_relation'])) { ?>
                                                <em>(<?php esc_html_e('Author\'s', 'tuturn'); ?> <?php echo esc_attr($user_information['info_relation']); ?>)</em>
                                            <?php } ?>
                                        </li>
                                    <?php } ?>
                                    <?php if (!empty($user_information['info_email'])) { ?>
                                        <li><?php echo esc_html($user_information['info_email']); ?></li>
                                    <?php } ?>
                                    <?php if (!empty($user_information['info_phone'])) { ?>
                                        <li><?php echo esc_html($user_information['info_phone']); ?></li>
                                    <?php } ?>
                                    <?php if (!empty($user_information['info_address'])) { ?>
                                        <li><?php echo esc_html($user_information['info_address']); ?></li>
                                    <?php } ?>

                                </ul>
                            </li>
                        <?php } else {
                            $location       = get_post_meta($profile_id_student, '_address', true);
                            $username       = tuturn_get_username($profile_id_student);
                            $user_data      = get_post_meta($profile_id_student, 'profile_details', true);
                            $phone          = !empty($user_data['contact_info']['phone']) ? $user_data['contact_info']['phone'] : '';
                            $email_address  = !empty($user_data['contact_info']['email_address']) ? $user_data['contact_info']['email_address'] : '';
                            ?>
                            <li>
                                <span><?php esc_html_e('Booked for', 'tuturn'); ?>:</span>
                                <ul class="tu-bookedinfo">
                                    <li>
                                        <?php echo esc_html($username); ?>
                                    </li>
                                    <?php if (!empty($email_address)) { ?>
                                        <li><?php echo esc_html($email_address); ?></li>
                                    <?php } ?>
                                    <?php if (!empty($phone)) { ?>
                                        <li><?php echo esc_html($phone); ?></li>
                                    <?php } ?>
                                    <?php if (!empty($location)) { ?>
                                        <li><?php echo esc_html($location); ?></li>
                                    <?php } ?>
                                </ul>
                            </li>
                        <?php }?>
                        
                        <li>
                            <span><?php esc_html_e('Booking ID', 'tuturn'); ?>:</span>
                            <h6><?php echo (int)$order->get_id(); ?></h6>
                        </li>
                        <?php if (!empty($user_information['info_desc'])) { ?>
                            <li>
                                <span><?php esc_html_e('Special comments', 'tuturn'); ?>:</span>
                                <p><?php echo esc_html($user_information['info_desc']); ?></p>
                            </li>
                            
                        <?php } 
                        if(!empty($meeting_detail)){?>
                            <li>
                                <span><?php esc_html_e('Meeting detail:', 'tuturn');?></span>
                                <div class="meeting-detail">
                                    <?php if(!empty($meeting_detail['meeting_url'])){?>
                                        <a href="<?php echo esc_url($meeting_detail['meeting_url'])?>"><?php echo esc_url($meeting_detail['meeting_url']);?></a>
                                    <?php } 
                                    if(!empty($meeting_detail['meeting_desc'])){?>
                                        <p><?php echo esc_html($meeting_detail['meeting_desc']);?></p>
                                    <?php } 
                                    if(!empty($meeting_detail['meeting_date'])){?>
                                        <span><i class="icon icon-calender"></i><?php esc_html_e('Last updated:','tuturn')?> <?php echo esc_html($meeting_detail['meeting_date']);?></span>
                                    <?php } ?>
                                </div>                               
                            </li>
                        <?php } ?>
                    </ul>
                    <?php if ((!empty($booking_status) && ($booking_status == 'pending' || $booking_status == 'publish')) || (apply_filters( 'tuturn_chat_solution_guppy',false ) === true)) { ?>
                        <div class="tu-btnlist">
                            <?php if (!empty($booking_status) && ($booking_status == 'pending' || $booking_status == 'publish')) { ?>
                                <?php if(!empty($booking_status) && $booking_status == 'publish' && (strtotime($booking_end_lastday_time) < strtotime($gmt_time))){?>
                                    <div class="tu-droplist">
                                        <a data-bs-toggle="collapse" href="#collapseExample<?php echo (int)$order->get_id();?>" role="button" aria-expanded="false" aria-controls="collapseExample<?php echo (int)$order->get_id();?>"><?php esc_html_e('Mark appointment as', 'tuturn');?></a>
                                        <ul  id="collapseExample<?php echo (int)$order->get_id();?>" class="collapse tu-dropdownlist">
                                            <li>
                                                <a class="tu-booking-modal" data-order_id="<?php echo (int)$order->get_id();?>" data-action_type="complete" href="javascript:void(0);"><?php esc_html_e('Completed', 'tuturn');?></a>
                                            </li>
                                        </ul>
                                    </div>
                                <?php } elseif(!empty($booking_status) && ($booking_status == 'publish' || $booking_status == 'pending') && (strtotime($booking_end_lastday_time) < strtotime($gmt_time))){?>
                                    <div class="tu-droplist">
                                        <a data-bs-toggle="collapse" href="#collapseExample<?php echo (int)$order->get_id();?>" role="button" aria-expanded="false" aria-controls="collapseExample<?php echo (int)$order->get_id();?>"><?php esc_html_e('Mark appointment as', 'tuturn');?></a>
                                        <ul  id="collapseExample<?php echo (int)$order->get_id();?>" class="collapse tu-dropdownlist">
                                            <li>
                                                <a class="tu-booking-modal" data-order_id="<?php echo (int)$order->get_id(); ?>" data-action_type="complete" href="javascript:void(0);"><?php esc_html_e('Completed', 'tuturn'); ?></a>
                                            </li>
                                        </ul>
                                    </div>
                                <?php } elseif (!empty($booking_status) && ($booking_status == 'publish' || $booking_status == 'pending') && (strtotime($booking_start_time) > strtotime($gmt_time))) { ?>
                                    <div class="tu-droplist">
                                        <a data-bs-toggle="collapse" href="#collapseExample<?php echo (int)$order->get_id();?>" role="button" aria-expanded="false" aria-controls="collapseExample<?php echo (int)$order->get_id();?>"><?php esc_html_e('Mark appointment as', 'tuturn');?></a>
                                        <ul  id="collapseExample<?php echo (int)$order->get_id();?>" class="collapse tu-dropdownlist">
                                            <li>
                                                <a class="tu-sb-sliver tu-booking-modal" data-order_id="<?php echo (int)$order->get_id(); ?>" data-action_type="cancel" href="javascript:void(0);"><?php esc_html_e('Cancelled', 'tuturn'); ?></a>
                                            </li>
                                        </ul>
                                    </div>

                                <?php } ?>
                                
                            <?php } ?>
                            <?php do_action('tuturn_guppy_message', $args); ?>
                        </div>
                    <?php } ?>
                    <?php if (!empty($booking_status) && ($booking_status == 'declined' || $booking_status == 'cancelled')) {
                        $decline_reason = get_post_meta($order->get_id(), 'decline_reason', true);
                        $decline_description = get_post_meta($order->get_id(), 'decline_description', true);
                        if (!empty($decline_reason) || !empty($decline_description)) { ?>
                            <div class="tu-decnoti">
                                <div class="tu-showdetails">
                                    <a class="tu_show_it" href="javascript:void(0);"><?php esc_html_e('Show booking details', 'tuturn'); ?> <i class="icon icon-chevron-down"></i> </a>
                                </div>
                                <div class="tu-noservices">
                                    <?php if (!empty($decline_reason)) { ?>
                                        <h5><?php echo esc_html($decline_reason); ?></h5>
                                    <?php } ?>
                                    <?php if (!empty($decline_description)) { ?>
                                        <p><?php echo esc_textarea($decline_description); ?></p>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
            <?php }
        } else { ?>
            <div class="tu-bookings tu-booking-epmty-field">
                <h4><?php esc_html_e('Uh ho! No bookings available to show', 'tuturn'); ?></h4>
                <p><?php esc_html_e('We\'re sorry but there is no bookings available to show today', 'tuturn'); ?></p>
            </div>
        <?php } ?>
    </div>
    <?php if (!empty($customer_orders->total) && $customer_orders->total > $posts_per_page) {       
         tuturn_paginate($customer_orders);
    }?>
</div>
<?php tuturn_get_template_part('profile-settings/'.$userType.'/booking', 'js-templates', $args);?>
<?php
$script = "
let datecallback = function (){
    jQuery('#booking-search-form').submit();
}
jQuery(document).on('ready', function(){
    initDatePicker('tu-booking-picker', 'YYYY-MM-DD', true, datecallback);
    jQuery(document).on('change', '#booking-search-form .tu-booking-search-field', function (e) {
        jQuery('#booking-search-form').submit();
    });
    jQuery(document).on('click', '.tu-exportdownload', function (e) {
        jQuery('#tu-csvdownload').val('1');
        jQuery('#booking-search-form').submit();
    });
});
";
wp_add_inline_script('tuturn-profile-settings', $script, 'after');
