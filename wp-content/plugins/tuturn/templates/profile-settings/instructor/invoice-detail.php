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
$order_id   = !empty($id) ? intval($id) : '';
$pdfDownload   = !empty($_GET['pdfDownload']) ? intval($_GET['pdfDownload']) : '';
/* if order Id empty */
if (empty($order_id)) {
    return;
}

$order                  = wc_get_order($order_id);
$data_created           = $order->get_date_created();
$date_format            = get_option('date_format') . ' ' . get_option('time_format');
$data_created           = wc_format_datetime( $order->get_date_created(), get_option( 'date_format' ) . ', ' . get_option( 'time_format' ) );
$data_created           = date_i18n($date_format, strtotime($data_created ));
$get_total              = $order->get_total();
$student_id             = get_post_meta( $order->get_id(), 'student_id',true );
$instructor_id          = get_post_meta( $order->get_id(), 'instructor_id',true );
$instructor_profile_id  = tuturn_get_linked_profile_id( $instructor_id );
$author_address         = get_post_meta( $instructor_profile_id, '_address',true );
$store_address          = get_option( 'woocommerce_store_address' );
$user_data              = get_userdata($instructor_id);
$user_email             = !empty($user_data->user_email) ? sanitize_email($user_data->user_email) : '' ;
$author_name            = !empty($profile_details['name']) ? sanitize_text_field($profile_details['name']) : ''; 
$instructor_email       = !empty($profile_details['contact_info']['email']) ? sanitize_email($profile_details['contact_info']['email']) : $user_email ; 
$billing_address        = $order->get_billing_address_1();
$billing_email          = $order->get_billing_email();
$bill_to_name           = $order->get_billing_first_name();
$payment_type           = get_post_meta( $order_id, 'payment_type',true );
$payment_type           = !empty($payment_type) ? $payment_type : '';
$total_tax 	            = $order->get_total_tax();
$total_tax              = !empty($total_tax) ? $total_tax : 0;
$invoice_terms          = !empty($tuturn_settings['invoice_terms']) ? sanitize_text_field($tuturn_settings['invoice_terms']) : '';
$admin_commision        = '';
$refunded               = $order->get_total_refunded();

if (!empty($payment_type) && $payment_type === 'booking') {
    $admin_commision    = !empty($tuturn_settings['admin_commision']) ? $tuturn_settings['admin_commision'] : '';
}

$order_total    = !empty($userType) && $userType === 'instructor' ? get_post_meta($order->get_id(),'instructor_shares',true  ): $order->get_total();

