<?php
/**
 * The template used for reset password
 *
 * @package     Tuturn
 * @subpackage  Tuturn/templates
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
*/
if(!function_exists('tuturn_forgot_password')){
	add_shortcode('tuturn_forgot_password', 'tuturn_forgot_password');
	function tuturn_forgot_password($atts){
		global $tuturn_settings,$wpdb;
        $bg_image           = !empty( $atts['background']) ? $atts['background'] : '';
        $logo               = !empty( $atts['logo']) ? $atts['logo'] : '';
        $tagline            = !empty( $atts['tagline']) ? $atts['tagline'] : '';
        $left_text          = !empty( $atts['left_text']) ? $atts['left_text'] : '';
        $left_tagline       = !empty( $atts['left_tagline']) ? $atts['left_tagline'] : '';
        $welcome_text       = !empty( $atts['welcome_text']) ? $atts['welcome_text'] : '';
		$login_url          = !empty( $tuturn_settings['tpl_login'] ) ? get_permalink($tuturn_settings['tpl_login']) : wp_login_url();
        $tpl_registration   = !empty( $tuturn_settings['tpl_registration'] ) ? get_permalink($tuturn_settings['tpl_registration']) : wp_login_url('/'); 

        if (!empty($_GET['key']) && ( isset($_GET['action']) && $_GET['action'] == 'reset_pwd' ) ) {
            $reset_key  = sanitize_text_field( $_GET['key'] );
            $user_data  = $wpdb->get_row($wpdb->prepare("SELECT ID, user_login, user_email FROM $wpdb->users WHERE user_activation_key = %s", $reset_key));
            if (!empty($user_data)) {
                
                $tagline            = esc_html__('Type your new password here','tuturn');
                $welcome_text       = esc_html__('You are nearly there','tuturn');
                ?>
                <div class="tu-main-login">
                    <div class="tu-login-left">
                        <?php if(!empty($logo)){?>
                            <strong>
                                <a href="<?php echo esc_url(home_url( '/' ));?>"><img src="<?php echo esc_url($logo)?>" alt="<?php esc_attr_e('Images','tuturn')?>"></a>
                            </strong>
                        <?php } 
                        if(!empty($bg_image)){?>
                            <figure>
                                <img src="<?php echo esc_url($bg_image)?>" alt="<?php esc_attr_e('Images','tuturn')?>">
                            </figure>
                        <?php } 
                        if(!empty($left_tagline) || !empty($left_text)){?>
                            <div class="tu-login-left_title">
                                <?php if(!empty($left_text)){?>
                                    <h2><?php echo esc_html($left_text)?></h2>
                                <?php } if(!empty($left_tagline)){?>
                                    <span><?php echo esc_html($left_tagline)?></span>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="tu-login-right">
                        <?php if(!empty($welcome_text) || !empty($tagline)){?>
                            <div class="tu-login-right_title">
                                <?php if(!empty($welcome_text)){?>
                                    <h2><?php echo esc_html($welcome_text)?></h2>
                                <?php }
                                if(!empty($tagline)){?>
                                    <h3><?php echo esc_html($tagline)?></h3>
                                <?php } ?>
                            </div>
                        <?php } ?>
                        <form class="tu-themeform tu-login-form tu-password-reset-form">
                            <fieldset>
                                <div class="tu-themeform__wrap">
                                    <div class="form-group-wrap">
                                        <div class="form-group">
                                            <div class="tu-placeholderholder">
                                                <input type="password" name="password" class="form-control" required="required" placeholder="<?php esc_attr_e('Type your new password','tuturn');?>">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <a href="javascript:void(0);" class="tu-primbtn-lg tu-password-recover"><span><?php esc_html_e('Reset now','tuturn')?></span><i class="icon icon-arrow-right"></i></a>
                                        </div>
                                        <?php if(!empty($login_url) || !empty($tpl_registration)) {?>
                                            <div class="tu-lost-password form-group">
                                                <?php if(!empty($tpl_registration)){?>
                                                    <a href="<?php echo esc_url($tpl_registration)?>"><?php esc_html_e('Join us today','tuturn')?> </a>
                                                <?php } 
                                                if(!empty($login_url)){?>
                                                    <a href="<?php echo esc_url($login_url)?>"><?php esc_html_e('Sign in','tuturn')?></a>
                                                <?php } ?>
                                            </div>
                                        <?php }?>
                                    </div>
                                </div>
                            </fieldset>
                            <input type="hidden" name="key" value="<?php echo esc_attr($reset_key);?>">

                        </form>
                    </div>
                </div>  
            <?php }
        }else{ ?>
            <div class="tu-main-login">
                <div class="tu-login-left">
                    <?php if(!empty($logo)){?>
                        <strong>
                            <a href="<?php echo esc_url(home_url( '/' ));?>"><img src="<?php echo esc_url($logo)?>" alt="<?php esc_attr_e('Images','tuturn')?>"></a>
                         </strong>
                    <?php } 
                    if(!empty($bg_image)){?>
                        <figure>
                            <img src="<?php echo esc_url($bg_image)?>" alt="<?php esc_attr_e('Images','tuturn')?>">
                        </figure>
                    <?php } 
                    if(!empty($left_tagline) || !empty($left_text)){?>
                        <div class="tu-login-left_title">
                            <?php if(!empty($left_text)){?>
                                <h2><?php echo esc_html($left_text)?></h2>
                            <?php } if(!empty($left_tagline)){?>
                                <span><?php echo esc_html($left_tagline)?></span>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
                <div class="tu-login-right">
                    <?php if(!empty($welcome_text) || !empty($tagline)){?>
                        <div class="tu-login-right_title">
                            <?php if(!empty($welcome_text)){?>
                                <h2><?php echo esc_html($welcome_text)?></h2>
                            <?php }
                            if(!empty($tagline)){?>
                                <h3><?php echo esc_html($tagline)?></h3>
                            <?php } ?>
                        </div>
                    <?php } ?>
                    <form class="tu-themeform tu-login-form tu-forgot-password">
                        <fieldset>
                            <div class="tu-themeform__wrap">
                                <div class="form-group-wrap">
                                    <div class="form-group">
                                        <div class="tu-placeholderholder">
                                            <input type="email" name="email" class="form-control" required="required" placeholder="<?php esc_html_e('Username/email','tuturn')?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <a href="javascript:void(0);" class="tu-primbtn-lg tu-password-forgot"><span><?php esc_html_e('Send reset link','tuturn')?></span><i class="icon icon-arrow-right"></i></a>
                                    </div>
                                    <?php if(!empty($login_url) || !empty($tpl_registration)) {?>
                                        <div class="tu-lost-password form-group">
                                            <?php if(!empty($tpl_registration)){?>
                                                <a href="<?php echo esc_url($tpl_registration)?>"><?php esc_html_e('Join us today','tuturn')?> </a>
                                            <?php } 
                                            if(!empty($login_url)){?>
                                                <a href="<?php echo esc_url($login_url)?>"><?php esc_html_e('Sign in','tuturn')?></a>
                                            <?php } ?>
                                        </div>
                                    <?php }?>
                                </div>
                            </div>
                        </fieldset>
                    </form>
                    <a class="tuturn-auth-back" href="<?php echo esc_url(home_url('/')); ?>"><i class="icon icon-arrow-left"></i> <?php esc_html_e('Go back', 'tuturn');?></a>
                </div>
            </div>
        <?php
        }
    }
}
