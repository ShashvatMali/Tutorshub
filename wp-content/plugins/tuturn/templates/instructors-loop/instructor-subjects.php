<?php
/**
 * Instructor teaching subjects
 *
 * @package     Tuturn
 * @subpackage  Tuturn/templates/instructor-loop
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
 */
global $post, $tuturn_settings;
$profile_id             = !empty($post->ID) ? intval($post->ID) : 0;
$profile_details        = get_post_meta($profile_id, 'profile_details', true);
$profile_details        = !empty($profile_details) ? $profile_details : array();  
$teaching_subject       = !empty($profile_details['subject']) ? $profile_details['subject'] : array();
$instructor_search_url  = tuturn_get_page_uri('instructor_search');
$subjects_details = array();
if(!empty($teaching_subject)){
    foreach($teaching_subject as $subject){
        $category_id        = $subject['parent_category_id'];
        $category_slug      = $subject['parent_category']['slug'];
        $category_text      = $subject['parent_category']['name'];
        /* only parents level */
        $subjects_details[] = array(
            'id'               => $category_id,
            'category_slug'    => $category_slug,
            'category_text'    => $category_text,
         );
    }
}


if(!empty($subjects_details)) {
    $count  = !empty(count($subjects_details)) ? intval(count($subjects_details)) : '';
    ?>
    <ul class="tu-serviceslist">
        <?php
        $show_subjects      = 4;
        $counter_subjects   = 0;
        $subjects_arr 	    = [];
        for($i=0; $i<$count; $i++){
            $counter_subjects++;
            $subject_name       = $subjects_details[$i]['category_text'];
            $subject_slug       = $subjects_details[$i]['category_slug'];
            $category_id        = $subjects_details[$i]['id'];

            if(!empty($category_id) && !empty($subject_name)){
                $instructor_search_url = add_query_arg('categories', esc_html($subject_slug), $instructor_search_url);
                if($counter_subjects <= $show_subjects){ ?>
                    <li>
                        <a href="<?php echo esc_url($instructor_search_url); ?>"><?php echo esc_html($subject_name)?></a>
                    </li>
                    <?php } else {
                        $subjects_arr[]  = 	array(
                            'subject_id'        => $category_id,
                            'subject_name'      => $subject_name,
                            'subject_slug'      => $subject_slug,
                        );
                    }
                }
            }
            if(($counter_subjects) > $show_subjects && !empty($subjects_arr)){ ?>
            <li>
                <a class="tu-showmore tu-tooltip-tags" href="javascript:void(0);"  data-tippy-trigger="click" data-template="tu-industrypro" data-tippy-interactive="true" data-tippy-placement="top-start"> <?php esc_html_e('...','tuturn')?></a>
                <div id="tu-industrypro" class="tu-tippytooltip d-none">
                    <div class="tu-selecttagtippy tu-tooltip ">
                        <ul class="tu-posttag tu-posttagv2">
                            <?php foreach($subjects_arr as $item){
                                if(!empty($item['subject_id'] && !empty($item['subject_id']))){
                                    $instructor_search_url = add_query_arg('categories', esc_html($item['subject_slug']), $instructor_search_url); ?>
                                <li>
                                    <a href="<?php echo esc_url($instructor_search_url); ?>"><?php echo esc_html($item['subject_name']) ?></a>
                                </li>
                            <?php } } ?>
                        </ul>
                    </div>
                </div>
            </li>
        <?php }?>
    </ul>
<?php }
