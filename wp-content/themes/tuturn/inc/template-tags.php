<?php
/**
 * Custom template tags for this theme
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Tuturn
 */

if ( ! function_exists( 'tuturn_post_tags' ) ) :
	/**
	 * Prints HTML with meta information for the current post tags.
	 */
	function tuturn_post_tags() {
		$tag_list = get_the_tag_list( '<ul class="post-categories"><li>', '</li><li>', '</li></ul>' );
		if(!empty($tag_list)){
			printf( '<div class="tu-theme-box"><span class="tuturn-cat-links"><i class="icon icon-tag"></i>' . esc_html__( 'Tags', 'tuturn' ) . '%1$s</span></div>', $tag_list );
		}
	}
endif;

/**
 * Custom template categories for this theme
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Tuturn
 */

if ( ! function_exists( '' ) ) :
	/**
	 * Prints HTML with meta information for the current post categories.
	 */
	function tuturn_post_categories() {
		$tag_list = get_the_category_list( '<ul class="post-categories"><li>', '</li><li>', '</li></ul>' );
		if(!empty($tag_list)){
			printf( '<div class="tu-theme-box"><span class="tuturn-cat-links"><i class="icon icon-tag"></i>' . esc_html__( 'Categories', 'tuturn' ) . '%1$s</span></div>', $tag_list );
		}
	}
endif;

/**
 * Custom template post date for this theme
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Tuturn
 */

if ( ! function_exists( 'tuturn_posted_on' ) ) :
	/**
	 * Prints HTML with meta information for the current post-date/time.
	 */
	function tuturn_posted_on() {
		$time_string = '<time class="tuturn-entry-date tuturn-published tuturn-updated" datetime="%1$s">%2$s</time>';
		
		$time_string = sprintf(
			$time_string,
			esc_attr( get_the_date( DATE_W3C ) ),
			esc_html( get_the_date() )
		);

		$posted_on = sprintf(
			/* translators: %s: post date. */
			esc_html_x( '%s', 'post date', 'tuturn' ),
			'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
		);

		echo '<div class="tuturn-posted-on"><i class="icon icon-calendar"></i>' . $posted_on . '</div>';

	}
endif;

if ( ! function_exists( 'tuturn_posted_by' ) ) :
	/**
	 * Prints HTML with meta information for the current author.
	 */
	function tuturn_posted_by() {
		$byline = sprintf(
			/* translators: %s: post author. */
			esc_html_x( '%s', 'post author', 'tuturn' ),
			'<span class="tuturn-author tuturn-vcard"><a href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
		);

		echo '<span class="tuturn-byline"><i class="icon icon-user"></i>' . $byline . '</span>';

	}
endif;

if ( ! function_exists( 'tuturn_entry_footer' ) ) :
	/**
	 * Prints HTML with meta information for the categories, tags and comments.
	 */
	function tuturn_entry_footer() {
		// Hide category and tag text for pages.
		if ( 'post' === get_post_type() && is_singular() ) {
			$categories_list = get_the_category_list();
			if ( $categories_list ) {
				/* translators: 1: list of categories. */
				printf( '%1$s', $categories_list );
			}
		}else if ( 'post' === get_post_type() ) {
			$categories_list = get_the_category_list();
			if ( $categories_list ) {
				/* translators: 1: list of categories. */
				printf( '<div class="tu-theme-box"><span class="tuturn-cat-links"><i class="icon icon-folder"></i>' . esc_html__( 'Categories:', 'tuturn' ) . ' %1$s</span></div>', $categories_list );
			}

			$tags_list = get_the_tag_list( '', esc_html_x( ', ', 'list item separator', 'tuturn' ) );
		}
	}
endif;

if ( ! function_exists( 'tuturn_post_thumbnail' ) ) {
	/**
	 * Displays an optional post thumbnail.
	 *
	 * Wraps the post thumbnail in an anchor element on index views, or a div
	 * element when on single views.
	 */
	function tuturn_post_thumbnail() {
		if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
			return;
		}

		if ( is_singular() ) :?>
			<div class="tuturn-post-thumbnail">
				<?php the_post_thumbnail('tu_post_detail'); ?>
			</div><!-- .post-thumbnail -->
		<?php else : ?>
			<a class="tuturn-post-thumbnail" href="<?php echo esc_url(get_the_permalink() ); ?>" aria-hidden="true" tabindex="-1">
				<?php
					the_post_thumbnail(
						'post-thumbnail',
						array(
							'alt' => the_title_attribute(
								array(
									'echo' => false,
								)
							),
						)
					);
				?>
			</a>
			<?php
		endif; // End is_singular().
	}
}

if ( ! function_exists( 'wp_body_open' ) ) {
	/**
	 * Start WordPress body
	 *
	 * @link https://core.trac.wordpress.org/ticket/12563
	 */
	function wp_body_open() {
		do_action( 'wp_body_open' );
	}
}