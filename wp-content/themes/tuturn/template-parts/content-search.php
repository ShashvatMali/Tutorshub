<?php
/**
 * Template part for displaying results in search pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Tuturn
 */

?>
<article id="post-<?php the_ID(); ?>" <?php post_class('tu-theme-box'); ?>>
	<?php tuturn_post_thumbnail(); ?>
	<header class="tuturn-entry-header">
		<?php echo get_the_term_list($post->ID, 'category', '<ul class="tu-taglinks"><li>', '</li><li>', '</li></ul>'); ?>
		<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
		<?php if ( 'post' === get_post_type() ) : ?>
			<div class="entry-meta">
				<span class="tuturn-byline">
                    <i class="icon icon-message-square"></i>
					<span><?php comments_number(esc_html__('0 Comments' , 'tuturn') , esc_html__('1 Comment' , 'tuturn') , esc_html__('% Comments' , 'tuturn')); ?></span>
                </span>
				<?php
					tuturn_posted_on();
				?>
			</div>
		<?php endif; ?>
	</header>
	

	<div class="tuturn-entry-summary">
		<?php the_excerpt(); ?>
	</div>
</article>