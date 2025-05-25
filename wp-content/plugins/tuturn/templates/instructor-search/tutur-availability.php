<?php

/**
 * price range
 *
 * @package     tuturn
 * @subpackage  tuturn/templates/instructor-search
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
 */
$time_day_key_arr   = apply_filters('tuturn_booking_time_of_day', '');
$weekdays			= tuturnGetWeekDays();
$available_time		= !empty($_GET['available_time']) ? $_GET['available_time'] : array();
$available_days		= !empty($_GET['available_days']) ? $_GET['available_days'] : array();
?>
<div class="tu-aside-holder">
    <div class="tu-asidetitle" data-bs-toggle="collapse" data-bs-target="#tutur-availability" role="button" aria-expanded="true">
        <h5><?php esc_html_e('Instructor availability','tuturn'); ?></h5>
    </div>
    <div id="tutur-availability" class="collapse show">
        <div class="tu-aside-content">
            <?php if( !empty($time_day_key_arr) ){ ?>
				<h6><?php esc_html_e('Time of day','tuturn');?></h6>
				<ul class="tu-categoriesfilter">
					<?php foreach($time_day_key_arr as $key => $value ){
						$heading  	= !empty($value['heading']) ? $value['heading'] : '';
						$icon		= !empty($value['icon']) ? $value['icon'] : '';
						$time_checked	= '';
						if( !empty($available_time) && in_array($key,$available_time) ){
							$time_checked	= 'checked';
						}
					?>
						<li>
							<div class="tu-check tu-checksm">
								<input name="available_time[]" value="<?php echo esc_attr($key);?>" type="checkbox" id="tu-availibility-<?php echo esc_attr($key);?>" <?php echo esc_attr($time_checked); ?>> 
								<label for="tu-availibility-<?php echo esc_attr($key);?>">
									<?php if( !empty($icon) ){?>
										<i class="<?php echo esc_attr($icon);?>"></i>
									<?php } ?>
									<?php echo esc_html($heading);?>
								</label>
							</div>
						</li>
					<?php } ?>
				</ul>
            <?php } ?>
			<?php if( !empty($weekdays) ){ ?>
				<h6><?php esc_html_e('Day of week','tuturn');?></h6>
				<ul class="tu-categoriesfilter">
					<?php foreach($weekdays as $day_key => $value ){
						$day_checked	= '';
						if( !empty($available_days) && in_array($day_key,$available_days) ){
							$day_checked	= 'checked';
						}
					?>
						<li>
							<div class="tu-check tu-checksm tu-<?php echo esc_attr($day_key);?>">
								<input name="available_days[]" value="<?php echo esc_attr($day_key);?>" type="checkbox" id="tu-day-availibility-<?php echo esc_attr($day_key);?>" <?php echo esc_attr($day_checked); ?>> 
								<label for="tu-day-availibility-<?php echo esc_attr($day_key);?>"><?php echo esc_html($value);?></label>
							</div>
						</li>
					<?php } ?>
				</ul>
            <?php } ?>
        </div>
    </div>
</div>

