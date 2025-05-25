<?php

/**
 * Identity verification
 *
 * @package     Tuturn
 * @subpackage  Tuturn/templates/dashboard/student
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
 */
global $tuturn_settings, $current_user;
if (!empty($args) && is_array($args)) {
    extract($args);
}

$verification_terms     = !empty($tuturn_settings['verification_terms']) ? $tuturn_settings['verification_terms'] : '';
$parental_consent       = !empty($tuturn_settings['parental_consent']) ? $tuturn_settings['parental_consent'] : 'no';
$student_fields         = !empty($tuturn_settings['student_fields']) ? $tuturn_settings['student_fields'] : array();

$user_identity          = intval($current_user->ID);
$profile_id             = tuturn_get_linked_profile_id($current_user->ID);
$profile_details        = get_post_meta($profile_id, 'profile_details', true);

$user_verification      = get_user_meta($user_identity, 'user_verification', true);
$identity_verified      = !empty($user_verification) ? $user_verification : '';
$fileImage              = TUTURN_GlobalSettings::get_plugin_url() . 'public/images/file.svg';
$userType               = apply_filters('tuturnGetUserType', $user_identity);
$listgender             = tuturn_get_gender_lists();

$post_id                    = !empty($_GET['post_id']) ? intval($_GET['post_id']) : 0;
$gender                     = '';
$address                    = '';
$contact_number             = '';
$other_introduction         = '';
$personal_photo             = array();
$personal_photo_class       = "d-none";
$attachments                = array();
$school                     = '';
$parent_name                = '';
$phone_number                = '';
$parent_email               = '';
$parent_phone               = '';
$attribute_val              = "";
$verification_number        = '';

$first_name        = !empty($profile_details['first_name']) ? $profile_details['first_name'] : '';
$last_name        = !empty($profile_details['last_name']) ? $profile_details['last_name'] : '';
$student_name   = $first_name . ' ' . $last_name;
$email          = !empty($current_user->user_email) ? $current_user->user_email : '';

