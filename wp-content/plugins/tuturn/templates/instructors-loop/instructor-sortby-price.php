<?php
/**
 * instructor add to favourites
 *
 * @package     Tuturn
 * @subpackage  Tuturn/templates/instructor-loop
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
 */
global  $post, $current_user;
if (!empty($args) && is_array($args)) {
    extract($args);
}
$sort_by        = !empty($args) ? ($args) : '';
$select         = 'selected="selected"';
$select_asc     = !empty($sort_by) && $sort_by == 'asc' ? $select  : '' ;
$select_desc    = !empty($sort_by) && $sort_by == 'desc' ? $select  : '' ;
?>
<div class="tu-sortby">
    <div class="tu-selectv">
        <select class="form-control" id="tu-instructor-search-sortby" data-placeholder="<?php esc_attr_e('Sort by price', 'tuturn');?>">
            <option value=""></option>
            <option value="desc"  <?php  echo do_shortcode($select_desc) ?> ><?php esc_html_e('Price high to low','tuturn')?></option>
            <option value="asc" <?php echo do_shortcode($select_asc) ?> ><?php esc_html_e('Price low to high','tuturn')?></option>
        </select>
    </div>
</div>