<?php

/**
 * Instructor template hooks
 *
 * @link       https://themeforest.net/user/amentotech/portfolio
 * @since      1.0.0
 *
 * @package    tuturn
 * @subpackage tuturn/public/partials
 */

/**
 * Instructor search categoreis field
 *
 * @author Amentotech <theamentotech@gmail.com>
*/
if (!function_exists('tuturn_categories_search')) {
	function tuturn_categories_search($args = array()){
		tuturn_get_template( 'instructor-search/category-search.php', $args );
	}
	add_action( "tuturn_categories_search", "tuturn_categories_search" );
}
/**
 * instructor search categoreis field
 *
 * @author Amentotech <theamentotech@gmail.com>
*/
if (!function_exists('tuturn_sub_categories')) {
	function tuturn_sub_categories($args = array()){
		tuturn_get_template( 'instructor-search/sub-categories.php', $args );
	}
	add_action( "tuturn_sub_categories", "tuturn_sub_categories" );
}

/**
 * instructor search price range field
 *
 * @author Amentotech <theamentotech@gmail.com>
*/
if (!function_exists('tuturn_rating_search')) {
	function tuturn_rating_search($args = array()){
		tuturn_get_template( 'instructor-search/rating-search.php', $args );
	}
	add_action( "tuturn_rating_search", "tuturn_rating_search" );
}

// price range

if (!function_exists('tuturn_price_range')) {
	function tuturn_price_range($args = array()){
		tuturn_get_template( 'instructor-search/price-range.php',$args);
	}
	add_action( "tuturn_price_range", "tuturn_price_range" );
}
if (!function_exists('tuturn_tutur_availability')) {
	function tuturn_tutur_availability($args = array()){
		tuturn_get_template( 'instructor-search/tutur-availability.php',$args);
	}
	add_action( "tuturn_tutur_availability", "tuturn_tutur_availability" );
}
/**
 * Instructors search miscellaneous field
 *
 * @author Amentotech <theamentotech@gmail.com>
*/
if (!function_exists('tuturn_miscellaneous_search')) {
	function tuturn_miscellaneous_search($args = array()){
		tuturn_get_template( 'instructor-search/miscellaneous-search.php', $args );
	}
	add_action( "tuturn_miscellaneous_search", "tuturn_miscellaneous_search" );
}

/**
 * Instructor location field
 *
 * @author Amentotech <theamentotech@gmail.com>
*/
if (!function_exists('tuturn_location_search')) {
	function tuturn_location_search($args = array()){
		tuturn_get_template( 'instructor-search/location-search.php', $args );
	}
	add_action( "tuturn_location_search", "tuturn_location_search" );
}

/**
 * instructor title
 *a
 * @author Amentotech <theamentotech@gmail.com>
*/
if (!function_exists('tuturn_instructor_title')) {
	function tuturn_instructor_title($tuturn_args=''){
		tuturn_get_template( 'instructors-loop/title.php',$tuturn_args);
	}
	add_action( "tuturn_instructor_title", "tuturn_instructor_title" );
}
/**
 *  search empty record
 *
 * @author Amentotech <theamentotech@gmail.com>
*/
if (!function_exists('tuturn_instructors_empty_record')) {
	function tuturn_instructors_empty_record(){
		tuturn_get_template( 'instructor-search/empty-record.php');
	}
	add_action( "tuturn_instructors_empty_record", "tuturn_instructors_empty_record" );
}
/**
 * Instructor location
 *
 * @author Amentotech <theamentotech@gmail.com>
*/
if (!function_exists('tuturn_instructor_location')) {
	function tuturn_instructor_location($tuturn_args=''){
		tuturn_get_template( 'instructors-loop/location.php',$tuturn_args);
	}
	add_action( "tuturn_instructor_location", "tuturn_instructor_location" );
}
/**
 * Instructor description
 *
 * @author Amentotech <theamentotech@gmail.com>
*/
if (!function_exists('tuturn_instructor_short_description')) {
	function tuturn_instructor_short_description($tuturn_args=''){
		tuturn_get_template( 'instructors-loop/short-description.php',$tuturn_args);
	}
	add_action( "tuturn_instructor_short_description", "tuturn_instructor_short_description" );
}
/**
 * Instructor media gallery
 *
 * @author Amentotech <theamentotech@gmail.com>
*/
if (!function_exists('tuturn_instructor_image')) {
	function tuturn_instructor_image($tuturn_args=''){

		tuturn_get_template( 'instructors-loop/feature-image.php',$tuturn_args);
	}
	add_action( "tuturn_instructor_image", "tuturn_instructor_image", 10, 2 );
}

