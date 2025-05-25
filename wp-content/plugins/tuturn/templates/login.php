<?php
/**
 *
 * The template used for login
 *
 * @package     tuturn
 * @subpackage  tuturn/templates
 * @author      Amentotech <info@amentotech.com>
 * @link       https://themeforest.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
*/
if(!function_exists('tuturn_signin')){
	add_shortcode('tuturn_signin', 'tuturn_signin');
	function tuturn_signin($atts){     
		global $tuturn_settings;		
        $bg_image        = !empty( $atts['background']) ? $atts['background'] : '';
        $logo            = !empty( $atts['logo']) ? $atts['logo'] : '';
        $tagline         = !empty( $atts['tagline']) ? $atts['tagline'] : '';
        $left_text       = !empty( $atts['left_text']) ? $atts['left_text'] : '';
        $left_tagline    = !empty( $atts['left_tagline']) ? $atts['left_tagline'] : '';
        $welcome_text    = !empty( $atts['welcome_text']) ? $atts['welcome_text'] : '';
        $tpl_reset          = !empty( $tuturn_settings['tpl_reset'] ) ? get_permalink($tuturn_settings['tpl_reset']) : home_url('/');
        $tpl_registration   = !empty( $tuturn_settings['tpl_registration'] ) ? get_permalink($tuturn_settings['tpl_registration']) : wp_login_url('/');?>
        
        <div class="tu-main-login">
            <div class="tu-login-left">
                <?php if(!empty($logo)){?>
                    <strong>
                        <a href="<?php echo esc_url(home_url( '/' ));?>"><img src="<?php echo esc_url($logo)?>" alt="<?php esc_attr_e('images','tuturn')?>"></a>
                    </strong>
                <?php }
                if(!empty($bg_image)){?>
                    <figure>
                        <img src="<?php echo esc_url($bg_image)?>" alt="<?php esc_attr_e('images','tuturn')?> ">
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
                <?php if(!empty($tagline) || !empty($welcome_text)){?>
                    <div class="tu-login-right_title">
                        <?php if(!empty($welcome_text)){?>
                            <h2><?php  echo esc_html($welcome_text)?></h2>
                        <?php } if(!empty($tagline)) {?>
                            <h3><?php echo esc_html($tagline)?></h3>
                        <?php } ?>
                    </div>
                <?php } ?>
				<form class="tu-themeform tu-login-form">
					<fieldset>
						<div class="tu-themeform__wrap">
							<div class="form-group-wrap">
								<div class="form-group">
									<div class="tu-placeholderholder">
										<input type="email" name="login[email]" class="form-control" required="required"  placeholder="<?php esc_html_e('Username/email','tuturn')?>">
									</div>
								</div>
								<div class="form-group">
									<div class="tu-placeholderholder">
                                        <div class="input-group tu-input-group form-control">
                                            <input type="password" name="login[password]" id ="tu-passwordinput" required="required" placeholder="<?php esc_html_e('Your password','tuturn')?>">
                                            <span class="tu-showpassword"><i class="icon icon-eye-off"></i></span>
                                        </div>
									</div>
								</div>
								<div class="form-group">
									<a href="javascript:void(0);" class="tu-primbtn-lg tu-user-login"><span><?php esc_html_e('Submit','tuturn')?> </span><i class="icon icon-arrow-right"></i></a>
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
								<?php if(!empty($tpl_registration) || !empty($tpl_reset)){?>
                                    <div class="tu-lost-password form-group">
                                        <?php if(!empty($tpl_registration)){?>
                                            <a href="<?php echo esc_url($tpl_registration)?>"><?php esc_html_e('Join us today','tuturn')?> </a>
                                        <?php } if(!empty($tpl_reset)){?>
                                            <a href="<?php echo esc_url($tpl_reset)?>" class="tu-password-clr_light"><?php esc_html_e('Lost password?','tuturn')?> </a>
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
        tuturn_get_template_part('user-type', 'js-templates');
	}
}
