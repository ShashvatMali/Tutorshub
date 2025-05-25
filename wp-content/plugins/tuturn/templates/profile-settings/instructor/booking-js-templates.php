<?php
/**
 * Booking JS template
 *
 * @package     Tuturn
 * @subpackage  Tuturn/Templates/Profile_Settings/instructor
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
 */
global $tuturn_settings;
$decline_booking_reason	= !empty($tuturn_settings['booking_decline_reasons']) ? $tuturn_settings['booking_decline_reasons'] : array(); ?>
<script type="text/template" id="tmpl-booking-approve">
    <div class="modal-body">
        <div class="tu-alertpopup">
            <span class="bg-lightgreen">
                <i class="icon icon-bell"></i>
            </span>
            <h3><?php esc_html_e('Double check before you do', 'tuturn');?></h3>
            <p><?php esc_html_e('Are you sure you want to approve appointment?', 'tuturn');?></p>
            <ul class="tu-btnareafull tu-btnareamid">
                <li><a href="javascript:void(0);" class="tu-primbtn tu-greenbtn tu-approve-submit"><?php esc_html_e('Approve appointment', 'tuturn');?></a></li>
                <li><a href="javascript:void(0);" class="tu-sb-lg tu-btngray" data-bs-dismiss="modal" aria-label="Close"><?php esc_html_e('No don\'t do anything', 'tuturn');?></a></li>
            </ul>
            <input type="hidden" id="booking_order_id" value="">
            <input type="hidden" id="booking_action_type" value="">
        </div>
    </div>
</script>
<script type="text/template" id="tmpl-booking-decline">
    <div class="modal-header">  
        <h5><?php esc_html_e('Decline appointment', 'tuturn');?></h5>
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
                                <?php if(!empty($decline_booking_reason)) {
                                    foreach($decline_booking_reason as $decline_booking_reason){ ?>
                                         <option value="<?php echo esc_attr($decline_booking_reason);?> "><?php echo esc_html($decline_booking_reason);?></option>
                                    <?php }  
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="tu-label"><?php esc_html_e('A little description', 'tuturn');?></label>
                        <textarea class="form-control tu-textarea cancle-app-value" name="decline_reason_desc" placeholder="<?php esc_attr_e('Enter description', 'tuturn');?>"></textarea>
                         
                    </div>
                    <div class="form-group tu-formbtn">
                        <a href="javascript:void(0);" class="tu-primbtn tu-redbtn tu-decline-submit"><?php esc_html_e('Decline appointment', 'tuturn');?></a>
                    </div>
                </div>
            </fieldset>
            <input type="hidden" name="booking_order_id" id="booking_order_id" value="">
            <input type="hidden" name="booking_action_type" id="booking_action_type" value="">
        </form>
    </div>
</script>
<script type="text/template" id="tmpl-booking-meeting-inst">
    <div class="modal-header">
        <h5><?php esc_html_e('Add/edit meeting details','tuturn')?> </h5>
        <a href="javascript:void(0);" class="tu-close" data-bs-dismiss="modal" aria-label="Close"><i class="icon icon-x"></i></a>
    </div>
    <div class="modal-body">
        <form class="tu-themeform tu-meetingform-inst" id="tu-meetingform-inst-form">
            <fieldset>
                <div class="tu-themeform__wrap">
                    <div class="form-group">
                        <label class="tu-label"><?php esc_html_e('Choose meeting option','tuturn')?>  </label>
                        <ul class="tu-meetingoption">
                            <li>
                                <div class="tu-radio">
                                    <input type="radio" id="zoom" name="meeting_type" value="zoom_meet" {{ data.meeting_type == 'zoom_meet' ? 'checked' : '' }}>
                                    <label for="zoom">
                                        <?php $zoom_img = tuturn_add_http_protcol(TUTURN_DIRECTORY_URI . 'public/images/meeting/zoom.png'); ?>
                                        <img src="<?php echo esc_url($zoom_img)?>" alt="<?php esc_attr_e('Zoom','tuturn')?>"><span><?php esc_html_e('Zoom','tuturn')?> </span>
                                    </label>
                                </div>
                            </li>
                            <li>
                                <div class="tu-radio">
                                    <input type="radio" id="googlemeet" name="meeting_type" value="google_meet" {{ data.meeting_type == 'google_meet' ? 'checked' : '' }}>
                                    <label for="googlemeet">
                                        <?php $google_meet = tuturn_add_http_protcol(TUTURN_DIRECTORY_URI . 'public/images/meeting/googlemeet.png'); ?>
                                        <img src="<?php echo esc_url($google_meet)?>" alt="<?php esc_attr_e('Google meet','tuturn')?>"><span><?php esc_html_e('Google meet','tuturn')?> </span>
                                    </label>
                                </div>
                            </li>
                            <li>
                                <div class="tu-radio">
                                    <input type="radio" id="other" name="meeting_type" value="other" {{ data.meeting_type == 'other' ? 'checked' : '' }}>
                                    <label for="other">
                                        <?php $others_meet = tuturn_add_http_protcol(TUTURN_DIRECTORY_URI . 'public/images/meeting/othermeet.png'); ?>
                                        <img src="<?php echo esc_url($others_meet)?>" alt="<?php esc_attr_e('Other','tuturn')?>"><span><?php esc_html_e('Other','tuturn')?></span>
                                    </label>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="form-group">
                        <label class="tu-label"><?php esc_html_e('Add zoom meeting URL','tuturn')?> </label>
                        <div class="tu-placeholderholder">
                            <input type="text" class="form-control" name="meeting_url" required="" value="{{data.meeting_url}}" placeholder="<?php esc_attr_e(' ','tuturn')?>">
                            <div class="tu-placeholder">
                                <span><?php esc_html_e('Enter zoom meeting URL here','tuturn')?> </span>
                                <em>*</em>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="tu-label"><?php esc_html_e('Meeting instructions','tuturn')?> </label>
                        <textarea class="form-control" name="meeting_description" placeholder="<?php esc_attr_e('Enter instructions','tuturn')?>">{{data.meeting_desc}}</textarea>
                    </div>
                    <div class="form-group tu-formbtn">
                        <a href="javascript:void(0);" class="tu-primbtn-lg tu-save-meeting-detail"><?php esc_html_e('Save & update changes','tuturn')?></a>
                    </div>
                </div>
            </fieldset>
            <input type="hidden" name="postId" id="booking_order_id" value="">
            <input type="hidden" name="action_type" id="booking_action_type" value="">
        </form>
    </div>
</script>