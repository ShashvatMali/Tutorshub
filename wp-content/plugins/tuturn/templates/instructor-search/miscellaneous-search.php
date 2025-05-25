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
$myhome = !empty($_GET['myhome']) ? esc_html($_GET['myhome']) : '';
$studenthome = !empty($_GET['studenthome']) ? esc_html($_GET['studenthome']) : '';
$online_bookings = !empty($_GET['online']) ? ($_GET['online']) : '';
$teaching_type = !empty($_GET['teaching_type']) ? ($_GET['teaching_type']) : '';
$teach_settings = !empty($tuturn_settings['teach_settings']) ? $tuturn_settings['teach_settings'] : 'default';
$home_checked = !empty($myhome) ? esc_html('checked') : '';
$student_home = !empty($studenthome) ? esc_html('checked') : '';
$online = !empty($online_bookings) ? esc_html('checked') : '';
$offline_places = tuturn_offline_places_lists();
$location_rand = rand(10, 2000);
$toggle_expand = 'true';
$toggle_show_class = ' show';
if (!empty($teach_settings)) {
    $toggle_expand = 'true';
    $toggle_show_class = ' show';
}
?>
<div class="tu-aside-holder">
    <div class="tu-asidetitle" data-bs-toggle="collapse" data-bs-target="#side1ab" role="button" aria-expanded="true">
        <h5><?php esc_html_e('Miscellaneous', 'tuturn')?> </h5>
    </div>
    <div id="side1ab" class="collapse show">
        <div class="tu-aside-content">
            <ul class="tu-categoriesfilter">
                <?php if (!empty($teach_settings) && $teach_settings === 'default') {?>
                    <li>
                        <div class="tu-check tu-checksm">
                            <input name="myhome" type="checkbox" id="myhome" name="expcheck" <?php echo esc_attr($home_checked); ?>>
                            <label for="myhome"><?php esc_html_e('My home', 'tuturn')?> </label>
                        </div>
                    </li>
                    <li>
                        <div class="tu-check tu-checksm">
                            <input name="studenthome" type="checkbox" id="studenthome" name="expcheck"  <?php echo esc_attr($student_home); ?>>
                            <label for="studenthome"><?php esc_html_e('Student\'s home', 'tuturn')?> </label>
                        </div>
                    </li>
                    <li>
                        <div class="tu-check tu-checksm">
                            <input name="online" type="checkbox" id="online" name="expcheck"  <?php echo esc_attr($online); ?>>
                            <label for="online"><?php esc_html_e('Online', 'tuturn')?> </label>
                        </div>
                    </li>
                <?php } else if (!empty($teach_settings) && $teach_settings === 'custom') {
    $online = !empty($teaching_type) && in_array('online', $teaching_type) ? esc_html('checked') : '';
    $offline = !empty($teaching_type) && in_array('offline', $teaching_type) ? esc_html('checked') : '';
    ?>
                    <li>
                        <div class="tu-check tu-checksm">
                            <input class="tu-teaching_type-se" type="checkbox" id="online" name="teaching_type[]" value="online"  <?php echo esc_attr($online); ?>>
                            <label for="online"><?php esc_html_e('Online', 'tuturn')?> </label>
                        </div>
                    </li>
                    <li>
                        <div class="tu-check tu-checksm">
                            <input class="tu-teaching_type-se" type="checkbox" id="offline" name="teaching_type[]"  value="offline"  <?php echo esc_attr($offline); ?>>
                            <label for="offline"><?php esc_html_e('Offline', 'tuturn')?> </label>
                        </div>
                    </li>
                <?php }?>
            </ul>
        </div>
    </div>
