<?php
/**
 * Instructor contact details
 *
 * @package     Tuturn
 * @subpackage  Tuturn/Templates/Profile_Settings/Instructor
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
*/
global $post,$current_user, $tuturn_settings;

$reasons  = array(
    esc_html__('I don\'t want to use any more', 'tuturrn'),
    esc_html__('Not fix as per my expectations', 'tuturrn'),
    esc_html__('Others', 'tuturrn'),
);
$delete_account_reasons          = !empty($tuturn_settings['delete_account_reasons']) ? $tuturn_settings['delete_account_reasons'] : $reasons;
$enable_delete_account          = !empty($tuturn_settings['enable_delete_account']) ? $tuturn_settings['enable_delete_account'] : 'no';

if(!empty($enable_delete_account) && $enable_delete_account == 'no'){
    return;
}
?>
<div class="tu-boxarea security-tab">
    <div class="tu-boxsm">
        <div class="tu-boxsmtitle">
            <h4><?php esc_html_e('Delete account', 'tuturn');?></h4>
        </div>
    </div>
    <div class="tu-box">       
        <fieldset>
            <form id="tu-delete-account" name="delete-account" class="tu-themeform tu-dhbform" action="">
                <div class="tu-themeform__wrap">
                    <div class="form-group-wrap">
                        <div class="form-group">
                            <label class="tu-label"><?php esc_html_e('Reason', 'tuturn'); ?></label>
                            <div class="tu-select">
                                <select id="tu-reason" name="reason" data-placeholder="<?php esc_attr_e('Select reason', 'tuturn'); ?>" class="form-control" required>
                                    <option label="<?php esc_attr_e('Select reason', 'tuturn'); ?>"></option>
                                    <?php foreach ($delete_account_reasons as $term) { ?>
                                        <option value="<?php echo esc_attr($term); ?>"><?php echo esc_html($term) ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="tu-label"><?php esc_html_e('Password', 'tuturn');?></label>
                            <div class="tu-placeholderholder">
                                <input type="password" class="form-control" name="password" value="" required placeholder="<?php esc_attr_e('Add your password*','tuturn');?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="tu-label"><?php esc_html_e('Comments','tuturn')?> </label>
                            <textarea class="form-control" name="comments" placeholder="<?php esc_attr_e('Comments','tuturn')?>"></textarea>
                        </div>
                        <div class="form-group tu-formbtn">
                            <a href="javascript:void(0);" class="tu-primbtn-lg delete-my-account"><?php esc_html_e('Delete account','tuturn')?></a>
                        </div>
                    </div>
                </div>
            </form>
        </fieldset>
    </div>
</div>