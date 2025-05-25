<?php
/**
 * Instructor appointment
 *
 * @package     Tuturn
 * @subpackage  Tuturn/Templates/Single_tuturn_Instructor
 * @author      Amentotech <info@amentotech.com>
 * @link        https://themeforest.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
*/
global $post, $current_user, $tuturn_settings;
if (!empty($args) && is_array($args)) {
    extract($args);
}
$loggedInUser           = $current_user->ID;
$userType 	            = apply_filters('tuturnGetUserType', $loggedInUser );
$booking_option         = !empty($tuturn_settings['booking_option']) ? $tuturn_settings['booking_option'] : 'yes';

if($userType != 'student'){
    return;
}

$instructor_profileID   = !empty($post->ID) ? $post->ID : 0;
$instructor_id          = tuturn_get_linked_profile_id($instructor_profileID, 'post');
/* total week days */
$week_days      = apply_filters( 'tuturnGetWeekDays','');
$day_time       = apply_filters('tuturnAppointmentTime','');
$time_format    = get_option('time_format');

?>
<div class="tu-serviceswizard-parent">
    <div class="tu-serviceswizard tu-hastip">
        <!-- embed code here -->
    </div>
</div>
<script type="text/template" id="tmpl-tu-book-appointment-step2">
    <div class="tu-tabswrapper">
        <div class="tu-bookingstep1">
            <div class="tu-boxtitle">
                <h4><?php esc_html_e('Please select booking details', 'tuturn'); ?></h4>
            </div>
            <form id="tu-booking-slots-filter-form" class="tu-themeform">
                <fieldset>
                    <div class="tu-themeform__wrap">
                        <div class="form-group form-group-half">
                            <div class="tu-calendergrid">
                                <div class="tu-placeholderholder">
                                    <label class="tu-label"><?php esc_html_e('I need a service from', 'tuturn') ?></label>
                                    <div class="tu-calendar">
                                    <input type="text" name="tu_start_date" class="datepicker form-control tu-form-input tu-startDate-picker tu-input-field" placeholder=" " required>
                                    
                                        <div class="tu-placeholder">
                                            <span><?php esc_html_e('Starting date', 'tuturn') ?></span>
                                            <em>*</em>
                                        </div>
                                    </div>
                                </div>
                                <div class="tu-placeholderholder">
                                    <label class="tu-label"><?php esc_html_e('Till date', 'tuturn') ?></label>
                                    <div class="tu-calendar">
                                    <input type="text" name="tu_end_date" class="datepicker form-control tu-form-input tu-endDate-picker tu-input-field" placeholder=" " required>
                                    
                                        <div class="tu-placeholder">
                                            <span><?php esc_html_e('Ending date', 'tuturn') ?></span>
                                            <em>*</em>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-half">
                            <div class="tu-calendergrid">
                                <div class="tu-placeholderholder">
                                    <label class="tu-label"><?php esc_html_e('Hours in between', 'tuturn') ?></label>
                                    <div class="tu-select">
                                        <select id="tu-start-time-filter" data-placeholder="<?php esc_attr_e('Starting time', 'tuturn'); ?>" name="tu_start_time" data-placeholderinput="<?php esc_attr_e('Starting time', 'tuturn') ?>" class="form-control tu_start_time" required>
                                            <option label="<?php esc_attr_e('Starting time', 'tuturn'); ?>"></option>
                                            <?php foreach($day_time as $key_time => $time_val){
                                                $time_val 	= date($time_format, strtotime('2022-01-01' . $time_val));                                                
                                                ?>
                                                <option value="<?php echo esc_attr($key_time); ?>"><?php echo esc_html($time_val); ?></option>
                                            <?php } ?>
                                        </select>   
                                    
                                    </div>
                                </div>
                                <div class="tu-placeholderholder">
                                    <label class="tu-label"><?php esc_html_e('To time', 'tuturn') ?></label>
                                    <div class="tu-select">
                                        <select id="tu-end-time-filter" data-placeholder="<?php esc_attr_e('Select end time', 'tuturn'); ?>" data-placeholderinput="<?php esc_attr_e('Ending time', 'tuturn') ?>" name="tu_end_time" class="form-control tu_end_time" required>
                                            <option label="<?php esc_attr_e('Select end time', 'tuturn'); ?>"></option>
                                            <?php foreach($day_time as $key_time => $time_val){
                                                $time_val 	= date($time_format, strtotime('2022-01-01 ' . $time_val));                                                
                                                ?>
                                            <option value="<?php echo esc_attr($key_time); ?>"><?php echo esc_html($time_val); ?></option>
                                            <?php } ?>
                                        </select>  
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="tu-label"><?php esc_html_e('Select days', 'tuturn') ?></label>
                            <?php if(!empty($week_days)){ ?>
                                <ul class="tu-daysfilter">
                                    <?php foreach($week_days as $key_day=>$day_val){ ?>
                                        <li> 
                                            <div class="tu-check tu-checksm">
                                                <input type="checkbox" name="weekDays[]" value="<?php echo esc_attr($key_day); ?>" id="<?php echo esc_attr($key_day); ?>"> 
                                                <label for="<?php echo esc_attr($key_day); ?>">
                                                        <?php echo esc_html($day_val); ?>
                                                </label>
                                            </div>   
                                        </li> 
                                    <?php } ?>
                                </ul>
                            <?php } ?>
                        </div>
                        <div class="form-group">
                            <a href="javascript:void(0);" class="tu-primbtn-lg" data-student_id="<?php echo esc_attr($current_user->ID); ?>" data-instructor_profile_id="<?php echo esc_attr($post->ID); ?>" id="tu-filter-days-booking-slots"><span><?php esc_html_e('Show availability', 'tuturn') ?></span><i class="icon icon-search"></i></a>
                        </div>
                    </div>
                </fieldset>
            </form>
        </div> 
        <div class="tu-bookingstep2"></div> 
        <div class="tu-bookingstep3"></div> 
        <div class="tu-bookingstep4"></div> 
    </div>
