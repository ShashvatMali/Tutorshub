<?php
namespace TheGem_Elementor;
use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Base_Tag;
use Elementor\Core\DynamicTags\Dynamic_CSS;
use Elementor\Core\Files\CSS\Post;
use TheGemGdpr;

/**
 * Class Plugin
 * Main Plugin class
 */
class Plugin {

	/**
	 * Instance
	 *
	 * @access private
	 * @static
	 *
	 * @var Plugin The single instance of the class.
	 */
	private static $_instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @access public
	 *
	 * @return Plugin An instance of the class.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;

	}

	/**
	 * widget_global_scripts
	 * Load global scripts & css files required plugin core.
	 *
	 * @access public
	 */
	public function widget_global_scripts() {

	}

	/**
	 * Register custom category for widgets
	 * @access public
	 */
	public function widget_categories( $el_manager ) {
		$el_manager->add_category(
			'thegem_elements',
			[
				'title' => __( 'TheGem Elements', 'thegem' ),
			]
		);
		$el_manager->add_category(
			'thegem_portfolios',
			[
				'title' => __( 'TheGem Portfolios', 'thegem' ),
			]
		);
		$el_manager->add_category(
			'thegem_blog',
			[
				'title' => __( 'TheGem Blog / Posts', 'thegem' ),
			]
		);
		$el_manager->add_category(
			'thegem_woocommerce',
			[
				'title' => __( 'TheGem WooCommerce', 'thegem' ),
			]
		);
		$el_manager->add_category(
			'thegem_header_builder',
			[
				'title' => __( 'TheGem Header Builder', 'thegem' ),
			]
		);
		$el_manager->add_category(
			'thegem_single_product_builder',
			[
				'title' => __( 'TheGem Single Product Builder', 'thegem' ),
			]
		);
		$el_manager->add_category(
			'thegem_product_archive_builder',
			[
				'title' => __( 'TheGem Product Archives Builder', 'thegem' ),
			]
		);
		$el_manager->add_category(
			'thegem_blog_archive_builder',
			[
				'title' => __( 'TheGem Archives Builder', 'thegem' ),
			]
		);
		$el_manager->add_category(
			'thegem_cart_builder',
			[
				'title' => __( 'TheGem Cart Builder', 'thegem' ),
			]
		);
		$el_manager->add_category(
			'thegem_checkout_builder',
			[
				'title' => __( 'TheGem Checkout Builder', 'thegem' ),
			]
		);
		$el_manager->add_category(
			'thegem_checkout_thanks_builder',
			[
				'title' => __( 'Purchase Summary Builder', 'thegem' ),
			]
		);
		$el_manager->add_category(
			'thegem_megamenu_builder',
			[
				'title' => __( 'TheGem Mega-Menu Builder', 'thegem' ),
			]
		);
		$el_manager->add_category(
			'thegem_single_post',
			[
				'title' => __( 'TheGem Single Post', 'thegem' ),
			]
		);
		$el_manager->add_category(
			'thegem_single_post_builder',
			[
				'title' => __( 'TheGem Single Post', 'thegem' ),
			]
		);
		$el_manager->add_category(
			'thegem_portfolio_builder',
			[
				'title' => __( 'TheGem Portfolio Page Builder', 'thegem' ),
			]
		);
		$el_manager->add_category(
			'thegem_loop_builder',
			[
				'title' => __( 'TheGem Loop Item Builder', 'thegem' ),
			]
		);
		$el_manager->add_category(
			'thegem_title_area_builder',
			[
				'title' => __( 'TheGem Title Area Builder', 'thegem' ),
			]
		);
		$el_manager->add_category(
			'thegem_deprecated',
			[
				'title' => __( 'TheGem Deprecated', 'thegem' ),
			]
		);
	}

	/**
	 * Register Widgets
	 *
	 * Register new Elementor widgets.
	 *
	 * @access public
	 */
	public function register_widgets($widgets_manager) {
		if(function_exists('thegem_scripts')) {
			foreach ( glob( THEGEM_ELEMENTOR_DIR . '/widgets/*/widget.php' ) as $filename ) {
				if ( empty( $filename ) || ! is_readable( $filename ) ) {
					continue;
				}
				require $filename;
			}
		}
		foreach($this->get_widget_list() as $widget) {
			if(thegem_get_option('content_widgets_' . $widget . '_disabled')) {
				$widgets_manager->unregister( 'thegem-'.$widget );
			}
		}
	}

	public function register_ajax() {
		foreach ( glob( THEGEM_ELEMENTOR_DIR . '/widgets/*/ajax.php' ) as $filename ) {
			if ( empty( $filename ) || ! is_readable( $filename ) ) {
				continue;
			}
			require $filename;
		}
	}

	/**
	 * Register Dynamic Tags
	 *
	 * Register new Elementor dynamic tags.
	 *
	 * @access public
	 */
	public function register_dynamic_tags($dynamic_tags) {

		$tags = array(
			array(
				'file'  =>  THEGEM_ELEMENTOR_DIR . '/dynamic-tags/custom-title-title.php',
				'class' => 'TheGem_Elementor\DynamicTags\Custom_Title_Title',
			),
			array(
				'file'  =>  THEGEM_ELEMENTOR_DIR . '/dynamic-tags/custom-title-rich-title.php',
				'class' => 'TheGem_Elementor\DynamicTags\Custom_Title_Rich_Title',
			),
			array(
				'file'  =>  THEGEM_ELEMENTOR_DIR . '/dynamic-tags/custom-title-excerpt.php',
				'class' => 'TheGem_Elementor\DynamicTags\Custom_Title_Excerpt',
			),
			array(
				'file'  =>  THEGEM_ELEMENTOR_DIR . '/dynamic-tags/custom-title-background.php',
				'class' => 'TheGem_Elementor\DynamicTags\Custom_Title_Background',
			),
			array(
				'file'  =>  THEGEM_ELEMENTOR_DIR . '/dynamic-tags/custom-title-color.php',
				'class' => 'TheGem_Elementor\DynamicTags\Custom_Title_Color',
			),
			array(
				'file'  =>  THEGEM_ELEMENTOR_DIR . '/dynamic-tags/custom-title-excerpt-color.php',
				'class' => 'TheGem_Elementor\DynamicTags\Custom_Title_Excerpt_Color',
			),
			array(
				'file'  =>  THEGEM_ELEMENTOR_DIR . '/dynamic-tags/custom-title-background-color.php',
				'class' => 'TheGem_Elementor\DynamicTags\Custom_Title_Background_Color',
			),
			array(
				'file'  =>  THEGEM_ELEMENTOR_DIR . '/dynamic-tags/custom-title-background-video.php',
				'class' => 'TheGem_Elementor\DynamicTags\Custom_Title_Background_Video',
			),
			array(
				'file'  =>  THEGEM_ELEMENTOR_DIR . '/dynamic-tags/custom-title-video-poster.php',
				'class' => 'TheGem_Elementor\DynamicTags\Custom_Title_Video_Poster',
			),
			array(
				'file'  =>  THEGEM_ELEMENTOR_DIR . '/dynamic-tags/custom-title-video-overlay.php',
				'class' => 'TheGem_Elementor\DynamicTags\Custom_Title_Video_Overlay',
			),
			//array(
			//	'file'  =>  THEGEM_ELEMENTOR_DIR . '/dynamic-tags/post-title-icon.php',
			// 	'class' => 'TheGem_Elementor\DynamicTags\Post_Title_Icon',
			//),
			array(
				'file'  =>  THEGEM_ELEMENTOR_DIR . '/dynamic-tags/post-title.php',
				'class' => 'TheGem_Elementor\DynamicTags\Post_Title',
			),
			array(
				'file'  =>  THEGEM_ELEMENTOR_DIR . '/dynamic-tags/post-url.php',
				'class' => 'TheGem_Elementor\DynamicTags\Post_URL',
			),
			array(
				'file'  =>  THEGEM_ELEMENTOR_DIR . '/dynamic-tags/post-excerpt.php',
				'class' => 'TheGem_Elementor\DynamicTags\Post_Excerpt',
			),
			array(
				'file'  =>  THEGEM_ELEMENTOR_DIR . '/dynamic-tags/post-featured-image.php',
				'class' => 'TheGem_Elementor\DynamicTags\Post_Featured_Image',
			),
			array(
				'file'  =>  THEGEM_ELEMENTOR_DIR . '/dynamic-tags/post-date.php',
				'class' => 'TheGem_Elementor\DynamicTags\Post_Date',
			),
			array(
				'file'  =>  THEGEM_ELEMENTOR_DIR . '/dynamic-tags/post-time.php',
				'class' => 'TheGem_Elementor\DynamicTags\Post_Time',
			),
			array(
				'file'  =>  THEGEM_ELEMENTOR_DIR . '/dynamic-tags/author-name.php',
				'class' => 'TheGem_Elementor\DynamicTags\Author_Name',
			),
			array(
				'file'  =>  THEGEM_ELEMENTOR_DIR . '/dynamic-tags/comments-number.php',
				'class' => 'TheGem_Elementor\DynamicTags\Comments_Number',
			),
			array(
				'file'  =>  THEGEM_ELEMENTOR_DIR . '/dynamic-tags/post-terms.php',
				'class' => 'TheGem_Elementor\DynamicTags\Post_Terms',
			),
			array(
				'file'  =>  THEGEM_ELEMENTOR_DIR . '/dynamic-tags/post-custom-field.php',
				'class' => 'TheGem_Elementor\DynamicTags\Post_Custom_Field',
			),
			array(
				'file'  =>  THEGEM_ELEMENTOR_DIR . '/dynamic-tags/custom-field.php',
				'class' => 'TheGem_Elementor\DynamicTags\Custom_Field',
			),
			array(
				'file'  =>  THEGEM_ELEMENTOR_DIR . '/dynamic-tags/custom-field-url.php',
				'class' => 'TheGem_Elementor\DynamicTags\Custom_Field_URL',
			),
			array(
				'file'  =>  THEGEM_ELEMENTOR_DIR . '/dynamic-tags/custom-field-number.php',
				'class' => 'TheGem_Elementor\DynamicTags\Custom_Field_Number',
			),
			array(
				'file'  =>  THEGEM_ELEMENTOR_DIR . '/dynamic-tags/popup.php',
				'class' => 'TheGem_Elementor\DynamicTags\Popup',
			),
			array(
				'file'  =>  THEGEM_ELEMENTOR_DIR . '/dynamic-tags/lightbox.php',
				'class' => 'TheGem_Elementor\DynamicTags\Lightbox',
			),
		);

		if(get_post_type() === 'thegem_pf_item' || (get_post_type() === 'thegem_templates' && thegem_get_template_type(get_the_id()) === 'portfolio') || (!empty($_REQUEST['action']) && $_REQUEST['action'] == 'thegem_templates_new')) {
			$tags[] = array(
				'file'  =>  THEGEM_ELEMENTOR_DIR . '/dynamic-tags/project-info.php',
				'class' => 'TheGem_Elementor\DynamicTags\Project_Info',
			);
			$tags[] = array(
				'file'  =>  THEGEM_ELEMENTOR_DIR . '/dynamic-tags/project-info-url.php',
				'class' => 'TheGem_Elementor\DynamicTags\Project_Info_URL',
			);
			$tags[] = array(
				'file'  =>  THEGEM_ELEMENTOR_DIR . '/dynamic-tags/project-info-number.php',
				'class' => 'TheGem_Elementor\DynamicTags\Project_Info_Number',
			);
		}

		if(class_exists( 'ACF' )) {
			$tags[] = array(
				'file'  =>  THEGEM_ELEMENTOR_DIR . '/dynamic-tags/acf-color.php',
				'class' => 'TheGem_Elementor\DynamicTags\ACF_Color',
			);
			$tags[] = array(
				'file'  =>  THEGEM_ELEMENTOR_DIR . '/dynamic-tags/acf-image.php',
				'class' => 'TheGem_Elementor\DynamicTags\ACF_Image',
			);
			$tags[] = array(
				'file'  =>  THEGEM_ELEMENTOR_DIR . '/dynamic-tags/acf-file.php',
				'class' => 'TheGem_Elementor\DynamicTags\ACF_File',
			);
			$tags[] = array(
				'file'  =>  THEGEM_ELEMENTOR_DIR . '/dynamic-tags/acf-gallery.php',
				'class' => 'TheGem_Elementor\DynamicTags\ACF_Image',
			);
			$tags[] = array(
				'file'  =>  THEGEM_ELEMENTOR_DIR . '/dynamic-tags/acf-number.php',
				'class' => 'TheGem_Elementor\DynamicTags\ACF_Gallery',
			);
			$tags[] = array(
				'file'  =>  THEGEM_ELEMENTOR_DIR . '/dynamic-tags/acf-text.php',
				'class' => 'TheGem_Elementor\DynamicTags\ACF_Text',
			);
			$tags[] = array(
				'file'  =>  THEGEM_ELEMENTOR_DIR . '/dynamic-tags/acf-url.php',
				'class' => 'TheGem_Elementor\DynamicTags\ACF_URL',
			);
		}

		if(function_exists('wpcf_admin_fields_get_groups')) {
			$tags[] = array(
				'file'  =>  THEGEM_ELEMENTOR_DIR . '/dynamic-tags/toolset-base.php',
				'class' => 'TheGem_Elementor\DynamicTags\Toolset_Base_Abstract',
			);
			$tags[] = array(
				'file'  =>  THEGEM_ELEMENTOR_DIR . '/dynamic-tags/toolset-date.php',
				'class' => 'TheGem_Elementor\DynamicTags\Toolset_Date',
			);
			$tags[] = array(
				'file'  =>  THEGEM_ELEMENTOR_DIR . '/dynamic-tags/toolset-gallery.php',
				'class' => 'TheGem_Elementor\DynamicTags\Toolset_Gallery',
			);
			$tags[] = array(
				'file'  =>  THEGEM_ELEMENTOR_DIR . '/dynamic-tags/toolset-image.php',
				'class' => 'TheGem_Elementor\DynamicTags\Toolset_Image',
			);
			$tags[] = array(
				'file'  =>  THEGEM_ELEMENTOR_DIR . '/dynamic-tags/toolset-text.php',
				'class' => 'TheGem_Elementor\DynamicTags\Toolset_Text',
			);
			$tags[] = array(
				'file'  =>  THEGEM_ELEMENTOR_DIR . '/dynamic-tags/toolset-url.php',
				'class' => 'TheGem_Elementor\DynamicTags\Toolset_URL',
			);
		}

		if(get_post_type() === 'thegem_title' || (get_post_type() === 'thegem_templates' && thegem_get_template_type(get_the_id()) === 'title') || get_post_type() === 'blocks') {
			\Elementor\Plugin::instance()->dynamic_tags->register_group( 'thegem-title' , [
				'title' => 'TheGem Custom Title'
			] );
		} else {
			\Elementor\Plugin::instance()->dynamic_tags->register_group( 'thegem' , [
				'title' => 'TheGem'
			] );
		}

		foreach ( $tags as $tag ) {
			if( ! empty( $tag['file'] ) && ! empty( $tag['class'] ) ){
				include_once( $tag['file'] );
				if( class_exists( $tag['class'] ) ){
					$class_name = $tag['class'];
					$dynamic_tags->register( new $class_name() );
				}
			}
		}
	}

	/**
	 * Section Change Gaps Control
	 *
	 * Change default elementor section gaps to TheGem theme dafault.
	 *
	 * @access public
	 */
	public function section_change_gaps_control($widget, $args) {
		$default_value = 'thegem';
		if(thegem_get_template_type( get_the_ID() ) === 'popup') {
			$default_value = 'default';
		}
		if(thegem_get_template_type( get_the_ID() ) === 'loop-item') {
			$default_value = 'no';
		}
		$widget->update_control(
			'gap',
			[
				'label' => __( 'Columns Gap', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => $default_value,
				'options' => [
					'default' => __( 'Default', 'elementor' ),
					'no' => __( 'No Gap', 'elementor' ),
					'narrow' => __( 'Narrow', 'elementor' ),
					'extended' => __( 'Extended', 'elementor' ),
					'wide' => __( 'Wide', 'elementor' ),
					'wider' => __( 'Wider', 'elementor' ),
					'thegem' => __( 'TheGem', 'elementor' ),
				],
			]
		);
	}

	/**
	 * Cantainer Layout Control
	 *
	 * Change default elementor container gaps and paddings to TheGem theme dafault.
	 *
	 * @access public
	 */
	public function container_thegem_layout_control($widget, $args) {
		$widget->add_control(
			'thegem_container_layout',
			[
				'label' => esc_html__( 'Default Paddings', 'thegem' ),
				'type' => Controls_Manager::SELECT,
				'default' => thegem_get_option('elementor_container_layout_defaults') === 'thegem' ? 'thegem' : 'elementor',
				'options' => [
					'thegem' => esc_html__( 'TheGem Defaults', 'thegem' ),
					'elementor' => esc_html__( 'Elementor Defaults', 'thegem' ),
				],
				'prefix_class' => 'thegem-e-con-layout-',
				'frontend_available' => true,
				'description' => sprintf(__('"TheGem Defaults" maximize compatibility with default TheGem paddings and gaps used for sections and columns in previous Elementor versions. If not needed, you can disable this option globally in <a href="%1$s" target="_blank">Theme Options</a>.', 'thegem'), admin_url('admin.php?page=thegem-theme-options#/extras')),
			]
		);
	}

	public function container_change_gaps_control($widget, $args) {
		$widget->update_responsive_control(
			'grid_gaps',
			[
				'selectors' => [
					'{{WRAPPER}}' => '--gap: {{ROW}}{{UNIT}} {{COLUMN}}{{UNIT}} !important;--row-gap: {{ROW}}{{UNIT}} !important;--column-gap: {{COLUMN}}{{UNIT}} !important;',
				],
			]
		);
	}

	public function section_offset_control($widget, $args) {
		$widget->start_injection( [
			'at' => 'before',
			'of' => 'column_position',
		] );
		$widget->add_responsive_control(
			'thegem_offset',
			[
				'label' => __( 'Offset', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
					'vh' => [
						'min' => 0,
						'max' => 100,
					],
					'vw' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => [ 'px', '%', 'vh', 'vw' ],
				'selectors' => [
					'{{WRAPPER}}' => 'height: calc(100vh - {{SIZE}}{{UNIT}});',
				],
				'condition' => [
					'height' => [ 'full' ],
				],
			]
		);
		$widget->end_injection();
	}

	public function global_change_lightbox_control($widget, $args) {
		$widget->update_control(
			'elementor_global_image_lightbox',
			[
				'default' => 'no'
			]
		);
	}

	public function change_template($post_ID, $post, $update) {
		if(!$update && ('yes' !== get_transient( 'wc_installing' )) && $post->post_name !== 'wishlist' && (empty($_REQUEST['action']) || $_REQUEST['action'] !== 'thegem_importer_process')) {
			if($post->post_type === 'page' && thegem_get_option('page_default_php_template') !== 'boxed') {
				update_post_meta($post_ID, '_wp_page_template', 'page-fullwidth.php');
			} elseif($post->post_type === 'post' && thegem_get_option('post_default_php_template') !== 'boxed') {
				update_post_meta($post_ID, '_wp_page_template', 'single-fullwidth.php');
			} elseif($post->post_type === 'thegem_pf_item' && thegem_get_option('portfolio_default_php_template') !== 'boxed') {
				update_post_meta($post_ID, '_wp_page_template', 'single-fullwidth.php');
			} elseif(function_exists('thegem_get_available_po_custom_post_types') && in_array($post->post_type, thegem_get_available_po_custom_post_types(), true)) {
				$post_type_data = get_option('thegem_options_page_settings_'.$post->post_type);
				$default_php_template = (!empty($post_type_data) && !empty($post_type_data['post_default_php_template']) && $post_type_data['post_default_php_template'] === 'boxed') ? 'boxed' : 'fullwidth';
				if($default_php_template === 'fullwidth') {
					update_post_meta($post_ID, '_wp_page_template', 'single-fullwidth.php');
				}
			} elseif($post->post_type === 'thegem_title') {
				update_post_meta($post_ID, '_wp_page_template', 'single-thegem_title-fullwidth.php');
			} elseif($post->post_type === 'thegem_footer') {
				update_post_meta($post_ID, '_wp_page_template', 'single-thegem_footer-fullwidth.php');
			}
		}
	}

	public function enqueue_editor_scripts() {
		wp_enqueue_script('thegem-elementor-editor', plugins_url( 'assets/js/editor.js', __FILE__ ), array('elementor-editor'), false, true);
		$templateClosePopupText = esc_html__('Do you want to close this template editor tab and return to the previous tab?', 'thegem');
		if(get_post_type() === 'thegem_templates') {
			if(thegem_get_template_type(get_the_id()) === 'loop-item') {
				$templateClosePopupText = esc_html__('Do you want to close this loop item editor tab and return to the previous tab with the posts extended grid/posts carousel?', 'thegem');
			}
			if(thegem_get_template_type(get_the_id()) === 'content') {
				$templateClosePopupText = esc_html__('Do you want to close this global section editor tab and return to the previous tab with the content editor?', 'thegem');
			}
		}
		wp_localize_script('thegem-elementor-editor', 'thegemElementor', array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'secret' => wp_create_nonce('thegem-elementor-secret'),
			'templateCreateLinkText' => esc_html__('Create Template', 'thegem'),
			'templateCreateLink' => admin_url('edit.php?post_type=thegem_templates&templates_type={type}#open-modal'),
			'templateEditLinkText' => esc_html__('Edit Template', 'thegem'),
			'templateEditLinkFormat' => add_query_arg(array('post' => '{postID}', 'action' => 'elementor', 'action' => 'elementor', 'thegemLoopItemCheckUpdate' => 1), admin_url( 'post.php' )),
			'templateClosePopupText' => $templateClosePopupText,
			'templateClosePopupCloseText' => esc_html__('Close and Return', 'thegem'),
			'templateClosePopupCancelText' => esc_html__('Cancel', 'thegem'),
			'templateImportLinkText' => esc_html__('Prebuilt Templates', 'thegem'),
			'templateImportLink' => admin_url('edit.php?post_type=thegem_templates&templates_type={type}#open-modal-import'),
		));
	}

	public function editor_after_enqueue_styles() {
		wp_enqueue_style('thegem-elementor-editor', plugins_url( 'assets/css/editor.css', __FILE__ ), array('elementor-editor'));
	}

	public function enqueue_preview_styles() {
		wp_enqueue_style('thegem-elementor-editor-preview', plugins_url( 'assets/css/editor-preview.css', __FILE__ ), array('editor-preview'));
	}

	public function get_preset_settings() {
		if ( ! check_ajax_referer( 'thegem-elementor-secret', 'secret' ) ) {
			wp_send_json_error( __( 'Invalid request', 'thegem' ), 403 );
		}

		if ( empty( $_REQUEST['widget'] ) ) {
			wp_send_json_error( __( 'Incomplete request', 'thegem' ), 404 );
		}

		$widget = \Elementor\Plugin::instance()->widgets_manager->get_widget_types($_REQUEST['widget']);

		$data = false;
		if(method_exists($widget, 'get_preset_data')) {
			$data = $widget->get_preset_data();
		}

		if ( !$data ) {
			wp_send_json_error( __( 'Not found', 'thegem' ), 404 );
		}

		wp_send_json_success( $data, 200 );
	}

	public function add_thegem_icons_tabs($tabs) {
		$theme_url = THEGEM_THEME_URI;
		$tabs['thegem-elegant'] = [
			'name' => 'thegem-elegant',
			'label' => __( 'TheGem - Elegant', 'thegem' ),
			'url' => $theme_url . '/css/icons-elegant.css',
			'enqueue' => [ $theme_url . '/css/icons-elegant.css' ],
			'prefix' => '',
			'displayPrefix' => 'gem-elegant',
			'labelIcon' => 'gem-elegant gem-elegant-label',
			'ver' => '1.0.0',
			'fetchJson' => THEGEM_ELEMENTOR_URL . '/assets/icons/gem-elegant.js',
			'native' => true,
		];
		$tabs['thegem-mdi'] = [
			'name' => 'thegem-mdi',
			'label' => __( 'TheGem - Material Design', 'thegem' ),
			'url' => $theme_url . '/css/icons-material.css',
			'enqueue' => [ $theme_url . '/css/icons-material.css' ],
			'prefix' => 'mdi-',
			'displayPrefix' => 'gem-mdi',
			'labelIcon' => 'gem-mdi gem-mdi-label',
			'ver' => '1.0.0',
			'fetchJson' => THEGEM_ELEMENTOR_URL . '/assets/icons/gem-mdi.js',
			'native' => true,
		];
		$tabs['thegem-hbi'] = [
			'name' => 'thegem-hbi',
			'label' => __( 'TheGem - Header Builder', 'thegem' ),
			'url' => $theme_url . '/css/icons-thegem-header.css',
			'enqueue' => [ $theme_url . '/css/icons-thegem-header.css' ],
			'prefix' => '',
			'displayPrefix' => 'tgh-icon',
			'labelIcon' => 'tgh-icon tgh-icon-label',
			'ver' => '1.0.0',
			'fetchJson' => THEGEM_ELEMENTOR_URL . '/assets/icons/gem-hbi.js',
			'native' => true,
		];
		return $tabs;
	}

	public function widgets_black_list($black_list) {
		global $wp_widget_factory;
		foreach ( $wp_widget_factory->widgets as $widget_class => $widget_obj ) {

			if(substr($widget_class, 0, 7) === 'The_Gem') {
				$black_list[] = $widget_class;
			}

			if ( in_array( $widget_class, $black_list ) ) {
				continue;
			}
		}
		return $black_list;
	}

	public function remove_experiments_additional_custom_breakpoints($exp_manager) {
		$exp_manager->remove_feature('additional_custom_breakpoints');
	}

	public function remove_experiments_e_element_cache($exp_manager, $experimental_data) {
		if($experimental_data['name'] === 'e_element_cache') {
			$exp_manager->remove_feature('e_element_cache');
		}
	}

	public function remove_e_optimized_control_loading($exp_manager) {
		$exp_manager->remove_feature('e_optimized_control_loading');
	}

	public function remove_e_image_loading_optimization($exp_manager, $data) {
		if($data['name'] === 'e_image_loading_optimization') {
			$exp_manager->remove_feature('e_image_loading_optimization');
		}
	}

	public function fix_custom_title($post_css) {
		if(get_post_type($post_css->get_post_id()) === 'thegem_title' || (get_post_type($post_css->get_post_id()) === 'thegem_templates' && thegem_get_template_type($post_css->get_post_id()) === 'title')) {
			$post_css->get_stylesheet()->add_rules('body:not(.elementor-editor-active):not(.elementor-editor-preview) .page-title-block.custom-page-title .elementor', array('opacity' => '1'));
		}
	}

	public function include_controls() {
		require_once( __DIR__ . '/controls/background-light.php' );
		require_once( __DIR__ . '/controls/query-control.php' );
	}

	public function register_query_control() {
		$controls_manager = \Elementor\Plugin::$instance->controls_manager;
		$controls_manager->register( new QueryControl() );
	}

	public function register_controls() {
		$controls_manager = \Elementor\Plugin::$instance->controls_manager;
		$controls_manager->add_group_control( 'background-light', new Group_Control_Background_Light() );
	}

	public function widgets_sidebar_change_render($widget_content, $element) {
		if(isset($GLOBALS['thegem_template_type']) && ($GLOBALS['thegem_template_type'] === 'single-product' || $GLOBALS['thegem_template_type'] === 'product-archive')) {
			if ('sidebar' === $element->get_name()) {
				$thegem_sidebar_classes = 'portfolio-filters-list native style-sidebar';
				if (!empty(thegem_get_option('categories_collapsible'))) {
					$thegem_sidebar_classes .= ' categories-widget-collapsible';
				}
				return '
					<div class="' . $thegem_sidebar_classes . '">
						<div class="widget-area-wrap">
							<div class="page-sidebar widget-area">' . $widget_content . '</div>
						</div>
					</div>
				';
			}
		}
		
		return $widget_content;
	}

	public function add_preview_settings_section(\Elementor\Controls_Stack $controls_stack) {
		if(thegem_get_template_type(get_the_ID()) === 'single-post' || thegem_get_template_type(get_the_ID()) === 'loop-item' || thegem_get_template_type(get_the_ID()) === 'title') {
			$controls_stack->start_controls_section(
				'thegem_preview_settings',
				[
					'label' => esc_html__( 'Preview Settings', 'thegem' ),
					'tab' => Controls_Manager::TAB_SETTINGS,
				]
			);

			$post_types = [
				'post' => __('Post', 'thegem'),
				'page' => __('Page', 'thegem'),
			];
			$post_types_data = get_post_types(array(
				'public' => true,
				'_builtin' => false
			), 'objects');

			foreach ($post_types_data as $post_type) {
				if (!empty($post_type->name) && !in_array($post_type->name, ['elementor_library', 'thegem_title', 'thegem_footer', 'thegem_templates'])) {
					$post_types[$post_type->name] = $post_type->label;
				}
			}

			if(thegem_get_template_type(get_the_ID()) === 'loop-item') {
				$post_types['thegem_team_person'] = __('Team Persons', 'thegem');
				$post_types['thegem_testimonial'] = __('Testimonials', 'thegem');
			}

			$controls_stack->add_control(
				'thegem_preview_type',
				[
					'label' => __('Post Type to Preview Dynamic Content', 'thegem'),
					'type' => \Elementor\Controls_Manager::SELECT,
					'label_block' => true,
					'options' => $post_types,
					'default' => 'post',
					'save_always' => 'true',
				]
			);

			foreach ($post_types as $post_type_name => $post_type_label) {
				$controls_stack->add_control(
					'thegem_preview_post_' . $post_type_name,
					[
						'label' => __('Select Post', 'thegem'),
						'type' => 'gem-query-control',
						'search' => 'thegem_get_posts_by_query',
						'render' => 'thegem_get_posts_title_by_id',
						'post_type' => $post_type_name,
						'label_block' => true,
						'multiple' => false,
						'condition' => [
							'thegem_preview_type' => $post_type_name,
						],
					]
				);
			
			}

			$controls_stack->add_control(
				'thegem_apply_preview',
				[
					'type' => Controls_Manager::BUTTON,
					'label' => esc_html__( 'Apply & Preview', 'thegem' ),
					'label_block' => true,
					'show_label' => false,
					'text' => esc_html__( 'Apply & Preview', 'thegem' ),
					'separator' => 'none',
					'event' => 'TheGemApplyPreview',
				]
			);

			if(thegem_get_template_type(get_the_ID()) === 'loop-item') {
				$controls_stack->add_responsive_control(
					'thegem_preview_width',
					[
						'label' => esc_html__( 'Width', 'thegem' ),
						'type' => Controls_Manager::SLIDER,
						'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
						'range' => [
							'px' => [
								'min' => 200,
								'max' => 1140,
							],
						],
						'selectors' => [
							'{{WRAPPER}} #main-content .thegem-template-wrapper > .elementor' => 'width: {{SIZE}}{{UNIT}};',
						],
					]
				);
			}

			$controls_stack->end_controls_section();
		}

		if(thegem_get_template_type(get_the_ID()) === 'single-product') {
			$controls_stack->start_controls_section(
				'thegem_preview_settings',
				[
					'label' => esc_html__( 'Preview Settings', 'thegem' ),
					'tab' => Controls_Manager::TAB_SETTINGS,
				]
			);

			$controls_stack->add_control(
				'thegem_preview_product',
				[
					'label' => __('Select Product', 'thegem'),
					'type' => 'gem-query-control',
					'search' => 'thegem_get_posts_by_query',
					'render' => 'thegem_get_posts_title_by_id',
					'post_type' => 'product',
					'label_block' => true,
					'multiple' => false,
				]
			);

			$controls_stack->add_control(
				'thegem_apply_preview',
				[
					'type' => Controls_Manager::BUTTON,
					'label' => esc_html__( 'Apply & Preview', 'thegem' ),
					'label_block' => true,
					'show_label' => false,
					'text' => esc_html__( 'Apply & Preview', 'thegem' ),
					'separator' => 'none',
					'event' => 'TheGemApplyPreview',
				]
			);

			$controls_stack->end_controls_section();
		}

		if(thegem_get_template_type(get_the_ID()) === 'portfolio') {
			$controls_stack->start_controls_section(
				'thegem_preview_settings',
				[
					'label' => esc_html__( 'Preview Settings', 'thegem' ),
					'tab' => Controls_Manager::TAB_SETTINGS,
				]
			);

			$controls_stack->add_control(
				'thegem_preview_portfolio',
				[
					'label' => __('Select Portfolio', 'thegem'),
					'type' => 'gem-query-control',
					'search' => 'thegem_get_posts_by_query',
					'render' => 'thegem_get_posts_title_by_id',
					'post_type' => 'thegem_pf_item',
					'label_block' => true,
					'multiple' => false,
				]
			);

			$controls_stack->add_control(
				'thegem_apply_preview',
				[
					'type' => Controls_Manager::BUTTON,
					'label' => esc_html__( 'Apply & Preview', 'thegem' ),
					'label_block' => true,
					'show_label' => false,
					'text' => esc_html__( 'Apply & Preview', 'thegem' ),
					'separator' => 'none',
					'event' => 'TheGemApplyPreview',
				]
			);

			$controls_stack->end_controls_section();
		}

		if(thegem_get_template_type(get_the_ID()) === 'blog-archive' || thegem_get_template_type(get_the_ID()) === 'product-archive') {
			$controls_stack->start_controls_section(
				'thegem_preview_settings',
				[
					'label' => esc_html__( 'Preview Settings', 'thegem' ),
					'tab' => Controls_Manager::TAB_SETTINGS,
				]
			);

			$taxonomies = [
				'category' => __('Post Category', 'thegem'),
				'post_tag' => __('Post Tag', 'thegem'),
			];
			$taxonomies_data = get_taxonomies(array(
				'public' => true,
				'_builtin' => false
			), 'objects');

			foreach ($taxonomies_data as $tax) {
				if (!empty($tax->object_type) && !in_array('product', $tax->object_type)) {
					$taxonomies[$tax->name] = $tax->label;
				}
			}

			if(thegem_get_template_type(get_the_ID()) === 'product-archive') {
				$taxonomies = [
					'product_cat' => __('Product Category', 'thegem'),
					'product_tag' => __('Product Tag', 'thegem'),
				];
			}

			$controls_stack->add_control(
				'thegem_preview_type',
				[
					'label' => __('Taxonomy to Preview Dynamic Content', 'thegem'),
					'type' => \Elementor\Controls_Manager::SELECT,
					'label_block' => true,
					'options' => $taxonomies,
					'default' => 'category',
					'save_always' => 'true',
				]
			);

			foreach ($taxonomies as $tax_name => $tax_label) {
				$controls_stack->add_control(
					'thegem_preview_term_' . $tax_name,
					[
						'label' => __('Select Term', 'thegem'),
						'type' => 'gem-query-control',
						'search' => 'thegem_get_taxonomy_terms_by_tax',
						'render' => 'thegem_get_taxonomy_terms_by_id',
						'taxonomy' => $tax_name,
						'label_block' => true,
						'multiple' => false,
						'condition' => [
							'thegem_preview_type' => $tax_name,
						],
					]
				);
			
			}

			$controls_stack->add_control(
				'thegem_apply_preview',
				[
					'type' => Controls_Manager::BUTTON,
					'label' => esc_html__( 'Apply & Preview', 'thegem' ),
					'label_block' => true,
					'show_label' => false,
					'text' => esc_html__( 'Apply & Preview', 'thegem' ),
					'separator' => 'none',
					'event' => 'TheGemApplyPreview',
				]
			);

			$controls_stack->end_controls_section();
		}
	}

	public function widget_video_gdpr_render($widget_content, $element) {
		if ('video' === $element->get_name()) {
			if (class_exists('TheGemGdpr')) {
				$type = null;
				$settings = $element->get_settings_for_display();
				if ( 'youtube' === $settings['video_type'] ) {
					$type = TheGemGdpr::CONSENT_NAME_YOUTUBE;
				} elseif ( 'vimeo' === $settings['video_type'] ) {
					$type = TheGemGdpr::CONSENT_NAME_VIMEO;
				}
				if (!empty($type)) {
					$widget_content = TheGemGdpr::getInstance()->replace_disallowed_content($widget_content, $type);
				}
			}
		}
		return $widget_content;
	}

	public function widget_google_maps_gdpr_render($widget_content, $element) {
		if ('google_maps' === $element->get_name()) {
			if (class_exists('TheGemGdpr')) {
				$widget_content = TheGemGdpr::getInstance()->replace_disallowed_content($widget_content, TheGemGdpr::CONSENT_NAME_GOOGLE_MAPS);
			}
		}
		return $widget_content;
	}

	public function widgets_gdpr_disable_cache($status, $raw_data, $obj) {
		$data = $obj->get_data();
		$settings = $raw_data['settings'];
		$type = null;

		if ('video' === $obj->get_name()) {
			if ( empty($settings['video_type']) || 'youtube' === $settings['video_type'] ) {
				$type = TheGemGdpr::CONSENT_NAME_YOUTUBE;
			} elseif ( 'vimeo' === $settings['video_type'] ) {
				$type = TheGemGdpr::CONSENT_NAME_VIMEO;
			}
		}
		if ('google_maps' === $obj->get_name()) {
			$type = TheGemGdpr::CONSENT_NAME_GOOGLE_MAPS;
		}
		if($type) {
			$status = array_key_exists($type, TheGemGdpr::getInstance()->consents);
		}

		return $status;
	}

	public function widgets_is_dynamic_content($status, $raw_data, $obj) {
		$data = $obj->get_data();
		$settings = $raw_data['settings'];

		if('thegem-testimonials' === $obj->get_name() || 'thegem-team' === $obj->get_name()) {
			if(!empty($settings['skin_source']) && $settings['skin_source'] === 'builder') {
				$status = true;
			}
		}

		return $status;
	}

	public function widget_text_editor_render($widget_content, $element) {
		if ('text-editor' === $element->get_name() && !\Elementor\Plugin::$instance->editor->is_edit_mode()) {

			$should_render_inline_editing = true;

			$content = $element->get_settings_for_display( 'editor' );
			$content = apply_filters( 'widget_text', $content, $element->get_settings() );

			$content = shortcode_unautop( $content );
			$content = do_shortcode( $content );
			$content = wptexturize( $content );

			if ( $GLOBALS['wp_embed'] instanceof \WP_Embed ) {
				$content = $GLOBALS['wp_embed']->autoembed( $content );
			}

			if ( $should_render_inline_editing ) {
				$element->add_render_attribute( 'editor', 'class', [ 'elementor-text-editor', 'elementor-clearfix' ] );
			}

			ob_start();

			?>
			<?php if ( $should_render_inline_editing ) { ?>
				<div <?php $element->print_render_attribute_string( 'editor' ); ?>>
			<?php } ?>
			<?php // PHPCS - the main text of a widget should not be escaped.
					echo $content; // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<?php if ( $should_render_inline_editing ) { ?>
				</div>
			<?php } ?>
			<?php
			$widget_content = ob_get_clean();

		}
		return $widget_content;
	}

	public function thegem_dynamic_popup_to_display($popup_data) {
		if(empty($GLOBALS['thegem_dynamic_popup_to_display']) || !is_array($GLOBALS['thegem_dynamic_popup_to_display'])) {
			return $popup_data;
		}
		foreach($GLOBALS['thegem_dynamic_popup_to_display'] as $popup_to_display) {
			$template_id = $popup_to_display;
			$exists = false;
			foreach($popup_data as $pd) {
				if($pd['id'] === 'thegem-dynamic-popup-' . $template_id) {
					$exists = true;
				}
			}
			if(!$exists) {
				$popup_data[] = array(
					'template' => $template_id,
					'active' => 1,
					'id' => 'thegem-dynamic-popup-' . $template_id,
					'hide_for_logged_in_users' => 0,
					'triggers' => array()
				);
			}
		}

		return $popup_data;
	}

	public function section_class_replace($elements_manager) {
		$elements_manager->unregister_element_type('section');
		require THEGEM_ELEMENTOR_DIR . '/section.php';
		$elements_manager->register_element_type( new \Elementor\Element_TheGem_Section() );
	}

	public function spacer_class_replace($elements_manager) {
		$elements_manager->unregister('spacer');
		require THEGEM_ELEMENTOR_DIR . '/spacer.php';
		$elements_manager->register( new \Elementor\Widget_TheGem_Spacer() );
	}

	function section_class_replace_editor_js() {
		$wp_scripts = wp_scripts();

		if ( isset( $wp_scripts->registered['elementor-editor'] ) ) {
			$wp_scripts->registered['elementor-editor']->src = plugins_url( 'assets/js/elementor-editor.js', __FILE__ );
			$wp_scripts->registered['elementor-editor']->ver .= '-thegem-' . THEGEM_THEME_VERSION;
		}
	}

	/**
	 *  Plugin class constructor
	 *
	 * Register plugin action hooks and filters
	 *
	 * @access public
	 */
	public function __construct() {

		// Register global widget scripts
		add_action( 'elementor/frontend/after_register_scripts', [ $this, 'widget_global_scripts' ] );

		// Register categories
		add_action( 'elementor/elements/categories_registered', [ $this, 'widget_categories'] );

		// Register widgets
		add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );

		// Register dynamic tags
		add_action( 'elementor/dynamic_tags/register', [ $this, 'register_dynamic_tags' ] );

		//Change section default gap
		add_action( 'elementor/element/section/section_layout/before_section_end', [ $this, 'section_change_gaps_control' ], 10, 2);

		//Change section default gap
		add_action( 'elementor/element/section/section_layout/before_section_end', [ $this, 'section_offset_control' ], 10, 2);

		if(!thegem_get_option('elementor_container_layout_disabled')) {
			add_action( 'elementor/element/container/section_layout_container/after_section_start', [ $this, 'container_thegem_layout_control' ], 10, 2);
		}

		//Change container default gap css
		add_action( 'elementor/element/container/section_layout_container/before_section_end', [ $this, 'container_change_gaps_control' ], 10, 2);

		//Change default LightBox
		add_action( 'ementor/element/global-settings/lightbox/before_section_end', [ $this, 'global_change_lightbox_control' ], 10, 2);

		//Change page default template
		add_action( 'wp_insert_post', [ $this, 'change_template' ], 10, 3);
		add_filter( 'default_page_template_title', function() { return esc_html__('TheGem Boxed', 'thegem'); });

		//Fix Custom Title
		add_action( 'elementor/css-file/post/parse', [ $this, 'fix_custom_title' ] );

		// Add Scripts
		add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'enqueue_editor_scripts' ], 20 );

		add_action( 'elementor/preview/enqueue_styles', [ $this, 'enqueue_preview_styles' ] );

		add_action( 'elementor/editor/after_enqueue_styles', [ $this, 'editor_after_enqueue_styles' ] );

		add_action( 'wp_ajax_thegem_elementor_get_preset_settings', [ $this, 'get_preset_settings' ] );

		add_filter( 'elementor/icons_manager/additional_tabs', [ $this, 'add_thegem_icons_tabs' ] );

		add_filter( 'elementor/widgets/black_list', [ $this, 'widgets_black_list' ] );

		add_action( 'elementor/documents/register_controls', [ $this, 'add_preview_settings_section' ] );

		/*if(!thegem_get_option('custom_breakpoints_feature')) {
			add_action('elementor/experiments/default-features-registered', [ $this, 'remove_experiments_additional_custom_breakpoints' ]);
		}*/

		add_action('elementor/experiments/default-features-registered', [ $this, 'remove_e_optimized_control_loading' ]);

		/*if (!TheGemGdpr::is_empty_options()) {
			add_action('elementor/experiments/feature-registered', [ $this, 'remove_experiments_e_element_cache' ], 10, 2);
		}*/

		if(thegem_get_option('page_speed_image_load') === 'js' && !thegem_get_option('e_image_loading_optimization_feature')) {
			add_action('elementor/experiments/feature-registered', [ $this, 'remove_e_image_loading_optimization' ], 10 , 2);
			add_filter('pre_option_elementor_optimized_image_loading', function() { return '0'; });
		}

		// Change sidebar render for product && archive builders
		add_filter( 'elementor/widget/render_content', [$this, 'widgets_sidebar_change_render'], 10, 2);

		// Change video render for gdpr
		add_filter( 'elementor/widget/render_content', [ $this, 'widget_video_gdpr_render' ], 10, 2);

		// Change google maps render for gdpr
		add_filter( 'elementor/widget/render_content', [ $this, 'widget_google_maps_gdpr_render' ], 10, 2);

		add_filter( 'elementor/element/is_dynamic_content', [ $this, 'widgets_gdpr_disable_cache' ], 10, 3);
		add_filter( 'elementor/element/is_dynamic_content', [ $this, 'widgets_is_dynamic_content' ], 10, 3);

		// Change text editor render
		add_filter( 'elementor/widget/render_content', [ $this, 'widget_text_editor_render' ], 10, 2);

		add_filter('thegem_popup_data', array($this, 'thegem_dynamic_popup_to_display'));

		// Include controls files
		$this->include_controls();

		// Register controls
		add_action( 'elementor/controls/register', [ $this, 'register_query_control' ] );
		add_action( 'elementor/controls/controls_registered', [ $this, 'register_controls' ] );

		if ( version_compare( ELEMENTOR_VERSION, '3.19.0', '>=' ) ) {
			// Section class replace
			add_action( 'elementor/elements/elements_registered', [ $this, 'section_class_replace' ] );
			add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'section_class_replace_editor_js' ] );
			add_filter( 'elementor/frontend/after_register_styles', function() {
				$min_suffix = \Elementor\Utils::is_script_debug() ? '' : '.min';
				$direction_suffix = is_rtl() ? '-rtl' : '';
				//$has_custom_breakpoints = \Elementor\Plugin::$instance->breakpoints->has_custom_breakpoints();
				wp_register_style(
					'elementor-frontend-legacy',
					plugins_url( 'assets/css/frontend-legacy' . $direction_suffix . $min_suffix . '.css', __FILE__ ),
					//$this->get_frontend_file_url( 'frontend-legacy' . $direction_suffix . $min_suffix . '.css', $has_custom_breakpoints ),
					[],
					ELEMENTOR_VERSION
				);
				global $wp_styles;
				$wp_styles->registered['elementor-frontend']->deps[] = 'elementor-frontend-legacy';
			});
		}

		if ( version_compare( ELEMENTOR_VERSION, '3.21.0', '>=' ) ) {
			// Section class replace
			add_action( 'elementor/widgets/register', [ $this, 'spacer_class_replace' ] );
		}

		// WPML Compatibility
		add_filter( 'wpml_elementor_widgets_to_translate', [ $this, 'wpml_widgets_to_translate_filter' ] );

		require THEGEM_ELEMENTOR_DIR . '/widgets/section-parallax/init.php';
		require THEGEM_ELEMENTOR_DIR . '/widgets/inner-section-absolute/init.php';
		require THEGEM_ELEMENTOR_DIR . '/widgets/section-fullpage/init.php';
		require THEGEM_ELEMENTOR_DIR . '/widgets/heading-extended/init.php';
		require THEGEM_ELEMENTOR_DIR . '/widgets/divider-extended/init.php';
		require THEGEM_ELEMENTOR_DIR . '/widgets/text-editor-dropcap/init.php';
		require THEGEM_ELEMENTOR_DIR . '/widgets/interactions/init.php';

		$this->register_ajax();

		require THEGEM_ELEMENTOR_DIR . '/thegem-options-section.php';
		require THEGEM_ELEMENTOR_DIR . '/editor-styles.php';

	}

	public static function dynamic_tags_acf_get_control_options( $types ) {
		// ACF >= 5.0.0
		if ( function_exists( 'acf_get_field_groups' ) ) {
			$acf_groups = acf_get_field_groups();
		} else {
			$acf_groups = apply_filters( 'acf/get_field_groups', [] );
		}

		$groups = [];

		$options_page_groups_ids = [];

		if ( function_exists( 'acf_options_page' ) ) {
			$pages = acf_options_page()->get_pages();
			foreach ( $pages as $slug => $page ) {
				$options_page_groups = acf_get_field_groups( [
					'options_page' => $slug,
				] );

				foreach ( $options_page_groups as $options_page_group ) {
					$options_page_groups_ids[] = $options_page_group['ID'];
				}
			}
		}

		foreach ( $acf_groups as $acf_group ) {
			// ACF >= 5.0.0
			if ( function_exists( 'acf_get_fields' ) ) {
				if ( isset( $acf_group['ID'] ) && ! empty( $acf_group['ID'] ) ) {
					$fields = acf_get_fields( $acf_group['ID'] );
				} else {
					$fields = acf_get_fields( $acf_group );
				}
			} else {
				$fields = apply_filters( 'acf/field_group/get_fields', [], $acf_group['id'] );
			}

			$options = [];

			if ( ! is_array( $fields ) ) {
				continue;
			}

			$has_option_page_location = in_array( $acf_group['ID'], $options_page_groups_ids, true );
			$is_only_options_page = $has_option_page_location && 1 === count( $acf_group['location'] );

			foreach ( $fields as $field ) {
				if ( ! in_array( $field['type'], $types, true ) ) {
					continue;
				}

				// Use group ID for unique keys
				if ( $has_option_page_location ) {
					$key = 'options:' . $field['name'];
					$options[ $key ] = esc_html__( 'Options', 'thegem' ) . ':' . $field['label'];
					if ( $is_only_options_page ) {
						continue;
					}
				}

				$key = $field['key'] . ':' . $field['name'];
				$options[ $key ] = $field['label'];
			}

			if ( empty( $options ) ) {
				continue;
			}

			if ( 1 === count( $options ) ) {
				$options = [ -1 => ' -- ' ] + $options;
			}

			$groups[] = [
				'label' => $acf_group['title'],
				'options' => $options,
			];
		} // End foreach().

		return $groups;
	}

	public static function dynamic_tags_cf_get_control_options( $type = '' ) {
		$groups = array();
		$post_types = array();
		$post_type = get_post_type();

		if($post_type == 'thegem_templates') {
			$post_types = array_unique(array_merge(array('post', 'page', 'product', 'thegem_pf_item', 'thegem_team_person', 'thegem_testimonial'), thegem_get_list_po_custom_post_types()));
		} else {
			$post_types = array($post_type);
		}

		foreach($post_types as $pt) {
			$fields = self::dynamic_tags_cf_get_fileds($pt);
			$options = array();
			foreach($fields as $field) {
				if(!empty($type) && $type != $field['type']) continue;
				$options[$field['key']] = $field['title'];
			}
			if(!empty($options)) {
				$groups[] = array(
					'label' => get_post_type_object($pt)->label,
					'options' => $options
				);
			}
		}

		return $groups;
	}

	public static function dynamic_tags_cf_get_fileds($post_type) {
		$option = $post_type;
		if($post_type == 'page') {
			$option = 'default';
		}
		if($post_type == 'thegem_pf_item') {
			$option = 'portfolio';
		}
		if($post_type == 'thegem_team_person') {
			$option = 'team_persons';
		}
		if($post_type == 'thegem_testimonial') {
			$option = 'testimonials';
		}
		$pt_data = thegem_theme_options_get_page_settings($option);
		$custom_fields = !empty($pt_data['custom_fields']) ? $pt_data['custom_fields'] : null;
		$custom_fields_data = !empty($pt_data['custom_fields_data']) ? json_decode($pt_data['custom_fields_data'], true) : null;
		$data = array();

		if($post_type === 'thegem_team_person') {
			$data = array_merge(array(
				array(
					'key' => 'thegem_team_person_name',
					'title' => __('Name', 'thegem'),
					'type' => 'text',
					'value' => '',
				),
				array(
					'key' => 'thegem_team_person_position',
					'title' => __('Position', 'thegem'),
					'type' => 'text',
					'value' => '',
				),
				array(
					'key' => 'thegem_team_person_phone',
					'title' => __('Phone', 'thegem'),
					'type' => 'text',
					'value' => '',
				),
				array(
					'key' => 'thegem_team_person_email',
					'title' => __('Email', 'thegem'),
					'type' => 'text',
					'value' => '',
				),
				array(
					'key' => 'thegem_team_person_link',
					'title' => __('Link', 'thegem'),
					'type' => 'link',
					'value' => '',
				),
				array(
					'key' => 'thegem_team_person_social_link_facebook',
					'title' => __('Facebook Link', 'thegem'),
					'type' => 'link',
					'value' => '',
				),
				array(
					'key' => 'thegem_team_person_social_link_twitter',
					'title' => __('Twitter (X) Link', 'thegem'),
					'type' => 'link',
					'value' => '',
				),
				array(
					'key' => 'thegem_team_person_social_link_linkedin',
					'title' => __('LinkedIn Link', 'thegem'),
					'type' => 'link',
					'value' => '',
				),
				array(
					'key' => 'thegem_team_person_social_link_instagram',
					'title' => __('Instagram Link', 'thegem'),
					'type' => 'link',
					'value' => '',
				),
				array(
					'key' => 'thegem_team_person_social_link_skype',
					'title' => __('Skype Link', 'thegem'),
					'type' => 'link',
					'value' => '',
				),
			), $data);
		}

		if($post_type === 'thegem_testimonial') {
			$data = array_merge(array(
				array(
					'key' => 'thegem_testimonial_name',
					'title' => __('Name', 'thegem'),
					'type' => 'text',
					'value' => '',
				),
				array(
					'key' => 'thegem_testimonial_company',
					'title' => __('Company', 'thegem'),
					'type' => 'text',
					'value' => '',
				),
				array(
					'key' => 'thegem_testimonial_position',
					'title' => __('Position', 'thegem'),
					'type' => 'text',
					'value' => '',
				),
				array(
					'key' => 'thegem_testimonial_link',
					'title' => __('Link', 'thegem'),
					'type' => 'link',
					'value' => '',
				),
			), $data);
		}

		if (empty($custom_fields)) return $data;

		if (!empty($custom_fields_data)) {
			foreach($custom_fields_data as $field) {
				$data[] = array(
					'key' => $field['key'],
					'title' => $field['title'],
					'type' => !empty($field['type']) ? $field['type'] : '',
					'value' => $field['value'],
				);
			}
		}

		return $data;
	}

	public static function dynamic_tags_acf_get_tag_value_field(Base_Tag $tag) {
		$key = $tag->get_settings( 'key' );

		if ( ! empty( $key ) ) {
			list( $field_key, $meta_key ) = explode( ':', $key );

			if ( 'options' === $field_key ) {
				$field = get_field_object( $meta_key, $field_key );
			} else {
				$obj = get_queried_object();
				if((is_object($obj) && !empty($obj->post_type) && $obj->post_type === 'thegem_templates') || (isset($_REQUEST['editor_post_id']) && get_post_type($_REQUEST['editor_post_id']) === 'thegem_templates')) {
					$obj = thegem_templates_init_post();
				}
				$field = get_field_object( $field_key, $obj );
				if(!empty($GLOBALS['thegem_loop_item_post'])) {
					$field = get_field_object( $field_key, $GLOBALS['thegem_loop_item_post'] );
				}
			}

			return [ $field, $meta_key ];
		}

		return ['', ''];
	}

	public static function dynamic_tags_cf_get_tag_value_field(Base_Tag $tag) {
		$key = $tag->get_settings( 'key' );

		if ( ! empty( $key ) ) {
			list( $field_key, $meta_key ) = explode( ':', $key );

			if ( 'options' === $field_key ) {
				$field = get_field_object( $meta_key, $field_key );
			} else {
				$obj = get_queried_object();
				if((is_object($obj) && !empty($obj->post_type) && $obj->post_type === 'thegem_templates') || (isset($_REQUEST['editor_post_id']) && get_post_type($_REQUEST['editor_post_id']) === 'thegem_templates')) {
					$obj = thegem_templates_init_post();
				}
				$field = get_field_object( $field_key, $obj );
			}

			return [ $field, $meta_key ];
		}

		return [];
	}

	public static function dynamic_tags_acf_add_key_control( Base_Tag $tag ) {
		$tag->add_control(
			'key',
			[
				'label' => esc_html__( 'Key', 'thegem' ),
				'type' => Controls_Manager::SELECT,
				'groups' => self::dynamic_tags_acf_get_control_options( $tag->get_supported_fields() ),
			]
		);
	}

	public static function dynamic_tags_cf_add_key_control( Base_Tag $tag, $type = '' ) {
		$tag->add_control(
			'key',
			[
				'label' => esc_html__( 'Key', 'thegem' ),
				'type' => Controls_Manager::SELECT,
				'groups' => self::dynamic_tags_cf_get_control_options( $type ),
			]
		);
	}

	public static function dynamic_tags_project_info_add_key_control( Base_Tag $tag, $type = '' ) {
		$options = array();
		$fields = array();

		$details = thegem_get_option('portfolio_project_details');
		if (!empty($details)) {
			$details_data = json_decode(thegem_get_option('portfolio_project_details_data'), true);
			if (!empty($details_data)) {
				foreach($details_data as $v) {
					$fields[] = array(
						'key' => '_thegem_cf_'.str_replace( '-', '_', sanitize_title($v['title'])),
						'title' => $v['title'],
						'type' => $v['type'],
						'value' => ''
					);
				}
			}
		}

		foreach($fields as $field) {
			if(!empty($type) && $type != $field['type']) continue;
			$options[$field['key']] = $field['title'];
		}

		$tag->add_control(
			'key',
			[
				'label' => esc_html__( 'Key', 'thegem' ),
				'type' => Controls_Manager::SELECT,
				'groups' => $options,
			]
		);
	}

	public static function dynamic_tags_get_taxonomies( $args = [], $output = 'names', $operator = 'and' ) {
		global $wp_taxonomies;

		$field = ( 'names' === $output ) ? 'name' : false;

		// Handle 'object_type' separately.
		if ( isset( $args['object_type'] ) ) {
			$object_type = (array) $args['object_type'];
			unset( $args['object_type'] );
		}

		$taxonomies = wp_filter_object_list( $wp_taxonomies, $args, $operator );

		if ( isset( $object_type ) ) {
			foreach ( $taxonomies as $tax => $tax_data ) {
				if ( ! array_intersect( $object_type, $tax_data->object_type ) ) {
					unset( $taxonomies[ $tax ] );
				}
			}
		}

		if ( $field ) {
			$taxonomies = wp_list_pluck( $taxonomies, $field );
		}

		return $taxonomies;
	}

	public static function dynamic_tags_toolset_get_control_options( $types ) {
		$toolset_groups = wpcf_admin_fields_get_groups();

		$groups = [];

		foreach ( $toolset_groups as $group ) {

			$options = [];

			$fields = wpcf_admin_fields_get_fields_by_group( $group['id'] );

			if ( ! is_array( $fields ) ) {
				continue;
			}

			foreach ( $fields as $field_key => $field ) {
				if ( ! is_array( $field ) || empty( $field['type'] ) ) {
					continue;
				}

				if ( ! self::dynamic_tags_toolset_valid_field_type( $types, $field ) ) {
					continue;
				}

				// Use group ID for unique keys
				$key = $group['slug'] . ':' . $field_key;
				$options[ $key ] = $field['name'];
			}

			if ( empty( $options ) ) {
				continue;
			}

			if ( 1 === count( $options ) ) {
				$options = [ -1 => ' -- ' ] + $options;
			}

			$groups[] = [
				'label' => $group['name'],
				'options' => $options,
			];
		}

		return $groups;
	}

	public static function dynamic_tags_toolset_image_mapping( $field, $single = true ) {
		if ( 'image' !== $field['type'] ) {
			return false;
		}

		$limit = $single ? '0' : '1';
		if ( empty( $field['data'] ) || $limit !== $field['data']['repetitive'] ) {
			return false;
		}

		return true;
	}

	public static function dynamic_tags_toolset_valid_field_type( $types, $field ) {
		// Only file field with single image value
		if ( in_array( 'toolset_image', $types, true ) && self::dynamic_tags_toolset_image_mapping( $field ) ) {
			return true;
		}

		// Only file with multiple images allowed
		if ( in_array( 'toolset_gallery', $types, true ) && self::dynamic_tags_toolset_image_mapping( $field, false ) ) {
			return true;
		}

		// Any other type
		if ( in_array( $field['type'], $types, true ) ) {
			return true;
		}

		return false;
	}

	public static function generate_action_link( $action, array $settings = [] ) {
		return '#' . rawurlencode( sprintf( 'thegem-action:action=%1$s&settings=%2$s', $action, base64_encode( wp_json_encode( $settings ) ) ) );
	}

	public static function get_widget_list() {
		return array(
			'accordion',
			'animated-heading',
			'extended-blog-grid',
			'posts-carousel',
			'featured-posts-slider',
			'blog-grid',
			'bloglist',
			'blogslider',
			'blogtimeline',
			'styledbutton',
			'cta',
			'clients',
			'contact-form7',
			'countdown',
			'counter',
			'custom-fields',
			'custom-menu',
			'extended-filter',
			'extended-products-grid',
			'extended-sorting',
			'gallery-grid',
			'gallery-slider',
			'template',
			'icon',
			'styledimage',
			'infobox',
			'mailchimp',
			'portfolio',
			'portfolio-carousel',
			'portfolio-list',
			'portfolio-slider',
			'pricing-table',
			'extended-products-carousel',
			'products-categories',
			'products-compact-grid',
			'products-grid',
			'products-slider',
			'diagram',
			'progressdonut',
			'projectinfo',
			'quickfinders',
			'quoted-text',
			'searchbar',
			'social-sharing',
			'tabs',
			'team',
			'testimonials',
			'styled-textbox',
			'wp-hook'
		);
	}

	public function wpml_widgets_to_translate_filter( $widgets ) {
		$custom_fields_groups = array(
			//'thegem_cf' => __('Custom Fields (TheGem)', 'thegem'),
			'project_details' => __('Project Details (TheGem)', 'thegem'),
			'manual_input' => __('Manual Input', 'thegem'),
		);

		$post_types = array();
		foreach (get_post_types(array('public' => true), 'object') as $slug => $post_type) {
			if (!in_array($slug, array('thegem_news', 'thegem_footer', 'thegem_title', 'thegem_templates', 'attachment'), true)) {
				$post_types[$slug] = $post_type->label;
			}
		}

		if (class_exists( 'ACF' )) {
			foreach (acf_get_field_groups() as $group) {
				if (!empty($group)) {
					$custom_fields_groups['_acf_' . $group['key']] = $group['title'] . ' (ACF)';
				}
			}
		}

		if (thegem_is_plugin_active('types/wpcf.php')) {
			$wpcf_groups = wpcf_admin_fields_get_groups();
			if(!empty($wpcf_groups)) {
				foreach($wpcf_groups as $group) {
					if(!empty($group)) {
						$custom_fields_groups['_toolset_' . $group['slug']] = $group['name'] . ' (Toolset)';
					}
				}
			}
		}

		$widgets['thegem-custom-fields'] = array('conditions' => array('widgetType' => 'thegem-custom-fields' ), 'fields' => array(), 'fields_in_item' => array());
		foreach($post_types as $post_type_key => $post_type_name) {
			$widgets['thegem-custom-fields']['fields_in_item'][$post_type_key . '_info_content'] = array(
				array(
					'field'       => 'field_prefix',
					'type'        => $post_type_name . ' ' . __('Custom Fields (TheGem)', 'thegem') . ': '. __( 'Prefix', 'thegem' ),
					'editor_type' => 'LINE'
				),
				array(
					'field'       => 'field_suffix',
					'type'        => $post_type_name . ' ' . __('Custom Fields (TheGem)', 'thegem') . ': '. __( 'Suffix', 'thegem' ),
					'editor_type' => 'LINE'
				),
				array(
					'field'       => 'label_text',
					'type'        => $post_type_name . ' ' . __('Custom Fields (TheGem)', 'thegem') . ': '. __( 'Label Text', 'thegem' ),
					'editor_type' => 'LINE'
				),
			);
		}
		foreach($custom_fields_groups as $group_key => $group_name) {
			$widgets['thegem-custom-fields']['fields_in_item'][$group_key . '_info_content'] = array(
				array(
					'field'       => 'field_prefix',
					'type'        => $group_name . ': '. __( 'Prefix', 'thegem' ),
					'editor_type' => 'LINE'
				),
				array(
					'field'       => 'field_suffix',
					'type'        => $group_name . ': '. __( 'Suffix', 'thegem' ),
					'editor_type' => 'LINE'
				),
				array(
					'field'       => 'label_text',
					'type'        => $group_name . ': '. __( 'Label Text', 'thegem' ),
					'editor_type' => 'LINE'
				),
			);
		}

		$details_status = thegem_get_option('portfolio_project_details');

		if (isset($details_status) && !empty($details_status)) {
			$portfolio_meta_data = json_decode(thegem_get_option('portfolio_project_details_data'), true);
		}

		$widgets['thegem-template-portfolio-info'] = array('conditions' => array('widgetType' => 'thegem-template-portfolio-info' ), 'fields' => array(), 'fields_in_item' => array());
		$widgets['thegem-custom-fields']['fields_in_item']['info_content'] = array(
				array(
					'field'       => 'date_label',
					'type'        => __( 'Project Meta/Details', 'thegem' ) . ': '. __( 'Date Label', 'thegem' ),
					'editor_type' => 'LINE'
				),
				array(
					'field'       => 'likes_label',
					'type'        => __( 'Project Meta/Details', 'thegem' ) . ': '. __( 'Likes Label', 'thegem' ),
					'editor_type' => 'LINE'
				),
		);

		if (!empty($portfolio_meta_data)) {
			foreach($portfolio_meta_data as $meta) {
				$meta_name = 'meta_thegem_cf_' . str_replace('-', '_', sanitize_title($meta['title']));
				$widgets['thegem-custom-fields']['fields_in_item']['info_content'][] = array(
					'field'       => $meta_name . '_field_prefix',
					'type'        => __( 'Project Meta/Details', 'thegem' ) . ' - ' . $meta['title'] . ': '. __( 'Prefix', 'thegem' ),
					'editor_type' => 'LINE'
				);
				$widgets['thegem-custom-fields']['fields_in_item']['info_content'][] = array(
					'field'       => $meta_name . '_field_suffix',
					'type'        => __( 'Project Meta/Details', 'thegem' ) . ' - ' . $meta['title'] . ': '. __( 'Suffix', 'thegem' ),
					'editor_type' => 'LINE'
				);
				$widgets['thegem-custom-fields']['fields_in_item']['info_content'][] = array(
					'field'       => $meta_name . '_label_text',
					'type'        => __( 'Project Meta/Details', 'thegem' ) . ' - ' . $meta['title'] . ': '. __( 'Label Text', 'thegem' ),
					'editor_type' => 'LINE'
				);
			}
		}

		return $widgets;
	}

}

class Loop_Dynamic_CSS extends Dynamic_CSS {

	private $post_id_for_data;

	public function __construct( $post_id, $post_id_for_data ) {

		$this->post_id_for_data = $post_id_for_data;

		$post_css_file = Post::create( $post_id_for_data );

		parent::__construct( $post_id, $post_css_file );
	}

	public function get_post_id_for_data() {
		return $this->post_id_for_data;
	}
}

// Instantiate Plugin Class
Plugin::instance();
