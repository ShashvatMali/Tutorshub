<?php
/**
 * Menus avatar dropdown items
 *
 * @package     Tuturn
 * @subpackage  Tuturn/templates/dashboard/menus
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
*/
global $current_user;
$reference 		 = (isset($args['ref']) && $args['ref'] <> '') ? esc_html($args['ref']) : '';
$mode 			 = (isset($args['mode']) && $args['mode'] <> '') ? esc_html($args['mode']) : '';
$title 			 = (isset($args['title']) && $args['title'] <> '') ? esc_html($args['title']) : '';
$id 			 = (isset($args['id']) && $args['id'] <> '') ? esc_attr($args['id']) : '';
$icon_class 	 = (isset($args['icon']) && $args['icon'] <> '') ? esc_html($args['icon']) : '';
$class 			 = (isset($args['class']) && $args['class'] <> '') ? esc_html($args['class']) : '';
$url 			 = (isset($args['url']) && $args['url'] <> '') ? esc_html($args['url']) : '#';
$user_identity 	 = $current_user->ID;

if(isset($args['submenu']) && is_array($args['submenu']) && count($args['submenu'])>0){
    $class .= ' tu-menudropdown';
}

if(!empty($reference) && $reference == 'logout'){
	$url	= esc_url(wp_logout_url(home_url('/')));
} else if( !empty($reference) && $reference === 'packages'){
    $url	= tuturn_get_page_uri('package_page');
} 
?>
<li class="tu-menudropdown <?php echo esc_attr($class); ?>">
    <a href="<?php echo esc_attr( $url ); ?>">
        <?php if(isset($icon_class) && !empty($icon_class)){?>
            <i class="<?php echo esc_attr($icon_class);?>"></i>
        <?php } echo esc_html($title);?>
    </a>
    <?php if(isset($args['submenu']) && is_array($args['submenu']) && count($args['submenu'])>0){ ?>
        <ul class="sub-menu">
            <?php foreach($args['submenu'] as $key => $submenu_item){
                $submenu_item['id'] = $key;
                $submenu_item['reference'] = $reference;
                $submenu_item['url'] = $url;
                tuturn_get_template_part('dashboard/menus/submenu', 'list-item', $submenu_item);
            }?>
        </ul>
    <?php }?>
</li>