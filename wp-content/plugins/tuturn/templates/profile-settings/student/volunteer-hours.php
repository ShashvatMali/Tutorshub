<?php
/**
 * Volunteer hours listing
 *
 * @package     Tuturn
 * @subpackage  Tuturn/templates/dashboard/provider
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
*/
global $tuturn_settings;
if (!empty($args) && is_array($args)) {
	extract($args);
}
$fileImage 		= TUTURN_GlobalSettings::get_plugin_url().'public/images/file.svg';
$day_time       = apply_filters('tuturnAppointmentTime','');
$time_format    = get_option('time_format');
$posts_per_page = get_option('posts_per_page') ? get_option('posts_per_page') : 10;
$pg_page        = get_query_var('page') ? get_query_var('page') : 1;
$pg_paged       = get_query_var('paged') ? get_query_var('paged') : 1;
$paged          = max($pg_page, $pg_paged);
$status         = !empty($_GET['sort_by']) ? $_GET['sort_by'] : 'any';
$hours_array    = tuturn_hours_data_by_meta(array(array('key' => 'student_id', 'value' => $user_identity )));
$tab    = !empty($_GET['tab']) ? esc_html($_GET['tab']) : '';
$args   = array(
    'posts_per_page' => $posts_per_page,
    'paged'          => $paged,
    'post_type'      => 'volunteer-hours',
    'post_status'    => $status,
    'orderby'        => 'date',
    'meta_query'     => array(
        array(
            'key'       => 'student_id',
            'value'     => $user_identity,
            'compare'   => '=',
        )
    )
);
wp_enqueue_style( 'tuturn-lightbox');
wp_enqueue_script( 'tuturn-lightbox');
$list_hours     = new WP_Query($args);
$total_posts    = $list_hours->found_posts;
$date_format    = get_option('date_format');
?>
<div class="tu-profilewrapper">
    <div class="tu-content-box tu-content-boxv2">
        <div class="tu-main-title">
            <h3><?php esc_html_e('Tutoring hour log','tuturn');?></h3>
        </div>
        <form id="booking-search-form" action="<?php echo esc_url($current_page_link); ?>">
            <input type="hidden" name="tab" value="<?php echo esc_html($tab); ?>">
            <div class="tu-selectwrapper">
                <span><?php esc_html_e('Sort by', 'tuturn'); ?>:</span>
                <div class="tu-selectv">
                    <select name="sort_by" id="tu-sortyby_booking" data-placeholder="<?php esc_attr_e('All', 'tuturn'); ?>" class="form-control tu-booking-search-field booking-sort-by">
                        <option value="all" <?php if (!empty($status) && $status == 'any') { echo esc_attr('selected');} ?>><?php esc_html_e('All status', 'tuturn'); ?></option>
                        <option value="pending" <?php if (!empty($status) && $status == 'pending') { echo esc_attr('selected');} ?>><?php esc_html_e('Pending', 'tuturn'); ?></option>
                        <option value="publish" <?php if (!empty($status) && $status == 'publish') { echo esc_attr('selected'); } ?>><?php esc_html_e('Approved', 'tuturn'); ?></option>
                        <option value="decline" <?php if (!empty($status) && $status == 'decline') { echo esc_attr('selected'); } ?>><?php esc_html_e('Declined', 'tuturn'); ?></option>
                    </select>
                </div>
            </div>
        </form> 
    </div>
    <ul class="tu-hours-status">
        <?php if( isset($hours_array['total']) ){?>
            <li>
                <div class="tu-hours-status-items tu-listinginfo">
                    <span class="tu-total-hours"><i class="icon icon-clock"></i></span>
                    <p><?php esc_html_e('Total hours','tuturn');?></p>
                    <h5><?php echo sprintf( esc_html__('%s hrs','tuturn'),$hours_array['total']);?></h5>
                </div>
            </li>
        <?php } ?>
        <?php if( isset($hours_array['completed']) ){?>
            <li>
                <div class="tu-hours-status-items tu-listinginfo">
                    <span class="tu-approved-hours"><i class="icon icon-check-circle"></i></span>
                    <p><?php esc_html_e('Approved hours','tuturn');?></p>
                    <h5><?php echo sprintf( esc_html__('%s hrs','tuturn'),$hours_array['completed']);?></h5>
                </div>
            </li>
        <?php } ?>
        <?php if( isset($hours_array['pending']) ){?>
            <li>
                <div class="tu-hours-status-items tu-listinginfo">
                    <span class="tu-pending-hours"><i class="icon icon-pie-chart"></i></span>
                    <p><?php esc_html_e('Pending/decline hours','tuturn');?></p>
                    <h5><?php echo sprintf( esc_html__('%s hrs','tuturn'),$hours_array['pending']);?></h5>
                </div>
            </li>
        <?php } ?>
    </ul>
    <?php
        if ($list_hours->have_posts()) { ?>
            <div class="tu-listinginfo-holder tu-listinginfo-holderv2">
                <?php
                    while ($list_hours->have_posts()) {
                        $list_hours->the_post();
                        global $post;
                        $hourly_data        = get_post_meta($post->ID,'hourly_data',true );
                        $instructor_id      = get_post_meta($post->ID,'instructor_id',true );
                        $instructor_proflie = !empty($instructor_id) ? get_user_meta($instructor_id,'_linked_profile',true ) : 0;
                        $avatar_url         = apply_filters(
                            'tuturn_avatar_fallback', tuturn_get_user_avatar(array('width' => 100, 'height' => 100), $instructor_proflie), array('width' => 100, 'height' => 100)
                        );
                        $user_name          = tuturn_get_username($instructor_proflie);
                        $is_verified        = get_post_meta($instructor_proflie, '_is_verified', true);
                        $is_verified        = !empty($is_verified) ? $is_verified : '';
                        $instructor_link    = get_the_permalink($instructor_proflie);
                        $hourly_date        = !empty($hourly_data['date']) ? ($hourly_data['date']) : 0; 
                        $hourly_date        = date_i18n($date_format, strtotime($hourly_date));
                        $start_time         = !empty($hourly_data['start_time']) ? date_i18n($time_format,strtotime($hourly_data['start_time'])) : 0; 
                        $end_time           = !empty($hourly_data['end_time']) ? date_i18n($time_format,strtotime($hourly_data['end_time'])) : 0; 
                        $total_hours        = isset($hourly_data['total_hours']) ? $hourly_data['total_hours'] : 0;
                        $post_status        = !empty($post->post_status) ? $post->post_status : '';

                        ?>
                        <div class="tu-listinginfo">
                             <div class="tu-listinginfo_wrapper">
                                <div class="tu-listinginfo_title">
                                    <div class="tu-listinginfo-img">
                                        <?php if( !empty($avatar_url) ){?>
                                            <figure>
                                                <img src="<?php echo esc_url($avatar_url)?>" alt="<?php echo esc_attr($user_name) ?>">
                                            </figure>
                                        <?php } ?>
                                        <div class="tu-listing-heading">
                                            <?php do_action('tuturn_hourly_post_status',$post_status );?>
                                            <?php if( !empty($user_name) ){?>
                                                <h5>
                                                    <a href="<?php echo esc_url($instructor_link);?>"><?php echo esc_html($user_name);?></a>
                                                    <?php if (!empty($is_verified) && $is_verified == 'yes') { ?>
                                                        <i class="icon icon-check-circle tu-greenclr"></i>
                                                    <?php } ?>
                                                </h5>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <?php if(isset($total_hours) || !empty($hourly_date)){?>
                                        <div class="tu-listinginfo_price">
                                            <?php if($total_hours){?>
                                                <h4><?php echo sprintf( esc_html__('%s Hrs','tuturn'),$total_hours);?></h4>
                                            <?php } ?>
                                            <?php if(!empty($start_time)){?><span class="tu-date"><?php esc_html_e('Start time','tuturn');?>: <em><?php echo esc_html($start_time);?></em></span><?php } ?>
                                            <?php if(!empty($end_time)){?><span class="tu-date"><?php esc_html_e('End time','tuturn');?>: <em><?php echo esc_html($end_time);?></em></span><?php } ?>
                                            
                                            <?php if( !empty($hourly_date) ){?>
                                                <span class="tu-date"><?php esc_html_e('Date','tuturn');?>: <em><?php echo esc_html($hourly_date);?></em></span>
                                            <?php } ?>
                                            <?php 
                                            if( !empty($hourly_data['documents']['attachments']) ){
                                                $download_html      = '';                            
                                                $download_html .= '<ul class="attachment-wrapper">';
                                                foreach($hourly_data['documents']['attachments'] as $at_key => $at_val){                                                                
                                                    $filetype       = wp_check_filetype($at_val['name']);
                                                    $allowed_types	= array('png','jpg','jpeg','gif','jfif');                            
                                                    if(!empty($filetype['ext']) && in_array($filetype['ext'],$allowed_types)){
                                                        $attachment_id = $at_val['attachment_id'];                                                    
                                                        $download_html .= '<li class="tu-attachment-listing tu-user-img"><a data-lightbox="example-'.$post->ID.'" data-title="'.get_the_title($post->ID).'" href="'.$at_val['url'].'">'.wp_get_attachment_image( $attachment_id, 'tu_profile_thumbnail' ).'</a></li>';
                                                    }else{
                                                        $download_html .= '<li class="tu-attachment-listing tu-user-img"><a target="_blank" href="'.$at_val['url'].'"><i class="icon icon-file-text"></i></a></li>';
                                                    }                                                
                                                }
                                                $download_html .= '</ul>';
                                                echo do_shortcode($download_html);
                                            }
                                            ?>
                                        </div>
                                    <?php } ?>
                                </div>
                                <?php if( !empty($post->post_title) ){?>
                                    <div class="tu-listinginfo-head">
                                        <h6><?php echo esc_html($post->post_title);?></h6>
                                    </div>
                                <?php } ?>
                                <?php if( !empty($post->post_content) ){?>
                                    <div class="tu-comment_wrap tu-readarticle">
                                        <div class="tu-listinginfo_description">
                                            <p><?php echo do_shortcode($post->post_content ); ?></p>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php if( !empty($hourly_data['decline_reason']) ){?>
                                    <div class="tu-boxitem tu-boxitemv2">
                                        <div class="tu-alertcontent">
                                            <h5><?php esc_html_e('Incorrect hours mentioned in the log','tuturn');?></h5>
                                            <p><?php echo esc_html($hourly_data['decline_reason']);?></p>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="tu-listinginfo_btn">
                                <div class="tu-btnarea">
                                    <?php if( !empty($hourly_data['documents']['attachments']) ){?>
                                        <a href="javascript:" class="tu-secbtn tu_download_zip_file" data-post_id="<?php echo intval($post->ID);?>"><?php esc_html_e('Attachments','tuturn');?><i class="icon icon-download"></i></a>
                                    <?php } ?>
                                    <?php if( !empty($post_status) && $post_status == 'pending' ){?>
                                        <a href="javascript:;" class="tu-primbtn tu-primgreen-btn btn-hours-approve" data-post_id="<?php echo intval($post->ID);?>"><?php esc_html_e('Approve','tuturn');?></a>
                                        <a href="javascript:;" class="tu-secbtn btn-hours-decline" data-post_id="<?php echo intval($post->ID);?>"><?php esc_html_e('Decline','tuturn');?></a>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                <?php } ?>
            </div>
            <?php
                if( !empty($total_posts)){
                    tuturn_paginate($list_hours,'tu-pagination'); 
                } 
            ?>
    <?php } else { ?>
        <div class="tu-bookings tu-booking-epmty-field">
            <h4><?php esc_html_e('Uh ho!', 'tuturn'); ?></h4>
            <p><?php esc_html_e('We\'re sorry but there is no volunteer hours log', 'tuturn'); ?></p>
        </div>
    <?php } ?>
</div>
<div class="modal fade" id="tu-send-decline" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5><?php esc_html_e('Decline hours','tuturn');?></h5>
            <a href="javascript:void(0);" class="tu-close" data-bs-dismiss="modal" aria-label="Close"><i class="icon icon-x"></i></a>
        </div>
        <div class="modal-body">
            <form class="tu-themeform" id="tu_hour_decline">
                <fieldset>
                    <div class="tu-themeform__wrap">
                        <div class="form-group">
                            <label class="tu-label"><?php esc_html_e('Decline reason','tuturn');?></label>
                            <div class="tu-placeholderholder">
                                <textarea name="decline_reason" id="tu_decline_reason" class="form-control" required="" placeholder="<?php esc_attr_e('Add decline reason','tuturn');?>"> </textarea>
                            </div>
                        </div>
                        <input type="hidden" name="post_id" value="" id="tu-declinepost-id">
                        <div class="form-group tu-formbtn">
                            <a href="javascript:;" class="tu-primbtn-lg tu-postdecline-btn"><?php esc_html_e('Submit','tuturn');?></a>
                        </div>
                    </div>
                </fieldset>
            </form>
        </div>
        </div>
    </div>
</div>
<?php
$script = "

jQuery(document).on('ready', function(){
    var tu_replywrap = jQuery('.tu-readarticle');
    tu_replywrap.readmore({
    speed: 50,
    collapsedHeight: 20,
    moreLink: '<a class=\"tu-readmore\" href=\"#\"><span class=\"tu-read\"> ".esc_html__('Read full details','tuturn')." <i class=\"icon icon-chevron-down\"></i></span></a>',
    lessLink: '<a class=\"tu-readmore tl-hidequs\" href=\"#\">".esc_html__('Hide details','tuturn')."<i class=\"icon icon-chevron-up\"></i></a>',
    });
});
";
wp_enqueue_script('readmore');
wp_add_inline_script('tuturn-profile-settings', $script, 'after');