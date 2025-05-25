<?php
/**
 * The template part for displaying the dashboard Income in Account for seller
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

$account_blance             = tuturn_account_details($user_identity, array('wc-completed'), array('completed'));
$withdrawn_amount           = tuturn_account_withdraw_details($user_identity, array('pending', 'publish'));
$available_withdraw_amount  = $account_blance - $withdrawn_amount;
$payout_list                = tuturn_get_payouts_lists();
$contents_payout            = get_user_meta($user_identity, 'tuturn_payout_method', true);
$contents_payout            = !empty($contents_payout) ? $contents_payout : array();

$default_payout = '';
if(!empty($contents_payout['default'])){
    $default_payout	= $contents_payout['default'];
}
?>
<div class="tu-incomeitem">
    <div class="tu-incomeprice">
        <span class="tu-incomeicon tu-bgblue"><i class="icon icon-shopping-cart tu-colorblue"></i></span>
        <h5>
            <?php tuturn_price_format($available_withdraw_amount); ?>
            <span><?php esc_html_e('Available in account', 'tuturn') ?></span>
        </h5>
        <a href="javascript:void(0);" class="tu-withdraw-payment-modal">
            <?php esc_html_e('Withdraw', 'tuturn') ?><i class="icon icon-chevron-right"></i>
        </a>
    </div>
</div>
<?php if (!empty($payout_list) && is_array($payout_list)) {    
    $term_page_url  = tuturn_get_page_uri('terms_conditions'); ?>
    <script type="text/template" id="tmpl-withdraw-saved-payment">
        <div class="modal-header">
            <h5><?php esc_html_e('Withdraw money', 'tuturn'); ?></h5>
            <a href="javascript:void(0);" class="tu-close"><i class="icon icon-x" data-bs-dismiss="modal"></i></a>
        </div>
        <div class="modal-body">
            <?php if (!empty($contents_payout)) { ?>
            <form class="tu-themeform tu-withdrawform">
                <fieldset>
                    <div class="form-group">
                        <label class="tu-label"><?php esc_html_e('Enter amount', 'tuturn'); ?>:</label>
                        <div class="tu-placeholderholder">
                            <input type="number" placeholder="<?php esc_attr_e('Enter amount here', 'tuturn'); ?>*" name="withdraw[amount]" class="form-control">
                            <em class="tu-label tu-maxlimit"><?php esc_html_e('Max Limit', 'tuturn'); ?>: <?php tuturn_price_format($available_withdraw_amount); ?></em>
                        </div>
                    </div>
                    <?php if(!empty($default_payout)){
                        $pay_val    = $payout_list[$default_payout];
                        ?>
                        <div class="form-group form-grouppayments">
                            <label class="tu-label"><?php esc_html_e('Payout method', 'tuturn'); ?>:</label>
                            <ul class="tu-payoutmethod">
                                <?php  if (!empty($pay_val)) { ?>
                                    <li class="tu-check">
                                        <input type="radio" id="<?php echo esc_attr($pay_val['id']); ?>" name="withdraw[gateway]" value="<?php echo esc_attr($pay_val['id']); ?>">
                                        <label for="<?php echo esc_attr($pay_val['id']); ?>" class="tu-label tu-radioholder">
                                            <span class="tu-payoutmode">
                                                <img src="<?php echo esc_url($pay_val['img_url']); ?>" alt="<?php echo esc_attr($pay_val['title']); ?>">
                                                <span><?php echo esc_html($pay_val['label']); ?></span>
                                            </span>
                                        </label>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                        <div class="form-group tu-popupbtnarea">
                            <div class="tu-check">
                                <input id="check3" type="checkbox" name="withdraw_consent">
                                <label for="check3">
                                    <span>
                                        <?php esc_html_e('By clicking you’re agree with our', 'tuturn'); ?>
                                        <a href="<?php echo esc_url($term_page_url); ?>" target="_blank"><?php esc_html_e('Withdraw policies', 'tuturn'); ?></a>
                                    </span>
                                </label>
                            </div>     
                            <button type="button" data-id="<?php echo intval($user_identity); ?>" data-profile_id="<?php echo intval($profile_id);?>" class="tu-primbtn tu-withdraw-money"><?php esc_html_e('Withdraw now', 'tuturn'); ?></button>
                        </div>
                    <?php } else {?>
                        <h4><?php esc_html_e('Select any payment method before withdrawal request', 'tuturn'); ?></h4>
                    <?php }?>                    
                </fieldset>
            </form>
            <?php } else { ?>
                <h4><?php esc_html_e('Select any payment method before withdrawal request', 'tuturn'); ?></h4>
            <?php } ?>
        </div>
    </script>
<?php }
