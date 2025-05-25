<?php
/**
 * Instructor profile picture
 *
 * @package     Tuturn
 * @subpackage  Tuturn/Templates/Profile_Settings/Instructor
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
*/
global $tuturn_settings;
$avatar = apply_filters(
    'tuturn_avatar_fallback', tuturn_get_user_avatar(array('width' => 100, 'height' => 100), $profile_id), array('width' => 100, 'height' => 100)
);
$prof_mx_image_size   = !empty($tuturn_settings['prof_mx_image_size']) ? $tuturn_settings['prof_mx_image_size'] : '5MB';
$prof_mx_image_width  = !empty($tuturn_settings['prof_mx_image_width']) ? $tuturn_settings['prof_mx_image_width'] : '1000';
$prof_mx_image_height = !empty($tuturn_settings['prof_mx_image_height']) ? $tuturn_settings['prof_mx_image_height'] : '1000';
$profile_settings_url = get_permalink();
if(!empty($user_identity)){
    $profile_settings_url   = add_query_arg(array('useridentity'=>$user_identity), $profile_settings_url);
}
?>
<aside class="tu-asider-holder">
    <div class="tu-asidebox" id="tuturn-droparea">
        <div id="tu-asideprostatusv2" class="tu-asideprostatusv2">
            <?php if( !empty($avatar) ){?>
                <figure>
                    <img src="<?php echo esc_url($avatar);?>" id="user_profile_avatar" alt="<?php echo esc_attr($user_name);?>">
                    <figcaption class="tu-uploadimage">
                        <a href="javascript:void(0);" id="profile-avatar-icon"><i class="icon icon-camera"></i></a>
                    </figcaption>
                </figure>           
            <?php } ?>
        </div>
        <div class="tu-uploadinfo text-center">
            <h6><?php echo sprintf(__("Your profile photo should not be exceed %dMB and dimensions %dpx width and height %dpx. you can upload .jpg or .png file format", "tuturn"), $prof_mx_image_size, $prof_mx_image_width, $prof_mx_image_height);?> </h6>
            <a id="profile-avatar" href="javascript:void(0);" class="tu-primbtn"><?php esc_html_e('Upload photo', 'tuturn');?></a>
        </div>
    </div>
    <ul class="nav nav-tabs tu-side-tabs" id="tu-profile-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link<?php if(!empty($_GET['tab']) && $_GET['tab'] == 'personal_details'){ echo esc_attr(' active');}?>" id="home-tab" href="<?php echo esc_url(add_query_arg(array('tab'=>'personal_details'), $profile_settings_url));?>"><i class="icon icon-user"></i><span><?php esc_html_e('Personal details', 'tuturn');?></span></a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link<?php if(!empty($_GET['tab']) && $_GET['tab'] == 'contact_details'){ echo esc_attr(' active');}?>" id="contact-tab" href="<?php echo esc_url(add_query_arg(array('tab'=>'contact_details'), $profile_settings_url));?>"><i class="icon icon-phone"></i><span><?php esc_html_e('Contact details', 'tuturn');?></span></a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link<?php if(!empty($_GET['tab']) && $_GET['tab'] == 'education'){ echo esc_attr(' active');}?>" id="education-tab" href="<?php echo esc_url(add_query_arg(array('tab'=>'education'), $profile_settings_url));?>"><i class="icon icon-book"></i><span><?php esc_html_e('Education', 'tuturn');?></span></a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link<?php if(!empty($_GET['tab']) && $_GET['tab'] == 'subjects'){ echo esc_attr(' active');}?>" id="subjects-tab" href="<?php echo esc_url(add_query_arg(array('tab'=>'subjects'), $profile_settings_url));?>"><i class="icon icon-book-open"></i><span><?php esc_html_e('Subjects I can teach', 'tuturn');?></span></a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link<?php if(!empty($_GET['tab']) && $_GET['tab'] == 'media'){ echo esc_attr(' active');}?>" id="media-tab" href="<?php echo esc_url(add_query_arg(array('tab'=>'media'), $profile_settings_url));?>"><i class="icon icon-image"></i><span><?php esc_html_e('Media gallery', 'tuturn');?></span></a>
        </li>
    </ul>
</aside>
<?php tuturn_get_template_part('profile-settings/profile', 'avatar-popup');
