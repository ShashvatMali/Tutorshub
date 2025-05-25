<?php
/**
 *
 * The template used for registration
 *
 * @package     tuturn
 * @subpackage  tuturn/templates
 * @author      Amentotech <info@amentotech.com>
 * @link       https://themeforest.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
*/
if(!function_exists('tuturn_registration')){
	add_shortcode('tuturn_registration', 'tuturn_registration');
	function tuturn_registration($atts){
		global $tuturn_settings;
        $bg_image      = !empty( $atts['background']) ? $atts['background'] : '';
        $logo          = !empty( $atts['logo']) ? $atts['logo'] : '';
        $tagline       = !empty( $atts['tagline']) ? $atts['tagline'] : '';
        $left_text     = !empty( $atts['left_text']) ? $atts['left_text'] : '';
        $left_tagline  = !empty( $atts['left_tagline']) ? $atts['left_tagline'] : '';
        $welcome_text  = !empty( $atts['welcome_text']) ? $atts['welcome_text'] : '';
        $terms_page    = !empty( $atts['terms_page']) ? $atts['terms_page'] : '';
        $user_name_option     = !empty( $tuturn_settings['user_name_option'] ) ? ($tuturn_settings['user_name_option']) : '';
        $user_phone_option     = !empty( $tuturn_settings['user_phone_option'] ) ? ($tuturn_settings['user_phone_option']) : '';
        $tpl_reset     = !empty( $tuturn_settings['tpl_reset'] ) ? get_permalink($tuturn_settings['tpl_reset']) : home_url('/');
		$login_url     = !empty( $tuturn_settings['tpl_login'] ) ? get_permalink($tuturn_settings['tpl_login']) : wp_login_url();?>
        
        <div class="tu-main-login">
            <div class="tu-login-left">
                <?php if(!empty($logo)){?>
                    <strong>
                        <a href="<?php echo esc_url(home_url( '/' ));?>"><img src="<?php echo esc_url($logo)?>" alt="<?php esc_attr_e('Registration','tuturn')?>"></a>
                    </strong>
                <?php }
                if(!empty($bg_image)){?>
                    <figure>
                        <img src="<?php echo esc_url($bg_image)?>" alt="<?php esc_attr_e('Registration','tuturn')?>">
                    </figure>
                <?php } 
                if(!empty($left_text) || !empty($left_tagline)){?>
                    <div class="tu-login-left_title">
                        <?php if(!empty($left_text)){?>
                            <h2><?php echo esc_html($left_text)?></h2>
                        <?php } 
                        if(!empty($left_tagline)){?>
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
				<form class="tu-themeform tu-login-form tu-signup-form">
					<fieldset>
						<div class="tu-themeform__wrap">
							<div class="form-group-wrap">
								<div class="form-group">
									<div class="tu-placeholderholder">
										<input type="text" name="registration[fname]" class="form-control" required="required" placeholder="<?php esc_html_e('First name','tuturn')?>">
									</div>
								</div>
								<div class="form-group">
									<div class="tu-placeholderholder">
										<input type="text" name="registration[lname]" required="required" class="form-control" placeholder="<?php esc_html_e('Last name','tuturn')?>">
									</div>
								</div>
                                <?php if(!empty($user_name_option)){?>
                                    <div class="form-group">
                                        <div class="tu-placeholderholder">
                                            <input type="text" name="registration[username]" required="required" class="form-control" placeholder="<?php esc_html_e('Username','tuturn')?>">
                                        </div>
                                    </div>
                                <?php }?>
                                <?php if(!empty($user_phone_option)){?>
                                    <div class="form-group">
                                        <div class="tu-placeholderholder">
                                            <input type="text" name="registration[phone_number]" required="required" class="form-control" placeholder="<?php esc_html_e('Phone number','tuturn')?>">
                                        </div>
                                    </div>
                                <?php }?>
								<div class="form-group">
									<div class="tu-placeholderholder">
										<input type="email" name="registration[email]" class="form-control" required="required" placeholder="<?php esc_html_e('Enter your email address','tuturn')?>">
									</div>
								</div>
								<div class="form-group">
									<div class="tu-placeholderholder">
                                        <div class="input-group tu-input-group form-control">
                                            <input type="password" name="registration[password]" id ="tu-passwordinput" required="required" placeholder="<?php esc_html_e('Enter password','tuturn')?>">
                                            <span class="tu-showpassword"><i class="icon icon-eye-off"></i></span>
                                        </div>
									</div>
								</div>
                                <div class="form-group tu-form-groupradio registration-user-type">
                                    <div class="tu-check tu-radiosm">
                                        <input id="user_type_instructor" type="radio" name="registration[user_type]" value="instructor">
                                        <label for="user_type_instructor"><?php esc_html_e('Instructor', 'tuturn'); ?></label>
                                    </div>
                                    <div class="tu-check tu-radiosm">
                                        <input id="user_type_student" type="radio" name="registration[user_type]" value="student">
                                        <label for="user_type_student"><?php esc_html_e('Student', 'tuturn'); ?></label>
                                    </div>
                                </div>
                                <div class="form-group">
									<div class="tu-check tu-signup-check">
                                        <input type="hidden" name="registration[terms]" value="">
                                        <input type="checkbox" name="registration[terms]" id="tu-terms" name="tu-terms">
                                        <label for="tu-terms"><span><?php esc_html_e('I have read and agree to all','tuturn')?></span><a href="<?php echo esc_url($terms_page)?>"><?php echo esc_html('Terms &amp; conditions','tuturn')?> </a></label>
                                    </div>
								</div>
								<div class="form-group">
									<a href="javascript:void(0);" class="tu-primbtn-lg tu-submit-registration"><span><?php esc_html_e('Join now','tuturn')?> </span><i class="icon icon-arrow-right"></i></a>
								</div>
                                <?php if(!empty($tuturn_settings['enable_social_connect'])){ ?>
                                    <div class="form-group">
                                        <div class="tu-optioanl-or">
                                            <span><?php esc_html_e('OR','tuturn')?></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div id="google_signin"></div>
                                    </div>
                                <?php } ?>
                                <div class="tu-lost-password form-group">
                                    <a href="<?php echo esc_url($login_url)?>"><?php esc_html_e('Sign in','tuturn')?></a>
                                    <a href="<?php echo esc_url($tpl_reset)?>" class="tu-password-clr_light"><?php esc_html_e('Lost password?','tuturn')?></a>
                                </div>
 							</div>
						</div>
					</fieldset>
				</form>
                <a class="tuturn-auth-back" href="<?php echo esc_url(home_url('/')); ?>"><i class="icon icon-arrow-left"></i><?php esc_html_e('Go back', 'tuturn');?></a>
            </div>
       </div>
		<?php
        tuturn_get_template_part('user-type', 'js-templates');
	}
}
