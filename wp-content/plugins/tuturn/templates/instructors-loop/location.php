<?php

/**
 * Instructor location
 *
 * @package     Tuturn
 * @subpackage  Tuturn/templates/nstructor-loop
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
 */
global $post, $tuturn_settings;
$show_address_type          = !empty($tuturn_settings['show_address_type']) ? $tuturn_settings['show_address_type'] : 'address';
$profile_state              = !empty($tuturn_settings['profile_state']) ? $tuturn_settings['profile_state'] : false;
$instructor_id              = !empty($post->ID) ? intval($post->ID) : '';
$location                   = get_post_meta($instructor_id, '_address', true);
$location                   = !empty($location) ? sanitize_text_field($location) : '';
$tu_average_rating          = get_post_meta($post->ID, 'tu_average_rating', true);
$tu_average_rating          = !empty($tu_average_rating) ? $tu_average_rating : 0;
$tu_review_users            = get_post_meta($post->ID, 'tu_review_users', true);
$tu_review_users            = !empty($tu_review_users) ? $tu_review_users : 0;

if (!empty($show_address_type) && $show_address_type != 'address' && $profile_state == true) {
    $list_adress        = array();
    $country_region     = get_post_meta( $post->ID, '_country_region', true);
    $_country           = get_post_meta( $post->ID, '_country', true);
    $_state             = get_post_meta( $post->ID, '_state', true);
    $_city              = get_post_meta( $post->ID, '_city', true);

    if(!empty($_city)){
        $list_adress[]    = $_city;
    }

    $states             = !empty($country_region) ? tuturn_country_array($country_region,'') : array();

    if(!empty($states) && !empty($states[strtoupper($_state)])){
        $list_adress[]      = $states[strtoupper($_state)];
    }
   
    if (!empty($show_address_type) && $show_address_type == 'city_state_country' && !empty($_country)){
        $countries 		    = tuturn_country_array();
        $list_adress[]      = !empty($countries[strtoupper($_country)]) ? $countries[strtoupper($_country)] : $_country;
    }
    
    $location   = !empty($list_adress) ? implode(', ',$list_adress) : $location;
}
?>

<?php if (!empty($location)) { ?>
    <address><i class="icon icon-map-pin"></i><?php echo esc_html($location) ?></address>
<?php } ?>
<div class="tu-rating">
    <h6><?php echo number_format((float)$tu_average_rating, 1, '.', '');?></h6>
    <i class="fas fa-star"></i>
    <span>(<?php echo str_pad($tu_review_users, 2, '0', STR_PAD_LEFT);?>)</span>               
</div>