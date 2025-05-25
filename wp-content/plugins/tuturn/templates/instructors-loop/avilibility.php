<?php
/**
 * Instructor teaching prefrtence
 *
 * @package     Tuturn
 * @subpackage  Tuturn/templates/nstructor-loop
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
 */
global $post;
$profile_id             = !empty($post->ID) ? intval($post->ID) : 0;
$time_of_day            = tuturnGetWeekDays();
?>
<div class="tu-instructors_service tu-instruc-avail">
    <p><?php esc_html_e('Availability','tuturn'); ?> </p>
    <ul class="tu-dayslist">
        <?php 
            foreach($time_of_day as $key => $time_day){
                $active_class   = '';
                if(metadata_exists( 'post', $post->ID, $key )){
                    $active_class   = 'tu-greenv2';
                }
            ?>
            <li class="tu-days">
                <span class="ti-day-name <?php echo esc_attr($active_class);?>"><?php echo esc_html($time_day);?></span>
            </li>
        <?php } ?>
    </ul>
</div>

<?php 

