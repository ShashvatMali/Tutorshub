<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Tuturn
 */

?>
<article id="post-<?php the_ID(); ?>" <?php post_class('tu-theme-box'); ?>>
	<header class="tuturn-entry-header">
		<?php
		if ( is_singular() ) {
			the_title( '<h4 class="tuturn-entry-title">', '</h4>' );
		}else{
			the_title( '<h4 class="tuturn-entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h4>' );
		}

		if ( 'post' === get_post_type() ) {?>
			<div class="tuturn-entry-meta">
				<?php
					tuturn_posted_on();
					tuturn_posted_by();
				?>
			</div>
		<?php } ?>
	</header>

	<?php tuturn_post_thumbnail(); ?>

	<div class="tuturn-entry-content">
		<?php
		the_content();
		wp_link_pages(
			array(
				'before' => '<div class="tuturn-page-links">' . esc_html__( 'Pages:', 'tuturn' ),
				'after'  => '</div>',
			)
		);
		?>
	</div>
	<footer class="tuturn-entry-footer">
		<?php tuturn_entry_footer(); ?>
	</footer>
</article>
