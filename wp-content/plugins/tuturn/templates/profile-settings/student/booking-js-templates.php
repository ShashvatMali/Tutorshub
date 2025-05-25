<?php 
/**
 * Booking JS templates
 *
 * @package     Tuturn
 * @subpackage  Tuturn/Templates/Profile_Settings/student
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
*/
global $tuturn_settings;
if (!empty($args) && is_array($args)) {
	extract($args);
}

if (!empty($args['profile_id']) && is_array($args['profile_id'])) {
	$profile_id = $args['profile_id'];
}
$terms_conditions_page	= tuturn_get_page_uri('terms_conditions');
$booking_cancle_reasons	= !empty($tuturn_settings['booking_cancle_reasons']) ? $tuturn_settings['booking_cancle_reasons'] : array(); ?>
<script type="text/template" id="tmpl-booking-complete">
    <div class="modal-header">  
        <h5><?php esc_html_e('Complete appointment', 'tuturn');?></h5>
        <a href="javascript:void(0);" class="tu-close " type="button" data-bs-dismiss="modal" aria-label="Close"><i class="icon icon-x"></i></a>
    </div>
    <div class="modal-body">
        <div class="tu-alertpopup">
            <span class="bg-lightgreen">
                <i class="icon icon-bell"></i>
            </span>
            <h3><?php esc_html_e('Double check before you do', 'tuturn');?></h3>
            <p><?php esc_html_e('Are you sure you want to complete appointment?', 'tuturn');?></p>

            <div class="tu-boxtitle">
                <h4><?php esc_html_e('Add your review', 'tuturn');?></h4>
            </div>
            <form class="tu-themeform" id="tu-reviews-form">
                <fieldset>
                    <div class="tu-themeform__wrap">
                        <div class="form-group-wrap">
                            <div class="form-group">
                                <div class="tu-reviews">
                                    <label class="tu-label"><?php esc_html_e('Give rating to your review', 'tuturn');?></label>
                                    <div class="tu-my-ratingholder">
                                        <ul id="tu_stars" class='tu-rating-stars tu_stars'>
                                            <li class='tu-star' data-value='1'>
                                                <i class='icon icon-star'></i>
                                            </li>
                                            <li class='tu-star' data-value='2'>
                                                <i class='icon icon-star'></i>
                                            </li>
                                            <li class='tu-star' data-value='3'>
                                                <i class='icon icon-star'></i>
                                            </li>
                                            <li class='tu-star' data-value='4'>
                                                <i class='icon icon-star'></i>
                                            </li>
                                            <li class='tu-star' data-value='5'>
                                                <i class='icon icon-star'></i>
                                            </li>
                                        </ul>
                                        <input type="hidden" id="tu_rating" name="rating" value="0">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group tu-message-text">
                                <label class="tu-label"><?php esc_html_e('Description', 'tuturn');?></label>
                                <div class="tu-placeholderholder">
                                    <textarea class="form-control tu-textarea" id="tu-reviews-content" name="reviews_content" required placeholder="<?php esc_attr_e('Enter description', 'tuturn');?>" maxlength="500"></textarea>
                                </div>
                                <div class="tu-input-counter">
                                    <span><?php esc_html_e('Characters left', 'tuturn');?>:</span>
                                    <b class="tu_current_comment"><?php echo intval(500);?></b>
                                    <?php esc_html_e('/','tuturn')?>
                                    <em class="tu_maximum_comment"> <?php echo intval(500);?></em>
                                </div>
                            </div>

                            <div class="form-group tu-formspacebtw">
                                <div class="tu-check">
                                    <input type="hidden" name="termsconditions" value="" >
                                    <input type="checkbox" id="termsconditions" name="termsconditions">
                                    <label for="termsconditions"><span><?php esc_html_e('I have read and agree to all', 'tuturn');?></span> <a href="<?php echo esc_url($terms_conditions_page);?>"><?php esc_html_e('Terms & conditions', 'tuturn');?></a></label>
                                </div>
                                <a href="javascript:void(0);" class="tu-primbtn-lg tu-submit-reviews" data-profile_id="<?php echo intval($profile_id);?>"><span><?php esc_html_e('Submit', 'tuturn');?></span><i class="icon icon-chevron-right"></i></a>
                                <input type="hidden" name="profile_id" value="<?php echo intval($profile_id);?>" >
                                <input type="hidden" name="user_id" value="<?php echo intval($user_identity);?>" >
                                <input type="hidden" name="postId" id="booking_order_id" value="">
                                <input type="hidden" name="action_type" id="booking_action_type" value="">
                            </div>
                        </div>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>
</script>
<script type="text/template" id="tmpl-booking-cancel">
    <div class="modal-header">  
        <h5><?php esc_html_e('Cancel appointment', 'tuturn');?></h5>
        <a href="javascript:void(0);" class="tu-close " type="button" data-bs-dismiss="modal" aria-label="Close"><i class="icon icon-x"></i></a>
    </div>
    <div class="modal-body">
        <form class="tu-themeform" id="tu-booking-decline-form">
            <fieldset>
                <div class="tu-themeform__wrap">
                    <div class="form-group">
                        <label class="tu-label"><?php esc_html_e('Choose reason', 'tuturn');?></label>
                        <div class="tu-select">
                            <select name="decline_reason" data-placeholder="<?php esc_attr_e('Type or select from list', 'tuturn');?>" class="form-control"  required>
                            <option label="<?php esc_attr_e('Select reason from list','tuturn') ?>"></option>
                                <?php if(!empty($booking_cancle_reasons)) {
                                    foreach($booking_cancle_reasons as $booking_cancle_reasons){ ?>
                                         <option value="<?php echo esc_attr($booking_cancle_reasons);?> "><?php echo esc_html($booking_cancle_reasons);?></option>
                                    <?php }  
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="tu-label"><?php esc_html_e('A little description', 'tuturn');?></label>
                        <textarea class="form-control tu-textarea cancle-app-value" name="decline_reason_desc" placeholder="<?php esc_attr_e('Enter description', 'tuturn');?>"></textarea>
                        <div class="tu-input-counter">
                             
                        </div>
                    </div>
                    <div class="form-group tu-formbtn">
                        <a href="javascript:void(0);" class="tu-primbtn tu-redbtn tu-decline-submit"><?php esc_html_e('Cancel appointment', 'tuturn');?></a>
                    </div>
                </div>
            </fieldset>
            <input type="hidden" name="booking_order_id" id="booking_order_id" value="">
            <input type="hidden" name="booking_action_type" id="booking_action_type" value="">
        </form>
    </div>
</script>