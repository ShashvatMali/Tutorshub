<?php
/**
 * Tuturn functions and definitions
 *
 * @link https://themeforest.net/user/amentotech/portfolio
 *
 * @package Tuturn
 */

if ( ! defined( 'THEME_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( 'THEME_VERSION', '1.0.0' );
}

require_once ( get_template_directory() . '/inc/tgmp/init.php'); //TGM init

if ( ! function_exists( 'tuturn_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function tuturn_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on tuturn, use a find and replace
		 * to change 'tuturn' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'tuturn', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus(
			array(
				'primary-menu' 	=> esc_html__( 'Primary menu', 'tuturn' ),
				'footer-menu' 	=> esc_html__( 'Footer menu', 'tuturn' ),
			)
		);

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption'
			)
		);

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		/*
		* Enable support for Post Formats.  
		* See http://codex.wordpress.org/Post_Formats     
		*/
		add_theme_support('post-formats' , array (
			''
		));
		
		// Add support for responsive embedded content.
		add_theme_support( 'responsive-embeds' );

		// Add support for Block Styles.
		add_theme_support( 'wp-block-styles' );

		// Add support for full and wide align images.
		add_theme_support( 'align-wide' );

		// Add support for editor styles.
		add_theme_support( 'editor-styles' );

		// Add custom editor font sizes.
		add_theme_support(
			'editor-font-sizes',
			array(
				array(
					'name'      => esc_html__( 'Small', 'tuturn' ),
					'size'      => 15,
					'slug'      => 'small',
				),
				array(
					'name'      => esc_html__( 'Normal', 'tuturn' ),
					'size'      => 16,
					'slug'      => 'normal',
				),
				array(
					'name'      => esc_html__( 'Large', 'tuturn' ),
					'size'      => 36,
					'slug'      => 'large',
				),
				array(
					'name'      => esc_html__( 'Extra Large', 'tuturn' ),
					'size'      => 48,
					'slug'      => 'extra-large',
				),
			)
		);
		
		//theme default color 
		add_theme_support( 
			'editor-color-palette', array(
			array(
				'name' => esc_html__( 'Theme color', 'tuturn' ),
				'slug' => 'strong-theme-color',
				'color' => '#6A307D',
			),
			array(
				'name' => esc_html__( 'Theme light text color', 'tuturn' ),
				'slug' => 'light-gray',
				'color' => '#999999',
			),
			array(
				'name' => esc_html__( 'Theme very light text color', 'tuturn' ),
				'slug' => 'very-light-gray',
				'color' => '#FCFCFC',
			),
			array(
				'name' => esc_html__( 'Theme Dark text color', 'tuturn' ),
				'slug' => 'very-dark-gray',
				'color' => '#1C1C1C',
			),
		) );

		// Add support for woocommerce
		add_theme_support( 'woocommerce' );
		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );
}
endif;
add_action( 'after_setup_theme', 'tuturn_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
if(!function_exists('tuturn_content_width')){
	function tuturn_content_width() {
		$GLOBALS['content_width'] = apply_filters( 'tuturn_content_width', 1296 );
	}
	add_action( 'after_setup_theme', 'tuturn_content_width', 0 );
}

/**
 * Tuturn paid theme.
 *
 * @global bolean $paid
 */
if(!function_exists('tuturn_paid_theme')){
	function tuturn_paid_theme($paid = 'free') {
		return 'paid';
	}
	add_filter( 'tuturn_paid_theme', 'tuturn_paid_theme', 10);
}


/**
 * Register widget area.
 *
 * @link https://themeforest.net/user/amentotech/portfolio
 */
if(!function_exists('tuturn_widgets_init')){
	function tuturn_widgets_init() {
		register_sidebar(
			array(
				'name'          => esc_html__( 'Default sidebar', 'tuturn' ),
				'id'            => 'tuturn-sidebar',
				'description'   => esc_html__( 'Default archive sidebar', 'tuturn' ),
				'before_widget' => '<div id="%1$s" class="widget %2$s tu-asideitem ">',
				'after_widget'  => '</div>',
				'before_title'  => '<h5>',
				'after_title'   => '</h5>',
			)
		);
		register_sidebar(
			array(
				'name'          => esc_html__( 'Single post sidebar', 'tuturn' ),
				'id'            => 'tuturn-single-sidebar',
				'description'   => esc_html__( 'Single post sidebar', 'tuturn' ),
				'before_widget' => '<div id="%1$s" class="widget %2$s tu-asideitem">',
				'after_widget'  => '</div>',
				'before_title'  => '<h5>',
				'after_title'   => '</h5>',
			)
		);
		register_sidebar(
			array(
				'name'          => esc_html__( 'Footer sidebar 1', 'tuturn' ),
				'id'            => 'tuturn-sidebar-f1',
				'description'   => esc_html__( 'For footer first section', 'tuturn' ),
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<div class="tu-sidetitle"><h5>',
				'after_title'   => '</h5></div>',
			)
		);
		register_sidebar(
			array(
				'name'          => esc_html__( 'Footer sidebar 2', 'tuturn' ),
				'id'            => 'tuturn-sidebar-f2',
				'description'   => esc_html__( 'For footer 2nd section', 'tuturn' ),
				'before_widget' => '<div id="%1$s" class="widget %2$s tu-footercontent">',
				'after_widget'  => '</div>',
				'before_title'  => '<div class="tu-sidetitle"><h5>',
				'after_title'   => '</h5></div>',
			)
		);
		register_sidebar(
			array(
				'name'          => esc_html__( 'Footer sidebar 3', 'tuturn' ),
				'id'            => 'tuturn-sidebar-fonlineclasses',
				'description'   => esc_html__( 'For footer 2nd section second column', 'tuturn' ),
				'before_widget' => '<div id="%1$s" class="widget %2$s tu-footercontent">',
				'after_widget'  => '</div>',
				'before_title'  => '<div class="tu-sidetitle"><h5>',
				'after_title'   => '</h5></div>',
			)
		);
		
		register_sidebar(
			array(
				'name'          => esc_html__( 'Footer sidebar 4', 'tuturn' ),
				'id'            => 'tuturn-sidebar-f3',
				'description'   => esc_html__( 'For footer 3rd section first column', 'tuturn' ),
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<div class="tu-sidetitle"><h5>',
				'after_title'   => '</h5></div>',
			)
		);		
		register_sidebar(
			array(
				'name'          => esc_html__( 'Footer sidebar 5', 'tuturn' ),
				'id'            => 'tuturn-sidebar-f4',
				'description'   => esc_html__( 'For footer 3rd section second column', 'tuturn' ),
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<div class="tu-sidetitle"><h5>',
				'after_title'   => '</h5></div>',
			)
		);		
		register_sidebar(
			array(
				'name'          => esc_html__( 'Footer sidebar 6', 'tuturn' ),
				'id'            => 'tuturn-sidebar-f5',
				'description'   => esc_html__( 'For 3rd section 3rd column', 'tuturn' ),
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<div class="tu-sidetitle"><h5>',
				'after_title'   => '</h5></div>',
			)
		);
	}
	add_action( 'widgets_init', 'tuturn_widgets_init' );
}

/**
 * Enqueue scripts and styles.
 */
if(!function_exists('tuturn_scripts')){
	function tuturn_scripts() {
		global $tuturn_settings;
		$loading_duration	= !empty($tuturn_settings['loading_duration']) ? $tuturn_settings['loading_duration'] : 500;
		$theme_version 		= wp_get_theme('tuturn');		
		//register css
		wp_enqueue_style( 'bootstrap', get_template_directory_uri() . '/css/bootstrap.min.css', array(), $theme_version->get('Version'));
		wp_enqueue_style( 'feather-icons', get_template_directory_uri(). '/css/feather.css', array(),$theme_version->get('Version'), 'all' );
		wp_enqueue_style( 'fontawesome', get_template_directory_uri(). '/css/fontawesome/fontawesome.css', array(), $theme_version->get('Version'), 'all' );
		wp_enqueue_style( 'tuturn-style', get_stylesheet_uri(), array(), $theme_version->get('Version') );
		wp_enqueue_style( 'tuturn-responsive', get_template_directory_uri() . '/css/responsive.css',  array(), $theme_version->get('Version'));
		wp_register_style( 'tuturn-rtl', get_template_directory_uri() . '/css/rtl.css',  array(), $theme_version->get('Version'));
		if(is_rtl()){ wp_enqueue_style( 'tuturn-rtl' );}

		if (function_exists('tuturn_add_dynamic_styles')) {
			$custom_css = tuturn_add_dynamic_styles();
			wp_add_inline_style('tuturn-style', $custom_css);
		}

		//register js
		wp_enqueue_script( 'bootstrap', get_template_directory_uri() . '/js/vendor/bootstrap.min.js', array( 'jquery' ), $theme_version->get('Version'), true );
		wp_enqueue_script( 'tuturn-callbacks', get_template_directory_uri() . '/js/callbacks.js', array('jquery'), $theme_version->get('Version'), true );

		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
		
		wp_localize_script('tuturn-callbacks', 'tuturn_vars', array(
			'loading_duration'			=> $loading_duration,
            'ajaxurl'					=> admin_url('admin-ajax.php'),
        ));
	}
	add_action( 'wp_enqueue_scripts', 'tuturn_scripts' );
}

/**
 * @Enque Google Font
 * @return
 */
if (!function_exists('tuturn_enqueue_google_fonts')) {
    function tuturn_enqueue_google_fonts() {
		$protocol = is_ssl() ? 'https' : 'http';		
		//Default theme font famlies
		$font_families	= array();
		$font_families[] = 'Outfit:400,500,600,700';
		$font_families[] = 'Open+Sans:400,600';
		$font_families[] = 'Gochi+Hand';		
		 $query_args = array (
			 'family' => implode('%7C' , $font_families) ,
			 'subset' => 'latin,latin-ext' ,
        );
        $theme_fonts = add_query_arg($query_args , $protocol.'://fonts.googleapis.com/css');
		wp_enqueue_style('tuturn-default-google-fonts' , esc_url_raw($theme_fonts), array () , null);
    }
    add_action('wp_enqueue_scripts' , 'tuturn_enqueue_google_fonts');
}

/**
 * @Set Post Views
 * @return {}
 */
if (!function_exists('tuturn_add_dynamic_styles')) {

    function tuturn_add_dynamic_styles() {
        global $tuturn_settings;
		$primary_color      =  !empty($tuturn_settings['tu_primary_color']) ? $tuturn_settings['tu_primary_color'] : '#6A307D';
        $secondary_color    =  !empty($tuturn_settings['tu_secondary_color']) ? $tuturn_settings['tu_secondary_color'] : '#F97316';
        $font_color         =  !empty($tuturn_settings['tu_font_color']) ? $tuturn_settings['tu_font_color'] : '#1C1C1C';
        $text_dark_color    =  !empty($tuturn_settings['text_dark_color']) ? $tuturn_settings['text_dark_color'] : '#484848';
        $text_light_color   =  !empty($tuturn_settings['text_light_color']) ? $tuturn_settings['text_light_color'] : '#676767';
        $button_bgcolor     =  !empty($tuturn_settings['button_bgcolor']) ? $tuturn_settings['button_bgcolor'] : '#6A307D';
        $button_textcolor   =  !empty($tuturn_settings['button_textcolor']) ? $tuturn_settings['button_textcolor'] : '#ffffff';
        $hyperlink          =  !empty($tuturn_settings['hyperlink']) ? $tuturn_settings['hyperlink'] : '#1DA1F2';
        $footerbg           =  !empty($tuturn_settings['footerbg']) ? $tuturn_settings['footerbg'] : '#2a1332';
		
        ob_start();
		$theme_color          = $primary_color;
		$font_color           = $font_color;
		$text_dark_color      = $text_dark_color;
		$text_light_color     = $text_light_color;
		$button_bgcolor       = $button_bgcolor;
		$button_textcolor     = $button_textcolor;
		$hyperlink            = $hyperlink;
		$footerbg             = $footerbg;
		?>
		:root {
			--themecolor: <?php echo esc_html($theme_color);?>;
			--orange: <?php echo esc_html($secondary_color);?>;
			--font_color: <?php echo esc_html($font_color);?>;
			--text_dark_color:<?php echo esc_html($text_dark_color);?>;
			--text_light_color:<?php echo esc_html($text_light_color);?>;
			--button_bgcolor: <?php echo esc_html($button_bgcolor);?>;
			--button_textcolor: <?php echo esc_html($button_textcolor);?>;
			--hyperlink: <?php echo esc_html($hyperlink);?>;
			--footerbg: <?php echo esc_html($footerbg);?>;
		}
		<?php
        return ob_get_clean();
    }
}

/**
 * Include files
 */
require get_template_directory() . '/inc/template-tags.php';
require get_template_directory() . '/inc/template-functions.php';
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}