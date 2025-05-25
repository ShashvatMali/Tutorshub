<?php

/**
 * Instructor personal detail
 *
 * @package     Tuturn
 * @subpackage  Tuturn/Templates/Profile_Settings/Instructor
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
 */
global $tuturn_settings;
$geocoding_option    = !empty($tuturn_settings['geocoding_option']) ? $tuturn_settings['geocoding_option'] : 'no';
$first_name        = !empty($profile_details['first_name']) ? $profile_details['first_name'] : '';
$last_name        = !empty($profile_details['last_name']) ? $profile_details['last_name'] : '';
$phone_number		= ! empty($profile_details['phone_number']) ? $profile_details['phone_number'] : '';
$tagline        = !empty($profile_details['tagline']) ? $profile_details['tagline'] : '';
$country_region = get_post_meta($profile_id, '_country_region', true);
$zipcode         = get_post_meta($profile_id, '_zipcode', true);
$address         = get_post_meta($profile_id, '_address', true);
$teaching_preference    = get_post_meta($profile_id, 'teaching_preference', true);
$teaching_preference    = !empty($teaching_preference) ? ($teaching_preference) : array();
$countries         = tuturn_country_array();
$countries        = !empty($countries) ? $countries : array();
$user_languages    = !empty($profile_details['languages']) ? $profile_details['languages'] : array();
$arguments      =  array(
    'taxonomy'      => 'languages',
    'hide_empty'    => false,
);
$terms          = get_terms($arguments);
$introduction   = get_post_field('post_content', $profile_id);
$state_option   = !empty($tuturn_settings['profile_state']) ? $tuturn_settings['profile_state'] : false;
$profile_gender = !empty($tuturn_settings['profile_gender']) ? $tuturn_settings['profile_gender'] : false;
$profile_grade  = !empty($tuturn_settings['profile_grade']) ? $tuturn_settings['profile_grade'] : false;

$profile_hourlyprice    = !empty($tuturn_settings['profile_hourlyprice']) ? $tuturn_settings['profile_hourlyprice'] : false;
$teach_settings         = !empty($tuturn_settings['teach_settings']) ? $tuturn_settings['teach_settings'] : 'default';

$state_country  = '';
$address_colms  = 'form-group-half';
$city           = '';
$selected_state = '';
$country_state  = '';
$country_city   = '';
if (!empty($state_option)) {
    $address_colms  = 'form-group-3half';
    $state_country  = 'tu-get-states';
    $country_state  = get_post_meta($profile_id, '_state', true);
    $country_state  = !empty($country_state) ? $country_state : '';
    $country_city   = get_post_meta($profile_id, '_city', true);
    $country_city   = !empty($country_city) ? $country_city : '';
}
$states             = !empty($country_region) ? tuturn_country_array($country_region, '') : array();
$states             = !empty($states) ? $states : array();

$listgender         = array();
$selected_gender    = '';
$grade              = '';
if (!empty($profile_gender)) {
    $listgender         = tuturn_get_gender_lists();
    $gender             = get_post_meta($profile_id, '_gender', true);
    $selected_gender    = !empty($gender) ? $gender : '';
}

if (!empty($profile_grade)) {
    $grade              = get_post_meta($profile_id, '_grade', true);
    $grade              = !empty($grade) ? $grade : '';
}

$offline_places     = tuturn_offline_places_lists();
$db_offline_radio   = 'd-none';
$db_offline_tutr    = 'd-none';
$db_offline_tutor_place = '';
$tutor_country          = '';
$tutor_states           = array();
$tutor_address          = '';
$tutor_city             = '';
if (!empty($teach_settings) && $teach_settings === 'custom' && !empty($teaching_preference) && is_array($teaching_preference) && in_array('offline', $teaching_preference)) {
    $db_offline_radio       = '';
    $db_offline_tutor_place = get_post_meta($profile_id, 'offline_place', true);
    $db_offline_tutor_place = !empty($db_offline_tutor_place) ? ($db_offline_tutor_place) : array();
    if (!empty($db_offline_tutor_place) && is_array($db_offline_tutor_place) && in_array('tutor', $db_offline_tutor_place)) {
        $tutor_country  = get_post_meta($profile_id, '_tutor_country', true);
        $tutor_address  = get_post_meta($profile_id, '_tutor_address', true);
        $tutor_city     = get_post_meta($profile_id, '_tutor_city', true);
        $tutor_state    = get_post_meta($profile_id, '_tutor_state', true);
        $tutor_states   = !empty($tutor_country) ? tuturn_country_array($tutor_country, '') : array();
        $db_offline_tutr    = '';
    }
}
$db_offline_place   = '';

