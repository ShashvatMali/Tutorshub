<?php
/**
 * Dashboard instructor earnings
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
?>
<ul class="tu-incomedetails">
    <!-- Earning boxes -->
    <li><?php tuturn_get_template_part('profile-settings/earning-template/dashboard', 'total-income', $args); ?></li>
    <li><?php tuturn_get_template_part('profile-settings/earning-template/dashboard', 'income-withdrawn', $args); ?></li>
    <li><?php tuturn_get_template_part('profile-settings/earning-template/dashboard', 'pending-income', $args); ?></li>
    <li><?php tuturn_get_template_part('profile-settings/earning-template/dashboard', 'income-in-account', $args); ?></li>
</ul>
<div class="tu-transaction-invoices">
    <!-- earning graph summary -->
    <?php tuturn_get_template_part('profile-settings/earning-template/dashboard', 'earning-graph-summary', $args); ?>
    <!-- payouts method -->
    <?php tuturn_get_template_part('profile-settings/earning-template/dashboard', 'payouts-method', $args); ?>
    <!-- payouts history -->
    <?php tuturn_get_template_part('profile-settings/earning-template/dashboard', 'payouts-history', $args); ?>
</div>