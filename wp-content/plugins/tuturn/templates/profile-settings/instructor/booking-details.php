<?php
/**
 * Instructor booking details
 *
 * @package     Tuturn
 * @subpackage  Tuturn/Templates/Profile_Settings/Instructor
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
*/
global $current_user;
$userType	            = apply_filters('tuturnGetUserType', $current_user->ID ); 
$week_days              = apply_filters( 'tuturnGetWeekDays','');
$appointment_duration   = apply_filters( 'tuturnAppointmentDuration','');
$appointment_interval   = apply_filters( 'tuturnAppointmentInterval','');
$time_hours             = apply_filters( 'tuturnAppointmentTime','');
$booking_details        = get_post_meta($profile_id, 'tuturn_bookings', true);
$bookings               = !empty($booking_details['bookings']['timeSlots']['bookings_slots']) ? $booking_details['bookings']['timeSlots']['bookings_slots'] : array();

$profile_details        = get_post_meta($profile_id,'profile_details',true );
$profile_details        = !empty($profile_details) ? $profile_details : array();
$available_time         = !empty($profile_details['available_time']) ? $profile_details['available_time'] : array();

$drop_image         = TUTURN_DIRECTORY_URI . 'public/images/drop-img.png';

$days_array             = tuturnListWeekDays();
$time_format 	        = get_option('time_format');
$time_of_day    = tuturn_booking_time_of_day();

if(!empty($bookings)){
    $ordered_slots  = $bookings;
    $orderedArray = array();
    foreach ($days_array as $key=>$day) {
        if(!empty($bookings[$key])){
            $orderedArray[$key] = $bookings[$key];
        }
    }
    $bookings   = $orderedArray;
}


$imageSrc 		= TUTURN_DIRECTORY_URI.'/public/images/';


$is_timeslot            = !empty($booking_details) ? 1 : 0;
/* UnAvailable days */
$unavailable_days       = !empty($booking_details['bookings']['unavailable_slots']) ? $booking_details['bookings']['unavailable_slots'] : array();
$is_unavailable_days    = !empty($unavailable_days) ? 1 : 0;
$saved_weekdays = '';


