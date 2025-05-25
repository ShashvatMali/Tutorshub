<?php 
/**
 * Instructor profile reviews
 *
 * @package     Tuturn
 * @subpackage  Tuturn/Templates/Single_tuturn_Instructor
 * @author      Amentotech <info@amentotech.com>
 * @link       https://themeforest.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
*/
global $post, $current_user;
$total_comments	= get_comments(array('post_id' => $post->ID));
$total_comments	= !empty($total_comments) ? count($total_comments) : 0;?>
<div class="tu-tabswrapper">
    <div class="tu-boxtitle">
        <h4><?php esc_html_e('Reviews', 'tuturn');?>(<?php echo intval($total_comments);?>)</h4>
    </div>
    <?php    
    $paged              = !empty($_GET['comment_page']) ? $_GET['comment_page'] : 1;
    $comments_per_page  = get_option( 'comments_per_page' );
    $args = array(
        'number'    => $comments_per_page,
        'post_type' => array( 'tuturn-instructor' ),
        'paged'     => $paged,
        'parent'    => 0,
        'post_id '  => $post->ID,
    );
    $pages 			= ceil($total_comments/$comments_per_page);
    $offset 		= ($paged * $comments_per_page) - $comments_per_page;
    $args 			= array ( 'post_id' => $post->ID,'offset'=> $offset,'number'=> $comments_per_page);
    $comments 		= get_comments( $args );
    ?>
    <div class="tu-commentarea">
        <?php if ( $comments ) { 
            foreach ( $comments as $comment ) {
                $post_author_id = $comment->user_id;
                $user_name      = $comment->comment_author;
                $rating         = get_comment_meta($comment->comment_ID, 'rating', true);
                $rating         = !empty($rating) ? $rating : 0;
                $url            = get_author_posts_url($post_author_id);
                $rating_percentage  = $rating * 20;
                $empty_avatar = '';
                if(empty(get_avatar($comment->comment_ID, 100))){
                    $empty_avatar = 'tu-empty-avatar';
                }

                $user_profile_id    = tuturn_get_linked_profile_id($post_author_id);
               
                if(!empty($user_profile_id)){
                    $avatar_url         = apply_filters(
                        'tuturn_avatar_fallback', tuturn_get_user_avatar(array('width' => 100, 'height' => 100), $user_profile_id), array('width' => 100, 'height' => 100)
                    );
                    $user_name  = tuturn_get_username($user_profile_id);
                    $avatar = '<img src="'.esc_url($avatar_url).'" alt="'.esc_attr($user_name).'">';
                } else {
                    $avatar = get_avatar($comment->comment_ID, 100);
                    $author_obj = get_user_by('id', $post_author_id);

                    if(!empty($author_obj->display_name)){
                        $user_name  = $author_obj->display_name;
                    }
                }

                $comment_date = mysql2date( 'U', $comment->comment_date, true );              
                ?>
                <div class="tu-commentlist" <?php comment_class('tu-commentlist '.$empty_avatar); ?> id="comment-<?php echo intval($comment->comment_ID); ?>">
                    <?php if(!empty($avatar)){?>
                        <figure>
                            <?php echo do_shortcode($avatar);?>
                        </figure>
                    <?php }?>                   
                    <div class="tu-coomentareaauth">
                        <div class="tu-commentright">
                            <div class="tu-commentauthor">
                                <h6><span><?php echo esc_html($user_name); ?></span> <em><?php echo sprintf( _x( '%s ago', '%s = human-readable time difference', 'tuturn' ), human_time_diff( $comment_date, current_time( 'timestamp' ) ) ); ?></em></h6>
                                <div class="tu-listing-location tu-ratingstars">
                                    <span><?php echo number_format((float)$rating, 1, '.', '');?></span>
                                    <span class="tu-stars tu-sm-stars" style="width: <?php echo intval($rating_percentage);?>%">
                                        <span></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="tu-description">
                            <?php echo apply_filters('the_content', $comment->comment_content);?>
                        </div>
                    </div>
                </div>
                <?php 
            }

            if ( $total_comments >  $comments_per_page) {
                $pagination_arg = array(
                    'base'         => @add_query_arg('comment_page','%#%'),
                    'format'       => '?comment_page=%#%',
                    'total'        => $pages,
                    'current'      => $paged,
                    'show_all'     => false,
                    'end_size'     => 1,
                    'mid_size'     => 2,
                    'prev_next'    => true,
                    'prev_text'    => esc_html__('Prev','tuturn'),
                    'next_text'    => esc_html__('Nex','tuturn'),
                    'type'         => 'list'
                );
                echo paginate_links( $pagination_arg );                
            }
        }
       ?>
    </div>
</div>