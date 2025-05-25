<?php

/**
 * instructor subcategories search field
 *
 * @package     Tuturn
 * @subpackage  Tuturn/templates/instructor-search
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
 */

if (!empty($args) && is_array($args)) {
    extract($args);
}
$sub_categories      = !empty($_GET['sub_categories']) ? $_GET['sub_categories'] : array();
$category            = !empty($args) ? $args['category']  : esc_html($_GET['categories']);
$toggle_expand       = 'true';
$toggle_show_class   = ' show';
if (!empty($category) || !empty($sub_categories)) {
    $toggle_expand    = 'true';
    $toggle_show_class    = ' show';
}
if (!empty($category)) {
    $term = get_term_by('slug', $category, 'product_cat');
    if (!empty($term->term_id)) {
        $tax_args = array(
            'taxonomy'      => 'product_cat',
            'hide_empty'    => 0,
            'parent'        => $term->term_id,
        );
        $categories = get_terms($tax_args);
        if ($categories) { ?>
            <h6><?php esc_html_e('Choose subcategory', 'tuturn'); ?></h6>
            <ul class="tu-categoriesfilter" >
                <?php foreach ($categories as $key => $category_) {
                    $checked    = '';
                    if (!empty($sub_categories) && is_array($sub_categories) && in_array($category_->slug, $sub_categories)) {
                        $checked    = 'checked';
                    } ?>
                    <li>
                        <div class="tu-check tu-checksm">
                            <input type="checkbox" id="expcheck<?php echo intval($category_->term_id); ?>" name="sub_categories[]" value="<?php echo esc_attr($category_->slug); ?>" <?php echo esc_attr($checked); ?>>
                            <label for="expcheck<?php echo intval($category_->term_id); ?>"><?php echo esc_html($category_->name); ?></label>
                        </div>
                    </li>
                    <?php 
                } ?>
                <div class="show-more">
                    <a href="javascript:void(0);" class="tu-readmorebtn tu-show_more"><?php esc_html_e('Show more','tuturn')?> </a>
                </div>
            </ul>
            <?php
        }
    }
}
