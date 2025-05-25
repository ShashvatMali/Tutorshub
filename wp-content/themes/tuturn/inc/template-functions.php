<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package Tuturn
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
if (!function_exists('tuturn__excerpt_length')) {
	function tuturn__excerpt_length( $length=22 ) {
		return 51;
	}
	add_filter( 'excerpt_length', 'tuturn__excerpt_length', 999 );
}

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
if (!function_exists('tuturn_excerpt_more')) {
	function tuturn_excerpt_more( $more='' ) {
		return '';
	}
	add_filter( 'excerpt_more', 'tuturn_excerpt_more' );
}

if (!function_exists('tuturn_body_classes')) {
	function tuturn_body_classes( $classes ) {
		// Adds a class of hfeed to non-singular pages.
		if ( ! is_singular() ) {
			$classes[] = 'hfeed';
		}

		// Adds a class of no-sidebar when there is no sidebar present.
		if ( ! is_active_sidebar( 'tuturn-sidebar' ) ) {
			$classes[] = 'no-sidebar';
		}

		return $classes;
	}
	add_filter( 'body_class', 'tuturn_body_classes' );
}

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
if (!function_exists('tuturn_pingback_header')) {
	function tuturn_pingback_header() {
		if ( is_singular() && pings_open() ) {
			printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
		}
	}
	add_action( 'wp_head', 'tuturn_pingback_header' );
}

/**
 * Pangination
 *
 * @return
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 */
if (!function_exists('tuturn_pagination')) {
	function tuturn_pagination($tuturn_query= '', $class= ''){
		global $wp_query;
		$tuturn_total = $wp_query->max_num_pages;
		
		if ($tuturn_total > 1) {
			if( !empty($class)){ ?>
				<div class="<?php echo esc_attr($class);?>">
			<?php } ?>
			<div class="tu-pagination">
				<?php echo paginate_links(array(
					'base'         => str_replace(999999999, '%#%', esc_url_raw(get_pagenum_link(999999999))),
					'total'        => $tuturn_total,
					'current'      => max(1, get_query_var('paged')),
					'format'       => '?paged=%#%',
					'show_all'     => false,
					'type'         => 'list',
					'end_size'     => 2,
					'mid_size'     => 1,
					'prev_next'    => true,
					'prev_text'    => sprintf('<i class="icon icon-chevron-left"></i>'),
					'next_text'    => sprintf('<i class="icon icon-chevron-right"></i>'),
					'add_args'     => false,
					'add_fragment' => '',
				)); ?>
			</div>
			<?php if( !empty($class)){ ?>
				</div>
			<?php }
		}
	}
}

/**
 * comments listings
 * @return slug
 */
if (!function_exists('tuturn_comments')) {

    function tuturn_comments($comment, $args, $depth) {
		$GLOBALS['comment'] = $comment;
		$args['reply_text'] = esc_html__('Reply','tuturn');	
		$post_author_id		= get_the_author_meta('ID');

		if(!empty($comment->user_id)){
			$post_author_id		= $comment->user_id;
		}
		$url    = get_author_posts_url($post_author_id);
		$empty_avatar = '';

		if(empty(get_avatar($comment, 100))){
			$empty_avatar = 'tu-empty-avatar';
		}

		$user_name		= '';
		$empty_avatar	= '';
		if(empty(get_avatar($comment->comment_ID, 100))){
			$empty_avatar = 'tu-empty-avatar';
		}

		$user_profile_id = get_user_meta($post_author_id, '_linked_profile', true);
		
		if(!empty($user_profile_id) && function_exists('tuturn_get_user_avatar')){
			$avatar_url         = apply_filters(
				'tuturn_avatar_fallback', tuturn_get_user_avatar(array('width' => 100, 'height' => 100), $user_profile_id), array('width' => 100, 'height' => 100)
			);
			
			if(function_exists('tuturn_get_username')){
				$user_name  = tuturn_get_username($user_profile_id);
			}
			$avatar = '<img src="'.esc_url($avatar_url).'" alt="'.esc_attr($user_name).'">';
		} else {
			$avatar_url = get_avatar_url($comment->user_id, 100);
			
			$author_obj = get_user_by('id', $post_author_id);

			if(!empty($author_obj->display_name)){
				$user_name  = $author_obj->display_name;
			}
			$avatar = '<img src="'.esc_url($avatar_url).'" alt="'.esc_attr($user_name).'">';
		}
		?>
		<li <?php comment_class('comment-entry clearfix '.$empty_avatar); ?>>
			<div class="tu-addcomment" id="comment-<?php comment_ID(); ?>">
				<div class="tu-blogimg">
					<?php if(!empty($avatar)){?><figure><?php echo do_shortcode($avatar); ?> </figure><?php }?>
					<div class="tu-blogcmntinfo">
						<div class="tu-blogcmntinfonames">
							<div class="tu-icondetails"><span><?php echo sprintf( _x( '%s ago', '%s = human-readable time difference', 'tuturn' ), human_time_diff( get_comment_time( 'U' ), current_time( 'timestamp' ) ) ); ?></span></div>
							<div class="tu-comentinfodetail">
								<h4><a href="<?php echo esc_url( $url ); ?>"><?php comment_author(); ?></a></h4>
							</div>
						</div>
						<div class="tuturn-reply">
							<?php comment_reply_link(array_merge($args, array('depth' => $depth, 'max_depth' => $args['max_depth']))); ?>
						</div>
					</div>
				</div>
				<div class="tu-main-description">
					<?php if ($comment->comment_approved == '0') : ?>
						<p class="tuturn-comment-moderation"><?php esc_html_e('Your comment is awaiting moderation.', 'tuturn'); ?></p>
					<?php endif; ?>
					<?php comment_text(); ?>
				</div>
			</div>
			<?php
		}
}

