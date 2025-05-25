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
global $post,$tuturn_settings;
$profile_id             = !empty($post->ID) ? intval($post->ID) : 0;
$teaching_preference    = get_post_meta( $profile_id,'teaching_preference',true );
$teaching_preference    = !empty($teaching_preference) ? $teaching_preference : array();
$teach_settings         = !empty($tuturn_settings['teach_settings']) ? $tuturn_settings['teach_settings'] : 'default';

if(!empty($teaching_preference)){?>
    <div class="tu-instructors_service">
        <p> <?php esc_html_e('You can get teaching service direct at','tuturn'); ?> </p>
        <?php if(!empty($teaching_preference)){?>
            <ul class="tu-instructors_service-list"> 
                <?php 
                    if( !empty($teach_settings) && $teach_settings === 'default'){
                        for($i=0;$i<count($teaching_preference);$i++) {
                            $preference = $teaching_preference[$i];
                            if($preference  === 'home'){ ?>
                                <li><i class="icon icon-home tu-greenclr"></i><span><?php  esc_html_e('My home','tuturn')?></span></li>
                            <?php }   
                            if($preference  === 'student_home'){?>
                                <li><i class="icon icon-map-pin tu-blueclr"></i><span><?php esc_html_e('Student\'s home', 'tuturn')?> </span></li>
                            <?php }
                            if($preference  === 'online'){?>
                                <li><i class="icon icon-video tu-orangeclr"></i><span><?php esc_html_e('Online','tuturn')?> </span></li>
                            <?php } 
                        }
                    } else if( !empty($teach_settings) && $teach_settings === 'custom'){ ?>
                        <?php if(!empty($teaching_preference) && in_array('online', $teaching_preference)){ ?>
                            <li><i class="icon icon-video tu-orangeclr"></i><span><?php esc_html_e('Online','tuturn')?> </span></li>
                        <?php } 
                            if(!empty($teaching_preference) && in_array('offline', $teaching_preference)){
                            $offline_place      = get_post_meta($profile_id,'offline_place',true );
                            $offline_place      = !empty($offline_place) ? ($offline_place) : array();
                            
                            ?>
                            <li><i class="icon icon-offline tu-colorblue icon-map-pin"></i><span><?php esc_html_e('Offline','tuturn')?></span></li>
                            <?php if( !empty($offline_place) ){
                                if(is_array($offline_place)){
                                foreach($offline_place as $key => $val){
                                    $offline_lable  = tuturn_offline_places_lists($val);
                                    $icon   = !empty($val) && $val == 'tutor' ? 'icon-navigation tu-colorgreen' :  'icon-home tu-colororange';
                                ?>
                                <li><i class="icon <?php echo esc_attr($icon);?>"></i><span><?php echo esc_html($offline_lable);?></span></li>
                            <?php }} else {  
                                $offline_lable  = tuturn_offline_places_lists($offline_place);?>
                                <li><i class="icon map-pin tu-colororange"></i><span><?php echo esc_html($offline_lable);?></span></li>
                            <?php } } ?>
                    <?php } ?>
            <?php } ?>
            </ul>
        <?php } ?>
    </div>
    <?php 
}
