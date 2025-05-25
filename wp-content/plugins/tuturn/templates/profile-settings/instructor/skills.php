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
    $add                   = 'add';
?>
    <div class="tu-boxarea" id="user-skills">
        <div class="tu-boxsm">
            <div class="tu-boxsmtitle">
                <h4><?php esc_html_e('I can teach', 'tuturn'); ?></h4>
                <a href="javascript:void(0)" class="tu-add-subjects" data-operation="<?php echo esc_attr($add) ?>"><?php esc_html_e('Add new', 'tuturn'); ?></a>
            </div>
        </div>
        <div class="tu-box">
            <ul class="tu-icanteach" id="tu-subsortable">
                <?php if (isset($subjects_listings) && is_array($subjects_listings) && count($subjects_listings) > 0) {
                    foreach ($subjects_listings as $key => $subject) {
                        if (!empty($subject['parent_category'])) {
                            $parent_category = $subject['parent_category'];
                            if (!empty($parent_category['slug'])) {
                                $cat_data = get_term_by('slug', $parent_category['slug'], 'product_cat');
                                if (!empty($cat_data->term_id)) { ?>
                                    <li class="tu-accordion-item tu-subject-item-<?php echo esc_attr($key); ?>">
                                        <div class="tu-tech-title">
                                            <h6><?php echo esc_html($cat_data->name); ?></h6>
                                            <div class="tu-icon-holder">
                                                <a href="javascript:void(0)" class="tu-add-subjects" data-subject='<?php echo json_encode($subject); ?>' data-operation="edit" data-subject_key="<?php echo esc_attr($key); ?>"><i class="icon icon-edit-3 tu-editclr"></i></a>
                                                <a href="javascript:void(0)" class="tu-subject-delete" data-operation="edit" data-subject_key="<?php echo esc_attr($key); ?>"><i class="icon icon-trash-2 tu-deleteclr"></i></a>
                                            </div>
                                        </div>
                                        <?php

                                        if (!empty($subject['subcategories']) && is_array($subject['subcategories']) && count($subject['subcategories']) > 0) {
                                            $subcategories  = $subject['subcategories'];
                                        ?>
                                            <ul class="tu-serviceslist">
                                                <?php foreach ($subcategories as $subcategory) {
                                                    if (!empty($subcategory['slug'])) {

                                                        $sub_cat_data = get_term_by('slug', $subcategory['slug'], 'product_cat');
                                                        if (!empty($sub_cat_data->term_id)) { ?>
                                                            <li>
                                                                <a href="javascript:void(0);"><?php echo esc_html($sub_cat_data->name); ?></a>
                                                            </li>
                                                <?php
                                                        }
                                                    }
                                                } ?>

                                            </ul>

                                        <?php } ?>
                                        <input type="hidden" id="subject_<?php echo esc_attr($key); ?>" name="subject[<?php echo esc_attr($cat_data->term_id); ?>]" value='<?php echo json_encode($subject); ?>'>
                                    </li>
                <?php
                                }
                            }
                        }
                    }
                } ?>
            </ul>
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
<?php } else {
    do_action('tuturn_woocommerce_install_notice');
}
