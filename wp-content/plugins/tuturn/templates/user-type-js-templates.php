<?php
/**
 * Google singup user type JS template
 *
 * @package     Tuturn
 * @subpackage  Turutn/templates
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
 */
wp_enqueue_script('wp-util');
?>
<script type="text/template" id="tmpl-tu-social-user-type">
    <div class="modal-header">
        <h5><?php esc_html_e('Choose user type', 'tuturn');?></h5>
        <a href="javascript:void(0);" class="tu-close" type="button" data-bs-dismiss="modal" aria-label="Close"><i class="icon icon-x"></i></a>
    </div>
    <div class="modal-body">
        <form class="tu-themeform">
            <fieldset>
                <div class="tu-themeform__wrap">
                    <div class="form-group registration-user-type">
                        <div class="tu-check tu-radiosm">
                            <input id="usertype_student" class="tu-usertype-radio" type="radio" name="choose_usertype" value="student">
                            <label for="usertype_student"><?php esc_html_e('Student', 'tuturn'); ?></label>
                        </div>
                        <div class="tu-check tu-radiosm">
                            <input id="usertype_instructor" class="tu-usertype-radio" type="radio" name="choose_usertype" value="instructor">
                            <label for="usertype_instructor"><?php esc_html_e('Instructor', 'tuturn'); ?></label>
                        </div>
                    </div>
                    <input type="hidden" id="social-userdetail" name="social_userdetail" value="{{data.userData}}">
                </div>
            </fieldset>
        </form>
    </div>
</script>