<?php
/**
 * About us Footer widget 
 *
 * @since      1.0.0
 *
 * @package    Tuturn
 * @subpackage Tuturn/admin
 */

if (!defined('ABSPATH')) {
    die('Direct access forbidden.');
}

if (!class_exists('Tuturn_Apps')) {

    class Tuturn_Apps extends WP_Widget {

        /**
         * Register widget with WordPress.
         */
        function __construct() {

            parent::__construct(
                'tuturn_apps' , // Base ID
                esc_html__('Get social contact | Tuturn' , 'tuturn') , // Name
                array (
                	'classname' 	=> 'tu-footerapp',
					'description' 	=> esc_html__('Tuturn info' , 'tuturn') ,
				) // Args
            );
        }

        /**
         * Outputs the content of the widget
         *
         * @param array $args
         * @param array $instance
         */
        public function widget($args , $instance) {
            // outputs the content of the widget
			global $post;

            extract($instance);

			$footer_logo 		    = (!empty($instance['footer_logo']) ) ? ($instance['footer_logo']) : '';
            $footer_content 	    = !empty($instance['footer_content']) ? ($instance['footer_content']) : '';
            $contact_timming 	    = !empty($instance['contact_timming']) ? ($instance['contact_timming']) : '';
            $footer_cotact_content 	= !empty($instance['footer_cotact_content']) ? ($instance['footer_cotact_content']) : '';

            $faccebook_link 	= !empty($instance['faccebook_link']) ? ($instance['faccebook_link']) : '';
            $twitter_link 	    = !empty($instance['twitter_link']) ? ($instance['twitter_link']) : '';
            $instagram_link 	    = !empty($instance['instagram_link']) ? ($instance['instagram_link']) : '';
            $linkedin_link 	    = !empty($instance['linkedin_link']) ? ($instance['linkedin_link']) : '';
            $dribbble_link 	    = !empty($instance['dribbble_link']) ? ($instance['dribbble_link']) : '';
            $twitch_link 	    = !empty($instance['twitch_link']) ? ($instance['twitch_link']) : '';

            $call_label 	    = !empty($instance['call_label']) ? ($instance['call_label']) : '';
            $email_label 	    = !empty($instance['email_label']) ? ($instance['email_label']) : '';
            $fax_label 	        = !empty($instance['fax_label']) ? ($instance['fax_label']) : '';
            $whatsapp_label 	= !empty($instance['whatsapp_label']) ? ($instance['whatsapp_label']) : '';

            $before		= ($args['before_widget']);
			$after	 	= ($args['after_widget']);

            echo do_shortcode($before);?>
             
            <div class="row gy-4">
                <div class="col-lg-7">
                    <?php if(!empty($footer_logo)){?>
                        <strong class="tu-footerlogo">
                            <a href="<?php echo esc_url($footer_logo)?>"><img src="<?php echo esc_url($footer_logo)?>" alt="<?php esc_attr_e('Logo','tuturn')?>"></a>
                        </strong>
                    <?php } 
                    if(!empty($footer_content)){?>
                        <p class="tu-footerdescription"><?php echo esc_html($footer_content)?></p>
                    <?php   }
                    if(!empty($faccebook_link) || !empty($twitter_link) || !empty($linkedin_link) || !empty($dribbble_link) || !empty($twitch_link)){ ?>
                        <ul class="tu-socialmedia">
                            <?php if(!empty($faccebook_link)){?>
                                <li class="tu-facebookv3"><a href="<?php echo esc_url($faccebook_link)?>"><i class="fab fa-facebook-f"></i></a></li>
                            <?php }
                            if(!empty($twitter_link)) {?>
                                <li class="tu-twitterv3"><a href="<?php echo esc_url($twitter_link)?>"><i class="fab fa-twitter"></i></a></li>
                            <?php } 
                            if(!empty($linkedin_link)) {?>
                                <li class="tu-linkedinv3"><a href="<?php echo esc_url($linkedin_link)?>"><i class="fab fa-linkedin-in"></i></a></li>
                            <?php } 
                            if(!empty($dribbble_link)) {?>
                                <li class="tu-dribbblev3"><a href="<?php echo esc_url($dribbble_link)?>"><i class="fab fa-dribbble"></i></a></li>
                            <?php } 
                            if(!empty($twitch_link)){?>
                                <li class="tu-twitchv3"><a href="<?php echo esc_url($twitch_link)?>"><i class="fab fa-twitch"></i></a></li>
                            <?php }
                            if(!empty($instagram_link)){?>
                                <li class="tu-twitchv3 tu-instagramv3"><a href="<?php echo esc_url($instagram_link)?>"><i class="fab fa-instagram"></i></a></li>
                            <?php } ?>
                        </ul>
                    <?php } ?>
                </div>
                <div class="col-lg-5">
                    <?php if(!empty($footer_cotact_content)){?>
                        <h5 class="tu-footertitle"><?php echo esc_html($footer_cotact_content)?></h5>
                    <?php }
                    if(!empty($call_label) || !empty($email_label) || !empty($fax_label) || !empty($whatsapp_label)) {?>
                        <ul class="tu-footerlist">
                            <?php if(!empty($call_label)){?>
                                <li><a href="javascript:void(0);"><i class="icon icon-phone-call"></i><em><?php echo esc_html($call_label)?></em><span>( <?php echo esc_html($contact_timming)?> )</span></a></li>
                            <?php }
                            if(!empty($email_label)) {?>
                                <li><a href="<?php echo esc_url($email_label)?>;"><i class="icon icon-mail"></i><em><?php echo esc_html($email_label)?></em></a></li>
                            <?php } 
                            if(!empty($fax_label)){?>
                                <li><a href="javascript:void(0);"><i class="icon icon-printer"></i><em><?php echo esc_html($fax_label)?></em></a></li>
                            <?php } 
                            if(!empty($whatsapp_label)){?>
                                <li><a href="javascript:void(0);"><i class="fab fa-whatsapp"></i><em><?php echo esc_html($whatsapp_label)?></em><span>( <?php echo esc_html($contact_timming)?> )</span></a></li>
                            <?php } ?>
                        </ul>
                    <?php } ?>
                </div>
            </div>
			<?php
			echo do_shortcode( $after );
        }

        /**
         * Outputs the options form on admin
         *
         * @param array $instance The widget options
         */
        public function form($instance) {
            // outputs the options form on admin
            $footer_logo 		    = (!empty($instance['footer_logo']) ) ? ($instance['footer_logo']) : '';
            $footer_content 	    = !empty($instance['footer_content']) ? ($instance['footer_content']) : '';
            $contact_timming 	    = !empty($instance['contact_timming']) ? ($instance['contact_timming']) : '';
            $footer_cotact_content 	= !empty($instance['footer_cotact_content']) ? ($instance['footer_cotact_content']) : '';

            $faccebook_link 	= !empty($instance['faccebook_link']) ? ($instance['faccebook_link']) : '';
            $twitter_link 	    = !empty($instance['twitter_link']) ? ($instance['twitter_link']) : '';
            $linkedin_link 	    = !empty($instance['linkedin_link']) ? ($instance['linkedin_link']) : '';
            $dribbble_link 	    = !empty($instance['dribbble_link']) ? ($instance['dribbble_link']) : '';
            $twitch_link 	    = !empty($instance['twitch_link']) ? ($instance['twitch_link']) : '';
            $instagram_link 	    = !empty($instance['instagram_link']) ? ($instance['instagram_link']) : '';

            $call_label 	    = !empty($instance['call_label']) ? ($instance['call_label']) : '';
            $email_label 	    = !empty($instance['email_label']) ? ($instance['email_label']) : '';
            $fax_label 	        = !empty($instance['fax_label']) ? ($instance['fax_label']) : '';
            $whatsapp_label 	= !empty($instance['whatsapp_label']) ? ($instance['whatsapp_label']) : '';
            ?>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id('footer_logo') ); ?>"><?php esc_html_e('Upload footer logo','tuturn'); ?></label>
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id('footer_logo') );?>" name="<?php echo esc_attr( $this->get_field_name('footer_logo') );?>" type="text" value="<?php echo esc_url($footer_logo);?>">
                <span id="upload" class="button upload_button_wgt"><?php esc_html_e( 'Footer logo', 'tuturn' ); ?><?php esc_html_e( 'Upload', 'tuturn' ); ?></span>
            </p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id('footer_content') ); ?>"><?php esc_html_e('Footer content','tuturn'); ?></label>
                <textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id('footer_content') ); ?>" name="<?php echo esc_attr( $this->get_field_name('footer_content') ); ?>"><?php echo esc_html($footer_content); ?></textarea>
            </p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id('faccebook_link') ); ?>"><?php esc_html_e('Facebook link','tuturn'); ?></label>
                <input class="widefat"  name="<?php echo esc_attr( $this->get_field_name('faccebook_link') ); ?>" type="text" value="<?php echo esc_url($faccebook_link); ?>">
            </p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id('twitter_link') ); ?>"><?php esc_html_e('Twitter link','tuturn'); ?></label>
                <input class="widefat"  name="<?php echo esc_attr( $this->get_field_name('twitter_link') ); ?>" type="text" value="<?php echo esc_url($twitter_link); ?>">
            </p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id('linkedin_link') ); ?>"><?php esc_html_e('Linkedin link','tuturn'); ?></label>
                <input class="widefat"  name="<?php echo esc_attr( $this->get_field_name('linkedin_link') ); ?>" type="text" value="<?php echo esc_url($linkedin_link); ?>">
            </p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id('dribbble_link') ); ?>"><?php esc_html_e('Dribble link','tuturn'); ?></label>
                <input class="widefat"  name="<?php echo esc_attr( $this->get_field_name('dribbble_link') ); ?>" type="text" value="<?php echo esc_url($dribbble_link); ?>">
            </p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id('twitch_link') ); ?>"><?php esc_html_e('Twitch link','tuturn'); ?></label>
                <input class="widefat"  name="<?php echo esc_attr( $this->get_field_name('twitch_link') ); ?>" type="text" value="<?php echo esc_url($twitch_link); ?>">
            </p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id('instagram_link') ); ?>"><?php esc_html_e('Instagram link','tuturn'); ?></label>
                <input class="widefat"  name="<?php echo esc_attr( $this->get_field_name('instagram_link') ); ?>" type="text" value="<?php echo esc_url($instagram_link); ?>">
            </p>

            <p>
                <label for="<?php echo esc_attr( $this->get_field_id('footer_cotact_content') ); ?>"><?php esc_html_e('Footer contact text','tuturn'); ?></label>
                <textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id('footer_cotact_content') ); ?>" name="<?php echo esc_attr( $this->get_field_name('footer_cotact_content') ); ?>"><?php echo esc_html($footer_cotact_content); ?></textarea>
            </p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id('contact_timming') ); ?>"><?php esc_html_e('Contact timming','tuturn'); ?></label>
                <textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id('contact_timming') ); ?>" name="<?php echo esc_attr( $this->get_field_name('contact_timming') ); ?>"><?php echo esc_html($contact_timming); ?></textarea>
            </p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id('call_label') ); ?>"><?php esc_html_e('Call label','tuturn'); ?></label>
                <input class="widefat"  name="<?php echo esc_attr( $this->get_field_name('call_label') ); ?>" type="text" value="<?php echo esc_attr($call_label); ?>">
            </p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id('email_label') ); ?>"><?php esc_html_e('Email label','tuturn'); ?></label>
                <input class="widefat"  name="<?php echo esc_attr( $this->get_field_name('email_label') ); ?>" type="text" value="<?php echo esc_attr($email_label); ?>">
            </p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id('fax_label') ); ?>"><?php esc_html_e('Fax label','tuturn'); ?></label>
                <input class="widefat"  name="<?php echo esc_attr( $this->get_field_name('fax_label') ); ?>" type="text" value="<?php echo esc_attr($fax_label); ?>">
            </p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id('whatsapp_label') ); ?>"><?php esc_html_e('Whatsapp label','tuturn'); ?></label>
                <input class="widefat"  name="<?php echo esc_attr( $this->get_field_name('whatsapp_label') ); ?>" type="text" value="<?php echo esc_attr($whatsapp_label); ?>">
            </p>
            <?php
        }

        /**
         * Processing widget options on save
         *
         * @param array $new_instance The new options
         * @param array $old_instance The previous options
         */
        public function update($new_instance , $old_instance) {
            // processes widget options to be saved
            $instance                   = $old_instance;
            $instance['footer_logo']	= (!empty($new_instance['footer_logo']) ) ? esc_url($new_instance['footer_logo']) : '';

            $instance['footer_content']	        = (!empty($new_instance['footer_content']) ) ? sanitize_textarea_field($new_instance['footer_content']) : '';
            $instance['contact_timming']	    = (!empty($new_instance['contact_timming']) ) ? sanitize_textarea_field($new_instance['contact_timming']) : '';
            $instance['footer_cotact_content']	= (!empty($new_instance['footer_cotact_content']) ) ? sanitize_textarea_field($new_instance['footer_cotact_content']) : '';

            $instance['faccebook_link']	= (!empty($new_instance['faccebook_link']) ) ? sanitize_text_field($new_instance['faccebook_link']) : '';
            $instance['twitter_link']	= (!empty($new_instance['twitter_link']) ) ? sanitize_text_field($new_instance['twitter_link']) : '';
            $instance['linkedin_link']	= (!empty($new_instance['linkedin_link']) ) ? sanitize_text_field($new_instance['linkedin_link']) : '';
            $instance['dribbble_link']	= (!empty($new_instance['dribbble_link']) ) ? sanitize_text_field($new_instance['dribbble_link']) : '';
            $instance['twitch_link']	= (!empty($new_instance['twitch_link']) ) ? sanitize_text_field($new_instance['twitch_link']) : '';
            $instance['instagram_link']	= (!empty($new_instance['instagram_link']) ) ? sanitize_text_field($new_instance['instagram_link']) : '';

            $instance['call_label']	    = (!empty($new_instance['call_label']) ) ? sanitize_textarea_field($new_instance['call_label']) : '';
            $instance['email_label']	= (!empty($new_instance['email_label']) ) ? sanitize_textarea_field($new_instance['email_label']) : '';
            $instance['fax_label']	    = (!empty($new_instance['fax_label']) ) ? sanitize_textarea_field($new_instance['fax_label']) : '';
            $instance['whatsapp_label'] = (!empty($new_instance['whatsapp_label']) ) ? sanitize_textarea_field($new_instance['whatsapp_label']) : '';

            return $instance;
        }
    }
}

//register widget
function tuturn_register_Apps_widgets() {
	register_widget( 'Tuturn_Apps' );
}

add_action( 'widgets_init', 'tuturn_register_Apps_widgets' );
