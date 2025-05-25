<?php
/**
 * Mailchimp newsLetters widget.
 *
 * @since      1.0.0
 *
 * @package    Tuturn
 * @subpackage Tuturn/widgets
 */
 
if (!defined('ABSPATH')) {
    die('Direct access forbidden.');
}

if (!class_exists('Tuturn_NewsLetters')) {
    class Tuturn_NewsLetters extends WP_Widget {
        /**
         * Register widget with WordPress.
         */
        function __construct() {
            parent::__construct(
                    'tuturn_newsletters' , // Base ID
                    esc_html__('News Letters | Tuturn' , 'tuturn') , // Name
                array (
                	'classname' 	=> '',
					'description' 	=> esc_html__('News Letters' , 'tuturn') , 
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
			$title		    = (!empty($instance['title']) ) ? esc_html($instance['title']) : '';
            $details 	    = (!empty($instance['details']) ) ? esc_html($instance['details']) : '';			
            $button_label 	= (!empty($instance['button_label']) ) ? esc_html($instance['button_label']) : '';			
            $before			= ($args['before_widget']);
			$after	 		= ($args['after_widget']);
			$mailchimp 	    = new Tuturn_MailChimp();                       
			echo do_shortcode($before);
			$mailchimp->tuturn_mailchimp_form($title,$details,$button_label);
			echo do_shortcode( $after );
        }

        /**
         * Outputs the options form on admin
         *
         * @param array $instance The widget options
         */
        public function form($instance) {
            // outputs the options form on admin
			$title		    = (!empty($instance['title']) ) ? esc_html($instance['title']) : '';
            $details 	    = (!empty($instance['details']) ) ? esc_html($instance['details']) : '';		
            $button_label 	= (!empty($instance['button_label']) ) ? esc_html($instance['button_label']) : '';		
            ?>
			<p>
                <label for="<?php echo ( $this->get_field_id('title') ); ?>"><?php esc_html_e('Title','tuturn'); ?></label> 
                <input class="widefat" id="<?php echo ( $this->get_field_id('title') ); ?>" name="<?php echo ( $this->get_field_name('title') ); ?>" type="text" value="<?php echo esc_attr($title); ?>">
            </p>
            <p>
                <label for="<?php echo ( $this->get_field_id('details') ); ?>"><?php esc_html_e('Details','tuturn'); ?></label> 
                <textarea id="details" name="<?php echo esc_attr($this->get_field_name('details')); ?>" class="widefat"><?php echo esc_html($details); ?></textarea>
            </p>
            <p>
                <label for="<?php echo ( $this->get_field_id('button_label') ); ?>"><?php esc_html_e('Button label','tuturn'); ?></label> 
                <input class="widefat" id="<?php echo ( $this->get_field_id('button_label') ); ?>" name="<?php echo ( $this->get_field_name('button_label') ); ?>" type="text" value="<?php echo esc_attr($button_label); ?>">
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
			$instance['title']		    = (!empty($new_instance['title']) ) ? sanitize_text_field($new_instance['title']) : '';
            $instance['details'] 	    = (!empty($new_instance['details']) ) ? sanitize_textarea_field($new_instance['details']) : '';
            $instance['button_label'] 	= (!empty($new_instance['button_label']) ) ? sanitize_text_field($new_instance['button_label']) : '';

            return $instance;
        }
    }
}

//register widget
function tuturn_register_NewsLetters_widgets() {
	register_widget( 'Tuturn_NewsLetters' );
}
add_action( 'widgets_init', 'tuturn_register_NewsLetters_widgets' );