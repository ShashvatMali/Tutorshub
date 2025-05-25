<?php

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Core\Breakpoints\Manager as Breakpoints_Manager;

#[\AllowDynamicProperties]
class TheGem_Inner_Section_Absolute {

	private static $instance = null;

	public static function instance() {
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;

	}

	public function __construct() {

		if (!defined('THEGEM_ELEMENTOR_INNER_SECTION_ABSOLUTE_DIR')) {
			define('THEGEM_ELEMENTOR_INNER_SECTION_ABSOLUTE_DIR', rtrim(__DIR__, ' /\\'));
		}

		if (!defined('THEGEM_ELEMENTOR_INNER_SECTION_ABSOLUTE_URL')) {
			define('THEGEM_ELEMENTOR_INNER_SECTION_ABSOLUTE_URL', rtrim(plugin_dir_url(__FILE__), ' /\\'));
		}

		add_action('elementor/element/section/section_advanced/before_section_end', array($this, 'controls_injection'), 10, 2);
	}

	public function controls_injection($obj, $args) {

		$active_breakpoints = \Elementor\Plugin::$instance->breakpoints->get_active_breakpoints();

		if ( array_key_exists( Breakpoints_Manager::BREAKPOINT_KEY_MOBILE_EXTRA, $active_breakpoints ) ) {
			$min_affected_device = Breakpoints_Manager::BREAKPOINT_KEY_MOBILE_EXTRA;
		} else {
			$min_affected_device = Breakpoints_Manager::BREAKPOINT_KEY_TABLET;
		}

		$width_control_settings = [
			'label' => esc_html__( 'Width', 'elementor' ),
			'type' => Controls_Manager::SLIDER,
			'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
			'range' => [
				'px' => [
					'min' => 500,
					'max' => 1600,
				],
				'%' => [
					'min' => 0,
					'max' => 100,
				],
				'vw' => [
					'min' => 0,
					'max' => 100,
				],
			],
			'default' => [
				'unit' => '%',
			],
			'min_affected_device' => [
				Breakpoints_Manager::BREAKPOINT_KEY_DESKTOP => $min_affected_device,
				Breakpoints_Manager::BREAKPOINT_KEY_LAPTOP => $min_affected_device,
				Breakpoints_Manager::BREAKPOINT_KEY_TABLET_EXTRA => $min_affected_device,
				Breakpoints_Manager::BREAKPOINT_KEY_TABLET => $min_affected_device,
				Breakpoints_Manager::BREAKPOINT_KEY_MOBILE_EXTRA => $min_affected_device,
			],
			'separator' => 'none',
		];

		$obj->add_responsive_control(
			'thegem_width',
			array_merge( $width_control_settings, [
				'selectors' => [
					'{{WRAPPER}}' => 'width: {{SIZE}}{{UNIT}};',
				],
				'device_args' => [
					Breakpoints_Manager::BREAKPOINT_KEY_DESKTOP => [
						'placeholder' => [
							'size' => 100,
							'unit' => '%',
						],
					],
					Breakpoints_Manager::BREAKPOINT_KEY_MOBILE => [
						// The mobile width is not inherited from the higher breakpoint width controls.
						'placeholder' => [
							'size' => 100,
							'unit' => '%',
						],
					],
				],
				'hide_in_top' => true,
			] )
		);

		$obj->add_control(
			'thegem_position_description',
			[
				'raw' => '<strong>' . esc_html__( 'Please note!', 'elementor' ) . '</strong> ' . esc_html__( 'Custom positioning is not considered best practice for responsive web design and should not be used too frequently.', 'elementor' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
				'render_type' => 'ui',
				'condition' => [
					'thegem_position!' => '',
				],
			]
		);

		$obj->add_control(
			'thegem_position',
			[
				'label' => esc_html__( 'Position', 'elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => esc_html__( 'Default', 'elementor' ),
					'absolute' => esc_html__( 'Absolute', 'elementor' ),
					'fixed' => esc_html__( 'Fixed', 'elementor' ),
				],
				'selectors' => [
					'{{WRAPPER}}' => 'position: {{VALUE}};',
				],
				'frontend_available' => true,
				'separator' => 'before',
				'hide_in_top' => true,
			]
		);

		$left = esc_html__( 'Left', 'elementor' );
		$right = esc_html__( 'Right', 'elementor' );

		$start = is_rtl() ? $right : $left;
		$end = ! is_rtl() ? $right : $left;

		$obj->add_control(
			'thegem_offset_orientation_h',
			[
				'label' => esc_html__( 'Horizontal Orientation', 'elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'toggle' => false,
				'default' => 'start',
				'options' => [
					'start' => [
						'title' => $start,
						'icon' => 'eicon-h-align-left',
					],
					'end' => [
						'title' => $end,
						'icon' => 'eicon-h-align-right',
					],
				],
				'classes' => 'elementor-control-start-end',
				'render_type' => 'ui',
				'condition' => [
					'thegem_position!' => '',
				],
				'hide_in_top' => true,
			]
		);

		$obj->add_responsive_control(
			'thegem_offset_x',
			[
				'label' => esc_html__( 'Offset', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => -200,
						'max' => 200,
					],
					'vw' => [
						'min' => -200,
						'max' => 200,
					],
					'vh' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'default' => [
					'size' => '0',
				],
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors' => [
					'body:not(.rtl) {{WRAPPER}}' => 'left: {{SIZE}}{{UNIT}}',
					'body.rtl {{WRAPPER}}' => 'right: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'thegem_offset_orientation_h!' => 'end',
					'thegem_position!' => '',
				],
				'hide_in_top' => true,
			]
		);

		$obj->add_responsive_control(
			'thegem_offset_x_end',
			[
				'label' => esc_html__( 'Offset', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
						'step' => 0.1,
					],
					'%' => [
						'min' => -200,
						'max' => 200,
					],
					'vw' => [
						'min' => -200,
						'max' => 200,
					],
					'vh' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'default' => [
					'size' => '0',
				],
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'vh', 'custom' ],
				'selectors' => [
					'body:not(.rtl) {{WRAPPER}}' => 'right: {{SIZE}}{{UNIT}}',
					'body.rtl {{WRAPPER}}' => 'left: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'thegem_offset_orientation_h' => 'end',
					'thegem_position!' => '',
				],
				'hide_in_top' => true,
			]
		);

		$obj->add_control(
			'thegem_offset_orientation_v',
			[
				'label' => esc_html__( 'Vertical Orientation', 'elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'toggle' => false,
				'default' => 'start',
				'options' => [
					'start' => [
						'title' => esc_html__( 'Top', 'elementor' ),
						'icon' => 'eicon-v-align-top',
					],
					'end' => [
						'title' => esc_html__( 'Bottom', 'elementor' ),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'render_type' => 'ui',
				'condition' => [
					'thegem_position!' => '',
				],
				'hide_in_top' => true,
			]
		);

		$obj->add_responsive_control(
			'thegem_offset_y',
			[
				'label' => esc_html__( 'Offset', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => -200,
						'max' => 200,
					],
					'vh' => [
						'min' => -200,
						'max' => 200,
					],
					'vw' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'size_units' => [ 'px', '%', 'em', 'rem', 'vh', 'vw', 'custom' ],
				'default' => [
					'size' => '0',
				],
				'selectors' => [
					'{{WRAPPER}}' => 'top: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'thegem_offset_orientation_v!' => 'end',
					'thegem_position!' => '',
				],
				'hide_in_top' => true,
			]
		);

		$obj->add_responsive_control(
			'thegem_offset_y_end',
			[
				'label' => esc_html__( 'Offset', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => -200,
						'max' => 200,
					],
					'vh' => [
						'min' => -200,
						'max' => 200,
					],
					'vw' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'size_units' => [ 'px', '%', 'em', 'rem', 'vh', 'vw', 'custom' ],
				'default' => [
					'size' => '0',
				],
				'selectors' => [
					'{{WRAPPER}}' => 'bottom: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'thegem_offset_orientation_v' => 'end',
					'thegem_position!' => '',
				],
				'hide_in_top' => true,
			]
		);

	}

}

TheGem_Inner_Section_Absolute::instance();