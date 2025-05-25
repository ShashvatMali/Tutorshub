<?php
/**
 *
 * Template Name: Service Checkout
 *
 * @package     tuturn
 * @subpackage  tuturn/templates
 * @author      Amentotech <info@amentotech.com>
 * @link       https://themeforest.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
*/

get_header();
global $woocommerce;

$data_transient = get_transient('tuturn_booked_service_data'); 
$section_col = 'col-xl-8';

if ( !is_active_sidebar( 'expertio-sidebar' ) ) {
	$section_col = 'col-xl-12';
}
?>
<div class="tu-main-section">
	<div class="container">
		<div class="row tu-blogs-bottom">
			<div class="col-xl-12">
				<?php
					while ( have_posts() ) :
						the_post();
						the_content();
					endwhile; // End of the loop.
				?>
			</div>
		</div>
	</div>
</div>
<?php
get_footer();
