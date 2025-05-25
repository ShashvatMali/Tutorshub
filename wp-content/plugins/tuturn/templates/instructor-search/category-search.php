<?php
/**
 * Categories search fields
 *
 * @package     tuturn
 * @subpackage  tuturn/templates/instructor-search
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
 */
global $tuturn_settings;
if (!empty($args) && is_array($args)) {
    extract($args);
}
if (!class_exists('WooCommerce')) {
    return;
}

$toggle_expand      = 'true';
$toggle_show_class  = ' show';
if (!empty($category) || !empty($sub_categories)) {
    $toggle_expand    = 'true';
    $toggle_show_class    = ' show';
}

$exclude_uncategory             = !empty($tuturn_settings['hide_product_uncat'][0]) ? $tuturn_settings['hide_product_uncat'][0] : '';
$uncategorized_obj              = !empty($exclude_uncategory) ? get_term_by('slug', $exclude_uncategory, 'product_cat') : '';
$uncategorized_id               = !empty($uncategorized_obj) ? $uncategorized_obj->term_id : 0;
?>
<div class="tu-aside-holder">
    <div class="tu-asidetitle" data-bs-toggle="collapse" data-bs-target="#side2" role="button" aria-expanded="<?php echo esc_attr($toggle_expand);?>">
        <h5><?php esc_html_e('Subject & Level', 'tuturn'); ?></h5>
    </div>
    <div id="side2" class="collapse <?php echo esc_attr($toggle_show_class);?>" style="">
        <div class="tu-aside-content">
            <div class="tu-filterselect">
                <div class="tu-select">
                    <?php
                    if(class_exists('WooCommerce')){
                        $tax_args = array(
                            'show_option_none'  => esc_html__('Select category', 'tuturn'),
                            'show_count'        => false,
                            'hide_empty'        => 0,
                            'name'              => 'categories',
                            'class'             => 'form-control tu-input-field tu-select-category',
                            'taxonomy'          => 'product_cat',
                            'id'                => 'instructor-search-dropdown',
                            'value_field'       => 'slug',
                            'orderby'           => 'name',
                            'selected'          => $category,
                            'hide_if_empty'     => true,
                            'echo'              => true,
                            'parent'            => 0,
                            'required'          => false,
                            'disabled'          => true,
                        );
                        if (!empty($uncategorized_id)) {
                            $tax_args['exclude'] = array($uncategorized_id);
                        }
                        wp_dropdown_categories($tax_args);
                    }
                    ?>
                </div>
            </div>
            <div class="tu-filterselect" id="tu-subcategories-instructor">
                <?php do_action('tuturn_sub_categories', $args);?>
            </div>
        </div>
    </div>
</div>