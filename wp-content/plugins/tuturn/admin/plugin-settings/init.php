<?php
/**
 * Theme Settings
 *
 * @package     Tuturn
 * @subpackage  Tuturn/admin/Plugin_Settings
 * @author      Amentotech <info@amentotech.com>
 * @link        http://amentotech.com/
 * @version     1.0
 * @since       1.0
*/

if ( ! class_exists( 'Redux' ) ) { return;}

require_once(TUTURN_DIRECTORY . '/libraries/scssphp/scss.inc.php');

$opt_name 	= "tuturn_settings";
$opt_name   = apply_filters( 'tuturn_settings_option_name', $opt_name );
$theme 		= wp_get_theme();

$args = array(
    'opt_name'    		=> $opt_name,
    'display_name' 		=> $theme->get('Name') ,
    'display_version' 	=> $theme->get('Version') ,
    'menu_type' 		=> 'menu',
    'allow_sub_menu' 	=> true,
    'menu_title' 		=> esc_html__('Tuturn Settings', 'tuturn'),
	'page_title'        => esc_html__('Tuturn Settings', 'tuturn') ,
    'google_api_key' 	=> '',
    'google_update_weekly' => false,
    'async_typography' 	   => true,
    'admin_bar' 		=> true,
    'admin_bar_icon' 	=> '',
    'admin_bar_priority'=> 50,
    'global_variable' 	=> $opt_name,
    'dev_mode' 			=> false,
    'update_notice' 	=> false,
    'customizer' 		=> false,
    'page_priority' 	=> null,
    'page_parent' 		=> 'themes.php',
    'page_permissions'  => 'manage_options',
    'menu_icon' 		=> 'dashicons-performance',
    'last_tab' 			=> '',
    'page_icon' 		=> 'icon-themes',
    'page_slug' 		=> 'tuturn_options',
    'save_defaults' 	=> true,
    'default_show' 		=> false,
    'default_mark' 		=> '',
    'show_import_export' => true
);
 
Redux::setArgs ($opt_name, $args);

$scan = glob(TUTURN_DIRECTORY."/admin/plugin-settings/settings/*");
foreach ( $scan as $path ) {
	include $path;
}

do_action( 'tuturn_settings_files');

if( !function_exists('tuturn_after_change_option') ){
    add_action ('redux/options/tuturn_settings/saved', 'tuturn_after_change_option');
    function tuturn_after_change_option($value){
        $primary_color      =  !empty($value['tu_primary_color']) ? $value['tu_primary_color'] : '#6A307D';
        $secondary_color    =  !empty($value['tu_secondary_color']) ? $value['tu_secondary_color'] : '#F97316';
        $font_color         =  !empty($value['tu_font_color']) ? $value['tu_font_color'] : '#1C1C1C';
        $text_dark_color    =  !empty($value['text_dark_color']) ? $value['text_dark_color'] : '#484848';
        $text_light_color   =  !empty($value['text_light_color']) ? $value['text_light_color'] : '#676767';
        $button_bgcolor     =  !empty($value['button_bgcolor']) ? $value['button_bgcolor'] : '#6A307D';
        $button_textcolor   =  !empty($value['button_textcolor']) ? $value['button_textcolor'] : '#ffffff';
        $hyperlink          =  !empty($value['hyperlink']) ? $value['hyperlink'] : '#1DA1F2';
        $footerbg           =  !empty($value['footerbg']) ? $value['footerbg'] : '#2a1332';
        
        $compiler       = new ScssPhp\ScssPhp\Compiler();
        $source_scss    = TUTURN_DIRECTORY . '/public/scss/main.scss';
        $scssContents   = file_get_contents($source_scss);
        $import_path    = TUTURN_DIRECTORY . '/public/scss';
        $compiler->addImportPath($import_path);

        $target_css = TUTURN_DIRECTORY . '/public/css/main.css';
        $variables  = array(
            '$theme_color'          => $primary_color,
            '$themecolor'          => $primary_color,
            '$orange'               => $secondary_color,
            '$font_color'           => $font_color,
            '$text_dark_color'      => $text_dark_color,
            '$text_light_color'     => $text_light_color,
            '$button_bgcolor'       => $button_bgcolor,
            '$button_textcolor'     => $button_textcolor,
            '$hyperlink'            => $hyperlink,
            '$footerbg'             => $footerbg
        );
        $compiler->setVariables($variables);
        
        $css = $compiler->compile($scssContents);
        if (!empty($css) && is_string($css)) {
            file_put_contents($target_css, $css);
        }
    }
}


//Redux design wrapper start
if( !function_exists('system_redux_style_start') ){
    add_action ('redux/'.$opt_name.'/panel/before', 'system_redux_style_start');
    function system_redux_style_start($value){
        echo '<div class="amt-redux-design">';
    }
}

//Redux design wrapper end
if( !function_exists('system_redux_style_end') ){
    add_action ('redux/'.$opt_name.'/panel/after', 'system_redux_style_end');
    function system_redux_style_end($value){
        echo '</div>';
    }
}
