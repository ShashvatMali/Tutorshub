<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Tuturn
 */

if ( ! is_active_sidebar( 'tuturn-sidebar' ) ) {
	return;
}
?>
<?php dynamic_sidebar( 'tuturn-sidebar' ); ?>
