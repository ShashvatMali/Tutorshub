<?php
/**
 * 
 * Class 'Tuturn_Admin_Taxonomies' defines the product post type custom taxonomy languages
 *
 * @package     Tuturn
 * @subpackage  Tuturn/admin/Taxonomy
 * @author      Amentotech <info@amentotech.com>
 * @link        http://amentotech.com/
 * @version     1.0
 * @since       1.0
*/
class Tuturn_Admin_Taxonomies {

	/**
	 * Language Taxonomy
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function __construct() {
		add_action('init', array(&$this, 'init_taxonomy'));
	}

	/**
	 * @Init taxonomy
	*/
	public function init_taxonomy() {
		$this->register_custom_taxonomy();
	}

	/**
	 * Regirster location Taxonomy
	*/
	public function register_custom_taxonomy() {
		//Languages
		$languages_labels = array(
			'name' 				=> esc_html__('Languages', 'tuturn'),
			'singular_name' 	=> esc_html__('Language','tuturn'),
			'search_items' 		=> esc_html__('Search Language', 'tuturn'),
			'all_items' 		=> esc_html__('All Language', 'tuturn'),
			'parent_item' 		=> esc_html__('Parent Language', 'tuturn'),
			'parent_item_colon' => esc_html__('Parent Language:', 'tuturn'),
			'edit_item' 		=> esc_html__('Edit Language', 'tuturn'),
			'update_item' 		=> esc_html__('Update Language', 'tuturn'),
			'add_new_item' 		=> esc_html__('Add New Language', 'tuturn'),
			'new_item_name' 	=> esc_html__('New Language Name', 'tuturn'),
			'menu_name' 		=> esc_html__('Languages', 'tuturn'),
		);
		
		$language_args = array(
			'hierarchical'          => true,
			'labels'                => apply_filters('tuturn_taxonomy_languages_labels', $languages_labels),
			'show_ui'               => true,
			'show_in_nav_menus' 	=> false,
			'show_admin_column'     => true,
			'query_var'             => true,
			'rewrite'               => array('slug' => 'languages'),			
			'show_in_rest'              => true,
			'rest_base'                 => 'languages',
			'rest_controller_class'     => 'WP_REST_Terms_Controller',
			
		);

		register_taxonomy('languages', array('tuturn-instructor', 'tuturn-student'), $language_args);	
		register_taxonomy_for_object_type('product_cat', 'tuturn-instructor');
	

	}
}

new Tuturn_Admin_Taxonomies();
