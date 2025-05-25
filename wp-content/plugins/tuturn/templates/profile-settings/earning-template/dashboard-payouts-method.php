<?php

/**
 * The template part for displaying the dashboard Payouts methods for seller
 *
 * @package     Tuturn
 * @subpackage  Tuturn/templates/dashboard/earning_template
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
 */
global $current_user, $tuturn_settings;
if (!empty($args) && is_array($args)) {
	extract($args);
}

$payout_list        = tuturn_get_payouts_lists();
$contents_payout        = get_user_meta($user_identity, 'tuturn_payout_method', true);
$terms_conditions_URL   = !empty($tuturn_settings['tpl_terms_conditions']) ? get_the_permalink($tuturn_settings['tpl_terms_conditions']) : '';

$default_payout = '';
if(!empty($contents_payout['default'])){
    $default_payout	= $contents_payout['default'];
}
?>
<div class="tu-dbwrapper">
    <div class="tu-dbtitle">
        <h3><?php esc_html_e('Payout methods', 'tuturn'); ?></h3>
    </div>
    <div class="tu-payoutmethods">
        <?php
        if (is_array($payout_list) && !empty($payout_list)) {
            $selected_payout_count = 0;
            foreach ($payout_list as $pay_key => $pay_val) {

                $selected_payout_key = '';
                if (is_array($contents_payout) && !empty($contents_payout)) {
                    $selected_payout_key = array_keys($contents_payout)[0];
                    $selected_payout_count++;
                }
                $selected_payout_key    = $pay_val['id'];

                if (!empty($pay_val['status']) && $pay_val['status'] === 'enable') { ?>
                    <div class="tu-payoutmethods__item">
                        <div class="tu-methodwrap">
                            <div class="tu-payinfo">
                                <div class="tu-check">
                                    <input type="radio" id="payrols-<?php echo esc_attr($pay_val['id']); ?>" name="payout_settings_type"  <?php checked($default_payout, $pay_val['id']); ?> value="<?php echo esc_attr($pay_val['id']); ?>">
                                    <label for="payrols-<?php echo esc_attr($pay_val['id']); ?>">
                                        <img src="<?php echo esc_url($pay_val['img_url']); ?>" alt="<?php echo esc_attr($pay_val['title']); ?>">
                                        <span> <?php echo esc_html($pay_val['label']); ?></span>
                                    </label>
                                </div>
                            </div>
                            <a class="tu-colorred d-none" href="javascript:void(0);"><?php esc_html_e('Delete account', 'tuturn'); ?><i class="icon icon-trash-2"></i></a>
                            <a href="javascript:void(0);" class="tu-payout-modal" data-id="<?php echo esc_attr($pay_val['id']); ?>"><?php esc_html_e('Add details', 'tuturn'); ?><i class="icon icon-plus"></i></a>
                        </div>
                    </div>
                    <script type="text/template" id="tmpl-<?php echo esc_attr($pay_val['id']); ?>">
                        <div class="modal-header">
                            <h5><?php echo esc_html($pay_val['title']); ?></h5>
                            <a href="javascript:void(0);" class="tu-close" type="button" data-bs-dismiss="modal" aria-label="Close"><i class="icon icon-x"></i></a>
                        </div>
                        <div class="modal-body">
                            <form class="tu-themeform tu-payout-user-form">
                                <input type="hidden" name="payout_settings[type]" value="<?php echo esc_attr($pay_val['id']); ?>">
                                <fieldset>
                                    <div class="tu-themeform__wrap">
                                        <?php if (is_array($pay_val['fields']) && !empty($pay_val['fields'])) {
                                            foreach ($pay_val['fields'] as $key => $field) {
                                                $db_value = !empty($contents_payout[$selected_payout_key][$key]) ? $contents_payout[$selected_payout_key][$key] : "";
                                                $required = !empty($field['required']) ? 'required' : "";
                                                ?>
                                                <div class="form-group">
                                                    <label class="tu-label"><?php echo esc_html($field['title']); ?></label>
                                                    <div class="tu-placeholderholder">
                                                        <input type="<?php echo esc_attr($field['type']); ?>" class="form-control tu-form-input" name="payout_settings[<?php echo esc_attr($key); ?>]" id="<?php echo esc_attr($key); ?>-payrols" value="<?php echo esc_attr($db_value); ?>"  placeholder=" " <?php echo esc_attr($required);?>>
                                                        <?php if(!empty($field['placeholder'])){?>
                                                            <div class="tu-placeholder">
                                                                <span><?php echo esc_html($field['placeholder']); ?></span>
                                                                <?php if(!empty($required)){?>
                                                                    <em>*</em>
                                                                <?php }?>
                                                            </div>
                                                        <?php }?>
                                                    </div>
                                                </div>
                                            <?php
                                            }
                                        }

                                        if (!empty($pay_val['desc'])) { ?>
                                            <div class="tu-paymentdesp form-group">
                                                <?php echo do_shortcode($pay_val['desc']); ?>
                                            </div>
                                        <?php } ?>
                                        <div class="form-group tu-formbtn">
                                            <a href="javascript:void(0);" data-user_id="<?php echo intval($user_identity);?>" data-profile_id="<?php echo intval($profile_id);?>" class="tu-primbtn tu-payrols-settings"><?php esc_html_e('Save &amp; update changes', 'tuturn'); ?></a>
                                        </div>
                                    </div>
                                </fieldset>
                            </form>
                        </div>			  
                    </script>
                    <?php 
                }
            }
        }
        ?>        
        <div class="tu-payoutmethods__item">
            <p>
                <?php esc_html_e('Choose any payment method to get your earned amount direct to your account. Leaving this empty or unchecked will cause delay or no payments to your account. For further info read our detailed.', 'tuturn');?>
                <a href="<?php echo esc_url($terms_conditions_URL);?>"><?php esc_html_e('Tranfer & usage policy.', 'tuturn') ?></a>
            </p>
        </div>
    </div>
</div>