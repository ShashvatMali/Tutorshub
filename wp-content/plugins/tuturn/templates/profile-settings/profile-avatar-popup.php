<?php
/**
 * User profile avatar
 *
 * @package     Tuturn
 * @subpackage  Tuturn/Templates/Profile_Settings
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
*/
global $current_user;
?>
<script type="text/template" id="tmpl-load-profile-avatar">
    <div class="tu-popuptitle">
        <h4><?php esc_html_e('Upload profile photo', 'tuturn'); ?></h4>
        <a href="javascript:void(0);" class="close"><i class="icon icon-x" data-bs-dismiss="modal"></i></a>
    </div>
    <div class="modal-body">
            <form class="tu-dhb-orders-listing">
                <div id="crop_img_area"></div>
            </form>
        <div class="tu-popupfooter">
            <em> <?php esc_html_e('Click “Save” to update profile photo', 'tuturn'); ?></em>
            <a href="javascript:void(0);" class="tu-btn" id="save-profile-img"><?php esc_html_e('Save', 'tuturn'); ?>
                <span class="rippleholder tu-jsripple" ><em class="ripplecircle"></em></span>
            </a>
        </div>
    </div>
</script>
<script type="text/template" id="tmpl-load-default-image">
	<figure id="thumb-{{data.id}}" >
		<img class="attachment_url" alt="<?php esc_attr_e('Profile avatar', 'tuturn' ); ?>">
		<div class="progress tu-upload-progressbar"><div style="width:{{data.percentage}}%" class="progress-bar"></div></div>
	</figure>
</script>