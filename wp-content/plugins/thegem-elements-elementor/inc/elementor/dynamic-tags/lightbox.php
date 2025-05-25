<?php
namespace TheGem_Elementor\DynamicTags;
use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use TheGem_Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Lightbox extends Tag {

	public function get_name() {
		return 'thegem-lightbox';
	}

	public function get_title() {
		return esc_html__( 'Lightbox', 'thegem' );
	}

	public function get_group() {
		return 'thegem';
	}

	public function get_categories() {
		return [ TagsModule::URL_CATEGORY ];
	}

	protected function register_controls() {
		$this->add_control(
			'type',
			[
				'label' => esc_html__( 'Type', 'thegem' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'video' => [
						'title' => esc_html__( 'Video', 'thegem' ),
						'icon' => 'eicon-video-camera',
					],
					'image' => [
						'title' => esc_html__( 'Image', 'thegem' ),
						'icon' => 'eicon-image-bold',
					],
				],
			]
		);

		$this->add_control(
			'image',
			[
				'label' => esc_html__( 'Image', 'thegem' ),
				'type' => Controls_Manager::MEDIA,
				'condition' => [
					'type' => 'image',
				],
			]
		);

		$this->add_control(
			'video_url',
			[
				'label' => esc_html__( 'Video URL', 'thegem' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'condition' => [
					'type' => 'video',
				],
			]
		);
	}


	private function get_image_settings( $settings ) {
		$image_settings = [
			'url' => $settings['image']['url'],
			'type' => 'image',
		];

		return $image_settings;
	}

	private function get_video_settings( $settings ) {
		$video_properties = \Elementor\Embed::get_video_properties( $settings['video_url'] );
		$video_url = null;
		if ( ! $video_properties ) {
			$video_type = 'hosted';
			$video_url = $settings['video_url'];
		} else {
			$video_type = $video_properties['provider'];
			$video_url = \Elementor\Embed::get_embed_url( $settings['video_url'] );
		}

		if ( null === $video_url ) {
			return '';
		}

		return [
			'type' => 'video',
			'videoType' => $video_type,
			'url' => $video_url,
		];
	}

	public function render() {
		$settings = $this->get_settings();

		$value = [];

		if ( ! $settings['type'] ) {
			return;
		}

		if ( 'image' === $settings['type'] && $settings['image'] ) {
			$value = $this->get_image_settings( $settings );
		} elseif ( 'video' === $settings['type'] && $settings['video_url'] ) {
			$value = $this->get_video_settings( $settings );
		}

		if ( ! $value ) {
			return;
		}

		echo Plugin::generate_action_link( 'lightbox', $value );
	}
}
