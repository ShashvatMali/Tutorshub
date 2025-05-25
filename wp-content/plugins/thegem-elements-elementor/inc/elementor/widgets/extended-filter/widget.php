<?php

namespace TheGem_Elementor\Widgets\Extended_Filter;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use Elementor\Repeater;
use Elementor\Widget_Base;
use TheGem_Elementor\Group_Control_Background_Light;

if (!defined('ABSPATH')) exit;

/**
 * Elementor widget for Extended Filter.
 */
#[\AllowDynamicProperties]
class TheGem_Extended_Filter extends Widget_Base {

	public function __construct($data = [], $args = null) {

		parent::__construct($data, $args);

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
		return 'thegem-extended-filter';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __('Grid Filter', 'thegem');
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
		if (get_post_type($post_id) === 'thegem_templates' && thegem_get_template_type($post_id) === 'product-archive') {
			return ['thegem_product_archive_builder'];
		}
		return ['thegem_elements'];
	}

	public function get_style_depends() {
		return ['thegem-portfolio-filters-list'];
	}

	public function get_script_depends() {
		if (Plugin::$instance->preview->is_preview_mode()) {
			return [
				'thegem-portfolio-grid-extended'
			];
		}
		return ['thegem-portfolio-grid-extended'];
	}

	/* Show reload button */
	public function is_reload_preview_required() {
		return true;
	}

