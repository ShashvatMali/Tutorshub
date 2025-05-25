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
$phone		        = !empty($profile_details['contact_info']['phone']) ? $profile_details['contact_info']['phone'] : '';
$skypeid		    = !empty($profile_details['contact_info']['skypeid']) ? $profile_details['contact_info']['skypeid'] : '';
$website		    = !empty($profile_details['contact_info']['website']) ? $profile_details['contact_info']['website'] : '';
$whatsapp_number    = !empty($profile_details['contact_info']['whatsapp_number']) ? $profile_details['contact_info']['whatsapp_number'] : '';
$email_address		= !empty($profile_details['contact_info']['email_address']) ? $profile_details['contact_info']['email_address'] :  $current_user->user_email;
$media_urls         = tuturn_social_media_lists();
$social_media       = !empty($tuturn_settings['social_media']) ? $tuturn_settings['social_media'] : array();
$conteact_details      = !empty($tuturn_settings['hide_conteact_details']) ? $tuturn_settings['hide_conteact_details'] : false;
?>
<div class="tu-boxarea">
    <div class="tu-boxsm">
        <div class="tu-boxsmtitle">
            <h4><?php esc_html_e('My Contact', 'tuturn');?></h4>
        </div>
    </div>
    <div class="tu-box">       
        <fieldset>
            <div class="tu-themeform__wrap">
                <div class="form-group-wrap">
                    <?php if( !empty($conteact_details)){?>
                        <div class="form-group">
                            <?php esc_html_e('Your phone number and email are semi-confidential, they are  visible to management and confirmed booking tutor/students.
    ','tuturn')?>
                        </div>
                    <?php }?>
                    <div class="form-group form-group-half">
                        <label class="tu-label"><?php esc_html_e('Phone number', 'tuturn');?></label>
                        <div class="tu-inputicon">
                            <div class="tu-facebookv3">
                                <i class="icon icon-phone-call"></i>
                            </div>
                            <div class="tu-placeholderholder">
                                <input type="text" class="form-control" required="" name="contact_info[phone]" value="<?php echo esc_attr($phone);?>" placeholder=" ">
                                <div class="tu-placeholder">
                                    <span><?php esc_html_e('Enter phone number', 'tuturn');?></span>
                                    <em><?php esc_html_e('*','tuturn')?></em>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group form-group-half">
                        <label class="tu-label"><?php esc_html_e('Enter email address', 'tuturn');?></label>
                        <div class="tu-inputicon">
                            <div class="tu-facebookv3">
                                <i class="icon icon-mail"></i>
                            </div>
                            <div class="tu-placeholderholder">
                                <input type="email" class="form-control" required="" name="contact_info[email_address]" value="<?php echo esc_attr($email_address);?>" placeholder=" ">
                                <div class="tu-placeholder">
                                    <span><?php esc_html_e('Enter email address', 'tuturn');?></span>
                                    <em><?php esc_html_e('*','tuturn')?></em>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="tu-label"><?php esc_html_e('Website', 'tuturn');?></label>
                        <div class="tu-inputicon">
                            <div class="tu-facebookv3">
                                <i class="icon icon-globe"></i>
                            </div>
                            <div class="tu-placeholderholder">
                                <input type="text" class="form-control" name="contact_info[website]" value="<?php echo esc_attr($website);?>" placeholder=" ">
                                <div class="tu-placeholder">
                                    <span><?php esc_html_e('Enter website URL', 'tuturn');?></span>
                                    <em><?php esc_html_e('*','tuturn')?></em>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group form-group-half">
                        <label class="tu-label"><?php esc_html_e('Whatsapp number', 'tuturn');?></label>
                        <div class="tu-inputicon">
                            <div class="tu-facebookv3">
                                <i class="fa-brands fa-whatsapp"></i>
                            </div>
                            <div class="tu-placeholderholder">
                                <input type="text" class="form-control" name="contact_info[whatsapp_number]" value="<?php echo esc_attr($whatsapp_number);?>" placeholder=" ">
                                <div class="tu-placeholder">
                                    <span><?php esc_html_e('Enter whatsapp number', 'tuturn');?></span>
                                    <em><?php esc_html_e('*','tuturn')?></em>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group form-group-half">
                        <label class="tu-label"><?php esc_html_e('Enter skype id', 'tuturn');?></label>
                        <div class="tu-inputicon">
                            <div class="tu-facebookv3">
                                <i class="fa-brands fa-skype"></i>
                            </div>
                            <div class="tu-placeholderholder">
                                <input type="text" class="form-control" name="contact_info[skypeid]" value="<?php echo esc_attr($skypeid);?>" placeholder=" ">
                                <div class="tu-placeholder">
                                    <span><?php esc_html_e('Enter skype id', 'tuturn');?></span>
                                    <em><?php esc_html_e('*','tuturn')?></em>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php if( !empty($social_media) ){
                            foreach($social_media as $key ){
                                $media_url	= !empty($profile_details['contact_info'][$key]) ? $profile_details['contact_info'][$key] : '';
                                $icon       = !empty($media_urls[$key]['icon']) ? $media_urls[$key]['icon'] : '';
                                $title      = !empty($media_urls[$key]['title']) ? $media_urls[$key]['title'] : '';
                                ?>
                                <div class="form-group form-group-half">
                                    <?php if( !empty($title) ){?>
                                        <label class="tu-label"><?php echo esc_html($title);?></label>
                                    <?php } ?>
                                    <div class="tu-inputicon">
                                        <?php if( !empty($icon) ){?>
                                            <div class="tu-facebookv3">
                                                <i class="<?php echo esc_attr($icon);?>"></i>
                                            </div>
                                        <?php } ?>
                                        <div class="tu-placeholderholder">
                                            <input type="text" class="form-control" name="contact_info[<?php echo esc_attr($key);?>]" value="<?php echo esc_url($media_url);?>" placeholder=" ">
                                            <?php if( !empty($title) ){?>
                                                <div class="tu-placeholder">
                                                    <span><?php echo esc_html($title);?></span>
                                                    <em><?php esc_html_e('*','tuturn')?></em>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </fieldset>
    </div>
</div>