</script>
<script type="text/template" id="tmpl-tu-book-appointment-filter">
    <div class="tu-boxtitle">
        <h4><?php esc_html_e('Available slots', 'tuturn'); ?></h4>
    </div>
    <# if (!_.isEmpty(data.filtered_slots)) { #>
    <form class="tu-multicheck-appointment-form tu-themeform" id="tu-multicheck-appointment-form">      
        <fieldset>
            <div class="tu-themeform__wrap">
                <div class="form-group-wrap">
                    <div class="form-group">
                        <div class="accordion tu-bookingaccordion" id="tu-bookingaccordion">
                            <# 
                            var a = 1;
                            _.each( data.filtered_slots , function( filterday, key ) { #>
                                <# if (!_.isEmpty(filterday.date)) { 
                                    var toggle_expand = (a==1) ? 'true' : '';
                                    var toggle_show_class = (a==1) ? ' show' : '';
                                     #>
                                    <div class="tu-tutioncollapse tu-booking-date-time">
                                        <div class="collapsed tu-booktutiontitle" data-bs-toggle="collapse" data-bs-target="#tution-collapse{{filterday.date_key}}" aria-expanded="{{toggle_expand}}" id="tution-heading{{filterday.date_key}}">
                                            <h6>{{filterday.date}} <span>{{filterday.day}}</span></h6>
                                            <span class="selected-slotscount"><em>0</em> <?php esc_html_e('slots selected', 'tuturn'); ?></span>
                                        </div>
                                        <i class="icon icon-minus" data-bs-toggle="collapse" data-bs-target="#tution-collapse{{filterday.date_key}}" aria-expanded="{{toggle_expand}}" id="tution-heading{{filterday.date_key}}"></i>
                                        <div id="tution-collapse{{filterday.date_key}}" class="accordion-collapse collapse{{toggle_show_class}}" data-bs-parent="#tu-bookingaccordion">
                                            <div class="tu-tutionslots">
                                                <# if (!_.isEmpty(filterday['0'])) {  #>
                                                    <ul class="tu-tutionslotslist dfdfdd">
                                                        <# _.each(filterday , function( filterslot, key ) { #>
                                                            <# if (!_.isEmpty(filterslot.slot_key)) { #>
                                                                <li id="{{filterslot.slot_key}}">
                                                                    <label class="tu-slots-calender tu-slotstimes" {{filterslot.disabled}}>
                                                                        <input type="checkbox" class="tu-timeslots" id="firsslot-{{filterslot.slot_key}}" name="booked_slot[{{filterslot.date}}][{{filterslot.slot_key}}]" data-selected_date="{{filterslot.selected_date}}" value="{{filterslot.slot_key}}" {{filterslot.selected}}>
                                                                        <a href="javascript:void(0);">
                                                                            <# if (!_.isEmpty(filterslot.slot_title)) {#>
                                                                                <h5>{{filterslot.slot_title}}</h5>
                                                                            <# } #>
                                                                            <h6>{{filterslot.start_time}} - {{filterslot.end_time}}</h6>
                                                                            <span>{{filterslot.slots }} <?php esc_html_e('Slot(s)', 'tuturn'); ?></span>
                                                                        </a>
                                                                    </label>                                               
                                                                </li>
                                                            <# } #>
                                                        <# }); #>                                            
                                                    </ul>
                                                <# } else { #>
                                                    <?php esc_html_e('No time slots available for this date','tuturn')?>
                                                <# } #>
                                            </div>
                                        </div>
                                    </div>
                                <# 
                                a  = 2;
                            } #>
                            <# }); #>
                        </div>
                    </div>
                    <div class="form-group gap-2 justify-content-between">
                        <a href="javascript:void(0);" class="tu-primbtn-lg tu-secbtnvtwo tu-bookingbackstep" data-current_step="2" data-previous_step="1"><i class="icon icon-chevron-left"></i><span><?php esc_html_e('Back', 'tuturn'); ?></span></a>
                        <a href="javascript:void(0);" class="tu-primbtn-lg" data-student_id="<?php echo esc_attr($current_user->ID); ?>" data-instructor_profile_id="<?php echo esc_attr($post->ID); ?>" id="tu-next-addbook-step2"><span><?php esc_html_e('Save & continue', 'tuturn'); ?></span><i class="icon icon-chevron-right"></i></a>
                    </div>
                </div>
            </div>
        </fieldset>           
    </form>  
    <# } else{ #>
        <div class=tu-no-slot>
            <p><?php esc_html_e('Slots not found!', 'tuturn'); ?></p>
        </div>
    <# } #>
</script>
<script type="text/template" id="tmpl-tu-book-appointment-step3">
    <form class="tu-themeform" id="tu-book-student-form">
        <fieldset>
            <div class="tu-themeform__wrap">
                <div class="form-group-wrap">
                    <div class="form-group">
                        <div class="tu-bhours tu-bhours-two">
                            <div class="tu-bhours-two_content">
                                <h5><?php esc_html_e('Book this appointment for someone else', 'tuturn'); ?></h5>
                                <p><?php esc_html_e('You need to provide his/her some personal information for booking.', 'tuturn'); ?></p>
                            </div>
                            <div class="tu-appointmentwitch">
                                <div class="form-check form-switch tu-witch">
                                    <# let is_someOne = (data.student_detail.info_someone_else=='on') ? 'checked' : ''; #>
                                    <label class="form-check-label" for="info_someone_else"><?php esc_html_e('Someone else','tuturn'); ?></label>
                                    <input class="form-check-input" type="checkbox" name="info_someone_else" role="switch" id="tu-info-someone-else" {{is_someOne}}>
                                </div>                                    
                            </div>
                        </div>
                    </div>
                    <div class="form-group-wrap tu-some-oneelse-form">
                        <div class="form-group form-group-half">
                            <label class="tu-label"><?php esc_html_e('First Name','tuturn'); ?></label>
                            <div class="tu-placeholderholder">
                                <input type="text" name="info_first_name" class="form-control tu-form-input" placeholder=" " value="{{data.student_detail.info_first_name}}" required>
                                <div class="tu-placeholder">
                                    <span><?php esc_html_e('Enter your full name','tuturn'); ?></span>
                                    <em>*</em>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-half">
                            <label class="tu-label"><?php esc_html_e('Last name','tuturn'); ?></label>
                            <div class="tu-placeholderholder">
                                <input type="text" class="form-control tu-form-input" name="info_last_name" placeholder=" " value="{{data.student_detail.info_last_name}}" required>
                                <div class="tu-placeholder">
                                    <span><?php esc_html_e('Enter your last name','tuturn'); ?></span>
                                    <em>*</em>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-half">
                            <label class="tu-label"><?php esc_html_e('Email address','tuturn'); ?></label>
                            <div class="tu-placeholderholder">
                                <input type="email" class="form-control tu-form-input" name="info_email" placeholder=" " value="{{data.student_detail.info_email}}" required>
                                <div class="tu-placeholder">
                                    <span><?php esc_html_e('Enter your emial address','tuturn'); ?></span>
                                    <em>*</em>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-half">
                            <label class="tu-label"><?php esc_html_e('Phone number','tuturn'); ?></label>
                            <div class="tu-placeholderholder">
                                <input type="tel" class="form-control tu-form-input" name="info_phone" placeholder=" " value="{{data.student_detail.info_phone}}" required>
                                <div class="tu-placeholder">
                                    <span><?php esc_html_e('Enter your Phone number','tuturn'); ?></span>
                                    <em>*</em>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-half">
                            <label class="tu-label"><?php esc_html_e('Address','tuturn'); ?></label>
                            <div class="tu-placeholderholder">
                                <input type="text" class="form-control tu-form-input" name="info_address" placeholder=" " value="{{data.student_detail.info_address}}" required>
                                <div class="tu-placeholder">
                                    <span><?php esc_html_e('Enter your Address','tuturn'); ?></span>
                                    <em>*</em>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-half">
                            <label class="tu-label"><?php esc_html_e('Relation','tuturn'); ?></label>
                            <div class="tu-select">
                                <select data-placeholderinput="<?php esc_attr_e('Search relation','tuturn') ?>" id="tu-info-relations" name="info_relation" data-placeholder="<?php esc_attr_e('Select your relation','tuturn') ?>" class="form-control" required>
                                    <option label="<?php esc_attr_e('Select your relation','tuturn'); ?>"></option>
                                    <# _.each( data.info_relation , function( relation, key ) { #>
                                        <# let selected_relation = (relation.term_id==data.student_detail.relation_id) ? 'selected' : ''; #>
                                        <option value="{{relation.term_id}}" {{selected_relation}}>{{relation.name}}</option>
                                    <# }); #>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="tu-label"><?php esc_html_e('Special comments','tuturn'); ?></label>
                            <div class="tu-placeholderholder">
                                <textarea class="form-control tu-info-desc" id="tu-info-desc" name="description">{{data.student_detail.info_desc}}</textarea>
                            </div>
                        </div>
                        <div class="form-group tu-checkarea">
                            <div class="tu-check tu-checkvtwo">
                                <# let is_verified = (data.student_detail.info_verified=='on') ? 'checked' : ''; #>
                                <input type="checkbox" id="bhourscheck2" name="info_verified" {{is_verified}}>
                                <label for="bhourscheck2"><?php esc_html_e('The above user details I provided are authentic & approved by this user', 'tuturn'); ?></label>
                            </div>
                        </div>
                    </div>                    
                    <div class="form-group gap-2 justify-content-between">
                        <a href="javascript:void(0);" class="tu-primbtn-lg tu-secbtnvtwo tu-secbtn tu-bookingbackstep" data-current_step="3" data-previous_step="2"><i class="icon icon-chevron-left"></i><span><?php esc_html_e('Back', 'tuturn'); ?></span></a>
                        <a href="javascript:void(0);" class="tu-primbtn-lg" data-student_id="<?php echo esc_attr($current_user->ID); ?>" data-instructor_profile_id="<?php echo esc_attr($post->ID); ?>" id="tu-next-addbook-step3"> <span><?php esc_html_e('Save & continue', 'tuturn'); ?></span><i class="icon icon-chevron-right"></i></a>
                    </div>
                </div>
            </div>
        </fieldset>
    </form>
    </div>
</script>
<script type="text/template" id="tmpl-tu-book-appointment-step4">
    <# if ( data.booking_option == 'yes') { #>
        <div class="tu-boxtitle">
            <h4><?php esc_html_e('Please check before you do','tuturn'); ?></h4>
        </div>
        <# if (!_.isEmpty(data.booked_information.student_detail)) { #>
            <ul class="tu-checkout">
                <li>
                    <h5 class="tu-checkout_title"><i class="icon icon-briefcase"></i><?php esc_html_e('Ordered services','tuturn'); ?></h5>
                </li>
                <# _.each( data.booked_information.student_detail.service_detail , function( service_detail, service_key ) { #>
                    <li>
                        <span>{{service_detail.service_name}}</span>
                        <# if ( data.booking_option == 'yes') { #>
                            <h6>{{service_detail.service_price}}</h6>
                        <# } #>
                    </li>
                <# }); #>
                <# if ( data.booking_option == 'yes') { #>
                    <ul class="tu-subtotalv2">
                        <li>
                            <span><?php esc_html_e('Total','tuturn'); ?>:</span>
                            <h4>{{data.booked_information.student_detail.total_price}}</h4>
                        </li>
                    </ul>
                <# } #>
            </ul>
        <# } #>
    <# } #>
    <div class="tu-boxtitle">
        <h5 class="tu-checkout_title"><i class="icon icon-calendar"></i><?php esc_html_e('Booking date & time','tuturn'); ?></h5>
    </div>
    <# if (!_.isEmpty(data.booked_information.student_detail.total_booked_slots)) { #>
        <# _.each( data.booked_information.student_detail.total_booked_slots , function( booked_slots_intervals, booked_slot_date ) { #>
            <div class="tu-bookedslots">
                <h5>{{booked_slot_date}}</h5>
                <# if (!_.isEmpty(booked_slots_intervals)) { #>
                    <ul class="tu-checkout tu-checkoutvtwo">
                        <# _.each( booked_slots_intervals , function( booked_slots_innerIntervals, booked_slot_innerIntervalKey ) { #>
                            <li>
                                <span>({{booked_slots_innerIntervals.slotStart_time}}-{{booked_slots_innerIntervals.slotEnd_time}})</span>
                            </li>
                        <# }); #>
                    </ul>
                <# } #>
                
            </div>
        <# }); #>
    <# } #>
    <# if (!_.isEmpty(data.booked_information.student_detail.service_info)) { #>
        <ul class="tu-checkout">
            <li>
                <h5 class="tu-checkout_title"><i class="icon icon-user"></i><?php esc_html_e('User personal details','tuturn'); ?></h5>
            </li>
            <# if (!_.isEmpty(data.booked_information.student_detail.service_info.info_full_name)) { #>
            <li>
                <span><?php esc_html_e('Full name','tuturn'); ?>:</span>
                <h6>{{data.booked_information.student_detail.service_info.info_full_name}}</h6>
            </li>
            <# } #>
            <# if (!_.isEmpty(data.booked_information.student_detail.service_info.info_email)) { #>
            <li>
                <span><?php esc_html_e('Email ID','tuturn'); ?>:</span>
                <h6>{{data.booked_information.student_detail.service_info.info_email}}</h6>
            </li>
            <# } #>
            <# if (!_.isEmpty(data.booked_information.student_detail.service_info.info_phone)) { #>
            <li>
                <span><?php esc_html_e('Phone number','tuturn'); ?>:</span>
                <h6>{{data.booked_information.student_detail.service_info.info_phone}}</h6>
            </li>
            <# } #>
            <# if (!_.isEmpty(data.booked_information.student_detail.service_info.info_address)) { #>
            <li>
                <span><?php esc_html_e('Address','tuturn'); ?>:</span>
                <h6>{{data.booked_information.student_detail.service_info.info_address}}</h6>
            </li>
            <# } #>
            <# if (!_.isEmpty(data.booked_information.student_detail.service_info.info_relation)) { #>
            <li>
                <span><?php esc_html_e('Relation with you','tuturn'); ?>:</span>
                <h6>{{data.booked_information.student_detail.service_info.info_relation}}</h6>
            </li>
            <# } #>
            <# if (!_.isEmpty(data.booked_information.student_detail.service_info.info_desc)) { #>
            <li class="tu-service-descwrap">
                <p class="tu-service-desc">{{data.booked_information.student_detail.service_info.info_desc}}</p>
            </li>
            <# } #>
        </ul>
    <# } #> 
    <div class="tu-btnareabtm gap-2">
        <a href="javascript:void(0);" class="tu-primbtn-lg tu-secbtnvtwo tu-secbtn tu-bookingbackstep" data-current_step="4" data-previous_step="3"><i class="icon icon-chevron-left"></i><span><?php esc_html_e('Back','tuturn'); ?></span></a>
        <# if ( data.booking_option == 'yes') { #>
            <a href="javascript:void(0);" class="tu-primbtn-lg tu-primbtn" id="tu-next-addbook-checkout" data-type="service_cart" data-service_author="<?php echo esc_attr($instructor_id); ?>" data-loggedin_user="<?php echo esc_attr($current_user->ID); ?>"><span><?php esc_html_e('Proceed to checkout','tuturn'); ?></span><i class="icon icon-chevron-right"></i></a>
        <# } else if ( data.booking_option == 'no') { #>
            <a href="javascript:void(0);" class="tu-primbtn-lg tu-primbtn" id="tu-next-complete-booking" data-type="service_cart" data-service_author="<?php echo esc_attr($instructor_id); ?>" data-loggedin_user="<?php echo esc_attr($current_user->ID); ?>"><span><?php esc_html_e('Complete booking','tuturn'); ?></span><i class="icon icon-chevron-right"></i></a>
        <# } #> 
    </div>
</script>
<script type="text/template" id="tmpl-tu-book-appointment-step0">
    <# if (!_.isEmpty(data)) { #>
        <div class="tu-wizardleft">
            <div class="tu-wizardtitle">
                <h4><?php esc_html_e('Select Subjects you want', 'tuturn'); ?></h4>
                <a href="javascript:void(0);">
                    <span class="icon icon-x"></span>
                </a>
            </div>
            <div class="tu-wizserviceslist">
                <# _.each( data.subjects , function( subjects, key ) { #>
                    <# if (!_.isEmpty(subjects.child_cats)) { #>
                        <div class="tu-wizardservices">
                            <div class="tu-servicestitle">
                                <h4>{{subjects.parent_cat.name}}</h4>
                            </div>
                            <ul class="tu-wizardlist">
                                <# _.each( subjects.child_cats , function( child_subjects, key ) { #>
                                    <li class="tu-book-services" data-services_ids="{{child_subjects.subject_id}}" data-book_service_val="{{child_subjects.is_checked ? true : false}}">
                                        <input type="checkbox" name="subject_select[]" class="book-subject-check" id="subject_select" value="{{child_subjects.subject_id}}" {{child_subjects.is_checked}}>
                                        <label for="byhoura" class="tu-bodyitem">
                                            <img src="{{child_subjects.subject_image}}" alt="{{child_subjects.subject_name}}">
                                            <span class="tu-bodyaccordinfo">
                                                <em>{{child_subjects.subject_name}} </em>
                                                <span><?php esc_html_e('Starting from','tuturn'); ?> <i>{{child_subjects.subject_price}}</i></span>
                                            </span>
                                        </label>
                                    </li>
                                <# }); #>
                            </ul>
                        </div>
                    <# } #>
                <# }); #>
            </div>
            <div class="tu-btnsnexts">
                <a href="javascript:void(0);" class="tu-pb" id="tu-next-addbook-step1" data-provider_id="<?php echo intval($user_id); ?>"><span><?php esc_html_e('Next', 'tuturn'); ?></span><i class="icon-chevron-right"></i></a>
            </div>
        </div>
    <# } #>
</script>