<?php
/**
 * Instructor profile media gallery
 *
 * @package     Tuturn
 * @subpackage  Tuturn/Templates/Single_tuturn_Instructor
 * @author      Amentotech <info@amentotech.com>
 * @link       https://themeforest.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
*/
global $post;
$mediaGallery   = get_post_meta($post->ID, 'media_gallery', true);
$mediaGallery	= ! empty($mediaGallery) 	? $mediaGallery 	: array();
$imageSrc 		= TUTURN_DIRECTORY_URI.'/public/images/';
$random         = rand(100, 9999);
if( ! empty( $mediaGallery )){ ?>
    <div class="tu-tabswrapper">
        <div class="tu-tabstitle">
            <h4><?php esc_html_e('Media gallery', 'tuturn');?></h4>
        </div>
        <div class="tu-slider-holder">
            <div id="tu_splide_detail_instructor-<?php echo intval($random); ?>" class="tu-sync splide tu_splide_instructor-<?php echo intval($random); ?>">
                <div class="splide__track">
                    <ul class="splide__list">
                        <?php foreach( $mediaGallery as $item){
						    $attachmentType = ! empty( $item['attachment_type'] ) ? $item['attachment_type'] : ''; ?>
                            <li class="splide__slide">
                                <figure class="tu-sync__content">
                                    <?php if( $attachmentType == 'image' ) {
                                        $full_image = wp_get_attachment_image_src($item['attachment_id'], 'tu_gallery_large'); ?>
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
            <div id="tu_splidev_detial_instructor-<?php echo intval($random); ?>" class="tu-syncthumbnail splide media-gallery-instructor-<?php echo intval($random); ?>">
                <div class="splide__track">
                    <ul class="splide__list">
                        <?php if( ! empty( $mediaGallery ) ) {?>
                            <?php foreach( $mediaGallery as $item){ 
                                $attachmentType = ! empty( $item['attachment_type'] ) ? $item['attachment_type'] : ''; ?>
                                <li class="splide__slide">
                                    <figure class="tu-syncthumbnail__content">
                                        <?php if( $attachmentType == 'image') {
                                            $thumbnail = wp_get_attachment_image_src($item['attachment_id'], 'tu_user_profile'); ?>
                                            <img src="<?php echo esc_url($thumbnail['0']); ?>" alt="<?php esc_attr_e('Banner', 'tuturn');?>" />
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
<?php 
$slider_direction   = 'ltr';
if ( is_rtl() ) {
    $slider_direction   = 'rtl';
}

$media_gallery_instructor = '
jQuery(window).bind("resize", function () {
var tu_splide_instructor = document.querySelector("#tu_splide_detail_instructor-'. esc_js($random) .'");
	if (tu_splide_instructor != null) {
		var secondarySliderInstructor = new Splide("#tu_splidev_detial_instructor-'. esc_js($random) .'", {
			direction: "'.esc_js($slider_direction).'",
			gap: 10,
            arrows: true,
            rewind: false,
            drag: false,
            focus: "center",
            perPage: 7,
            isNavigation: true, 
            pagination: false,
            updateOnMove: true,
            cover      : true,
            breakpoints: {
                991 : {
                    perPage: 5,
                },
            }

		}).mount();
		var primarySlider_ = new Splide("#tu_splide_detail_instructor-'. esc_js($random) .'", {
			direction: "'.esc_js($slider_direction).'",
            type       : "fade",
            pagination : false,
            cover      : true,
            arrows: false,
            breakpoints: {
                767 : {
                pagination : true,
                },
            }
		})
       primarySlider_.sync(secondarySliderInstructor).mount(); 
    }
}).trigger("resize");';
    wp_add_inline_script('splide', $media_gallery_instructor, 'after');
}
