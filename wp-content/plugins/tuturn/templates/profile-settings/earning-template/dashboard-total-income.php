<?php
/**
 * The template part for displaying the dashboard Total income for seller
 *
 * @package     Tuturn
 * @subpackage  Tuturn/templates/dashboard/earning_template
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
*/ 
if(!class_exists('WooCommerce')){
    return;
}
if (!empty($args) && is_array($args)) {
	extract($args);
}
$account_blance     = tuturn_account_details($user_identity,array('wc-completed'), array('publish', 'completed'));

$profile_settings_url   = get_permalink();
if(!empty($user_identity)){
    $profile_settings_url   = add_query_arg(array('useridentity'=>$user_identity), $profile_settings_url);
} 
?>
<div class="tu-incomeitem">
    <div class="tu-incomeprice">
        <span class="tu-incomeicon"><i class="icon icon-pie-chart"></i></span>
        <h5><?php tuturn_price_format($account_blance);?><span><?php esc_html_e('Total income', 'tuturn'); ?></span></h5>
        <a href="<?php echo esc_url(add_query_arg(array('tab'=>'earnings'), $profile_settings_url));?>"><i class="icon icon-rotate-cw"></i></a>
    </div>	
</div>