<?php
/*
Plugin Name: TheGem Theme Elements (for Elementor)
Plugin URI: http://codex-themes.com/thegem/
Description: Extended features and functionality for TheGem theme, including rich collection of page elements for Elementor Page Builder.
Author: Codex Themes
Version: 5.10.3.1
Author URI: http://codex-themes.com/thegem/
TextDomain: thegem
DomainPath: /languages
Elementor tested up to: 3.28.4
*/

if ( ! defined( 'THEGEM_THEME_URI' ) ) {
	define( 'THEGEM_THEME_URI', get_template_directory_uri() );
}
if ( ! defined( 'THEGEM_THEME_VERSION' ) ) {
	define( 'THEGEM_THEME_VERSION', wp_get_theme(wp_get_theme()->get('Template'))->get('Version') );
}
if ( ! defined( 'THEGEM_PAGE_EDITOR' ) ) {
	define( 'THEGEM_PAGE_EDITOR', 'elementor' );
}
if ( ! defined( 'THEGEM_ELEMENTS_ELEMENTOR' ) ) {
	define( 'THEGEM_ELEMENTS_ELEMENTOR', 1 );
}

add_action( 'plugins_loaded', 'thegem_load_textdomain' );
function thegem_load_textdomain() {
	load_plugin_textdomain( 'thegem', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

if(!function_exists('thegem_is_plugin_active')) {
	function thegem_is_plugin_active($plugin) {
		include_once(ABSPATH . 'wp-admin/includes/plugin.php');
		return is_plugin_active($plugin);
	}
}

if(!function_exists('thegem_user_icons_info_link')) {
function thegem_user_icons_info_link($pack = '') {
	return esc_url(apply_filters('thegem_user_icons_info_link', THEGEM_THEME_URI.'/fonts/icons-list-'.$pack.'.html', $pack));
}
}

/* Get theme option*/
if(!function_exists('thegem_get_current_language')) {
function thegem_get_current_language() {
	static $result;

	if (isset($result)) {
		return $result;
	}

	if(defined('ICL_SITEPRESS_VERSION') && defined('ICL_LANGUAGE_CODE') && ICL_LANGUAGE_CODE) {
		$result = ICL_LANGUAGE_CODE;
		return $result;
	}
	if(defined( 'POLYLANG_VERSION' ) && function_exists('pll_current_language') && pll_current_language('slug')) {
		$result = pll_current_language('slug');
		return $result;
	}

	$result = false;
	return $result;
}
}

if(!function_exists('thegem_get_default_language')) {
function thegem_get_default_language() {
	static $result;

	if (isset($result)) {
		return $result;
	}

	if(defined('ICL_SITEPRESS_VERSION')) {
		global $sitepress;
		if(is_object($sitepress) && $sitepress->get_default_language()) {
			$result = $sitepress->get_default_language();
			return $result;
		}
	}
	if(defined( 'POLYLANG_VERSION' ) && pll_default_language('slug')) {
		$result = pll_default_language('slug');
		return $result;
	}

	$result = false;
	return $result;
}
}

function thegem_home_url( $path = '', $scheme = null ) {
	return home_url( $path, $scheme );
}

function thegem_pll_home_url_white_list( $white_list ) {
	$white_list[] = array( 'function' => 'thegem_home_url' );
	return $white_list;
}
add_filter( 'pll_home_url_white_list', 'thegem_pll_home_url_white_list' );

if (!function_exists('thegem_lazy_loading_enqueue')) {
	function thegem_lazy_loading_enqueue() {
		wp_enqueue_script('thegem-lazy-loading');
		wp_enqueue_style('thegem-lazy-loading-animations');
	}
}

if (!function_exists('thegem_translated_options')) {
	function thegem_translated_options() {
		return apply_filters('thegem_translated_options', array(
			'footer_html', 'top_area_button_text', 'top_area_button_link', 'contacts_address', 'contacts_phone', 'contacts_fax', 'contacts_email', 'contacts_website', 'top_area_contacts_address', 'top_area_contacts_phone', 'top_area_contacts_fax', 'top_area_contacts_email', 'top_area_contacts_website', 'custom_footer', 'header_builder', 'header_builder_sticky', 'post_builder_template', 'portfolio_builder_template', 'blog_archive_builder_template', 'blog_archive_item_builder_template', 'search_builder_template', 'search_item_builder_template', 'logo', 'logo_light', 'small_logo', 'small_logo_light', 'portfolio_archive_page',
			'product_archive_quick_view_text', 'product_archive_cart_button_text', 'product_archive_select_options_button_text', 'product_archive_more_button_text', 'product_archive_filter_by_categories_title', 'product_archive_filter_by_price_title', 'product_archive_filter_by_status_title', 'product_archive_filter_by_status_sale_text', 'product_archive_filter_by_status_stock_text', 'product_archive_filters_text_labels_all_text', 'product_archive_filters_text_labels_clear_text', 'product_archive_filters_text_labels_search_text', 'product_archive_filter_buttons_hidden_show_text', 'product_archive_filter_buttons_hidden_sidebar_title', 'product_archive_filter_buttons_hidden_filter_by_text', 'product_archive_added_cart_text', 'product_archive_added_wishlist_text', 'product_archive_removed_wishlist_text', 'product_archive_view_cart_button_text', 'product_archive_checkout_button_text', 'product_archive_view_wishlist_button_text', 'product_archive_not_found_text',
			'product_page_desc_review_description_title', 'product_page_desc_review_additional_info_title', 'product_page_desc_review_reviews_title', 'product_page_button_add_to_cart_text', 'product_page_button_clear_attributes_text', 'product_page_elements_reviews_text', 'product_page_elements_sku_title', 'product_page_elements_categories_title', 'product_page_elements_tags_title', 'product_page_elements_share_title', 'product_page_elements_upsell_title', 'product_page_elements_related_title',
			'cart_empty_title', 'cart_empty_text', 'cart_elements_cross_sells_title', 'checkout_thank_you_extra', 'product_builder_template', 'product_archive_builder_template', 'cart_builder_template', 'cart_empty_builder_template', 'checkout_builder_template', 'search_layout_mixed_grids_title', 'search_layout_mixed_grids_show_all', 'product_archive_filter_by_attribute_data', 'size_guide_text', 'mini_cart_sidebar_title', 'mini_cart_sidebar_infotext', 'product_page_additional_tabs_data', 'blog_layout_caption_read_more_text', 'website_search_page_title', 'website_search_page_excerpt', 'mobile_menu_show_this_page_text', 'mobile_menu_back_text'
		));
	}
}

if(!function_exists('thegem_get_option')) {
function thegem_get_option($name, $default = false, $ml_full = false, $clearCache = false) {
	static $ref_options;
	static $cache = [];

	if ($clearCache) {
		$ref_options = null;
		$cache = [];
	}
	$cacheKey = $name.'_'.$default.'_'.$ml_full;

	if (isset($cache[$cacheKey])) {
		return $cache[$cacheKey];
	}

	if (!isset($ref_options)) {
		$ref_options = get_option('thegem_theme_options');
	}
	$options = $ref_options;

	if(isset($options[$name])) {
		$ml_options = thegem_translated_options();
		if(in_array($name, $ml_options) && is_array($options[$name]) && !$ml_full) {
			if(thegem_get_current_language()) {
				if(isset($options[$name][thegem_get_current_language()])) {
					$options[$name] = $options[$name][thegem_get_current_language()];
				} elseif(thegem_get_default_language() && isset($options[$name][thegem_get_default_language()])) {
					$options[$name] = $options[$name][thegem_get_default_language()];
				} else {
					$options[$name] = '';
				}
			}else {
				$options[$name] = reset($options[$name]);
			}
		}
		$result = apply_filters('thegem_option_'.$name, $options[$name]);
		$cache[$cacheKey] = $result;
		return $result;
	}
	$result = apply_filters('thegem_option_'.$name, $default);
	$cache[$cacheKey] = $result;
	return $result;
}
}

/* USER ICON PACK */

if(!function_exists('thegem_icon_userpack_enabled')) {
function thegem_icon_userpack_enabled() {
	return apply_filters('thegem_icon_userpack_enabled', false);
}
}

if(!function_exists('thegem_icon_packs_select_array')) {
function thegem_icon_packs_select_array() {
	$packs = array('elegant' => __('Elegant', 'thegem'), 'material' => __('Material Design', 'thegem'), 'fontawesome' => __('FontAwesome', 'thegem'));
	if(thegem_icon_userpack_enabled()) {
		$packs['userpack'] = __('UserPack', 'thegem');
	}
	return $packs;
}
}

if(!function_exists('thegem_icon_packs_infos')) {
function thegem_icon_packs_infos() {
	ob_start();
?>
<?php _e('Enter icon code', 'thegem'); ?>.
<a class="gem-icon-info gem-icon-info-elegant" href="<?php echo thegem_user_icons_info_link('elegant'); ?>" onclick="tb_show('<?php _e('Icons info', 'thegem'); ?>', this.href+'?TB_iframe=true'); return false;"><?php _e('Show Elegant Icon Codes', 'thegem'); ?></a>
<a class="gem-icon-info gem-icon-info-material" href="<?php echo thegem_user_icons_info_link('material'); ?>" onclick="tb_show('<?php _e('Icons info', 'thegem'); ?>', this.href+'?TB_iframe=true'); return false;"><?php _e('Show Material Design Icon Codes', 'thegem'); ?></a>
<a class="gem-icon-info gem-icon-info-fontawesome" href="<?php echo thegem_user_icons_info_link('fontawesome'); ?>" onclick="tb_show('<?php _e('Icons info', 'thegem'); ?>', this.href+'?TB_iframe=true'); return false;"><?php _e('Show FontAwesome Icon Codes', 'thegem'); ?></a>
<?php if(thegem_icon_userpack_enabled()) : ?>
<a class="gem-icon-info gem-icon-info-userpack" href="<?php echo thegem_user_icons_info_link('userpack'); ?>" onclick="tb_show('<?php _e('Icons info', 'thegem'); ?>', this.href+'?TB_iframe=true'); return false;"><?php _e('Show UserPack Icon Codes', 'thegem'); ?></a>
<?php endif; ?>
<?php
	return ob_get_clean();
}
}


/* META BOXES */

if(!function_exists('thegem_print_select_input')) {
function thegem_print_select_input($values = array(), $value = '', $name = '', $id = '') {
	if(!is_array($values)) {
		$values = array();
	}
?>
	<select name="<?php echo esc_attr($name) ?>" id="<?php echo esc_attr($id); ?>" class="thegem-combobox">
		<?php foreach($values as $key => $title) : ?>
			<option value="<?php echo esc_attr($key); ?>" <?php selected($key, $value); ?>><?php echo esc_html($title); ?></option>
		<?php endforeach; ?>
	</select>
<?php
}
}

if(!function_exists('thegem_print_checkboxes')) {
function thegem_print_checkboxes($values = array(), $value = array(), $name = '', $id_prefix = '', $after = '') {
	if(!is_array($values)) {
		$values = array();
	}
	if(!is_array($value)) {
		$value = array();
	}
?>
	<?php foreach($values as $key => $title) : ?>
		<input name="<?php echo esc_attr($name); ?>" type="checkbox" id="<?php echo esc_attr($id_prefix.'-'.$key); ?>" value="<?php echo esc_attr($key); ?>" <?php checked(in_array($key, $value), 1); ?> />
		<label for="<?php echo esc_attr($id_prefix.'-'.$key); ?>"><?php echo esc_html($title); ?></label>
		<?php echo $after; ?>
	<?php endforeach; ?>
<?php
}
}

if(!function_exists('thegem_add_srcset_rule')) {
	function thegem_add_srcset_rule(&$srcset, $condition, $size, $id=false) {
		if (!$id) {
			$id = get_post_thumbnail_id();
		}
		$im = thegem_generate_thumbnail_src($id, $size);
		$srcset[$condition] = $im[0];
	}
}

if(!function_exists('thegem_srcset_generate_urls')) {
	function thegem_srcset_generate_urls($attachment_id, $srcset) {
		$result = array();
		$thegem_sizes = array_keys(thegem_image_sizes());
		foreach ($srcset as $condition => $size) {
			if (!in_array($size, $thegem_sizes)) {
				continue;
			}
			if(!thegem_get_option('retina_thumbnails_enabled') && $condition !== '1x') {
				continue;
			}
			$im = thegem_generate_thumbnail_src($attachment_id, $size);
			if ($im) {
				$result[$condition] = esc_url($im[0]);
			}
		}
		return $result;
	}
}

if(!function_exists('thegem_srcset_list_to_string')) {
	function thegem_srcset_list_to_string($srcset) {
		if (count($srcset) == 0) {
			return '';
		}
		$srcset_condtions = array();
		foreach ($srcset as $condition => $url) {
			$srcset_condtions[] = $url . ' ' . $condition;
		}
		return implode(', ', $srcset_condtions);
	}
}

if(!function_exists('thegem_generate_picture_sources')) {
	function thegem_generate_picture_sources($attachment_id, $sources) {
		if (!$sources) {
			return '';
		}
		?>
		<?php foreach ($sources as $source): ?>
			<?php
				if(!thegem_get_option('mobile_thumbnails_enabled') && !empty($source['media'])) {
					continue;
				}
				$srcset = thegem_srcset_generate_urls($attachment_id, $source['srcset']);
				if (!$srcset) {
					continue;
				}
			?>
			<source srcset="<?php echo thegem_srcset_list_to_string($srcset); ?>" <?php if(!empty($source['media'])): ?>media="<?php echo esc_attr($source['media']); ?>"<?php endif; ?> <?php if(!empty($source['type'])): ?>type="<?php echo esc_attr($source['type']); ?>"<?php endif; ?> sizes="<?php echo !empty($source['sizes']) ? esc_attr($source['sizes']) : '100vw'; ?>">
		<?php endforeach; ?>
		<?php
	}
}

if(!function_exists('thegem_generate_picture')) {
	function thegem_generate_picture($attachment_id, $default_size, $sources=array(), $attrs=array(), $return_info=false) {
		if (!$attachment_id || (!in_array($default_size, ['full', 'woocommerce_thumbnail', 'woocommerce_single', 'thumbnail', 'medium', 'medium_large', 'large', '1536x1536', '2048x2048']) && !in_array($default_size, array_keys(thegem_image_sizes())))) {
			return '';
		}
		$default_image = thegem_generate_thumbnail_src($attachment_id, $default_size);
		if (!$default_image) {
			return '';
		}
		list($src, $width, $height) = $default_image;
		$hwstring = image_hwstring($width, $height);

		$default_attrs = array('class' => "attachment-$default_size");
		if (empty($attrs['alt'])) {
			$attachment = get_post($attachment_id);
			$attrs['alt'] = trim(strip_tags(get_post_meta($attachment_id, '_wp_attachment_image_alt', true)));
			if ($attachment) {
				if(empty($default_attr['alt']))
					$attrs['alt'] = trim(strip_tags($attachment->post_excerpt));
				if(empty($default_attr['alt']))
					$attrs['alt'] = trim(strip_tags($attachment->post_title));
			}
		}

		$attrs = wp_parse_args($attrs, $default_attrs);
		$attrs = array_map('esc_attr', $attrs);
		$attrs_set = array();
		foreach ($attrs as $attr_key => $attr_value) {
			$attrs_set[] = $attr_key . '="' . $attr_value . '"';
		}
		?>
		<picture>
			<?php thegem_generate_picture_sources($attachment_id, $sources); ?>
			<img src="<?php echo $src; ?>" <?php echo $hwstring; ?> <?php echo implode(' ', $attrs_set); ?> />
		</picture>
		<?php
		if ($return_info) {
			return array(
				'default' => $default_image
			);
		}
	}
}

add_filter('wp_lazy_loading_enabled', function() { return false; } );
add_filter('pre_wp_get_loading_optimization_attributes', function() { return array(); } );

/*if(!function_exists('thegem_check_array_value')) {
function thegem_check_array_value($array = array(), $value = '', $default = '') {
	if(in_array($value, $array)) {
		return $value;
	}
	return $default;
}
}*/

if(!function_exists('thegem_templates_element_design_options')) {
	function thegem_templates_element_design_options($uniqid, $params) {
		$custom_css = '';
		$disable = 'flex_hide_element';
		$absolute = 'flex_absolute';
		$order = 'flex_sort_order';
		$horizontal = 'flex_horizontal_align';
		$vertical = 'flex_vertical_align';
		$padding = 'flex_padding';
		$margin = 'flex_margin';
		$gaps_dir = array('top', 'bottom', 'left', 'right');

		// Design options visibility
		if (isset( $params[$disable]) && $params[$disable] == '1') {
			$custom_css .= $uniqid . ' {display: none!important;}';
		}
		if (isset( $params[$disable.'_tablet']) && $params[$disable.'_tablet'] == '1') {
			$custom_css .= '@media screen and (max-width: 1023px) {' . $uniqid . ' {display: none!important;}}';
		} else {
			$custom_css .= '@media screen and (max-width: 1023px) {' . $uniqid . ' {display: block!important;}}';
		}
		if (isset( $params[$disable.'_mobile']) && $params[$disable.'_mobile'] == '1') {
			$custom_css .= '@media screen and (max-width: 767px) {' . $uniqid . ' {display: none!important;}}';
		} else {
			$custom_css .= '@media screen and (max-width: 767px) {' . $uniqid . ' {display: block!important;}}';
		}

		// Design options absolute
		$is_desktop_absolute = $is_tablet_absolute = $is_mobile_absolute = false;
		if (isset($params[$absolute]) && $params[$absolute] == '1') {
			$is_desktop_absolute = true;
			$custom_css .= $uniqid . ' {position: absolute !important;}';
		}
		if (isset($params[$absolute.'_tablet']) && $params[$absolute.'_tablet'] == '1') {
			$is_tablet_absolute = true;
			$custom_css .= '@media screen and (max-width: 1023px) {' . $uniqid . ' {position: absolute !important;}}';
		} else {
			$is_tablet_absolute = false;
			$custom_css .= '@media screen and (max-width: 1023px) {' . $uniqid . ' {position: relative !important; left:auto; right:auto; top:auto; bottom:auto; transform: none;}}';
		}
		if (isset($params[$absolute.'_mobile']) && $params[$absolute.'_mobile'] == '1') {
			$is_mobile_absolute = true;
			$custom_css .= '@media screen and (max-width: 767px) {' . $uniqid . ' {position: absolute !important;}}';
		} else {
			$is_mobile_absolute = false;
			$custom_css .= '@media screen and (max-width: 767px) {' . $uniqid . ' {position: relative !important; left:auto; right:auto; top:auto; bottom:auto; transform: none;}}';
		}

		// Design options order
		if (isset($params[$order]) && !empty($params[$order])) {
			$result = str_replace(' ', '', $params[$order]);
			$custom_css .= $uniqid . ' {order: ' . $result . ' !important;}';
		}
		if (isset($params[$order.'_tablet']) && !empty($params[$order.'_tablet'])) {
			$result = str_replace(' ', '', $params[$order.'_tablet']);
			$custom_css .= '@media screen and (max-width: 1023px) {' . $uniqid . ' {order: ' . $result . ' !important;}}';
		}
		if (isset($params[$order.'_mobile']) && !empty($params[$order.'_mobile'])) {
			$result = str_replace(' ', '', $params[$order.'_mobile']);
			$custom_css .= '@media screen and (max-width: 767px) {' . $uniqid . ' {order: ' . $result . ' !important;}}';
		}

		// Design options horizontal align
		if (isset($params[$horizontal]) && !empty($params[$horizontal])) {
			switch ($params[$horizontal]) {
				case 'left':
					$result = $is_desktop_absolute ? 'left: 0; transform: none; right: auto;' : 'margin: 0; margin-right: auto;';
					break;
				case 'center':
					$result = $is_desktop_absolute ? 'left: 50%; transform: translateX(-50%); right: auto;' : 'margin: 0 auto;';
					break;
				case 'right':
					$result = $is_desktop_absolute ? 'right: 0; transform: none; left: auto;' : 'margin: 0; margin-left: auto;';
					break;
				default: $result = 'margin: 0;';
			}
			$custom_css .= $uniqid . ' {' . $result . '}';
		}
		if (isset($params[$horizontal.'_tablet']) && !empty($params[$horizontal.'_tablet'])) {
			switch ($params[$horizontal.'_tablet']) {
				case 'unset':
					$result = $is_tablet_absolute ? 'left:auto; right:auto; transform: none;' : 'margin: 0; left:auto; right:auto; transform: none;';
					break;
				case 'left':
					$result = $is_tablet_absolute ? 'left: 0; transform: none; right: auto;' : 'margin: 0; margin-right: auto;';
					break;
				case 'center':
					$result = $is_tablet_absolute ? 'left: 50%; transform: translateX(-50%); right: auto;' : 'margin: 0 auto;';
					break;
				case 'right':
					$result = $is_tablet_absolute ? 'right: 0; transform: none; left: auto;' : 'margin: 0; margin-left: auto;';
					break;
				default: $result = 'margin: 0;';
			}
			$custom_css .= '@media screen and (max-width: 1023px) {' . $uniqid . ' {' . $result . '}}';
		}
		if (isset($params[$horizontal.'_mobile']) && !empty($params[$horizontal.'_mobile'])) {
			switch ($params[$horizontal.'_mobile']) {
				case 'unset':
					$result = $is_mobile_absolute ? 'left:auto; right:auto; transform: none;' : 'margin: 0; left:auto; right:auto; transform: none;';
					break;
				case 'left':
					$result = $is_mobile_absolute ? 'left: 0; transform: none; right: auto;' : 'margin: 0; margin-right: auto;';
					break;
				case 'center':
					$result = $is_mobile_absolute ? 'left: 50%; transform: translateX(-50%); right: auto;' : 'margin: 0 auto;';
					break;
				case 'right':
					$result = $is_mobile_absolute ? 'right: 0; transform: none; left: auto;' : 'margin: 0; margin-left: auto;';
					break;
				default: $result = 'margin: 0;';
			}
			$custom_css .= '@media screen and (max-width: 767px) {' . $uniqid . ' {' . $result . '}}';
		}

		// Design options vertical align
		if (isset($params[$vertical])) {
			switch ($params[$vertical]) {
				case 'top':
					$result = $is_desktop_absolute ? 'top: 0; transform: none; bottom: auto;' : 'align-self: flex-start;';
					break;
				case 'center':
					$result = $is_desktop_absolute ? 'top: 50%; transform: translateY(-50%); bottom: auto;' : 'align-self: center;';
					break;
				case 'bottom':
					$result = $is_desktop_absolute ? 'bottom: 0; transform: none; top: auto;' : 'align-self: flex-end;';
					break;
				default:
					$result = 'align-self: center;';
			}
			$custom_css .= $uniqid . ' {' . $result . '}';
		}
		if (isset($params[$vertical.'_tablet']) && !empty($params[$vertical.'_tablet'])) {
			switch ($params[$vertical.'_tablet']) {
				case 'unset':
					$result = $is_tablet_absolute ? 'top:auto; bottom:auto; transform: none;' : 'align-self: center; top:auto; bottom:auto; transform: none;';
					break;
				case 'top':
					$result = $is_tablet_absolute ? 'top: 0; transform: none; bottom: auto;' : 'align-self: flex-start;';
					break;
				case 'center':
					$result = $is_tablet_absolute ? 'top: 50%; transform: translateY(-50%); bottom: auto;' : 'align-self: center;';
					break;
				case 'bottom':
					$result = $is_tablet_absolute ? 'bottom: 0; transform: none; top: auto;' : 'align-self: flex-end;';
					break;
				default:
					$result = 'align-self: center;';
			}
			$custom_css .= '@media screen and (max-width: 1023px) {' . $uniqid . ' {' . $result . '}}';
		}
		if (isset($params[$vertical.'_mobile']) && !empty($params[$vertical.'_mobile'])) {
			switch ($params[$vertical.'_mobile']) {
				case 'unset':
					$result = $is_mobile_absolute ? 'top:auto; bottom:auto; transform: none;' : 'align-self: center; top:auto; bottom:auto; transform: none;';
					break;
				case 'top':
					$result = $is_mobile_absolute ? 'top: 0; transform: none; bottom: auto;' : 'align-self: flex-start;';
					break;
				case 'center':
					$result = $is_mobile_absolute ? 'top: 50%; transform: translateY(-50%); bottom: auto;' : 'align-self: center;';
					break;
				case 'bottom':
					$result = $is_mobile_absolute ? 'bottom: 0; transform: none; top: auto;' : 'align-self: flex-end;';
					break;
				default:
					$result = 'align-self: center;';
			}
			$custom_css .= '@media screen and (max-width: 767px) {' . $uniqid . ' {' . $result . '}}';
		}

		// Design options paddings
		if (isset($params[$padding]) && !empty($params[$padding])) {
			$unit = $params[$padding]['unit'];
			foreach ($gaps_dir as $dir) {
				$custom_css .= $uniqid . ' {padding-' . $dir . ': ' . $params[$padding][$dir] . $unit . ' !important;}';
			}
		}

		if (isset($params[$padding.'_tablet']) && !empty($params[$padding.'_tablet'])) {
			$unit = $params[$padding.'_tablet']['unit'];
			foreach ($gaps_dir as $dir) {
				$custom_css .= '@media screen and (max-width: 1023px) {' . $uniqid . ' {padding-' . $dir . ': ' . $params[$padding.'_tablet'][$dir] . $unit . ' !important;}}';
			}
		}

		if (isset($params[$padding.'_mobile']) && !empty($params[$padding.'_mobile'])) {
			$unit = $params[$padding.'_mobile']['unit'];
			foreach ($gaps_dir as $dir) {
				$custom_css .= '@media screen and (max-width: 767px) {' . $uniqid . ' {padding-' . $dir . ': ' . $params[$padding.'_mobile'][$dir] . $unit . ' !important;}}';
			}
		}

		// Design options margins
		if (isset($params[$margin]) && !empty($params[$margin])) {
			$unit = $params[$margin]['unit'];
			foreach ($gaps_dir as $dir) {
				$custom_css .= $uniqid . ' {margin-' . $dir . ': ' . $params[$margin][$dir] . $unit . ' !important;}';
			}
		}

		if (isset($params[$margin.'_tablet']) && !empty($params[$margin.'_tablet'])) {
			$unit = $params[$margin.'_tablet']['unit'];
			foreach ($gaps_dir as $dir) {
				$custom_css .= '@media screen and (max-width: 1023px) {' . $uniqid . ' {margin-' . $dir . ': ' . $params[$margin.'_tablet'][$dir] . $unit . ' !important;}}';
			}
		}

		if (isset($params[$margin.'_mobile']) && !empty($params[$margin.'_mobile'])) {
			$unit = $params[$margin.'_mobile']['unit'];
			foreach ($gaps_dir as $dir) {
				$custom_css .= '@media screen and (max-width: 767px) {' . $uniqid . ' {margin-' . $dir . ': ' . $params[$margin.'_mobile'][$dir] . $unit . ' !important;}}';
			}
		}

		return $custom_css;
	}
}

/* FONTS MANAGER */

function thegem_fonts_allowed_mime_types( $existing_mimes = array() ) {
	$existing_mimes['ttf'] = 'application/x-font-ttf';
	$existing_mimes['ttf1'] = 'font/ttf';
	$existing_mimes['eot'] = 'application/vnd.ms-fontobject';
	$existing_mimes['woff'] = 'application/x-font-woff';
	$existing_mimes['woff2'] = 'font/woff2';
	$existing_mimes['svg'] = 'image/svg+xml';
	$existing_mimes['json'] = 'application/json';
	return $existing_mimes;
}
add_filter('upload_mimes', 'thegem_fonts_allowed_mime_types');

function thegem_modify_post_mime_types( $post_mime_types ) {
	$post_mime_types['application/x-font-ttf'] = array(esc_html__('TTF Font (Legacy)', 'thegem'), esc_html__( 'Manage Legacy TTFs (Legacy)', 'thegem' ), _n_noop( 'TTF (Legacy) <span class="count">(%s)</span>', 'TTFs (Legacy) <span class="count">(%s)</span>', 'thegem' ) );
	$post_mime_types['font/ttf'] = array(esc_html__('TTF Font', 'thegem'), esc_html__( 'Manage TTFs', 'thegem' ), _n_noop( 'TTF <span class="count">(%s)</span>', 'TTFs <span class="count">(%s)</span>', 'thegem' ) );
	$post_mime_types['application/vnd.ms-fontobject'] = array(esc_html__('EOT Font', 'thegem'), esc_html__( 'Manage EOTs', 'thegem' ), _n_noop( 'EOT <span class="count">(%s)</span>', 'EOTs <span class="count">(%s)</span>', 'thegem' ) );
	$post_mime_types['application/x-font-woff'] = array(esc_html__('WOFF Font', 'thegem'), esc_html__( 'Manage WOFFs', 'thegem' ), _n_noop( 'WOFF <span class="count">(%s)</span>', 'WOFFs <span class="count">(%s)</span>', 'thegem' ) );
	$post_mime_types['font/woff2'] = array(esc_html__('WOFF2 Font', 'thegem'), esc_html__( 'Manage WOFF2s', 'thegem' ), _n_noop( 'WOFF2 <span class="count">(%s)</span>', 'WOFF2s <span class="count">(%s)</span>', 'thegem' ) );
	$post_mime_types['image/svg+xml'] = array(esc_html__('SVG Font', 'thegem'), esc_html__( 'Manage SVGs', 'thegem' ), _n_noop( 'SVG <span class="count">(%s)</span>', 'SVGs <span class="count">(%s)</span>', 'thegem' ) );
	return $post_mime_types;
}
add_filter('post_mime_types', 'thegem_modify_post_mime_types');

/* SCRTIPTs & STYLES */

function thegem_elements_scripts() {
	$theme_uri = THEGEM_THEME_URI;
	wp_register_style('thegem-portfolio', $theme_uri . '/css/thegem-portfolio.css', array('thegem-hovers'), THEGEM_THEME_VERSION);
	wp_register_style('thegem-portfolio-filters-list', $theme_uri . '/css/thegem-portfolio-filters-list.css', array('thegem-portfolio'), THEGEM_THEME_VERSION);
	wp_register_style('thegem-news-grid', $theme_uri . '/css/thegem-news-grid.css', array( 'thegem-portfolio'), THEGEM_THEME_VERSION);
	wp_register_style('thegem-news-grid-hovers', $theme_uri . '/css/thegem-news-grid-hovers.css', array(), THEGEM_THEME_VERSION);

	wp_register_style('thegem-news-grid-version-new-hovers-default', $theme_uri . '/css/thegem-news-grid-version-new/default.css', array(), THEGEM_THEME_VERSION);
	wp_register_style('thegem-news-grid-version-new-hovers-zooming-blur', $theme_uri . '/css/thegem-news-grid-version-new/zooming-blur.css', array(), THEGEM_THEME_VERSION);
	wp_register_style('thegem-news-grid-version-new-hovers-horizontal-sliding', $theme_uri . '/css/thegem-news-grid-version-new/horizontal-sliding.css', array(), THEGEM_THEME_VERSION);
	wp_register_style('thegem-news-grid-version-new-hovers-vertical-sliding', $theme_uri . '/css/thegem-news-grid-version-new/vertical-sliding.css', array(), THEGEM_THEME_VERSION);
	wp_register_style('thegem-news-grid-version-new-hovers-gradient', $theme_uri . '/css/thegem-news-grid-version-new/gradient.css', array(), THEGEM_THEME_VERSION);
	wp_register_style('thegem-news-grid-version-new-hovers-circular', $theme_uri . '/css/thegem-news-grid-version-new/circular.css', array(), THEGEM_THEME_VERSION);
	wp_register_style('thegem-news-grid-version-new-hovers-zoom-overlay', $theme_uri . '/css/thegem-news-grid-version-new/zoom-overlay.css', array(), THEGEM_THEME_VERSION);
	wp_register_style('thegem-news-grid-version-new-hovers-disabled', $theme_uri . '/css/thegem-news-grid-version-new/disabled.css', array(), THEGEM_THEME_VERSION);

	wp_register_style('thegem-news-grid-version-default-hovers-default', $theme_uri . '/css/thegem-news-grid-version-default/default.css', array(), THEGEM_THEME_VERSION);
	wp_register_style('thegem-news-grid-version-default-hovers-zooming-blur', $theme_uri . '/css/thegem-news-grid-version-default/zooming-blur.css', array(), THEGEM_THEME_VERSION);
	wp_register_style('thegem-news-grid-version-default-hovers-horizontal-sliding', $theme_uri . '/css/thegem-news-grid-version-default/horizontal-sliding.css', array(), THEGEM_THEME_VERSION);
	wp_register_style('thegem-news-grid-version-default-hovers-vertical-sliding', $theme_uri . '/css/thegem-news-grid-version-default/vertical-sliding.css', array(), THEGEM_THEME_VERSION);
	wp_register_style('thegem-news-grid-version-default-hovers-gradient', $theme_uri . '/css/thegem-news-grid-version-default/gradient.css', array(), THEGEM_THEME_VERSION);
	wp_register_style('thegem-news-grid-version-default-hovers-circular', $theme_uri . '/css/thegem-news-grid-version-default/circular.css', array(), THEGEM_THEME_VERSION);
	wp_register_style('thegem-news-grid-version-default-zoom-overlay', $theme_uri . '/css/thegem-news-grid-version-default/zoom-overlay.css', array(), THEGEM_THEME_VERSION);
	wp_register_style('thegem-news-grid-version-default-hovers-disabled', $theme_uri . '/css/thegem-news-grid-version-default/disabled.css', array(), THEGEM_THEME_VERSION);

	wp_register_style('thegem-gallery-grid-styles', $theme_uri . '/css/thegem-gallery-grid.css', array( 'thegem-wrapboxes', 'thegem-hovers'), THEGEM_THEME_VERSION);

	wp_register_script('raphael', $theme_uri . '/js/raphael.js', array('jquery'), THEGEM_THEME_VERSION, true);
	wp_register_script('thegem-news-carousel', $theme_uri . '/js/news-carousel.js', array('jquery', 'jquery-carouFredSel'), THEGEM_THEME_VERSION, true);
	wp_register_script('thegem-portfolio-grid-carousel', $theme_uri . '/js/portfolio-grid-carousel.js', array('jquery', 'jquery-carouFredSel'), THEGEM_THEME_VERSION, true);
	wp_register_script('thegem-portfolio-grid-extended', $theme_uri . '/js/thegem-portfolio-grid-extended.js', array('jquery'), THEGEM_THEME_VERSION, true);
	wp_register_script( 'thegem-portfolio-grid-extended-inline', '', [], '', true );
	wp_register_script('thegem-widgets', $theme_uri . '/js/widgets.js', array('jquery', 'jquery-carouFredSel'), THEGEM_THEME_VERSION, true);
	wp_register_script('jquery-restable', $theme_uri . '/js/jquery.restable.js', array('jquery'), THEGEM_THEME_VERSION, true);
	wp_register_script('thegem-parallax-vertical', $theme_uri . '/js/jquery.parallaxVertical.js', array('jquery'), THEGEM_THEME_VERSION, true);
	wp_register_script('thegem-parallax-horizontal', $theme_uri . '/js/jquery.parallaxHorizontal.js', array('jquery'), THEGEM_THEME_VERSION, true);
	wp_register_style('nivo-slider', $theme_uri . '/css/nivo-slider.css', array(), THEGEM_THEME_VERSION);
	wp_register_script('jquery-nivoslider', $theme_uri . '/js/jquery.nivo.slider.pack.js', array('jquery'), THEGEM_THEME_VERSION);
	wp_register_script('thegem-nivoslider-init-script', $theme_uri . '/js/nivoslider-init.js', array('jquery', 'jquery-nivoslider'), THEGEM_THEME_VERSION);
	wp_localize_script('thegem-nivoslider-init-script', 'thegem_nivoslider_options', array(
		'effect' => thegem_get_option('slider_effect') ? thegem_get_option('slider_effect') : 'random',
		'slices' => thegem_get_option('slider_slices') ? thegem_get_option('slider_slices') : 15,
		'boxCols' => thegem_get_option('slider_boxCols') ? thegem_get_option('slider_boxCols') : 8,
		'boxRows' => thegem_get_option('slider_boxRows') ? thegem_get_option('slider_boxRows') : 4,
		'animSpeed' => thegem_get_option('slider_animSpeed') ? thegem_get_option('slider_animSpeed')*100 : 500,
		'pauseTime' => thegem_get_option('slider_pauseTime') ? thegem_get_option('slider_pauseTime')*1000 : 3000,
		'directionNav' => thegem_get_option('slider_directionNav') ? true : false,
		'controlNav' => thegem_get_option('slider_controlNav') ? true : false,
	));

	wp_register_script('thegem-video', THEGEM_THEME_URI . '/js/thegem-video.js', array(), THEGEM_THEME_VERSION, true);

	wp_register_script('thegem-isotope-metro', $theme_uri . '/js/isotope_layout_metro.js', array('thegem-isotope-js'), THEGEM_THEME_VERSION, true);
	wp_register_script('thegem-isotope-masonry-custom', $theme_uri . '/js/isotope-masonry-custom.js', array('thegem-isotope-js'), THEGEM_THEME_VERSION, true);
	wp_register_script('jquery-waypoints', $theme_uri . '/js/jquery.waypoints.js', array('jquery'), THEGEM_THEME_VERSION, true);
	wp_register_script('thegem-stickyColumn', $theme_uri . '/js/thegem-stickyColumn.js', array('jquery', 'jquery-waypoints'), THEGEM_THEME_VERSION, true);

}
add_action('wp_enqueue_scripts', 'thegem_elements_scripts', 6);

function thegem_elements_builders_scripts() {
	if (is_singular() || (
		(function_exists('thegem_archive_product_template') && thegem_archive_product_template())
		|| (function_exists('thegem_blog_archive_template') && thegem_blog_archive_template())
		|| (function_exists('thegem_cpt_archive_template') && thegem_cpt_archive_template())
		|| (function_exists('thegem_search_template') && thegem_search_template())
		)) {
		$post_id = function_exists('thegem_archive_product_template') && thegem_archive_product_template() ? thegem_archive_product_template() :
			(function_exists('thegem_blog_archive_template') && thegem_blog_archive_template() ? thegem_blog_archive_template() :
				(function_exists('thegem_cpt_archive_template') && thegem_cpt_archive_template() ? thegem_cpt_archive_template() :
					(function_exists('thegem_search_template') && thegem_search_template() ? thegem_search_template() :
						(function_exists('thegem_single_post_template') && in_array(get_post_type(), array('post', 'thegem_news'), true) && thegem_single_post_template() ? thegem_single_post_template() :
							(function_exists('thegem_cpt_template') && in_array(get_post_type(), thegem_get_available_po_custom_post_types(), true) && thegem_cpt_template() ? thegem_cpt_template() : get_the_ID())
						)
					)
				)
			);
		if (function_exists('thegem_enqueue_template_styles') && $post_id !== get_the_ID()) thegem_enqueue_template_styles($post_id);
		$elementor_data = get_post_meta($post_id, '_elementor_data');
		if ($elementor_data && defined('ELEMENTOR_VERSION')) {
			if (is_array($elementor_data)) {
				$elementor_data = $elementor_data[0];
			}
			$data = json_decode($elementor_data, true);
			if (is_array($data)) {
				$elements = array();
				\Elementor\Plugin::$instance->db->iterate_data( $data, function( $element ) use ( &$elements ) {
					$elements[] = (object)$element;
				} );
				$inlineFiltersScript = false;
				foreach($elements as $widget) {
					if (isset($widget->elType) && $widget->elType == 'widget') {
						$widgetSettings = $widget->settings;
						if ($widget->widgetType == 'thegem-portfolio' || $widget->widgetType == 'thegem-portfolio-list') {
							wp_enqueue_style('thegem-portfolio');
							$showFilter = !isset($widgetSettings['portfolio_show_filter']) || $widgetSettings['portfolio_show_filter'] == 'yes';
							$showSorting = !isset($widgetSettings['portfolio_show_sorting']) || $widgetSettings['portfolio_show_sorting'] == 'yes';
							if ($showFilter || $showSorting) {
								wp_enqueue_style('thegem-portfolio-filters-list');
							}
						} else if ($widget->widgetType == 'thegem-extended-blog-grid') {
							wp_enqueue_style('thegem-news-grid');
							$showFilter = !isset($widgetSettings['blog_show_filter']) || $widgetSettings['blog_show_filter'] == 'yes';
							$showSorting = !isset($widgetSettings['blog_show_sorting']) || $widgetSettings['blog_show_sorting'] == 'yes';
							if ($showFilter || $showSorting) {
								wp_enqueue_style('thegem-portfolio-filters-list');
							}
							if(isset($widgetSettings['skin_source']) && $widgetSettings['skin_source'] === 'builder' && !empty($widgetSettings['loop_builder'])) {
								thegem_enqueue_template_styles($widgetSettings['loop_builder']);
							}
						} else if ($widget->widgetType == 'thegem-posts-carousel') {
							if(isset($widgetSettings['skin_source']) && $widgetSettings['skin_source'] === 'builder' && !empty($widgetSettings['loop_builder'])) {
								thegem_enqueue_template_styles($widgetSettings['loop_builder']);
							}
						} else if ($widget->widgetType == 'thegem-team') {
							if(isset($widgetSettings['skin_source']) && $widgetSettings['skin_source'] === 'builder' && !empty($widgetSettings['loop_builder'])) {
								thegem_enqueue_template_styles($widgetSettings['loop_builder']);
							}
						} else if ($widget->widgetType == 'thegem-testimonials') {
							if(isset($widgetSettings['skin_source']) && $widgetSettings['skin_source'] === 'builder' && !empty($widgetSettings['loop_builder'])) {
								thegem_enqueue_template_styles($widgetSettings['loop_builder']);
							}
						} else if ($widget->widgetType == 'thegem-gallery-grid') {
							wp_enqueue_style('thegem-gallery-grid-styles');
							$showFilter = !isset($widgetSettings['show_filter']) || $widgetSettings['show_filter'] == 'yes';
							if ($showFilter) {
								wp_enqueue_style('thegem-portfolio-filters-list');
							}
						} else if ($widget->widgetType == 'thegem-styledbutton') {
							wp_enqueue_style('thegem-button');
						} else if ($widget->widgetType == 'thegem-extended-filter') {
							wp_enqueue_style('thegem-portfolio-filters-list');
						}
					}
				}
			}
		}
	}
	if (is_singular() && defined('ELEMENTOR_VERSION') && (
		(function_exists('thegem_single_post_template') && thegem_single_post_template()) ||
		(function_exists('thegem_portfolio_template') && thegem_portfolio_template()) ||
		(function_exists('thegem_cpt_template') && thegem_cpt_template())
	)) {
		add_action( 'wp_enqueue_scripts', [ \Elementor\Plugin::instance()->frontend, 'enqueue_styles' ] );
	}
}
add_action('wp_enqueue_scripts', 'thegem_elements_builders_scripts', 7);

function thegem_nonce_life() {
	return 31536000;
}
add_filter('nonce_life', 'thegem_nonce_life');

function thegem_fix_pw_init() {
	if(defined('PAGE_WIDGET_VERSION')) {
		remove_action('admin_print_scripts', 'pw_print_scripts');
		add_action('admin_print_scripts', 'thegem_pw_print_scripts');
	}
}
add_filter('init', 'thegem_fix_pw_init');

function thegem_pw_print_scripts() {

	global $pagenow, $typenow;

	$plugins_url = plugins_url();
	$pw_plugin_url = $plugins_url;
	if(defined('PAGE_WIDGET_VERSION') && version_compare(PAGE_WIDGET_VERSION, '4.0.1', '<')) {
		$pw_plugin_url .= '/wp-page-widget';
	} else {
		$pw_plugin_url .= '/wordpress-page-widget';
	}

	if (function_exists('pw_backend_check_allow_continue_process') && pw_backend_check_allow_continue_process()) {

		do_action( 'admin_print_scripts-widgets.php' );

		/* Plugin support */

		// Image widget support
		if (is_plugin_active('image-widget/image-widget.php')) {
			wp_enqueue_script('tribe-image-widget', $plugins_url . '/image-widget/resources/js/image-widget.js', array('jquery', 'media-upload', 'media-views'), false, true);
			wp_localize_script( 'tribe-image-widget', 'TribeImageWidget', array(
			'frame_title' => __( 'Select an Image', 'image_widget' ),
			'button_title' => __( 'Insert Into Widget', 'image_widget' ),
			) );
		}

		// Simple Link List Widget plugin support/
		if (is_plugin_active('simple-link-list-widget/simple-link-list-widget.php')) {
			wp_enqueue_script( 'sllw-sort-js', $plugins_url .'/simple-link-list-widget/js/sllw-sort.js');
		}

		// Easy releated posts and Simple social icons support.
		if (
			is_plugin_active('easy-related-posts/easy_related_posts.php')
			||
			is_plugin_active('simple-social-icons/simple-social-icons.php')
		) {
			wp_enqueue_script( 'wp-color-picker');
		}


		wp_enqueue_script('pw-widgets', $pw_plugin_url . '/assets/js/page-widgets.js', array('jquery', 'jquery-ui-sortable', 'jquery-ui-draggable', 'jquery-ui-droppable'), rand(), true);


		/*
		* Add pwTextWidgets extend from wp.textWidgets
		* Add pwMediaWidgets extend from wp.mediaWidgets
		*/
		if( version_compare( get_bloginfo('version'), '4.7.9', '>' ) ) {
			wp_enqueue_script('pw-extend-text-widgets', $pw_plugin_url . '/assets/js/pw-text-widgets-extend-wp-text-widgets.js', array('jquery', 'jquery-ui-sortable', 'jquery-ui-draggable', 'jquery-ui-droppable'), PAGE_WIDGET_VERSION, true);
			wp_enqueue_script('pw-extend-media-widgets', $pw_plugin_url . '/assets/js/pw-media-widgets-extend-wp-media-widgets.js', array('jquery', 'jquery-ui-sortable', 'jquery-ui-draggable', 'jquery-ui-droppable'), PAGE_WIDGET_VERSION, true);
		}

		/*
		* Add pwCustomHTML extend from wp.customHtmlWidgets
		*/
		if( version_compare( get_bloginfo('version'), '4.8.5', '>' ) ) {
			wp_enqueue_script('pw-extend-custom-html', $pw_plugin_url . '/assets/js/pw-custom-html-extend-wp-custom-html.js', array('jquery', 'jquery-ui-sortable', 'jquery-ui-draggable', 'jquery-ui-droppable'), PAGE_WIDGET_VERSION, true);

			$settings = wp_enqueue_code_editor( array(
				'type' => 'text/html',
				'codemirror' => array(
					'indentUnit' => 2,
					'tabSize' => 2,
				),
			));

			if ( empty( $settings ) ) {
				$settings = array(
					'disabled' => true,
				);
			}
			wp_add_inline_script( 'pw-extend-custom-html', sprintf( 'pwCustomHTML.init( %s );', wp_json_encode( $settings ) ), 'after' );
		}

		wp_localize_script( 'pw-widgets', 'wp_page_widgets', array(
			'remove_inactive_widgets_text'  => __('Press the following button will remove all of these inactive widgets', 'wp-page-widgets'),
			'remove_inactive_widgets' => __( 'Remove inactive widgets', 'wp-page-widgets' ),
		) );
	}
}

function thegem_info_message_notice() {
	if ( !current_user_can('update_themes' ) )
		return false;

	$message_data = get_option('thegem_info_message', array());
	$notice_class = '';
	$notice_html = '';
	$notice_until = 0;
	if(is_array($message_data) and !empty($message_data['last_check'])) {
		$last_check = intval($message_data['last_check']);
	} else {
		$last_check = 0;
	}

	if((time()-$last_check)/3600 > 24) {
		$last_check = time();
		$response = wp_remote_get('http://democontent.codex-themes.com/plugins/thegem/theme/message.json', array('timeout' => 5));
		if ( is_wp_error( $response ) ) {
			update_option('thegem_info_message', array('last_check' => $last_check));
			return false;
		}
		$body = wp_remote_retrieve_body($response);
		$data = json_decode($body, 1);
		if ( ! isset( $data['class'] ) ) {
			update_option('thegem_info_message', array('last_check' => $last_check));
			return false;
		}
		$notice_class = $data['class'];
		$notice_html = isset($data['html']) ? $data['html'] : '';
		$notice_until = isset($data['until']) ? intval($data['until']) : $last_check+24*60*60;
	} else {
		if(!empty($message_data['class']) && !empty($message_data['html'])) {
			$notice_class = $message_data['class'];
			$notice_html = $message_data['html'];
			$notice_until = isset($message_data['until']) ? intval($message_data['until']) : $last_check+24*60*60;
		}
	}

	update_option('thegem_info_message', array(
		'last_check' => $last_check,
		'class' => $notice_class,
		'html' => $notice_html,
		'until' => $notice_until,
	));

	if(!empty($notice_class) && !empty($notice_html) && $notice_until > time()) {
		echo '<div class="thegem-news-notice notice '.esc_attr($notice_class).' is-dismissible">';
		echo wp_kses_post($notice_html);
		echo '</div>';
	}
}

function thegem_plugin_update_notice() {
	if ( !current_user_can('update_themes' ) )
		return false;
	if ( !isset($themes_update) )
		$themes_update = get_site_transient('update_themes');
	if ( isset($themes_update->response['thegem-elementor']) ) {
		$update = $themes_update->response['thegem-elementor'];
		$theme = wp_prepare_themes_for_js( array( wp_get_theme('thegem-elementor') ) );
		$details_url = add_query_arg(array(), $update['url']);
		$update_url = wp_nonce_url( admin_url( 'update.php?action=upgrade-theme&amp;theme=' . urlencode( 'thegem-elementor' ) ), 'upgrade-theme_thegem-elementor' );
		if(isset($theme[0]) && isset($theme[0]['hasUpdate']) && $theme[0]['hasUpdate'] && version_compare( $theme[0]['version'], '5.0.0', '<' )) {
			wp_enqueue_script('jquery-fancybox');
			wp_enqueue_style('jquery-fancybox');
			echo '<div class="thegem-update-notice notice notice-warning is-dismissible">';
			echo '<p>'.sprintf(wp_kses(__('There is a new version of TheGem theme available. Your current version is <strong>%s</strong>. Update to <strong>%s</strong>.', 'thegem'), array('strong' => array())), $theme[0]['version'], $update['new_version']).'</p>';
			echo '<p>'.sprintf(wp_kses(__('<strong><a href="%s" class="thegem-view-details-link">View update details</a></strong> or <strong><a href="%s" class="thegem-update-link">Update now</a></strong>.', 'thegem'), array('strong' => array(), 'a' => array('href' => array(), 'class' => array()))), $details_url, $update_url).'</p>';
			echo '</div>';
		}
	}
}
add_action('admin_notices', 'thegem_plugin_update_notice');

function thegem_plugin_theme_update_notice() {
	if ( !current_user_can('update_themes' ) )
		return false;
	$thegem_theme = wp_get_theme('thegem-elementor');
	if($thegem_theme->exists() && version_compare($thegem_theme->get('Version'), '5.10.3.1', '<')) {
		echo '<div class="thegem-update-notice-new notice notice-error" style="display: flex; align-items: center;">';
		echo '<p style="margin: 5px 15px 0 10px;"><img src=" '.THEGEM_THEME_URI . '/images/alert-icon.svg'.' " width="40px" alt="thegem-blocks-logo"></p>';
		echo '<p><b style="display: block; font-size: 14px; padding-bottom: 5px">'.__('IMPORTANT:', 'thegem').'</b>'.__('Please update <strong>«TheGem Theme»</strong> to the latest version.', 'thegem').'</p>';
		echo '<p style="margin-left: auto;">'.sprintf(wp_kses(__('<a href="%s" class="button button-primary">Update now</a>', 'thegem'), array('strong' => array(), 'a' => array('href' => array(), 'class' => array()))), esc_url(admin_url('update-core.php'))).'</p>';
		echo '</div>';
	}
}
add_action('admin_notices', 'thegem_plugin_theme_update_notice');

if (!function_exists('thegem_get_posts_by_query')) {
	function thegem_get_posts_by_query() {
		$search_string = isset($_POST['q']) ? sanitize_text_field(wp_unslash($_POST['q'])) : '';
		$post_type = isset($_POST['post_type']) ? $_POST['post_type'] : 'post';
		$results = array();

		$query = new WP_Query(
			array(
				's' => $search_string,
				'post_type' => $post_type,
				'posts_per_page' => -1,
			)
		);

		if (!isset($query->posts)) {
			return;
		}

		foreach ($query->posts as $post) {
			$results[] = array(
				'id' => $post->ID,
				'text' => $post->post_title,
			);
		}

		wp_send_json($results);
	}
	add_action('wp_ajax_thegem_get_posts_by_query', 'thegem_get_posts_by_query');
}

if (!function_exists('thegem_get_posts_title_by_id')) {
	function thegem_get_posts_title_by_id() {
		$ids = isset($_POST['id']) ? $_POST['id'] : array();
		$post_type = isset($_POST['post_type']) ? $_POST['post_type'] : 'post';
		$results = array();

		$query = new WP_Query(
			array(
				'post_type' => $post_type,
				'post__in' => $ids,
				'posts_per_page' => -1,
				'orderby' => 'post__in',
			)
		);

		if (!isset($query->posts)) {
			return;
		}

		foreach ($query->posts as $post) {
			$results[$post->ID] = $post->post_title;
		}

		wp_send_json($results);
	}
	add_action('wp_ajax_thegem_get_posts_title_by_id', 'thegem_get_posts_title_by_id');
}

if (!function_exists('thegem_get_taxonomy_terms_by_query')) {
	function thegem_get_taxonomy_terms_by_query() {
		$search_string = isset($_POST['q']) ? sanitize_text_field(wp_unslash($_POST['q'])) : '';
		$post_type = isset($_POST['post_type']) ? $_POST['post_type'] : 'post';
		$results = array();

		if ($post_type && $post_type != 'any')  {
			$taxonomies = get_object_taxonomies( $post_type);
		} else {
			$taxonomies = get_taxonomies(array(
				'public' => true,
			));
		}

		if (!empty($taxonomies) && !is_wp_error($taxonomies)) {
			$args = array(
				'taxonomy' => $taxonomies,
				'hide_empty' => true,
				'name__like' => $search_string
			);

			$terms = get_terms( $args );

			if (!empty($terms)) {
				foreach ($terms as $term) {
					$results[] = array(
						'id' => $term->taxonomy . '|' . $term->term_id,
						'text' => $term->name . ' (' . $term->taxonomy . ')',
					);
				}
			}
		}

		wp_send_json($results);
	}
	add_action('wp_ajax_thegem_get_taxonomy_terms_by_query', 'thegem_get_taxonomy_terms_by_query');
}

if (!function_exists('thegem_get_taxonomy_terms_by_tax')) {
	function thegem_get_taxonomy_terms_by_tax() {
		$search_string = isset($_POST['q']) ? sanitize_text_field(wp_unslash($_POST['q'])) : '';
		$taxonomy = isset($_POST['taxonomy']) ? $_POST['taxonomy'] : 'category';
		$results = array();

		$args = array(
			'taxonomy' => $taxonomy,
			'hide_empty' => true,
			'name__like' => $search_string
		);

		$terms = get_terms( $args );

		if (!empty($terms)) {
			foreach ($terms as $term) {
				$results[] = array(
					'id' => $term->term_id,
					'text' => $term->name . ' (' . $term->taxonomy . ')',
				);
			}
		}

		wp_send_json($results);
	}
	add_action('wp_ajax_thegem_get_taxonomy_terms_by_tax', 'thegem_get_taxonomy_terms_by_tax');
}

if (!function_exists('thegem_get_taxonomy_terms_by_id')) {
	function thegem_get_taxonomy_terms_by_id() {
		$ids = isset($_POST['id']) ? $_POST['id'] : array();
		$results = array();

		foreach ($ids as $id) {
			$arr = explode("|", $id);
			$term = get_term_by('id', $arr[1], $arr[0]);
			$results[$term->taxonomy . '|' . $term->term_id] = $term->name . ' (' . $term->taxonomy . ')';
		}

		wp_send_json($results);
	}
	add_action('wp_ajax_thegem_get_taxonomy_terms_by_id', 'thegem_get_taxonomy_terms_by_id');
}

function thegem_elementor_content_set_deafult_settings_for_element($data, $type, $settings, $widgetType = '') {
	if(is_array($data)) {
		foreach($data as &$element) {
			if($element['elType'] === $type && ($type !== 'widget' || (!empty($element['widgetType']) && $widgetType === $element['widgetType']))) {
				$element['settings'] = array_merge($settings, $element['settings']);
			}
			if(!empty($element['elements'])) {
				$element['elements'] = thegem_elementor_content_set_deafult_settings_for_element($element['elements'], $type, $settings, $widgetType);
			}
		}
	}
	return $data;
}

$thegem_plugin_file = __FILE__;

add_action('admin_notices', 'thegem_info_message_notice');

function thegem_woocommerce_register_feature_definitions($features) {
	if(!thegem_get_option('woocommerce_order_attribution_feature_enabled')) {
		$features->add_feature_definition( 'order_attribution', __( 'Order Attribution', 'woocommerce' ), array(
			'name'               => __( 'Order Attribution', 'woocommerce' ),
			'description'        => __(
				'Enable this feature to track and credit channels and campaigns that contribute to orders on your site',
				'woocommerce'
			),
			'enabled_by_default' => true,
			'disable_ui'         => true,
			'is_legacy'          => true,
			'is_experimental'    => false,
		) );
	}
}
//add_action('woocommerce_register_feature_definitions', 'thegem_woocommerce_register_feature_definitions');

function thegem_woocommerce_feature_order_attribution_enabled($value, $option, $default_value) {
	if(!thegem_get_option('woocommerce_order_attribution_feature_enabled')) {
		$value = 'no';
	}
	return $value;
}
//add_filter('pre_option_woocommerce_feature_order_attribution_enabled', 'thegem_woocommerce_feature_order_attribution_enabled', 10 , 3);

function thegem_prepare_revslider_sr7_migrate() {
	if(defined('RS_REVISION') && !get_option('thegem_revslider_sr7_migrate', false)) {
		$slider = new RevSlider();
		$arrSliders = $slider->get_sliders();
		if(empty($arrSliders)) {
			$func = RevSliderGlobals::instance()->get('RevSliderFunctions');
			$global = $func->get_global_settings();
			$func->set_val($global, array('getTec', 'engine'), 'SR6');
			$func->set_global_settings($global);
		}
		update_option('thegem_revslider_sr7_migrate', 1);
	}
}
add_action( 'admin_init', 'thegem_prepare_revslider_sr7_migrate' );

require_once(plugin_dir_path( __FILE__ ) . 'inc/content.php');
require_once(plugin_dir_path( __FILE__ ) . 'inc/remote_media_upload.php');
require_once(plugin_dir_path( __FILE__ ) . 'inc/diagram.php');
require_once(plugin_dir_path( __FILE__ ) . 'inc/post-types/init.php');
require_once(plugin_dir_path( __FILE__ ) . 'inc/widgets/init.php');
require_once(plugin_dir_path( __FILE__ ) . 'inc/gdpr/gdpr.php');
require_once(plugin_dir_path( __FILE__ ) . 'inc/blocks-helper/index.php');
require_once(plugin_dir_path( __FILE__ ) . 'inc/elementor/elementor.php');
require_once(plugin_dir_path( __FILE__ ) . 'inc/woo-swatches.php');
require_once(plugin_dir_path( __FILE__ ) . 'inc/templates/templates.php');
require_once(plugin_dir_path( __FILE__ ) . 'inc/heading-animation/index.php');
require_once(plugin_dir_path( __FILE__ ) . 'inc/button-animation/index.php');