$rand               = rand(10, 2000);
$location_rand      = rand(10, 2000);
?>
<div class="tu-boxarea">
    <div class="tu-boxsm">
        <div class="tu-boxsmtitle">
            <h4><?php esc_html_e('My Profile', 'tuturn'); ?></h4>
        </div>
    </div>
    <div class="tu-box tu-settingform">
        <fieldset>
            <div class="tu-themeform__wrap">
                <div class="form-group-wrap">
                    <div class="form-group form-group-half">
                        <label class="tu-label"><?php esc_html_e('First name', 'tuturn'); ?></label>
                        <div class="tu-placeholderholder">
                            <input type="text" name="first_name" value="<?php echo esc_attr($first_name); ?>" class="form-control" required placeholder="<?php esc_html_e('Your first name', 'tuturn'); ?>">
                        </div>
                    </div>
                    <div class="form-group form-group-half">
                        <label class="tu-label"><?php esc_html_e('Last name', 'tuturn'); ?></label>
                        <div class="tu-placeholderholder">
                            <input type="text" name="last_name" value="<?php echo esc_attr($last_name); ?>" class="form-control" required placeholder="<?php esc_html_e('Your last name', 'tuturn'); ?>">
                        </div>
                    </div>
                    <?php if (!empty($profile_gender)) { ?>
                        <div class="form-group form-group-half">
                            <label class="tu-label"><?php esc_html_e('Gender', 'tuturn'); ?></label>
                            <div class="tu-select">
                                <select class="form-control" name="gender" data-placeholder="<?php esc_attr_e('Select gender', 'tuturn'); ?>" required>
                                    <option value="" selected hidden disabled><?php esc_html_e('Choose gender', 'tuturn'); ?></option>
                                    <?php foreach ($listgender as $key => $value) { ?>
                                        <option <?php if (strtolower($selected_gender) == strtolower($key)) {
                                                    echo esc_attr('selected');
                                                } ?> value="<?php echo esc_attr(strtolower($key)) ?>"><?php echo esc_html($value) ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if (!empty($profile_grade)) { ?>
                        <div class="form-group form-group-half">
                            <label class="tu-label"><?php esc_html_e('Grade', 'tuturn'); ?></label>
                            <div class="tu-placeholderholder">
                                <input type="text" name="grade" value="<?php echo esc_html($grade); ?>" class="form-control" required placeholder="<?php esc_html_e('Your grade', 'tuturn'); ?>">
                            </div>
                        </div>
                    <?php } ?>
                    <?php do_action('tuturn_display_tutor_price', $profile_id); ?>
                    <div class="form-group form-group-half">
                        <label class="tu-label"><?php esc_html_e('Your tagline', 'tuturn'); ?></label>
                        <div class="tu-placeholderholder">
                            <input type="text" name="tagline" value="<?php echo esc_attr($tagline); ?>" class="form-control" required placeholder="<?php esc_html_e('Add your tagline', 'tuturn'); ?>">
                        </div>
                    </div>


                    <div class="form-group form-group-3half">
                        <label class="tu-label"><?php esc_html_e('Country', 'tuturn'); ?></label>
                        <div class="tu-select">
                            <select id="tu_country" data-id="<?php echo intval($rand); ?>" class="form-control <?php echo esc_attr($state_country); ?>" name="country" data-placeholder="<?php esc_attr_e('Select Country from list', 'tuturn'); ?>" required>
                                <option value="" selected hidden disabled><?php esc_html_e('Choose country', 'tuturn'); ?></option>
                                <?php foreach ($countries as $key => $country) { ?>
                                    <option <?php if (strtolower($country_region) == strtolower($key)) {
                                                echo esc_attr('selected');
                                            } ?> value="<?php echo esc_attr(strtolower($key)) ?>"><?php echo esc_html($country) ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <?php if (!empty($state_option)) { ?>
                        <div class="form-group form-group-3half tu-state-list">
                            <label class="tu-label"><?php esc_html_e('State', 'tuturn'); ?></label>
                            <div class="tu-select">
                                <select id="tu_state" class="form-control tu-stat-<?php echo intval($rand); ?>" name="state" data-placeholder="<?php esc_attr_e('Select state from list', 'tuturn'); ?>">
                                    <option value="" selected hidden disabled><?php esc_html_e('Choose state', 'tuturn'); ?></option>
                                    <?php foreach ($states as $key => $state) { ?>
                                        <option <?php if (strtolower($country_state) == strtolower($key)) {
                                                    echo esc_attr('selected');
                                                } ?> value="<?php echo esc_attr(strtolower($key)) ?>"><?php echo esc_html($state) ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group form-group-3half">
                            <label class="tu-label"><?php esc_html_e('City', 'tuturn'); ?></label>
                            <div class="tu-placeholderholder">
                                <input type="text" name="city" value="<?php echo esc_attr($country_city) ?>" class="form-control" placeholder="<?php esc_html_e('Enter your city', 'tuturn'); ?>">
                            </div>
                        </div>
                    <?php } ?>
                    <div class="form-group <?php echo esc_attr($address_colms); ?>">
                        <label class="tu-label"><?php esc_html_e('Address', 'tuturn'); ?></label>
                        <div class="tu-placeholderholder">
                            <input type="text" name="address" value="<?php echo esc_attr($address) ?>" class="form-control" placeholder="<?php esc_html_e('Enter your address', 'tuturn'); ?>" required>
                        </div>
                    </div>
                    <div class="form-group <?php echo esc_attr($address_colms); ?>">
                        <label class="tu-label"><?php esc_html_e('Zipcode', 'tuturn'); ?></label>
                        <div class="tu-placeholderholder">
                            <input type="text" name="zipcode" value="<?php echo esc_attr($zipcode); ?>" class="form-control" required placeholder="<?php esc_html_e('Enter zipcode', 'tuturn'); ?>">
                        </div>
                    </div>
                    <?php if (!empty($args['package_info']['languages'])) { ?>
                        <div class="form-group">
                            <label class="tu-label"><?php esc_html_e('Languages', 'tuturn'); ?></label>
                            <div class="tu-select">
                                <select id="tu-languages" name="languages" data-placeholder="<?php esc_attr_e('Select languages you know', 'tuturn'); ?>" class="form-control" required>
                                    <option label="<?php esc_attr_e('Select languages you know', 'tuturn'); ?>"></option>
                                    <?php foreach ($terms as $term) { ?>
                                        <option value="<?php echo esc_attr($term->slug); ?>"><?php echo esc_html($term->name) ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <ul class="tu_wrappersortable tu-labels">
                                <?php if (!empty($user_languages) && is_array($user_languages)) { ?>
                                    <?php foreach ($user_languages as $index => $language) { ?>
                                        <li id="<?php echo esc_attr($index); ?>">
                                            <span><?php echo esc_html($language); ?> <a href="javascript:void(0);" data-slug="<?php echo esc_attr($index); ?>" class="select2-remove-item"><i class="icon icon-x"></i></a></span>
                                            <input type="hidden" data-slug="<?php echo esc_attr($index) ?>" value="<?php echo esc_attr($index); ?>" name="languages[]">
                                        </li>
                                    <?php } ?>
                                <?php } ?>
                            </ul>
                        </div>
                    <?php } ?>
                    <div class="form-group">
                        <label class="tu-label"><?php esc_html_e('Teaching Location', 'tuturn'); ?></label>
                        <ul class="tu-status-filter">
                            <?php if (!empty($teach_settings) && $teach_settings === 'default') { ?>
                                <li class="teaching-preference-home">
                                    <div class="tu-status-contnent">
                                        <div class="tu-check tu-checksm">
                                            <input id="home" type="checkbox" name="teaching_preference[]" value="home" <?php if (!empty($teaching_preference) && is_array($teaching_preference) && in_array('home', $teaching_preference)) {
                                                                                                                            echo esc_attr('checked');
                                                                                                                        } ?>>
                                            <label for="home"><?php esc_html_e('My home', 'tuturn'); ?></label>
                                        </div>
                                    </div>
                                </li>
                                <li class="teaching-preference-student">
                                    <div class="tu-status-contnent">
                                        <div class="tu-check tu-checksm">
                                            <input id="student_home" type="checkbox" name="teaching_preference[]" value="student_home" <?php if (!empty($teaching_preference) && is_array($teaching_preference) && in_array('student_home', $teaching_preference)) {
                                                                                                                                            echo esc_attr('checked');
                                                                                                                                        } ?>>
                                            <label for="student_home"><?php esc_html_e('Student\'s home', 'tuturn'); ?></label>
                                        </div>
                                    </div>
                                </li>
                                <li class="teaching-preference-online">
                                    <div class="tu-status-contnent">
                                        <div class="tu-check tu-checksm">
                                            <input id="online" type="checkbox" name="teaching_preference[]" value="online" <?php if (!empty($teaching_preference) && is_array($teaching_preference) && in_array('online', $teaching_preference)) {
                                                                                                                                echo esc_attr('checked');
                                                                                                                            } ?>>
                                            <label for="online"><?php esc_html_e('Online', 'tuturn'); ?></label>
                                        </div>
                                    </div>
                                </li>
                            <?php } else if (!empty($teach_settings) && $teach_settings === 'custom') { ?>
                                <li  class="teaching-preference-online">
                                    <div class="tu-status-contnent">
                                        <div class="tu-check tu-checksm">
                                            <input id="online" class="tu-teaching_type-se" type="checkbox" name="teaching_preference[]" value="online" <?php if (!empty($teaching_preference) && is_array($teaching_preference) && in_array('online', $teaching_preference)) {
                                                                                                                                                            echo esc_attr('checked');
                                                                                                                                                        } ?>>
                                            <label for="online"><?php esc_html_e('Online', 'tuturn'); ?></label>
                                        </div>
                                    </div>
                                </li>
                                <li class="teaching-preference-offline">
                                    <div class="tu-status-contnent">
                                        <div class="tu-check tu-checksm">
                                            <input id="offline" class="tu-teaching_type-se" type="checkbox" name="teaching_preference[]" value="offline" <?php if (!empty($teaching_preference) && is_array($teaching_preference) && in_array('offline', $teaching_preference)) {
                                                                                                                                                                echo esc_attr('checked');
                                                                                                                                                            } ?>>
                                            <label for="offline"><?php esc_html_e('Offline', 'tuturn'); ?></label>
                                        </div>
                                    </div>
                                </li>
                            <?php } ?>

                        </ul>
                    </div>
                    <?php if (!empty($teach_settings) && $teach_settings === 'custom' && !empty($offline_places)) { ?>
                        <div class="form-group tu-custom-offline <?php echo esc_attr($db_offline_radio); ?>">
                            <label class="tu-label"><?php esc_html_e('Offline place', 'tuturn'); ?></label>
                            <ul class="tu-status-filter">
                                <?php foreach ($offline_places as $plcace_key => $place_val) { ?>
                                    <li>
                                        <div class="tu-status-contnent">
                                            <div class="tu-check tu-checksm">
                                                <input class="tu-offline_type-se" id="<?php echo esc_attr($plcace_key); ?>" type="checkbox" name="offline_place[]" value="<?php echo esc_attr($plcace_key); ?>" <?php if (!empty($db_offline_tutor_place) && is_array($db_offline_tutor_place) && in_array($plcace_key, $db_offline_tutor_place)) {
                                                echo esc_attr('checked');
                                            } ?>>
                                                <label for="<?php echo esc_attr($plcace_key); ?>"><?php echo esc_html($place_val); ?></label>
                                            </div>
                                        </div>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                        <?php if ($geocoding_option === 'yes') { ?>
                            <div class="form-group form-group-3half tu-location-op <?php echo esc_attr($db_offline_tutr); ?>">
                                <label class="tu-label"><?php esc_html_e('Country', 'tuturn'); ?></label>
                                <div class="tu-select">
                                    <select data-id="<?php echo intval($location_rand); ?>" class="form-control <?php echo esc_attr($state_country); ?>" name="tutor_country" data-placeholder="<?php esc_attr_e('Select Country from list', 'tuturn'); ?>" required>
                                        <option value="" selected hidden disabled><?php esc_html_e('Choose country', 'tuturn'); ?></option>
                                        <?php foreach ($countries as $key => $country) { ?>
                                            <option <?php if (strtolower($tutor_country) == strtolower($key)) {
                                                        echo esc_attr('selected');
                                                    } ?> value="<?php echo esc_attr(strtolower($key)) ?>"><?php echo esc_html($country) ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group form-group-3half tu-location-op <?php echo esc_attr($db_offline_tutr); ?>">
                                <label class="tu-label"><?php esc_html_e('State', 'tuturn'); ?></label>
                                <div class="tu-select">
                                    <select class="form-control tu-stat-<?php echo intval($location_rand); ?>" name="tutor_state" data-placeholder="<?php esc_attr_e('Select state from list', 'tuturn'); ?>">
                                        <option value="" selected hidden disabled><?php esc_html_e('Choose state', 'tuturn'); ?></option>
                                        <?php foreach ($tutor_states as $key => $state) { ?>
                                            <option <?php if (strtolower($tutor_state) == strtolower($key)) {
                                                        echo esc_attr('selected');
                                                    } ?> value="<?php echo esc_attr(strtolower($key)) ?>"><?php echo esc_html($state) ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group form-group-3half tu-location-op <?php echo esc_attr($db_offline_tutr); ?>">
                                <label class="tu-label"><?php esc_html_e('City', 'tuturn'); ?></label>
                                <div class="tu-placeholderholder">
                                    <input type="text" name="tutor_city" value="<?php echo esc_attr($tutor_city) ?>" class="form-control" placeholder="<?php esc_html_e('Enter your city', 'tuturn'); ?>">
                                </div>
                            </div>
                            <div class="form-group form-group-3half tu-location-op <?php echo esc_attr($db_offline_tutr); ?>">
                                <label class="tu-label"><?php esc_html_e('Address', 'tuturn'); ?></label>
                                <div class="tu-placeholderholder">
                                    <input type="text" name="tutor_address" value="<?php echo esc_attr($tutor_address) ?>" class="form-control" placeholder="Enter your address">
                                </div>
                            </div>
                            <div class="form-group form-group-3half tu-location-op <?php echo esc_attr($db_offline_tutr); ?>">
                                <label class="tu-label"><?php esc_html_e('Zipcode', 'tuturn'); ?></label>
                                <div class="tu-placeholderholder">
                                    <input type="text" name="tutor_zipcode" value="<?php echo esc_attr($zipcode); ?>" class="form-control" required placeholder="<?php esc_html_e('Enter zipcode', 'tuturn'); ?>">
                                </div>
                            </div>
                        <?php } ?>
                    <?php } ?>
                    <div class="form-group">
                        <label class="tu-label"><?php esc_html_e('A brief introduction', 'tuturn'); ?></label>
                        <div class="tu-placeholderholder">
                            <?php
                            $editor_id = 'profile_introduction';
                            $settings =   array(
                                'wpautop'       => false,
                                'media_buttons' => false,
                                'textarea_name' => 'brief_introduction',
                                'textarea_rows' => 100,
                                'textarea_cols' => 200,
                                'tabindex'      => '',
                                'editor_css'    => '',
                                'editor_class'  => '',
                                'teeny'         => false,
                                'dfw'           => false,
                                'tinymce'       => true,
                                'quicktags'     => true,
                                'editor_height' => 400,
                            );
                            wp_editor($introduction, $editor_id, $settings);
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>
    </div>
</div>
<?php
$script = "
jQuery(document).on('ready', function(){
    openSelect2('languages');
    
});
";
wp_add_inline_script('tuturn-profile-settings', $script, 'before');