if(!empty($userType) && $userType === 'instructor' && !empty($payment_type) && $payment_type === 'package'){
    $order_total        = $order->get_total();
    $instructor_email   = sanitize_email(get_option('admin_email'));
    $author_name        = 'admin';
    $author_address     =  $store_address; 
}
$order_total  = !empty($order_total) ? $order_total : 0;
?>
<div class="tu-instructor-invoice-detail"> 
    <div class="tu-dbwrapper">
        <?php if(empty($pdfDownload) || (!empty($pdfDownload) && $pdfDownload !== 1)){
            $invoice_url                = add_query_arg(array('mode' => 'detail', 'id'=>intval($order->get_id()), 'pdfDownload' => 1), $current_page_link);
            $invoice_pdfdownload_url    = !empty($invoice_url) ? $invoice_url : '#';
            ?>
            <div class="tu-dbtitle">
                <h3><?php esc_html_e('Invoice preview','tuturn') ?></h3>
                <div class="tu-invoivebtns">
                    <a href="<?php echo esc_url($invoice_pdfdownload_url);?>" class="tu-primbtn tu-greenbtn"> <i class="icon icon-download"></i><span><?php esc_html_e('Download Invoice','tuturn') ?></span></a>
                </div>
            </div>
        <?php }?>
        <div class="tu-invoicedetail" id="divToPrint">
            <div class="tu-boxlg tu-boxdark">
                <div class="tu-incoiveinfo">
                    <h4>
                        <?php
                        esc_html_e('Invoice # ','tuturn');
                        echo intval($order_id);
                        ?>
                    </h4>
                     <ul class="tu-userinvoice">
                        <?php if(!empty($author_name)){?>
                            <li><?php echo esc_html($author_name)?><i class="icon icon-user"></i> </li>
                        <?php } ?>
                        <?php if(!empty($instructor_email)){?>
                            <li><?php echo esc_html($instructor_email)?> <i class="icon icon-mail"></i> </li>
                        <?php } ?>
                        <?php if(!empty($author_address)){?>
                            <li><?php echo do_shortcode(nl2br($author_address)) ?> <i class="icon icon-map-pin"></i> </li>
                        <?php } 
                        ?>
                    </ul>
                </div>
            </div>
            <div class="tu-boxlg tu-billadd">
                 <ul class="tu-billinfo">
                    <li>
                        <h6><?php esc_html_e('Billed to','tuturn') ?></h6>
                        <?php if(!empty( $order->get_formatted_billing_full_name())) {?>
                            <h5><?php echo esc_html($order->get_formatted_billing_full_name()); ?></h5>
                        <?php } ?>
                    </li>
                    <?php if(!empty($data_created)){?>
                        <li>
                            <h6><?php esc_html_e('Date','tuturrn')?></h6>
                            <?php echo esc_html($data_created); ?>
                        </li>
                    <?php } ?>
                    <?php if(!empty($billing_address)){?>
                        <li>
                            <h6><?php esc_html_e('Address','tuturn') ?></h6>
                            <h5><?php echo do_shortcode($billing_address) ?></h5>
                        </li>
                    <?php } ?>
                </ul>
                <div class="tu-invoiceamount">
                    <h6><?php esc_html_e('Invoice total amount','tuturn') ?></h6>
                    <h3><?php echo tuturn_price_format($order_total);?></h3>
                </div>
            </div>
            <div class="tu-boxlg">
                <div class="tu-incoiveinfo">
                    <h4><?php esc_html_e('Invoice summary','tuturn') ?></h4>
                </div>
                <table class="table tu-tableinvoice">
                    <thead>
                        <tr>
                            <th scope="col"><?php esc_html_e('Description','tuturn')?></th>
                            <th scope="col"><?php esc_html_e('Item price','tuturn')?></th>
                            <th scope="col"><?php esc_html_e('Quantity','tuturn')?></th>
                            <th scope="col"><?php esc_html_e('Amount','tuturn') ?></th>
                        </tr>
                    </thead>
                    <tbody>   
                        <?php
                        if(!empty($order->get_items())){
                          
                            foreach ( $order->get_items() as $item_id => $item ) {
                                $product        = $item->get_product();
                                $product_name   = $item->get_name();
                                $product_price  = $item->get_subtotal();
                                $item_quantity  = $item->get_quantity();
                                $subtotal       = $item->get_subtotal();
                                $subtotal       = $item->get_subtotal();
                                $total          = $item->get_total();
                                ?>
                                <tr>
                                    <td data-label='<?php esc_attr_e('Description','tuturn') ?>'><?php echo esc_html($product_name)?></td>
                                    <td data-label='<?php esc_attr_e('Item price','tuturn') ?>'><?php echo tuturn_price_format($product_price)?></td>
                                    <td data-label='<?php esc_attr_e('Quantity','tuturn') ?>'><?php echo intval($item_quantity)?></td>
                                    <td data-label='<?php esc_attr_e('Amount','tuturn') ?>'><?php echo tuturn_price_format($subtotal)?></td>
                                </tr>  
                            <?php }?>
                            <tr>
                                <td></td>
                                <td></td>
                                <td class="tu-subtotal"><?php esc_html_e('Subtotal:','tuturn')?></td>
                                <td class="tu-subtotal"><?php echo tuturn_price_format($get_total)?></td>
                            </tr> 
                            <?php if(!empty($total_tax)){?>
                                <tr>
                                    <td></td>
                                    <td></td>    
                                    <td class="tu-subtotal"><?php esc_html_e('Tax including:', 'tuturn')?></td>
                                    <td class="tu-subtotal"><?php echo tuturn_price_format($total_tax);?></td>
                                </tr>
                            <?php } ?>
                        
                            <?php if (!empty($payment_type) && $payment_type === 'booking' && !empty($total) && !empty($admin_commision)) { ?>
                                <tr>
                                    <td></td>
                                    <td></td>    
                                    <td class="tu-subtotal"><?php esc_html_e('Service fee:', 'tuturn')?></td>
                                    <td class="tu-subtotal"><?php echo esc_html($admin_commision);?>%</td>
                                </tr>
                            <?php } ?>
                        <?php }?>
                        <?php if(!empty($refunded)){?>
                            <tr>
                                <td></td>
                                <td></td>    
                                <td class="tu-subtotal"><?php esc_html_e('Refunded:', 'tuturn')?></td>
                                <td class="tu-subtotal"><?php echo tuturn_price_format($refunded);?></td>
                            </tr>
                        <?php }?>
                        <?php if(!empty($order_total)){?>
                            <tr>
                                <td></td>
                                <td></td>    
                                <td class="tu-subtotal"><?php esc_html_e('Total:', 'tuturn')?></td>
                                <td class="tu-subtotal"><?php echo tuturn_price_format($order_total);?></td>
                            </tr>
                        <?php }?>
                    </tbody>
                </table>
            </div>
            <?php if (!empty($invoice_terms)) { ?>
                <div class="tu-descpbox">
                    <div class="tu-tabledescription">
                        <h5><?php esc_html_e('Terms & conditions','tuturn')?></h5>
                        <p><?php echo esc_html($invoice_terms)?></p>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>