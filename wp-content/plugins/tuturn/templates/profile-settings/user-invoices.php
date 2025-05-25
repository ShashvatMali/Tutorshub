<?php
/**
 * Invoices listing
 *
 * @package     Tuturn
 * @subpackage  Tuturn/Templates/Profile_Settings
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

$sort_by        = !empty($_GET['sort_by']) ? esc_html($_GET['sort_by']) : '';
$posts_per_page = get_option('posts_per_page') ? get_option('posts_per_page') : 10;
$pg_page        = get_query_var('page') ? get_query_var('page') : 1;
$pg_paged       = get_query_var('paged') ? get_query_var('paged') : 1;
$paged          = max($pg_page, $pg_paged);
$order_arg      = array(
    'page'      => $paged,
    'orderby'   => 'date',
    'paginate'  => true,
    'limit'     => $posts_per_page,
);

if (!empty($userType) && $userType === 'student') {
    $order_arg['student_id']  = $user_identity;
} elseif (!empty($userType) && $userType === 'instructor') {
    $order_arg['instructor_id']  = $user_identity;
}

if( !empty($sort_by) ){
    if($sort_by == 'orderby_asc'){
        $order_arg['order']  = 'ASC';
    } elseif($sort_by == 'payoneer' || $sort_by == 'stripe'){
        $order_arg['payment_method']  = $sort_by;
       
    } elseif($sort_by == 'orderby_desc'){
        $order_arg['order']  = 'DESC';
    }    
}

$customer_orders = wc_get_orders( $order_arg );
?>
<div class="tu-invoice-listings">
    <div class="tu-dbwrapper">
        <div class="tu-dbtitle">
            <h3><?php esc_html_e('Invoices & bills', 'tuturn');?></h3>
            <form id="invoice-search-form" action="<?php echo esc_url( $current_page_link ); ?>">
                 <input type="hidden" name="identity" value="<?php echo esc_attr($user_identity); ?>">
                       
                <div class="tu-selectwrapper">
                    <span><?php esc_html_e('Sort by', 'tuturn');?>:</span>
                    <div class="tu-selectv">
                        <select name="sort_by" id="tu-sortyby" class="form-control invoice-sort-by">                           
                            <option value=""><?php esc_html_e('All invoices', 'tuturn');?></option>
                            <option value="orderby_desc" <?php if(!empty($sort_by) && $sort_by == 'orderby_desc'){echo esc_attr('selected');}?>><?php esc_html_e('New to old', 'tuturn');?></option>
                            <option value="orderby_asc" <?php if(!empty($sort_by) && $sort_by == 'orderby_asc'){echo esc_attr('selected');}?>><?php esc_html_e('Old to new', 'tuturn');?></option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
        <?php       
        if (!empty($customer_orders->orders)) {
            ?>
            <div class="tu-invoicestable">
                <table class="table dhb-table">
                    <thead>
                        <tr>
                            <th scope="col"><?php esc_html_e('Client name', 'tuturn');?></th>
                            <th scope="col"><?php esc_html_e('Invoice date', 'tuturn');?></th>
                            <th scope="col"><?php esc_html_e('Amount', 'tuturn');?></th>
                            <th scope="col"><?php esc_html_e('Action', 'tuturn');?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($customer_orders->orders)) {
                            $count_post = count($customer_orders->orders);
                            foreach ($customer_orders->orders as $order) {
                                $data_created = $order->get_date_created();
                                $payment_type = get_post_meta( $order->get_id(), 'payment_type',true );
                                $instructor_profile_id = get_post_meta( $order->get_id(), 'instructor_profile_id',true );
                                $order_total  = !empty($userType) && $userType === 'instructor' ? get_post_meta($order->get_id(),'instructor_shares',true  ): $order->get_total();
                                
                                if($userType === 'instructor'){
                                    $customer_name  = $order->get_formatted_billing_full_name();
                                } else {
                                    $instructor_profile_id = get_post_meta( $order->get_id(), 'instructor_profile_id',true );
                                    $customer_name  = tuturn_get_username($instructor_profile_id);
                                }

                                if(!empty($userType) && $userType === 'instructor' && !empty($payment_type) && $payment_type === 'package'){
                                    $order_total  = $order->get_total();
                                    $customer_name  = $order->get_formatted_billing_full_name();
                                }
                                $order_total  = !empty($order_total) ? $order_total : 0;
                                $invoice_url  = add_query_arg(array('mode' => 'detail', 'id'=>intval($order->get_id())), $current_page_link);
                                ?>
                                <tr>
                                    <td data-label="<?php esc_attr_e('Client name', 'tuturn');?>"><a href="javascript:void(0);"><?php echo esc_html($customer_name); ?></a></td>
                                    <td data-label="<?php esc_attr_e('Invoice #', 'tuturn');?>"><?php echo wc_format_datetime( $order->get_date_created(), get_option( 'date_format' ) . ', ' . get_option( 'time_format' ) );?></td>
                                    <td data-label="<?php esc_attr_e('Amount', 'tuturn');?>"><?php echo tuturn_price_format($order_total);?></td>
                                    <td data-label="<?php esc_attr_e('Action', 'tuturn');?>"><a class="tu-linksm" href="<?php echo esc_url($invoice_url); ?>"><?php esc_html_e('View detail', 'tuturn');?> <i class="icon icon-chevron-right"></i></a> </td>
                                </tr>                       
                            <?php }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        <?php } else {?>
            <div class="tu-bookings tu-booking-epmty-field">
                <h4><?php esc_html_e('Uh ho! No invoice available to show', 'tuturn'); ?></h4>
                <p><?php esc_html_e('We\'re sorry but there is no invoices available to show today', 'tuturn'); ?></p>
            </div>
            <?php
            $args['message']   = esc_html__('Oops!! record not found', 'tuturn');
            tuturn_get_template_part('dashboard/dashboard', 'empty-record', $args);
        }?>        
    </div>
    <?php if (!empty($customer_orders->orders)) {
         tuturn_paginate($customer_orders);
    }?>
</div>