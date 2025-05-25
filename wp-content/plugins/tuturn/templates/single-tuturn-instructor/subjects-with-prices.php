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
$drop_image         = TUTURN_DIRECTORY_URI . 'public/images/drop-img.png';
$accodion_img       = TUTURN_DIRECTORY_URI . 'public/images/default-avatar.jpg';
$cat_id             = !empty($_GET['cat_id']) ? intval($_GET['cat_id']) : 0;

if (isset($subjects_listings) && is_array($subjects_listings) && count($subjects_listings) > 0) { ?>
    <div class="tu-tabswrapper">
        <div class="tu-tabstitle">
            <h4><?php esc_html_e('I can teach', 'tuturn');?></h4>
        </div>   
        <div class="accordion tu-accordioneduvtwo" id="accordionFlushExampleaa">
            <ul id="tu-edusortable" class="tu-edusortable">
                <?php if (isset($subjects_listings) && is_array($subjects_listings) && count($subjects_listings) > 0) {  
                    $item_counter   = 0;
                    foreach ($subjects_listings as $key => $subject) { 
                        if (!empty($subject['parent_category'])) {
                            $item_counter++;
                            $parent_category = $subject['parent_category'];
                            if (!empty($parent_category['slug'])) {
                                $cat_data = get_term_by('slug', $parent_category['slug'], 'product_cat');
                                if (!empty($cat_data->term_id)) {

                                    $tab_expanded       = !empty($item_counter) && $item_counter === 1 ? 'true' : 'false';
                                    $tab_collapse       = !empty($cat_id) && $cat_id === $cat_data->term_id ? 'collapsed' : '';
                                    $tab_show           = !empty($item_counter) && $item_counter === 1 ? 'show' : '';

                                    ?>
                                    <li class="tu-accordion-item tu-subject-item-<?php echo esc_attr($key); ?>">
                                        <div class="tu-expwrapper">
                                            <div class="tu-accordionedu">
                                                <div class="tu-expinfo">
                                                    <div class="tu-accodion-holder">
                                                        <div class="tu-iccoion-info">
                                                            <div class="tu-accodion-title">
                                                                <h4 class="<?php echo esc_attr($tab_collapse); ?>" data-bs-toggle="collapse" data-bs-target="#flush-collapseOneba-<?php echo esc_attr($key); ?>" aria-expanded="<?php echo esc_attr($tab_expanded); ?>" aria-controls="flush-collapseOneba"><?php echo esc_html($cat_data->name); ?></h4>
                    
                                                            </div>
                                                            <?php
                                                            if (!empty($subject['subcategories']) && is_array($subject['subcategories']) && count($subject['subcategories']) > 0) {
                                                                $subcategories  = $subject['subcategories'];
                                                                ?>
                                                                <ul class="tu-accodion-listing">
                                                                    <?php
                                                                    foreach ($subcategories as $subcategory) {
                                                                        if (!empty($subcategory['slug'])) {
                                                                            $sub_cat_data = get_term_by('slug', $subcategory['slug'], 'product_cat');
                                                                            if (!empty($sub_cat_data->term_id)) {
                                                                            ?>
                                                                                <li>
                                                                                    <?php echo esc_html($sub_cat_data->name); ?>
                                                                                </li>
                                                                        <?php }
                                                                        }
                                                                    } ?>
                                                                </ul>

                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                    <i class="icon icon-plus <?php echo esc_attr($tab_collapse); ?>" role="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOneba-<?php echo esc_attr($key); ?>" aria-expanded="<?php echo esc_attr($tab_expanded); ?>" aria-controls="flush-collapseOneba"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="flush-collapseOneba-<?php echo esc_attr($key); ?>" class="accordion-collapse collapse <?php echo esc_attr($tab_show); ?>" data-bs-parent="#accordionFlushExampleaa">
                                            <div class="tu-edubodymain">
                                                <?php
                                                if (!empty($subject['subcategories']) && is_array($subject['subcategories']) && count($subject['subcategories']) > 0) {
                                                    $subcategories  = $subject['subcategories'];
                                                ?>
                                                    <ul class="tu-accordioneduc">
                                                        <?php
                                                        foreach ($subcategories as $subcategory) {
                                                            if (!empty($subcategory['slug'])) {
                                                                $sub_cat_data = get_term_by('slug', $subcategory['slug'], 'product_cat');
                                                                if (!empty($sub_cat_data->term_id)) {
                                                                    $thumbnail_id  = get_term_meta($sub_cat_data->term_id, 'thumbnail_id', true);
                                                                    $image_url          = !empty($thumbnail_id) ? wp_get_attachment_url($thumbnail_id) : '';
                                                                    $image_url          = !empty($image_url) ? $image_url : $accodion_img;
                                                                    ?>
                                                                    <li class="tu-accodion-holder">
                                                                        <div class="tu-img-area">
                                                                            <figure class="tu-icocodion-img">
                                                                                <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($sub_cat_data->name); ?>">
                                                                            </figure>
                                                                        </div>
                                                                        <div class="tu-iccoion-info">
                                                                            <div class="tu-accodion-title">
                                                                                <h5><?php echo esc_html($sub_cat_data->name); ?></h5>
                                                                            </div>
                                                                            <?php if (isset($subcategory['price'])) { ?>
                                                                                <div class="tu-listinginfo_price">
                                                                                    <span><?php esc_html_e('Starting from', 'tuturn'); ?>:</span>
                                                                                    <h4><?php echo tuturn_price_format($subcategory['price']); ?></h4>
                                                                                </div>
                                                                            <?php } ?>
                                                                            <?php if (!empty($subcategory['content'])) { ?>
                                                                                <p><?php echo esc_html($subcategory['content']); ?></p>
                                                                            <?php } ?>
                                                                        </div>
                                                                    </li>
                                                            <?php }}}?>
                                                    </ul>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </li>
                            <?php }}}}}?>
            </ul>
        </div>
    </div>
<?php }
