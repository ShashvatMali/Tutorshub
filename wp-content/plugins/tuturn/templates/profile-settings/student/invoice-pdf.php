<?php

/**
 * Instructor invoice detail
 *
 * @package     Tuturn
 * @subpackage  Tuturn/Templates/Profile_Settings/Instructor
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
global $tuturn_settings;
$order_id       = !empty($id) ? intval($id) : '';
$pdfDownload    = !empty($_GET['pdfDownload']) ? intval($_GET['pdfDownload']) : '';
/* if order Id empty */
if (empty($order_id)) {
    return;
}

$order              = wc_get_order($order_id);
$data_created       = $order->get_date_created();
$data_created       = date_i18n(get_option('date_format'), strtotime($data_created));
$get_total          = $order->get_total();
$student_id         = get_post_meta($order->get_id(), 'student_id', true);
$instructor_id      = get_post_meta($order->get_id(), 'instructor_id', true);
$instructor_profile_id  = tuturn_get_linked_profile_id($instructor_id);
$profile_details        = get_post_meta($instructor_profile_id, 'profile_details', true);
$store_address          = get_option('woocommerce_store_address');
$user_data              = get_userdata($instructor_id);
$user_data              = !empty($user_data->user_email) ? sanitize_email($user_data->user_email) : '';
$author_name            = !empty($profile_details['name']) ? sanitize_text_field($profile_details['name']) : '';
$instructor_email       = !empty($profile_details['contact_info']['email_address']) ? sanitize_email($profile_details['contact_info']['email_address']) : $user_data;
$author_address         = !empty($profile_details['location']['address']) ? $profile_details['location']['address'] : '';
$author_address         = get_post_meta($instructor_profile_id, '_address', true);
$billing_address    = $order->get_billing_address_1();
$billing_email      = $order->get_billing_email();
$bill_to_name       = $order->get_billing_first_name();
$payment_type       = get_post_meta($order_id, 'payment_type', true);
$payment_type       = !empty($payment_type) ? $payment_type : '';
$total_tax             = $order->get_total_tax();
$total_tax          = !empty($total_tax) ? $total_tax : 0;
$invoice_terms      = !empty($tuturn_settings['invoice_terms']) ? sanitize_text_field($tuturn_settings['invoice_terms']) : '';
$admin_commision    = '';
$refunded           = $order->get_total_refunded();

if (!empty($payment_type) && $payment_type === 'booking') {
    $admin_commision    = !empty($tuturn_settings['admin_commision']) ? $tuturn_settings['admin_commision'] : '';
}

$order_total  = !empty($user_type) && $user_type === 'instructor' ? get_post_meta($order->get_id(), 'instructor_shares', true) : $order->get_total();

