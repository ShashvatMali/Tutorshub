<?php
/**
 * Instructor add media gallery
 *
 * @package     Tuturn
 * @subpackage  Tuturn/Templates/Profile_Settings/Instructor
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
*/
global $tuturn_settings;
$mediaGallery   = get_post_meta($profile_id, 'media_gallery', true);
$mediaGallery	= ! empty($mediaGallery) 	? $mediaGallery 	: array();
$imageSrc 		= TUTURN_DIRECTORY_URI.'/public/images/';
$upload_max_file_size 			= !empty( $tuturn_settings['upload_max_file_size'] ) ? $tuturn_settings['upload_max_file_size'] : '5MB';
$media_gallery_items_limit 		= !empty( $tuturn_settings['media_gallery_items_limit'] ) ? $tuturn_settings['media_gallery_items_limit'] : '15';
$media_gallery_image_max_height	= !empty( $tuturn_settings['media_gallery_image_max_height'] ) ? $tuturn_settings['media_gallery_image_max_height'] : '1200';
$media_gallery_image_max_width	= !empty( $tuturn_settings['media_gallery_image_max_width'] ) ? $tuturn_settings['media_gallery_image_max_width'] : '1200';
?>
<div class="tu-boxarea">
    <div class="tu-boxsm">
        <div class="tu-boxsmtitle">
            <h4><?php esc_html_e('Media gallery', 'tuturn');?></h4>
            <a href="javascript:void(0)" id="tu_edit_media_gallery"><?php esc_html_e('Add/Edit', 'tuturn');?></a>
        </div>
    </div>
    <?php if( ! empty( $mediaGallery )){ ?>
    <div class="tu-box">        
        <div class="tu-slider-holder">
            <div id="tu_splide" class="tu-sync splide">
                <div class="splide__track"> 
                    <ul class="splide__list">
                        <?php foreach( $mediaGallery as $item){
						    $attachmentType = ! empty( $item['attachment_type'] ) ? $item['attachment_type'] : ''; ?>
                            <li class="splide__slide">
                                <figure class="tu-sync__content">
                                    <?php if( $attachmentType == 'image' ) {
                                        $full_image = wp_get_attachment_image_src($item['attachment_id'], 'full'); ?>
                                        <img src="<?php echo esc_attr($full_image['0']); ?>" alt="<?php esc_attr_e('Banner', 'tuturn');?>" />
                                    <?php } elseif($attachmentType == 'video') {
                                        do_action('tu_embeded_video', $item['videofile'] );
                                    } else {
                                        do_action('tu_embeded_video', $item['url'] );
                                    }?>
                                </figure>
                            </li>
						<?php }?>
                    </ul>
                </div>
            </div>
            <div id="tu_splidev2" class="tu-syncthumbnail splide">
                <div class="splide__track">
                    <ul class="splide__list">
                        <?php if( ! empty( $mediaGallery ) ) {?>
                            <?php foreach( $mediaGallery as $item){ 
                                $attachmentType = ! empty( $item['attachment_type'] ) ? $item['attachment_type'] : ''; ?>
                                <li class="splide__slide">
                                    <figure class="tu-syncthumbnail__content">
                                        <?php if( $attachmentType == 'image') {
                                            $thumbnail = wp_get_attachment_image_src($item['attachment_id'], 'tu_user_profile'); ?>
                                            <img src="<?php echo esc_url($thumbnail['0']); ?>" alt="<?php esc_attr_e('image','tuturn')?>" />
                                        <?php } else if($attachmentType == 'video') {
                                            $url = !empty( $item['videofile'] ) ? $item['videofile'] : ''; ?>
                                            <span class="tu-servicesvideo"></span>
                                            <img class="tu_video" src="<?php echo esc_url($imageSrc.'vidoe_thumbnail.jpg');?>" alt="<?php esc_attr_e('video thumbnail', 'tuturn') ?>">
                                        <?php } else {
                                            $url = !empty( $item['url'] ) ? $item['url'] : ''; ?>
                                            <span class="tu-servicesvideo"></span>
                                            <img class="tu_video" src="<?php echo esc_url($imageSrc.'vidoe_thumbnail.jpg');?>" alt="<?php esc_attr_e('image','tuturn')?> ">
                                        <?php }?>
                                    </figure>
                                </li>
                            <?php }?>
                        <?php }?>
                       
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <?php }?>
</div>
<script type="text/template" id="tmpl-media-gallery">
    <div class="modal-header">
        <h5><?php esc_html_e('Add/edit media gallery','tuturn') ?></h5>
        <a href="javascript:void(0);" class="tu-close" type="button" data-bs-dismiss="modal" aria-label="Close"><i class="icon icon-x"></i></a>
    </div>
    <div class="modal-body">
        <form class="tu-themeform tu-gallaryform" id="tu_media_gallary_frm">
            <fieldset>
                <div class="tu-themeform__wrap">
                    <div class="form-group" id="tu_upload_images">
                        <label class="tu-label"><?php esc_html_e('Upload gallery','tuturn'); ?></label>
                        <div class="tu-uploadphoto" id="tu_gallery_droparea">
                            <i class="icon icon-grid"></i>
                            <h5><?php esc_html_e('Drag or','tuturn'); ?> <input type="file" id="tu_upload_images_btn"><label for="tu_upload_images_btn" ><?php esc_html_e('click here','tuturn'); ?></label> <?php esc_html_e('to upload photo','tuturn'); ?></h5>
                            <p><?php echo sprintf(__('Your file size does not exceed %s and dimensions %dpx width and height %dpx. you can upload max up to %d media items','tuturn'), $upload_max_file_size , $media_gallery_image_max_width, $media_gallery_image_max_height, $media_gallery_items_limit); ?></p>
                            <svg><rect width="100%" height="100%"></rect></svg>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="tu-label"><?php esc_html_e('Upload via video URL','tuturn'); ?></label>
                        <div class="tu-placeholderholder">
                            <div class="tu-appendbtn">
                                <input type="url" name="tu_video_url" id="tu_video_url" placeholder="<?php esc_attr_e('Add youtube, vimeo or any other valid video URL', 'tuturn');?>" class="form-control tu-form-input">
                                <a href="javascript:void(0);" id="tu_add_video_url" disabled="disabled" class="tu-pb-sm tu-primbtn-sm"><?php esc_html_e('Add to gallery','tuturn'); ?></a>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <ul id="tu_gallery_fileprocessing" class="tu-thumbnails">
                            <?php if( ! empty( $mediaGallery ) ) {                               
                                foreach( $mediaGallery as $item){
                                    $attachmentType = ! empty( $item['attachment_type'] ) ? $item['attachment_type'] : '';
                                    ?>
                                    <li>
                                        <div class="tu-thumbnails_content">
                                            <figure>
                                                <?php if( $attachmentType == 'image') {
                                                    $thumbnail = wp_get_attachment_image_src($item['attachment_id'], 'tu_user_profile');
                                                        $singleRec = json_encode($item); ?>
                                                        <img src="<?php echo esc_url($thumbnail['0']);?>" alt="<?php esc_attr_e('image', 'tuturn') ?>">
                                                        <input type="hidden" data-is_save="0" class="file_name" data-attachment_src="<?php echo esc_html($singleRec); ?>" data-attachment_type="image" name="attachments[]" value="" />
                                                    <?php } elseif($attachmentType == 'video'){?>
                                                        <?php $videofile = !empty( $item['videofile'] ) ? $item['videofile'] : ''; ?>
                                                        <img class="tu_video" src="<?php echo esc_url($imageSrc.'vidoe_thumbnail.jpg');?>" alt="<?php esc_attr_e('video thumbnail', 'tuturn') ?>">
                                                        <input type="hidden" data-is_save="0" class="file_name" data-attachment_src="<?php echo esc_attr($videofile) ?>" data-attachment_type="video" name="attachments[]" value="" />
                                                    <?php } else {?>
                                                        <?php $url = !empty( $item['url'] ) ? $item['url'] : ''; ?>
                                                        <img class="tu_video" src="<?php echo esc_url($imageSrc.'vidoe_thumbnail.jpg');?>" alt="<?php esc_attr_e('video thumbnail', 'tuturn') ?>">
                                                        <input type="hidden" data-is_save="0" class="file_name" data-attachment_src="<?php echo esc_url($url) ?>" data-attachment_type="url" name="attachments[]" value="" />
                                                    <?php } ?>
                                                </figure>
                                                <?php if( $attachmentType == 'video' || $attachmentType == 'url') { ?>
                                                    <span class="tu-servicesvideo"></span>
                                                <?php } ?>
                                                <div class="tu-thumbnails_action">
                                                    <span class="tu_delete_item"><i class="icon icon-trash-2"></i></span>
                                                    <img class="st_sorth tu-sort-handle" src="<?php echo esc_url($imageSrc.'sort.svg'); ?>" alt="<?php esc_attr_e('list sort handle', 'tuturn') ?>">
                                                </div>
                                            </div>
                                        </li>
                            <?php }?>
                        <?php }?>
                        </ul>
                    </div>
                    <div class="form-group tu-formbtn">
                        <a href="javascript:void(0);" data-profile_id="<?php echo intval($profile_id) ?>" id="tu_update_media_gallery" class="tu-primbtn-lg"><?php esc_html_e('Save & update changes','tuturn'); ?></a>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
