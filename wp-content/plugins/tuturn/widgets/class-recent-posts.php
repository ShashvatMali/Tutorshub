<?php
/**
 * Recent posts widget 
 *
 * @since      1.0.0
 *
 * @package    Tuturn
 * @subpackage Tuturn/widgets
 */

if (!defined('ABSPATH')) {
    die('Direct access forbidden.');
}

if (!class_exists('Tuturn_Recent_Posts')) {

    class Tuturn_Recent_Posts extends WP_Widget {

        /**
         * Register widget with WordPress.
         */
        function __construct() {

            parent::__construct(
                'tuturn_recent_posts' , // Base ID
                esc_html__('Recent post | Tuturn' , 'tuturn') , // Name
                array (
                	'classname' 	=> '',
					'description' 	=> esc_html__('Blog info' , 'tuturn') ,
				) // Args
            );
        }

        /**
         * Outputs the content of the widget
         *
         * @param array $args
         * @param array $instance
        */
        public function widget( $args, $instance) {
            extract($instance);
            $title          = !empty($instance['title']) ? ($instance['title']) : '';
            $no_posts       = !empty($instance['no_posts']) ? ($instance['no_posts']) : 5;
            $before		    = ($args['before_widget']);
			$after	 	    = ($args['after_widget']);
            echo do_shortcode($before);
            
            $args   = array(
                'numberposts'   => $no_posts,
            );

            $recent_posts   = wp_get_recent_posts($args);
            ?>
            <div class="tu-recentposts">
                <?php if(!empty($title)){?>
                    <h5><?php echo esc_html($title)?></h5>
                <?php } ?>
                <ul class="tu-recentposts_list">
                    <?php 
                    foreach( $recent_posts as $recent ) {
                        $post_ID            = $recent['ID'];
                        $post_thumbnail_id  = get_post_thumbnail_id( $post_ID );
                        $thumbnail_url      = wp_get_attachment_image_url( $post_thumbnail_id, 'thumbnail' );
                        $post_title         = get_the_title($post_ID);
                        $post_date          = date_i18n(get_option('date_format'), strtotime(get_the_date())) ;
                        ?>
                        <li>  
                            <div class="tu-recentposts_info">
                                <?php if(!empty($thumbnail_url)){?>
                                    <figure>
                                        <img src="<?php echo esc_url($thumbnail_url);?>" alt="<?php echo esc_attr($post_title);?>">
                                    </figure>
                                <?php } ?>
                                <?php if(!empty($post_title) || !empty($post_date)){?>
                                    <div class="tu-recentposts_title">
                                        <?php if(!empty($post_title)){?>
                                            <a href="<?php echo esc_url( get_permalink($post_ID)); ?>"><h6><?php echo esc_html($post_title)?></h6></a>
                                        <?php } ?>
                                        <?php  if(!empty($post_date)){?>
                                            <time datetime="<?php echo get_the_date('c'); ?>" itemprop="datePublished"><?php echo esc_html($post_date); ?></time>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
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
            $title      = !empty($instance['title']) ? ($instance['title']) : esc_html__('Recent posts', 'tuturn');
            $no_posts   = !empty($instance['no_posts']) ? ($instance['no_posts']) : '5';
            ?>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id('title') ); ?>"><?php esc_html_e('Title','tuturn'); ?></label>
                <input class="widefat"  name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" type="text" value="<?php echo esc_attr($title); ?>">
            </p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id('no_posts') ); ?>"><?php esc_html_e('No of posts','tuturn'); ?></label>
                <input class="widefat"  name="<?php echo esc_attr( $this->get_field_name('no_posts') ); ?>" type="text" value="<?php echo intval($no_posts); ?>">
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
            $instance               = $old_instance;
            $instance['title']	    = (!empty($new_instance['title']) ) ? sanitize_text_field($new_instance['title']) : '';
            $instance['no_posts']	= (!empty($new_instance['no_posts']) ) ? sanitize_text_field($new_instance['no_posts']) : '';
            return $instance;
        }
    }
}

//register widget
function tuturn_recent_post_widgets() {
	register_widget( 'Tuturn_Recent_Posts' );
}
add_action( 'widgets_init', 'tuturn_recent_post_widgets' );
