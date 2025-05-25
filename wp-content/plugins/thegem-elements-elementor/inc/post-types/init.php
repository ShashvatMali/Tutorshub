<?php

require_once(plugin_dir_path( __FILE__ ) . 'portfolios.php');
require_once(plugin_dir_path( __FILE__ ) . 'teams.php');
require_once(plugin_dir_path( __FILE__ ) . 'testimonials.php');
require_once(plugin_dir_path( __FILE__ ) . 'news.php');
require_once(plugin_dir_path( __FILE__ ) . 'titles.php');
require_once(plugin_dir_path( __FILE__ ) . 'footers.php');
require_once(plugin_dir_path( __FILE__ ) . 'slideshows.php');

function thegem_rewrite_flush() {
	thegem_news_post_type_init();
	thegem_portfolio_item_post_type_init();

	thegem_rewrite_rules_flush();
}

function thegem_rewrite_rules_flush() {
	// force recreate rewrite rules, flush_rewrite_rules works unstable
	delete_option( 'rewrite_rules' );
}

register_activation_hook($thegem_plugin_file, 'thegem_rewrite_flush' );
register_deactivation_hook($thegem_plugin_file, 'thegem_rewrite_rules_flush' );

add_action( 'after_switch_theme', 'thegem_rewrite_rules_flush' );

// Post gallery meta box
function thegem_post_item_meta_box_gallery($post) {
	wp_nonce_field(__FILE__, '_thegem_post_item_meta_box_gallery_nonce');
	$thegem_gallery_images_ids = '';
	if (metadata_exists('post', $post->ID, 'thegem_post_item_gallery_images')) {
		$thegem_gallery_images_ids = get_post_meta($post->ID, 'thegem_post_item_gallery_images', true);
	}
	$attachments_ids = array_filter(explode(',', $thegem_gallery_images_ids));

	echo '<div id="gallery_manager" class="gallery_settings_box">';
	echo '<input type="hidden" id="thegem_gallery_images" name="thegem_post_item_gallery_images" value="' . esc_attr($thegem_gallery_images_ids) . '" />';

	echo '<ul class="gallery-images">';
	if ($attachments_ids) {
		foreach ($attachments_ids as $attachment_id) {
			$attachment = wp_get_attachment_image($attachment_id);
			if (empty($attachment)) {
				continue;
			}
			echo '<li class="image" data-attachment_id="' . esc_attr($attachment_id) . '"><a target="_blank" href="' . get_edit_post_link($attachment_id) . '" class="edit">' . $attachment . '</a><a href="javascript:void(0);" class="remove">x</a></li>';
		}
	}
	echo '</ul><br class="clear" />';

	echo '<a id="upload_button" href="javascript:void(0);">' . __('Add gallery images', 'thegem') . '</a>';

	echo '</div>';
}

add_action('add_meta_boxes', function () {
	if(!function_exists('thegem_get_available_po_custom_post_types')) return ;
	$post_types = thegem_get_available_po_custom_post_types();
	add_meta_box('thegem_post_item_gallery', 'Post Gallery', 'thegem_post_item_meta_box_gallery', 'post', 'side');
	add_meta_box('thegem_post_item_gallery', 'Post Gallery', 'thegem_post_item_meta_box_gallery', 'page', 'side');
	foreach($post_types as $post_type) {
		if(!thegem_get_option($post_type.'_post_gallery_disabled')) {
			add_meta_box('thegem_post_item_gallery', 'Post Gallery', 'thegem_post_item_meta_box_gallery', $post_type, 'side');
		}
	}
});

add_action('save_post', function ($post_id) {
	if (isset($_POST['thegem_post_item_gallery_images'], $_POST['_thegem_post_item_meta_box_gallery_nonce']) && wp_verify_nonce($_POST['_thegem_post_item_meta_box_gallery_nonce'], __FILE__)) {
		update_post_meta($post_id, 'thegem_post_item_gallery_images', sanitize_text_field($_POST['thegem_post_item_gallery_images']));
	}
});

function thegem_getCustomFieldsFromPostTypes($post_type) {
	global $pagenow;
	switch ($post_type) {
		case 'page':
			$post_type = 'default';
			break;
		case 'thegem_pf_item':
			$post_type = 'portfolio';
			break;
	}

	$pt_data = thegem_theme_options_get_page_settings($post_type);
	$custom_fields = !empty($pt_data['custom_fields']) ? $pt_data['custom_fields'] : null;
	$custom_fields_data = !empty($pt_data['custom_fields_data']) ? json_decode($pt_data['custom_fields_data'], true) : null;
	$data = array();

	if (empty($custom_fields)) return;

	if (!empty($custom_fields_data)) {
		foreach($custom_fields_data as $field) {
			$pm = get_post_meta(get_the_ID(), $field['key'], true);
			$value = !empty($pm) ? $pm : '';

			$data[] = array(
				'key' => $field['key'],
				'title' => $field['title'],
				'type' => $field['type'],
				'value' => $pagenow == 'post-new.php' ? $field['value'] : $value,
			);
		}
	}

	return $data;
}