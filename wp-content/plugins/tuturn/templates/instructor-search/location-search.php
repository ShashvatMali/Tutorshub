<?php
/**
 * @package     Tuturn
 * @subpackage  Tuturn/templates
 * @author      Amentotech <info@amentotech.com>
 * @link        https://themeforest.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
 */
global $tuturn_settings;

if (!empty($args) && is_array($args)) {
    extract($args);
}
$toggle_expand = 'true';
$toggle_show_class = ' show';
if (!empty($location) || !empty($latitude) || !empty($longitude)) {
    $toggle_expand = 'true';
    $toggle_show_class = ' show';
}
$countries = tuturn_country_array();
$countries = !empty($countries) ? $countries : array();
$state_option = !empty($tuturn_settings['profile_state']) ? $tuturn_settings['profile_state'] : false;
$country_region = !empty($_GET['country']) ? $_GET['country'] : '';
$state_country = '';
$country_state = '';
$country_city = '';
if (!empty($state_option)) {
    $country_state = !empty($_GET['state']) ? $_GET['state'] : '';
    $state_country = 'tu-get-states';
}

$country_city = !empty($_GET['city']) ? $_GET['city'] : '';
$states = !empty($country_region) ? tuturn_country_array($country_region, '') : array();
$states = !empty($states) ? $states : array();
$rand = rand(100, 2000);


$radius_in          = esc_html__('Radius in miles', 'tuturn');
$radius_in_type     = esc_html__('m', 'tuturn');
$radius_type        = !empty( $tuturn_settings['defult_search_location_type'] ) ? $tuturn_settings['defult_search_location_type'] : 'miles';
if(!empty($radius_type) && $radius_type == 'km'){
    $radius_in          = esc_html__('Radius in kilometer', 'tuturn');
    $radius_in_type     = esc_html__('km', 'tuturn');
}
?>
<div class="tu-aside-holder">
    <div class="tu-asidetitle" data-bs-toggle="collapse" data-bs-target="#tu-location" role="button" aria-expanded="<?php echo esc_attr($toggle_expand); ?>">
        <h5><?php esc_html_e('Tutor location', 'tuturn')?> </h5>
    </div>
    <div id="tu-location" class="collapse<?php echo esc_attr($toggle_show_class) ?>">
        <div class="tu-aside-content">
            <div class="tu-filterselect">
                <div class="tu-select">
                    <select id="tu_country" data-id="<?php echo intval($rand); ?>" class="form-control <?php echo esc_attr($state_country); ?>" name="country" data-placeholder="<?php esc_attr_e('Select Country', 'tuturn');?>">
                        <option value="" selected hidden disabled><?php esc_html_e('Choose country', 'tuturn');?></option>
                        <?php foreach ($countries as $key => $country) {?>
                                <option <?php if (strtolower($country_region) == strtolower($key)) {echo esc_attr('selected');}?> value="<?php echo esc_attr(strtolower($key)) ?>"><?php echo esc_html($country) ?></option>
                        <?php }?>
                    </select>
                </div>
            </div>
            <?php if (!empty($state_option)) {?>
                <div class="tu-filterselect tu-state-list">
                    <div class="tu-select">
                        <select class="form-control tu-stat-<?php echo intval($rand); ?>" name="state" data-placeholder="<?php esc_attr_e('Select state from list', 'tuturn');?>">
                            <option value="" selected hidden disabled><?php esc_html_e('Choose state', 'tuturn');?></option>
                            <?php foreach ($states as $key => $state) {?>
                                    <option <?php if (strtolower($country_state) == strtolower($key)) {echo esc_attr('selected');}?> value="<?php echo esc_attr(strtolower($key)) ?>"><?php echo esc_html($state) ?></option>
                            <?php }?>
                        </select>
                    </div>
                </div>
                <div class="tu-filterselect">
                    <div class="tu-placeholderholder">
                        <input type="text" name="city" value="<?php echo esc_attr($country_city) ?>" class="form-control" placeholder="<?php esc_attr_e('Enter City', 'tuturn');?>">
                    </div>
                </div>
            <?php }?>
            <div class="tu-filterselect">
                <div class="tu-input-holder">
                    <div class="tu-placeholderholder">
                        <input type="text" id="tu-locationinput" name="location" value="<?php echo esc_attr($location); ?>" class="form-control" placeholder="<?php esc_attr_e('Enter address or zipcode', 'tuturn');?>"/>
                    </div>
                    <input type="hidden" id="tu-latitude" name="latitude" value="<?php echo floatval($latitude); ?>" >
                    <input type="hidden" id="tu-longitude" name="longitude" value="<?php echo floatval($longitude); ?>" >
                </div>
            </div>
            <div class="tu-distanceholder">
                <div class="tu-rangeslider tu-tooltiparrow">
                    <span><?php echo esc_html($radius_in);?>  <em><?php echo esc_html($radius_in_type);?> </em>
                        <span class="example-val" id="slider1-span"></span>
                        <input type="hidden" id="tu-distance" name="distance" value="<?php echo intval($distance); ?>" ></span>
                    <div id="tu-rangeslidertwo"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$script = "
jQuery(document).ready(function () {
    google.maps.event.addDomListener(window, 'load', initialize);
});

 function initialize() {
    var input = document.getElementById('tu-locationinput');
    var autocomplete = new google.maps.places.Autocomplete(input);
    google.maps.event.addListener(autocomplete, 'place_changed', function () {
        var place = autocomplete.getPlace();
        document.getElementById('tu-latitude').value = place.geometry.location.lat();
        document.getElementById('tu-longitude').value = place.geometry.location.lng();
    });
}
";
wp_add_inline_script('googleapis', $script, 'after');
$script = "
jQuery(document).ready(function () {
    google.maps.event.addDomListener(window, 'load', initialize);
});

 function initialize() {
    var input = document.getElementById('tu-locationinput');
    var autocomplete = new google.maps.places.Autocomplete(input);
    google.maps.event.addListener(autocomplete, 'place_changed', function () {
        var place = autocomplete.getPlace();
        document.getElementById('tu-latitude').value = place.geometry.location.lat();
        document.getElementById('tu-longitude').value = place.geometry.location.lng();
    });
}
var softSlider = document.getElementById('tu-rangeslidertwo');
if (softSlider != null) {
    noUiSlider.create(softSlider, {
        start: " . esc_js($distance) . ",
        connect: 'lower',
        step: 1,
        range: {
            min: 0,
            max: 9000,
        },
        format: wNumb({
            decimals: 0,
        }),
    });
    var slider2Value = document.getElementById('slider1-span');
    softSlider.noUiSlider.on('update', function (values, handle) {
        slider2Value.innerHTML = values[handle];
        document.getElementById('tu-distance').value = values[handle];

    });
}
";
wp_add_inline_script('nouislider', $script, 'after');
