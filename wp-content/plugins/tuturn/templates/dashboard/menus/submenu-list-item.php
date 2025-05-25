<?php
/**
 * Menus sub menu items
 *
 * @package     Tuturn
 * @subpackage  Tuturn/templates/dashboard/menus
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
*/
global $current_user, $post;
$reference 		 = (isset($args['ref']) && $args['ref'] <> '') ? $args['ref'] : '';
$mode 			 = (isset($args['mode']) && $args['mode'] <> '') ? $args['mode'] : '';
$title 			 = (isset($args['title']) && $args['title'] <> '') ? $args['title'] : '';
$id 			 = (isset($args['id']) && $args['id'] <> '') ? $args['id'] : '';
$icon_class 	 = (isset($args['icon']) && $args['icon'] <> '') ? $args['icon'] : '';
$class 			 = (isset($args['class']) && $args['class'] <> '') ? $args['class'] : '';
$url 			 = (isset($args['url']) && $args['url'] <> '') ? $args['url'] : '';
$user_identity 	 = $current_user->ID;
?>
<li class="<?php echo esc_attr($class); ?>">
	<a href="<?php echo esc_attr( $url ); ?>">
        <?php 
			if(isset($icon_class) && !empty($icon_class)){?>
				<i class="<?php echo esc_attr($icon_class);?>"></i>
				<?php
			}
			
			if( !empty($title) ){
        		echo esc_html($title);
			}
        ?>
	</a>
</li>