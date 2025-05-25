<?php
/**
 * Fired during plugin activation
 *
 * @link       https://codecanyon.net/user/amentotech/portfolio
 * @since      1.0.0
 *
 * @package    Tuturn
 * @subpackage Tuturn/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Tuturn
 * @subpackage Tuturn/includes
 * @author     Amento Tech Pvt ltd <info@amentotech.com>
 */
class Tuturn_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		if ( ! current_user_can( 'activate_plugins' ) ) return;
		$current_user		= wp_get_current_user();

		$settings_page		= Tuturn_Activator::get_template_page('templates/profile-settings.php');
		if(count($settings_page)<1){
			$title = esc_html__('User Dashboard', 'tuturn');
			Tuturn_Activator::create_page($title, $current_user->ID, 'templates/profile-settings.php');
		}

		$instructor_page		= Tuturn_Activator::get_template_page('templates/search-instructors.php');
		if(count($instructor_page)<1){
			$title = esc_html__('Find Instructors', 'tuturn');
			Tuturn_Activator::create_page($title, $current_user->ID, 'templates/search-instructors.php');
		}

		$student_page		= Tuturn_Activator::get_template_page('templates/search-students.php');
		if(count($student_page)<1){
			$title = esc_html__('Find Students', 'tuturn');
			Tuturn_Activator::create_page($title, $current_user->ID, 'templates/search-students.php');
		}
		
		$packages_page		= Tuturn_Activator::get_template_page('templates/packages.php');
		if(count($packages_page)<1){
			$title = esc_html__('Packages', 'tuturn');
			Tuturn_Activator::create_page($title, $current_user->ID, 'templates/packages.php');
		}

		$inbox_page		= Tuturn_Activator::get_template_page('templates/inbox.php');
		if(count($inbox_page)<1){
			$title = esc_html__('Inbox', 'tuturn');
			Tuturn_Activator::create_page($title, $current_user->ID, 'templates/inbox.php');
		}
	}

	public static function get_template_page($page_template){
		$pages = get_pages(array(
			'meta_key' => '_wp_page_template',
			'meta_value' => $page_template
		));
		return $pages;
	}

	public static function create_page($title, $user_id, $template_file){
		// create page
		$page = array(
			'post_title'  => $title,
			'post_status' => 'publish',
			'post_author' => $user_id,
			'post_type'   => 'page',
		);			
		// insert the post into the database
		$post_id = wp_insert_post( $page );
		update_post_meta( $post_id, '_wp_page_template', $template_file );
	}
}