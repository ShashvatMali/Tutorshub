<?php
/**
 *
 * Comments Page
 *
 * @package   Tuturn
 * @author    Amentotech
 * @link      https://themeforest.net/user/amentotech/portfolio
 * @version 1.0
 * @since 1.0
 */
global $current_user;
if (post_password_required()) {
    return;
}

if (have_comments()) { ?>
	<div id="tuturn-comments" class="tuturn-comments tu-theme-box tu-boxlg">
		<div class="tu-boxtitle"><h4><?php comments_number(esc_html__('0 Comments' , 'tuturn') , esc_html__('1 Comment' , 'tuturn') , esc_html__('% Comments' , 'tuturn')); ?></h4></div>
		<ul class="tu-commentarea"><?php wp_list_comments(array ('callback' => 'tuturn_comments' ));?></ul>
		<?php if (get_comment_pages_count() > 1 && get_option('page_comments')) : ?>
			<div class="tuturn-haslayout tuturn-comments-paginate">
				<?php if (get_comment_pages_count() > 1 && get_option('page_comments')) : ?>
					<span class="tuturn-comment-prev"><?php previous_comments_link(esc_html__('&larr; Older comments', 'tuturn')); ?></span>
					<span class="tuturn-comment-next"><?php next_comments_link(esc_html__('Newer comments &rarr;', 'tuturn')); ?></span>
				<?php endif; ?>
			</div>
		<?php endif;?>
	</div>	
<?php } ?>

<?php if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) {?>
		<div class="tuturn-comments-closed"><?php esc_html_e('Comments are closed.', 'tuturn');?></div>
<?php }?>

<?php if ( comments_open() ) {
	$userType		= apply_filters('tuturnGetUserType', $current_user->ID );
	$profile_url	= admin_url( "profile.php" );

	if($userType == 'instructor' || $userType == 'student'){
		if(function_exists('tuturn_get_page_uri')){
			$profile_url	= tuturn_get_page_uri('dashboard');
		}
	} else {
		$profile_url	= admin_url( "profile.php" );
	}
	$comments_args = array(
		'must_log_in'			=> '<div class="form-group"><p class="tuturn-must-log-in">' .  sprintf( __( "You must be %slogged in%s to post a comment.", 'tuturn' ), '<a href="'.esc_url(wp_login_url( apply_filters( 'the_permalink', get_permalink( ) ) )).'">', '</a>' ) . '</p></div>',
		'logged_in_as'			=> '<div class="form-group"><p class="tuturn-logged-in-as">' . esc_html__( "Logged in as",'tuturn' ).' <a href="' .esc_url( $profile_url ).'">'.$user_identity.'</a>. <a href="' .esc_url(wp_logout_url(get_permalink())).'" title="' . esc_attr__("Log out of this account", 'tuturn').'">'. esc_html__("Log out &raquo;", 'tuturn').'</a></p></div>',
		'fields' 				=> apply_filters( 'tuturn_default_comment_fields', array(
			'author'	=> '<div class="form-group  form-group-half"><label class="tu-label">'. esc_html__('Full Name', 'tuturn').'</label><div class="tu-placeholderholder"><input type="text" name="author" id="author" value="'. esc_attr( $commenter['comment_author'] ) .'" placeholder="'. esc_attr__("Your name (required)", 'tuturn').'" size="22" tabindex="1" aria-required="true" class="form-control" /></div></div>',
			'email'		=> '<div class="form-group  form-group-half"><label class="tu-label">'. esc_html__('Email address', 'tuturn').'</label><div class="tu-placeholderholder"><input type="text" name="email" id="email" value="'. esc_attr( $commenter['comment_author_email'] ) .'" placeholder="'. esc_attr__("Your email (required)", 'tuturn').'" size="22" tabindex="2" aria-required="true" class="form-control"  /></div></div>',
			'url'		=> '<div class="form-group"><label class="tu-label">'. esc_html__('Website', 'tuturn').'</label><div class="tu-placeholderholder"><input type="text" name="url" id="url" value="'. esc_attr( $commenter['comment_author_url'] ) .'" placeholder="'. esc_attr__("Website", 'tuturn').'" size="22" tabindex="3" class="form-control" /></div></div>'
		)),
		'comment_field'			=> '<div class="form-group"><label class="tu-label">'. esc_html__('Description', 'tuturn').'</label><div class="tu-placeholderholder"><textarea name="comment" id="comment" cols="39" rows="5" tabindex="4" class="form-control" placeholder="'. esc_attr__("Type your comment", 'tuturn').'"></textarea></div></div>',
		'notes'                	=> '' ,
		'comment_notes_before' 	=> '' ,
		'comment_notes_after'  	=> '' ,
		'id_form'              	=> 'tu-themeform' ,
		'id_submit'            	=> 'tuturn-formtheme' ,
		'class_form'           	=> 'tu-themeform' ,
		'class_submit'         	=> 'tu-theme-btn',
		'class_container'      	=> 'comment-respond tu-theme-box',
		'name_submit'          	=> 'submit' ,
		'title_reply'          	=> esc_html__('Leave your comment' , 'tuturn') ,
		'title_reply_to'       	=> esc_html__('Leave a reply to %s' , 'tuturn') ,
		'title_reply_before'   	=> '<div class="tu-boxtitle"><h4>' ,
		'title_reply_after'    	=> '</h4></div>' ,
		'cancel_reply_before'  	=> '' ,
		'cancel_reply_after'   	=> '' ,
		'cancel_reply_link'    	=> esc_html__('Cancel reply' , 'tuturn') ,
		'label_submit'        	=> esc_html__('Post comment' , 'tuturn') ,
		'submit_button'        	=> '<div class="form-group"><button name="%1$s" type="submit" id="%2$s" class="tu-theme-btn" value="%4$s"> '.esc_html__( 'Post comment', 'tuturn' ).'</button></div>' ,
		'submit_field'         	=> ' %1$s %2$s ' ,
		'format'               	=> 'xhtml' ,
	);
	comment_form($comments_args);	
}
