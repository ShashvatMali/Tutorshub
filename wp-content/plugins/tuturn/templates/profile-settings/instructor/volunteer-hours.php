<?php
/**
 * Volunteer hours =
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
$tab            = !empty($_GET['tab']) ? esc_html($_GET['tab']) : '';
$day_time       = apply_filters('tuturnAppointmentTime','');
$time_format    = get_option('time_format');
$posts_per_page = get_option('posts_per_page') ? get_option('posts_per_page') : 10;
$pg_page        = get_query_var('page') ? get_query_var('page') : 1;
$pg_paged       = get_query_var('paged') ? get_query_var('paged') : 1;
$status         = !empty($_GET['sort_by']) ? $_GET['sort_by'] : 'any';
$paged          = max($pg_page, $pg_paged);
$hours_array    = tuturn_hours_data_by_meta(array(array('key' => 'instructor_id', 'value' => $user_identity )));
$tab    = !empty($_GET['tab']) ? esc_html($_GET['tab']) : '';
$args   = array(
    'posts_per_page' => $posts_per_page,
    'paged'          => $paged,
    'post_type'      => 'volunteer-hours',
    'post_status'    => $status,
    'orderby'        => 'date',
    'author__in'     => $user_identity,
);
wp_enqueue_style( 'tuturn-lightbox');
wp_enqueue_script( 'tuturn-lightbox');

$list_hours     = new WP_Query($args);
$total_posts    = $list_hours->found_posts;
$date_format    = get_option('date_format');
?>
<div class="tu-profilewrapper tu-hourlog-wrapper">
    <div class="tu-content-box tu-content-boxv2">
        <div class="tu-main-title">
            <h3><?php esc_html_e('Volunteer hours log','tuturn');?></h3>
        </div>
        <form class="tu-sort-by" id="booking-search-form" action="<?php echo esc_url($current_page_link); ?>">
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
        <form class="tb-themeform tb-displistform" id="tu-logdata-form" method="post">
            <input type="hidden" name="download" value="true">
        </form>
        <ul class="tu-right-side-list">
            <li>
                <a class="tu-add-btn tb-addhourly-form"><?php esc_html_e('Add hour log','tuturn');?><i class="icon icon-plus"></i></a>
            </li>
        </ul>
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
    <?php if ($list_hours->have_posts()) {?>
        <a href="#" class="tu-primbtn-lg tu-primbtn-orange download-csv-log"><?php esc_html_e('Download hour log','tuturn');?>&nbsp;<i class="icon icon-download"></i></a>
    <?php } ?>    
    <?php
        $hourly_data_datails    = array();
        if ($list_hours->have_posts()) {?>
            <div class="tu-listinginfo-holder tu-listinginfo-holderv2">
                <?php
                    while ($list_hours->have_posts()) {
                        $list_hours->the_post();
                        global $post;
                        $hourly_data        = get_post_meta($post->ID,'hourly_data',true );
                        $hourly_data_datails[$post->ID]['data']	    = $hourly_data;
                        $hourly_data_datails[$post->ID]['title']    = $post->post_title;
                        $hourly_data_datails[$post->ID]['content']  = $post->post_content;
                        $student_id         = !empty($hourly_data['student_id']) ? intval($hourly_data['student_id']) : 0; 
                        $student_proflie    = !empty($student_id) ? get_user_meta($student_id,'_linked_profile',true ) : 0;
                        $avatar_url         = apply_filters(
                            'tuturn_avatar_fallback', tuturn_get_user_avatar(array('width' => 100, 'height' => 100), $student_proflie), array('width' => 100, 'height' => 100)
                        );
                        
                        $user_name          = tuturn_get_username($student_proflie);
                        $avatar_html        = !empty($avatar_url) ? '<img src="'.esc_url($avatar_url).'" atl="'.esc_attr($user_name).'"/>' : '';
                        $hourly_data_datails[$post->ID]['user_html']  = $avatar_html.'<span>'.$user_name.'</span>';

                        $hourly_data_datails[$post->ID]['user_class']  = 'tu-valid';

                        $is_verified        = get_post_meta($student_proflie, '_is_verified', true);
                        $is_verified        = !empty($is_verified) ? $is_verified : '';
                        $student_link       = get_the_permalink($student_proflie);
                        $hourly_date        = !empty($hourly_data['date']) ? ($hourly_data['date']) : 0; 
                        $start_time         = !empty($hourly_data['start_time']) ? date_i18n($time_format,strtotime($hourly_data['start_time'])) : 0; 
                        $end_time           = !empty($hourly_data['end_time']) ? date_i18n($time_format,strtotime($hourly_data['end_time'])) : 0; 
                        $hourly_date        = date_i18n($date_format, strtotime($hourly_date));
                        $total_hours        = isset($hourly_data['total_hours']) ? $hourly_data['total_hours'] : 0;
                        $post_status        = !empty($post->post_status) ? $post->post_status : '';

                        $send_reminder          = get_post_meta($post->ID,'_send_reminder',true );
                        $send_reminder_class    = !empty($send_reminder) ? 'tu-primgray-btn' : 'tu-reminder_btn';
                        $send_reminder_checked  = !empty($send_reminder) ? 'icon-check' : 'icon-bell';
                        $send_reminder_text     = !empty($send_reminder) ? esc_html__('Reminder sent','tuturn') : esc_html__('Send reminder','tuturn');
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
                                                    <a href="<?php echo esc_url($student_link);?>"><?php echo esc_html($user_name);?></a>
                                                    <?php if (!empty($is_verified) && $is_verified == 'yes') { ?>
                                                        <i class="icon icon-check-circle tu-greenclr"></i>
                                                    <?php } ?>
                                                </h5>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <?php if(isset($total_hours) || !empty($hourly_date) || !empty($hourly_data['documents']['attachments'])){?>
                                        <div class="tu-listinginfo_price">
                                            <?php if(!empty($total_hours)){?>
                                                <h4><?php echo sprintf( esc_html__('%s Hrs','tuturn'),$total_hours);?></h4>
                                            <?php } ?>
                                            <?php if(!empty($start_time)){?><span class="tu-date"><?php esc_html_e('Start time','tuturn');?>: <em><?php echo esc_html($start_time);?></em></span><?php } ?>
                                            <?php if(!empty($end_time)){?><span class="tu-date"><?php esc_html_e('End time','tuturn');?>: <em><?php echo esc_html($end_time);?></em></span><?php } ?>
                                            <?php if( !empty($hourly_date) ){?>
                                                <span class="tu-date"><?php esc_html_e('Work date','tuturn');?>: <em><?php echo esc_html($hourly_date);?></em></span>
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
                            </div>
                            <div class="tu-listinginfo_btn">
                                <?php if( !empty($post_status) && in_array($post_status,array('pending','decline'))){?>
                                    <div class="tu-icon-holder tu-icon-holderv2">
                                        <a href="javascript:void(0)" class="tu_edit_hours" data-post_id="<?php echo intval($post->ID);?>"><i class="icon icon-edit-3 tu-editclr"></i><?php esc_html_e('Edit','tuturn');?></a>
                                        <a href="javascript:void(0)" class="tu_remove_hours" data-post_id="<?php echo intval($post->ID);?>"><i class="icon icon-trash-2 tu-deleteclr"></i><?php esc_html_e('Delete','tuturn');?></a>
                                    
                                    </div>
                                <?php } ?>
                                <div class="tu-btnarea">
                                    <?php if( !empty($hourly_data['documents']['attachments']) ){?>
                                        <a href="javascript:" class="tu-secbtn tu_download_zip_file" data-post_id="<?php echo intval($post->ID);?>"><?php esc_html_e('Attachments','tuturn');?><i class="icon icon-download"></i></a>
                                    <?php } ?>
                                    <?php if( !empty($post_status) && in_array($post_status,array('decline'))){?>
                                        <a href="javascript:void(0)" class="tu_edit_hours tu-primbtn tu-primgreen-btn" data-post_id="<?php echo intval($post->ID);?>"><?php esc_html_e('Edit and Resend','tuturn');?></a>
                                    <?php } ?>
                                    <?php if( !empty($post_status) && in_array($post_status,array('pending'))){?>
                                        <a href="#" data-id="<?php echo intval($post->ID);?>" class="tu-primbtn <?php echo esc_attr($send_reminder_class);?>"><?php echo esc_html($send_reminder_text);?><i class="icon <?php echo esc_attr($send_reminder_checked);?>"></i></a>
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
        <p><?php esc_html_e('We\'re sorry but there is no hours log', 'tuturn'); ?></p>
    </div>
    <?php } ?>
    <script>
        var hourly_data = [];
        window.hourly_data	= <?php echo json_encode($hourly_data_datails); ?>
    </script>
</div>
<div class="modal fade" id="tu-add-hour-log" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5><?php esc_html_e('Add/Update hour log','tuturn');?></h5>
                <a href="javascript:void(0);" class="tu-close" data-bs-dismiss="modal" aria-label="Close"><i class="icon icon-x"></i></a>
            </div>
            <div class="modal-body" id="temp-hours-body"></div>
        </div>
    </div>
</div>
<div class="modal fade" id="tu-send-reminder" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5><?php esc_html_e('Send reminder to parents','tuturn');?></h5>
            <a href="javascript:void(0);" class="tu-close" data-bs-dismiss="modal" aria-label="Close"><i class="icon icon-x"></i></a>
        </div>
        <div class="modal-body">
            <form class="tu-themeform" id="tu_hour_reminder">
                <fieldset>
                    <div class="tu-themeform__wrap">
                        <div class="form-group">
                            <label class="tu-label"><?php esc_html_e('Parent name','tuturn');?></label>
                            <div class="tu-placeholderholder">
                                <input type="text" name="name" class="form-control" required="" placeholder="<?php esc_attr_e('Enter full name','tuturn');?>">
                                <div class="tu-placeholder">
                                    <span><?php esc_html_e('Enter full name','tuturn');?></span>
                                    <em>*</em>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="tu-label"><?php esc_html_e('Parent email address','tuturn');?></label>
                            <div class="tu-placeholderholder">
                                <input type="text" name="email" class="form-control" required="" placeholder="<?php esc_attr_e('Enter email address','tuturn');?>">
                                <div class="tu-placeholder">
                                    <span><?php esc_html_e('Enter email address','tuturn');?></span>
                                    <em>*</em>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="post_id" value="" id="tu-postreminder-id">
                        <div class="form-group tu-formbtn">
                            <a href="javascript:;" class="tu-primbtn-lg tu-postreminder-btn"><?php esc_html_e('Send reminder','tuturn');?></a>
                        </div>
                    </div>
                </fieldset>
            </form>
        </div>
        </div>
    </div>
</div>
<script type="text/template" id="tmpl-updatehours-temp">
    <form class="tu-themeform" id="tu-hourly-form">
        <fieldset>
            <div class="tu-themeform__wrap">
                <div class="form-group">
                    <label class="tu-label"><?php esc_html_e('Log title','tuturn');?></label>
                    <div class="tu-placeholderholder">
                        <input type="text" class="form-control" name="title" value="{{data.title}}">
                        <div class="tu-placeholder">
                            <span><?php esc_html_e('Enter log title here','tuturn');?></span>
                            <em>*</em>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="tu-label"><?php esc_html_e('Add student email to add the log','tuturn');?></label>
                    <p><?php esc_html_e('Type email address and hit enter to fetch the user data','tuturn');?></p>
                    <div class="tu-placeholderholder">
                        <input type="text" class="form-control" id="tu-checkstudent-email" name="email" value="{{data.data.email}}">
                        <div class="tu-placeholder">
                            <span><?php esc_html_e('Enter student email here','tuturn');?></span>
                            <em>*</em>
                        </div>
                        <div class="tu-user-info d-none">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="tu-label"><?php esc_html_e('Log date','tuturn');?></label>
                    <div class="tu-placeholderholder {{data.user_class}}">
                        <input type="text" class="form-control tu-datepicker" name="date" value="{{data.data.date}}">
                        <div class="tu-placeholder">
                            <span><?php esc_html_e('Enter log date','tuturn');?></span>
                            <em>*</em>
                        </div>
                    </div>
                </div>
                <div class="form-group-wrap form-group-wrapv2">
                    <div class="form-group pb-0">
                        <label class="tu-label"><?php esc_html_e('Start & end time','tuturn');?></label>
                    </div>
                    <div class="form-group form-group-half">
                        <div class="tu-select">
                            <select id="tu-start-time-filter" data-placeholder="<?php esc_attr_e('Starting time', 'tuturn'); ?>" name="start_time" data-placeholderinput="<?php esc_attr_e('Starting time', 'tuturn') ?>" class="form-control tu_start_time" required>
                                <option label="<?php esc_attr_e('Starting time', 'tuturn'); ?>"></option>
                                <?php foreach($day_time as $key_time => $time_val){
                                    $time_val 	= date($time_format, strtotime('2022-01-01' . $time_val));    
                                    $selected   = "";                                            
                                    ?>
                                    <option value="<?php echo esc_attr($key_time); ?>" <?php echo esc_attr($selected);?>><?php echo esc_html($time_val); ?></option>
                                <?php } ?>
                            </select>   
                        
                        </div>  
                    </div>
                    <div class="form-group form-group-half">
                        <div class="tu-select">
                            <select id="tu-end-time-filter" data-placeholder="<?php esc_attr_e('Select end time', 'tuturn'); ?>" data-placeholderinput="<?php esc_attr_e('Ending time', 'tuturn') ?>" name="end_time" class="form-control tu_end_time" required>
                                <option label="<?php esc_attr_e('Select end time', 'tuturn'); ?>"></option>
                                <?php foreach($day_time as $key_time => $time_val){
                                    $time_val 	= date($time_format, strtotime('2022-01-01 ' . $time_val));                                                
                                    ?>
                                <option value="<?php echo esc_attr($key_time); ?>"><?php echo esc_html($time_val); ?></option>
                                <?php } ?>
                            </select>  
                            
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="tu-label"><?php esc_html_e('Description', 'tuturn'); ?></label>
                    <div class="tu-placeholderholder">
                        <textarea class="form-control tu-textarea" name="description">{{data.content}}</textarea>
                        <div class="tu-placeholder">
                            <span><?php esc_html_e('Enter description', 'tuturn'); ?></span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="tu-label"><?php esc_html_e('Upload attachment','tuturn')?> </label>
                    <div id="tu-hr-upload-verification" class="tu-identity-documents-upload">
                        <div id="tu-hr-verification-droparea"class="tu-uploadphoto tu-uploadphotovtwo">
                            <div class="tu-uploaddesc">  
                                <h5><?php esc_html_e('Drag or ', 'peer-review-system') ?><input type="file" id="file4"><label for="tuturn-hr-attachment-btn" id="tuturn-hr-attachment-btn"><?php esc_html_e('click here ', 'tuturn') ?></label><?php esc_html_e(' to Upload documents', 'tuturn') ?></h5>
                                <p><?php esc_html_e('Document file size does not exceed 5MB.', 'tuturn');?></p>
                            </div>
                            <svg>
                                <rect width="100%" height="100%"></rect>
                            </svg>
                        </div>
                    </div>
                </div>
                <# if( !_.isEmpty(data.data.documents.attachments) ) {#>
                    <div class="form-group" id="appent-attachment-hr">
                        <ul class="tu-uploadbar tu-bars tuturn-fileprocessing tu-uploadbarv2" id="tu-hr-tuturn-fileprocessing">
                            <# _.each( data.data.documents.attachments , function( element, index ) { #>
                                <li id="tu_file_{{element.attachment_id}}">
                                    <div class="tu-doclist_content">
                                        <div class="tu-doclist_title" id="tu_file_{{element.attachment_id}}">
                                            <h6>{{element.name}}</h6>
                                            <img src="<?php echo esc_url($fileImage);?>" class="file_url" alt="<?php esc_attr_e('upload image','tuturn');?>">
                                            <input type="hidden" class="hour_file_name" name="attachments[{{element.attachment_id}}][attachment_id]" value="{{element.attachment_id}}" />
                                            <input type="hidden" class="hour_file_name" name="attachments[{{element.attachment_id}}][name]" value="{{element.name}}" />
                                            <input type="hidden" class="hour_file_name" name="attachments[{{element.attachment_id}}][url]" value="{{element.url}}" />
                                            <div class="progress">
                                                <div class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                        <a id="tu_delete_attachment" href="javascript:void(0);"><i class="icon icon-trash-2"></i></a>
                                    </div>
                                </li>
                            <# }); #>
                        </ul>
                    </div>
                <# } else{ #>
                    <div class="form-group d-none" id="appent-attachment-hr">
                        <ul class="tu-uploadbar tu-bars tuturn-fileprocessing" id="tu-hr-tuturn-fileprocessing"></ul>
                    </div>
                <# } #>

                <input type="hidden" id="hour_post_id" name="post_id" value="" />
                <div class="form-group tu-formbtn">
                    <a href=" " class="tu-primbtn-lg tu-update-hours"><?php esc_html_e('Add/update time log','tuturn');?></a>
                </div>
            </div>
        </fieldset>
    </form>
</script>
 

<script type="text/template" id="tmpl-usefull-load-hr">
    <li id="tu_file_{{data.id}}">
        <div class="tu-doclist_content">
            <img src="<?php echo esc_url($fileImage); ?>" alt="<?php esc_attr_e('upload image','tuturn');?>">
            <div class="tu-doclist_title" id="tu_file_{{data.id}}">
                <h6>{{data.name}}</h6>
                <span>{{data.size}}</span>
                <input type="hidden" class="hour_file_name" name="attachments[]" value="{{data.file}}" />
                <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
            <a id="tu_delete_attachment" href="javascript:void(0);"><i class="icon icon-trash-2"></i></a>
        </div>
    </li>
</script>
 
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