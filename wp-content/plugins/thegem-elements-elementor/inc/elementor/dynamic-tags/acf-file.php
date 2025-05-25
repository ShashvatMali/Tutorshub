<?php
namespace TheGem_Elementor\DynamicTags;

use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class ACF_File extends ACF_Image {

	public function get_name() {
		return 'acf-file';
	}

	public function get_title() {
		return esc_html__( 'ACF', 'thegem' ) . ' ' . esc_html__( 'File Field', 'thegem' );
	}

	public function get_categories() {
		return [
			TagsModule::MEDIA_CATEGORY,
		];
	}

	public function get_supported_fields() {
		return [
			'file',
		];
	}
}
