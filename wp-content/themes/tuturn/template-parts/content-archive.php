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
	<?php tuturn_post_thumbnail(); ?>
	<header class="tuturn-entry-header">
		<?php echo get_the_term_list($post->ID, 'category', '<ul class="tu-taglinks"><li>', '</li><li>', '</li></ul>'); ?>
		<?php
		if ( is_singular() ) {
			the_title( '<h4 class="tuturn-entry-title">', '</h4>' );
		}else if ( is_home() && ! is_front_page() ) {?>
			<header>
				<h1 class="tuturn-page-title tuturn-screen-reader-text"><?php single_post_title(); ?></h1>
			</header>
			<?php
		}else{
			the_title( '<h4 class="tuturn-entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h4>' );
		}

		if ( 'post' === get_post_type() ) {?>
			<div class="tuturn-entry-meta">				
				<span class="tuturn-byline">
                    <i class="icon icon-message-square"></i>
					<span><?php comments_number(esc_html__('0 Comments' , 'tuturn') , esc_html__('1 Comment' , 'tuturn') , esc_html__('% Comments' , 'tuturn')); ?></span>
                </span>
				<?php
					tuturn_posted_on();
				?>
			</div>
		<?php } ?>
	</header>
	<div class="tuturn-entry-content">
		<?php
		if (!empty(get_the_excerpt())) {
			the_excerpt();
		}
		
		wp_link_pages(
			array(
				'before' => '<div class="tuturn-page-links">' . esc_html__( 'Pages:', 'tuturn' ),
				'after'  => '</div>',
			)
		);
		?>
	</div>
</article>