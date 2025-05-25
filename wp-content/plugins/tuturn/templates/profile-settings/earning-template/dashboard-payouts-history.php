<?php

/**
 * The template part for displaying the dashboard Payouts History for seller
 *
 * @package     Tuturn
 * @subpackage  Tuturn/templates/dashboard/earning_template
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
 */
if (!empty($args) && is_array($args)) {
	extract($args);
}
$reference = !empty($reference) ? $reference : '';
$earning_page_link  = !empty($earning_page_link) ? $earning_page_link : '';
// variable to store all query args for search
$query_args     = array();
$post_status    = array('pending', 'publish', 'rejected');
$sort_by        = !empty($_GET['sort_by']) ? esc_html($_GET['sort_by']) : '';

// standard $query_args as $withdraw_args
$paged          = (get_query_var('paged')) ? get_query_var('paged') : 1;
$show_posts     = get_option('posts_per_page');
$withdraw_args  = array(
    'post_type'       => 'withdraw',
    'author'          => $user_identity,
    'post_status'     => 'any',
    'posts_per_page'  => $show_posts,
    'paged'           => $paged,
);

$meta_array = array();
if (!empty($sort_by)) {
    switch ($sort_by) {
        case "latest":
            $withdraw_args['orderby']   = 'ID';
            $withdraw_args['order']   = 'DESC';
          break;
        case "paypal":
            $meta_array    = array(
                array(
                    'key'         => '_payment_method',
                    'value'       => 'paypal',
                ),                
            );
          break;
        case "payoneer":
            $meta_array    = array(
                array(
                    'key'         => '_payment_method',
                    'value'       => 'payoneer',
                ),                
            );
          break;
        case "bank":
            $meta_array    = array(
                array(
                    'key'         => '_payment_method',
                    'value'       => 'bank',
                ),                
            );
          break;
        case "in-process":
            $withdraw_args['post_status']   = 'pending';
            break;
        case "completed":
            $withdraw_args['post_status']   = 'publish';
          break;
    }
 
}
$withdraw_args      = array_merge_recursive($withdraw_args, $query_args);

if(!empty($meta_array)){
    $withdraw_args['meta_query']    = $meta_array;
}

$withdraw_query     = new WP_Query(apply_filters('tuturn_withdraw_listings_args', $withdraw_args));
$count_post         = $withdraw_query->found_posts;
?>
<div class="tu-dbwrapper tu-payouthistory">
    <div class="tu-dbtitle">
        <h3><?php esc_html_e('Payout history', 'tuturn'); ?></h3>
        <div class="tu-selectwrapper">
            <span><?php esc_html_e('Sort by', 'tuturn'); ?>:</span>
            <form class="tu-themeform tu-displistform" id="withdraw_search_form" action="<?php echo esc_url($earning_page_link); ?>">
                <input type="hidden" name="ref" value="<?php echo esc_attr($reference); ?>">
                <input type="hidden" name="identity" value="<?php echo esc_attr($user_identity); ?>">
                <div class="tu-selectv">
                    <select name="sort_by" data-placeholder="<?php esc_attr_e('All history', 'tuturn'); ?>" class="form-control tu-sortby-filter">
                        <option value=""><?php esc_html_e('All history', 'tuturn'); ?></option>
                        <option value="latest" <?php if($sort_by == 'latest'){echo esc_attr('selected');}?>><?php esc_html_e('Latest first', 'tuturn'); ?></option>
                        <option value="paypal" <?php if($sort_by == 'paypal'){echo esc_attr('selected');}?>><?php esc_html_e('Paypal', 'tuturn'); ?></option>
                        <option value="payoneer" <?php if($sort_by == 'payoneer'){echo esc_attr('selected');}?>><?php esc_html_e('Payoneer', 'tuturn'); ?></option>
                        <option value="bank" <?php if($sort_by == 'bank'){echo esc_attr('selected');}?>><?php esc_html_e('Bank transfer', 'tuturn'); ?></option>
                        <option value="in-process" <?php if($sort_by == 'in-process'){echo esc_attr('selected');}?>><?php esc_html_e('In process', 'tuturn'); ?></option>
                        <option value="completed" <?php if($sort_by == 'completed'){echo esc_attr('selected');}?>><?php esc_html_e('Completed', 'tuturn'); ?></option>
                    </select>
                </div>
            </form>
        </div>
    </div>
    <?php if ($withdraw_query->have_posts()){?>
        <table class="table dhb-table">
            <thead>
                <tr>
                    <th scope="col"><?php esc_html_e('Ref #', 'tuturn'); ?></th>
                    <th scope="col"><?php esc_html_e('Payout method', 'tuturn'); ?></th>
                    <th scope="col"><?php esc_html_e('Date', 'tuturn'); ?></th>
                    <th scope="col"><?php esc_html_e('Amount', 'tuturn'); ?></th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                    while ($withdraw_query->have_posts()) : $withdraw_query->the_post();
                        $post_id    = get_the_ID();
                        $date       = get_the_date();
                        $status     = get_post_status($post_id);
                        $post_date  = !empty($date) ? date_i18n('F j, Y', strtotime($date)) : '';
                        $post_date  = date_i18n(get_option('date_format'),  strtotime(get_the_date()));
                        $withdraw_amount    = !empty(get_post_meta($post_id, '_withdraw_amount', true)) ? get_post_meta($post_id, '_withdraw_amount', true) : '';
                        $payment_method     = !empty(get_post_meta($post_id, '_payment_method', true))  ? get_post_meta($post_id, '_payment_method', true)  : '';
                        $unique_key         = !empty(get_post_meta($post_id, '_unique_key', true))      ? get_post_meta($post_id, '_unique_key', true)      : '';
                        ?>
                        <tr>
                            <td data-label="<?php esc_attr_e('Ref #', 'tuturn'); ?>" scope="row">
                                <span><i class="icon icon-file-text"></i> <?php echo esc_html($unique_key); ?> </span>
                            </td>
                            <td data-label="<?php esc_attr_e('Method', 'tuturn'); ?>"><a href="javascript:void(0)"><?php echo ucfirst(esc_html($payment_method)); ?></a></td>
                            <td data-label="<?php esc_attr_e('Date', 'tuturn'); ?>"><?php echo esc_html($post_date); ?></td>
                            <td data-label="<?php esc_attr_e('Amount', 'tuturn'); ?>"><span><?php tuturn_price_format($withdraw_amount); ?></span></td>
                            <td data-label="<?php esc_attr_e('Status', 'tuturn'); ?>"><span class="tu-tagstatus"><?php echo esc_html($status);?></span> </td>
                        </tr>
                        <?php 
                    endwhile;                
                ?>
            </tbody>
        </table>
    <?php  } else {?>
        <div class="tu-bookings tu-booking-epmty-field">
            <h4><?php esc_html_e('Uh ho! No payout available to show', 'tuturn'); ?></h4>
            <p><?php esc_html_e('We\'re sorry but there is no payouts history available to show today', 'tuturn'); ?></p>
        </div>
        <?php
        $args['message']   = esc_html__('Oops!! record not found', 'tuturn');
        tuturn_get_template_part('dashboard/dashboard', 'empty-record', $args);
    }?>
</div>
<?php if (!empty($count_post) && $count_post > $show_posts) { ?>
    <?php tuturn_paginate($withdraw_query); ?>
<?php }
wp_reset_postdata();
$script = "
jQuery(document).on('ready', function(){
    jQuery(document).on('change', '.tu-sortby-filter', function (e) {
        jQuery('#withdraw_search_form').submit();
    });
});";
wp_add_inline_script('tuturn-dashboard', $script, 'after');