	protected function get_post_type_meta_values($meta_key = '', $post_status = 'publish') {
		global $wpdb;

		if (empty($meta_key)) {
			return;
		}

		$meta_values = $wpdb->get_col($wpdb->prepare("
        SELECT pm.meta_value FROM {$wpdb->postmeta} pm
        LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
        WHERE pm.meta_key = %s
        AND p.post_status = %s
    ", $meta_key, $post_status));

		return array_values(array_unique($meta_values));
	}

	protected function thegem_print_attributes_list($terms, $item, $attribute_name, $attributes_url, $attribute_data, $is_child = false, $collapsed = false) {
		if ($is_child) { ?>
			<ul<?php if ($collapsed) { ?> style="display: none" <?php } ?>>
		<?php }
		$keys = array_keys($terms);
		$simple_arr = $keys == array_keys($keys);
		foreach ($terms as $key => $term) {
			$term_slug = isset($term->slug) ? $term->slug : ($simple_arr ? $term : $key);
			$term_title = isset($term->name) ? $term->name : $term;
			if (empty($term_slug) || empty($term_title)) continue;
			if ($item['attribute_type'] == 'taxonomies' && $item['attribute_taxonomies_hierarchy'] == 'yes') {
				$child_terms = get_terms([
					'taxonomy' => $item['attribute_taxonomies'],
					'orderby' => $item['attribute_order_by'],
					'parent' => $term->term_id,
				]);
				if ($item['attribute_taxonomies_collapsible'] == 'yes') {
					$collapsed = true;
					if (isset($attributes_url[$attribute_name])) {
						foreach ($attributes_url[$attribute_name] as $slug) {
							$active_cat_term = get_term_by('slug', $slug, str_replace("tax_","", $attribute_name));
							if ($term->term_id == $active_cat_term->term_id || term_is_ancestor_of($term->term_id, $active_cat_term->term_id, str_replace("tax_","", $attribute_name))) {
								$collapsed = false;
							}
						}
					}
				}
			} ?>
			<li>
				<?php if ($item['attribute_type'] == 'taxonomies' && $item['attribute_taxonomies_click_behavior'] == 'archive_link') { ?>
					<a href="<?php echo get_term_link($term->slug, $item['attribute_taxonomies']); ?>"
						class="<?php echo isset($attributes_url[$attribute_name]) && in_array($term_slug, $attributes_url[$attribute_name]) ? 'active' : '';
						echo $collapsed ? ' collapsed' : ''; ?>">
						<span class="title"><?php echo esc_html($term_title); ?></span>
					</a>
				<?php } else {
					if ( $attribute_name == 'tax_product_cat') {
						$attribute_type = 'category';
					} else {
						$attribute_type = $item['attribute_type'];
					} ?>
					<a href="#"
						data-filter-type="<?php echo esc_attr($attribute_type); ?>"
						data-attr="<?php echo esc_attr($attribute_name); ?>"
						data-filter="<?php echo esc_attr($term_slug); ?>"
						class="<?php echo isset($attributes_url[$attribute_name]) && in_array($term_slug, $attributes_url[$attribute_name]) ? 'active' : '';
						echo $collapsed ? ' collapsed' : ''; ?>"
						rel="nofollow">
						<?php if (!empty($attribute_data) && ($attribute_data->type == 'color' || $attribute_data->type == 'label' || $attribute_data->type == 'image')) {
							if(!wp_style_is('thegem-woocommerce')) {
								wp_enqueue_style('thegem-woocommerce-sidebar-swatches');
							}
							if ($attribute_data->type == 'color') {
								$attribute_display_type = get_term_meta( $term->term_id, 'thegem_color_display_type', true );
								$attribute_color = get_term_meta( $term->term_id, 'thegem_color', true );
								$attribute_color_dual = get_term_meta( $term->term_id, 'thegem_color_dual', true );
								$attribute_color_2 = get_term_meta( $term->term_id, 'thegem_color_2', true );
								$attribute_color_image = get_term_meta( $term->term_id, 'thegem_color_image', true );
								$attribute_image = absint(get_term_meta( $term->term_id, 'thegem_color_image', true ));
								$attribute_image_thumb = wc_placeholder_img_src();
								if ( $attribute_image ) {
									$attribute_image_thumb_data = thegem_generate_thumbnail_src($attribute_image, 'thegem-post-thumb-medium');
									$attribute_image_thumb = $attribute_image_thumb_data[0];
								}

								if($attribute_display_type === 'image') {
									echo '<span class="color"' . (!empty($attribute_image_thumb) ? ' style="background-image: url(' . esc_attr($attribute_image_thumb) . ');"' : '') . '></span>';
								} else {
									if($attribute_color_dual) {
										echo '<span class="color"' . (!empty($attribute_color) && !empty($attribute_color_2) ? ' style="background-image: linear-gradient(90deg, ' . esc_attr($attribute_color).' 50%, ' . esc_attr($attribute_color_2) . ' 50%);"' : '') . '></span>';
									} else {
										echo '<span class="color"' . (!empty($attribute_color) ? ' style="background-color: ' . esc_attr($attribute_color) . ';"' : '') . '></span>';
									}
								}
							} else if ($attribute_data->type == 'image') {
								$attribute_image = wp_get_attachment_image_url(get_term_meta( $term->term_id, 'thegem_image', true ), 'woocommerce_thumbnail');
								echo '<span class="image"' . (!empty($attribute_image) ? ' style="background-image: url(' . esc_attr($attribute_image).');"' : '') . '></span>';
							} else if ($attribute_data->type == 'label') {
								$attribute_label = get_term_meta( $term->term_id, 'thegem_label', true );
								$term_title = !empty($attribute_label) ? $attribute_label : $term_title;
							}
						} else if ($item['attribute_query_type'] == 'or') {
							echo '<span class="check"></span>';
						} ?>
						<span class="title"><?php echo esc_html($term_title); ?></span>
						<?php if (!empty($child_terms) && $item['attribute_taxonomies_collapsible'] == 'yes') { ?>
							<span class="filters-collapsible-arrow"></span>
						<?php } ?>
					</a>
				<?php }

				if (!empty($child_terms)) {
					$this->thegem_print_attributes_list($child_terms, $item, $attribute_name, $attributes_url, $attribute_data, true, $collapsed);
				} ?>
			</li>
		<?php }
		if ($is_child) {
			echo '</ul>';
		}
	}

	protected function thegem_select_products_attributes() {
		$out = [];
		if (defined( 'WC_PLUGIN_FILE' )) {
			global $wc_product_attributes;

			if (empty($wc_product_attributes) || is_wp_error($wc_product_attributes)) {
				return $out;
			}

			foreach ((array)$wc_product_attributes as $attr) {
				if (!empty($attr->attribute_name)) {
					$out[$attr->attribute_name] = $attr->attribute_label;
				}
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
				'label' => __('Filters Layout', 'thegem'),
			]
		);

		$this->add_control(
			'grid_filter',
			[
				'label' => __('Grid to Filter', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'id',
				'options' => [
					'id' => __('Grid ID', 'thegem'),
					'archive' => __('Current Query (Archive)', 'thegem'),
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'grid_id',
			[
				'label' => __('Grid ID', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'description' => __('Specify the ID of the grid to filter. For archive templates use "Current Query" setting in "Grid to Fllter" field.', 'thegem'),
				'condition' => [
					'grid_filter' => 'id'
				]
			]
		);

		$this->add_control(
			'filtering_type',
			[
				'label' => __('Filtering Type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'instant',
				'options' => [
					'instant' => __('Instant Filter', 'thegem'),
					'button' => __('Filter Button', 'thegem'),
					'external' => __('Triggered by Other Grid Filter', 'thegem'),
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'apply_button_text',
			[
				'label' => __('Button Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Apply Filters', 'thegem'),
				'condition' => [
					'filtering_type' => 'button',
				],
			]
		);

		$this->add_control(
			'apply_button_id',
			[
				'label' => __('Button ID', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => __('Button ID', 'thegem'),
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'filtering_type' => 'button',
				],
			]
		);

		$this->add_control(
			'apply_button_class',
			[
				'label' => __('Button Class', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => __('Button Class', 'thegem'),
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'filtering_type' => 'button',
				],
			]
		);

		$this->add_control(
			'grid_url',
			[
				'label' => __('Grid URL (optional)', 'thegem'),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __('https://your-link.com', 'thegem'),
				'condition' => [
					'filtering_type' => 'button',
				],
				'description' => __('You can filter the grid located on the other page by specifying its URL', 'thegem')
			]
		);

		$this->add_control(
			'filters_header',
			[
				'label' => __('Filters', 'thegem'),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'filters_style',
			[
				'label' => __('Filters Style (Desktop)', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'sidebar',
				'options' => [
					'sidebar' => __('Vertical', 'thegem'),
					'standard' => __('Horizontal', 'thegem'),
					'hidden' => __('Hidden Sidebar', 'thegem'),
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'filters_style_mobile',
			[
				'label' => __('Filters Style (Mobiles)', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'as_desktop',
				'options' => [
					'hidden' => __('Hidden Sidebar', 'thegem'),
					'as_desktop' => __('As Set for Desktop', 'thegem'),
				],
				'frontend_available' => true,
				'condition' => [
					'filters_style!' => 'hidden'
				]
			]
		);

		$this->add_control(
			'hidden_breakpoint',
			[
				'label' => __('Hidden Sidebar Breakpoint', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 375,
						'max' => 1920,
					],
				],
				'condition' => [
					'filters_style!' => 'hidden',
					'filters_style_mobile' => 'hidden',
				]
			]
		);

		$this->add_control(
			'filters_scroll_top', [
				'label' => __('Scroll To Top', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'attribute_title',
			[
				'type' => Controls_Manager::TEXT,
				'label' => __('Title', 'thegem'),
				'default' => __('Filter by', 'thegem'),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'show_title', [
				'label' => __('Show Title', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'thegem'),
				'label_off' => __('Hide', 'thegem')
			]
		);

		$product_arr = [];
		if (defined( 'WC_PLUGIN_FILE' )) {
			$product_arr = [
				'products_attributes' => __('Products Attributes', 'thegem'),
				'products_price' => __('Products Price', 'thegem'),
				'products_status' => __('Products Status', 'thegem')
			];
		}

		$repeater->add_control(
			'attribute_type',
			[
				'label' => __('Filter By', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'taxonomies',
				'options' => array_merge(
					[
						'taxonomies' => __('Taxonomies', 'thegem'),
						'custom_fields' => __('Custom Fields (TheGem)', 'thegem'),
						'details' => __('Project Details', 'thegem'),
						'manual_key' => __('Manual Meta Key', 'thegem'),
						'search' => __('Search', 'thegem'),
					],
					$product_arr,
					thegem_get_acf_plugin_groups()
				),
			]
		);

		$repeater->add_control(
			'manual_key_field',
			[
				'label' => __('Specify Field`s Name', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'description' => __('Use field`s name / meta key as set in custom fields settings', 'thegem'),
				'condition' => [
					'attribute_type' => 'manual_key',
				],
			]
		);

		if (defined( 'WC_PLUGIN_FILE' )) {

			$repeater->add_control(
				'attribute_name_products',
				[
					'label' => __('Attribute', 'thegem'),
					'type' => Controls_Manager::SELECT,
					'options' => $this->thegem_select_products_attributes(),
					'frontend_available' => true,
					'condition' => [
						'attribute_type' => 'products_attributes',
					],
				]
			);

			$repeater->add_control(
				'filter_by_status_sale', [
					'label' => __('On Sale', 'thegem'),
					'default' => 'yes',
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('On', 'thegem'),
					'label_off' => __('Off', 'thegem'),
					'frontend_available' => true,
					'condition' => [
						'attribute_type' => 'products_status',
					],
				]
			);

			$repeater->add_control(
				'filter_by_status_sale_text',
				[
					'type' => Controls_Manager::TEXT,
					'label' => __('“On Sale” Text', 'thegem'),
					'input_type' => 'text',
					'default' => __('On Sale', 'thegem'),
					'frontend_available' => true,
					'condition' => [
						'filter_by_status_sale' => 'yes',
						'attribute_type' => 'products_status',
					],
					'dynamic' => [
						'active' => true,
					],
				]
			);

			$repeater->add_control(
				'filter_by_status_stock', [
					'label' => __('In Stock', 'thegem'),
					'default' => 'yes',
					'type' => Controls_Manager::SWITCHER,
					'label_on' => __('On', 'thegem'),
					'label_off' => __('Off', 'thegem'),
					'frontend_available' => true,
					'condition' => [
						'attribute_type' => 'products_status',
					],
				]
			);

			$repeater->add_control(
				'filter_by_status_stock_text',
				[
					'type' => Controls_Manager::TEXT,
					'label' => __('“In Stock” Text', 'thegem'),
					'input_type' => 'text',
					'default' => __('In Stock', 'thegem'),
					'frontend_available' => true,
					'condition' => [
						'filter_by_status_stock' => 'yes',
						'attribute_type' => 'products_status',
					],
					'dynamic' => [
						'active' => true,
					],
				]
			);
		}

		$repeater->add_control(
			'attribute_taxonomies',
			[
				'label' => __('Select Taxonomy', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => get_thegem_select_post_type_taxonomies(),
				'default' => 'category',
				'condition' => [
					'attribute_type' => 'taxonomies',
				],
			]
		);

		$repeater->add_control(
			'attribute_taxonomies_hierarchy', [
				'label' => __('Hierarchy', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'thegem'),
				'label_off' => __('Hide', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'attribute_type' => 'taxonomies',
				],
			]
		);

		$repeater->add_control(
			'attribute_taxonomies_collapsible', [
				'label' => __('Collapsible', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'thegem'),
				'label_off' => __('Hide', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'attribute_type' => 'taxonomies',
					'attribute_taxonomies_hierarchy' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'attribute_taxonomies_click_behavior',
			[
				'label' => __('Click Behavior', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'filtering' => __('Filtering', 'thegem'),
					'archive_link' => __('Link to Post Archive', 'thegem'),
				],
				'default' => 'filtering',
				'condition' => [
					'attribute_type' => 'taxonomies',
				]
			]
		);

		$options = thegem_select_theme_options_custom_fields_all();
		$default = !empty($options) ? array_keys($options)[0] : '';

		$repeater->add_control(
			'attribute_custom_fields',
			[
				'label' => __('Select Field', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => $options,
				'default' => $default,
				'condition' => [
					'attribute_type' => 'custom_fields',
				],
				'description' => __('Go to the <a href="' . get_site_url() . '/wp-admin/admin.php?page=thegem-theme-options#/single-pages" target="_blank">Theme Options -> Single Pages</a> to manage your custom fields.', 'thegem')
			]
		);

		$options = thegem_select_portfolio_details();
		$default = !empty($options) ? array_keys($options)[0] : '';

		$repeater->add_control(
			'attribute_details',
			[
				'label' => __('Select Field', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'options' => $options,
				'default' => $default,
				'condition' => [
					'attribute_type' => 'details',
				],
				'description' => __('Go to the <a href="' . get_site_url() . '/wp-admin/admin.php?page=thegem-theme-options#/single-pages/portfolio" target="_blank">Theme Options -> Single Pages -> Portfolio Page</a> to manage your custom fields.', 'thegem')
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
						'description' => __('Go to the <a href="' . get_site_url() . '/wp-admin/edit.php?post_type=acf-field-group" target="_blank">ACF -> Field Groups</a> to manage your custom fields.', 'thegem'),
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
				'condition' => [
					'attribute_type!' => ['taxonomies', 'products_attributes', 'products_price', 'products_status', 'search'],
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
					'attribute_type!' => ['taxonomies', 'products_price', 'products_status'],
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
					'attribute_type!' => ['taxonomies', 'products_price', 'products_status'],
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
					'attribute_type!' => ['taxonomies', 'products_price', 'products_status'],
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
					'attribute_type!' => ['taxonomies', 'products_price', 'products_status'],
					'attribute_meta_type' => 'number',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'attribute_order_by',
			[
				'label' => __('Order By', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'name',
				'options' => [
					'name' => __('Name', 'thegem'),
					'term_order' => __('Term Order', 'thegem'),
				],
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'attribute_type',
							'operator' => '=',
							'value' => 'taxonomies',
						],
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'attribute_type',
									'operator' => '!in',
									'value' => ['taxonomies', 'products_price', 'products_status', 'search'],
								],
								[
									'name' => 'attribute_meta_type',
									'operator' => '=',
									'value' => 'text',
								],
							]
						]
					]
				]
			]
		);

		$repeater->add_control(
			'attribute_query_type',
			[
				'label' => __('Query Type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'or',
				'options' => [
					'and' => __('AND', 'thegem'),
					'or' => __('OR', 'thegem'),
				],
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'attribute_type',
							'operator' => '=',
							'value' => 'taxonomies',
						],
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'attribute_type',
									'operator' => '!in',
									'value' => ['taxonomies', 'products_price', 'products_status', 'search'],
								],
								[
									'name' => 'attribute_meta_type',
									'operator' => '=',
									'value' => 'text',
								],
							]
						]
					]
				]
			]
		);

		$repeater->add_control(
			'attribute_display_type',
			[
				'label' => __('Display Type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'dropdown',
				'options' => [
					'list' => __('List', 'thegem'),
					'dropdown' => __('Dropdown', 'thegem'),
				],
				'condition' => [
					'attribute_type!' => 'search',
				],
			]
		);

		$repeater->add_control(
			'attribute_display_dropdown_open',
			[
				'label' => __('Open Dropdown', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'default' => 'hover',
				'options' => [
					'hover' => __('On Hover', 'thegem'),
					'click' => __('On Click', 'thegem'),
				],
				'condition' => [
					'attribute_display_type' => 'dropdown',
					'attribute_type!' => 'search',
				],
			]
		);

		$this->add_control(
			'search_description',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => __('To choose between search by content or by meta fields go to the grid\'s "Filters & Sorting" settings.', 'thegem'),
				'content_classes' => 'elementor-descriptor',
				'condition' => [
					'attribute_type' => 'search',
				],
			]
		);

		$repeater->add_control(
			'search_reset_filters', [
				'label' => __('Reset Filters on Search', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'attribute_type' => 'search',
				],
			]
		);

		$repeater->add_control(
			'live_search', [
				'label' => __('Live Search', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'condition' => [
					'attribute_type' => 'search',
				],
			]
		);

		$this->add_control(
			'repeater_attributes',
			[
				'type' => Controls_Manager::REPEATER,
				'label' => __('Attributes', 'thegem'),
				'fields' => $repeater->get_controls(),
				'title_field' => '{{{ attribute_title }}}',
				'default' => [
					[
						'attribute_title' => __('Categories', 'thegem'),
					]
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'filters_text_labels_header',
			[
				'label' => __('Text Labels', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'filters_text_labels_all_text',
			[
				'type' => Controls_Manager::TEXT,
				'label' => __('“Show All” Text', 'thegem'),
				'input_type' => 'text',
				'default' => __('Show All', 'thegem'),
				'frontend_available' => true,
				'dynamic' => [
					'active' => true,
				],
				'description' => __('Use %ATTR% to inject the attribute\'s title', 'thegem')
			]
		);

		$this->add_control(
			'filters_text_labels_search_text',
			[
				'label' => __('“Search” Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => __('Search...', 'thegem'),
				'frontend_available' => true,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'filters_text_labels_search_title',
			[
				'label' => __('“Search” Title', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => __('Search', 'thegem'),
				'frontend_available' => true,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'filter_buttons_hidden_show_text',
			[
				'label' => __('“Show Filters” Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => __('Show Filters', 'thegem'),
				'frontend_available' => true,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'filter_buttons_hidden_sidebar_title',
			[
				'label' => __('Sidebar Title', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => __('Filter', 'thegem'),
				'frontend_available' => true,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'filter_buttons_hidden_filter_by_text',
			[
				'label' => __('“Filter By” Text', 'thegem'),
				'type' => Controls_Manager::TEXT,
				'input_type' => 'text',
				'default' => __('Filter By', 'thegem'),
				'frontend_available' => true,
				'dynamic' => [
					'active' => true,
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

		/* Filter Style */
		$this->filter_buttons_style($control);

		/* Filter Style (Vertical and Hidden) */
		$this->filter_buttons_sidebar_style($control);

		/* Apply Button Style */
		$this->apply_button_style($control);
	}

	/**
	 * Filter Style
	 * @access protected
	 */
	protected function filter_buttons_style($control) {

		$control->start_controls_section(
			'filter_buttons_style',
			[
				'label' => __('Filter Style', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->add_group_control(Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'filter_buttons_typography',
				'selector' => '{{WRAPPER}} .portfolio-filters-list .portfolio-filter-item,
				{{WRAPPER}} .portfolio-filters-list .portfolio-show-filters-button,
				{{WRAPPER}} .portfolio-filters-list .portfolio-search-filter input,
				{{WRAPPER}} .portfolio-filters-list .portfolio-search-filter input::placeholder',
			]
		);

		$control->add_responsive_control(
			'filter_buttons_width',
			[
				'label' => __('Field Width', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
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
					'{{WRAPPER}} .portfolio-filters-list .portfolio-filter-item:not(.filters-apply-button)' => 'width: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$control->add_responsive_control(
			'filter_buttons_bottom_spacing',
			[
				'label' => __('Bottom Spacing', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
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
					'{{WRAPPER}} .portfolio-top-panel' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$control->add_responsive_control(
			'filter_buttons_standard_space_between',
			[
				'label' => __('Space Between', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'rem', 'em'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
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
					'{{WRAPPER}} .portfolio-filters-list.style-standard:not(.style-standard-mobile) .widget-area' => 'gap: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .portfolio-filters-list:is(.style-sidebar, .style-hidden, .style-standard-mobile) .portfolio-filter-item:not(:first-child)' => 'padding-top: calc({{SIZE}}{{UNIT}}/2)',
					'{{WRAPPER}} .portfolio-filters-list:is(.style-sidebar, .style-hidden, .style-standard-mobile) .portfolio-filter-item:not(:last-child)' => 'padding-bottom: calc({{SIZE}}{{UNIT}}/2)',
				]
			]
		);

		$control->add_control(
			'filter_buttons_standard_alignment',
			[
				'label' => __('Horizontal Alignment', 'thegem'),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'default' => 'left',
				'options' => [
					'left' => [
						'title' => __('Left', 'thegem'),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __('Center', 'elementor'),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' => __('Right', 'thegem'),
						'icon' => 'eicon-h-align-right',
					],
					'justify' => [
						'title' => __('Justified', 'thegem'),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'frontend_available' => true,
				'condition' => [
					'filters_style' => 'standard',
				]
			]
		);

		$control->add_responsive_control(
			'filter_buttons_standard_alignment_vertical',
			[
				'label' => __('Vertical Alignment', 'thegem'),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'flex-start' => [
						'title' => __('Top', 'thegem'),
						'icon' => 'eicon-v-align-top',
					],
					'center' => [
						'title' => __('Center', 'thegem'),
						'icon' => 'eicon-v-align-middle',
					],
					'flex-end' => [
						'title' => __('Bottom', 'thegem'),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio-filters-list.style-standard:not(.style-standard-mobile) .widget-area' => 'align-items: {{VALUE}}',
				],
				'condition' => [
					'filters_style' => 'standard',
				]
			]
		);

		$control->add_responsive_control(
			'apply_button_stretch_last_element',
			[
				'label' => __('Stretch Last Element', 'thegem'),
				'default' => '',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'selectors' => [
					'{{WRAPPER}} .portfolio-filters-list.style-standard:not(.style-standard-mobile) .portfolio-filter-item:last-child' => 'flex: auto;',
				],
				'condition' => [
					'filters_style' => 'standard',
				]
			]
		);

		$control->add_group_control(Group_Control_Typography::get_type(),
			[
				'label' => __('"Filter by" Typography', 'thegem'),
				'name' => 'filter_buttons_filterby_typography',
				'selector' => '{{WRAPPER}} .portfolio-filters-list .portfolio-filter-item .widget-title',
			]
		);

		$control->add_control(
			'filter_buttons_filterby_color',
			[
				'label' => __('"Filter by" Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio-filters-list .portfolio-filter-item .widget-title' => 'color: {{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			'filter_buttons_filterby_spacing',
			[
				'label' => __('"Filter by" Spacing', 'thegem'),
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
					'{{WRAPPER}} .portfolio-filters-list .portfolio-filter-item .widget-title' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				]
			]
		);

		$control->add_control(
			'filter_buttons_dropdown_selector_header',
			[
				'label' => __('Dropdown Selector and Search Input', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_control(
			'filter_buttons_dropdown_selector_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio-filters-list.style-standard:not(.style-standard-mobile) .portfolio-filter-item:not(.display-type-dropdown) .name,
					{{WRAPPER}} .portfolio-filters-list .portfolio-filter-item.display-type-dropdown .selector-title,
					{{WRAPPER}} .portfolio-filters-list .portfolio-filters-area .portfolio-search-filter input,
					{{WRAPPER}} .portfolio-filters-list .portfolio-filters-area .portfolio-search-filter .portfolio-search-filter-button' => 'color: {{VALUE}};',
				],
			]
		);

		$control->add_control(
			'filter_buttons_dropdown_selector_background_color',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio-filters-list.style-standard:not(.style-standard-mobile) .portfolio-filter-item:not(.display-type-dropdown) .name,
					{{WRAPPER}} .portfolio-filters-list .portfolio-filter-item.display-type-dropdown .selector-title,
					{{WRAPPER}} .portfolio-filters-list .portfolio-filters-area .portfolio-search-filter input' => 'background-color: {{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			'filter_buttons_dropdown_selector_border_radius',
			[
				'label' => __('Border Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .portfolio-filters-list.style-standard:not(.style-standard-mobile) .portfolio-filter-item:not(.display-type-dropdown) .name,
					{{WRAPPER}} .portfolio-filters-list .portfolio-filter-item.display-type-dropdown .selector-title,
					{{WRAPPER}} .portfolio-filters-list .portfolio-filters-area .portfolio-search-filter input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'filter_buttons_dropdown_selector_border_type',
				'label' => __('Border Type', 'thegem'),
				'selector' => '{{WRAPPER}} .portfolio-filters-list.style-standard:not(.style-standard-mobile) .portfolio-filter-item:not(.display-type-dropdown) .name,
					{{WRAPPER}} .portfolio-filters-list .portfolio-filter-item.display-type-dropdown .selector-title,
					{{WRAPPER}} .portfolio-filters-list .portfolio-filters-area .portfolio-search-filter input',
			]
		);

		$control->add_responsive_control(
			'filter_buttons_dropdown_selector_text_padding',
			[
				'label' => __('Text Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .portfolio-filters-list.style-standard:not(.style-standard-mobile) .portfolio-filter-item:not(.display-type-dropdown) .name,
					{{WRAPPER}} .portfolio-filters-list .portfolio-filter-item.display-type-dropdown .selector-title,
					{{WRAPPER}} .portfolio-filters-list .portfolio-filters-area .portfolio-search-filter input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_control(
			'filter_buttons_dropdown_header',
			[
				'label' => __('Dropdown', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->add_group_control(
			Group_Control_Background_Light::get_type(),
			[
				'name' => 'filter_buttons_dropdown_background',
				'label' => __('Background Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .portfolio-filters-list.style-standard:not(.style-standard-mobile) .portfolio-filter-item .portfolio-filter-item-list,
					{{WRAPPER}} .portfolio-filters-list .portfolio-filter-item.display-type-dropdown .portfolio-filter-item-list',
			]
		);

		$control->add_responsive_control(
			'filter_buttons_dropdown_border_radius',
			[
				'label' => __('Border Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .portfolio-filters-list.style-standard:not(.style-standard-mobile) .portfolio-filter-item .portfolio-filter-item-list,
					{{WRAPPER}} .portfolio-filters-list .portfolio-filter-item.display-type-dropdown .portfolio-filter-item-list' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'filter_buttons_dropdown_shadow',
				'label' => __('Shadow', 'thegem'),
				'selector' => '{{WRAPPER}} .portfolio-filters-list.style-standard:not(.style-standard-mobile) .portfolio-filter-item .portfolio-filter-item-list,
					{{WRAPPER}} .portfolio-filters-list .portfolio-filter-item.display-type-dropdown .portfolio-filter-item-list',
			]
		);

		$control->start_controls_tabs('filter_buttons_dropdown_tabs');

		if (!empty($control->states_list)) {
			foreach ((array)$control->states_list as $stkey => $stelem) {
				$state = '';
				$addition_selector = '';
				$addition_selector_normal = '';
				if ($stkey == 'active') {
					$state = '.active';
					$addition_selector = '{{WRAPPER}} .portfolio-filters-list.style-standard:not(.style-standard-mobile) .portfolio-filter-item .portfolio-filter-item-list .price-range-slider .slider-range .ui-slider-range,
					{{WRAPPER}} .portfolio-filters-list .portfolio-filter-item.display-type-dropdown .portfolio-filter-item-list .price-range-slider .slider-range .ui-slider-range,
					{{WRAPPER}} .portfolio-filters-list.style-standard:not(.style-standard-mobile) .portfolio-filter-item .portfolio-filter-item-list .price-range-slider .slider-range .ui-slider-handle + span,
					{{WRAPPER}} .portfolio-filters-list .portfolio-filter-item.display-type-dropdown .portfolio-filter-item-list .price-range-slider .slider-range .ui-slider-handle + span,
					{{WRAPPER}} .portfolio-filters-list.style-standard:not(.style-standard-mobile) .portfolio-filter-item .portfolio-filter-item-list .price-range-slider .slider-range .ui-slider-handle,
					{{WRAPPER}} .portfolio-filters-list .portfolio-filter-item.display-type-dropdown .portfolio-filter-item-list .price-range-slider .slider-range .ui-slider-handle';
				} else if ($stkey == 'hover') {
					$state = ':hover';
				} else {
					$addition_selector_normal = ', {{WRAPPER}} .portfolio-filters-list.style-standard:not(.style-standard-mobile) .portfolio-filter-item .portfolio-filter-item-list .price-range-slider .slider-amount, 
					{{WRAPPER}} .portfolio-filters-list .portfolio-filter-item.display-type-dropdown .portfolio-filter-item-list .price-range-slider .slider-amount, 
					{{WRAPPER}} .portfolio-filters-list.style-standard:not(.style-standard-mobile) .portfolio-filter-item .portfolio-filter-item-list .price-range-slider .slider-amount .slider-amount-text, 
					{{WRAPPER}} .portfolio-filters-list .portfolio-filter-item.display-type-dropdown .portfolio-filter-item-list .price-range-slider .slider-amount .slider-amount-text';
				}

				$control->start_controls_tab('filter_buttons_dropdown_tab_' . $stkey, ['label' => $stelem]);

				$control->add_control(
					'filter_buttons_dropdown_text_color_' . $stkey,
					[
						'label' => __('Text Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .portfolio-filters-list.style-standard:not(.style-standard-mobile) .portfolio-filter-item .portfolio-filter-item-list ul li a' . $state . ',
							{{WRAPPER}} .portfolio-filters-list .portfolio-filter-item.display-type-dropdown .portfolio-filter-item-list ul li a' . $state . $addition_selector_normal => 'color: {{VALUE}};',
							$addition_selector => 'background-color: {{VALUE}};',
						],
					]
				);

				$control->add_control(
					'filter_buttons_dropdown_price_range_background_color_' . $stkey,
					[
						'label' => __('Number Range Background', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .portfolio-filters-list.style-standard:not(.style-standard-mobile) .portfolio-filter-item .portfolio-filter-item-list .price-range-slider .slider-amount' . $state . ',
							{{WRAPPER}} .portfolio-filters-list .portfolio-filter-item.display-type-dropdown .portfolio-filter-item-list .price-range-slider .slider-amount' . $state => 'background-color: {{VALUE}};',
						],
					]
				);

				$control->end_controls_tab();
			}
		}

		$control->end_controls_tabs();

		$control->add_control(
			'filter_buttons_list_dropdown_header',
			[
				'label' => __('Filters List', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$control->start_controls_tabs('filter_buttons_list_tabs');

		if (!empty($control->states_list)) {
			foreach ((array)$control->states_list as $stkey => $stelem) {
				$state = '';
				$addition_selector = '';
				$addition_selector_normal = '';
				if ($stkey == 'active') {
					$state = '.active';
					$addition_selector = '{{WRAPPER}} .portfolio-filters-list:is(.style-sidebar, .style-hidden, .style-standard-mobile) .portfolio-filter-item:not(.display-type-dropdown) .price-range-slider .slider-range .ui-slider-range,
					{{WRAPPER}} .portfolio-filters-list:is(.style-sidebar, .style-hidden, .style-standard-mobile) .portfolio-filter-item:not(.display-type-dropdown) .price-range-slider .slider-range .ui-slider-handle + span,
					{{WRAPPER}} .portfolio-filters-list:is(.style-sidebar, .style-hidden, .style-standard-mobile) .portfolio-filter-item:not(.display-type-dropdown) .price-range-slider .slider-range .ui-slider-handle';
				} else if ($stkey == 'hover') {
					$state = ':hover';
				} else {
					$addition_selector_normal = ', {{WRAPPER}} .portfolio-filters-list:is(.style-sidebar, .style-hidden, .style-standard-mobile) .portfolio-filter-item:not(.display-type-dropdown) .price-range-slider .slider-amount, 
					{{WRAPPER}} .portfolio-filters-list:is(.style-sidebar, .style-hidden, .style-standard-mobile) .portfolio-filter-item:not(.display-type-dropdown) .price-range-slider .slider-amount .slider-amount-text';
				}

				$control->start_controls_tab('filter_buttons_list_tab_' . $stkey, ['label' => $stelem]);

				$control->add_control(
					'filter_buttons_list_text_color_' . $stkey,
					[
						'label' => __('Text Color', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .portfolio-filters-list:is(.style-sidebar, .style-hidden, .style-standard-mobile) .portfolio-filter-item:not(.display-type-dropdown) ul li a' . $state . $addition_selector_normal => 'color: {{VALUE}};',
							$addition_selector => 'background-color: {{VALUE}};',
						],
					]
				);

				$control->add_control(
					'filter_buttons_list_price_range_background_color_' . $stkey,
					[
						'label' => __('Number Range Background', 'thegem'),
						'type' => Controls_Manager::COLOR,
						'label_block' => false,
						'selectors' => [
							'{{WRAPPER}} .portfolio-filters-list:is(.style-sidebar, .style-hidden, .style-standard-mobile) .portfolio-filter-item:not(.display-type-dropdown) .price-range-slider .slider-amount' . $state => 'background-color: {{VALUE}};',
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
	 * Filter Style (Vertical and Hidden)
	 * @access protected
	 */
	protected function filter_buttons_sidebar_style($control) {

		$control->start_controls_section(
			'filter_buttons_style_hidden',
			[
				'label' => __('Filter Style (Vertical and Hidden)', 'thegem'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$control->add_responsive_control(
			'items_list_max_height',
			[
				'label' => __('Filter List Max Height', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 1000,
					],
				],
				'description' => __('Limit the filter attributes displayed in the list using the scrollbar. Leave blank for a complete list.', 'thegem'),
				'selectors' => [
					'{{WRAPPER}} .portfolio-filter-item-list' => 'max-height: {{SIZE}}{{UNIT}}; padding-right: 10px;',
				],
			]
		);

		$control->add_control(
			'filter_buttons_hidden_sidebar_separator_enable', [
				'label' => __('Filters Separator', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'thegem'),
				'label_off' => __('Hide', 'thegem'),
				'frontend_available' => true,
			]
		);

		$control->add_responsive_control(
			'filter_buttons_hidden_sidebar_separator_width',
			[
				'label' => __('Separator Width', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 4,
					],
				],
				'default' => [
					'size' => 1,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio-filters-list:is(.style-hidden, .style-sidebar, .style-standard-mobile) .portfolio-filter-item' => 'border-width: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'filter_buttons_hidden_sidebar_separator_enable' => 'yes',
				]
			]
		);

		$control->add_control(
			'filter_buttons_hidden_sidebar_separator_color',
			[
				'label' => __('Separator Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio-filters-list:is(.style-hidden, .style-sidebar, .style-standard-mobile) .portfolio-filter-item' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'filter_buttons_hidden_sidebar_separator_enable' => 'yes',
				]
			]
		);

		$control->add_control(
			'filter_buttons_show_filters_header',
			[
				'label' => __('Show Filters Button', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'filters_style' => 'hidden',
				]
			]
		);

		$control->add_control(
			'filter_buttons_show_responsive_filters_header',
			[
				'label' => __('Responsive Mode - Show Filters Button', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'filters_style!' => 'hidden',
				]
			]
		);

		$control->add_control(
			'filter_buttons_hidden_position',
			[
				'label' => __('Position', 'thegem'),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'left' => [
						'title' => __('Left', 'thegem'),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __('Center', 'elementor'),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' => __('Right', 'thegem'),
						'icon' => 'eicon-h-align-right',
					],
					'justify' => [
						'title' => __('Justified', 'thegem'),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'selectors_dictionary' => [
					'left' => 'margin-left: 0; margin-right: auto;',
					'center' => 'margin-left: auto; margin-right: auto;',
					'right' => 'margin-left: auto; margin-right: 0;',
					'justify' => 'width: 100%; margin-left: 0; margin-right: 0;',
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio-show-filters-button' => '{{VALUE}};',
				],
				'default' => 'left',
			]
		);

		$control->add_control(
			'filter_buttons_hidden_show_icon', [
				'label' => __('Show Icon', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
			]
		);

		$control->add_control(
			'filter_buttons_hidden_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio-filters-list .portfolio-show-filters-button' => 'color: {{VALUE}};',
				]
			]
		);

		$control->add_group_control(Group_Control_Typography::get_type(),
			[
				'label' => __('Typography', 'thegem'),
				'name' => 'filter_buttons_hidden_typography',
				'selector' => '{{WRAPPER}} .portfolio-filters-list .portfolio-show-filters-button',
			]
		);

		$control->add_responsive_control(
			'filter_buttons_hidden_border_radius',
			[
				'label' => __('Border Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .portfolio-filters-list .portfolio-show-filters-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$control->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'filter_buttons_hidden_border_type',
				'label' => __('Border Type', 'thegem'),
				'selector' => '{{WRAPPER}} .portfolio-filters-list .portfolio-show-filters-button'
			]
		);

		$control->add_responsive_control(
			'filter_buttons_hidden_text_padding',
			[
				'label' => __('Text Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .portfolio-filters-list .portfolio-show-filters-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Background_Light::get_type(),
			[
				'name' => 'filter_buttons_hidden_background',
				'label' => __('Background Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .portfolio-filters-list .portfolio-show-filters-button',
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'filter_buttons_hidden_shadow',
				'label' => __('Shadow', 'thegem'),
				'selector' => '{{WRAPPER}} .portfolio-filters-list .portfolio-show-filters-button',
			]
		);

		$control->add_control(
			'filter_buttons_hidden_sidebar_header',
			[
				'label' => __('Hidden Sidebar', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'filters_style' => 'hidden',
				]
			]
		);

		$control->add_control(
			'filter_buttons_responsive_hidden_sidebar_header',
			[
				'label' => __('Responsive Mode - Hidden Sidebar', 'thegem'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'filters_style!' => 'hidden',
				]
			]
		);

		$control->add_group_control(Group_Control_Typography::get_type(),
			[
				'label' => __('Sidebar Title Typography', 'thegem'),
				'name' => 'filter_buttons_hidden_sidebar_title_typography',
				'selector' => '{{WRAPPER}} .portfolio-filters-list .portfolio-filters-area h2',
			]
		);

		$control->add_control(
			'filter_buttons_hidden_sidebar_title_color',
			[
				'label' => __('Sidebar Title Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio-filters-list .portfolio-filters-area h2' => 'color: {{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			'filter_buttons_hidden_sidebar_width',
			[
				'label' => __('Sidebar Width', 'thegem'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio-filters-list:is(.style-hidden, .style-sidebar-mobile, .style-standard-mobile) .portfolio-filters-outer .portfolio-filters-area' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Background_Light::get_type(),
			[
				'name' => 'filter_buttons_hidden_sidebar_background',
				'label' => __('Background Type', 'thegem'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .portfolio-filters-list:is(.style-hidden, .style-sidebar-mobile, .style-standard-mobile) .portfolio-filters-outer .portfolio-filters-area',
			]
		);

		$control->add_control(
			'filter_buttons_hidden_sidebar_overlay_color',
			[
				'label' => __('Overlay Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio-filters-list:is(.style-hidden, .style-sidebar-mobile, .style-standard-mobile) .portfolio-filters-outer' => 'background-color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'filter_buttons_hidden_sidebar_overlay_shadow',
				'label' => __('Shadow', 'thegem'),
				'selector' => '{{WRAPPER}} .portfolio-filters-list:is(.style-hidden, .style-sidebar-mobile, .style-standard-mobile) .portfolio-filters-outer .portfolio-filters-area',
			]
		);

		$control->add_control(
			'filter_buttons_hidden_sidebar_close_icon_color',
			[
				'label' => __('“Close” Icon Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .portfolio-filters-list .portfolio-close-filters' => 'color: {{VALUE}};',
				],
			]
		);

		$control->end_controls_section();
	}

	/**
	 * Apply Button Style
	 * @access protected
	 */
	protected function apply_button_style( $control ) {

		$control->start_controls_section(
			'apply_button_section',
			[
				'label' => __( 'Apply Filters Button Style', 'thegem' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'filtering_type' => 'button',
				]
			]
		);

		$control->add_responsive_control(
			'apply_button_stretch_full_width',
			[
				'label' => __('Stretch to Full Width', 'thegem'),
				'default' => 'yes',
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __('On', 'thegem'),
				'label_off' => __('Off', 'thegem'),
				'frontend_available' => true,
				'selectors' => [
					'{{WRAPPER}} .filters-apply-button' => 'flex: auto;',
					'{{WRAPPER}} .filters-apply-button .gem-button' => 'width: 100%',
				],
			]
		);

		$control->add_control(
			'apply_button_position',
			[
				'label' => __('Position (Vertical and Hidden)', 'thegem'),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => true,
				'options' => [
					'left' => [
						'title' => __('Left', 'thegem'),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __('Center', 'elementor'),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' => __('Right', 'thegem'),
						'icon' => 'eicon-h-align-right',
					],
				],
				'toggle' => false,
				'selectors' => [
					'{{WRAPPER}} .filters-apply-button' => 'text-align: {{VALUE}};',
				],
				'default' => 'left',
			]
		);

		$control->add_control(
			'apply_button_type',
			[
				'label' => __('Button Type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'default' => 'flat',
				'options' => [
					'flat' => __('Flat', 'thegem'),
					'outline' => __('Outline', 'thegem'),
				],
				'frontend_available' => true,
			]
		);

		$control->add_control(
			'apply_button_size',
			[
				'label' => __('Size', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'default' => 'small',
				'options' => [
					'tiny' => __('Tiny', 'thegem' ),
					'small' => __('Small', 'thegem' ),
					'medium' => __('Medium', 'thegem'),
					'large' => __('Large', 'thegem' ),
					'giant' => __('Giant', 'thegem' ),
				],
				'frontend_available' => true,
			]
		);

		$control->add_responsive_control(
			'apply_button_border_radius',
			[
				'label' => __('Border Radius', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .filters-apply-button .gem-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_control(
			'apply_button_border_type',
			[
				'label' => __('Border Type', 'thegem'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'options' => [
					'none'   => __('None', 'thegem'),
					'solid'  => __('Solid', 'thegem'),
					'double' => __('Double', 'thegem'),
					'dotted' => __('Dotted', 'thegem'),
					'dashed' => __('Dashed', 'thegem'),
				],
				'selectors' => [
					'{{WRAPPER}} .filters-apply-button .gem-button' => 'border-style: {{VALUE}};',
				],
				'frontend_available' => true,
			]
		);

		$control->add_control(
			'apply_button_border_width',
			[
				'label' => __('Border Width', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'rem', 'em' ],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .filters-apply-button .gem-button' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->add_responsive_control(
			'apply_button_text_padding',
			[
				'label' => __('Text Padding', 'thegem'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em'],
				'label_block' => true,
				'selectors' => [
					'{{WRAPPER}} .filters-apply-button .gem-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$control->start_controls_tabs( 'apply_button_tabs' );

		$control->start_controls_tab(
			'apply_button_tab_normal',
			[
				'label' => __( 'Normal', 'thegem' ),
			]
		);

		$control->add_control(
			'apply_button_text_color',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .filters-apply-button .gem-button' => 'color:{{VALUE}}; fill:{{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'apply_button_typography',
				'selector' => '{{WRAPPER}} .filters-apply-button .gem-button',
			]
		);

		$control->add_responsive_control(
			'apply_button_bg_color',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .filters-apply-button .gem-button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			'apply_button_border_color',
			[
				'label' => __( 'Border Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .filters-apply-button .gem-button' => 'border-color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'apply_button_shadow',
				'label' => __( 'Shadow', 'thegem' ),
				'selector' => '{{WRAPPER}} .filters-apply-button .gem-button',
			]
		);

		$control->end_controls_tab();

		$control->start_controls_tab(
			'apply_button_tab_hover',
			[
				'label' => __( 'Hover', 'thegem' ),
			]
		);

		$control->add_control(
			'apply_button_text_color_hover',
			[
				'label' => __('Text Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .filters-apply-button .gem-button:hover' => 'color:{{VALUE}}; fill:{{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'label' => __( 'Typography', 'thegem' ),
				'name' => 'apply_button_typography_hover',
				'selector' => '{{WRAPPER}} .filters-apply-button .gem-button:hover',
			]
		);

		$control->add_responsive_control(
			'apply_button_bg_color_hover',
			[
				'label' => __('Background Color', 'thegem'),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .filters-apply-button .gem-button:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$control->add_responsive_control(
			'apply_button_border_color_hover',
			[
				'label' => __( 'Border Color', 'thegem' ),
				'type' => Controls_Manager::COLOR,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .filters-apply-button .gem-button:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$control->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'apply_button_shadow_hover',
				'label' => __( 'Shadow', 'thegem' ),
				'selector' => '{{WRAPPER}} .filters-apply-button .gem-button:hover',
			]
		);

		$control->end_controls_tab();

		$control->end_controls_tabs();

		$control->end_controls_section();
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
		if ($settings['grid_filter'] == 'id') {
			$grid_uid = $settings['grid_id'];
			$grid_uid_url = $grid_uid . '-';
		} else {
			$grid_uid = $grid_uid_url = '';
		}

		parse_str($_SERVER['QUERY_STRING'], $url_params);

		$portfolios_filters_tax_url = $portfolios_filters_meta_url = [];
		$sale_only = $stock_only = false;
		$search_current = null;
		if (!empty($url_params)) {
			foreach ($url_params as $name => $value) {
				if (str_contains($name, $grid_uid_url . 'filter_tax_')) {
					$portfolios_filters_tax_url[str_replace($grid_uid_url . 'filter_', '', $name)] = explode(",", $value);
				} else if (str_contains($name, $grid_uid_url . 'category')) {
					$portfolios_filters_tax_url['tax_product_cat'] = explode(",", $value);
				} else if (str_contains($name, $grid_uid_url . 'filter_')) {
					$portfolios_filters_meta_url[str_replace($grid_uid_url . 'filter_', '', $name)] = explode(",", $value);
				} else if (str_contains($name, $grid_uid_url . 'status')) {
					$status_current = explode(",", $value);
					if (in_array('sale', $status_current)) {
						$sale_only = true;
					}
					if (in_array('stock', $status_current)) {
						$stock_only = true;
					}
				} else if (str_contains($name, $grid_uid_url . 's')) {
					$search_current = $value;
				}
			}
		}

		ob_start();
		$filter_attr = $settings['repeater_attributes'];
		$filter_attr_numeric = [];
		if (!empty($filter_attr)) {
			foreach ($filter_attr as $item) {
				$terms = false;
				if ($item['attribute_type'] == 'search') { ?>
					<div class="portfolio-filter-item display-type-dropdown">
						<?php if ($item['show_title'] == 'yes') { ?>
							<h4 class="name widget-title">
								<?php echo esc_html($item['attribute_title']); ?>
							</h4>
						<?php } ?>
						<form class="portfolio-search-filter<?php
						echo $item['live_search'] == 'yes' ? ' live-search' : '';
						echo $item['search_reset_filters'] == 'yes' ? ' reset-filters' : ''; ?>"
							role="search" action="">
							<div class="portfolio-search-filter-form">
								<input type="search"
										placeholder="<?php echo esc_attr($settings['filters_text_labels_search_text']); ?>"
										value="<?php echo esc_attr($search_current); ?>">
							</div>
							<div class="portfolio-search-filter-button"></div>
						</form>
					</div>
				<?php continue; }
			$attributes_url = $portfolios_filters_meta_url;
			if ($item['attribute_type'] == 'taxonomies') {
				if (empty($item['attribute_taxonomies'])) continue;
				$term_args = [
					'taxonomy' => $item['attribute_taxonomies'],
					'orderby' => $item['attribute_order_by'],
				];
				if ($item['attribute_taxonomies_hierarchy'] == 'yes') {
					$term_args['parent'] = 0;
				}
				$terms = get_terms($term_args);
				$attribute_name = 'tax_' . $item['attribute_taxonomies'];
				$attributes_url = $portfolios_filters_tax_url;
			} else if ($item['attribute_type'] == 'products_attributes') {
				$attribute_name = $item['attribute_name_products'];
				$terms = get_terms('pa_' . $attribute_name);
			} else if ($item['attribute_type'] == 'products_price') {
				$attribute_name = 'price';
				$terms = true;
			} else if ($item['attribute_type'] == 'products_status') {
				$attribute_name = 'status';
				$terms = true;
			} else {
				if ($item['attribute_type'] == 'details') {
					$attribute_name = $item['attribute_details'];
				} else if ($item['attribute_type'] == 'custom_fields') {
					$attribute_name = $item['attribute_custom_fields'];
				} else if ($item['attribute_type'] == 'manual_key') {
					$attribute_name = $item['manual_key_field'];
				} else {
					$attribute_name = $item['attribute_custom_fields_acf_' . $item['attribute_type']];
					$group_fields = class_exists( 'ACF' ) ? acf_get_fields($item['attribute_type']) : array();
					$found_key = array_search($attribute_name, array_column($group_fields, 'name'));
					$checkbox_field = get_field_object($group_fields[$found_key]['key']);
					if (isset($checkbox_field['choices'])) {
						$terms = $checkbox_field['choices'];
						if ($checkbox_field['type'] == 'checkbox') {
							$attribute_name .= '__check';
						}
					}
					$item['attribute_type'] = 'acf_fields';
				}
				if (empty($attribute_name)) continue;
				if (empty($terms)) {
					$terms = $this->get_post_type_meta_values($attribute_name);
				}
				$attribute_name = 'meta_' . $attribute_name;
			}
			if (!empty($terms) && !is_wp_error($terms)) {
				$is_dropdown = isset($item['attribute_display_type']) && $item['attribute_display_type'] == 'dropdown';
				if ($item['attribute_type'] == 'products_price' || ($item['attribute_type'] != 'taxonomies' && $item['attribute_type'] != 'products_attributes' && $item['attribute_meta_type'] == 'number')) {
					wp_enqueue_script('jquery-touch-punch');
					wp_enqueue_script('jquery-ui-slider');
					if ($item['attribute_type'] == 'products_price') {
						$price_range = thegem_extended_products_get_product_price_range();
						$min = $price_range['min'];
						$max = $price_range['max'];
					} else {
						$terms = array_map('floatval', $terms);
						$filter_attr_numeric[$attribute_name] = $item;
						$min = min($terms);
						$max = max($terms);
					} ?>
					<div class="portfolio-filter-item price<?php
					echo $is_dropdown ? ' display-type-dropdown open-dropdown-' . $item['attribute_display_dropdown_open'] : ''; ?>">
						<?php if (($item['show_title'] == 'yes' && !empty($item['attribute_title'])) || (!$is_dropdown && $settings['filters_style'] == 'standard')) { ?>
							<h4 class="name widget-title">
								<span class="widget-title-by">
									<?php echo esc_html($settings['filter_buttons_hidden_filter_by_text']); ?>
								</span>
								<?php echo esc_html($item['attribute_title']); ?>
								<span class="widget-title-arrow"></span>
							</h4>
						<?php } ?>
						<?php if ($is_dropdown) { ?>
							<div class="dropdown-selector">
								<div class="selector-title" data-selectable="1">
									<span class="name">
										<?php if ($item['show_title'] !== 'yes') { ?>
											<span class="slider-amount-text"><?php echo esc_html($item['attribute_title']); ?>: </span>
										<?php } ?>
										<span class="slider-amount-value"></span>
									</span>
									<span class="widget-title-arrow"></span>
								</div>
						<?php } ?>
								<div class="portfolio-filter-item-list">
									<div class="price-range-slider">
										<div class="slider-range"
											 data-min="<?php echo esc_attr($min); ?>"
											 data-max="<?php echo esc_attr($max); ?>"
											<?php if ($item['attribute_type'] == 'products_price') { ?>
												data-currency="<?php echo esc_attr(get_woocommerce_currency_symbol()); ?>"
												data-currency-position="<?php echo esc_attr(get_option('woocommerce_currency_pos')); ?>"
												data-thousand-separator="<?php echo esc_attr(get_option('woocommerce_price_thousand_sep')); ?>"
												data-decimal-separator="<?php echo esc_attr(get_option('woocommerce_price_decimal_sep')); ?>"
											<?php } else { ?>
												data-attr="<?php echo esc_attr($attribute_name); ?>"
												data-prefix="<?php echo esc_attr($item['attribute_price_format_prefix']); ?>"
												data-suffix="<?php echo esc_attr($item['attribute_price_format_suffix']); ?>"
												<?php if ($item['attribute_price_format'] != 'disabled') { ?>data-locale="<?php echo esc_attr($item['attribute_price_format'] == 'wp_locale' ? get_locale() : $item['attribute_price_format_locale']); ?>"<?php }
											} ?>></div>
										<div class="slider-amount">
											<span class="slider-amount-text"><?php echo esc_html($item['attribute_title']); ?>: </span>
											<span class="slider-amount-value"></span>
										</div>
									</div>
								</div>
						<?php if ($is_dropdown) { ?>
							</div>
						<?php } ?>
					</div>
				<?php } else if ($item['attribute_type'] == 'products_status') { ?>
					<div class="portfolio-filter-item status multiple<?php
					echo $is_dropdown ? ' display-type-dropdown open-dropdown-' . $item['attribute_display_dropdown_open'] : ''; ?>">
						<?php if (($item['show_title'] == 'yes' && !empty($item['attribute_title'])) || (!$is_dropdown && $settings['filters_style'] == 'standard')) { ?>
							<h4 class="name widget-title">
								<span class="widget-title-by">
									<?php echo esc_html($settings['filter_buttons_hidden_filter_by_text']); ?>
								</span>
								<?php echo esc_html($item['attribute_title']); ?>
								<span class="widget-title-arrow"></span>
							</h4>
						<?php } ?>
						<?php if ($is_dropdown) { ?>
							<div class="dropdown-selector">
								<div class="selector-title" data-selectable="1">
									<?php $title = $item['show_title'] !== 'yes' ? $item['attribute_title'] : str_replace('%ATTR%', $item['attribute_title'], $settings['filters_text_labels_all_text']); ?>
									<span class="name" data-title="<?php echo esc_attr($title); ?>">
										<?php if (!$sale_only && !$stock_only) { ?>
											<span data-filter="*"><?php echo esc_html($title); ?></span>
										<?php } else {
											if ($sale_only) {
												echo '<span data-filter="sale">' . esc_html($item['filter_by_status_sale_text']) . '<span class="separator">, </span></span>';
											}
											if ($stock_only) {
												echo '<span data-filter="stock">' . esc_html($item['filter_by_status_stock_text']) . '<span class="separator">, </span></span>';
											}
										} ?>
									</span>
									<span class="widget-title-arrow"></span>
								</div>
						<?php } ?>
								<div class="portfolio-filter-item-list">
							<ul>
								<li>
									<a href="#" data-filter="*"
										data-filter-type="status"
										class="all <?php echo ($sale_only || $stock_only) ? '' : 'active'; ?>"
										rel="nofollow"><span class="title"><?php echo esc_html(str_replace('%ATTR%', $item['attribute_title'], $settings['filters_text_labels_all_text'])); ?></span>
									</a>
								</li>
								<?php if ($item['filter_by_status_sale'] == 'yes') { ?>
									<li>
										<a href="#"
											data-filter-type="status"
											data-filter="sale"
											data-filter-id="sale"
											class="<?php echo $sale_only ? 'active' : ''; ?>"
											rel="nofollow">
											<span class="title"><?php echo esc_html($item['filter_by_status_sale_text']); ?></span>
										</a>
									</li>
								<?php }
								if ($item['filter_by_status_stock'] == 'yes') { ?>
									<li>
										<a href="#"
											data-filter-type="status"
											data-filter="stock"
											data-filter-id="stock"
											class="<?php echo $stock_only ? 'active' : ''; ?>"
											rel="nofollow">
											<span class="title"><?php echo esc_html($item['filter_by_status_stock_text']); ?></span>
										</a>
									</li>
								<?php } ?>
							</ul>
						</div>
						<?php if ($is_dropdown) { ?>
							</div>
						<?php } ?>
					</div>
				<?php } else {
					$attribute_type_class = '';
					$attribute_data = false;
					if ($item['attribute_type'] == 'products_attributes') {
						$attribute_data = wc_get_attribute(wc_attribute_taxonomy_id_by_name('pa_' . $attribute_name));
						$attribute_type_class = $attribute_data->type == 'color' || $attribute_data->type == 'label' || $attribute_data->type == 'image' ? ' attribute-type-' . $attribute_data->type : '';
					}
					if ( $attribute_name == 'tax_product_cat') {
						$attribute_type = 'category';
					} else {
						$attribute_type = $item['attribute_type'];
					}
					$keys = array_keys($terms);
					$simple_arr = $keys == array_keys($keys);
					if ($item['attribute_order_by'] == 'name') {
						if ($simple_arr) {
							sort($terms);
						} else {
							asort($terms);
						}
					} ?>
					<div class="portfolio-filter-item attribute <?php
					echo esc_attr($attribute_name);
					echo strtolower($item['attribute_query_type']) == 'and' ? ' multiple' : ' single';
					echo $is_dropdown ? ' display-type-dropdown open-dropdown-' . $item['attribute_display_dropdown_open'] : '';
					echo esc_attr($attribute_type_class); ?>">
						<?php if (($item['show_title'] == 'yes' && !empty($item['attribute_title'])) || (!$is_dropdown && $settings['filters_style'] == 'standard')) { ?>
							<h4 class="name widget-title">
								<span class="widget-title-by">
									<?php echo esc_html($settings['filter_buttons_hidden_filter_by_text']); ?>
								</span>
								<?php echo esc_html($item['attribute_title']); ?>
								<span class="widget-title-arrow"></span>
							</h4>
						<?php } ?>
						<?php if ($is_dropdown) { ?>
							<div class="dropdown-selector">
								<div class="selector-title" data-selectable="1">
									<?php $title = $item['show_title'] !== 'yes' ? $item['attribute_title'] : str_replace('%ATTR%', $item['attribute_title'], $settings['filters_text_labels_all_text']); ?>
									<span class="name" data-title="<?php echo esc_attr($title); ?>">
										<?php if (!isset($attributes_url[$attribute_name])) { ?>
											<span data-filter="*"><?php echo esc_html($title); ?></span>
										<?php } else {
											foreach ($terms as $key => $term) {
												$term_slug = isset($term->slug) ? $term->slug : ($simple_arr ? $term : $key);
												$term_title = isset($term->name) ? $term->name : $term;
												if (in_array($term_slug, $attributes_url[$attribute_name])) {
													echo '<span data-filter="' . $term_slug . '">' . $term_title . '<span class="separator">, </span></span>';
												}
											}
										} ?>
									</span>
									<span class="widget-title-arrow"></span>
								</div>
						<?php } ?>
								<div class="portfolio-filter-item-list">
									<ul>
										<li<?php if ($attribute_data && ($attribute_data->type == 'color' || $attribute_data->type == 'label' || $attribute_data->type == 'image')) {
											echo ' style="display: none;"';
										} ?>>
											<a href="#"
												data-filter-type="<?php echo esc_attr($attribute_type); ?>"
												data-attr="<?php echo esc_attr($attribute_name); ?>"
												data-filter="*"
												class="all <?php echo !isset($attributes_url[$attribute_name]) ? 'active' : ''; ?>"
												rel="nofollow">
												<?php if ($item['attribute_query_type'] == 'or') {
													echo '<span class="check"></span>';
												} ?>
												<span class="title"><?php echo esc_html(str_replace('%ATTR%', $item['attribute_title'], $settings['filters_text_labels_all_text'])); ?></span>
											</a>
										</li>
										<?php $this->thegem_print_attributes_list($terms, $item, $attribute_name, $attributes_url, $attribute_data); ?>
									</ul>
								</div>
						<?php if ($is_dropdown) { ?>
							</div>
						<?php } ?>
					</div>
				<?php }
			}
		}
		}
		$filters_list = ob_get_clean();
		$style_uid = substr(md5(rand()), 0, 7);

		if (!empty($filters_list) || $settings['filtering_type'] == 'button') { ?>
			<div id="style-<?php echo esc_attr($style_uid); ?>" class="extended-posts-filter portfolio-top-panel filter-type-extended<?php
				echo $settings['filters_style'] == 'sidebar' ? ' sidebar-filter' : '';
				echo $settings['filter_buttons_hidden_sidebar_separator_enable'] !== 'yes' ? ' hide-separator' : ''; ?>"
				 data-portfolio-uid="<?php echo esc_attr($grid_uid); ?>"
				 <?php if (!empty($settings['grid_url']['url'])) {?>
				 	data-url="<?php echo esc_attr($settings['grid_url']['url']); ?>"
				 <?php } ?>>
				<div class="portfolio-top-panel-row">
					<div class="portfolio-top-panel-left <?php echo esc_attr($settings['filter_buttons_standard_alignment']); ?>">
						<?php if (!empty($filter_attr) || $settings['filtering_type'] == 'button') { ?>
							<div class="portfolio-filters-list style-<?php echo esc_attr($settings['filters_style']);
							echo ' filtering-type-' . $settings['filtering_type'];
							echo $settings['filters_scroll_top'] == 'yes' ? ' scroll-top' : '';
							echo $settings['filters_style_mobile'] !== 'hidden' ? ' prevent-hidden-mobile' : ''; ?>"
							data-breakpoint="<?php echo $settings['filters_style_mobile'] == 'hidden' ? esc_attr($settings['hidden_breakpoint']['size']) : ''; ?>">
								<?php if ($settings['filters_style'] == 'hidden' || $settings['filters_style_mobile'] == 'hidden') { ?>
									<div class="portfolio-show-filters-button with-icon">
										<?php echo esc_html($settings['filter_buttons_hidden_show_text']); ?>
										<?php if ($settings['filter_buttons_hidden_show_icon'] == 'yes') { ?>
											<span class="portfolio-show-filters-button-icon"></span>
										<?php } ?>
									</div>
								<?php } ?>

								<div class="portfolio-filters-outer without-padding">
									<div class="portfolio-filters-area">
										<div class="portfolio-filters-area-scrollable">
											<?php if ($settings['filters_style'] == 'hidden' || $settings['filters_style_mobile'] == 'hidden') { ?>
												<h2 class="light"><?php echo esc_html($settings['filter_buttons_hidden_sidebar_title']); ?></h2>
											<?php } ?>
											<div class="widget-area-wrap">
												<div class="portfolio-filters-extended widget-area">
													<?php echo $filters_list;

													if ($settings['filtering_type'] == 'button') { ?>
														<div class="portfolio-filter-item filters-apply-button">
															<a href="#" id="<?php echo esc_attr($settings['apply_button_id']); ?>"
															class="gem-button gem-button-size-<?php echo $settings['apply_button_size']; ?>
															gem-button-style-<?php echo $settings['apply_button_type']; ?>
															<?php echo esc_attr($settings['apply_button_class']); ?>">
																<span class="gem-inner-wrapper-btn">
																	<span class="gem-text-button">
																		<?php echo esc_html($settings['apply_button_text']); ?>
																	</span>
																</span>
															</a>
														</div>
													<?php } ?>
												</div>
											</div>
										</div>
									</div>

									<?php if ($settings['filters_style'] == 'hidden' || $settings['filters_style_mobile'] == 'hidden') { ?>
										<div class="portfolio-close-filters"></div>
									<?php } ?>
								</div>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
			<script>
				!function(s){function a(a,e){s(window).outerWidth()<e?a.hasClass("style-standard")?a.addClass("style-standard-mobile"):a.hasClass("style-sidebar")&&a.addClass("style-sidebar-mobile"):a.hasClass("style-standard")?a.removeClass("style-standard-mobile"):a.hasClass("style-sidebar")&&a.removeClass("style-sidebar-mobile")}s("#style-<?php echo esc_attr($style_uid); ?> .portfolio-filters-list").each(function(){if(!s(this).hasClass("style-hidden")&&!s(this).hasClass("prevent-hidden-mobile")){let e=s(this),t=e.data("breakpoint")?e.data("breakpoint"):992;a(s(this),t),s(window).on("resize",function(){a(e,t)})}})}(jQuery);
			</script>
			<?php if (is_admin() && Plugin::$instance->editor->is_edit_mode()): ?>
			<script type="text/javascript">
				(function ($) {
					setTimeout(function () {
						$('.elementor-element-<?php echo $this->get_id(); ?> .extended-posts-filter').initPortfolioFiltersList();
					}, 1000);
				})(jQuery);

			</script>
		<?php endif;
		}
	}
}

Plugin::instance()->widgets_manager->register(new TheGem_Extended_Filter());
