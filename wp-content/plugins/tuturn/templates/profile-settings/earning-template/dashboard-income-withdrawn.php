<?php
/**
 * The template part for displaying the dashboard Income withdrawn for seller
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

$total_amount   = 0;

if( function_exists('tuturn_account_withdraw_details') ){
  $total_amount = tuturn_account_withdraw_details($user_identity);
}

global $tuturn_settings;
$profile_settings_url   = get_permalink();

$booking_option         = !empty($tuturn_settings['booking_option']) ? $tuturn_settings['booking_option'] : 'yes';
$invoice_page_hide      = !empty($tuturn_settings['invoice_page_hide']) ? $tuturn_settings['invoice_page_hide'] : 'show';

if(!empty($user_identity)){
    $profile_settings_url   = add_query_arg(array('useridentity'=>$user_identity), $profile_settings_url);
}
?>
<div class="tu-incomeitem">
      <div class="tu-incomeprice">
            <span class="tu-incomeicon tu-bgpurp"><i class="icon icon-briefcase tu-colorpurp"></i></span>
            <h5>
                <?php tuturn_price_format($total_amount);?>
                <span><?php esc_html_e('Income withdrawn', 'tuturn'); ?></span>
            </h5>
            <?php if($booking_option == 'yes' || $invoice_page_hide == 'show'){?>
                <a href="<?php echo esc_url(add_query_arg(array('tab'=>'invoices'), $profile_settings_url));?>">
                    <?php esc_html_e('All invoices', 'tuturn'); ?> <i class="icon icon-chevron-right"></i>
                </a>
            <?php } ?>
      </div>
</div>