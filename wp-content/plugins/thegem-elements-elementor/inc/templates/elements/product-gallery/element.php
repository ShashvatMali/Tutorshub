<?php

namespace TheGem_Elementor\Widgets\TemplateProductGallery;

use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Plugin;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;

if (!defined('ABSPATH')) exit;

/**
 * Elementor widget for Product Gallery.
 */

#[\AllowDynamicProperties]
class TheGem_TemplateProductGallery extends Widget_Base {
	public function __construct($data = [], $args = null) {
		parent::__construct($data, $args);

		if ( !defined('THEGEM_TE_PRODUCT_GALLERY_DIR' )) {
			define('THEGEM_TE_PRODUCT_GALLERY_DIR', rtrim(__DIR__, ' /\\'));
		}

		if ( !defined('THEGEM_TE_PRODUCT_GALLERY_URL') ) {
			define('THEGEM_TE_PRODUCT_GALLERY_URL', rtrim(plugin_dir_url(__FILE__), ' /\\'));
		}

		wp_register_script('thegem-te-product-gallery', THEGEM_TE_PRODUCT_GALLERY_URL . '/js/product-gallery.js', array('jquery', 'owl', 'owl-zoom'), false, true);
		wp_register_script('thegem-te-product-gallery-grid', THEGEM_TE_PRODUCT_GALLERY_URL . '/js/product-gallery-grid.js', array('jquery', 'owl-zoom'), false, true);

		wp_register_style('thegem-te-product-gallery', THEGEM_TE_PRODUCT_GALLERY_URL . '/css/product-gallery.css', array('owl'));
	}

	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'thegem-template-product-gallery';
	}

	/**
	 * Show in panel.
	 *
	 * Whether to show the widget in the panel or not. By default returns true.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return bool Whether to show the widget in the panel or not.
	 */
	public function show_in_panel() {
		$post_id = \Elementor\Plugin::$instance->editor->get_post_id();
		return get_post_type($post_id) === 'thegem_templates' && thegem_get_template_type($post_id) === 'single-product';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __('Product Gallery', 'thegem');
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return str_replace('thegem-', 'thegem-eicon thegem-eicon-', $this->get_name());
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return ['thegem_single_product_builder'];
	}

	public function get_style_depends() {
		return ['thegem-te-product-gallery'];
	}

	public function get_script_depends() {
		return ['thegem-te-product-gallery'];
	}

	/** Show reload button */
	public function is_reload_preview_required() {
		return true;
	}

	/** Get widget wrapper */
	public function get_widget_wrapper() {
		return 'thegem-te-product-gallery';
	}

	/** Get customize class */
	public function get_customize_class($only_parent = false, $grid = false) {
        $inner_block = 'product-gallery';

		if ($grid) {
			$inner_block = 'product-gallery-grid';
		}

		if ($only_parent) {
			return ' .'.$this->get_widget_wrapper();
		}

		return ' .'.$this->get_widget_wrapper().' .'.$inner_block;
	}

    /** Check is admin edit mode */
	public function is_admin_mode() {
		return is_admin() && Plugin::$instance->editor->is_edit_mode();
	}

	/**
	 * Register the widget controls.
	 *
	 * @access protected
	 */
	protected function register_controls() {

		// General Section
		$this->start_controls_section(
			'section_general',
			[
				'label' => __('General', 'thegem'),
			]
		);

		$this->add_control(
			'type',
			[
				'label' => __('Gallery Type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'horizontal' => __('Horizontal Thumbnails', 'thegem'),
					'vertical' => __('Vertical Thumbnails', 'thegem'),
					'dots' => __('Dots Navigation', 'thegem'),
					'single' => __('Carousel Grid', 'thegem'),
					'grid' => __('Grid', 'thegem'),
					'native' => __('WooCommerce Native', 'thegem'),
				],
				'default' => 'horizontal',
			]
		);

		$this->add_control(
			'single_columns',
			[
				'label' => __('Columns', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'1' => __('1x columns', 'thegem'),
					'2' => __('2x columns', 'thegem'),
					'3' => __('3x columns', 'thegem'),
				],
				'default' => '1',
				'condition' => [
					'type' => ['single'],
				],
			]
		);

		$this->add_control(
			'thumbs_position',
			[
				'label' => __('Thumbnails Bar Position', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'left' => __('Left', 'thegem'),
					'right' => __('Right', 'thegem'),
				],
				'default' => 'left',
				'condition' => [
					'type' => ['vertical'],
				],
			]
		);

		$this->add_control(
			'single_gaps',
			[
				'label' => __('Gaps', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'condition' => [
					'type' => ['single'],
				],
			]
		);

		$this->add_control(
			'grid_columns',
			[
				'label' => __('Columns', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'1x' => __('1x columns', 'thegem'),
					'2x' => __('2x columns', 'thegem'),
					'3x' => __('3x columns', 'thegem'),
					'4x' => __('4x columns', 'thegem'),
					'5x' => __('5x columns', 'thegem'),
					'6x' => __('6x columns', 'thegem'),
				],
				'default' => '1x',
				'condition' => [
					'type' => ['grid'],
				],
			]
		);

		$this->add_control(
			'grid_gaps',
			[
				'label' => __('Gaps', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => '42',
				'condition' => [
					'type' => ['grid'],
				],
			]
		);

		$this->add_control(
			'grid_image_size',
			[
				'label' => __('Image Size', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'default' => __('As in Grid Layout (TheGem Thumbnails)', 'thegem'),
					'full' => __('Full Size', 'thegem'),
				],
				'default' => 'default',
				'condition' => [
					'type' => ['grid'],
				],
			]
		);

		$this->add_control(
			'grid_image_ratio',
			[
				'label' => __('Image Ratio', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => '',
				],
				'range' => [
					'px' => [
						'min' => 0.1,
						'max' => 2,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-product-gallery.image-aspect-ratio .product-gallery-grid-item .image-inner' => 'aspect-ratio: {{SIZE}} !important;',
				],
				'condition' => [
					'type' => ['grid'],
					'grid_image_size' => ['full'],
				],
				'description' => __('Specify a decimal value, i.e. instead of 3:4, specify 0.75. Use a dot as a decimal separator. Leave blank to show the original image ratio', 'thegem'),
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'image_ratio',
			[
				'label' => __('Image Ratio', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => '',
				],
				'range' => [
					'px' => [
						'min' => 0.1,
						'max' => 2,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .thegem-te-product-gallery.image-aspect-ratio .product-gallery-slider-item .image-inner' => 'aspect-ratio: {{SIZE}} !important;',
				],
				'condition' => [
					'type' => ['horizontal', 'vertical', 'dots', 'single'],
				],
				'description' => __('Specify a decimal value, i.e. instead of 3:4, specify 0.75. Use a dot as a decimal separator. Leave blank to show the original image ratio', 'thegem'),
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'grid_image_count',
			[
				'label' => __('Max. Number of Images', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'condition' => [
					'type' => ['grid'],
				],
			]
		);

		$this->add_control(
			'show_image',
			[
				'label' => __('Show Main Image', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'click' => __('Click on Thumbnail', 'thegem'),
					'hover' => __('Hover on Thumbnail', 'thegem'),
				],
				'default' => 'hover',
				'condition' => [
					'type' => ['horizontal', 'vertical'],
				],
			]
		);

		$this->add_control(
			'product_image',
			[
				'label' => __('Product Main Image', 'thegem'),
				'return_value' => '1',
				'default' => '1',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'condition' => [
					'type' => ['horizontal', 'vertical', 'dots', 'single', 'grid'],
				],
			]
		);

		$this->add_control(
			'thumb_on_mobile',
			[
				'label' => __('Show Thumbnails on Mobiles', 'thegem'),
				'return_value' => '1',
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'condition' => [
					'type' => ['horizontal', 'vertical'],
				],
			]
		);

		$this->add_control(
			'product_video',
			[
				'label' => __('Product Video', 'thegem'),
				'return_value' => '1',
				'default' => '1',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'condition' => [
					'type' => ['horizontal', 'vertical', 'dots', 'single', 'grid'],
				],
			]
		);

		$this->add_control(
			'video_autoplay',
			[
				'label' => __('Video Autoplay', 'thegem'),
				'return_value' => '1',
				'default' => '0',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'condition' => [
					'type' => ['horizontal', 'vertical', 'dots', 'single', 'grid'],
				],
			]
		);

		$this->add_control(
			'zoom',
			[
				'label' => __('Zoom Magnifier', 'thegem'),
				'return_value' => '1',
				'default' => '1',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'condition' => [
					'type' => ['horizontal', 'vertical', 'dots', 'single', 'grid'],
				],
			]
		);

		$this->add_control(
			'lightbox',
			[
				'label' => __('Lightbox Gallery', 'thegem'),
				'return_value' => '1',
				'default' => '1',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'condition' => [
					'type' => ['horizontal', 'vertical', 'dots', 'single', 'grid'],
				],
			]
		);

		$this->add_control(
			'auto_height',
			[
				'label' => __('Gallery Auto Height', 'thegem'),
				'return_value' => '1',
				'default' => '1',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'condition' => [
					'type' => ['horizontal', 'vertical', 'dots', 'single', 'grid'],
				],
			]
		);

		$this->add_control(
			'labels',
			[
				'label' => __('Show Labels', 'thegem'),
				'return_value' => '1',
				'default' => '1',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'condition' => [
					'type' => ['horizontal', 'vertical', 'dots', 'single', 'grid'],
				],
			]
		);

		$this->add_control(
			'label_sale',
			[
				'label' => __('"Sale" Label', 'thegem'),
				'return_value' => '1',
				'default' => '1',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'condition' => [
					'type' => ['horizontal', 'vertical', 'dots', 'single', 'grid'],
					'labels' => '1',
				],
			]
		);

		$this->add_control(
			'label_new',
			[
				'label' => __('"New" Label', 'thegem'),
				'return_value' => '1',
				'default' => '1',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'condition' => [
					'type' => ['horizontal', 'vertical', 'dots', 'single', 'grid'],
					'labels' => '1',
				],
			]
		);

		$this->add_control(
			'label_out_stock',
			[
				'label' => __('"Out of stock" Label', 'thegem'),
				'return_value' => '1',
				'default' => '1',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'condition' => [
					'type' => ['horizontal', 'vertical', 'dots', 'single', 'grid'],
					'labels' => '1',
				],
			]
		);

		$this->add_control(
			'retina_ready',
			[
				'label' => __('Retina-ready thumbnails', 'thegem'),
				'return_value' => '1',
				'default' => '0',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'condition' => [
					'type' => ['horizontal', 'vertical', 'grid'],
				],
			]
		);

		$this->add_control(
			'skeleton_loader',
			[
				'label' => __('Skeleton Preloader', 'thegem'),
				'return_value' => '1',
				'default' => '0',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'condition' => [
					'type' => ['horizontal', 'vertical', 'grid'],
				],
			]
		);

		$this->end_controls_section();

		// Style Section
		$this->start_controls_section(
			'Style',
			[
				'label' => __('Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'elements_color',
			[
				'label' => __('Arrows and Icons Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
			]
		);

		$this->add_control(
			'dots_color',
			[
				'label' => __('Normal Dot Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'type' => ['horizontal', 'vertical', 'dots'],
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .owl-dots .owl-dot span' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'dots_color_active',
			[
				'label' => __('Active Dot Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'condition' => [
					'type' => ['horizontal', 'vertical', 'dots'],
				],
				'selectors' => [
					'{{WRAPPER}}'.$this->get_customize_class().' .owl-dots .owl-dot.active span' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	public function render() {
		$settings = $this->get_settings_for_display();

		// General params
		$params = array_merge(array(

		), $settings);

		// Enqueue Scripts
		if ($params['type'] != 'grid') {
			wp_enqueue_script('thegem-te-product-gallery');
		} else {
			wp_enqueue_script('thegem-te-product-gallery-grid');
		}
		wp_enqueue_style('thegem-te-product-gallery');

		// Init Gallery
		ob_start();
		$product = thegem_templates_init_product();
		global $post;

		if (empty($product)) {
			ob_end_clean();
			echo thegem_templates_close_product(str_replace('-template-', '-te-', $this->get_name()), $this->get_title(), '');
			return;
		}

		//Init grid params
        if ($params['type'] == 'grid'){
	        $grid_column_width = round( 100 / mb_substr( $params['grid_columns'], 0, 1 ), 4 );
	        $grid_gaps_size = !empty($params['grid_gaps']) ? round( $params['grid_gaps'] / 2 ) : 0;
        }
		$is_images_sizes = thegem_get_option( 'woocommerce_activate_images_sizes' );

		//Init images params
		$attachments_ids = array();
		if (has_post_thumbnail() && !empty($params['product_image'])) {
			$attachments_ids = array(get_post_thumbnail_id());
		}
		$attachments_ids = array_merge($attachments_ids, $product->get_gallery_image_ids());
		if ('variable' === $product->get_type()) {
			foreach ($product->get_available_variations() as $variation) {
				if (has_post_thumbnail($variation['variation_id'])) {
					$thumbnail_id = get_post_thumbnail_id($variation['variation_id']);
					if (!in_array($thumbnail_id, $attachments_ids)) {
						$attachments_ids[] = $thumbnail_id;
					}
				}
			}
		}

		if (empty($attachments_ids)) {
			ob_end_clean();
			echo thegem_templates_close_product(str_replace('-template-', '-te-', $this->get_name()), $this->get_title(), '');
			return;
		}

		$gallery_uid = uniqid();

		$firstImagePath = wp_get_original_image_path($attachments_ids[0]);
		$isSingleImg = count($attachments_ids) < 2;
		$isSingleImgSkeleton = $isSingleImg ? ' product-gallery-skeleton-single' : '';
		$isSquareImg = '1';
		if ($firstImagePath) {
			$firstImageSize = wp_getimagesize($firstImagePath);
			$firstImageSize = empty($firstImageSize) ? array(1, 1) : $firstImageSize;
			$skeletonPadding = 100 * $firstImageSize[1] / $firstImageSize[0];
			if ($skeletonPadding > 100) {
				$isSquareImg = '';
			}
		}

		//Init skeleton params
		$isVertical = $params['type'] == 'vertical';
		$isVerticalSkeleton = $isVertical ? 'product-gallery-skeleton-vertical' : '';
		$isVerticalSkeleton .= $isSquareImg == '1' ? ' product-gallery-skeleton-vertical-square' : '';

		//Init video params
		$product_video_data = get_post_meta($post->ID, 'thegem_product_video', true);
		$product_video = thegem_get_sanitize_product_video_data($product_video_data);
		$video_type = $product_video['product_video_type'];
		$video = $product_video['product_video_id'];
		$video_self = $product_video['product_video_link'];
		$poster = $product_video['product_video_thumb'];
		$poster_id = attachment_url_to_postid($poster);

		if (!empty($video) && $video_type == 'youtube') {
			$youtube_id = thegem_parcing_youtube_url($video);
		}

		if (!empty($video) && $video_type == 'vimeo') {
			$vimeo_id = thegem_parcing_vimeo_url($video);
		}

		$link = '';
		if ($video_type == 'youtube' || $video_type == 'vimeo') {
			if ($video_type == 'youtube' && $youtube_id) {
				$link = '//www.youtube.com/embed/' . $youtube_id . '?playlist=' . $youtube_id . '&autoplay=1&mute=1&controls=1&loop=1&showinfo=0&autohide=1&iv_load_policy=3&rel=0&disablekb=1&wmode=transparent';

				if ($poster) {
					$video_block = '<iframe src="' . esc_url($link) . '" frameborder="0" muted="muted" allowfullscreen></iframe>';
				} else {
					$video_block = '<div id="productYoutubeVideo" data-yt-id="' . $youtube_id . '"></div>';
				}
			}
			if ($video_type == 'vimeo' && $vimeo_id) {
				$link = '//player.vimeo.com/video/' . $vimeo_id . '?autoplay=1&muted=1&controls=1&loop=1&title=0&badge=0&byline=0&autopause=0&autohide=1';

				if ($poster) {
					$video_block = '<iframe src="' . esc_url($link) . '" frameborder="0" muted="muted" allowfullscreen></iframe>';
				} else {
					$video_block = '<div id="productVimeoVideo" data-vm-id="' . $vimeo_id . '"></div>';
				}
			}
		} else if ($video_type == 'self') {
			$link = $video_self;
			$video_self_autoplay = $params['video_autoplay'] ? 'playsinline autoplay' : null;
			$video_block = '<video id="productSelfVideo" class="fancybox-video" style="opacity: 0;" controls disablePictureInPicture controlsList="nodownload" loop="loop" '.$video_self_autoplay.' src="' . $link . '" muted="muted"' . ( $poster ? ' poster="' . esc_url($poster) . '"' : '' ) . '></video>';
		}

		$type = 'product-gallery--'.$params['type'];
		$aspect_ratio = !empty($params['image_ratio']['size']) || (!empty($params['grid_image_size']) && $params['grid_image_size'] == 'full' && !empty($params['grid_image_ratio']['size'])) ? 'image-aspect-ratio' : '';
		$params['element_class'] = implode(' ', array($this->get_widget_wrapper(), $type, $aspect_ratio));

		?>

        <script>
            function firstImageLoaded() {
                (function ($) {
                    var $galleryElement = $('.thegem-te-product-gallery > .product-gallery'),
                        isVertical = $galleryElement.attr("data-thumb") === 'vertical',
                        isTrueCount = $('.product-gallery-slider-item', $galleryElement).length > 1,
                        isMobile = $(window).width() < 768 && /Android|webOS|iPhone|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ? true : false,
                        isDots = $galleryElement.attr("data-thumb") === 'dots';

                    if (isVertical && isTrueCount && !isMobile && !isDots) {
                        if ($galleryElement.data('square-img')) {
                            $galleryElement.css('height', $galleryElement.width() * 0.7411).css('overflow', 'hidden');
                        } else {
                            $galleryElement.css('height', $galleryElement.width() - 30).css('overflow', 'hidden');
                        }

	                    if ($galleryElement.data("thumb-position") == 'right') {
		                    $galleryElement.addClass('is-vertical-inited-right');
	                    } else {
		                    $galleryElement.addClass('is-vertical-inited');
	                    }
                    }
                    $galleryElement.prev('.preloader').remove();
                })(jQuery);
            }

            function firstImageGridLoaded() {
                (function ($) {
                    let $image = $('.product-gallery-grid-item img');
                    let $video = $('.product-gallery-grid-item video');
                    $image.prev('.preloader').remove();
                    $video.prev('.preloader').remove();
                })(jQuery);
            }
        </script>

		<?php if($this->is_admin_mode()) { ?>
            <script>
                (function ($) {
                    $('body').updateWidgetProductGalleries();
                })(jQuery);

                firstImageGridLoaded();
            </script>
		<?php } ?>

		<div class="<?= esc_attr($params['element_class']); ?>">
			<?php if ($params['type'] == 'native'): ?>
				<!--Product gallery native output-->
				<?php
					remove_action( 'woocommerce_before_single_product_summary', 'thegem_socials_sharing', 30 );
					remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_template_single_meta', 35 );
					do_action('woocommerce_before_single_product_summary');
				?>
			<?php elseif ($params['type'] == 'grid'): ?>
				<!--Product gallery grid output-->
				<div class="product-gallery-grid col-<?=$params['grid_columns']?>"
				     data-gallery="<?=$params['type']?>"
				     data-fancy="<?=$params['lightbox']?>"
				     data-zoom="<?=$params['zoom']?>"
				     data-color="<?=$params['elements_color']?>">

					<div class="product-gallery-grid-wrap" style="margin: -<?=$grid_gaps_size?>px; display: flex; flex-wrap: wrap;">
						<!--Product gallery grid images output-->
						<?php
						$attachments_grid_ids = !empty($params['grid_image_count']) ? array_slice($attachments_ids, 0, $params['grid_image_count']) : $attachments_ids;
						foreach ( $attachments_grid_ids as $key => $attachments_id ) {
							if ( $is_images_sizes && $params['grid_columns'] != '1x' && $params['grid_image_size'] == 'default') {
								$thumb_image_url = thegem_generate_thumbnail_src( $attachments_id, 'thegem-product-single' );
								$thumb_image_url_2x = thegem_generate_thumbnail_src( $attachments_id, 'thegem-product-single-2x' );
							} else {
								$thumb_image_url = wp_get_attachment_image_src( $attachments_id, 'full' );
								$thumb_image_url_2x = wp_get_attachment_image_src( $attachments_id, 'full' );
							}
							$full_image_url = wp_get_attachment_image_src( $attachments_id, 'full' );
							?>

							<?php if ( $thumb_image_url || $full_image_url ): ?>
								<div class="product-gallery-grid-item"
								     data-image-id="<?= esc_attr( $attachments_id ); ?>"
								     style="width: <?=$grid_column_width?>%; padding: <?=$grid_gaps_size?>px;">

									<!--Product gallery grid labels output-->
									<?php if ( $params['labels'] && $key == 0): ?>
										<div class="product-gallery-grid-elements" style="opacity: 0;">
											<div class="product-gallery-labels"><?=thegem_woocommerce_single_product_gallery_labels()?></div>
										</div>
									<?php endif; ?>

									<div class="product-gallery-image <?php if ( $params['zoom'] ): ?>init-zoom<?php endif;?> <?php if ( $params['lightbox'] ): ?>init-fancy<?php endif;?>">
										<?php if ( $params['lightbox'] ): ?>
											<a href="<?= esc_url( $full_image_url[0] ); ?>"
											   class="fancy-product-gallery"
											   data-fancybox-group="product-gallery-<?= esc_attr( $gallery_uid ); ?>"
											   data-fancybox="product-gallery-<?= esc_attr( $gallery_uid ); ?>"
											   data-full-image-url="<?= esc_url( $full_image_url[0] ); ?>"
											   data-elementor-open-lightbox="no">
												<div class="image-inner">
													<i class="product-gallery-fancy" style="opacity: 0;"></i>
													<?php if ( $params['skeleton_loader'] ): ?>
														<span class="preloader skeleton product-grid-gallery-skeleton"></span>
													<?php endif; ?>
													<img
															src="<?= esc_url( $thumb_image_url[0] ); ?>"
															<?php if ( $params['retina_ready'] ): ?>srcset="<?= esc_url( $thumb_image_url_2x[0] ); ?> 2x"<?php endif; ?>
															class="img-responsive"
															width="<?=$thumb_image_url[1]?>" height="<?=$thumb_image_url[2]?>"
															alt="<?= thegem_gallery_get_alt_text( $attachments_id ) ?>"
															<?php if (!$this->is_admin_mode()): ?>onload="firstImageGridLoaded()"<?php endif; ?>
													/>
												</div>
											</a>
										<?php else: ?>
											<div class="image-inner">
												<?php if ( $params['skeleton_loader'] ): ?>
													<span class="preloader skeleton product-grid-gallery-skeleton"></span>
												<?php endif; ?>
												<img
														src="<?= esc_url( $thumb_image_url[0] ); ?>"
														<?php if ( $params['retina_ready'] ): ?>srcset="<?php echo esc_url( $thumb_image_url_2x[0] ); ?> 2x"<?php endif; ?>
														class="img-responsive"
														width="<?=$thumb_image_url[1]?>" height="<?=$thumb_image_url[2]?>"
														alt="<?= thegem_gallery_get_alt_text( $attachments_id ) ?>"
														<?php if (!$this->is_admin_mode()): ?>onload="firstImageGridLoaded()"<?php endif; ?>
												/>
											</div>
										<?php endif; ?>
									</div>
								</div>
							<?php endif; ?>

							<?php
						}

						if (isset( $video_block ) && !empty($params['product_video'])) { ?>
							<?php
							if ( $is_images_sizes && $params['grid_columns'] != '1x') {
								$thumb_image_url = thegem_generate_thumbnail_src( $poster_id, 'thegem-product-single' );
								$thumb_image_url_2x = thegem_generate_thumbnail_src( $poster_id, 'thegem-product-single-2x' );
							} else {
								$thumb_image_url = wp_get_attachment_image_src( $poster_id, 'full' );
								$thumb_image_url_2x = wp_get_attachment_image_src( $poster_id, 'full' );
							}
							?>
							<!--Product gallery grid video output-->
							<div class="product-gallery-grid-item <?php if ( !$poster || $video_type == 'self' ): ?>item--video<?php endif; ?>"
							     data-video-type="<?= $video_type ?>"
							     data-video-autoplay="<?= $params['video_autoplay'] ?>"
							     data-video-poster="<?= $poster_id ?>"
							     data-video-gaps="<?= $grid_gaps_size ?>"
							     style="width: <?=$grid_column_width?>%; padding: <?=$grid_gaps_size?>px; background-color: transparent; overflow: hidden;">
								<?php if ( $params['lightbox'] ): ?>
									<a href="<?= $link ?>"
									   class="fancy-product-gallery"
									   data-fancybox-group="product-gallery-<?= esc_attr( $gallery_uid ); ?>"
									   data-fancybox="product-gallery-<?= esc_attr( $gallery_uid ); ?>"
									   data-elementor-open-lightbox="no">
										<div class="image-inner">
											<?php if ( $poster && $video_type != 'self' ): ?>
												<?php if ( $params['skeleton_loader'] ): ?>
													<span class="preloader skeleton product-grid-gallery-skeleton"></span>
												<?php endif; ?>
												<img src="<?= esc_url( $thumb_image_url[0] ); ?>"
													<?php if ( $params['retina_ready'] ): ?>
														srcset="<?= esc_url( $thumb_image_url_2x[0] ); ?> 2x"
													<?php endif; ?>
													 class="img-responsive" style="width: 100%; height: auto;"
													 width="<?=$thumb_image_url[1]?>" height="<?=$thumb_image_url[2]?>"
													 alt="<?= thegem_gallery_get_alt_text( $poster_id ) ?>"
													<?php if (!$this->is_admin_mode()) { ?>
														onload="firstImageGridLoaded()"
													<?php } ?>
												/>
												<i class="icon-play <?= $video_type ?>"></i>
											<?php else: ?>
												<span class="preloader skeleton product-grid-gallery-skeleton"></span>
												<?= $video_block ?>
											<?php endif; ?>
										</div>
									</a>
								<?php else: ?>
									<span class="preloader skeleton product-grid-gallery-skeleton"></span>
									<?= $video_block ?>
								<?php endif; ?>
							</div>
						<?php } ?>
					</div>
				</div>
			<?php else: ?>
				<!--Skeleton loader output-->
				<?php if (!empty($params['skeleton_loader'])) {
					echo '<div class="preloader skeleton product-gallery-skeleton ' . $isVerticalSkeleton . $isSingleImgSkeleton . '" >';
					echo '<div class="product-gallery-skeleton-image" style="padding-bottom:' . $skeletonPadding . '%"></div>';
					if (!$isSingleImg && ($params['type'] == 'horizontal' || $params['type'] == 'vertical')) {
						echo '<div class="product-gallery-skeleton-thumbs product-gallery-skeleton-thumbs-' . $params['type'] . '"></div>';
					}
					echo '</div>';
				} ?>
				<div class="product-gallery <?= $params['type'] ?>"
				     data-type="<?= esc_attr($params['show_image']); ?>"
				     data-thumb="<?= esc_attr($params['type']); ?>"
				     data-thumb-on-mobile="<?= esc_attr($params['thumb_on_mobile']); ?>"
				     data-thumb-position="<?= esc_attr($params['thumbs_position']); ?>"
				     data-fancy="<?= esc_attr($params['lightbox']); ?>"
				     data-zoom="<?= esc_attr($params['zoom']); ?>"
				     data-colors="<?= esc_attr($params['elements_color']); ?>"
				     data-auto-height="<?= esc_attr($params['auto_height']); ?>"
				     data-video-autoplay="<?= $params['video_autoplay'] ?>"
				     data-items-count="<?= esc_attr($params['single_columns']); ?>"
				     data-items-gaps="<?= esc_attr($params['single_gaps']); ?>"
				     data-square-img="<?= esc_attr($isSquareImg); ?>">

					<!--Preview gallery output-->
					<div class="product-gallery-slider-wrap <?= $params['lightbox'] ? 'init-fancy' : null; ?> <?= !empty($params['elements_color']) ? 'init-color' : null; ?>"
					     data-color="<?= esc_attr($params['elements_color']); ?>">
						<div class="product-gallery-slider owl-carousel <?= $params['type'] == 'dots' ? 'dots' : null; ?>">
							<?php
							// Preview gallery images output
							foreach ($attachments_ids as $i => $attachments_id) {
								$full_image_url = wp_get_attachment_image_src($attachments_id, 'full');
								if ($full_image_url): ?>
									<div class="product-gallery-slider-item"
									     <?php if ($params['type'] == 'single' && $params['single_columns'] > 1 && !$this->is_admin_mode()): ?>style="opacity: 0"<?php endif; ?>
									     data-image-id="<?= esc_attr($attachments_id); ?>">
										<div class="product-gallery-image <?= $params['zoom'] ? 'init-zoom' : null ?>">
											<?php if ($params['lightbox']): ?>
												<a href="<?= esc_url($full_image_url[0]); ?>" class="fancy-product-gallery"
												   data-fancybox-group="product-gallery-<?= esc_attr($gallery_uid); ?>"
												   data-fancybox="product-gallery-<?= esc_attr($gallery_uid); ?>"
												   data-full-image-url="<?= esc_url($full_image_url[0]); ?>"
												   data-elementor-open-lightbox="no">
													<div class="image-inner">
														<img src="<?= esc_url($full_image_url[0]); ?>"
														     data-ww="<?php echo esc_url($full_image_url[0]); ?>"
														     alt="<?= thegem_gallery_get_alt_text($attachments_id) ?>"
														     class="img-responsive"
														     width="<?=$full_image_url[1]?>" height="<?=$full_image_url[2]?>"
															<?php if ($i == 0 && !$this->is_admin_mode()) { ?>
																onload="firstImageLoaded()"
															<?php } ?>
														/>
													</div>
												</a>
											<?php else: ?>
												<div class="image-inner">
													<img src="<?= esc_url($full_image_url[0]); ?>"
													     data-ww="<?php echo esc_url($full_image_url[0]); ?>"
													     alt="<?= thegem_gallery_get_alt_text($attachments_id) ?>"
													     class="img-responsive"
													     width="<?=$full_image_url[1]?>" height="<?=$full_image_url[2]?>"
														<?php if ($i == 0 && !$this->is_admin_mode()) { ?>
															onload="firstImageLoaded()"
														<?php } ?>
													/>
												</div>
											<?php endif; ?>
										</div>
									</div>
								<?php endif;
							}

							// Preview gallery video output
							if (isset($video_block) && !empty($params['product_video'])) { ?>
								<div class="product-gallery-slider-item <?php if (!$poster || $video_type == 'self'): ?>item--video<?php endif; ?>"
								     data-video-type="<?= $video_type ?>">
									<?php if ($params['lightbox']): ?>
										<a href="<?= $link ?>"
										   class="fancy-product-gallery"
										   data-fancybox-group="product-gallery-<?= esc_attr($gallery_uid); ?>"
										   data-fancybox="product-gallery-<?= esc_attr($gallery_uid); ?>"
										   data-elementor-open-lightbox="no">
											<?php if ($poster && $video_type != 'self'): ?>
												<div class="image-inner">
													<img src="<?php echo esc_url($poster); ?>"
													     alt="<?= thegem_gallery_get_alt_text($poster_id) ?>"
													     class="img-responsive">
												</div>

												<i class="icon-play <?= $video_type ?>"></i>
											<?php else: ?>
												<?= $video_block ?>
											<?php endif; ?>
										</a>
									<?php else: ?>
										<?= $video_block ?>
									<?php endif; ?>
								</div>
							<?php } ?>
						</div>

						<?php
						// Preview gallery zoom icon output
						if ($params['lightbox']) {
							echo '<div class="product-gallery-fancy"></div>';
						}

						// Preview gallery labels output
						if ($params['labels']) { ?>
							<div class="product-gallery-labels">
								<?= thegem_woocommerce_single_product_gallery_labels($params['label_sale'], $params['label_new'], $params['label_out_stock']) ?>
							</div>
						<?php }
						?>
					</div>

					<!--Thumbnail gallery output-->
					<?php if (!$isSingleImg && ($params['type'] == 'horizontal' || $params['type'] == 'vertical')): ?>
						<div class="product-gallery-skeleton-thumbs product-gallery-skeleton-thumbs-<?= $params['type'] ?>"></div>
						<div class="product-gallery-thumbs-wrap <?= !empty($params['elements_color']) ? 'init-color' : null; ?>">
							<div class="product-gallery-thumbs owl-carousel">
								<?php
								// Thumbnail gallery images output
								foreach ($attachments_ids as $attachments_id) {
									if (thegem_get_option('woocommerce_activate_images_sizes')) {
										$thumb_image_url = thegem_generate_thumbnail_src($attachments_id, 'thegem-product-thumbnail');
										$thumb_image_url_2x = thegem_generate_thumbnail_src($attachments_id, 'thegem-product-thumbnail-2x');
										$thumb_vertical_image_url = thegem_generate_thumbnail_src($attachments_id, 'thegem-product-thumbnail-vertical');
										$thumb_vertical_image_url_2x = thegem_generate_thumbnail_src($attachments_id, 'thegem-product-thumbnail-vertical-2x');
									} else {
										$thumb_image_url = wp_get_attachment_image_src($attachments_id, apply_filters('single_product_small_thumbnail_size', 'woocommerce_gallery_thumbnail'));
										$thumb_image_url_2x = wp_get_attachment_image_src($attachments_id, apply_filters('single_product_small_thumbnail_size', 'woocommerce_gallery_thumbnail'));
										$thumb_vertical_image_url = wp_get_attachment_image_src($attachments_id, apply_filters('single_product_large_thumbnail_size', 'woocommerce_gallery_thumbnail'));
										$thumb_vertical_image_url_2x = wp_get_attachment_image_src($attachments_id, apply_filters('single_product_large_thumbnail_size', 'woocommerce_gallery_thumbnail'));
									}
									?>
									<?php if ($thumb_image_url || $thumb_vertical_image_url): ?>
										<div class="product-gallery-thumb-item"
										     data-image-id="<?= esc_attr($attachments_id); ?>">
											<div class="product-gallery-image">
												<img
													<?php if ($params['type'] == 'vertical'): ?>
														src="<?php echo esc_url($thumb_vertical_image_url[0]); ?>"
														<?php if ($params['retina_ready']): ?>
															srcset="<?php echo esc_url($thumb_vertical_image_url_2x[0]); ?> 2x"
														<?php endif; ?>
														data-ww="<?php echo esc_url($thumb_vertical_image_url[0]); ?>"
													<?php else: ?>
														src="<?php echo esc_url($thumb_image_url[0]); ?>"
														<?php if ($params['retina_ready']): ?>
															srcset="<?php echo esc_url($thumb_image_url_2x[0]); ?> 2x"
														<?php endif; ?>
													<?php endif; ?>
														alt="<?= thegem_gallery_get_alt_text($attachments_id) ?>"
														class="img-responsive"
												>
											</div>
										</div>
									<?php endif;
								}

								// Thumbnail gallery video output
								if (thegem_get_option('woocommerce_activate_images_sizes')) {
									$thumb_video_url = thegem_generate_thumbnail_src($poster_id, 'thegem-product-thumbnail');
									$thumb_video_url_2x = thegem_generate_thumbnail_src($poster_id, 'thegem-product-thumbnail-2x');
									$thumb_vertical_video_url = thegem_generate_thumbnail_src($poster_id, 'thegem-product-thumbnail-vertical');
									$thumb_vertical_video_url_2x = thegem_generate_thumbnail_src($poster_id, 'thegem-product-thumbnail-vertical-2x');
								} else {
									$thumb_video_url = wp_get_attachment_image_src($poster_id, apply_filters('single_product_small_thumbnail_size', 'woocommerce_gallery_thumbnail'));
									$thumb_video_url_2x = wp_get_attachment_image_src($poster_id, apply_filters('single_product_small_thumbnail_size', 'woocommerce_gallery_thumbnail'));
									$thumb_vertical_video_url = wp_get_attachment_image_src($poster_id, apply_filters('single_product_large_thumbnail_size', 'woocommerce_single'));
									$thumb_vertical_video_url_2x = wp_get_attachment_image_src($poster_id, apply_filters('single_product_large_thumbnail_size', 'woocommerce_single'));
								}

								if (isset($video_block) && !empty($params['product_video'])) { ?>
									<div class="product-gallery-thumb-item">
										<div class="product-gallery-image">
											<?php if ($poster): ?>
												<img
													<?php if ($params['type'] == 'vertical'): ?>
														src="<?php echo esc_url($thumb_vertical_video_url[0]); ?>"
														<?php if ($params['retina_ready']): ?>
															srcset="<?php echo esc_url($thumb_vertical_video_url_2x[0]); ?> 2x"
														<?php endif; ?>
														data-ww="<?php echo esc_url($thumb_vertical_video_url[0]); ?>"
													<?php else: ?>
														src="<?php echo esc_url($thumb_video_url[0]); ?>"
														<?php if ($params['retina_ready']): ?>
															srcset="<?php echo esc_url($thumb_video_url_2x[0]); ?> 2x"
														<?php endif; ?>
													<?php endif; ?>
														alt="<?= thegem_gallery_get_alt_text($poster_id) ?>"
														class="img-responsive"
												>
											<?php else: ?>
												<img src="<?= get_stylesheet_directory_uri() ?>/images/dummy/dummy.png"
												     alt="dummy"
												     class="img-responsive">
											<?php endif; ?>
											<i class="icon-play <?= $video_type ?>" style="color: <?= $poster ? '#ffffff' : '#dfe5e8' ?>"></i>
										</div>
									</div>
								<?php } ?>
							</div>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
        </div>

		<?php

		$return_html = trim(preg_replace('/\s\s+/', ' ', ob_get_clean()));

		echo thegem_templates_close_product(str_replace('-template-', '-te-', $this->get_name()), $this->get_title(), $return_html);
	}

}

if(defined('WC_PLUGIN_FILE')) {
	\Elementor\Plugin::instance()->widgets_manager->register(new TheGem_TemplateProductGallery());
}