<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Tuturn
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class('tu-theme-box'); ?>>
	<header class="entry-header tb-checkoutheader">
		<?php the_title( '<h3 class="tuturn-entry-title">', '</h3>' ); ?>
	</header>
	<?php tuturn_post_thumbnail(); ?>

	<div class="entry-content">
		<?php
			the_content();

			wp_link_pages(
				array(
					'before' => '<div class="tuturn-page-links">' . esc_html__( 'Pages:', 'tuturn' ),
					'after' => '</div>',
				)
			);
		?>
	</div>
</article>
				