/**
 * comments wrap start
 * @return slug
 */
if (!function_exists('tuturn_comment_form_top')) {
	add_action('comment_form_top', 'tuturn_comment_form_top');

	function tuturn_comment_form_top() {
		// Adjust this to your needs:
		$output = '';
		$output .='<fieldset><div class="tu-themeform__wrap"><div class="form-group-wrap">';

		echo do_shortcode( $output);
	}

}

/**
 * comments wrap start
 * @return slug
 */
if (!function_exists('tuturn_comment_form')) {
	add_action('comment_form', 'tuturn_comment_form');

	function tuturn_comment_form() {
		$output = '';
		$output .= '</div></div></fieldset>';

		echo do_shortcode( $output );
	}

}

/**
 * @count items in array
 * @return {}
 */
if (!function_exists('tuturn_count_items')) {
    function tuturn_count_items($items) {
        if( is_array($items) ){
			return count($items);
		} else{
			return 0;
		}
    }
}

/**
 * Comment field order
 *
 * @throws error
 * @author Amentotech <theamentotech@gmail.com>
 * @return
 */
if (!function_exists('tuturn_comment_fields_custom_order')) {
	add_filter( 'comment_form_fields', 'tuturn_comment_fields_custom_order' );
	function tuturn_comment_fields_custom_order( $fields ) {
		$comment_field			= '';
		$author_field			= '';
		$email_field			= '';
		$url_field				= '';
		$cookies_field			= '';

		if(isset($fields['comment'])){
			$comment_field		= $fields['comment'];
		}
		if(isset($fields['author'])){
			$author_field		= $fields['author'];
		}
		if(isset($fields['email'])){
			$email_field		= $fields['email'];
		}
		if(isset($fields['cookies'])){
			$cookies_field		= $fields['cookies'];
		}
		$comment_field	= $fields['comment'];
		$author_field	= $fields['author'];
		$email_field	= $fields['email'];

		if(isset($fields['url'])){
			$url_field		= $fields['url'];
		}

		$cookies_field	= $fields['cookies'];

		unset( $fields['comment'] );
		unset( $fields['author'] );
		unset( $fields['email'] );
		unset( $fields['url'] );
		unset( $fields['cookies'] );

		// the order of fields is the order below, change it as needed:
		$fields['author']	= $author_field;
		$fields['email']	= $email_field;
		$fields['url']		= $url_field;
		$fields['comment']	= $comment_field;
		$fields['cookies']	= $cookies_field;
		// done ordering, now return the fields:
		return $fields;
	}
}

/**
 * @Change Reply link Class
 * @return sizes
 */
if (!function_exists('tuturn_replace_reply_link_class')) {
    add_filter('comment_reply_link', 'tuturn_replace_reply_link_class');

    function tuturn_replace_reply_link_class($class) {
        $class = str_replace("class='comment-reply-link'", 'class="comment-reply-link tu-theme-btn"', $class);
        return $class;
    }
}

/**
 * @Enqueue admin scripts and styles.
 * @return{}
 */
if (!function_exists('tuturn_admin_enqueue')) {

    function tuturn_admin_enqueue($hook) {
        global $post;
        $protolcol = is_ssl() ? "https" : "http";
        $theme_version = wp_get_theme('tuturn');

        wp_localize_script('tuturn-admin-functions', 'scripts_vars', array(
			'ajax_nonce' 		=> wp_create_nonce('ajax_nonce'),
        ));
    }

    add_action('admin_enqueue_scripts', 'tuturn_admin_enqueue', 10, 1);
}

/**
 * @Theme Editor/guttenberg Style
 * 
 */
