<?php
/**
 * Template Name: Inbox
 *
 * @package     Tuturn
 * @subpackage  Tuturn/templates
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
*/
global $tuturn_settings;
if( !is_user_logged_in() ){
	$redirect_url   = get_home_url();    
    wp_redirect( $redirect_url );
    exit;
}

get_header();
?>
<section class="tu-main-section">
	<div class="container">
		<div class="row">
			<div class="col-12">
				<div class="tu-inbox-wrapper">
					<div class="tu-boxsmtitle">
						<h4><?php esc_html_e('Start conversation','tuturn');?></h4>
					</div>
					<?php echo do_shortcode('[getGuppyConversation]');?>
				</div>
			</div>
		</div>
	</div>
</section>
<?php
get_footer();
