<?php
/**
 * Template Name: Search students
 *
 * @package     Tuturn
 * @subpackage  Tuturn/templates
 * @author      Amentotech <info@amentotech.com>
 * @link        https://themeforest.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
 */
global $tuturn_settings;
get_header();
$meta_query_args = $meta_query_args2 = array();
$pg_page            = get_query_var('page') ? get_query_var('page') : 1; //rewrite the global var
$pg_paged           = get_query_var('paged') ? get_query_var('paged') : 1;
$paged              = max($pg_page, $pg_paged);
$search_keyword     = !empty($_GET['keyword']) ? esc_html($_GET['keyword']) : '';
$base_url           = get_the_permalink();
$per_page           = get_option('posts_per_page');
$meta_query_args[]  = array(
	'key' 		=> '_is_verified',
	'value' 	=> 'yes',
	'compare' 	=> '='
);

$query_args = array(
    'posts_per_page'        => $per_page,
    'paged'                 => $paged,
    'post_type'             => 'tuturn-student',
    'post_status'           => 'publish',
    'ignore_sticky_posts'   => 1,
    'orderby'               => 'date',
    'order'                 => 'DESC',
);

// if keyword field is set in search then append its args in $query_args
if (!empty($search_keyword)) {
    $query_args['s'] = $search_keyword;
}

//Meta Query
if (!empty($meta_query_args)) {
    $query_relation           = array('relation' => 'AND',);
    $meta_query_args          = array_merge($query_relation, $meta_query_args);
    $query_args['meta_query'] = $meta_query_args;
}

$instructors_data = new WP_Query(apply_filters('tuturn_students_search_filter', $query_args));
$total_posts = $instructors_data->found_posts;
?>
<section class="tu-main-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="tu-listing-wrapper">
                    <div class="tu-sort">
                        <div class="tu-searchbar-wrapper">
                            <div class="tu-appendinput">
                                <form id="tu-instructor-keyword-search-form" action="<?php echo esc_url($base_url)?>" method="GET" class="tu-searcbar" >
                                    <div class="tu-inputicon">
                                        <a href="javascript:void(0);"><i class="icon icon-search"></i></a>
                                        <input name="keyword" type="text" class="form-control" value="<?php echo esc_attr($search_keyword);?>"  placeholder="<?php esc_attr_e('What are you looking for?','tuturn')?>">
                                    </div>
                                    <a href="javascript:void(0);" class="tu-primbtn-lg tu-primbtn-orange" id="tu-instructor-search-keyword"><?php esc_html_e('Search now','tuturn')?></a>
                                </form>
                            </div>
                            <div class="tu-listing-search">
                                <figure>
                                    <img src="<?php echo esc_url(TUTURN_DIRECTORY_URI . 'public/images/shape.png')?>" alt="<?php esc_attr_e('image','tuturn')?>">
                                </figure>
                                <span><?php esc_html_e('Start from here','tuturn')?> </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>            
            <div class="col-xl-12 col-xxl-12">
                <div class="tu-listinginfo-holder">
                    <?php if ($instructors_data->have_posts()) {

                        while ($instructors_data->have_posts()) {
                            $instructors_data->the_post();
                            global $post;
                            ?>
                            <div class="tu-listinginfo tu-listinginfovthree">                                
                                <div class="tu-listinginfo_wrapper">
                                    <div class="tu-listinginfo_title">
                                        <div class="tu-listinginfo-img">
                                            <?php do_action('tuturn_instructor_image', $post); ?>
                                            <div class="tu-listing-heading">
                                                <?php do_action('tuturn_instructor_title', $post); ?>
                                                <?php if(!empty($location)) {?>
                                                    <div class="tu-listing-location">                                                        
                                                        </span><address><i class="icon icon-map-pin"></i><?php echo esc_html($location)?></address>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php do_action('tuturn_instructor_short_description', $post); ?>
                                    <?php 
                                    $teaching_preference    = get_post_meta( $post->ID,'teaching_preference',true );
                                    if(!empty($teaching_preference)){?>
                                        <div class="tu-instructors_service">
                                            <p> <?php esc_html_e('I would like to get teaching service direct at','tuturn'); ?> </p>
                                            <?php if(!empty($teaching_preference)){?>
                                                <ul class="tu-instructors_service-list"> 
                                                    <?php for($i=0;$i<count($teaching_preference);$i++) {
                                                        $preference = $teaching_preference[$i];
                                                        if($preference  === 'home'){ ?>
                                                            <li><i class="icon icon-home tu-greenclr"></i><span><?php  esc_html_e('My home','tuturn')?></span></li>
                                                        <?php }   
                                                        if($preference  === 'student_home'){?>
                                                            <li><i class="icon icon-map-pin tu-blueclr"></i><span><?php esc_html_e('Student\'s home', 'tuturn')?> </span></li>
                                                        <?php }
                                                        if($preference  === 'online'){?>
                                                            <li><i class="icon icon-video tu-orangeclr"></i><span><?php esc_html_e('Online','tuturn')?> </span></li>
                                                        <?php } 
                                                    }?>
                                                </ul>
                                            <?php } ?>
                                        </div>
                                        <?php 
                                    }?>
                                </div>
                                <div class="tu-listinginfo_btn">
                                    <div class="tu-iconheart">
                                        <?php if(apply_filters( 'tuturn_chat_solution_guppy',false ) === true){
                                            if(is_user_logged_in()){
                                                $tuturn_inbox_url   = apply_filters('tuturn_guppy_inbox_url', $post->ID);
                                                $chat_class         = 'wpguppy_start_chat';
                                                $chat_with          = $post->ID;
                                            } else {
                                                $tuturn_inbox_url   = tuturn_get_page_uri('login');
                                                $chat_class         = '';
                                                $chat_with          = '';
                                            }?>
                                            <a href="<?php echo esc_url($tuturn_inbox_url);?>" data-receiver_id="<?php echo esc_attr($chat_with);?>" class="tu-secbtn <?php echo esc_attr($chat_class);?>"><?php esc_html_e('Let’s chat','tuturn')?> </a>
                                        <?php } ?>
                                    </div>
                                    <div class="tu-btnarea">
                                        
                                        <a href="<?php echo esc_url(get_permalink()); ?>" class="tu-primbtn"><?php esc_html_e('View full profile','tuturn')?> </a>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                        if( !empty($total_posts)){
                            tuturn_paginate($instructors_data,'tu-pagination'); 
                        }
                    } else {
                        tuturn_get_template( 'single-tuturn-student/empty-record.php');                
                    } ?> 
                </div>
            </div>
        </div>
    </div>
</section>
<?php 
get_footer();