/**
 * Instructor Hourly rate
 *
 * @author Amentotech <theamentotech@gmail.com>
*/
if (!function_exists('tuturn_instructor_hourly_rate')) {
	function tuturn_instructor_hourly_rate($tuturn_args=''){
		tuturn_get_template( 'instructors-loop/hourly-rate.php',$tuturn_args);
	}
	add_action( "tuturn_instructor_hourly_rate", "tuturn_instructor_hourly_rate" );
}

/**
 * Instructor Hourly rate
 *
 * @author Amentotech <theamentotech@gmail.com>
*/
if (!function_exists('tuturn_instructor_subjects')) {
	function tuturn_instructor_subjects($tuturn_args=''){
		tuturn_get_template( 'instructors-loop/instructor-subjects.php',$tuturn_args);
	}
	add_action( "tuturn_instructor_subjects", "tuturn_instructor_subjects" );
}

/**
 * Instructor image gallery
 *
 * @author Amentotech <theamentotech@gmail.com>
*/
if (!function_exists('tuturn_instructor_image_gallery')) {
	function tuturn_instructor_image_gallery($tuturn_args=''){
		tuturn_get_template( 'instructors-loop/image-gallery.php',$tuturn_args);
	}
	add_action( "tuturn_instructor_image_gallery", "tuturn_instructor_image_gallery" );
}

/**
 * Instructor Availibility
 *
 * @author Amentotech <theamentotech@gmail.com>
*/
if (!function_exists('tuturn_instructor_avilibility')) {
	function tuturn_instructor_avilibility($tuturn_args=''){
		tuturn_get_template( 'instructors-loop/avilibility.php',$tuturn_args);
	}
	add_action( "tuturn_instructor_avilibility", "tuturn_instructor_avilibility" );
}

/**
 * Instructor description
 *
 * @author Amentotech <theamentotech@gmail.com>
*/
if (!function_exists('tuturn_teaching_preference')) {
	function tuturn_teaching_preference($tuturn_args=''){
		tuturn_get_template( 'instructors-loop/teaching_preference.php',$tuturn_args);
	}
	add_action( "tuturn_teaching_preference", "tuturn_teaching_preference" );
}

/**
 * Instructor description
 *
 * @author Amentotech <theamentotech@gmail.com>
*/
if (!function_exists('tuturn_instructor_add_to_save')) {
	function tuturn_instructor_add_to_save($tuturn_args=''){
		tuturn_get_template( 'instructors-loop/profile-add-to-save.php',$tuturn_args);
	}
	add_action( "tuturn_instructor_add_to_save", "tuturn_instructor_add_to_save" );
}

/**
 * Instructor soet by prices
 *
 * @author Amentotech <theamentotech@gmail.com>
*/
if (!function_exists('instructor_search_sort')) {
	function instructor_search_sort($tuturn_args=''){
		tuturn_get_template( 'instructors-loop/instructor-sortby-price.php',$tuturn_args);
	}
	add_action( "instructor_search_sort", "instructor_search_sort" );
}

/**
 * Instructor soet by prices
 *
 * @author Amentotech <theamentotech@gmail.com>
*/
if (!function_exists('tuturn_featured_profile')) {
	function tuturn_featured_profile($profile_id=''){
		tuturn_get_template( 'single-tuturn-instructor/featured-profile.php',array('profile_id' => $profile_id));
	}
	add_action( "tuturn_featured_profile", "tuturn_featured_profile" );
}
