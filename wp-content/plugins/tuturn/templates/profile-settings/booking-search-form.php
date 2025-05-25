<?php

/**
 * Dashboard booking search form
 *
 * @package     Tuturn
 * @subpackage  Tuturn/Templates/Profile_Settings
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
 */
if (!class_exists('WooCommerce')) {
    return;
}

if (!empty($args) && is_array($args)) {
    extract($args);
}

$tab    = !empty($_GET['tab']) ? esc_html($_GET['tab']) : '';
$disable_class  = '';
if (empty($total_orders)) {
    $disable_class  = ' tu-bookingdisabled';
}
?>
<form id="booking-search-form" action="<?php echo esc_url($current_page_link); ?>">
    <div class="tu-dbtitle">
        <h3><?php esc_html_e('My bookings', 'tuturn'); ?> <em class="tu-linknotification tu-linknotificationvtwo"><?php do_action('tuturn_new_bookings_count', $args); ?></em></h3>
        <input type="hidden" name="tab" value="<?php echo esc_html($tab); ?>">
        <div class="tu-selectwrapper">
            <span><?php esc_html_e('Sort by', 'tuturn'); ?>:</span>
            <div class="tu-selectv">
                <select name="booking_status" id="tu-sortyby_booking" data-placeholder="<?php esc_attr_e('All status', 'tuturn'); ?>" class="form-control tu-booking-search-field booking-sort-by">
                    <option value="all" <?php if (!empty($booking_status) && $booking_status == 'all') { echo esc_attr('selected');} ?>><?php esc_html_e('All status', 'tuturn'); ?></option>
                    <option value="pending" <?php if (!empty($booking_status) && $booking_status == 'pending') { echo esc_attr('selected');} ?>><?php esc_html_e('Pending', 'tuturn'); ?></option>
                    <option value="publish" <?php if (!empty($booking_status) && $booking_status == 'publish') {echo esc_attr('selected'); } ?>><?php esc_html_e('Ongoing', 'tuturn'); ?></option>
                    <option value="completed" <?php if (!empty($booking_status) && $booking_status == 'completed') { echo esc_attr('selected'); } ?>><?php esc_html_e('Completed', 'tuturn'); ?></option>
                    <option value="declined" <?php if (!empty($booking_status) && $booking_status == 'declined') { echo esc_attr('selected'); } ?>><?php esc_html_e('Declined', 'tuturn'); ?></option>
                </select>
            </div>
        </div>
    </div>
    <ul class="tu-booking-list">
        <li>
            <div class="tu-placeholderholder">
                <div class="tu-calendar tu-selectv2">
                    <input type="text" name="date" value="<?php echo esc_attr($booking_date); ?>" class="datepickerv2 form-control tu-booking-picker tu-booking-search-field" autocomplete="off" placeholder="<?php esc_attr_e('By date', 'tuturn'); ?>">
                </div>
            </div>
        </li>
        <li class="tu-exportbtn<?php echo esc_attr($disable_class); ?>">
            <a href="javascript:void(0)" class="tu-exportdownload">
                <?php esc_html_e('Export', 'tuturn'); ?>
                <span>(<?php esc_html_e('CSV file', 'tuturn'); ?>)</span>
                <i class="icon icon-download"></i>
            </a>
        </li>
    </ul>
    <input type="hidden" name="csvexport" id="tu-csvdownload" value="">
</form>