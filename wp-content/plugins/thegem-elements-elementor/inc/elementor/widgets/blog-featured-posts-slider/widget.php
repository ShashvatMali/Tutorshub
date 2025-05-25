<?php

namespace TheGem_Elementor\Widgets\FeaturedPostsSlider;

use Elementor\Group_Control_Background;
use Elementor\Group_Control_Css_Filter;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;

use WP_Query;

if (!defined('ABSPATH')) exit;


/**
 * Elementor widget for Featured Posts Slider.
 */
#[\AllowDynamicProperties]
class TheGem_FeaturedPostsSlider extends Widget_Base {

	public function __construct($data = [], $args = null) {

		$template_type = isset($GLOBALS['thegem_template_type']) ? $GLOBALS['thegem_template_type'] : thegem_get_template_type(get_the_ID());
		$this->is_blog_archive_template = $template_type === 'blog-archive';
		$this->is_single_post_template = $template_type === 'single-post' || $template_type === 'cpt' || $template_type === 'page';

		if (isset($data['settings']) && (empty($_REQUEST['action']) || !in_array($_REQUEST['action'], array('thegem_importer_process', 'thegem_templates_new', 'thegem_blocks_import')))) {

			if ($this->is_blog_archive_template || $this->is_single_post_template) {
				if (isset($data['settings']['source_type']) && $data['settings']['source_type'] == 'custom') {
					$data['settings']['query_type'] = 'post';
					unset($data['settings']['source_type']);
				} else if (isset($data['settings']['exclude_blog_posts'])) {
					$data['settings']['exclude_posts'] = 'manual';
					$data['settings']['exclude_posts_manual'] = $data['settings']['exclude_blog_posts'];
					unset($data['settings']['exclude_blog_posts']);
				}
			}

			if ($this->is_blog_archive_template) {
				if (!isset($data['settings']['query_type'])) {
					$data['settings']['query_type'] = 'archive';
				}
				if (!isset($data['settings']['featured_source'])) {
					$data['settings']['featured_source'] = 'all';
				}
			}

			if ($this->is_single_post_template) {
				if (!isset($data['settings']['query_type'])) {
					$data['settings']['query_type'] = 'related';
				}
				if (isset($data['settings']['related_by'])) {
					foreach ($data['settings']['related_by'] as $related) {
						if ($related == 'categories') {
							$data['settings']['taxonomy_related'][] = 'category';
						} else if ($related == 'tags') {
							$data['settings']['taxonomy_related'][] = 'post_tag';
						} else {
							$data['settings']['taxonomy_related'][] = $related;
						}
					}
					unset($data['settings']['related_by']);
				}
				if (!isset($data['settings']['taxonomy_related'])) {
					$data['settings']['taxonomy_related'] = ['category'];
				}
				if (!isset($data['settings']['exclude_posts'])) {
					$data['settings']['exclude_posts'] = 'current';
				}
			}

			if (isset($data['settings']['title_style'])) {
				switch ($data['settings']['title_style']) {
					case 'small':
						$data['settings']['blog_title_preset'] = 'title-h4';
						break;
					case 'normal':
						$data['settings']['blog_title_preset'] = 'title-h2';
						break;
					case 'big':
						$data['settings']['blog_title_preset'] = 'title-h1';
						break;
					case 'large':
						$data['settings']['blog_title_preset'] = 'title-xlarge';
						break;
				}
				unset($data['settings']['title_style']);
			}
		}

		parent::__construct($data, $args);

		if (!defined('THEGEM_ELEMENTOR_WIDGET_FEATUREDPOSTSSLIDER_DIR')) {
			define('THEGEM_ELEMENTOR_WIDGET_FEATUREDPOSTSSLIDER_DIR', rtrim(__DIR__, ' /\\'));
		}

		if (!defined('THEGEM_ELEMENTOR_WIDGET_FEATUREDPOSTSSLIDER_URL')) {
			define('THEGEM_ELEMENTOR_WIDGET_FEATUREDPOSTSSLIDER_URL', rtrim(plugin_dir_url(__FILE__), ' /\\'));
		}

		wp_register_style('thegem-featured-posts-slider-style', THEGEM_ELEMENTOR_WIDGET_FEATUREDPOSTSSLIDER_URL . '/assets/css/thegem-featured-posts-slider.css', array(), NULL);
		wp_register_script('thegem-featured-posts-slider-script', THEGEM_ELEMENTOR_WIDGET_FEATUREDPOSTSSLIDER_URL . '/assets/js/thegem-featured-posts-slider.js', array('jquery', 'jquery-carouFredSel'), NULL);

		$this->states_list = [
			'normal' => __('Normal', 'thegem'),
			'hover' => __('Hover', 'thegem'),
			'active' => __('Active', 'thegem'),
		];
	}

	/**
	 * Retrieve the widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'thegem-featured-posts-slider';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __('Featured Posts Slider', 'thegem');
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
		$post_id = \Elementor\Plugin::$instance->editor->get_post_id();
		if (get_post_type($post_id) === 'thegem_templates' && thegem_get_template_type($post_id) === 'blog-archive') {
			return ['thegem_blog_archive_builder'];
		}

		if (get_post_type($post_id) === 'thegem_templates' && thegem_get_template_type($post_id) === 'single-post') {
			return ['thegem_single_post_builder'];
		}

		return ['thegem_blog'];
	}

	public function get_style_depends() {
		return ['thegem-featured-posts-slider-style'];
	}

	public function get_script_depends() {
		return ['thegem-featured-posts-slider-script'];
	}

	/*Show reload button*/
	public function is_reload_preview_required() {
		return true;
	}

	/**
	 * Create presets options for Select
	 *
	 * @access protected
	 * @return array
	 */
	protected function get_presets_options() {
		$out = array(
			'default' => __('Classic', 'thegem'),
			'new' => __('Alternative', 'thegem'),
		);
		return $out;
	}


	/**
	 * Get default presets options for Select
	 *
	 * @param int $index
	 *
	 * @access protected
	 * @return string
	 */
	protected function set_default_presets_options() {
		return 'default';
	}

	protected function select_blog_featured_categories() {
		global $post;
		$posttemp = $post;
		$query_args = array(
			'post_type' => array('post'),
			'post_status' => 'publish',
			'meta_query' => array(
				array(
					'key' => 'thegem_show_featured_posts_slider',
					'value' => 1
				)
			),
			'posts_per_page' => -1,
		);

		$query = new WP_Query($query_args);
		$categories = array();

		if ($query->have_posts()) {
			while ($query->have_posts()) {
				$query->the_post();
				if (get_the_category()) {
					foreach (get_the_category() as $category) {
						$categories[$category->cat_ID] = $category;
					}
				}
			}
		}
		$post = $posttemp;
		wp_reset_postdata();

		$categories = array_values($categories);

		$items = ['0' => __('All', 'thegem')];

		foreach ($categories as $category) {
			$items[$category->slug] = $category->name;
		}

		return $items;
	}

	protected function select_post_types() {
		$out = ['page' => __('Pages', 'thegem')];
		$post_types = get_post_types(array(
			'public' => true,
			'_builtin' => false
		), 'objects');

		if (empty($post_types) || is_wp_error($post_types)) {
			return $out;
		}

		foreach ($post_types as $post_type) {
			if (!empty($post_type->name) && !in_array($post_type->name, ['elementor_library', 'thegem_title', 'thegem_footer', 'thegem_templates'])) {
				$out[$post_type->name] = $post_type->label;
			}
		}

		return $out;
	}

	protected function select_post_type_taxonomies($post_type = false, $add_authors = false) {
		$out = [];
		if ($post_type && $post_type != 'any')  {
			$taxonomies = get_object_taxonomies( $post_type, 'objects' );
		} else {
			$taxonomies = get_taxonomies(array(
				'public' => true,
			), 'objects');
			$out = ['thegem_portfolios' => __('Portfolio Categories', 'thegem')];
			if ($add_authors) {
				$out['authors'] = __('Authors', 'thegem');
			}
		}


		if (empty($taxonomies) || is_wp_error($taxonomies)) {
			return $out;
		}

		foreach ($taxonomies as $taxonomy) {
			if (!empty($taxonomy->name) && !in_array($taxonomy->name, ['product_shipping_class'])) {
				$out[$taxonomy->name] = $taxonomy->label;
			}
		}

		return $out;
	}


