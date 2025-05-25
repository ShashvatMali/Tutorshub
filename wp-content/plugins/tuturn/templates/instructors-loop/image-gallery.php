<?php
/**
 * Provider image gallery
 *
 * @package     Tuturn
 * @subpackage  Tuturn/templates/instructor-loop
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
 */
global $post, $tuturn_settings;
$profile_id     = !empty($post->ID) ? intval($post->ID) : 0 ; 
$mediaGallery   = get_post_meta($profile_id, 'media_gallery', true);
$mediaGallery   = !empty($mediaGallery) ? $mediaGallery : array();
$imageSrc       = TUTURN_DIRECTORY_URI.'/public/images/';
$any_image  = false;
if(!empty($mediaGallery)){
    foreach( $mediaGallery as $item){
        $attachmentType = ! empty( $item['attachment_type'] ) ? $item['attachment_type'] : '';
        if( $attachmentType == 'image' ) {
            $any_image  = true;
        }
    }
}

if(empty($mediaGallery) || $any_image == false ){
    $tu_post_meta       = get_post_meta($profile_id, 'profile_details', true);
    $tu_post_meta       = !empty($tu_post_meta) ? $tu_post_meta : array();
    $instructor_name    = tuturn_get_username($profile_id);
    $user_dp            = TUTURN_DIRECTORY_URI . 'public/images/default-avatar_416x281.jpg';

    if(!empty($tuturn_settings['defaul_instructor_profile']['id'])){
        $placeholder_id = !empty($tuturn_settings['defaul_instructor_profile']['id']) ? $tuturn_settings['defaul_instructor_profile']['id'] : '';
        $img_atts = wp_get_attachment_image_src($placeholder_id, 'tu_gallery_medium');
        $profile_image  = !empty($img_atts['0']) ? $img_atts['0'] : '';
    } else {
        $profile_image  = !empty($tuturn_settings['defaul_instructor_profile']['url']) ? $tuturn_settings['defaul_instructor_profile']['url'] : $user_dp;
    }
    $class  =   empty($tu_post_meta['profile_image']['featureImage']) ? 'class="tu-thumbnail-image"' : '';

    if(!empty($profile_image) ) {?>
        <figure <?php echo do_shortcode($class);?>>
            <?php if(!empty($profile_image)) {?>
                <img src="<?php echo esc_url($profile_image);?>" alt="<?php echo esc_attr($instructor_name);?>">
            <?php } ?>
            <?php if(!empty($media_images)){?>
                <figcaption>
                    <?php foreach( $media_images as $media_image) {
                        if(!empty($media_image['thumbnail'])){ ?>
                            <span><img src="<?php echo esc_url($media_image['thumbnail'])?>" alt="<?php esc_attr_e('User gallery','tuturn')?>"></span>
                        <?php }
                    }?>
                </figcaption>
            <?php }?>
        </figure>
        <?php 
    }
} else if(!empty($any_image) && !empty($mediaGallery)) {
    ?>
    <div  class="tusync splide tu_splide_view2-<?php echo esc_attr($profile_id); ?>">
        <div class="splide__track">
            <ul class="splide__list">
            <?php foreach( $mediaGallery as $item){
                $attachmentType = ! empty( $item['attachment_type'] ) ? $item['attachment_type'] : ''; ?>
                <?php if( $attachmentType == 'image' ) { ?>
                    <li class="splide__slide">
                        <figure class="tu-sync__content">
                            <?php   $full_image = wp_get_attachment_image_src($item['attachment_id'], 'tu_gallery_medium'); ?>
                            <a class="venobox" data-gall="gall-<?php echo esc_attr($item['attachment_id']); ?>" href="javascript:void(0)">
                                <img src="<?php echo esc_attr($full_image['0']); ?>" alt="<?php esc_attr_e('Banner', 'tuturn');?>">
                            </a>
                        </figure>
                    </li>
                    <?php } ?>
                <?php }?>
            </ul>
        </div>
    </div>
    <div  class="tusyncthumbnail_view2-<?php echo esc_attr($profile_id); ?> splide">
        <div class="splide__track">
            <ul class="splide__list">
            <?php if( ! empty( $mediaGallery ) ) {?>
                <?php foreach( $mediaGallery as $item){ 
                    $attachmentType = ! empty( $item['attachment_type'] ) ? $item['attachment_type'] : ''; ?>
                    <?php if( $attachmentType == 'image' ) { ?>
                        <li class="splide__slide">
                            <figure class="tusyncthumbnail__content">
                                <?php $full_image = wp_get_attachment_image_src($item['attachment_id'], 'tu_user_profile'); ?>
                                <img src="<?php echo esc_url($full_image['0']); ?>" alt="<?php esc_attr_e('Banner', 'tuturn');?>">
                            </figure>
                        </li>
                        <?php } ?>
                    <?php }?>
                <?php }?>
            </ul>
        </div>
    </div>
    <?php
    /* search slider view2 */
    $slider_direction   = 'ltr';
    if ( is_rtl() ) {
        $slider_direction   = 'rtl';
    }
    $search_view2_script = '
    jQuery(window).bind("resize", function () {
        var tu_splide_view2 = document.querySelector(".tu_splide_view2-'.esc_js($profile_id).'")
        if (tu_splide_view2 != null) {
            var secondarySlider_view2 = new Splide( ".tusyncthumbnail_view2-'.esc_js($profile_id).'", {
                direction: "'.$slider_direction.'",
                rewind      : true,
                fixedWidth  : 50,
                fixedHeight : 50,
                isNavigation: true,
                gap         : 10,
                pagination  : false,
                arrows     : false,
                focus  : "center",
                updateOnMove: true,    
            } ).mount();
            var primarySlider_view2 = new Splide( ".tu_splide_view2-'.esc_js($profile_id).'", {
                direction: "'.$slider_direction.'",
                type       : "fade",
                pagination : false,
                cover      : true,
                arrows     : false,
            } )
            primarySlider_view2.sync( secondarySlider_view2 ).mount(); 
        }
    }).trigger("resize");
    ';
    wp_add_inline_script('splide', $search_view2_script, 'after');
}