if (!empty($post_id)) {
    $attribute_val              = "disabled";
    $verification_data          = get_post_meta($post_id, 'verification_info', true);
    $verification_info          = !empty($verification_data['info']) ? $verification_data['info'] : array();
    $student_name               = !empty($verification_info['name']) ? $verification_info['name'] : '';
    $email                      = !empty($verification_info['email_address']) ? $verification_info['email_address'] : '';
    $gender                     = !empty($verification_info['gender']) ? $verification_info['gender'] : '';
    $school                     = !empty($verification_info['school']) ? $verification_info['school'] : '';
    $address                    = !empty($verification_info['address']) ? $verification_info['address'] : '';
    $other_introduction         = !empty($verification_info['other_introduction']) ? $verification_info['other_introduction'] : '';
    $contact_number             = !empty($verification_info['student_number']) ? $verification_info['student_number'] : '';
    $phone_number               = !empty($verification_info['phone_number']) ? $verification_info['phone_number'] : '';
    $verification_number        = !empty($verification_info['verification_number']) ? $verification_info['verification_number'] : '';
    $personal_photo             = !empty($verification_data['personal_photo'][0]) ? $verification_data['personal_photo'][0] : array();
    $personal_photo_class       = !empty($personal_photo) ? '' : $personal_photo_class;
    $attachments                = !empty($verification_data['attachments']) ? $verification_data['attachments'] : array();
    $parent_name                = !empty($verification_info['parent_name']) ? $verification_info['parent_name'] : '';
    $parent_phone               = !empty($verification_info['parent_phone']) ? $verification_info['parent_phone'] : '';
    $parent_email               = !empty($verification_info['parent_email']) ? $verification_info['parent_email'] : '';
}
?>
<div class="tu-identity-verification">
    <div class="tu-boxwrapper">
        <div class="tu-boxtitle">
            <h3><?php esc_html_e('Identity verification', 'tuturn'); ?></h3>
        </div>
        <div class="tu-box">
            <form class="tu-themeform tu-dhbform" id="tu-verification-required">
                <fieldset>
                    <div class="tu-themeform__wrap">
                        <div class="form-group-wrap">
                            <div class="form-group form-group-half">
                                <label class="tu-label"><?php esc_html_e('Name', 'tuturn'); ?></label>
                                <div class="tu-placeholderholder">
                                    <input type="text" <?php echo esc_attr($attribute_val); ?> name="name" value="<?php echo esc_attr($student_name); ?>" class="form-control" placeholder=" " required>
                                    <div class="tu-placeholder">
                                        <span><?php esc_html_e('Enter your full name', 'tuturn'); ?></span>
                                        <em>*</em>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group form-group-half">
                                <label class="tu-label"><?php esc_html_e('Email', 'tuturn'); ?></label>
                                <div class="tu-placeholderholder">
                                    <input type="text" <?php echo esc_attr($attribute_val); ?> name="email_address" value="<?php echo esc_attr($email); ?>" class="form-control" placeholder=" " required>
                                    <div class="tu-placeholder">
                                        <span><?php esc_html_e('Enter your email address', 'tuturn'); ?></span>
                                        <em>*</em>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group form-group-half">
                                <label class="tu-label"><?php esc_html_e('Phone number', 'tuturn'); ?></label>
                                <div class="tu-placeholderholder">
                                    <input type="text" <?php echo esc_attr($attribute_val); ?> name="phone_number" class="form-control" value="<?php echo esc_attr($phone_number); ?>" placeholder="<?php esc_html_e('Enter phone number', 'tuturn'); ?>">
                                </div>
                            </div>
                            <div class="form-group form-group-half">
                                <label class="tu-label"><?php esc_html_e('Gender', 'tuturn'); ?></label>
                                <div class="tu-select">
                                    <select class="form-control" <?php echo esc_attr($attribute_val); ?> name="gender" data-placeholder="<?php esc_attr_e('Select gender', 'tuturn'); ?>" required>
                                        <option value=""><?php esc_html_e('Choose gender', 'tuturn'); ?></option>
                                        <?php foreach ($listgender as $key => $value) {
                                            $selected   = "";
                                            if (!empty($gender) && $gender === $key) {
                                                $selected   = "selected";
                                            }
                                        ?>
                                            <option <?php echo esc_attr($selected); ?> value="<?php echo esc_attr(strtolower($key)) ?>"><?php echo esc_html($value) ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group form-group-half">
                                <label class="tu-label"><?php esc_html_e('CNIC/Passport/NIN/SSN', 'tuturn'); ?></label>
                                <div class="tu-placeholderholder">
                                    <input type="text" name="verification_number" value="<?php echo esc_html($verification_number); ?>" class="form-control" placeholder="<?php esc_attr_e('Enter CNIC/Passport/NIN/SSN', 'tuturn'); ?>">
                                </div>
                            </div>
                            <div class="form-group  form-group-half">
                                <label class="tu-label"><?php esc_html_e('Address', 'tuturn'); ?></label>
                                <div class="tu-placeholderholder">
                                    <input type="text" <?php echo esc_attr($attribute_val); ?> name="address" value="<?php echo esc_attr($address); ?>" class="form-control" placeholder="<?php esc_attr_e('Enter your address', 'tuturn'); ?>">
                                </div>
                            </div>
                            <!-- Only for student -->
                            <?php if (($parental_consent === 'yes') && ($userType === 'student')) {
                                if (!empty($student_fields)) {
                                    if (in_array('student_id', $student_fields)) { ?>
                                        <div class="form-group form-group-half">
                                            <label class="tu-label"><?php esc_html_e('Student id/number', 'tuturn'); ?></label>
                                            <div class="tu-placeholderholder">
                                                <input type="text" <?php echo esc_attr($attribute_val); ?> name="student_number" class="form-control" value="<?php echo esc_attr($contact_number); ?>" placeholder="<?php esc_html_e('Enter student number', 'tuturn'); ?>">
                                            </div>
                                        </div>
                                    <?php }
                                    if (in_array('school', $student_fields)) {
                                    ?>
                                        <div class="form-group  form-group-half">
                                            <label class="tu-label"><?php esc_html_e('School', 'tuturn'); ?></label>
                                            <div class="tu-placeholderholder">
                                                <input type="text" <?php echo esc_attr($attribute_val); ?> name="school" class="form-control" value="<?php echo esc_attr($school); ?>" placeholder=" " required>
                                                <div class="tu-placeholder">
                                                    <span><?php esc_html_e('Enter student school', 'tuturn'); ?></span>
                                                    <em>*</em>
                                                </div>
                                            </div>
                                        </div>
                                    <?php }
                                    if (in_array('parent_name', $student_fields)) {
                                    ?>
                                        <div class="form-group  form-group-half">
                                            <label class="tu-label"><?php esc_html_e('Parent name', 'tuturn'); ?></label>
                                            <div class="tu-placeholderholder">
                                                <input type="text" <?php echo esc_attr($attribute_val); ?> name="parent_name" value="<?php echo esc_attr($parent_name); ?>" class="form-control" placeholder=" " required>
                                                <div class="tu-placeholder">
                                                    <span><?php esc_html_e('Enter parent name', 'tuturn'); ?></span>
                                                    <em>*</em>
                                                </div>
                                            </div>
                                        </div>
                                    <?php }
                                    if (in_array('parent_phone', $student_fields)) {
                                    ?>
                                        <div class="form-group form-group-half">
                                            <label class="tu-label"><?php esc_html_e('Parent phone', 'tuturn'); ?></label>
                                            <div class="tu-placeholderholder">
                                                <input type="text" <?php echo esc_attr($attribute_val); ?> name="parent_phone" class="form-control" value="<?php echo esc_attr($parent_phone); ?>" placeholder=" " required>
                                                <div class="tu-placeholder">
                                                    <span><?php esc_html_e('Enter parent phone number', 'tuturn'); ?></span>
                                                    <em>*</em>
                                                </div>
                                            </div>
                                        </div>
                                    <?php }
                                    if (in_array('parent_email', $student_fields)) {
                                    ?>
                                        <div class="form-group form-group-half">
                                            <label class="tu-label"><?php esc_html_e('Parent email', 'tuturn'); ?></label>
                                            <div class="tu-placeholderholder">
                                                <input type="email" <?php echo esc_attr($attribute_val); ?> name="parent_email" class="form-control" placeholder=" " value="<?php echo esc_attr($parent_email); ?>" required>
                                                <div class="tu-placeholder">
                                                    <span><?php esc_html_e('Enter parent email', 'tuturn'); ?></span>
                                                    <em>*</em>
                                                </div>
                                            </div>
                                        </div>
                            <?php }
                                }
                            } ?>
                            <!-- End Only for student -->
                            <div class="form-group">
                                <label class="tu-label"><?php esc_html_e('Other details', 'tuturn'); ?></label>
                                <div class="tu-placeholderholder">
                                    <textarea <?php echo esc_attr($attribute_val); ?> id="other_introduction" name="other_introduction" class="form-control" placeholder="<?php esc_attr_e('Enter detail introduction', 'tuturn'); ?>"><?php echo do_shortcode($other_introduction); ?></textarea>
                                </div>
                            </div>
                            <?php if (empty($post_id)) { ?>
                                <div class="form-group">
                                    <label class="tu-label"><?php esc_html_e('Personal photo', 'tuturn'); ?></label>
                                    <div id="tu-pp-upload-verification" class="tu-identity-documents-upload">
                                        <div id="tu-pp-verification-droparea" class="tu-uploadphoto tu-uploadphotovtwo">
                                            <div class="tu-uploaddesc">
                                                <h5><?php esc_html_e('Drag or ', 'peer-review-system') ?><input type="file" id="file4"><label for="tuturn-pp-attachment-btn" id="tuturn-pp-attachment-btn"><?php esc_html_e('click here ', 'tuturn') ?></label><?php esc_html_e(' to Upload photo', 'tuturn') ?></h5>
                                                <p><?php esc_html_e('Photo file size does not exceed 5MB.', 'tuturn'); ?></p>
                                            </div>
                                            <svg>
                                                <rect width="100%" height="100%"></rect>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group d-none" id="appent-attachment-pp">
                                    <ul class="tu-uploadbar tu-bars tuturn-fileprocessing" id="tu-pp-tuturn-fileprocessing"></ul>
                                </div>

                                <div class="form-group">
                                    <label class="tu-label"><?php esc_html_e('Other attachment', 'tuturn'); ?></label>
                                    <div id="tu-upload-verification" class="tu-identity-documents-upload">
                                        <div id="tu-verification-droparea" class="tu-uploadphoto tu-uploadphotovtwo">
                                            <div class="tu-uploaddesc">
                                                <h5><?php esc_html_e('Drag or ', 'peer-review-system') ?><input type="file" id="file4"><label for="tuturn-attachment-btn" id="tuturn-attachment-btn"><?php esc_html_e('click here ', 'tuturn') ?></label><?php esc_html_e(' to Upload documents', 'tuturn') ?></h5>
                                                <p><?php esc_html_e('Document file size does not exceed 5MB and you can upload any document file format for admin verification', 'tuturn'); ?></p>
                                            </div>
                                            <svg>
                                                <rect width="100%" height="100%"></rect>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group d-none" id="appent-attachment">
                                    <ul class="tu-uploadbar tu-bars tuturn-fileprocessing" id="tu-tuturn-fileprocessing"></ul>
                                </div>
                                <?php if (!empty($verification_terms)) { ?>
                                    <div class="form-group">
                                        <div class="tu-check tu-checksm">
                                            <input type="hidden" name="terms" value="no">
                                            <input type="checkbox" id="vterms" name="terms" value="yes">
                                            <label for="vterms"><?php echo do_shortcode($verification_terms); ?></label>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="form-group">
                                    <a href="javascript:void(0);" data-user_type="<?php echo esc_html($userType) ?>" class="tu-primbtn tu-user-verification-btn"><?php esc_html_e('Save &amp; update changes', 'tuturn'); ?></a>
                                </div>
                            <?php } else { ?>
                                <?php if (!empty($personal_photo) && !empty($personal_photo['url'])) { ?>
                                    <div class="form-group">
                                        <ul class="tu-uploadbar tu-bars tuturn-fileprocessing tu-infouploading">
                                            <li>
                                                <div class="tu-doclist_content">
                                                    <div class="tu-doclist_title">
                                                        <h6><?php echo esc_html_e('Profile photo', 'tuturn'); ?></h6>
                                                    </div>
                                                    <a href="javascript:" class="wt-download-file-attachment" data-post_id="<?php echo intval($post_id); ?>" data-attachment_id="<?php echo intval($personal_photo['attachment_id']); ?>"><i class="icon icon-download"></i></a>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                <?php } ?>
                                <?php if (!empty($attachments)) { ?>
                                    <div class="form-group">
                                        <ul class="tu-uploadbar tu-bars tuturn-fileprocessing tu-infouploading">
                                            <li>
                                                <div class="tu-doclist_content">
                                                    <div class="tu-doclist_title">
                                                        <h6><?php echo esc_html_e('Attachments', 'tuturn'); ?></h6>
                                                    </div>
                                                    <a href="javascript:" class="wt-download-attachments" data-post_id="<?php echo intval($post_id); ?>"><i class="icon icon-download"></i></a>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>
</div>


<script type="text/template" id="tmpl-usefull-load-attachment">
    <li id="tu_file_{{data.id}}">
        <div class="tu-doclist_content">
            <img src="<?php echo esc_url($fileImage); ?>" alt="<?php esc_attr_e('upload image', 'tuturn'); ?>">
            <div class="tu-doclist_title" id="tu_file_{{data.id}}">
                <h6>{{data.name}}</h6>
                <span>{{data.size}}</span>
                <input type="hidden" class="file_name" name="attachments[]" value="{{data.file}}" />
                <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
            <a id="tu_delete_attachment" href="javascript:void(0);"><i class="icon icon-trash-2"></i></a>
        </div>
    </li>
</script>

<script type="text/template" id="tmpl-usefull-load-pp">
    <li id="tu_file_{{data.id}}">
        <div class="tu-doclist_content">
            <img src="" class="file_url" alt="<?php esc_attr_e('upload image', 'tuturn'); ?>">
            <div class="tu-doclist_title" id="tu_file_{{data.id}}">
                <h6>{{data.name}}</h6>
                <span>{{data.size}}</span>
                <input type="hidden" class="profile_file_name" name="profile_photo[]" value="{{data.file}}" />
                <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
            <a id="tu_delete_attachment" href="javascript:void(0);"><i class="icon icon-trash-2"></i></a>
        </div>
    </li>
</script>