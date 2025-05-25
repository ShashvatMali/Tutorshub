<?php

/**
 * Shortcode
 *
 *
 * @package    tuturn
 * @subpackage tuturn/admin
 * @author     Amentotech <theamentotech@gmail.com>
 */

namespace Elementor;

if (!defined('ABSPATH')) {
	exit;
}

if (!class_exists('tuturn_sort_fqs')) {
	class tuturn_sort_fqs extends Widget_Base
	{

		/**
		 *
		 * @since    1.0.0
		 * @access   static
		 * @var      base
		 */
		public function get_name()
		{
			return 'tuturn_element_sort_faqs';
		}

		/**
		 *
		 * @since    1.0.0
		 * @access   static
		 * @var      title
		 */
		public function get_title()
		{
			return esc_html__('FAQ', 'tuturn');
		}

		/**
		 *
		 * @since    1.0.0
		 * @access   public
		 * @var      icon
		 */
		public function get_icon()
		{
			return 'eicon-skill-bar';
		}

		/**
		 *
		 * @since    1.0.0
		 * @access   public
		 * @var      category of shortcode
		 */
		public function get_categories()
		{
			return ['tuturn-elements'];
		}

		/**
		 * Register category controls.
		 * @since    1.0.0
		 * @access   protected
		 */

		protected function register_controls()
		{
			$faq_categories   = tuturn_elementor_get_taxonomies('faq', 'faq_categories');
			$faq_categories   = !empty($faq_categories) ? $faq_categories : array();

			$this->start_controls_section(
				'content_section',
				[
					'label'   => esc_html__('Category section', 'tuturn'),
					'tab'     => Controls_Manager::TAB_CONTENT,
				]
			);

			$this->add_control(
				'cat_sec_title',
				[
					'type'          => Controls_Manager::TEXT,
					'label'         => esc_html__('Title', 'tuturn'),
					'description'   => esc_html__('Add title. leave it empty to hide.', 'tuturn'),
				]
			);

			$this->add_control(
				'faq_categories',
				[
					'label'     => esc_html__('Categories', 'tuturn'),
					'type'      => Controls_Manager::SELECT2,
					'multiple'  => true,
					'options'   => $faq_categories,
				]
			);
			$this->end_controls_section();

			$this->start_controls_section(
				'search_field_section',
				[
					'label'     => esc_html__('Search section', 'tuturn'),
					'tab'       => Controls_Manager::TAB_CONTENT,
				]
			);

			$this->add_control(
				'show_search',
				[
					'label'         => esc_html__('Show search', 'tuturn'),
					'type'          => Controls_Manager::SWITCHER,
					'label_on'      => esc_html__('Show', 'tuturn'),
					'label_off'     => esc_html__('Hide', 'tuturn'),
					'return_value'  => 'yes',
					'default'       => 'yes',
				]
			);

			$this->add_control(
				'search_tagline',
				[
					'type'        => Controls_Manager::TEXT,
					'label'       => esc_html__('Search field tagline', 'tuturn'),
					'description' => esc_html__('Add search field tagline.', 'tuturn'),
					'default'     => esc_html__('Have question in mind?', 'tuturn'),
				]
			);

			$this->add_control(
				'search_title',
				[
					'type'        => Controls_Manager::TEXT,
					'label'       => esc_html__('Search field title', 'tuturn'),
					'description' => esc_html__('Add search field title.', 'tuturn'),
					'default'     => esc_html__('Search from our common FAQs', 'tuturn'),
				]
			);

			$this->add_control(
				'search_placeholder',
				[
					'type'        => Controls_Manager::TEXT,
					'label'       => esc_html__('Search field placeholder', 'tuturn'),
					'description' => esc_html__('Add search field placeholder.', 'tuturn'),
					'default'     => esc_html__('Search what’s frequently asked', 'tuturn'),
				]
			);

			$this->add_control(
				'search_btn_text',
				[
					'type'        => Controls_Manager::TEXT,
					'label'       => esc_html__('Search button text', 'tuturn'),
					'description' => esc_html__('Add search button text.', 'tuturn'),
					'default'     => esc_html__('Search', 'tuturn'),
				]
			);

			$this->end_controls_section();
		}

		/**
		 * Render shortcode
		 *
		 * @since 1.0.0
		 * @access protected
		 */
		protected function render()
		{
			$settings             = $this->get_settings_for_display();
			$cat_sec_title        = !empty($settings['cat_sec_title']) ? $settings['cat_sec_title'] : '';
			$categories           = !empty($settings['faq_categories']) ? $settings['faq_categories'] : array();

			$show_search_field    = !empty($settings['show_search']) ? $settings['show_search'] : false;
			$search_tagline       = !empty($settings['search_tagline']) ? $settings['search_tagline'] : '';
			$search_title         = !empty($settings['search_title']) ? $settings['search_title'] : '';
			$search_placeholder   = !empty($settings['search_placeholder']) ? $settings['search_placeholder'] : '';
			$search_btn_text      = !empty($settings['search_btn_text']) ? $settings['search_btn_text'] : '';
			$faq_first_category   = !empty($categories) ? $categories[0] : 0;
			$faq_category         = !empty($_GET['faq_category']) ? $_GET['faq_category'] : $faq_first_category;
			$faq_search           = !empty($_GET['faq_search']) ? esc_html($_GET['faq_search']) : '';
			$rand_faq             = rand(99, 9999);
			$default_zigzag		  = tuturn_add_http_protcol(TUTURN_DIRECTORY_URI . 'public/images/zigzag-line.svg');
				
			$args = array(
				'post_type' => 'faq',
				'numberposts' => -1,
			);

			if (!empty($faq_search)) {
				$args['s'] = $faq_search;
			} else if (!empty($faq_category)) {
				$faq_category_page_url = add_query_arg(
					array('faq_category' => $faq_category),
					get_permalink()
				);

				$args['tax_query'] = array(
					array(
						'taxonomy'  => 'faq_categories',
						'field'     => 'term_id',
						'terms'     => $faq_category
					)
				);
			}

			$slider_direction   = 'ltr';
			if ( is_rtl() ) {
				$slider_direction   = 'rtl';
			}

			$the_query = new \WP_Query(apply_filters('tuturn_faq_args', $args));
			if (!empty($categories) || !empty($cat_sec_title)) { ?>				
				<div class="tu-slider-section">
					<div class="container">
						<?php if(!empty($cat_sec_title) || !empty($categories)){?>
							<div class="row justify-content-center">
								<?php
								if (!empty($cat_sec_title)) { ?>
									<div class="col-12">
										<div class="tu-maintitle text-center">
											<h2><?php echo esc_html($cat_sec_title); ?></h2>
										</div>
									</div>
								<?php } 
								if(!empty($categories)){?>
									<div class="col-lg-10">
										<div id="tu-faqsslider-<?php echo intval($rand_faq); ?> tu-faqsslider" class="tu-faqsslider-<?php echo intval($rand_faq); ?> tu-faqsslider tu-sliderarrow">
											<div class="splide__track">
												<ul class="splide__list">
													<?php
													$count = 0;
													foreach ($categories as $cat_id) {
														$count++;
														$term	= get_term_by('id', $cat_id, 'faq_categories');

														if (empty($term)) {
															continue;
														}

														$image_id               = get_term_meta($term->term_id, 'category-image-id', true);
														$thumbnail_url          = tuturn_prepare_image_source($image_id, 200, 200);
														$term_post_count        = get_term($cat_id, 'faq_categories');
														$term_post_count        = !empty($term_post_count) ? $term_post_count->count : 0;
														$faq_category_page_url  = add_query_arg(
															array('faq_category' => $term->term_id),
															get_permalink()
														);

														$active_class     = "tu-faq-category";

														if ($faq_category == $term->term_id) {
															$active_class = "tu-faq-category tu-faq-category-active";
														} else if ($count === 1 && $faq_category == 0) {
															$active_class = "tu-faq-category tu-faq-category-active";
														}

														if ($term_post_count > 0) { ?>
															<li class="splide__slide">
																<a class="tu-faqholderwrap" href="<?php echo esc_url($faq_category_page_url); ?>">
																	<div class="tu-faq-holder">
																		<div class="<?php echo esc_attr($active_class); ?>">
																			<figure>
																				<?php if (!empty($thumbnail_url)) { ?>
																					<img src="<?php echo esc_url($thumbnail_url); ?>" alt="<?php esc_attr_e('Faq', 'tuturn'); ?>">
																				<?php } ?>
																				<figcaption class="tu-faq_desp">
																					<?php if (!empty($term->name)) { ?>
																						<h5><?php echo esc_html($term->name); ?></h5>
																					<?php } ?>
																					<?php if (!empty($term_post_count)) { ?>
																						<span><?php echo wp_sprintf(esc_html__('%d FAQ\'s', 'tuturn'), $term_post_count); ?></span>
																					<?php } ?>
																				</figcaption>
																			</figure>
																		</div>
																	</div>
																</a>
															</li>
														<?php
														}
													} ?>
												</ul>
											</div>
										</div>
									</div>
								<?php } ?>
							</div>
						<?php } ?>
					</div>
				</div>
				<div class="tu-main-section tu-faq-section">
					<div class="container">
						<div class="row justify-content-center">
							<div class="col-xl-10 col-md-12">
								<?php if (!empty($show_search_field) && $show_search_field === 'yes') { ?>
									<div class="tu-faq-search text-center">
										<?php if(!empty($search_tagline) || !empty($search_title)){?>
											<div class="tu-maintitle text-center">
												<?php if(!empty($default_zigzag)) {?>
													<img src="<?php echo esc_attr($default_zigzag); ?>" alt="<?php echo esc_attr($search_title);?>">
												<?php } ?>
												<?php if (!empty($search_tagline)) { ?>
													<h5><?php echo esc_html($search_tagline); ?></h5>
												<?php } ?>
												<?php if (!empty($search_title)) { ?>
													<h2 class="tu-blue-clr"><?php echo esc_html($search_title); ?></h2>
												<?php } ?>
											</div>
										<?php } ?>
										<div class="tu-faq_input">
											<div class="tu-inputappend">
												<form method="get" class="tu-faqform" action="">
													<div class="tu-searcbar">
														<div class="tu-inputicon">
															<a href="javascript:void(0);"><i class="icon icon-search"></i></a>
															<input type="text" name="faq_search" class="form-control" placeholder="<?php echo esc_attr($search_placeholder); ?>">
														</div>
														<input type="hidden" name="faq_category" value="<?php echo intval($faq_category); ?>" value="<?php echo esc_attr($faq_search); ?>"/>
														<?php if (!empty($search_btn_text)) { ?>
															<button type="submit" class="tu-primbtn-lg"><?php echo esc_html($search_btn_text); ?></button>
														<?php } ?>
													</div>
												</form>
											</div>
										</div>
									</div>
								<?php } 
								if(!empty($faq_category)) {?>
									<div class="tu-faq-acordian">
										<?php
										$term       = get_term_by('id', $faq_category, 'faq_categories');
										$term_name  = !empty($term->name) ? sprintf(esc_html__('%s FAQ\'s', 'tuturn'), $term->name) : esc_html__('FAQ\'s', 'tuturn');
										if(!empty($term_name)){?>
											<div class="tu-acoridan_title">
												<h3><?php echo do_shortcode($term_name); ?></h3>
											</div>
										<?php } ?>
										<div class="tu-acordian">
											<?php if ($the_query->have_posts()) { ?>
												<ul id="tu-accordion-faq" class="tu-accordion-faq">
													<?php
													$count_post = 0;
													while ($the_query->have_posts()) {
														$count_post++;
														$the_query->the_post();
														$post_id        = get_the_ID();
														$collapse       = ($count_post === 1) ? 'show' : '';
														$aria_expand    = ($count_post === 1) ? 'true' : 'false';
														if (!empty($post_id)) { ?>
															<li>
																<div class="tu-accordion-faq_title" data-bs-toggle="collapse" role="button" data-bs-target="#collapseLi<?php echo esc_attr($post_id); ?>" aria-expanded="<?php echo esc_attr($aria_expand); ?>">
																	<?php if(get_the_title()){?> 
																		<h5><?php the_title(); ?></h5>
																	<?php } ?>
																</div>
																<?php if (get_the_content()) { ?>
																	<div class="collapse <?php echo esc_attr($collapse); ?>" id="collapseLi<?php echo esc_attr($post_id); ?>" data-bs-parent="#tu-accordion">
																		<div class="tu-accordion-faq_info">
																			<?php the_content(); ?>
																		</div>
																	</div>
																<?php } ?>
															</li>
													    <?php
														}
													} ?>
													<?php wp_reset_postdata(); ?>
												</ul>
											<?php } else { ?>
												<p><?php esc_html_e('Sorry, no FAQ\'S 	 your criteria.', 'tuturn'); ?></p>
											<?php } ?>
										</div>
									</div>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
				<script>
					jQuery(document).ready(function () {
						var tu_faqsslider = document.querySelector(".tu-faqsslider-<?php echo esc_js($rand_faq);?>");
						if (tu_faqsslider != null) {
							var splide = new Splide(".tu-faqsslider-<?php echo esc_js($rand_faq);?>", {
								direction: "<?php echo esc_js($slider_direction);?>",
								type: "loop",
								perPage: 4,
								perMove: 1,
								arrows: true,
								pagination: false,
								gap: 20,
								breakpoints: {
									1400: {
										perPage: 3,
									},
									991: {
										perPage: 2,
										arrows: false,
										pagination: true,
										focus: "center",
									},
									767: {
										perPage: 2,
										gap: 20,
										arrows: false,
										pagination: true,
										focus: "center",
									},
									480: {
										perPage: 1,
										arrows: false,
										pagination: true,
										focus: "center",
									},
								},
							});
							splide.mount();
						}
					});
				</script>
				<?php
			}
		}
	}

	Plugin::instance()->widgets_manager->register(new tuturn_sort_fqs);
}
