<?php

if (!defined('ABSPATH')) exit;

class TheGemButtonAnimation {

    private static $instance = null;
    private $activeAnimations;

    const ANIMATION_SLIDE_UP = 'slide-up';
    const ANIMATION_SLIDE_DOWN = 'slide-down';
    const ANIMATION_SLIDE_LEFT = 'slide-left';
    const ANIMATION_SLIDE_RIGHT = 'slide-right';
    const ANIMATION_FADE_UP = 'fade-up';
    const ANIMATION_FADE_LEFT = 'fade-left';
    const ANIMATION_FADE_RIGHT = 'fade-right';
    const ANIMATION_FADE_DOWN = 'fade-down';
    const ANIMATION_FADE = 'fade';

    public static function instance() {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return self::$instance;
    }

    public static function getAnimationList() {
        return [
            static::ANIMATION_SLIDE_UP => __('Slide Up', 'thegem'),
            static::ANIMATION_SLIDE_DOWN => __('Slide Down', 'thegem'),
            static::ANIMATION_SLIDE_LEFT => __('Slide Left', 'thegem'),
            static::ANIMATION_SLIDE_RIGHT => __('Slide Right', 'thegem'),
            static::ANIMATION_FADE_UP => __('Fade Up', 'thegem'),
            static::ANIMATION_FADE_LEFT => __('Fade Left', 'thegem'),
            static::ANIMATION_FADE_RIGHT => __('Fade Right', 'thegem'),
            static::ANIMATION_FADE_DOWN => __('Fade Down', 'thegem'),
            static::ANIMATION_FADE => __('Fade', 'thegem')
        ];
    }

    public function init() {
		wp_register_style('thegem-button-animation', plugin_dir_url(__FILE__).'assets/css/main.css', []);
		wp_register_script('thegem-button-animation', plugin_dir_url(__FILE__).'assets/js/main.js');

		if (is_singular() && defined('ELEMENTOR_VERSION') && get_the_ID() > 0 && \Elementor\Plugin::$instance->documents->get( get_the_ID() )->is_built_with_elementor()) {
			$elementor_data = get_post_meta(get_the_ID(), '_elementor_data');

			if ($elementor_data) {
				if (is_array($elementor_data)) {
					$elementor_data = $elementor_data[0];
				}
				$data = json_decode($elementor_data, true);
				$elements = array();
				\Elementor\Plugin::$instance->db->iterate_data( $data, function( $element ) use ( &$elements ) {
					$elements[] = $element;
				} );

				if (!empty($elements)) {
					foreach($elements as $element) {
						if (isset($element['elType']) && $element['elType'] == 'widget') {
							if ($element['widgetType'] == 'thegem-styledbutton') {
								$buttonSettings = $element['settings'];
								if (isset($buttonSettings['effects_enabled']) && isset($buttonSettings['effects_enabled_name'])) {
									$this->activeAnimations[] = $buttonSettings['effects_enabled_name'];

									if (!empty($this->activeAnimations)) {
										$this->includeAssets();
									}
								}
							}
						}
					}
				}
			}
		}
    }

    private function includeAssets() {
        wp_enqueue_style('thegem-button-animation');

        if (is_user_logged_in()) {
            wp_enqueue_script('thegem-button-animation');
        }
    }

    public function includeInlineJs() {
        if (is_user_logged_in() || empty($this->activeAnimations)) return;

        static $isIncludeInlineJs;

        if (!$isIncludeInlineJs) {
            if ($js = file_get_contents(plugin_dir_path(__FILE__).'assets/js/main.js')) {
                $js = preg_replace('/(\s{2,})/', '', $js);
                $js = str_replace(["\r\n", "\r", "\n"], '',  $js);
                echo "<script type=\"text/javascript\">$js</script>";
            }

            $isIncludeInlineJs = true;
        }
    }

}