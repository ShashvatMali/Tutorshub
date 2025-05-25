<?php

/**
 * Student profile settigns side menu
 *
 * @package     Tuturn
 * @subpackage  Tuturn/Templates/Profile_Settings/student
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
 */
global $tuturn_settings;
$settings_tabs  = array('personal_details', 'contact_details', 'education', 'subjects', 'bookings', 'media', 'security');
$avatar         = apply_filters(
    'tuturn_avatar_fallback',
    tuturn_get_user_avatar(array('width' => 400, 'height' => 400), $profile_id),
    array('width' => 400, 'height' => 400)
);
$prof_mx_image_size     = !empty($tuturn_settings['prof_mx_image_size']) ? $tuturn_settings['prof_mx_image_size'] : '5MB';
$prof_mx_image_width    = !empty($tuturn_settings['prof_mx_image_width']) ? $tuturn_settings['prof_mx_image_width'] : '1000';
$prof_mx_image_height   = !empty($tuturn_settings['prof_mx_image_height']) ? $tuturn_settings['prof_mx_image_height'] : '1000';
$booking_option         = !empty($tuturn_settings['booking_option']) ? $tuturn_settings['booking_option'] : 'yes';
$invoice_page_hide      = !empty($tuturn_settings['invoice_page_hide']) ? $tuturn_settings['invoice_page_hide'] : 'show';

$identity_verification          = !empty($tuturn_settings['identity_verification']) ? $tuturn_settings['identity_verification'] : '';
$resubmit_verification          = !empty($tuturn_settings['resubmit_verification']) ? $tuturn_settings['resubmit_verification'] : false;
$instructor_hours_submission    = !empty($tuturn_settings['instructor_hours_submission']) ? $tuturn_settings['instructor_hours_submission'] : false;
$enable_delete_account          = !empty($tuturn_settings['enable_delete_account']) ? $tuturn_settings['enable_delete_account'] : 'no';
$profile_settings_url           = get_permalink();

if (!empty($user_identity)) {
    $profile_settings_url   = add_query_arg(array('useridentity' => $user_identity), $profile_settings_url);
}