	/**
	 * Register the widget controls.
	 *
	 * @access protected
	 */
	protected function register_controls() {

		$this->start_controls_section(
			'section_layout',
			[
				'label' => __('Layout', 'thegem'),
			]
		);

		$this->add_control(
			'thegem_elementor_preset',
			[
				'label' => __('Skin', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_presets_options(),
				'default' => $this->set_default_presets_options(),
				'frontend_available' => true,
				'render_type' => 'none',
			]
		);

		$this->add_control(
			'fullwidth',
			[
				'label' => __('Stretch to Fullwidth', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'fullheight',
			[
				'label' => __('Fit to Screen', 'thegem'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'return_value' => 'yes',
				'condition' => [
					'fullwidth' => 'yes'
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content',
			[
				'label' => __('Featured Posts', 'thegem'),
			]
		);

		if ($this->is_single_post_template) {
			$query_type_default = 'related';
		} else if ($this->is_blog_archive_template) {
			$query_type_default = 'archive';
		} else {
			$query_type_default = 'post';
		}

		$this->add_control(
			'query_type',
			[
				'label' => __('Show', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => true,
				'options' => array_merge(
					[
						'post' => __('Posts (Blog)', 'thegem'),
					],
					$this->select_post_types(),
					[
						'related' => __('Same Taxonomy Items (Related)', 'thegem'),
						'archive' => __('Posts Archive', 'thegem'),
						'manual' => __('Manual Selection', 'thegem'),
					]
				),
				'default' => $query_type_default,
				'frontend_available' => true,
			]
		);

		foreach ($this->select_post_types() as $post_type_name => $post_type_label) {

			$options = $this->select_post_type_taxonomies($post_type_name);

			$this->add_control(
				'source_post_type_' . $post_type_name,
				[
					'label' => __('Source', 'thegem'),
					'type' => Controls_Manager::SELECT2,
					'label_block' => true,
					'multiple' => true,
					'options' => array_merge(
						[
							'all' => __('All', 'thegem'),
						],
						$options,
						[
							'manual' => __('Manual Selection', 'thegem'),
						]
					),
					'default' => ['all'],
					'frontend_available' => true,
					'condition' => [
						'query_type' => $post_type_name,
					],
				]
			);

			foreach ($options as $tax_name => $tax_label) {

				$this->add_control(
					'source_post_type_' . $post_type_name . '_tax_' . $tax_name,
					[
						'label' => sprintf(__('Select %s Terms', 'thegem'), $tax_label),
						'type' => Controls_Manager::SELECT2,
						'multiple' => true,
						'options' => get_thegem_select_taxonomy_terms($tax_name),
						'frontend_available' => true,
						'label_block' => true,
						'condition' => [
							'query_type' => $post_type_name,
							'source_post_type_' . $post_type_name => $tax_name,
						],
					]
				);
			}

			$this->add_control(
				'source_post_type_' . $post_type_name . '_manual',
				[
					'label' => sprintf(__('Select %s', 'thegem'), $post_type_label),
					'type' => 'gem-query-control',
					'search' => 'thegem_get_posts_by_query',
					'render' => 'thegem_get_posts_title_by_id',
					'post_type' => $post_type_name,
					'label_block' => true,
					'multiple' => true,
					'frontend_available' => true,
					'condition' => [
						'query_type' => $post_type_name,
						'source_post_type_' . $post_type_name => 'manual',
					],
				]
			);

			$this->add_control(
				'source_post_type_' . $post_type_name . '_exclude_type',
				[
					'label' => __('Exclude', 'thegem'),
					'type' => Controls_Manager::SELECT,
					'label_block' => true,
					'multiple' => true,
					'options' => [
						'manual' => __('Manual Selection', 'thegem'),
						'current' => __('Current Post', 'thegem'),
						'term' => __('Term', 'thegem'),
					],
					'default' => 'manual',
					'frontend_available' => true,
					'condition' => [
						'query_type' => $post_type_name,
					],
				]
			);

			$this->add_control(
				'source_post_type_' . $post_type_name . '_exclude',
				[
					'label' => sprintf(__('Exclude %s', 'thegem'), $post_type_label),
					'type' => 'gem-query-control',
					'search' => 'thegem_get_posts_by_query',
					'render' => 'thegem_get_posts_title_by_id',
					'post_type' => $post_type_name,
					'label_block' => true,
					'multiple' => true,
					'frontend_available' => true,
					'description' => __('Add post by title.', 'thegem'),
					'condition' => [
						'query_type' => $post_type_name,
						'source_post_type_' . $post_type_name . '_exclude_type' => 'manual',
					],
				]
			);

			$this->add_control(
				'source_post_type_' . $post_type_name . '_exclude_terms',
				[
					'label' => sprintf(__('Exclude %s Terms', 'thegem'), $post_type_label),
					'type' => 'gem-query-control',
					'search' => 'thegem_get_taxonomy_terms_by_query',
					'render' => 'thegem_get_taxonomy_terms_by_id',
					'post_type' => $post_type_name,
					'label_block' => true,
					'multiple' => true,
					'frontend_available' => true,
					'description' => __('Add term by name.', 'thegem'),
					'condition' => [
						'query_type' => $post_type_name,
						'source_post_type_' . $post_type_name . '_exclude_type' => 'term',
					],
				]
			);

		}


		if ($this->is_blog_archive_template) {
			$featured_source_default = 'all';
		} else {
			$featured_source_default = 'featured';
		}

		$this->add_control(
			'featured_source',
			[
				'label' => __('Source', 'thegem'),
				'default' => $featured_source_default,
				'type' => Controls_Manager::SELECT,
				'label_block' => true,
				'options' => [
					'featured' => __('Featured via post\'s page options', 'thegem'),
					'all' => __('All posts from posts archive', 'thegem'),
				],
				'condition' => [
					'query_type' => 'archive',
				],
			]
		);

		$this->add_control(
			'source',
			[
				'label' => __('Source', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'default' => 'featured',
				'options' => [
					'featured' => __('Featured via post\'s page options', 'thegem'),
					'posts' => __('Select posts', 'thegem'),
					'categories' => __('All posts from category', 'thegem'),
					'tags' => __('All posts from tag', 'thegem'),
					'authors' => __('All posts from author', 'thegem'),
				],
				'condition' => [
					'query_type' => 'post',
				],
			]
		);

		if (count($this->select_blog_featured_categories()) < 2) {

			$this->add_control(
				'no_categories',
				[
					'type' => Controls_Manager::RAW_HTML,
					'raw' => __('No featured posts in no categories were found. Please select some posts as featured using blog post settings in page options', 'thegem'),
					'content_classes' => 'elementor-descriptor',
					'condition' => [
						'query_type' => 'post',
						'source' => 'featured',
					],
				]
			);
		} else {
			$this->add_control(
				'categories',
				[
					'label' => __('Select Blog Categories', 'thegem'),
					'type' => Controls_Manager::SELECT2,
					'multiple' => true,
					'options' => $this->select_blog_featured_categories(),
					'frontend_available' => true,
					'label_block' => true,
					'condition' => [
						'query_type' => 'post',
						'source' => 'featured',
					],
				]
			);
		}

		$this->add_control(
			'select_blog_cat',
			[
				'label' => __('Select Blog Categories', 'thegem'),
				'type' => Controls_Manager::SELECT2,
				'multiple' => true,
				'options' => get_thegem_select_taxonomy_terms('category', true),
				'frontend_available' => true,
				'label_block' => true,
				'condition' => [
					'query_type' => 'post',
					'source' => 'categories',
				],
			]
		);

		$this->add_control(
			'select_blog_tags',
			[
				'label' => __( 'Select Blog Tags', 'thegem' ),
				'type' => Controls_Manager::SELECT2,
				'multiple' => true,
				'options' => get_thegem_select_taxonomy_terms('post_tag', true),
				'frontend_available' => true,
				'label_block' => true,
				'condition' => [
					'query_type' => 'post',
					'source' => 'tags',
				],
			]
		);

		$this->add_control(
			'select_blog_posts',
			[
				'label' => __('Select Blog Posts', 'thegem'),
				'type' => 'gem-query-control',
				'search' => 'thegem_get_posts_by_query',
				'render' => 'thegem_get_posts_title_by_id',
				'post_type' => 'post',
				'label_block' => true,
				'multiple' => true,
				'condition' => [
					'query_type' => 'post',
					'source' => 'posts',
				],
			]
		);

		$this->add_control(
			'select_blog_authors',
			[
				'label' => __( 'Select Blog Authors', 'thegem' ),
				'type' => Controls_Manager::SELECT2,
				'multiple' => true,
				'options' => get_thegem_select_blog_authors(),
				'frontend_available' => true,
				'label_block' => true,
				'condition' => [
					'query_type' => 'post',
					'source' => 'authors',
				],
			]
		);

		$this->add_control(
			'exclude_blog_posts_type',
			[
				'label' => __('Exclude', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => true,
				'multiple' => true,
				'options' => [
					'manual' => __('Manual Selection', 'thegem'),
					'current' => __('Current Post', 'thegem'),
					'term' => __('Term', 'thegem'),
				],
				'default' => 'manual',
				'frontend_available' => true,
				'condition' => [
					'query_type' => 'post',
				],
			]
		);

		$this->add_control(
			'exclude_blog_posts',
			[
				'label' => __('Exclude Blog Posts', 'thegem'),
				'type' => 'gem-query-control',
				'search' => 'thegem_get_posts_by_query',
				'render' => 'thegem_get_posts_title_by_id',
				'post_type' => 'post',
				'label_block' => true,
				'multiple' => true,
				'frontend_available' => true,
				'description' => __('Add post by title.', 'thegem'),
				'condition' => [
					'query_type' => 'post',
					'exclude_blog_posts_type' => 'manual',
				],
			]
		);

		$this->add_control(
			'exclude_blog_posts_terms',
			[
				'label' => __('Exclude Posts Terms', 'thegem'),
				'type' => 'gem-query-control',
				'search' => 'thegem_get_taxonomy_terms_by_query',
				'render' => 'thegem_get_taxonomy_terms_by_id',
				'post_type' => 'post',
				'label_block' => true,
				'multiple' => true,
				'frontend_available' => true,
				'description' => __('Add term by name.', 'thegem'),
				'condition' => [
					'query_type' => 'post',
					'exclude_blog_posts_type' => 'term',
				],
			]
		);

		if ($this->is_single_post_template) {
			$taxonomy_related_default = ['category'];
		} else {
			$taxonomy_related_default = [];
		}

		$this->add_control(
			'taxonomy_related',
			[
				'label' => __('Related by', 'thegem'),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple' => true,
				'options' => $this->select_post_type_taxonomies(false, true),
				'default' => $taxonomy_related_default,
				'frontend_available' => true,
				'condition' => [
					'query_type' => 'related',
				],
			]
		);

		$this->add_control(
			'taxonomy_related_post_type',
			[
				'label' => __('Related by Post Type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => true,
				'options' => array_merge(
					[
						'any' => __('Any', 'thegem'),
						'post' => __('Posts (Blog)', 'thegem'),
					],
					get_thegem_select_post_types()
				),
				'default' => 'any',
				'frontend_available' => true,
				'condition' => [
					'query_type' => 'related',
				],
			]
		);

		$this->add_control(
			'select_posts_manual',
			[
				'label' => __('Select Posts', 'thegem'),
				'type' => 'gem-query-control',
				'search' => 'thegem_get_posts_by_query',
				'render' => 'thegem_get_posts_title_by_id',
				'post_type' => 'any',
				'label_block' => true,
				'multiple' => true,
				'frontend_available' => true,
				'condition' => [
					'query_type' => 'manual',
				],
			]
		);

		if ($this->is_single_post_template) {
			$exclude_posts_default = 'current';
		} else {
			$exclude_posts_default = 'manual';
		}

		$this->add_control(
			'exclude_posts',
			[
				'label' => __('Exclude', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => true,
				'multiple' => true,
				'options' => [
					'manual' => __('Manual Selection', 'thegem'),
					'current' => __('Current Post', 'thegem'),
					'term' => __('Term', 'thegem'),
				],
				'default' => $exclude_posts_default,
				'frontend_available' => true,
				'condition' => [
					'query_type' => ['manual', 'related', 'archive'],
				],
			]
		);

		$this->add_control(
			'exclude_posts_manual',
			[
				'label' => __('Exclude Posts', 'thegem'),
				'type' => 'gem-query-control',
				'search' => 'thegem_get_posts_by_query',
				'render' => 'thegem_get_posts_title_by_id',
				'post_type' => 'any',
				'label_block' => true,
				'multiple' => true,
				'frontend_available' => true,
				'condition' => [
					'exclude_posts' => 'manual',
					'query_type' => ['manual', 'related', 'archive'],
				],
			]
		);

		$this->add_control(
			'exclude_posts_manual_terms',
			[
				'label' => __('Exclude Posts Terms', 'thegem'),
				'type' => 'gem-query-control',
				'search' => 'thegem_get_taxonomy_terms_by_query',
				'render' => 'thegem_get_taxonomy_terms_by_id',
				'post_type' => 'any',
				'label_block' => true,
				'multiple' => true,
				'frontend_available' => true,
				'description' => __('Add term by name.', 'thegem'),
				'condition' => [
					'exclude_posts' => 'term',
					'query_type' => ['manual', 'related', 'archive'],
				],
			]
		);

		$this->add_control(
			'order_by',
			[
				'label' => __('Order By', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => true,
				'multiple' => true,
				'options' => [
					'default' => __('Default', 'thegem'),
					'date' => __('Date', 'thegem'),
					'id' => __('ID', 'thegem'),
					'author' => __('Author', 'thegem'),
					'title' => __('Title', 'thegem'),
					'modified' => __('Last modified date', 'thegem'),
					'comment_count' => __('Number of comments', 'thegem'),
					'rand' => __('Random', 'thegem'),
				],
				'default' => 'default',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'order',
			[
				'label' => __('Sort Order', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => true,
				'multiple' => true,
				'options' => [
					'default' => __('Default', 'thegem'),
					'desc' => __('Descending', 'thegem'),
					'asc' => __('Ascending', 'thegem'),
				],
				'default' => 'default',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'offset',
			[
				'label' => __('Offset', 'thegem'),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 100,
				'step' => 1,
				'description' => __('Number of items to displace or pass over', 'thegem'),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_caption',
			[
				'label' => __('Caption', 'thegem'),
			]
		);

		$slider_fields = [
			'featured' => __('Featured Image', 'thegem'),
			'title' => __('Title', 'thegem'),
			'excerpt' => __('Description', 'thegem'),
			'date' => __('Date', 'thegem'),
			'categories' => __('Additional Meta', 'thegem'), // Old Categories Checkbox
			'author' => __('Author', 'thegem'),
			'author_avatar' => __('Author’s Avatar', 'thegem'),
		];

		foreach ($slider_fields as $ekey => $elem) {

			if ($ekey == 'featured') {

				$this->add_control(
					'title_description_header',
					[
						'label' => __('Title & Description', 'thegem'),
						'type' => Controls_Manager::HEADING,
						'separator' => 'before',
					]
				);

			} else if ($ekey == 'date') {

				$this->add_control(
					'meta_header',
					[
						'label' => __('Meta', 'thegem'),
						'type' => Controls_Manager::HEADING,
						'separator' => 'before',
					]
				);
			}

			$this->add_control(
				'slider_show_' . $ekey, [
					'label' => $elem,
					'default' => 'yes',
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('Show', 'thegem'),
					'label_off' => __('Hide', 'thegem'),
					'frontend_available' => true,
				]
			);

			if ($ekey == 'categories') {

				$this->add_control(
					'additional_meta_type',
					[
						'label' => __('Select Meta Type', 'thegem'),
						'type' => Controls_Manager::SELECT,
						'default' => 'taxonomies',
						'options' => array_merge(
							[
								'taxonomies' => __('Taxonomies', 'thegem'),
								'custom_fields' => __('Custom Fields (TheGem)', 'thegem'),
								'details' => __('Portfolio Details', 'thegem'),
							],
							thegem_get_acf_plugin_groups()
						),
						'frontend_available' => true,
						'condition' => [
							'slider_show_categories' => 'yes',
						]
					]
				);

				$this->add_control(
					'additional_meta_taxonomies',
					[
						'label' => __( 'Select Taxonomy', 'thegem' ),
						'type' => Controls_Manager::SELECT,
						'options' => get_thegem_select_post_type_taxonomies(),
						'default' => 'category',
						'frontend_available' => true,
						'condition' => [
							'slider_show_categories' => 'yes',
							'additional_meta_type' => 'taxonomies',
						],
					]
				);

				$options = thegem_select_portfolio_details();
				$default = !empty($options) ? array_keys($options)[0] : '';

				$this->add_control(
					'additional_meta_details',
					[
						'label' => __( 'Select Field', 'thegem' ),
						'type' => Controls_Manager::SELECT,
						'options' => $options,
						'default' => $default,
						'frontend_available' => true,
						'condition' => [
							'slider_show_categories' => 'yes',
							'additional_meta_type' => 'details',
							'query_type' => 'thegem_pf_item',
						],
						'description' => __('Go to the <a href="'.get_site_url().'/wp-admin/admin.php?page=thegem-theme-options#/single-pages/portfolio" target="_blank">Theme Options -> Single Pages -> Portfolio Page</a> to manage your custom fields.', 'thegem')
					]
				);

				$options = thegem_select_theme_options_custom_fields_all();
				$default = !empty($options) ? array_keys($options)[0] : '';

				$this->add_control(
					'additional_meta_custom_fields',
					[
						'label' => __( 'Select Field', 'thegem' ),
						'type' => Controls_Manager::SELECT,
						'options' => $options,
						'default' => $default,
						'frontend_available' => true,
						'condition' => [
							'slider_show_categories' => 'yes',
							'additional_meta_type' => 'custom_fields',
						],
						'description' => __('Go to the <a href="'.get_site_url().'/wp-admin/admin.php?page=thegem-theme-options#/single-pages" target="_blank">Theme Options -> Single Pages</a> to manage your custom fields.', 'thegem')
					]
				);

				if (class_exists( 'ACF' ) && !empty(thegem_get_acf_plugin_groups())) {
					foreach (thegem_get_acf_plugin_groups() as $gr => $label) {

						$options = thegem_get_acf_plugin_fields_by_group($gr);
						$default = !empty($options) ? array_keys($options)[0] : '';

						$this->add_control(
							'additional_meta_custom_fields_acf_' . $gr,
							[
								'label' => __('Select Custom Field', 'thegem'),
								'type' => Controls_Manager::SELECT,
								'options' => $options,
								'default' => $default,
								'frontend_available' => true,
								'condition' => [
									'slider_show_categories' => 'yes',
									'additional_meta_type' => $gr,
								],
								'description' => __('Go to the <a href="'.get_site_url().'/wp-admin/edit.php?post_type=acf-field-group" target="_blank">ACF -> Field Groups</a> to manage your custom fields.', 'thegem'),
							]
						);
					}
				}

				$this->add_control(
					'additional_meta_click_behavior',
					[
						'type' => Controls_Manager::HIDDEN,
						'default' => 'archive_link',
					]
				);

				$this->add_control(
					'additional_meta_click_behavior_meta',
					[
						'type' => Controls_Manager::HIDDEN,
						'default' => 'disabled',
					]
				);

			}
		}

		$this->add_control(
			'details_header',
			[
				'label' => __('Custom Fields', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'blog_show_details', [
				'label' => __( 'Show Custom Fields', 'thegem' ),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Yes', 'thegem'),
				'label_off' => __('No', 'thegem'),
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'details_style',
			[
				'label' => __('Fields Style', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'text',
				'options' => [
					'text' => __('Text', 'thegem'),
					'labels' => __('Labels', 'thegem'),
				],
				'frontend_available' => true,
				'condition' => [
					'blog_show_details' => 'yes',
				],
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'attribute_title',
			[
				'type' => Controls_Manager::TEXT,
				'label' => __('Title', 'thegem'),
				'default' => __('Attribute', 'thegem'),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'attribute_type',
			[
				'label' => __('Type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'custom_fields',
				'options' => array_merge(
					[
						'custom_fields' => __('Custom Fields (TheGem)', 'thegem'),
						'taxonomies' => __('Taxonomies', 'thegem'),
						'details' => __('Portfolio Details', 'thegem'),
					],
					thegem_get_acf_plugin_groups()
				),
			]
		);

		$options = thegem_select_theme_options_custom_fields_all();
		$default = !empty($options) ? array_keys($options)[0] : '';

		$repeater->add_control(
			'attribute_custom_fields',
			[
				'label' => __( 'Select Field', 'thegem' ),
				'type' => Controls_Manager::SELECT,
				'options' => $options,
				'default' => $default,
				'condition' => [
					'attribute_type' => 'custom_fields',
				],
				'description' => __('Go to the <a href="'.get_site_url().'/wp-admin/admin.php?page=thegem-theme-options#/single-pages" target="_blank">Theme Options -> Single Pages</a> to manage your custom fields.', 'thegem')
			]
		);

		$repeater->add_control(
			'attribute_taxonomies',
			[
				'label' => __( 'Select Taxonomy', 'thegem' ),
				'type' => Controls_Manager::SELECT,
				'options' => get_thegem_select_post_type_taxonomies(),
				'default' => 'category',
				'condition' => [
					'attribute_type' => 'taxonomies',
				],
			]
		);

		$options = thegem_select_portfolio_details();
		$default = !empty($options) ? array_keys($options)[0] : '';

		$repeater->add_control(
			'attribute_details',
			[
				'label' => __( 'Select Field', 'thegem' ),
				'type' => Controls_Manager::SELECT,
				'options' => $options,
				'default' => $default,
				'condition' => [
					'attribute_type' => 'details',
				],
				'description' => __('Go to the <a href="'.get_site_url().'/wp-admin/admin.php?page=thegem-theme-options#/single-pages/portfolio" target="_blank">Theme Options -> Single Pages -> Portfolio Page</a> to manage your custom fields.', 'thegem')
			]
		);

		if (class_exists( 'ACF' ) && !empty(thegem_get_acf_plugin_groups())) {
			foreach (thegem_get_acf_plugin_groups() as $gr => $label) {

				$options = thegem_get_acf_plugin_fields_by_group($gr);
				$default = !empty($options) ? array_keys($options)[0] : '';

				$repeater->add_control(
					'attribute_custom_fields_acf_' . $gr,
					[
						'label' => __('Select Custom Field', 'thegem'),
						'type' => Controls_Manager::SELECT,
						'options' => $options,
						'default' => $default,
						'condition' => [
							'attribute_type' => $gr,
						],
						'description' => __('Go to the <a href="'.get_site_url().'/wp-admin/edit.php?post_type=acf-field-group" target="_blank">ACF -> Field Groups</a> to manage your custom fields.', 'thegem'),
					]
				);
			}
		}

		$repeater->add_control(
			'attribute_meta_type',
			[
				'label' => __('Field Type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'text',
				'options' => [
					'text' => __('Text', 'thegem'),
					'number' => __('Number', 'thegem'),
				],
			]
		);

		$repeater->add_control(
			'attribute_price_format',
			[
				'label' => __('Price Format', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'disabled',
				'options' => [
					'disabled' => __('Disabled', 'thegem'),
					'wp_locale' => __('WP Locale', 'thegem'),
					'custom_locale' => __('Custom Locale', 'thegem'),
				],
				'condition' => [
					'attribute_meta_type' => 'number',
				],
			]
		);

		$repeater->add_control(
			'attribute_price_format_locale',
			[
				'label' => __('Custom Locale', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'condition' => [
					'attribute_meta_type' => 'number',
					'attribute_price_format' => 'custom_locale',
				],
				'description' => __('Enter locale, e.g. en_GB. See <a href="https://wpcentral.io/internationalization/" target="_blank">complete locale list</a>.', 'thegem'),
			]
		);

		$repeater->add_control(
			'attribute_price_format_prefix',
			[
				'label' => __('Prefix', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'condition' => [
					'attribute_meta_type' => 'number',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'attribute_price_format_suffix',
			[
				'label' => __('Suffix', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'condition' => [
					'attribute_meta_type' => 'number',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'attribute_icon',
			[
				'label' => __('Icon', 'thegem'),
				'type' => Controls_Manager::ICONS,
			]
		);

		$this->add_control(
			'repeater_details',
			[
				'type' => Controls_Manager::REPEATER,
				'label' => __('Fields', 'thegem'),
				'fields' => $repeater->get_controls(),
				'title_field' => '{{{ attribute_title }}}',
				'default' => [
					[
						'attribute_title' => __('Field 1', 'thegem'),
					]
				],
				'frontend_available' => true,
				'condition' => [
					'blog_show_details' => 'yes',
				],
			]
		);

		$this->add_control(
			'button_header',
			[
				'label' => __('Button', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'slider_show_button', [
				'label' => __( 'Show "Read More" Button', 'thegem' ),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'thegem'),
				'label_off' => __('Hide', 'thegem'),
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'more_button_text',
			[
				'label' => __('Button Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => __('Read More', 'thegem'),
				'condition' => [
					'slider_show_button' => 'yes',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'more_button_icon',
			[
				'label' => __('Button Icon', 'thegem'),
				'type' => Controls_Manager::ICONS,
				'condition' => [
					'slider_show_button' => 'yes',
				],
			]
		);

		$this->add_control(
			'more_button_text_weight',
			[
				'label' => __('Text Weight', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'normal' => __('Normal', 'thegem'),
					'thin' => __('Thin', 'thegem'),
				],
				'default' => 'normal',
				'condition' => [
					'slider_show_button' => 'yes',
				],
			]
		);

		$this->add_control(
			'more_button_link',
			[
				'label' => __( 'Button Link', 'thegem' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'default' => __('Link to Single Post', 'thegem'),
					'custom' => __('Custom Link', 'thegem'),
				],
				'default' => 'default',
				'frontend_available' => true,
				'condition' => [
					'slider_show_button' => 'yes',
				],
			]
		);

		$this->add_control(
			'more_button_custom_link',
			[
				'label' => __( 'Custom Link', 'thegem' ),
				'type' => Controls_Manager::URL,
				'placeholder' => __( 'https://your-link.com', 'thegem' ),
				'options' => false,
				'frontend_available' => true,
				'condition' => [
					'slider_show_button' => 'yes',
					'more_button_link' => 'custom',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'more_button_id',
			[
				'label' => __( 'Button ID', 'thegem' ),
				'type' => Controls_Manager::TEXT,
				'frontend_available' => true,
				'condition' => [
					'slider_show_button' => 'yes',
					'more_button_link' => 'custom',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_navigation',
			[
				'label' => __('Navigation', 'thegem'),
			]
		);

		$this->add_responsive_control(
			'max_posts',
			[
				'label' => __('Max. number of posts in slider', 'thegem'),
				'type' => Controls_Manager::NUMBER,
				'min' => -1,
				'max' => 100,
				'step' => 1,
				'default' => 3,
				'description' => __('Use -1 to show all', 'thegem'),
			]
		);

		$this->add_control(
			'slider_show_navigation',
			[
				'label' => __('Navigation', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'paginator_type',
			[
				'label' => __('Navigation Type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'arrows' => __('Arrows', 'thegem'),
					'bullets' => __('Navigation Dots', 'thegem'),
				],
				'default' => 'arrows',
				'condition' => [
					'slider_show_navigation' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_arrows',
			[
				'label' => __('Arrows', 'thegem'),
				'condition' => [
					'slider_show_navigation' => 'yes',
					'paginator_type' => 'arrows',
				],
			]
		);

		$this->add_control(
			'paginator_icon',
			[
				'label' => __('Arrows Icons Preset', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'1' => __('Arrows Style 1', 'thegem'),
					'2' => __('Arrows Style 2', 'thegem'),
					'3' => __('Arrows Style 3', 'thegem'),
					'4' => __('Arrows Style 4', 'thegem'),
					'5' => __('Arrows Style 5', 'thegem'),
				],
				'default' => '1',
				'condition' => [
					'custom_arrows!' => 'yes',
				],
			]
		);

		$this->add_control(
			'custom_arrows',
			[
				'label' => __('Custom Arrows Icons', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->add_control(
			'left_arrow_icon',
			[
				'label' => __('Left Arrow Icon', 'thegem'),
				'type' => Controls_Manager::ICONS,
				'condition' => [
					'custom_arrows' => 'yes',
				],
			]
		);

		$this->add_control(
			'right_arrow_icon',
			[
				'label' => __('Right Arrow Icon', 'thegem'),
				'type' => Controls_Manager::ICONS,
				'condition' => [
					'custom_arrows' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_bullets',
			[
				'label' => __('Navigation Dots', 'thegem'),
				'condition' => [
					'slider_show_navigation' => 'yes',
					'paginator_type' => 'bullets',
				],
			]
		);

		$this->add_control(
			'paginator_dots_size',
			[
				'label' => __('Dots Size Preset', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'regular' => __('Regular', 'thegem'),
					'large' => __('Large', 'thegem'),
				],
				'default' => 'regular',
			]
		);

		$this->add_control(
			'paginator_dots_style',
			[
				'label' => __('Dots Style Preset', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'light' => __('Light', 'thegem'),
					'dark' => __('Dark', 'thegem'),
				],
				'default' => 'light',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_additional',
			[
				'label' => __('Additional Options', 'thegem'),
			]
		);

		$this->add_control(
			'sliding_effect',
			[
				'label' => __('Sliding effect', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'slide' => __('Slide', 'thegem'),
					'fade' => __('Fade', 'thegem'),
				],
				'default' => 'slide',
			]
		);

		$this->add_control(
			'auto_scroll',
			[
				'label' => __('Autoscroll', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
			]
		);

		$this->add_responsive_control(
			'auto_scroll_speed',
			[
				'label' => __('Autoscroll Speed', 'thegem'),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'step' => 100,
				'description' => __('Speed in Milliseconds, example - 5000', 'thegem'),
				'condition' => [
					'auto_scroll' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->add_styles_controls($this);

	}

	/**
	 * Controls call
	 * @access public
	 */
	public function add_styles_controls($control) {

		$this->control = $control;

		/* Image Container Style */
		$this->image_container_style($control);

		/* Caption Style */
		$this->caption_style($control);

		/* Custom Fields */
		$this->project_details_style($control);

		/* Read More Button Style */
		$this->more_button_style($control);

		/* Arrows Style */
		$this->arrows_style($control);

		/* Navigation Dots Style */
		$this->bullets_style($control);
	}

	/**
	 * Image Container Style
	 * @access protected
	 */
	protected function image_container_style($control) {

		$control->start_controls_section(
			'image_container_style_section',
			[
				'label' => __('Image Container Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->add_responsive_control(
			'container_height',
			[
				'label' => __( 'Height', 'thegem' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'render_type' => 'template',
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-featured-posts-slider .slide-item, {{WRAPPER}} .preloader' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'content_alignment',
			[
				'label' => __('Content Alignment', 'thegem'),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'left' => [
						'title' => __('Left', 'thegem'),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __('Center', 'thegem'),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' => __('Right', 'thegem'),
						'icon' => 'eicon-h-align-right',
					],
				],
				'toggle' => false,
				'default' => 'left',
			]
		);

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'image_container_background',
				'label' => __('Overlay Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'fields_options' => [
					'background' => [
						'label' => _x('Background ', 'Background Control', 'thegem'),
					],
					'color' => [
						'selectors' => [
							'{{WRAPPER}} .slide-item' => 'background: {{VALUE}} !important;',
						],
					],
					'gradient_angle' => [
						'selectors' => [
							'{{WRAPPER}} .slide-item' => 'background-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}}) !important;',
						],
					],
					'gradient_position' => [
						'selectors' => [
							'{{WRAPPER}} .slide-item' => 'background-color: transparent; background-image: radial-gradient(at {{SIZE}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}}) !important;',
						],
					],
				],
				'condition' => [
					'slider_show_featured' => 'yes',
				],
			]
		);
		$control->remove_control('image_container_background_image');

		$control->add_responsive_control(
			'image_container_radius',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .slide-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .gem-featured-posts-slide-overlay' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'image_container_border_type',
				'label' => __('Border Type', 'thegem'),
				'selector' => '{{WRAPPER}} .slide-item',
			]
		);

		$control->add_responsive_control(
			'image_container_padding',
			[
				'label' => __('Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .slide-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
			]
		);

		/*	$control->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'image_container_shadow',
					'label' => __('Shadow', 'thegem'),
					'selector' => '{{WRAPPER}} .slide-item',
				]
			);*/

		$control->add_control(
			'image_heading',
			[
				'label' => __('Image Overlay', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'slider_show_featured' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'image_opacity',
			[
				'label' => __('Opacity', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['%'],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 100,
					'unit' => '%',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-featured-posts-slide-overlay' => 'opacity: calc({{SIZE}}/100);',
				],
				'condition' => [
					'slider_show_featured' => 'yes',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'image_overlay',
				'label' => __('Overlay Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'fields_options' => [
					'background' => [
						'label' => _x('Background ', 'Background Control', 'thegem'),
					],
					'color' => [
						'selectors' => [
							'{{WRAPPER}} .gem-featured-posts-slide-overlay' => 'background: {{VALUE}} !important;',
						],
					],
					'gradient_angle' => [
						'selectors' => [
							'{{WRAPPER}} .gem-featured-posts-slide-overlay' => 'background-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}}) !important;',
						],
					],
					'gradient_position' => [
						'selectors' => [
							'{{WRAPPER}} .gem-featured-posts-slide-overlay' => 'background-color: transparent; background-image: radial-gradient(at {{SIZE}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}}) !important;',
						],
					],
				],
			]
		);
		$control->remove_control('image_overlay_image');

		$control->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'image_css',
				'label' => __('CSS Filters', 'thegem'),
				'selector' => '{{WRAPPER}} .gem-featured-posts-slide-overlay',
				'condition' => [
					'slider_show_featured' => 'yes',
				],
			]
		);

		$control->add_control(
			'image_blend_mode',
			[
				'label' => __('Blend Mode', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __('Normal', 'thegem'),
					'multiply' => 'Multiply',
					'screen' => 'Screen',
					'overlay' => 'Overlay',
					'darken' => 'Darken',
					'lighten' => 'Lighten',
					'color-dodge' => 'Color Dodge',
					'color-burn' => 'Color Burn',
					'hue' => 'Hue',
					'saturation' => 'Saturation',
					'color' => 'Color',
					'exclusion' => 'Exclusion',
					'luminosity' => 'Luminosity',
				],
				'selectors' => [
					'{{WRAPPER}} .gem-featured-posts-slide-overlay' => 'mix-blend-mode: {{VALUE}}',
				],
				'condition' => [
					'slider_show_featured' => 'yes',
				],
			]
		);

		$control->add_control(
			'title_heading',
			[
				'label' => __('Title', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'slider_show_title' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'title_top_spacing',
			[
				'label' => __('Top Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-featured-post-title' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'slider_show_title' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'title_bottom_spacing',
			[
				'label' => __('Bottom Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-featured-post-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'slider_show_title' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'title_max_width',
			[
				'label' => __('Title Max Width', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1920,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					]
				],
				'selectors' => [
					'{{WRAPPER}} .gem-featured-post-title div' => 'max-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'slider_show_title' => 'yes',
				],
			]
		);

		$control->add_control(
			'description_heading',
			[
				'label' => __('Description', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'slider_show_excerpt' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'description_bottom_spacing',
			[
				'label' => __('Bottom Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-featured-post-excerpt' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'slider_show_excerpt' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'description_max_width',
			[
				'label' => __('Description Max Width', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1920,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					]
				],
				'selectors' => [
					'{{WRAPPER}} .gem-featured-post-excerpt div' => 'max-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'slider_show_excerpt' => 'yes',
				],
			]
		);

		$control->add_control(
			'categories_heading',
			[
				'label' => __('Categories', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'slider_show_categories' => 'yes',
					'thegem_elementor_preset' => 'default',
				],
			]
		);

		$control->add_responsive_control(
			'categories_bottom_spacing',
			[
				'label' => __('Bottom Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .with-label .gem-featured-post-meta-categories' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'slider_show_categories' => 'yes',
					'thegem_elementor_preset' => 'default',
				],
			]
		);

		$control->add_control(
			'date_heading',
			[
				'label' => __('Date', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'slider_show_date' => 'yes',
					'thegem_elementor_preset' => 'new',
				],
			]
		);

		$control->add_responsive_control(
			'date_bottom_spacing',
			[
				'label' => __('Bottom Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-featured-post-date' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'slider_show_date' => 'yes',
					'thegem_elementor_preset' => 'new',
				],
			]
		);

		$control->add_control(
			'author_heading',
			[
				'label' => __('Author', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'slider_show_author' => 'yes',
				],
			]
		);

		$control->add_responsive_control(
			'author_bottom_spacing',
			[
				'label' => __('Bottom Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-featured-post-meta-author' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'slider_show_author' => 'yes',
				],
			]
		);

		$control->end_controls_section();
	}

	/* Repeatable Text Style Controls for Caption Style */

	protected function caption_text_controls($control, $ekey, $hover = false) {
		if ($hover) {
			$hover_name = '_hover';
			$hover_selector = ':hover';
		} else {
			$hover_name = '';
			$hover_selector = '';
		}

		if ($ekey == 'categories') {
			$selector = '.slide-item .gem-featured-post-meta-categories span';
		} else if ($ekey == 'author') {
			$selector = '.gem-featured-post-meta-author .author .author-name';
		} else {
			$selector = '{{WRAPPER}} .gem-featured-post-' . $ekey;
		}

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'caption_typography_' . $ekey . $hover_name,
				'label' => __('Typography', 'thegem'),
				'selector' => $selector . $hover_selector,
				'condition' => [
					'slider_show_' . $ekey => 'yes',
				],
			]
		);

		$control->add_control(
			'caption_color_' . $ekey . $hover_name,
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					$selector . $hover_selector => 'color: {{VALUE}}',
				],
				'condition' => [
					'slider_show_' . $ekey => 'yes',
				],
			]
		);
	}

	/**
	 * Caption Style
	 * @access protected
	 */
	protected function caption_style($control) {

		$control->start_controls_section(
			'caption_style_section',
			[
				'label' => __('Caption Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$caption_fields = [
			'title' => __('Title', 'thegem'),
			'excerpt' => __('Description', 'thegem'),
			'date' => __('Date', 'thegem'),
			'categories' => __('Categories', 'thegem'),
			'author' => __('Author', 'thegem'),
		];

		foreach ($caption_fields as $ekey => $elem) {

			$control->add_control(
				'caption_heading_' . $ekey,
				[
					'label' => $elem,
					'type' => Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => [
						'slider_show_' . $ekey => 'yes',
					],
				]
			);

			if ($ekey == 'excerpt') {

				$control->add_control(
					'blog_description_preset',
					[
						'label' => 'Description Size Preset',
						'type' => Controls_Manager::SELECT,
						'options' => [
							'default' => __('Default', 'thegem'),
							'title-h1' => __('Title H1', 'thegem'),
							'title-h2' => __('Title H2', 'thegem'),
							'title-h3' => __('Title H3', 'thegem'),
							'title-h4' => __('Title H4', 'thegem'),
							'title-h5' => __('Title H5', 'thegem'),
							'title-h6' => __('Title H6', 'thegem'),
							'title-xlarge' => __('Title xLarge', 'thegem'),
							'styled-subtitle' => __('Styled Subtitle', 'thegem'),
							'main-menu-item' => __('Main Menu', 'thegem'),
							'text-body' => __('Body', 'thegem'),
							'text-body-tiny' => __('Tiny Body', 'thegem'),
						],
						'default' => 'default',
						'frontend_available' => true,
						'condition' => [
							'slider_show_excerpt' => 'yes',
						],
					]
				);

			}

			if ($ekey == 'title') {

				$control->add_control(
					'blog_title_preset',
					[
						'label' => 'Title Size Preset',
						'type' => Controls_Manager::SELECT,
						'options' => [
							'title-h1' => __('Title H1', 'thegem'),
							'title-h2' => __('Title H2', 'thegem'),
							'title-h3' => __('Title H3', 'thegem'),
							'title-h4' => __('Title H4', 'thegem'),
							'title-h5' => __('Title H5', 'thegem'),
							'title-h6' => __('Title H6', 'thegem'),
							'title-xlarge' => __('Title xLarge', 'thegem'),
							'styled-subtitle' => __('Styled Subtitle', 'thegem'),
							'main-menu-item' => __('Main Menu', 'thegem'),
							'text-body' => __('Body', 'thegem'),
							'text-body-tiny' => __('Tiny Body', 'thegem'),
						],
						'default' => 'title-h2',
						'frontend_available' => true,
						'condition' => [
							'slider_show_title' => 'yes',
						],
					]
				);

				$control->add_control(
					'title_tag',
					[
						'label' => __('HTML Tag', 'thegem'),
						'type' => Controls_Manager::SELECT,
						'options' => [
							'h1' => 'H1',
							'h2' => 'H2',
							'h3' => 'H3',
							'h4' => 'H4',
							'h5' => 'H5',
							'h6' => 'H6',
							'div' => 'div',
							'span' => 'span',
							'p' => 'p',
						],
						'default' => 'div',
						'frontend_available' => true,
						'condition' => [
							'slider_show_title' => 'yes',
						],
					]
				);

				$control->add_control(
					'title_transform',
					[
						'label' => 'Title Font Transform',
						'type' => Controls_Manager::SELECT,
						'options' => [
							'' => __('Default', 'thegem'),
							'none' => __('None', 'thegem'),
							'lowercase' => __('Lowercase', 'thegem'),
							'uppercase' => __('Uppercase', 'thegem'),
							'capitalize' => __('Capitalize', 'thegem'),
						],
						'default' => '',
						'condition' => [
							'slider_show_title' => 'yes',
						],
						'selectors' => [
							'{{WRAPPER}} .gem-featured-post-title div' => 'text-transform: {{VALUE}};',
						],
					]
				);

				$control->add_control(
					'title_weight',
					[
						'label' => __('Title Font weight', 'thegem'),
						'type' => Controls_Manager::SELECT,
						'options' => [
							'' => __('Default', 'thegem'),
							'light' => __('Thin', 'thegem'),
						],
						'default' => '',
						'frontend_available' => true,
						'condition' => [
							'slider_show_title' => 'yes',
						],
					]
				);
			}

			if ($ekey == 'categories') {
				$control->start_controls_tabs('caption_categories_tabs');
				$control->start_controls_tab('caption_categories_tab_normal', ['label' => __('Normal', 'thegem')]);

				$this->caption_text_controls($control, $ekey);

				$control->add_control(
					'caption_background_categories',
					[
						'label' => __('Background Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'default' => '',
						'selectors' => [
							'{{WRAPPER}} .style-new .gem-featured-post-meta-categories span' => 'background: {{VALUE}}',
						],
						'condition' => [
							'slider_show_' . $ekey => 'yes',
							'thegem_elementor_preset' => 'new',
						],
					]
				);

				$control->end_controls_tab();

				$control->start_controls_tab('caption_categories_tab_hover', ['label' => __('Hover', 'thegem')]);

				$this->caption_text_controls($control, $ekey, true);

				$control->add_control(
					'caption_background_categories_hover',
					[
						'label' => __('Background Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'default' => '',
						'selectors' => [
							'{{WRAPPER}} .style-new .gem-featured-post-meta-categories span:hover' => 'background: {{VALUE}}',
						],
						'condition' => [
							'slider_show_' . $ekey => 'yes',
							'thegem_elementor_preset' => 'new',
						],
					]
				);

				$control->end_controls_tab();
				$control->end_controls_tabs();

			} else {
				$this->caption_text_controls($control, $ekey);
			}

			if ($ekey == 'author') {
				$this->add_control(
					'by_text',
					[
						'label' => __('“By” text', 'thegem'),
						'type' => Controls_Manager::TEXT,
						'input_type' => 'text',
						'default' => __('By', 'thegem'),
						'condition' => [
							'slider_show_' . $ekey => 'yes',
						],
						'dynamic' => [
							'active' => true,
						],
					]
				);
			}


		}

		$control->end_controls_section();
	}

	/**
	 * Read More Button Style
	 * @access protected
	 */
	protected function more_button_style($control) {

		$control->start_controls_section(
			'more_button_style_section',
			[
				'label' => __('“Read More” Button', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'slider_show_button' => 'yes',
				],
			]
		);

		$control->add_control(
			'more_button_type',
			[
				'label' => __('Button Type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'default' => 'flat',
				'options' => [
					'flat' => __('Flat', 'thegem'),
					'outline' => __('Outline', 'thegem'),
				],
			]
		);

		$control->add_responsive_control(
			'more_button_size',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'default' => 'small',
				'options' => [
					'small' => __('Small', 'thegem'),
					'medium' => __('Medium', 'thegem'),
					'large' => __('Large', 'thegem'),
				],
			]
		);

		$control->add_responsive_control(
			'more_button_border_radius',
			[
				'label' => __('Border Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-featured-post-btn-box a.gem-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'more_button_border_type',
				'label' => __('Border Type', 'thegem'),
				'selector' => '{{WRAPPER}} .gem-featured-post-btn-box a.gem-button',
			]
		);

		$control->remove_control('more_button_border_type_color');

		$control->add_responsive_control(
			'more_button_text_padding',
			[
				'label' => __('Text Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-featured-post-btn-box a.gem-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->start_controls_tabs('more_button_tabs');
		if (!empty($control->states_list)) {
			foreach ((array)$control->states_list as $stkey => $stelem) {

				if ($stkey == 'active') {
					continue;
				}

				$state = '';
				if ($stkey == 'hover') {
					$state = ':hover';
				}

				$control->start_controls_tab('more_button_tab_' . $stkey, ['label' => $stelem]);

				$control->add_responsive_control(
					'more_button_text_color_' . $stkey,
					[
						'label' => __('Text Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .gem-featured-post-btn-box a.gem-button' . $state . ' span' => 'color: {{VALUE}};',
							'{{WRAPPER}} .gem-featured-post-btn-box a.gem-button' . $state . ' i:before' => 'color: {{VALUE}};',
							'{{WRAPPER}} .gem-featured-post-btn-box a.gem-button' . $state . ' svg' => 'fill: {{VALUE}};',
						],
					]
				);

				$control->add_group_control(
					Group_Control_Typography::get_type(),
					[
						'label' => __('Typography', 'thegem'),
						'name' => 'more_button_typography_' . $stkey,
						'selector' => '{{WRAPPER}} .gem-featured-post-btn-box a.gem-button' . $state . ' span',
					]
				);

				$control->add_responsive_control(
					'more_button_bg_color_' . $stkey,
					[
						'label' => __('Background Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .gem-featured-post-btn-box a.gem-button' . $state => 'background-color: {{VALUE}};',
						],
					]
				);

				$control->add_responsive_control(
					'more_button_border_color_' . $stkey,
					[
						'label' => __('Border Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .gem-featured-post-btn-box a.gem-button' . $state => 'border-color: {{VALUE}};',
						],
					]
				);

				$control->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name' => 'more_button_shadow_' . $stkey,
						'label' => __('Shadow', 'thegem'),
						'selector' => '{{WRAPPER}} .gem-featured-post-btn-box a.gem-button' . $state,
					]
				);

				$control->end_controls_tab();

			}
		}

		$control->end_controls_tabs();

		$control->add_responsive_control(
			'more_button_icon_align',
			[
				'label' => __('Icon Alignment', 'thegem'),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'default' => 'left',
				'options' => [
					'left' => [
						'title' => __('Left', 'thegem'),
						'icon' => 'eicon-h-align-left',
					],
					'right' => [
						'title' => __('Right', 'thegem'),
						'icon' => 'eicon-h-align-right',
					],
				],
				'toggle' => false,
				'selectors_dictionary' => [
					'left' => 'left',
					'right' => 'right',
				],
			]
		);

		$control->add_responsive_control(
			'more_button_icon_spacing',
			[
				'label' => __('Icon Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
					'%' => [
						'min' => 1,
						'max' => 100,
					],
					'rem' => [
						'min' => 1,
						'max' => 100,
					],
					'em' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-featured-post-btn-box a.gem-button.gem-button-icon-position-right .gem-button-icon' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .gem-featured-post-btn-box a.gem-button.gem-button-icon-position-left .gem-button-icon' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->end_controls_section();
	}

	/**
	 * Arrows Style
	 * @access protected
	 */
	protected function arrows_style($control) {

		$control->start_controls_section(
			'arrows_style_section',
			[
				'label' => __('Arrows Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'paginator_type' => 'arrows',
				],
			]
		);

		$this->add_control(
			'paginator_size',
			[
				'label' => __('Size Preset', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'regular' => __('Regular', 'thegem'),
					'large' => __('Large', 'thegem'),
				],
				'default' => 'regular',
			]
		);

		$this->add_control(
			'paginator_style',
			[
				'label' => __('Style Preset', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'light' => __('Light', 'thegem'),
					'dark' => __('Dark', 'thegem'),
				],
				'default' => 'light',
			]
		);

		$this->add_responsive_control(
			'paginator_position',
			[
				'label' => __('Position', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'left_right' => __('Left & right', 'thegem'),
					'bottom_centered' => __('Bottom centered', 'thegem'),
				],
				'default' => 'left_right',
			]
		);

		$control->add_responsive_control(
			'arrows_icon_size',
			[
				'label' => __('Icon Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 5,
						'max' => 300,
					],
					'%' => [
						'min' => 1,
						'max' => 100,
					],
					'rem' => [
						'min' => 1,
						'max' => 100,
					],
					'em' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-featured-posts-slider .gem-featured-posts-slider-nav a' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .gem-featured-posts-slider .gem-featured-posts-slider-nav a svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'arrows_padding',
			[
				'label' => __('Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'rem', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .gem-featured-posts-slider .gem-featured-posts-slider-nav a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'arrows_radius',
			[
				'label' => __('Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .gem-featured-posts-slider .gem-featured-posts-slider-nav a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'arrows_border_type',
				'label' => __('Border Type', 'thegem'),
				'selector' => '{{WRAPPER}} .gem-featured-posts-slider .gem-featured-posts-slider-nav a',
			]
		);
		$control->remove_control('arrows_border_type_color');

		$control->add_responsive_control(
			'arrows_top_spacing',
			[
				'label' => __('Top Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-featured-posts-slider .gem-featured-posts-slider-nav a' => 'margin-top: {{SIZE}}px;',
				],
				'condition' => [
					'paginator_position' => 'left_right',
				],
			]
		);

		$control->add_responsive_control(
			'arrows_bottom_spacing',
			[
				'label' => __('Bottom Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-featured-posts-slider .gem-featured-posts-slider-nav a' => 'margin-bottom: {{SIZE}}px;',
				],
				'condition' => [
					'paginator_position' => 'bottom_centered',
				],
			]
		);

		$control->add_responsive_control(
			'arrows_side_spacing',
			[
				'label' => __('Side Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-featured-posts-slider .gem-featured-posts-slider-nav a.gem-featured-posts-slide-prev' => 'margin-left: {{SIZE}}px;',
					'{{WRAPPER}} .gem-featured-posts-slider .gem-featured-posts-slider-nav a.gem-featured-posts-slide-next' => 'margin-right: {{SIZE}}px;',
				],
			]
		);

		$control->add_responsive_control(
			'arrows_between_spacing',
			[
				'label' => __('Spacing Between', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-featured-posts-slider .gem-featured-posts-slider-nav a' => 'margin-left: calc({{SIZE}}px/2); margin-right: calc({{SIZE}}px/2);',
				],
				'condition' => [
					'paginator_position' => 'bottom_centered',
				],
			]
		);

		$control->start_controls_tabs('arrows_tabs');
		if (!empty($control->states_list)) {
			foreach ((array)$control->states_list as $stkey => $stelem) {

				if ($stkey == 'active') {
					continue;
				}

				$state = '';
				if ($stkey == 'hover') {
					$state = ':hover';
				}

				$control->start_controls_tab('arrows_tab_' . $stkey, ['label' => $stelem]);


				$control->add_responsive_control(
					'arrows_bg_color_' . $stkey,
					[
						'label' => __('Background Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .gem-featured-posts-slider .gem-featured-posts-slider-nav a' . $state => 'background-color: {{VALUE}};',
						],
					]
				);

				$control->add_responsive_control(
					'arrows_border_color_' . $stkey,
					[
						'label' => __('Border Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .gem-featured-posts-slider .gem-featured-posts-slider-nav a' . $state => 'border-color: {{VALUE}};',
						],
					]
				);

				$control->add_responsive_control(
					'arrows_icon_color_' . $stkey,
					[
						'label' => __('Icon Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .gem-featured-posts-slider .gem-featured-posts-slider-nav a' . $state => 'color: {{VALUE}};',
						],
					]
				);

				$control->end_controls_tab();

			}
		}

		$control->end_controls_tabs();

		$control->end_controls_section();
	}

	/**
	 * Navigation Dots Style
	 * @access protected
	 */
	protected function bullets_style($control) {

		$control->start_controls_section(
			'bullets_style_section',
			[
				'label' => __('Navigation Dots', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'paginator_type' => 'bullets',
				],
			]
		);

		$control->add_responsive_control(
			'bullets_size',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-featured-posts-slider-dots a > span, .gem-featured-posts-slider-dots.size-regular a > span' => 'width: {{SIZE}}px; height: {{SIZE}}px;',
				],
			]
		);

		$control->add_responsive_control(
			'bullets_bottom_position',
			[
				'label' => __('Bottom Position', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-featured-posts-slider-dots' => 'bottom: {{SIZE}}px;',
				],
			]
		);

		$control->add_responsive_control(
			'bullets_between_spacing',
			[
				'label' => __('Spacing Between', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .gem-featured-posts-slider-dots a' => 'margin: 0 calc({{SIZE}}px/2);',
				],
			]
		);

		$control->start_controls_tabs('bullets_tabs');
		if (!empty($control->states_list)) {
			foreach ((array)$control->states_list as $stkey => $stelem) {

				if ($stkey == 'hover') {
					continue;
				}

				$state = '';
				if ($stkey == 'active') {
					$state = '.selected';
				} else {
					$state = ':not(.selected)';
				}

				$control->start_controls_tab('bullets_tab_' . $stkey, ['label' => $stelem]);


				$control->add_responsive_control(
					'bullets_bg_color_' . $stkey,
					[
						'label' => __('Background Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .gem-featured-posts-slider-dots a' . $state . ' > span, .gem-featured-posts-slider-dots.size-regular a' . $state . ' > span' => 'background-color: {{VALUE}};',
						],
					]
				);

				$control->add_group_control(
					Group_Control_Border::get_type(),
					[
						'name' => 'bullets_border_type_' . $stkey,
						'label' => __('Border Type', 'thegem'),
						'selector' => '{{WRAPPER}} .gem-featured-posts-slider-dots a' . $state . ' > span, .gem-featured-posts-slider-dots.size-regular a' . $state . ' > span',
					]
				);

				$control->end_controls_tab();

			}
		}

		$control->end_controls_tabs();

		$control->end_controls_section();
	}

	/**
	 * Custom Fields
	 * @access protected
	 */
	protected function project_details_style($control) {

		$control->start_controls_section(
			'project_details_style',
			[
				'label' => __('Custom Fields', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'blog_show_details' => 'yes',
				],
			]
		);

		$control->add_control(
			'details_alignment_inline',
			[
				'label' => __('Alignment', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'default' => 'default',
				'options' => [
					'default' => __('Default', 'thegem'),
					'center' => __('Center', 'thegem'),
					'left' => __('Left', 'thegem'),
					'right' => __('Right', 'thegem'),
				],
				'frontend_available' => true,
			]
		);

		$control->add_control(
			'details_separator',
			[
				'type' => Controls_Manager::TEXT,
				'label' => __('Separator', 'thegem'),
				'default' => '',
				'frontend_available' => true,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$control->add_control(
			'details_value_header',
			[
				'label' => __('Field Value', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_control(
			'details_value_preset',
			[
				'label' => 'Size Preset',
				'type' => Controls_Manager::SELECT,
				'options' => [
					'title-h1' => __('Title H1', 'thegem'),
					'title-h2' => __('Title H2', 'thegem'),
					'title-h3' => __('Title H3', 'thegem'),
					'title-h4' => __('Title H4', 'thegem'),
					'title-h5' => __('Title H5', 'thegem'),
					'title-h6' => __('Title H6', 'thegem'),
					'title-xlarge' => __('Title xLarge', 'thegem'),
					'styled-subtitle' => __('Styled Subtitle', 'thegem'),
					'main-menu-item' => __('Main Menu', 'thegem'),
					'text-body' => __('Body', 'thegem'),
					'text-body-tiny' => __('Tiny Body', 'thegem'),
				],
				'default' => 'text-body-tiny',
				'frontend_available' => true,
			]
		);

		$control->add_control(
			'details_value_transform',
			[
				'label' => 'Font Transform',
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __('Default', 'thegem'),
					'none' => __('None', 'thegem'),
					'lowercase' => __('Lowercase', 'thegem'),
					'uppercase' => __('Uppercase', 'thegem'),
					'capitalize' => __('Capitalize', 'thegem'),
				],
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .portfolio-item .details .details-item .value' => 'text-transform: {{VALUE}};',
				],
			]
		);

		$control->add_control(
			'details_value_font_weight',
			[
				'label' => __('Font weight', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __('Default', 'thegem'),
					'light' => __('Thin', 'thegem'),
				],
				'default' => '',
				'frontend_available' => true,
			]
		);

		$control->add_group_control(Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'details_value_typography',
				'selector' => '{{WRAPPER}} .portfolio-item .details .details-item .value',
			]
		);

		$control->add_control(
			'details_value_color',
			[
				'label' => __('Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio-item .details .details-item .value' => 'color: {{VALUE}};',
				],
			]
		);

		$control->add_control(
			'details_value_border_color',
			[
				'label' => __('Border Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio-item .details .details-item' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'details_style' => 'labels',
				],
			]
		);

		$control->add_responsive_control(
			'details_icon_size_value',
			[
				'label' => __('Icon Size', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 5,
						'max' => 300,
					],
					'%' => [
						'min' => 1,
						'max' => 100,
					],
					'rem' => [
						'min' => 1,
						'max' => 100,
					],
					'em' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio-item .details .details-item .value i' => 'font-size: {{SIZE}}{{UNIT}}; line-height: 1.2;',
					'{{WRAPPER}} .portfolio-item .details .details-item .value svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->end_controls_section();
	}


	protected function render() {
		$settings = $this->get_settings_for_display();

		$slider_uid = $this->get_id();

		$single_post_id = thegem_templates_init_post() ? thegem_templates_init_post()->ID : get_the_ID();

		extract(thegem_posts_query_section_render($settings, true));

		$query_args = array(
			'post_type' => $post_type,
			'post_status' => 'publish',
			'ignore_sticky_posts' => 1,
		);

		$tax_query = [];

		if (($settings['query_type'] == 'post' && $settings['source'] == 'featured') ||
			($settings['query_type'] == 'archive' && $settings['featured_source'] == 'featured')) {
			$query_args['post__not_in'] = array($single_post_id);
			$query_args['meta_query'] = array(
				array(
					'key' => 'thegem_show_featured_posts_slider',
					'value' => 1
				)
			);
		}

		if (($settings['query_type'] == 'related' && empty($settings['taxonomy_related'])) ||
			($settings['query_type'] == 'manual' && empty($settings['select_posts_manual'])) ||
			(!in_array($settings['query_type'], ['post', 'related', 'archive', 'manual']) && empty($settings['source_post_type_' . $post_type]))) { ?>
			<div class="bordered-box centered-box styled-subtitle">
				<?php echo __('Please select posts sources in "Featured Posts" section', 'thegem') ?>
			</div>
			<?php
			return;
		}

		if (!empty($taxonomy_filter)) {
			foreach ($taxonomy_filter as $tax => $tax_arr) {
				if (!empty($tax_arr) && !in_array('0', $tax_arr)) {
					$query_arr = array(
						'taxonomy' => $tax,
						'field' => 'slug',
						'terms' => $tax_arr,
					);
				} else {
					$query_arr = array(
						'taxonomy' => $tax,
						'operator' => 'EXISTS'
					);
				}
				$tax_query[] = $query_arr;
			}
		}

		if (!empty($date_query)) {
			$query_args['date_query'] = array($date_query);
		}

		if (!empty($tax_query)) {
			$query_args['tax_query'] = $tax_query;
		}

		if (!empty($manual_selection)) {
			$query_args['post__in'] = $manual_selection;
		}

		if (!empty($exclude)) {
			$query_args['post__not_in'] = $exclude;
		}

		if (!empty($blog_authors)) {
			$query_args['author__in'] = $blog_authors;
		}

		if ($settings['order_by'] == 'default' || $settings['order_by'] == 'date_asc' || $settings['order_by'] == 'date_desc') {
			$query_args['orderby'] = 'date';
			$query_args['order'] = $settings['order_by'] == 'date_asc' ? 'ASC' : 'DESC';
		} else {
			$query_args['orderby'] = $settings['order_by'];
		}

		if (isset($settings['order']) && $settings['order'] != 'default') {
			$query_args['order'] = $settings['order'];
		}

		if (!empty($settings['offset'])) {
			$query_args['offset'] = $settings['offset'];
		}

		$query_args['posts_per_page'] = $settings['max_posts'];
		$query = new WP_Query($query_args);

		if (is_array($settings)) {
			foreach ($settings as $key => $value) {
				if (substr($key, 0, 10) == 'paginator_') {
					$paginator_params[substr($key, 10)] = $value;
				}
			}
		}
		if ($paginator_params['icon'] == null) {
			$paginator_params['icon'] = 'custom';
		}

		$this->add_render_attribute(
			'slider-wrap',
			[
				'class' => [
					'gem-featured-posts-slider',
					'style-' . $settings['thegem_elementor_preset'],
					'content-alignment-' . $settings['content_alignment'],
					($settings['fullwidth'] == 'yes' ? 'fullwidth-block' : ''),
				],
				'data-paginator' => htmlspecialchars(json_encode($paginator_params)),
				'data-sliding-effect' => $settings['sliding_effect'],
				'data-auto-scroll' => ($settings['auto_scroll'] == 'yes' && intval($settings['auto_scroll_speed']) > 0) ? esc_attr(intval($settings['auto_scroll_speed'])) : 'false',
			]
		);

		$this->add_render_attribute(
			'button-wrap',
			[
				'class' => [
					'load-more-button gem-button',
					'gem-button-size-' . $settings['more_button_size'],
					'gem-button-style-' . $settings['more_button_type'],
					'gem-button-icon-position-' . $settings['more_button_icon_align'],
					'gem-button-text-weight-' . $settings['more_button_text_weight'],
				],
			]
		);
		$slide_num = 0;
		if ($query->have_posts()) : ?>
			<div class="preloader featured-posts-slider-preloader default-background <?php echo $settings['fullwidth'] == 'yes' ? 'fullwidth-preloader' : ''; ?>"
				 style="<?php echo $settings['fullheight'] == 'yes' ? 'height: 100vh;' : ''; ?>">
				<div class="preloader-spin-new"></div>
			</div>

			<div <?php echo $this->get_render_attribute_string('slider-wrap'); ?>>
				<?php while ($query->have_posts()) {
					$query->the_post();
					$preset_path = __DIR__ . '/templates/content-blog-item-featured-posts-slider.php';
					$preset_path_filtered = apply_filters( 'thegem_featured_posts_slider_item_preset', $preset_path);
					$preset_path_theme = get_stylesheet_directory() . '/templates/featured-posts-slider/content-blog-item-featured-posts-slider.php';

					if (!empty($preset_path_theme) && file_exists($preset_path_theme)) {
						include($preset_path_theme);
					} else if (!empty($preset_path_filtered) && file_exists($preset_path_filtered)) {
						include($preset_path_filtered);
					}
				} ?>

			</div>
			<?php if (!empty($settings['left_arrow_icon']['value'])) : ?>
				<span class="custom-arrow-left">
					<?php \Elementor\Icons_Manager::render_icon($settings['left_arrow_icon'], ['aria-hidden' => 'true']); ?>
				</span>
			<?php endif; ?>
			<?php if (!empty($settings['right_arrow_icon']['value'])) : ?>
				<span class="custom-arrow-right">
					<?php \Elementor\Icons_Manager::render_icon($settings['right_arrow_icon'], ['aria-hidden' => 'true']); ?>
				</span>
			<?php endif; ?>
		<?php endif; ?>

		<?php thegem_templates_close_post($this->get_name(), $this->get_title(), $query->have_posts());

		if (is_admin() && Plugin::$instance->editor->is_edit_mode()): ?>
			<script type="text/javascript">
				jQuery('body').prepareFeaturedPostsSlider();
				jQuery('body').updateFeaturedPostsSlider();
			</script>
		<?php endif;

	}
}

\Elementor\Plugin::instance()->widgets_manager->register(new TheGem_FeaturedPostsSlider());