if (!function_exists('tuturn_add_editor_styles')) {

    function tuturn_add_editor_styles() {
		global $theme_settings;
		$protocol = is_ssl() ? 'https' : 'http';
        $theme_version = wp_get_theme('tuturn');
		$editor_css  = '';

		$site_colors 	= '#6a307d';
		
		if (!empty($site_colors)) {
			$editor_css  .= 'body.block-editor-page .editor-styles-wrapper a,
			body.block-editor-page .editor-styles-wrapper p a,
			body.block-editor-page .editor-styles-wrapper p a:hover,
			body.block-editor-page .editor-styles-wrapper a:hover,
			body.block-editor-page .editor-styles-wrapper a:focus,
			body.block-editor-page .editor-styles-wrapper a:active{color: #1da1f2;}';
			
			$editor_css  .= 'body.block-editor-page .editor-styles-wrapper blockquote:not(.blockquote-link),
							 body.block-editor-page .editor-styles-wrapper .wp-block-quote.is-style-large,
							 body.block-editor-page .editor-styles-wrapper .wp-block-quote:not(.is-large):not(.is-style-large),
							 body.block-editor-page .editor-styles-wrapper .wp-block-quote.is-style-large,
							 body.block-editor-page .editor-styles-wrapper .wp-block-pullquote, 
							 body.block-editor-page .editor-styles-wrapper .wp-block-quote, 
							 body.block-editor-page .editor-styles-wrapper .wp-block-quote:not(.is-large):not(.is-style-large),
							 body.block-editor-page .wp-block-pullquote, 
							 body.block-editor-page .wp-block-quote, 
							 body.block-editor-page .wp-block-verse, 
							 body.block-editor-page .wp-block-quote:not(.is-large):not(.is-style-large){border-color:'.$site_colors.';}';
		}
		
		$font_families	= array();
		$font_families[] = 'Outfit:400,500,600,700';
		$font_families[] = 'Open+Sans:400,600';
		$font_families[] = 'Gochi+Hand';	
		
		 $query_args = array (
			 'family' => implode('%7C' , $font_families) ,
			 'subset' => 'latin,latin-ext' ,
        );

        $theme_fonts = add_query_arg($query_args , $protocol.'://fonts.googleapis.com/css');
		add_editor_style(esc_url_raw($theme_fonts));
		
		wp_enqueue_style('tuturn-admin-google-fonts' , esc_url_raw($theme_fonts), array () , null);
		
		$editor_css .= "
		body.block-editor-page editor-post-title__input,
		body.block-editor-page .wp-block.wp-block-post-title
		{font: 600 1.75rem/1.3571428571em 'Outfit', sans-serif;}";
		
		$editor_css .= "body.block-editor-page .editor-styles-wrapper{font:400 1rem/1.625em 'Open Sans', sans-serif}";
		
		$editor_css .= "body.block-editor-page .editor-styles-wrapper{color: #1c1c1c;}";
		$editor_css .= "body.block-editor-page editor-post-title__input,
		body.block-editor-page .editor-post-title__block .editor-post-title__input,
		body.block-editor-page .editor-styles-wrapper h1, 
				body.block-editor-page .editor-styles-wrapper h2, 
				body.block-editor-page .editor-styles-wrapper h3, 
				body.block-editor-page .editor-styles-wrapper h4, 
				body.block-editor-page .editor-styles-wrapper h5, 
				body.block-editor-page .editor-styles-wrapper h6 {font-family: 'Outfit', sans-serif}";
							   
		wp_enqueue_style( 'theme-editor-style', get_template_directory_uri() . '/admin/css/theme-editor-style.css', array(), $theme_version->get('Version'));
		wp_add_inline_style( 'theme-editor-style', $editor_css );
		
    }

    add_action('enqueue_block_editor_assets', 'tuturn_add_editor_styles');
}

/**
 * search type
 * @return slug
 */
if (!function_exists('tuturn_custom_body_class')) {
	add_filter( 'body_class', 'tuturn_custom_body_class' );
	function tuturn_custom_body_class( $classes ) {
		global $tuturn_settings;
		$page_id 	= get_the_ID();
		$pages		= !empty($tuturn_settings['tb_dark_header']) ? $tuturn_settings['tb_dark_header'] : array();
		if ( is_page() && !empty($pages) && in_array($page_id,$pages) ) {
			$classes[] = 'tu-mainbodydark';
		}
		return $classes;
	}
}

/**
 * @get post thumbnail
 * @return thumbnail url
 */
if (!function_exists('tuturn_prepare_image_source')) {

	function tuturn_prepare_image_source($image_id, $width = '300', $height = '300') {
		$thumb_url = wp_get_attachment_image_src($image_id, array($width, $height), true);
		
		if ($thumb_url[1] == $width and $thumb_url[2] == $height) {
			return !empty($thumb_url[0]) ? $thumb_url[0] : '';
		} else {
			$thumb_url = wp_get_attachment_image_src($image_id, 'full', true);
			return !empty($thumb_url[0]) ? $thumb_url[0] : '';
		}
		
	}
}