<?php

/**
 * Instructor add subjects
 *
 * @package     Tuturn
 * @subpackage  Tuturn/Templates/Profile_Settings/Instructor
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
 */
if (class_exists('WooCommerce')) {
    $subject_data        = !empty($profile_details['subject']) ? $profile_details['subject'] : '';
    $subjects_listings    = !empty($subject_data) ? $subject_data : array();
    $sub_category_data_datails  = array();
?>
    <div class="tu-boxarea" id="user-skills">
        <div class="tu-boxsm">
            <div class="tu-boxsmtitle">
                <h4><?php esc_html_e('I can teach', 'tuturn'); ?></h4>
                <a href="javascript:void(0)" class="tu-add-subjects" data-operation="add"><?php esc_html_e('Add new', 'tuturn'); ?></a>
            </div>
        </div>
        <div class="tu-box">
            <div class="accordion tu-accordioneduvtwo" id="accordionFlushExampleaa">
                <div id="tu-edusortable tu-subsortable" class="tu-edusortable">
                    <?php if (isset($subjects_listings) && is_array($subjects_listings) && count($subjects_listings) > 0) { ?>
                        <?php
                        foreach ($subjects_listings as $key => $subject) {
                            $parent_category = $subject['parent_category'];
                            if (!empty($parent_category['slug'])) {
                                $cat_data = get_term_by('slug', $parent_category['slug'], 'product_cat');
                                if (!empty($cat_data->term_id)) {
                                    $list_subcategories  = array();
                                    if (!empty($subject['subcategories']) && is_array($subject['subcategories']) && count($subject['subcategories']) > 0) {
                                        $subcategories  = $subject['subcategories'];
                                        $cat_titles     = '';
                                        foreach ($subcategories as $subcategory) {
                                            if (!empty($subcategory['slug'])) {
                                                $sub_cat_data = get_term_by('slug', $subcategory['slug'], 'product_cat');
                                                if (!empty($sub_cat_data->term_id)) {

                                                    $thumbnail_id                                           = get_term_meta($sub_cat_data->term_id, 'thumbnail_id', true);
                                                    $price                                                  = isset($subcategory['price']) && $subcategory['price'] > 0 ? $subcategory['price'] : 0;
                                                    $content                                                = !empty($subcategory['content']) ? $subcategory['content'] : '';
                                                    $image_url                                              = !empty($thumbnail_id) ? wp_get_attachment_url($thumbnail_id) : TUTURN_DIRECTORY_URI . 'public/images/default-avatar.jpg';
                                                    $list_subcategories[$sub_cat_data->term_id]['slug']     = $sub_cat_data->slug;
                                                    $list_subcategories[$sub_cat_data->term_id]['name']     = $sub_cat_data->name;
                                                    $list_subcategories[$sub_cat_data->term_id]['term_id']  = $sub_cat_data->term_id;
                                                    $list_subcategories[$sub_cat_data->term_id]['content']  = $content;
                                                    $list_subcategories[$sub_cat_data->term_id]['image_url'] = $image_url;
                                                    $list_subcategories[$sub_cat_data->term_id]['price']    = isset($price) && $price > 0 ? $price : 0;
                                                    $cat_titles = $cat_titles . '<li><h6>' . $sub_cat_data->name . '</h6></li>';
                                                    $sub_category_data_datails[$sub_cat_data->term_id]  = $list_subcategories[$sub_cat_data->term_id];
                                                    
                                                }
                                            }
                                        }
                                    }
                                    ?>
                                    <div class="tu-accordion-item tu-subject-item-<?php echo esc_attr($key); ?>">
                                        <div id="flush-headingOneaa" class="tu-expwrapper">
                                            <div class="tu-accordionedu">
                                                <div class="tu-expinfo">
                                                    <div class="tu-accodion-holder">
                                                        <div class="tu-iccoion-info">
                                                            <div class="tu-accodion-title">
                                                                <h4 class="collapsed" data-bs-toggle="collapse" data-bs-target="#flush-collapseOneaa-<?php echo esc_attr($key); ?>" aria-expanded="true" aria-controls="flush-collapseOneaa-<?php echo esc_attr($key); ?>">
                                                                    <?php echo esc_html($cat_data->name); ?>
                                                                </h4>
                                                                <div class="tu-icon-holder">
                                                                    <a href="javascript:void(0)" class="tu-add-subjects" data-subject='<?php echo json_encode($subject); ?>' data-operation="edit" data-subject_key="<?php echo esc_attr($key); ?>"><i class="icon icon-edit-3 tu-editclr"></i></a>
                                                                    <a href="javascript:void(0)" class="tu-subject-delete" data-operation="edit" data-subject_key="<?php echo esc_attr($key); ?>"><i class="icon icon-trash-2 tu-deleteclr"></i></a>
                                                                </div>
                                                            </div>
                                                            <?php if (!empty($cat_titles)) { ?>
                                                                <ul class="tu-accodion-listing">
                                                                    <?php echo do_shortcode($cat_titles); ?>
                                                                </ul>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                    <i class="icon icon-plus" role="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOneaa-<?php echo esc_attr($key); ?>" aria-expanded="false" aria-controls="flush-collapseOneaa-<?php echo esc_attr($key); ?>"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if (!empty($list_subcategories)) { ?>
                                            <div id="flush-collapseOneaa-<?php echo esc_attr($key); ?>" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExampleaa">
                                                <div class="tu-edubodymain">
                                                    <div class="tu-accordioneduc">
                                                        <?php
                                                        foreach ($list_subcategories as $sub_cat) {
                                                            $term_id        = !empty($sub_cat['term_id']) ? intval($sub_cat['term_id']) : 0;
                                                            $subcat_name    = !empty($sub_cat['name']) ? $sub_cat['name'] : '';
                                                            $price          = isset($sub_cat['price']) ? $sub_cat['price'] : 0;
                                                            $subcat_desc    = isset($sub_cat['description']) ? $sub_cat['description'] : '';
                                                        ?>
                                                            <div class="tu-accodion-holder tu-subcat-<?php echo intval($term_id); ?>">
                                                                <?php if (!empty($sub_cat['image_url'])) { ?>
                                                                    <div class="tu-img-area">
                                                                        <figure class="tu-icocodion-img">
                                                                            <img src="<?php echo esc_url($sub_cat['image_url']); ?>" alt="<?php echo esc_attr($subcat_name); ?>">
                                                                        </figure>
                                                                    </div>
                                                                <?php } ?>
                                                                <div class="tu-iccoion-info">
                                                                    <div class="tu-accodion-title">
                                                                        <?php if (!empty($subcat_name)) { ?>
                                                                            <h5><?php echo esc_html($subcat_name); ?></h5>
                                                                        <?php } ?>
                                                                        <div class="tu-icon-holder">
                                                                            <a href="javascript:void(0)" class="tu-sub-subjects" data-operation="edit" data-term_price="<?php echo esc_attr($price); ?>" data-term_desc="<?php echo esc_attr($subcat_desc); ?>" data-term_id="<?php echo intval($term_id); ?>" data-subject_key="<?php echo esc_attr($key); ?>"><i class="icon icon-edit-3 tu-editclr"></i></a>
                                                                            <a href="javascript:void(0)"><i class="icon icon-trash-2 tu-deleteclr"></i></a>
                                                                        </div>
                                                                    </div>
                                                                    <div class="tu-listinginfo_price">
                                                                        <span><?php esc_html_e('Starting from', 'tuturn'); ?>:</span>
                                                                        <h4><?php tuturn_price_format($price) ?></h4>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                        <?php }
                            }
                        }
                        ?>
                    <?php }  ?>

                    <script>
                        var sub_category_data = [];
                        window.sub_category_data = <?php echo json_encode($sub_category_data_datails); ?>
                    </script>
                </div>
            </div>
        </div>
    </div>
    <script type="text/template" id="tmpl-load-skills-list">
        <div class="tu-tech-title">
            <h6>{{data.categories.parent_category.name}}</h6>
            <div class="tu-icon-holder">
                <a href="javascript:void(0)" class="tu-add-subjects"  data-subject='{{JSON.stringify(data.categories)}}' data-operation="edit" data-subject_key="{{data.id}}"><i class="icon icon-edit-3 tu-editclr"></i></a>
                <a href="javascript:void(0)"><i class="icon icon-trash-2 tu-deleteclr"></i></a>
            </div>
        </div>
        <ul class="tu-serviceslist">
            <#
            if( !_.isEmpty(data.categories.subcategories) ) {
                let counter=1;
                _.each( data.categories.subcategories , function( item, index ) {
                    #>
                    <li>
                        <a href="javascript:void(0);">{{item.name}}</a>
                    </li>
                    <# 
                }); 
            } 
            #>
        </ul>
        <input type="hidden" id="subject_{{data.id}}" name="subject[{{data.categories.parent_category_id}}]" value='{{JSON.stringify(data.categories)}}' >
    </script>
    <script type="text/template" id="tmpl-load-skills-popup">
        <div class="modal-header">
            <h5><?php esc_html_e('Add/edit subjects', 'tuturn'); ?></h5>
            <a href="javascript:void(0);" class="tu-close" type="button" data-bs-dismiss="modal" aria-label="Close"><i class="icon icon-x"></i></a>
        </div>
        <div class="modal-body">
            <form class="tu-themeform tu-subjectform">
                <input type="hidden" id="subject_key" value="{{data.id}}">
                <fieldset>
                    <div class="tu-themeform__wrap">
                        <div class="form-group">
                            <label class="tu-label"><?php esc_html_e('Please select what you can teach', 'tuturn'); ?></label>
                            <div class="tu-select"  id="tu-profile-categories"></div>
                        </div>
                        <div class="form-group form-categories">
                            <div id="tu-profile-sub-categories" class="tu-categoriesoption"></div>
                            <ul class="tu_wrappersortable tu-labels" id="tu-profile-selected-categories"></ul>
                        </div>
                        <div class="form-group tu-formbtn">
                            <a href="javascript:void(0);" class="tu-primbtn-lg" id="tu-submit-subjects"><?php esc_html_e('Save & update changes', 'tuturn') ?></a>
                        </div>
                    </div>
                </fieldset>
            </form>
        </div>
    </script>
    <script type="text/template" id="tmpl-load-subcat-detail-popup">
        <div class="modal-header">
            <h5><?php esc_html_e('Add/edit sub category detail', 'tuturn'); ?></h5>
            <a href="javascript:void(0);" class="tu-close" type="button" data-bs-dismiss="modal" aria-label="Close"><i class="icon icon-x"></i></a>
        </div>
        <div class="modal-body">
            <form class="tu-themeform tu-subcatform">
                <fieldset>
                    <div class="tu-themeform__wrap">

                    <div class="form-group">
                        <label class="tu-label"><?php esc_html_e('Add price', 'tuturn'); ?></label>
                        <div class="tu-placeholderholder">
                            <input type="text" name="subcategory_price" value="{{data.child_term_price}}" id="subcategory_price" class="form-control tu-form-input" placeholder=" " required>
                            <div class="tu-placeholder">
                                <span><?php esc_html_e('Enter price here', 'tuturn'); ?></span>
                                <em>*</em>
                            </div>
                        </div>
                    </div>
                    <div class="form-group tu-message-text">
                        <label class="tu-label"><?php esc_html_e('Add description', 'tuturn'); ?></label>
                        <div class="tu-placeholderholder">
                        <textarea class="form-control tu-form-input subcategory_desc" id="subcategory_desc" name="subcategory_desc" required placeholder="<?php esc_attr_e('Enter description', 'tuturn'); ?>"  maxlength="500">{{ data.child_term_desc }}</textarea>
                        </div>
                        <div class="tu-input-counter">
                            <span><?php esc_html_e('Characters left', 'tuturn'); ?>:</span>
                            <b class="tu_current_comment"><?php echo intval(500); ?></b>
                            /
                            <em class="tu_maximum_comment"> <?php echo intval(500); ?></em>
                        </div>
                    </div> 
                        <div class="form-group tu-formbtn">
                            <a href="javascript:void(0);" data-parent_term_id="{{data.parent_id}}" data-child_term_id="{{data.child_term_id}}" class="tu-primbtn-lg" id="tu-submit-subcats-details"><?php esc_html_e('Save & update changes', 'tuturn') ?></a>
                        </div>
                    </div>
                </fieldset>
            </form>
        </div>
    </script>
<?php } else {
    do_action('tuturn_woocommerce_install_notice');
}
