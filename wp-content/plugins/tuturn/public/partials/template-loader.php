<?php
/**
 * Template loader
 *
 * @link      https://themeforest.net/user/amentotech/portfolio
 * @since      1.0.0
 *
 * @package    tuturn_
 * @subpackage tuturn_/public
 */
class Tuturn_PageTemplaterLoader {

    private static $instance;
    protected $templates;

    //get class instance
    public static function get_instance() {

        if ( null == self::$instance ) {
            self::$instance = new Tuturn_PageTemplaterLoader();
        }

        return self::$instance;
    }

    //Constructor
    private function __construct() {
        $this->templates = array();

        if ( version_compare( floatval( get_bloginfo( 'version' ) ), '4.7', '<' ) ) {
            add_filter('page_attributes_dropdown_pages_args',array( $this, 'register_custom_templates' ));
        } else {
            add_filter('theme_page_templates', array( $this, 'tuturn_add_new_template' ));
        }

        add_filter('wp_insert_post_data', array( $this, 'tuturn_register_custom_templates' ) );
        add_filter('template_include', array( $this, 'tuturn_view_custom_templates'), 99 );

        $this->templates = array(
            'templates/admin-dashboard.php'         => esc_html__('Administrator dashboard','tuturn'),
            'templates/search-instructors.php'      => esc_html__('Search instructors','tuturn'),
            'templates/search-students.php'         => esc_html__('Search students','tuturn'),
            'templates/packages.php'                => esc_html__('Packages','tuturn'),
            'templates/blogs.php'                   => esc_html__('Blog Template','tuturn'),
            'templates/inbox.php'                   => esc_html__('Inbox','tuturn'),
            'templates/profile-settings.php'        => esc_html__('User Dashboard','tuturn'),
            'templates/tuturn-checkout.php'         => esc_html__('Checkout','tuturn'),
        );
    }

    //Add new templates
    public function tuturn_add_new_template( $posts_templates ) {
        $posts_templates = array_merge( $posts_templates, $this->templates );
        return $posts_templates;
    }

    //Register Templates
    public function tuturn_register_custom_templates( $atts ) {
        $cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

        $templates = wp_get_theme()->get_page_templates();
        if ( empty( $templates ) ) {
            $templates = array();
        }

        wp_cache_delete( $cache_key , 'themes');
        $templates = array_merge( $templates, $this->templates );
        wp_cache_add( $cache_key, $templates, 'themes', 1800 );

        return $atts;

    }

    //Embed into dropdown
    public function tuturn_view_custom_templates( $template ) {
        global $post,$woocommerce,$product;
        if ( ! $post ) {return $template;}

        if (is_singular() && $post->post_type == 'tuturn-instructor') {
            $template = tuturn_locate_template( 'single-tuturn-instructor.php');

            if ( '' != $template ) {
                return $template ;
            }
        }

        if (is_singular() && $post->post_type == 'tuturn-student') {
            $template = tuturn_locate_template( 'single-tuturn-student.php');

            if ( '' != $template ) {
                return $template ;
            }
        }

        if ( ! isset( $this->templates[get_post_meta( $post->ID, '_wp_page_template', true )] ) ) {
            return $template;
        }

        $file = TUTURN_DIRECTORY . get_post_meta($post->ID, '_wp_page_template', true);

        if ( file_exists( $file ) ) {
            return $file;
        } else {
            return $file;
        }
        
        return $template;
    }
}
add_action( 'plugins_loaded', array( 'Tuturn_PageTemplaterLoader', 'get_instance' ) );
