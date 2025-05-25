<?php
/**
 * Template part for displaying related articles on single.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Tuturn
 */
$tuturn_args = array(
    'post_type'     => 'post',
    'posts_per_page' => 3,
    'post__not_in'  => array(get_the_ID()),
    'category__in' => wp_get_post_categories($post->ID), 
);  
$tuturn_related_posts    = new \WP_Query(apply_filters('tuturn_related_post_args', $tuturn_args)); 
$total_posts  = $tuturn_related_posts->found_posts; 
if($tuturn_related_posts->have_posts()){?>
    <div class="tu-relatedatricles">
        <div class="tu-blogtitle">
            <h3><?php esc_html_e('Explore related articles','tuturn')?> </h3>
        </div>
        <div class="row">
            <?php  while ($tuturn_related_posts->have_posts()) {
                $tuturn_related_posts->the_post();
                $image_id           = get_post_thumbnail_id($post->ID);
                $post_img_url       = tuturn_prepare_image_source($image_id, 612, 400);
                $post_url           = !empty($post->ID) ? get_the_permalink($post->ID) : '';
                $author_id          = get_post_field ('post_author', $post->ID);
                $author_name        = get_the_author_meta( 'display_name' , $author_id ); 
                $term_list          = wp_get_post_terms($post->ID, 'category', array("fields" => "all"));
                $post_date          =  date_i18n(get_option('date_format'), strtotime(get_the_date())) ;
                ?>
                <div id="post-<?php the_ID(); ?>" <?php post_class('col-md-6 col-xxl-4'); ?>>
                    <div class="tu-articleitem">
                        <?php if(!empty($post_img_url)){?>
                            <figure>
                                <img src="<?php echo esc_url($post_img_url)?>" alt="<?php echo esc_attr(get_the_title())?>">
                            </figure>
                        <?php } ?>
                        <div class="tu-articleinfo">                            	
                            <?php echo get_the_term_list( $post->ID, 'category', '<ul class="tu-taglinks tu-taglinksm"><li>', '</li><li>', '</li></ul>'); ?>
                            <div class="tu-arrticltitle">
                                <h5><a href="<?php the_permalink();?>"><?php echo esc_html(get_the_title());?></a></h5>
                            </div>
                            <ul class="tu-articleauth">
                                <li class="tu-articleauthor">
                                    <i class="icon icon-message-square"></i>
				                    <span><?php comments_number(esc_html__('0 Comments' , 'tuturn') , esc_html__('1 Comment' , 'tuturn') , esc_html__('% Comments' , 'tuturn')); ?></span>
                                </li>
                                <li>
                                    <i class="icon icon-calendar"></i>
                                    <span><?php echo esc_html($post_date); ?></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php }?>
        </div>
    </div>
    <?php
}
wp_reset_postdata();
