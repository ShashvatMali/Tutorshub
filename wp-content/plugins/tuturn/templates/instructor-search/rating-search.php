<?php

/**
 * rating search
 *
 * @package     tuturn
 * @subpackage  tuturn/templates/instructor-search
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
 */
if (!empty($args) && is_array($args)) {
    extract($args);
}
$ratings_values = apply_filters('tuturn_rating_search111', array(
    '5.0'    => 'tu-fivestar',
    '4.0'   => 'tu-fourstar',
    '3'     => 'tu-threestar',
    '2'     => 'tu-twostar',
    '1'      => 'tu-onestar',
));
?>
<div class="tu-aside-holder">
    <div class="tu-asidetitle" data-bs-toggle="collapse" data-bs-target="#side1a" role="button" aria-expanded="true">
        <h5><?php esc_html_e('Rating', 'tuturn'); ?></h5>
    </div>
    <div id="side1a" class="collapse show">
        <div class="tu-aside-content">
            <ul class="tu-categoriesfilter">
                <?php foreach ($ratings_values as $key => $rating_class) {
                    $key    = (int)$key;
                    $checked    = '';
                    if (!empty($rating) && is_array($rating) && in_array($key, $rating)) {
                        $checked    = 'checked';
                    } ?>
                    <li>
                        <div class="tu-check tu-checksm">
                            <input type="checkbox" id="rate<?php echo (int)$key; ?>" name="rating[]" value="<?php echo (int)$key; ?>" <?php echo esc_attr($checked); ?>>
                            <label for="rate<?php echo (int)$key; ?>">
                                <span class="tu-featureRating">
                                    <span class="-featureRating__stars <?php echo esc_attr($rating_class); ?> tu-stars"><span></span></span>
                                    <span class="tu-totalreview">
                                        <span><?php echo number_format(floatval($key), 1); ?><em>/<?php echo number_format(floatval(count($ratings_values)), 1); ?></em></span>
                                    </span>
                                </span>
                            </label>
                        </div>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
</div>