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
    $subject_data       = !empty($profile_details['subject']) ? $profile_details['subject'] : '';
    $subjects_listings  = !empty($subject_data) ? $subject_data : array();
    
    $cat_id             = !empty($_GET['cat_id']) ? intval($_GET['cat_id']) : 0;
    $drop_image         = TUTURN_DIRECTORY_URI . 'public/images/drop-img.png';
    $accodion_img       = TUTURN_DIRECTORY_URI . 'public/images/default-avatar.jpg';
    $add                = 'add';
?>

<div class="tu-boxarea">
    <div class="tu-boxsm">
        <div class="tu-boxsmtitle">
            <h4><?php esc_html_e('I can teach', 'tuturn'); ?></h4>
            <a href="javascript:void(0)" class="tu-add-subjects" data-operation="<?php echo esc_attr($add); ?>"><?php esc_html_e('Add new', 'tuturn'); ?></a>
        </div>
    </div>
    <div class="tu-box">
        <div class="accordion tu-accordioneduvtwo" id="accordionFlushExampleaa">
            <ul id="tu-edusortable" class="tu-edusortable">

                <?php if (isset($subjects_listings) && is_array($subjects_listings) && count($subjects_listings) > 0) {  
                    foreach ($subjects_listings as $key => $subject) { 
                        if (!empty($subject['parent_category'])) {
                            $parent_category = $subject['parent_category'];
                            if (!empty($parent_category['slug'])) {
                                $cat_data = get_term_by('slug', $parent_category['slug'], 'product_cat');
                                if (!empty($cat_data->term_id)) {

                                    $tab_expanded       = !empty($cat_id) && $cat_id === $cat_data->term_id ? 'true' : 'false';
                                    $tab_collapse       = !empty($cat_id) && $cat_id === $cat_data->term_id ? 'collapsed' : '';
                                    $tab_show           = !empty($cat_id) && $cat_id === $cat_data->term_id ? 'show' : '';

                                    ?>
                                    <li class="tu-accordion-item tu-subject-item-<?php echo esc_attr($key); ?>">
                                        <div class="tu-expwrapper">
                                            <div class="tu-accordionedu">
                                                <div class="tu-expinfo">
                                                    <div class="tu-accodion-holder">
                                                        <img class="tu-drop-img" src="<?php echo esc_url($drop_image); ?>" alt="<?php esc_attr_e('Sort', 'tuturn'); ?>">
                                                        <div class="tu-iccoion-info">
                                                            <div class="tu-accodion-title">
                                                                <h4 class="<?php echo esc_attr($tab_collapse); ?>" data-bs-toggle="collapse" data-bs-target="#flush-collapseOneba-<?php echo esc_attr($key); ?>" aria-expanded="<?php echo esc_attr($tab_expanded); ?>" aria-controls="flush-collapseOneba"><?php echo esc_html($cat_data->name); ?></h4>
                                                                <div class="tu-icon-holder">
                                                                    <a href="javascript:void(0)" class="tu-add-subjects" data-subject='<?php echo json_encode($subject); ?>' data-operation="edit" data-subject_key="<?php echo esc_attr($key); ?>"><i class="icon icon-edit-3 tu-editclr"></i></a>
                                                                    <a href="javascript:void(0)" class="tu_subcat_subject_delete" data-operation="edit" data-subject_key="<?php echo esc_attr($key); ?>"><i class="icon icon-trash-2 tu-deleteclr"></i></a>
                                                                </div>
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
                                                                            <img class="tu-drop-img" src="<?php echo esc_url($drop_image); ?>" alt="<?php esc_html_e('image', 'tuturn'); ?>">
                                                                            <figure class="tu-icocodion-img">
                                                                                <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($sub_cat_data->name); ?>">
                                                                            </figure>
                                                                        </div>

                                                                        <div class="tu-iccoion-info">
                                                                            <div class="tu-accodion-title">
                                                                                <h5><?php echo esc_html($sub_cat_data->name); ?></h5>
                                                                                <div class="tu-icon-holder">
                                                                                    <a href="javascript:void(0)" class="tu-add-subcat-subjects" data-tu_parent_subject_id="<?php echo esc_attr($key); ?>" data-tu_child_subject_id="<?php echo esc_attr($subcategory['id']); ?>" data-subcat_subject='<?php echo json_encode($subcategory); ?>' data-parent_subject='<?php echo json_encode($subject); ?>' data-operation="edit" data-subcat_subject_key="<?php echo esc_attr($sub_cat_data->term_id); ?>"><i class="icon icon-edit-3 tu-editclr"></i></a>
                                                                                    <a href="javascript:void(0)" class="tu-sub-subject-delete" data-tu_parent_subject_id="<?php echo esc_attr($key); ?>" data-tu_child_subject_id="<?php echo esc_attr($subcategory['id']); ?>" data-sub_subject_key="<?php echo esc_attr($sub_cat_data->term_id); ?>"><i class="icon icon-trash-2 tu-deleteclr"></i></a>
                                                                                </div>
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
                                                        <?php }
                                                            }
                                                        } ?>
                                                    </ul>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <input type="hidden" id="subject_<?php echo esc_attr($key); ?>" name="subject[<?php echo esc_attr($cat_data->term_id); ?>]" value='<?php echo json_encode($subject); ?>'>
                                    </li>
                <?php }
                            }
                        }
                    }
                } ?>

            </ul>
        </div>
    </div>
