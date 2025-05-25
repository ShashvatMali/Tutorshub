<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Tuturn
 */
?>
<?php if(!empty(get_the_content())){?>
	<article id="post-<?php the_ID(); ?>" <?php post_class('tu-theme-box'); ?>>
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
	</article>
<?php }?>