?>
<div class="tu-boxarea">
    <div class="tu-boxsm">
        <div class="tu-boxsmtitle">
            <h4><?php esc_html_e('Booking details', 'tuturn');?></h4>
            <a href="javascript:void(0)" class="tu-add-booking-slots tu-add-appointment" data-profile_id="<?php echo intval($profile_id) ?>" data-user_id="<?php echo intval($current_user->ID); ?>" data-is_timeslot="<?php echo intval($is_timeslot); ?>" data-operation="add"><?php esc_html_e('Add/Edit bookings', 'tuturn'); ?></a>
        </div>
    </div>
    <div class="tu-box">      
        <div class="w-100">
            <?php 
            if(!empty($bookings)){
                $count = 0; ?>
                <div class="tu-themeform__wrap">
                    <div class="form-group-wrap">
                        <div class="w-100" id="time_accordion">
                            <?php foreach($bookings as $weekday=>$slots){
                                $count++;
                                $show_expand = ($count===1) ? 'true' : 'false';
                                $is_show = ($count==1) ? 'collapse show' : 'collapse';
                                ?>
                                <div class="tu-formarea timeslot-parent-container" id="parent-div-<?php echo esc_attr($weekday); ?>">
                                    <div class="tu-formarea_title" type="list" data-bs-toggle="collapse" data-bs-target="#<?php echo esc_attr($weekday); ?>" aria-expanded="<?php echo esc_attr($show_expand); ?>">
                                        <h5><?php if(isset($days_array[$weekday])){echo esc_attr($days_array[$weekday]);}?></h5>
                                        <?php if( !empty($available_time[$weekday]) ){?>
                                            <h6>
                                                <?php
                                                foreach($available_time[$weekday] as $av_day){
                                                    $icon_class = !empty($time_of_day[$av_day]['icon']) ? $time_of_day[$av_day]['icon'] : '';
                                                    if( !empty($time_of_day[$av_day]['heading'])){?>
                                                        <span><i class="<?php echo esc_attr($icon_class);?>"></i><?php echo esc_html($time_of_day[$av_day]['heading']);?></span>
                                                    <?php }
                                                }?>
                                            </h6>
                                        <?php } ?>
                                    </div>
                                    <div class="<?php echo esc_attr($is_show); ?>" data-bs-parent="#time_accordion" id="<?php echo esc_attr($weekday); ?>">
                                        <div class="tu-formarea_content">
                                            <div class="tu-formarea_group">
                                                <div class="form-group">
                                                    <ul class="tu-formarea_list tu-formarea_listvtwo" >
                                                        <?php if(!empty($slots)){
                                                            foreach($slots['slots'] as $slot_key=>$slot_val){ 
                                                                $slot_key_val 	= explode('-', $slot_val['time']);
                                                                $first_time 	= date($time_format, strtotime('2022-01-01' . $slot_key_val[0]));
                                                                $second_time 	= date($time_format, strtotime('2022-01-01' . $slot_key_val[1]));
                                                                ?>
                                                                <li id="<?php echo esc_attr($weekday); ?>-<?php echo esc_attr($slot_key); ?>">
                                                                    <a href="javascript:void(0);">
                                                                        <?php if( !empty($slot_val['slot_title']) ){?>
                                                                            <h5><?php echo esc_html($slot_val['slot_title'] );?></h5>
                                                                        <?php } ?>
                                                                        <h6><?php echo esc_html($first_time); ?> - <?php echo esc_html($second_time); ?></h6>
                                                                        <span><?php echo esc_html($slot_val['slot']); ?><?php esc_html_e(' Slots', 'tuturn'); ?></span>
                                                                    </a>
                                                                </li>
                                                                <?php 
                                                            } 
                                                        } ?>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<script type="text/template" id="tmpl-tu-add-appointment">
    <div class="modal-header">
        <h5><?php esc_html_e('Add/edit appointment details','tuturn'); ?></h5>
        <a href="javascript:void(0);" class="tu-close close-adappointment" type="button" data-bs-dismiss="modal" aria-label="Close"><i class="icon icon-x"></i></a>
    </div>
    <div class="modal-body">
        <div class="tu-themeform">
            <fieldset class="tu-fieldsetwrap">                     
                <div class="tu-themeform__wrap">
                    <div class="tu-tab tu-appointment-content">
                        <div class="form-group">
                            <ul class="nav nav-tabs" id="myTab1" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" data-is_timeslot="<?php echo intval($is_timeslot); ?>" id="tu-timeslot-tab" data-profile_id="<?php echo intval($profile_id) ?>" data-bs-toggle="tab" data-bs-target="#timeslot" type="button" role="tab" aria-controls="timeslot" aria-selected="true"><i class="icon icon-clock"></i><?php esc_html_e('Add time slots', 'tuturn'); ?></button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="tu-unavailable_dayslot-tab" data-profile_id="<?php echo intval($profile_id) ?>" data-is_unavailable_days="<?php echo intval($is_unavailable_days); ?>" data-bs-toggle="tab" data-bs-target="#dayslot" type="button" role="tab" aria-controls="dayslot" aria-selected="false"><i class="icon  icon icon-lock"></i><?php esc_html_e('Unavailable days', 'tuturn'); ?></button>
                                </li>
                            </ul>
                        </div>
                        <div class="tab-content tab-timeslots form-group" id="nav-tabContent1">
                            <div class="tab-pane fade show active tu-appointment-content-area" id="timeslot" role="tabpanel" aria-labelledby="nav-timeslot-tab"></div>
                        </div>
                    </div>
                </div>   
            </fieldset>
        </div>
    </div>
</script>
<!-- available generated timeslot -->
<script type="text/template" id="tmpl-tu-available-timeslots">
    <div class="tu-addattachs" id="tu_add_new_slot_btn">
        <a href="javascript:void(0);" class="tu-newslots tu-add-appointment-timeslot">
            <i class="fa fa-plus"></i> <?php esc_html_e('Add new time slots', 'tuturn'); ?>
            <svg><rect width="100%" height="100%"></rect></svg>
        </a>
    </div>
    <# if (!_.isEmpty(data.time_slots)) { #>
        <form class="tu-themeform tu-save-timeslots" id="tu-save-timeslots" name="tu-save-timeslots">
            <div id="time_accordion">
                <div id="time_accordionwrapper">
                    <# let count = 0; 
                        _.each(data.time_slots , function( slotimes, day ) { 
                            count++; 
                            let show_expand = (count==1) ? 'true' : 'false'; #>
                        <div class="tu-formarea timeslot-parent-container " id="parent-div-{{day}}">
                            <div class="tu-formarea_title" type="list" data-bs-toggle="collapse" data-bs-target="#{{day}}" aria-expanded="{{show_expand}}">
                                <img class="tu-drop-img tu-sort-handle" src="<?php echo esc_url($drop_image); ?>" alt="<?php esc_attr_e('Sort', 'tuturn'); ?>">
                                <h5>{{data.week_days[day]}}</h5>
                            </div>
                            <# let is_show = (count==1) ? 'collapse show' : 'collapse'; #>
                            <div class="{{is_show}}" data-bs-parent="#time_accordion" id="{{day}}">
                                <div class="tu-formarea_content">
                                    <div class="tu-formarea_group">
                                        <div class="form-group">
                                            <ul class="tu-formarea_list">
                                            <# if (!_.isEmpty(slotimes))  { 
                                                    _.each( slotimes , function( slottime, key ) { 
                                                    let slotIndex = Math.floor((Math.random() * 9999999999999) + 1);
                                                    #>
                                                    <li id="{{day}}-{{key}}">
                                                        <a href="javascript:void(0);">
                                                            <# if (!_.isEmpty(slottime.slot_title)) { #>
                                                                <h5>{{ slottime.slot_title }}</h5>
                                                            <# } #>
                                                            <h6>{{ slottime.start_time }} - {{ slottime.end_time }}</h6>
                                                            <span>{{ slottime.slots }} <?php esc_html_e('Slots', 'tuturn'); ?></span>
                                                            <i class="icon icon-trash-2 tu-timeslot-del" data-slot_key="{{slottime.slot_key}}" data-day="{{day}}" ></i>
                                                        </a>
                                                        <input type="hidden" name="bookings_slots[{{day}}][slots][{{slotIndex}}][slot_title]" value="{{slottime.slot_title}}">
                                                        <input type="hidden" name="bookings_slots[{{day}}][slots][{{slotIndex}}][time]" value="{{slottime.slot_key}}">
                                                        <input type="hidden" name="bookings_slots[{{day}}][slots][{{slotIndex}}][slot]" value="{{slottime.slots}}">
                                                    </li>
                                                <# }); 
                                                } #>
                                            </ul>
                                        </div>
                                        <div class="form-group tu-formbtn tu-dayslots-del" data-day="{{day}}">
                                            <a href="javascript:void(0);" class="tu-primbtn-lg tu-redbtn" ><span><?php esc_html_e('Remove this time slot', 'tuturn'); ?></span><i class="icon icon-trash-2"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <# }); #>
                </div>
            </div>
            <div class="tu-formbtn">
                <a href="javascript:void(0);" class="tu-primbtn-lg" id="tu-save-timeslots-btn" data-profile_id="<?php echo intval($profile_id); ?>" data-user_id="<?php echo intval($current_user->ID); ?>" ><?php esc_html_e('Save & update changes','tuturn'); ?></a>
            </div>
        </form>
    <# } #>
</script>
<!-- form generate timeslot -->
<script type="text/template" id="tmpl-tu-edit-timeslots">
    <div class="tab-pane fade show active" id="timeslot" role="tabpanel" aria-labelledby="nav-timeslot-tab">
        <form class="tu-themeform tu-timeslotform" id="tu-add-timeslots" name="tu-add-timeslots">
            <fieldset>               
                <div class="form-group">
                    <label class="tu-label"><?php esc_html_e('Available service days', 'tuturn'); ?></label>
                    <div class="tu-select">
                        <select data-select2-id="tu-appointment-weekdays" data-name="weekdays" data-placeholderinput="<?php esc_attr_e('Select days from list', 'tuturn'); ?>" id="tu-weekdays" class="form-control tu-form-input" data-placeholder="<?php esc_attr_e('Select days from list', 'tuturn') ?>" required multiple>
                            <option label="<?php esc_attr_e('Select days from list', 'tuturn'); ?>"></option>
                            <# _.each( data.weekDays, function( day, index ) {
                                let selected = data.selectedDays.includes(index)==true ? 'selected' : '';
                                #>
                                <option value="{{index}}">{{day}}</option>
                            <# })#>
                        </select>
                    </div>
                    <ul class="tu-labels" id="tu_wrappersortable"></ul>
                </div>
                <div class="form-group tu-full-wide">
                    <label class="tu-label"><?php esc_html_e('Slot title', 'tuturn'); ?></label>
                    <div class="form-group tu-placeholderholder">
                        <input type="text"  name="tu_slot_title" placeholder="<?php esc_html_e('Slot title', 'tuturn'); ?>" required>
                    </div>
                </div>
                <div class="form-group-wrap">
                    <div class="form-group pb-0">
                        <label class="tu-label"><?php esc_html_e('Start & end time', 'tuturn'); ?></label>
                    </div>
                    <div class="form-group form-group-half">
                        <div class="tu-select">
                            <select id="start-time" data-placeholderinput ="<?php esc_attr_e('Select start time', 'tuturn'); ?>" name="tu_appointment_starttime" class="form-control tu-form-input" data-placeholder="<?php esc_attr_e('Select start time', 'tuturn') ?>" required>
                                <option label="<?php esc_attr_e('Select start time', 'tuturn'); ?>"></option>
                                <?php
                                    if(!empty($time_hours)){
                                        foreach($time_hours as $key=>$hours){
                                            $hours 	= date($time_format, strtotime('2022-01-01 ' . $hours));
                                            ?>
                                            <option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($hours); ?></option>
                                            <?php
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group form-group-half">
                        <div class="tu-select">
                            <select id="end-time"  data-placeholderinput ="<?php esc_attr_e('Select end time', 'tuturn'); ?>" name="tu_appointment_endtime" class="form-control tu-form-input" data-placeholder="<?php esc_attr_e('Select end time', 'tuturn') ?>" required>
                                <option label="<?php esc_attr_e('Select end time', 'tuturn'); ?>"></option>
                                <?php
                                    if(!empty($time_hours)){
                                        foreach($time_hours as $key=>$hours){
                                            $hours 	= date($time_format, strtotime('2022-01-01 ' . $hours));
                                            ?>
                                            <option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($hours); ?></option>
                                            <?php
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="tu-label"><?php esc_html_e('Break time', 'tuturn'); ?></label>
                    <div class="tu-select">
                        <select id="inter-duration"  data-placeholderinput ="<?php esc_attr_e('Select  break time', 'tuturn'); ?>" name="tu_appointment_interval" class="form-control tu-form-input" data-placeholder="<?php esc_attr_e('Select  break time', 'tuturn') ?>">
                            <option label="<?php esc_attr_e('Select  break time', 'tuturn'); ?>"></option>
                            <?php
                                if(!empty($appointment_interval)){
                                    foreach($appointment_interval as $key=>$interval){
                                        ?>
                                        <option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($interval); ?></option>
                                        <?php
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="tu-label"><?php esc_html_e('Session duration', 'tuturn'); ?></label>
                    <div class="tu-select">
                        <select id="apintment-duration" data-placeholderinput="<?php esc_attr_e('Select duration from list', 'tuturn') ?>" name="tu_appointment_duration" class="form-control tu-form-input" data-placeholder="<?php esc_attr_e('Select duration from list', 'tuturn') ?>" required>
                            <option label="<?php esc_attr_e('Select duration from list', 'tuturn'); ?>"></option>
                            <?php
                            if(!empty($appointment_duration)){
                                foreach($appointment_duration as $key=>$duration){
                                    ?>
                                        <option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($duration); ?></option>
                                    <?php
                                }
                            }
                            ?>                                
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="tu-label"><?php esc_html_e('No. of appointment spaces', 'tuturn'); ?></label>
                    <div class="tu-apspaces">
                        <div class="tu-check">
                            <input type="radio" id="appointment-space1" value="1" name="appointment_spaces">
                            <label for="appointment-space1"><?php echo intval(01); ?></label>
                        </div>
                        <div class="tu-check">
                            <input type="radio" id="appointment-space2" value="2" name="appointment_spaces">
                            <label for="appointment-space2"><?php echo intval(02); ?></label>
                        </div>
                        <div class="tu-check">
                            <input type="radio" id="appointment-space3" value="3" name="appointment_spaces">
                            <label for="appointment-space3"><?php echo intval(03); ?></label>
                        </div>
                        <div class="tu-check">
                            <input type="radio" id="appointment-space-other" value="others" name="appointment_spaces">
                            <label for="appointment-space-other"><?php esc_html_e('Others', 'tuturn') ?></label>
                        </div>
                        <input type="number" id="appointment_custom_val" name="appointment_custom_val" class="form-control tu-form-input appointment-custom-val" placeholder="<?php esc_attr_e('Add custom value', 'tuturn');?>">
                    </div>
                </div>
                <input type="hidden" name="profile_id" value="<?php echo esc_attr($profile_id); ?>">
                <div class="form-group tu-formbtn">
                    <a href="javascript:void(0);" data-profile_id="<?php echo intval($profile_id) ?>" data-user_id="<?php echo intval($current_user->ID) ?>" id="tu-generate-timeslots" class="tu-primbtn tu-greenbtn"><?php esc_html_e('Generate time slots', 'tuturn'); ?></a>
                    <# if( data.selectedDays.length ){ #>
                        <a href="javascript:void(0);" class="tu-primbtn tu-btngray tu-cancel-appointment"><?php esc_html_e('Cancel', 'tuturn') ?></a>
                    <# } #>
                </div>
            </fieldset>
        </form>
    </div>            
</script>
<!-- unavailable generated days slot -->
<script type="text/template" id="tmpl-tu-unavailable_days">
    <div class="tu-addattachs add-unavailable-days">
        <a href="javascript:void(0);" class="tu-newslots tu-unavailable-slots" data-is_unavailable_days="<?php echo intval($is_unavailable_days); ?>">
            <i class="fa fa-plus"></i> <?php esc_html_e('Add unavailable days', 'tuturn'); ?>
            <svg><rect width="100%" height="100%"></rect></svg>
        </a>
    </div>
    <# if (!_.isEmpty(data.days_slots)) { #>
    <form class="tu-themeform" id="tu-save-unavailable-days-form" name="tu-save-unavailable-days-form">
        <div id="day_accordian">
            <ul class="tu-undayslist" id="unavailable-slots-days">
                <#
                        _.each( data.days_slots , function( slotday, key ) {
                        let dayIndex = Math.floor((Math.random() * 9999999999999) + 1); #>
                        <li>
                            <div class="tu-undayslist_content">
                                <div class="tu-undayslist_title">
                                    <h6><?php esc_html_e('Unavailable on', 'tuturn') ?></h6>
                                    <#
                                    let upcoming = '';
                                    let now_date = Math.floor(new Date().getTime()/1000);
                                    let text_class="";
                                    if(slotday.start_date == slotday.end_date){
                                        upcoming = (now_date < slotday.start_date) ? 'Upcoming' : 'Expired';
                                        text_class = (now_date < slotday.start_date) ? '' : 'tu-expired';
                                    } else {
                                        if(now_date < slotday.start_date){
                                            upcoming = 'Upcoming';
                                        }
                                        if(now_date > slotday.start_date && now_date < slotday.end_date){
                                            upcoming = 'Ongoing';
                                            text_class = 'tu-ongoing';
                                        }
                                        if(now_date > slotday.start_date && now_date > slotday.end_date){
                                            upcoming = 'Expired';
                                            text_class = 'tu-expired';
                                        }
                                    }
                                    #>
                                    <h5><b>{{slotday.date_string}}</b> <span class="tu-tag"><em class="{{text_class}}">{{upcoming}}</em></span></h5>
                                </div>
                                <a href="javascript:void(0);" class="tu-unavailalbe-delete-slot" data-index="{{key}}"><i class="icon icon-trash-2"></i></a>
                            </div>
                            <input type="hidden" name="unavailabledays[{{dayIndex}}][today_day]" value="{{slotday.today_day}}">
                            <input type="hidden" name="unavailabledays[{{dayIndex}}][date_string]" value="{{slotday.date_string}}">
                            <input type="hidden" name="unavailabledays[{{dayIndex}}][start_date]" value="{{slotday.start_date}}">
                            <input type="hidden" name="unavailabledays[{{dayIndex}}][end_date]" value="{{slotday.end_date}}">
                        </li>
                    <# }); #>                  
            </ul>
        </div>
        <div class="tu-formbtn">
            <a href="javascript:void(0);" data-user_id="<?php echo intval($current_user->ID); ?>" data-profile_id="<?php echo intval($profile_id) ?>" id="tu-save-unavailable-days" class="tu-primbtn-lg"><?php esc_html_e('Save & update changes', 'tuturn') ?></a>
        </div>
    </form>
    <# } #>
</script>
<!-- unavailable form with date picker -->
<script type="text/template" id="tmpl-tu-unavailable_date">
    <div class="form-group">
        <label class="tu-label"><?php esc_html_e('Start & end dates', 'tuturn') ?></label>
        <div class="tu-placeholderholder">
            <div class="tu-calendar">
                <input type="text" class="datepicker form-control tu-form-input tu-unavailable-picker" placeholder=" " required>
                <div class="tu-placeholder">
                    <span><?php esc_html_e('Select start & end dates', 'tuturn') ?></span>
                    <em>*</em>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group tu-formbtn">
        <a href="javascript:void(0);" data-profile_id="<?php echo intval($profile_id) ?>" data-user_id="<?php echo intval($current_user->ID); ?>" id="tu-generate-unavailable-days" class="tu-primbtn-lg tu-greenbtn"><?php esc_html_e('Mark these days unavailable', 'tuturn') ?></a>
    </div>
</script>