</script>
<script type="text/template" id="tmpl-load-gallary-images">
    <li id="tu_file_{{data.id}}">
        <div class="tu-thumbnails_content">
            <# if(data.fileType == 'video') #>
            <figure>
                <img class="tu_image_{{data.id}}" src="<?php echo esc_url($imageSrc.'vidoe_thumbnail.jpg');?>" alt="<?php esc_attr_e('Video thumbnail', 'tuturn');?>">
                <input type="hidden" data-is_save="1" class="file_name" data-attachment_src="" data-attachment_type="video" name="attachments[]" value="" />
            </figure>
            <span class="tu-servicesvideo"></span>
            <# else { #>
                <figure>
                <img class="tu_image_{{data.id}}" src="<?php echo esc_url($imageSrc.'default-avatar.jpg');?>" alt="<?php esc_attr_e('Profile thumbnail', 'tuturn');?>">
                <input type="hidden" data-is_save="1" class="file_name" data-attachment_src="" data-attachment_type="image" name="attachments[]" value="" />
            </figure>
            <#} #>
            
            <div class="tu-thumbnails_action">
                <span class="tu_delete_item"><i class="icon icon-trash-2"></i></span>
                <img class="st_sorth" src="<?php echo esc_url($imageSrc.'sort.svg'); ?>" alt="<?php esc_attr_e('image', 'tuturn') ?>">
            </div>
        </div>
    </li>
</script>
<script type="text/template" id="tmpl-load-gallary-vidoe-url">
    <li id="tu_file_{{data.id}}">
        <div class="tu-thumbnails_content">
            <figure>
                <img class="tu_image_{{data.id}}" src="<?php echo esc_url($imageSrc.'vidoe_thumbnail.jpg');?>" alt="<?php esc_attr_e('video thumbnail', 'tuturn') ?>">
                <input type="hidden" data-is_save="1" class="file_name" data-attachment_src="{{data.url}}" data-attachment_type="url" name="attachments[]" value="" />
            </figure>
            <span class="tu-servicesvideo"></span>
            <div class="tu-thumbnails_action">
                <span class="tu_delete_item"><i class="icon icon-trash-2"></i></span>
                <img class="st_sorth tu-sort-handle" src="<?php echo esc_url($imageSrc.'sort.svg'); ?>" alt="<?php esc_attr_e('list sort handle', 'tuturn') ?>">
            </div>
        </div>
    </li>
</script>