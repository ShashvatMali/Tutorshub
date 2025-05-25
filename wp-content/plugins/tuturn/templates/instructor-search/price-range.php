<?php

/**
 * price range
 *
 * @package     tuturn
 * @subpackage  tuturn/templates/instructor-search
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
 */
global $tuturn_settings;
$min_price                    = !empty($tuturn_settings['min_search_price']) ? $tuturn_settings['min_search_price'] : '';
$max_price                    = !empty($tuturn_settings['max_search_price']) ? $tuturn_settings['max_search_price'] : '';
$disable_range_slider         = !empty($tuturn_settings['disable_range_slider']) ? ($tuturn_settings['disable_range_slider']) : '';
if (!empty($args) && is_array($args)) {
  extract($args);
}
$min_search_price             = !empty($args['min-range']) ? ($args['min-range']) : $min_price;
$max_search_price             = !empty($args['max-range']) ? ($args['max-range']) : $max_price;

if (empty($max_price)) {
  $max_price  = 5000;
}
?>
<div class="tu-aside-holder">
  <div class="tu-asidetitle" data-bs-toggle="collapse" data-bs-target="#side3" role="button" aria-expanded="true">
    <h5><?php esc_html_e('Price range', 'tuturn'); ?></h5>
  </div>
  <div id="side3" class="collapse show">
    <div class="tu-aside-content">
      <div class="tu-rangevalue" data-bs-target="#tu-rangecollapse" role="list" aria-expanded="false">
        <div class="tu-areasizebox">
          <input type="number" min="<?php echo intval($min_search_price); ?>" max="<?php echo intval($min_search_price); ?>" name="tu-min-range" min-val=" value=" <?php echo esc_attr($min_search_price); ?>" class="form-control tu-input-field" step="1" placeholder="<?php esc_attr_e('Min price', 'tuturn'); ?>" id="tu-min-value" />
          <input type="number" name="tu-max-range" min="<?php echo intval($min_search_price); ?>" max="<?php echo intval($min_search_price); ?>" value="<?php echo esc_attr($max_search_price); ?>" class="form-control tu-input-field" step="1" placeholder="<?php esc_attr_e('Max price', 'tuturn'); ?>" id="tu-max-value" />
        </div>
      </div>
    </div>
    <?php if (empty($disable_range_slider)) { ?>
      <div class="tu-distanceholder">
        <div id="tu-rangecollapse" class="collapse">
          <div class="tu-distance">
            <div id="tu-rangeslider" class="tu-tooltiparrow tu-rangeslider"></div>
          </div>
        </div>
      </div>
    <?php } ?>
  </div>
</div>
<?php
$script = 'jQuery(document).on("ready", function ($) {
    jQuery(".tu-rangevalue").on("click",function() {
        jQuery("#tu-rangecollapse").collapse("show");
    });
     
    var stepsSlider = document.getElementById("tu-rangeslider");
   if (stepsSlider !== null) {
     var input0 = document.getElementById("tu-min-value");
     var input1 = document.getElementById("tu-max-value");
     var inputs = [input0, input1];
 
     noUiSlider.create(stepsSlider, {
       start: [' . esc_js($min_search_price) . ', ' . esc_js($max_search_price) . '],
       connect: true,
       range: {
         min: ' . esc_js($min_price) . ',
         max: ' . esc_js($max_price) . ',
       },
       format: {
         to: (v) => parseFloat(v).toFixed(0),
         from: (v) => parseFloat(v).toFixed(0),
         suffix: " (US $)",
       },
     });
     stepsSlider.noUiSlider.on("update", function(values, handle) {
       inputs[handle].value = values[handle];
     });
     inputs.forEach(function(input, handle) {
       input.addEventListener("change", function() {
         stepsSlider.noUiSlider.setHandle(handle, this.value);
       });
       input.addEventListener("keydown", function(e) {
         var values = stepsSlider.noUiSlider.get();
         var value = Number(values[handle]);
         var steps = stepsSlider.noUiSlider.steps();
         var step = steps[handle];
         var position;
         switch (e.which) {
           case 13:
             stepsSlider.noUiSlider.setHandle(handle, this.value);
             break;
           case 38:
             position = step[1];
             if (position === false) {
               position = 1;
             }
             if (position !== null) {
               stepsSlider.noUiSlider.setHandle(handle, value + position);
             }
           break;
           case 40:
             position = step[0];
             if (position === false) {
               position = 1;
             }
             if (position !== null) {
               stepsSlider.noUiSlider.setHandle(handle, value - position);
             }
           break;
         }
       });
     });
   }})';
wp_add_inline_script('nouislider', $script, 'after');