?>
<aside class="tu-asider-holder">
    <div class="tu-asidebox tu-profilehead" id="tuturn-droparea">
        <div id="tu-asideprostatusv2" class="tu-asideprostatusv2">
            <?php if (!empty($avatar)) { ?>
                <figure>
                    <img src="<?php echo esc_url($avatar); ?>" id="user_profile_avatar" alt="<?php echo esc_attr($user_name); ?>">
                    <figcaption class="tu-uploadimage">
                        <a href="javascript:void(0);" id="profile-avatar-icon"><i class="icon icon-camera"></i></a>
                    </figcaption>
                </figure>
            <?php } ?>
        </div>
        <div class="tu-uploadinfo">
            <h6><?php echo sprintf(__("Your photo should not exceed %dMB and dimensions should be %dpx x %dpx. You can upload .jpg or .png file format", "tuturn"), $prof_mx_image_size, $prof_mx_image_width, $prof_mx_image_height); ?> </h6>
            <a id="profile-avatar" href="javascript:void(0);" class="tu-primbtn"><?php esc_html_e('Upload photo', 'tuturn'); ?></a>
        </div>
    </div>
    <ul class="nav nav-tabs tu-side-tabs" id="myTab" role="tablist">
        <li class="nav-item<?php if (empty($_GET['tab']) || (!empty($_GET['tab']) && in_array($_GET['tab'], $settings_tabs))) {
                                echo esc_attr(' active');
                            } ?> tu-sidebar-dropdown" role="presentation">
            <a class="nav-link<?php if (empty($_GET['tab']) || (!empty($_GET['tab']) && in_array($_GET['tab'], $settings_tabs))) {
                                    echo esc_attr(' active');
                                } ?>" href="javascript:void(0);"> <i class="icon icon-settings"></i> <?php esc_html_e('Profile settings', 'tuturn'); ?></a>
            <div class="tu-sidebar-submenu">
                <ul class="tu-nestedmenu">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link<?php if (empty($_GET['tab']) || (!empty($_GET['tab']) && $_GET['tab'] == 'personal_details')) {
                                                echo esc_attr(' active');
                                            } ?>" id="home-tab" href="<?php echo esc_url(add_query_arg(array('tab' => 'personal_details'), $profile_settings_url)); ?>"><span><?php esc_html_e('Personal details', 'tuturn'); ?></span></a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link<?php if (!empty($_GET['tab']) && $_GET['tab'] == 'contact_details') {
                                                echo esc_attr(' tab-active');
                                            } ?>" id="contact-tab" href="<?php echo esc_url(add_query_arg(array('tab' => 'contact_details'), $profile_settings_url)); ?>"><span><?php esc_html_e('Contact details', 'tuturn'); ?></span></a>
                    </li>
                    <?php if (!empty($enable_delete_account) && $enable_delete_account == 'yes') { ?>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link<?php if (!empty($_GET['tab']) && $_GET['tab'] == 'security') {
                                                    echo esc_attr(' active');
                                                } ?>" id="security-tab" href="<?php echo esc_url(add_query_arg(array('tab' => 'security'), $profile_settings_url)); ?>"><span><?php esc_html_e('Security', 'tuturn'); ?></span></a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </li>
        <li class="nav-item<?php if (!empty($_GET['tab']) && $_GET['tab'] == 'saved') {
                                echo esc_attr(' active');
                            } ?>" role="presentation">
            <a class="nav-link" id="education-tab" href="<?php echo esc_url(add_query_arg(array('tab' => 'saved'), $profile_settings_url)); ?>"><i class="icon icon-heart"></i><span><?php esc_html_e('Favourites', 'tuturn'); ?></span></a>
        </li>
        <?php if (!empty($instructor_hours_submission)) { ?>
            <li class="nav-item <?php if (!empty($_GET['tab']) && in_array($_GET['tab'], array('hours'))) {
                                    echo esc_attr('active');
                                } ?>" role="presentation">
                <a class="nav-link <?php if (!empty($_GET['tab']) && in_array($_GET['tab'], array('hours'))) {
                                        echo esc_attr('active');
                                    } ?>" id="media-tab" href="<?php echo esc_url(add_query_arg(array('tab' => 'hours'), $profile_settings_url)); ?>"> <i class="icon icon-clock"></i> <?php esc_html_e('Tutoring hour log', 'tuturn'); ?></a>
            </li>
        <?php } ?>
        <?php if (!empty($identity_verification) && ($identity_verification === 'students') || ($identity_verification === 'both')) {
            $identity_verified  	    = get_user_meta($user_identity, 'identity_verified', true);
            $identity_verified		    = !empty($identity_verified) ? $identity_verified : 0;
            $verify_class = 'icon icon-x-circle';
            if( !empty($identity_verified) && $identity_verified == 1){
                $verify_class = 'icon icon-check-circle';
            }
            ?>
            <li class="nav-item <?php if (!empty($_GET['tab']) && in_array($_GET['tab'], array('verfication-listing', 'user-verification'))) {
                                    echo esc_attr('active');
                                } ?>" role="presentation">
                <a class="nav-link <?php if (!empty($_GET['tab']) && in_array($_GET['tab'], array('verfication-listing', 'user-verification'))) {
                                        echo esc_attr('active');
                                    } ?>" id="media-tab" href="<?php echo esc_url(add_query_arg(array('tab' => 'verfication-listing'), $profile_settings_url)); ?>"> <i class="<?php echo esc_attr($verify_class); ?>"></i> <?php esc_html_e('Identity verfication', 'tuturn'); ?></a>
            </li>
        <?php } ?>
        <?php if ($booking_option !== 'disable') { ?>
            <li class="nav-item<?php if (!empty($_GET['tab']) && $_GET['tab'] == 'booking-listings') {
                                    echo esc_attr(' active');
                                } ?>" role="presentation">
                <a class="nav-link" id="media-tab" href="<?php echo esc_url(add_query_arg(array('tab' => 'booking-listings'), $profile_settings_url)); ?>"> <i class="icon icon-book"></i> <?php esc_html_e('My bookings', 'tuturn'); ?></a>
            </li>
        <?php } ?>
        <?php if (!empty($invoice_page_hide) && $invoice_page_hide == 'show') { ?>
            <li class="nav-item<?php if (!empty($_GET['tab']) && $_GET['tab'] == 'invoices') {
                                    echo esc_attr(' active');
                                } ?>" role="presentation">
                <a class="nav-link<?php if (!empty($_GET['tab']) && $_GET['tab'] == 'invoices') {
                                        echo esc_attr(' active');
                                    } ?>" id="invoices-tab" href="<?php echo esc_url(add_query_arg(array('tab' => 'invoices'), $profile_settings_url)); ?>"> <i class="icon icon-home"></i> <?php esc_html_e('Invoices', 'tuturn'); ?></a>
            </li>
        <?php } ?>
    </ul>
</aside>
<?php tuturn_get_template_part('profile-settings/profile', 'avatar-popup');