if (!empty($user_type) && $user_type === 'instructor' && !empty($payment_type) && $payment_type === 'package') {
    $order_total        = $order->get_total();
    $instructor_email   = sanitize_email(get_option('admin_email'));
    $author_name        = 'admin';
    $author_address     = $store_address;
}
$order_total            = !empty($order_total) ? $order_total : 0;
?>
<div class="tu-instructor-invoice-detail">
    <div class="tu-dbwrapper">
        <h4 style="background-color: #0A0F26; color: #ffffff; text-align:center"><?php esc_html_e('Invoice # ', 'tuturn');
                                                                                    echo intval($order_id); ?></h4>
        <table style="background-color: #0A0F26; border-bottom:1px solid black; border-radius:5px;" border="0" align="center">
            <tr style="color: #ffffff;">
                <?php if (!empty($author_name)) { ?>
                    <th><?php echo esc_html__("Name", "tuturn"); ?></th>
                <?php } ?>
                <?php if (!empty($instructor_email)) { ?>
                    <th><?php echo esc_html__("Email", "tuturn"); ?></th>
                <?php } ?>
                <?php if (!empty($author_address)) { ?>
                    <th><?php echo esc_html__("Address", "tuturn"); ?></th>
                <?php } ?>
            </tr>
            <tr style="color: #ffffff;">
                <?php if (!empty($author_name)) { ?>
                    <td>
                        <?php echo esc_html($author_name); ?>
                    </td>
                <?php } ?>
                <?php if (!empty($instructor_email)) { ?>
                    <td>
                        <?php echo esc_html($instructor_email); ?>
                    </td>
                <?php } ?>
                <?php if (!empty($author_address)) { ?>
                    <td>
                        <?php echo do_shortcode(nl2br($author_address)); ?>
                    </td>
                <?php } ?>
            </tr>
        </table>

        <h4><?php esc_html_e('Billed to', 'tuturn') ?></h4>
        <table border="1" align="center">
            <tr style="font-size: 15px;">
                <?php if (!empty($order->get_formatted_billing_full_name())) { ?>
                    <th>
                        <h6><?php echo esc_html__("Name", "tuturn"); ?></h6>
                    </th>
                <?php } ?>
                <?php if (!empty($data_created)) { ?>
                    <th>
                        <h6><?php echo esc_html__("Date", "tuturn"); ?></h6>
                    </th>
                <?php } ?>
                <?php if (!empty($billing_address)) { ?>
                    <th>
                        <h6><?php echo esc_html__("Address", "tuturn"); ?></h6>
                    </th>
                <?php } ?>
                <?php if (!empty($order_total)) { ?>
                    <th>
                        <h6><?php echo esc_html__("Invoice total amount", "tuturn"); ?></h6>
                    </th>
                <?php } ?>
            </tr>
            <tr>
                <?php if (!empty($order->get_formatted_billing_full_name())) { ?>
                    <td>
                        <h5><?php echo esc_html($order->get_formatted_billing_full_name()); ?></h5>
                    </td>
                <?php } ?>
                <?php if (!empty($data_created)) { ?>
                    <td>
                        <h5><?php echo esc_html($data_created); ?></h5>
                    </td>
                <?php } ?>
                <?php if (!empty($billing_address)) { ?>
                    <td>
                        <h5><?php echo do_shortcode($billing_address) ?></h5>
                    </td>
                <?php } ?>
                <?php if (!empty($order_total)) { ?>
                    <td>
                        <h3><?php echo tuturn_price_format($order_total); ?></h3>
                    </td>
                <?php } ?>
            </tr>
        </table>

        <h4><?php esc_html_e("Invoice summary", "tuturn") ?></h4>
        <table border="1" align="center">
            <tr>
                <th><?php echo esc_html__("Description", "tuturn") ?></th>
                <th><?php echo esc_html__("Item price", "tuturn") ?></th>
                <th><?php echo esc_html__("Quantity", "tuturn") ?></th>
                <th><?php echo esc_html__("Amount", "tuturn") ?></th>
            </tr>
            <?php
            if (!empty($order->get_items())) {
                foreach ($order->get_items() as $item_id => $item) {
                    $product        = $item->get_product();
                    $product_name   = $item->get_name();
                    $product_price  = $item->get_subtotal();
                    $item_quantity  = $item->get_quantity();
                    $subtotal       = $item->get_subtotal();
                    $subtotal       = $item->get_subtotal();
                    $total          = $item->get_total();
            ?>
                    <tr>
                        <td data-label='<?php esc_attr_e("Description", "tuturn") ?>'><?php echo esc_html($product_name) ?></td>
                        <td data-label='<?php esc_attr_e("Item price", "tuturn") ?>'><?php echo tuturn_price_format($product_price) ?></td>
                        <td data-label='<?php esc_attr_e("Quantity", "tuturn") ?>'><?php echo intval($item_quantity) ?></td>
                        <td data-label='<?php esc_attr_e("Amount", "tuturn") ?>'><?php echo tuturn_price_format($subtotal) ?></td>
                    </tr>
                <?php } ?>
                <tr>
                    <td></td>
                    <td></td>
                    <td><?php esc_html_e('Subtotal:', "tuturn") ?></td>
                    <td><?php echo tuturn_price_format($get_total) ?></td>
                </tr>
            <?php } ?>
            <?php if (!empty($refunded)) { ?>
                <tr>
                    <td></td>
                    <td></td>
                    <td><?php esc_html_e('Refunded:', "tuturn") ?></td>
                    <td><?php echo tuturn_price_format($refunded); ?></td>
                </tr>
            <?php } ?>
            <?php if (!empty($order_total)) { ?>
                <tr>
                    <td></td>
                    <td></td>
                    <td><?php esc_html_e('Total:', "tuturn") ?></td>
                    <td><?php echo tuturn_price_format($order_total); ?></td>
                </tr>
            <?php } ?>
        </table>

        <?php if (!empty($invoice_terms)) { ?>
            <div class="tu-descpbox">
                <div class="tu-tabledescription">
                    <h5><?php esc_html_e('Terms & conditions', "tuturn") ?></h5>
                    <p><?php echo esc_html($invoice_terms) ?></p>
                </div>
            </div>
        <?php } ?>
    </div>
</div>