</div>
<script type="text/template" id="tmpl-load-skills-list">
    <div class="tu-expwrapper">
        <div class="tu-accordionedu">
            <div class="tu-expinfo">
                <div class="tu-accodion-holder">
                    <img class="tu-drop-img" src="<?php echo esc_url($drop_image); ?>" alt="<?php esc_html_e('image', 'tuturn'); ?>">
                    <div class="tu-iccoion-info">
                        <div class="tu-accodion-title">
                            <h4 class="collapsed" data-bs-toggle="collapse" data-bs-target="#flush-collapseOneba" aria-expanded="true" aria-controls="flush-collapseOneba">{{data.categories.parent_category.name}}</h4>
                            <div class="tu-icon-holder">
                                <a href="javascript:void(0)" class="tu-add-subjects" data-subject='{{JSON.stringify(data.categories)}}' data-operation="edit" data-subject_key="{{data.id}}"><i class="icon icon-edit-3 tu-editclr"></i></a>
                                <a href="javascript:void(0)"><i class="icon icon-trash-2 tu-deleteclr"></i></a>
                            </div>
                        </div>
                        <# if( !_.isEmpty(data.categories.subcategories) ) { #>
                            <ul class="tu-serviceslist tu-accodion-listing">
                                <# let counter=1; _.each( data.categories.subcategories , function( item, index ) { #>
                                    <li>
                                        <h6>{{item.name}}</h6>
                                    </li>
                                    <# }); #>
                            </ul>
                            <# } #>
                                <input type="hidden" id="subject_{{data.id}}" name="subject[{{data.categories.parent_category_id}}]" value='{{JSON.stringify(data.categories)}}'>
                    </div>
                </div>
                <i class="icon icon-plus" role="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOneba-{{data.id}}" aria-expanded="true" aria-controls="flush-collapseOneba"></i>
            </div>
        </div>
    </div>
    <# if( !_.isEmpty(data.categories.subcategories) ) { #>
        <div id="flush-collapseOneba-{{data.id}}" class="accordion-collapse collapse show" data-bs-parent="#accordionFlushExampleaa">
            <div class="tu-edubodymain">
                <div class="tu-accordioneduc">
                    <# let count=1; _.each( data.categories.subcategories , function( item, index ) { #>
                        <div class="tu-accodion-holder">
                            <div class="tu-img-area">
                                <img class="tu-drop-img" src="<?php echo esc_url($drop_image) ?>" alt="<?php esc_html_e('image', 'tuturn'); ?>">
                                <figure class="tu-icocodion-img">
                                    <img src="<?php echo esc_url($accodion_img); ?>" alt="<?php esc_attr_e('img', 'tuturn'); ?>">
                                </figure>
                            </div>
                            <img src="" alt="">
                            <div class="tu-iccoion-info">
                                <div class="tu-accodion-title">
                                    <h5>{{item.name}}</h5>
                                    <div class="tu-icon-holder">
                                        <a href="javascript:void(0)" class="tu-add-subcat-subjects" data-tu_parent_subject_id="{{data.id}}" data-tu_child_subject_id="{{item.id}}" data-subcat_subject='{{JSON.stringify(data.categories.subcategories[index])}}}' data-parent_subject='{{JSON.stringify(data.categories)}}' data-operation="edit" data-subcat_subject_key="{{item.id}}"><i class="icon icon-edit-3 tu-editclr"></i></a>
                                        <a href="javascript:void(0)"><i class="icon icon-trash-2 tu-child-deleteclr"></i></a>
                                    </div>
                                </div>
                                <div class="tu-listinginfo_price">
                                    <span><?php esc_html_e('Starting froms', 'tuturn'); ?>:</span>
                                    <h4>{{item.price}}</h4>
                                </div>
                                <p>{{item.content}}</p>
                            </div>
                        </div>
                        <# }); #>
                </div>
            </div>
        </div>
        <# } #>
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
                        <a href="javascript:void(0);" class="tu-primbtn-lg" id="tu-subcat-submit-subjects" data-operation="{{data.operation}}"><?php esc_html_e('Save & update changes', 'tuturn') ?></a>
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
                        <input type="text" name="subcategory_price" value="{{data.child_subject_obj.price}}" id="subcategory_price" class="form-control tu-form-input" placeholder=" " required>
                        <div class="tu-placeholder">
                            <span><?php esc_html_e('Enter price here', 'tuturn'); ?></span>
                            <em>*</em>
                        </div>
                    </div>
                </div>
                <div class="form-group tu-message-text">
                    <label class="tu-label"><?php esc_html_e('Add description', 'tuturn'); ?></label>
                    <div class="tu-placeholderholder">
                    <textarea class="form-control tu-form-input subcategory_desc" id="subcategory_desc" name="subcategory_desc" required placeholder="<?php esc_attr_e('Enter description', 'tuturn'); ?>"  maxlength="500">{{ data.child_subject_obj.content }}</textarea>
                    </div>
                    <div class="tu-input-counter">
                        <span><?php esc_html_e('Characters left', 'tuturn'); ?>:</span>
                        <b class="tu_current_comment"><?php echo intval(500); ?></b>
                        /
                        <em class="tu_maximum_comment"> <?php echo intval(500); ?></em>
                    </div>
                </div> 
                    <div class="form-group tu-formbtn">
                        <a href="javascript:void(0);" id="tu-submit-child-subject-form" data-tu_parent_term_id="{{data.parent_subject_id}}" data-tu_child_term_id="{{data.child_subject_id}}" class="tu-primbtn-lg"><?php esc_html_e('Save & update changes', 'tuturn') ?></a>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
</script>
<?php
} else {
    do_action('tuturn_woocommerce_install_notice');
}
