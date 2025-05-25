<?php

/**
 * instructor add to favourites
 *
 * @package     Tuturn
 * @subpackage  Tuturn/templates/nstructor-loop
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
 */
global  $post, $current_user;
$userType					= apply_filters('tuturnGetUserType', $current_user->ID);
$student_profile_id    		= tuturn_get_linked_profile_id($current_user->ID);
$favourite_instructor   	= get_post_meta($student_profile_id, 'favourite_instructor', true);
?>
<a class="tu-linkheart" href="javascript:void(0);" data-user_id="<?php echo intval($current_user->ID); ?>" data-profile_id="<?php echo intval($post->ID); ?>">
	<?php if (!empty($favourite_instructor) && in_array($post->ID, $favourite_instructor)) { ?>
		<i class="icon icon-heart tu-colorred"></i><span><?php esc_html_e('Saved', 'tuturn'); ?></span>
	<?php } else { ?>
		<i class="icon icon-heart"></i><span><?php echo esc_html($label); ?></span>
	<?php } ?>
</a>