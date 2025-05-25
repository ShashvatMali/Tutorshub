<?php

/**
 * 
 * Class 'Tuturn_Admin_Menu_Options' defines the product post type custom taxonomy languages
 *
 * @package     Tuturn
 * @subpackage  Tuturn/admin/
 * @author      Amentotech <info@amentotech.com>
 * @link        http://amentotech.com/
 * @version     1.0
 * @since       1.0
 */
class Tuturn_Admin_Menu_Options
{

	/**
	 * Language Taxonomy
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function __construct()
	{
		add_action('init', array(&$this, 'menus_settings'));
		add_action('admin_footer', array(&$this, 'tuturn_menu_meta'), 999);
	}

	/**
	 * @Init taxonomy
	 */
	public function menus_settings()
	{
		/* add extra data */		
		add_action('wp_update_nav_menu_item', array(&$this, 'tuturn_menus_meta_update'), 10, 3);
	}

	/**
	 * generate meta fields
	 */
	public function tuturn_menu_meta()
	{
		$screen = get_current_screen();		
		$recently_edited = absint( get_user_option( 'nav_menu_recently_edited' ) );		
		$nav_menu_selected_id = isset( $_REQUEST['menu'] ) ? (int) $_REQUEST['menu'] : $recently_edited;

		$checked	= $elementor_checked = '';
		if(!empty($nav_menu_selected_id)){
			$header_menu	= get_term_meta($nav_menu_selected_id, 'main_header_menu', true);
			if (!empty($header_menu) && $header_menu == 'yes') {
				$checked	= 'checked';
			}

			$elementor_header_menu	= get_term_meta($nav_menu_selected_id, 'elementor_header_menu', true);
			if (!empty($elementor_header_menu) && $elementor_header_menu == 'yes') {
				$elementor_checked	= 'checked';
			}

		}
		
		if(!empty($screen->id) && $screen->id == 'nav-menus'){
			wp_enqueue_script('wp-util');
			?>
			<script type="text/html" id="tmpl-tu-menus-settings">			
				<div class="main-menu-settings">
					<h3><?php esc_html_e('Menu Settings', 'tuturn'); ?></h3>
					<fieldset class="menu-settings-group main-header-menu-settings">
						<legend class="menu-settings-group-name howto"><?php esc_html_e('Main menu', 'tuturn');?></legend>
						<div class="menu-settings-input checkbox-input">
							<input type="hidden" name="main_header_menu" value="no" />
							<input type="checkbox" name="main_header_menu" value="yes" <?php echo esc_attr($checked); ?> />
							<label><?php esc_html_e('Main header menu', 'tuturn'); ?></label>
						</div>
					</fieldset>			
					<fieldset class="menu-settings-group main-header-menu-settings">
						<legend class="menu-settings-group-name howto"><?php esc_html_e('Dashboard menu', 'tuturn');?></legend>
						<div class="menu-settings-input checkbox-input">
							<input type="hidden" name="elementor_header_menu" value="no" />
							<input type="checkbox" name="elementor_header_menu" value="yes" <?php echo esc_attr($elementor_checked); ?> />
							<label><?php esc_html_e('Elementor dashboard menu (This menu only work when you are using Elementor header)', 'tuturn'); ?></label>
						</div>
					</fieldset>			
				</div>
			</script>
			<?php
		}		
	}

	/**
	 * update meta fields
	 */
	public function tuturn_menus_meta_update($menu_id, $menu_item_db_id, $args)
	{	
		if (!empty($_POST['main_header_menu']) && $_POST['main_header_menu']) {
			update_term_meta($menu_id, 'main_header_menu', $_POST['main_header_menu']);
		}

		if (!empty($_POST['elementor_header_menu']) && $_POST['elementor_header_menu']) {
			update_term_meta($menu_id, 'elementor_header_menu', $_POST['elementor_header_menu']);
		}
	}
}

new Tuturn_Admin_Menu_Options();
