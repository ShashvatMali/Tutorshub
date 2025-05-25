<?php
/**
 *
 * Related instructors 
 *
 * @package     Tuturn
 * @subpackage  Tuturn/Templates/Single_tuturn_Instructor
 * @author      Amentotech <info@amentotech.com>
 * @link        https://themeforest.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
*/
global $post,$tuturn_settings;
$meta_query_args    = array();
$identity_verification_listings = !empty($tuturn_settings['identity_verification_listings']) ?  $tuturn_settings['identity_verification_listings'] : false;
$identity_verification          = !empty($tuturn_settings['identity_verification']) ?  $tuturn_settings['identity_verification'] : 'none';


$args = array(
    'post_type' => 'tuturn-instructor',
    'posts_per_page' => 3,
    'post_status' => 'publish',
    'post__not_in' => array( $post->ID ),
    'orderby' => 'date',
);
if(!empty($identity_verification_listings) && !empty($identity_verification) && ( $identity_verification == 'tutors' || $identity_verification == 'both' )){
    $meta_query_args[]  = array(
        'key'       => 'identity_verified',
        'value'     => 'yes',
        'compare'   => '=',
    );   
    $query_relation           = array('relation' => 'AND',);
    $meta_query_args          = array_merge($query_relation, $meta_query_args);
    $args['meta_query'] = $meta_query_args;
}

$related_query = new WP_Query( $args );
if($related_query->have_posts()){
?>
    <div class="row gy-4">
        <div class="col-md-12">
            <div class="tu-explore-title">
                <h3><?php esc_html_e('Explore related tutors', 'tuturn');?></h3>
            </div>
        </div>
        <?php 
        while($related_query->have_posts()){
        $related_query->the_post();
        ?>
        <div class="col-md-6 col-lg-4">
            <?php tuturn_get_template_part( 'instructor-search/instructor-search-view-v4');?>
        </div>
        <?php } ?>
    </div>
<?php
}
