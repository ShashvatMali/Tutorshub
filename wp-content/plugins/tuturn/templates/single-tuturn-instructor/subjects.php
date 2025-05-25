<?php
/**
 * Instructor profile subjects
 *
 * @package     Tuturn
 * @subpackage  Tuturn/Templates/Single_tuturn_Instructor
 * @author      Amentotech <info@amentotech.com>
 * @link       https://themeforest.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
*/
$subject_listings       = !empty($profile_details['subject']) ? $profile_details['subject'] : array();
$subjects_listings      = !empty($subject_listings) ? $subject_listings : array(); 
$instructor_search_url  = tuturn_get_page_uri('instructor_search');
if (isset($subjects_listings) && is_array($subjects_listings) && count($subjects_listings) > 0) { ?>
    <div class="tu-tabswrapper">
        <div class="tu-tabstitle">
            <h4><?php esc_html_e('I can teach', 'tuturn');?></h4>
        </div>        
        <ul class="tu-icanteach">
            <?php foreach ($subjects_listings as $key => $subject) {
                $parent_category = $subject['parent_category'];
                if(!empty($parent_category['slug'])){
                    $cat_data = get_term_by('slug', $parent_category['slug'], 'product_cat');
                    if(!empty($cat_data->term_id)){ ?>
                        <li>
                            <h6><?php echo esc_html($cat_data->name);?></h6>
                            <?php if (!empty($subject['subcategories']) && is_array($subject['subcategories']) && count($subject['subcategories']) > 0) {
                                $show_subjects      = 9;
                                $counter_subjects   = 0;
                                $subjects_arr 	    = array();
                                $subcategories      = $subject['subcategories']; 
                                ?>
                                <ul class="tu-serviceslist">
                                    <?php  foreach ($subcategories as $subcategory) {
                                        $counter_subjects++;
                                        if(!empty($subcategory['slug'])){
                                            $instructor_search_url  = add_query_arg(array('categories'=> esc_html($parent_category['slug']), 'sub_categories' => array($subcategory['slug'])), $instructor_search_url);
                                            $sub_cat_data           = get_term_by('slug', $subcategory['slug'], 'product_cat');
                                            if (!empty($sub_cat_data->term_id)) {
                                                if($counter_subjects <= $show_subjects){ ?>
                                                    <li>
                                                        <a href="<?php echo esc_url($instructor_search_url); ?>"><?php echo esc_html($sub_cat_data->name); ?></a>
                                                    </li>
                                                    <?php } else {
                                                        $subjects_arr[]  = 	array(
                                                            'name' => $sub_cat_data->name,
                                                            'slug' => $sub_cat_data->slug,
                                                        );
                                                    }
                                                }
                                            }
                                    } ?>
                                    <?php if(($counter_subjects) > $show_subjects && !empty($subjects_arr)){ ?>
                                        <li>
                                            <a class="tu-showmore tu-tooltip-tags" id="tu-detail-subjects" href="javascript:void(0);"  data-tippy-trigger="click" data-template="tu-industrypro-<?php echo intval($cat_data->term_id); ?>" data-tippy-interactive="true" data-tippy-placement="top-start"> <?php echo sprintf( __( '+%s more', 'tuturn' ), intval($counter_subjects) - $show_subjects  ); ?></a>
                                            <div id="tu-industrypro-<?php echo intval($cat_data->term_id); ?>" class="tu-tippytooltip d-none">
                                                <div class="tu-selecttagtippy tu-tooltip ">
                                                    <ul class="tu-posttag tu-posttagv2">
                                                        <?php foreach($subjects_arr as $item){
                                                            if(!empty($item['name'] && !empty($item['slug']))){
                                                            $instructor_search_url = add_query_arg(array('categories'=> esc_html($parent_category['slug']), 'sub_categories' => array($item['slug'])), $instructor_search_url); ?>
                                                            <li>
                                                                <a href="<?php echo esc_url($instructor_search_url); ?>"><?php echo esc_html($item['name']) ?></a>
                                                            </li>
                                                        <?php } } ?>
                                                    </ul>
                                                </div>
                                            </div>
                                        </li>
                                    <?php } ?>
                                </ul>
                            <?php } ?>
                        </li>                 
                        <?php
                    }
                }
            } ?>                    
        </ul>
    </div>
<?php }