</div>
<?php if (!empty($teach_settings) && $teach_settings === 'custom') {
    $offline_classs = 'd-none';
    if (!empty($teaching_type) && in_array('offline', $teaching_type)) {
        $offline_classs = '';
    }
    $db_offline_tutor_place = !empty($_GET['offline_type']) ? $_GET['offline_type'] : '';
    $tutor_class = !empty($db_offline_tutor_place) && in_array('tutor', $db_offline_tutor_place) ? '' : 'd-none';
    $tutor_distance = !empty($_GET['tutor_distance']) ? $_GET['tutor_distance'] : 2;
    $tutor_latitude = !empty($_GET['tutor_latitude']) ? $_GET['tutor_latitude'] : '';
    $tutor_longitude = !empty($_GET['tutor_longitude']) ? $_GET['tutor_longitude'] : '';
    $tutor_location = !empty($_GET['tutor_location']) ? $_GET['tutor_location'] : '';

    $tutor_country = !empty($_GET['tutor_country']) ? $_GET['tutor_country'] : '';
    $tutor_city = !empty($_GET['tutor_city']) ? $_GET['tutor_city'] : '';
    $tutor_state = !empty($_GET['tutor_state']) ? $_GET['tutor_state'] : '';

    $countries = tuturn_country_array();
    $countries = !empty($countries) ? $countries : array();
    $db_offline_tutr = 'd-none';
    $tutor_states = !empty($tutor_country) ? tuturn_country_array($tutor_country, '') : array();
    $rand = rand(10, 2000);
    $location_rand = rand(10, 2000);
    $state_country = 'tu-get-states';

    $radius_in          = esc_html__('Radius in miles', 'tuturn');
    $radius_in_type     = esc_html__('m', 'tuturn');
    $radius_type        = !empty( $tuturn_settings['defult_search_location_type'] ) ? $tuturn_settings['defult_search_location_type'] : 'miles';
    if(!empty($radius_type) && $radius_type == 'km'){
        $radius_in      = esc_html__('Radius in kilometer', 'tuturn');
        $radius_in_type  = esc_html__('km', 'tuturn');
    }
    ?>
    <div class="tu-aside-holder tu-custom-offline <?php echo esc_attr($offline_classs); ?>">
        <div class="tu-asidetitle" data-bs-toggle="collapse" data-bs-target="#side1ab-offline" role="button" aria-expanded="true">
            <h5><?php esc_html_e('Tuition place', 'tuturn')?> </h5>
        </div>
        <div id="side1ab-offline" class="collapse show">
            <div class="tu-aside-content">
                <ul class="tu-categoriesfilter">
                    <?php foreach ($offline_places as $plcace_key => $place_val) {?>
                        <li>
                            <div class="tu-check tu-checksm">
                                <input id="<?php echo esc_attr($plcace_key); ?>" type="checkbox" class="tu-offline_type-se" name="offline_type[]" value="<?php echo esc_attr($plcace_key); ?>" <?php if (!empty($db_offline_tutor_place) && in_array($plcace_key, $db_offline_tutor_place)) {echo esc_attr('checked');}?>>
                                <label for="<?php echo esc_attr($plcace_key); ?>"><?php echo esc_html($place_val); ?></label>
                            </div>
                        </li>
                    <?php }?>
                </ul>
            </div>
        </div>
    </div>
    <div class="tu-aside-holder tu-location-op <?php echo esc_attr($tutor_class); ?>">
        <div class="tu-asidetitle" data-bs-toggle="collapse" data-bs-target="#location-offline" role="button" aria-expanded="<?php echo esc_attr($toggle_expand); ?>">
            <h5><?php esc_html_e('Tuition location', 'tuturn')?> </h5>
        </div>
        <div id="location-offline" class="collapse show">
            <div class="tu-aside-content">
                <div class="tu-filterselect tu-location-op">
                    <div class="tu-select">
                        <select data-id="<?php echo intval($location_rand); ?>" class="form-control <?php echo esc_attr($state_country); ?>" name="tutor_country" data-placeholder="<?php esc_attr_e('Select Country from list', 'tuturn');?>">
                            <option value="" selected hidden disabled><?php esc_html_e('Choose country', 'tuturn');?></option>
                            <?php foreach ($countries as $key => $country) {?>
                                    <option <?php if (strtolower($tutor_country) == strtolower($key)) {echo esc_attr('selected');}?> value="<?php echo esc_attr(strtolower($key)) ?>"><?php echo esc_html($country) ?></option>
                            <?php }?>
                        </select>
                    </div>
                </div>
                <div class="tu-filterselect tu-location-op">
                    <div class="tu-select">
                        <select class="form-control tu-stat-<?php echo intval($location_rand); ?>" name="tutor_state" data-placeholder="<?php esc_attr_e('Select state from list', 'tuturn');?>">
                            <option value="" selected hidden disabled><?php esc_html_e('Choose state', 'tuturn');?></option>
                            <?php foreach ($tutor_states as $key => $state) {?>
                                    <option <?php if (strtolower($tutor_state) == strtolower($key)) {echo esc_attr('selected');}?> value="<?php echo esc_attr(strtolower($key)) ?>"><?php echo esc_html($state) ?></option>
                            <?php }?>
                        </select>
                    </div>
                </div>
                <div class="tu-filterselect tu-location-op">
                    <div class="tu-placeholderholder">
                        <input type="text" name="tutor_city" value="<?php echo esc_attr($tutor_city) ?>" class="form-control" placeholder="<?php esc_ATTR_e('Enter city', 'tuturn');?>">
                    </div>
                </div>

                <div class="tu-filterselect">
                    <div class="tu-input-holder">
                        <div class="tu-placeholderholder">
                            <input type="text" id="tu-locationinput-<?php echo intval($location_rand); ?>" name="tutor_location" value="<?php echo esc_attr($tutor_location); ?>" class="form-control" />
                        </div>
                        <input type="hidden" id="tu-latitude-<?php echo intval($location_rand); ?>" name="tutor_latitude" value="<?php echo floatval($tutor_latitude); ?>" >
                        <input type="hidden" id="tu-longitude-<?php echo intval($location_rand); ?>" name="tutor_longitude" value="<?php echo floatval($tutor_longitude); ?>" >
                    </div>
                </div>
                <div class="tu-distanceholder">
                    <div class="tu-rangeslider tu-tooltiparrow">
                        <span><?php echo esc_html($radius_in);?>  <em><?php echo esc_html($radius_in_type);?> </em>
                            <span class="example-val" id="slider1-span-<?php echo intval($location_rand); ?>"></span>
                            <input type="hidden" id="tu-distance-<?php echo intval($location_rand); ?>" name="tutor_distance" value="<?php echo intval($tutor_distance); ?>" >
                        </span>
                        <div id="tu-rangeslidertwo-<?php echo intval($location_rand); ?>"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
$tutor_script = "
jQuery(document).ready(function () {
    google.maps.event.addDomListener(window, 'load', initialize);
});

 function initialize() {
    var input = document.getElementById('tu-locationinput-" . esc_js($location_rand) . "');
    var autocomplete = new google.maps.places.Autocomplete(input);
    google.maps.event.addListener(autocomplete, 'place_changed', function () {
        var place = autocomplete.getPlace();
        document.getElementById('tu-latitude-" . esc_js($location_rand) . "').value = place.geometry.location.lat();
        document.getElementById('tu-longitude-" . esc_js($location_rand) . "').value = place.geometry.location.lng();
    });
}
";
    wp_add_inline_script('googleapis', $tutor_script, 'after');
    $tutor_script = "
jQuery(document).ready(function () {
    google.maps.event.addDomListener(window, 'load', initialize);
});

 function initialize() {
    var input = document.getElementById('tu-locationinput-" . esc_js($location_rand) . "');
    var autocomplete = new google.maps.places.Autocomplete(input);
    google.maps.event.addListener(autocomplete, 'place_changed', function () {
        var place = autocomplete.getPlace();
        document.getElementById('tu-latitude-" . esc_js($location_rand) . "').value = place.geometry.location.lat();
        document.getElementById('tu-longitude-" . esc_js($location_rand) . "').value = place.geometry.location.lng();
    });
}
var softSlider = document.getElementById('tu-rangeslidertwo-" . esc_js($location_rand) . "');
if (softSlider != null) {
    noUiSlider.create(softSlider, {
        start: " . esc_js($tutor_distance) . ",
        connect: 'lower',
        step: 1,
        range: {
            min: 0,
            max: 500,
        },
        format: wNumb({
            decimals: 0,
        }),
    });
    var slider1Value = document.getElementById('slider1-span-" . esc_js($location_rand) . "');
    softSlider.noUiSlider.on('update', function (values, handle) {
        slider1Value.innerHTML = values[handle];
        document.getElementById('tu-distance-". esc_js($location_rand) ."').value = values[handle];

    });
}
";
    wp_add_inline_script('nouislider', $tutor_script, 'after');
}?>