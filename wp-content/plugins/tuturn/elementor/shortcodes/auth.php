<?php
/**
 * Shortcode
 *
 *
 * @package    Tuturn
 * @subpackage Tuturn/admin
 * @author     Amentotech <theamentotech@gmail.com>
 */

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if( !class_exists('Tuturn_Authentication') ){
	class Tuturn_Authentication extends Widget_Base {

		/**
		 *
		 * @since    1.0.0
		 * @access   static
		 * @var      base
		 */
		public function get_name() {
			return 'tuturn_element_authentication';
		}

		/**
		 *
		 * @since    1.0.0
		 * @access   static
		 * @var      title
		 */
		public function get_title() {
			return esc_html__( 'Authentication', 'tuturn' );
		}

		/**
		 *
		 * @since    1.0.0
		 * @access   public
		 * @var      icon
		 */
		public function get_icon() {
			return 'eicon-lock-user';
		}

		/**
		 *
		 * @since    1.0.0
		 * @access   public
		 * @var      category of shortcode
		 */
		public function get_categories() {
			return [ 'tuturn-elements' ];
		}

		/**
		 * Register category controls.
		 * @since    1.0.0
		 * @access   protected
		 */
		protected function register_controls() {

			//Content
			$this->start_controls_section(
				'content_section',
				[
					'label' => esc_html__( 'Content', 'tuturn' ),
					'tab' => Controls_Manager::TAB_CONTENT,
				]
			);

			$this->add_control(
				'form_type',
				[
					'label' => esc_html__( 'Form type', 'tuturn' ),
					'type'  => Controls_Manager::SELECT,
					'default' => 'register',
					'options' => [
						'register' 	=> esc_html__('Register form', 'tuturn'),
						'login' 	=> esc_html__('Login form', 'tuturn'),
						'forgot' 	=> esc_html__('Forgot password form', 'tuturn')
					],
				]
			);

			$this->add_control(
				'bg_image',
				[
					'label' => esc_html__( 'Background image', 'tuturn' ),
					'type'  => Controls_Manager::MEDIA,
					'default' => [
						'url' => \Elementor\Utils::get_placeholder_image_src(),
					],
				]
			);

			// logo on top of signup
			$this->add_control(
				'logo',
				[
					'type' => Controls_Manager::MEDIA,
					'label' => __( 'Choose logo', 'tuturn' ),
					'description' => esc_html__('Add logo. leave it empty to hide.', 'tuturn'),
					'default' => [
						'url' => \Elementor\Utils::get_placeholder_image_src(),
					],
				]
			);


			$this->add_control(
				'left_text',
				[
				'type'      	=> Controls_Manager::TEXT,
				'label'     	=> esc_html__( 'Left bottom text', 'tuturn' ),
				'description'   => esc_html__( 'Add text. leave it empty to hide.', 'tuturn' ),
				]
			);

			$this->add_control(
				'left_tagline',
				[
				'type'      	=> Controls_Manager::TEXT,
				'label'     	=> esc_html__( 'Left bottom tagline', 'tuturn' ),
				'description'   => esc_html__( 'Add tagline. leave it empty to hide.', 'tuturn' ),
				]
			);
			$this->add_control(
				'welcome_text',
				[
				'type'      	=> Controls_Manager::TEXT,
				'label'     	=> esc_html__( 'Welcome text', 'tuturn' ),
				'description'   => esc_html__( 'Add welcome text. leave it empty to hide.', 'tuturn' ),
				]
			);
			$this->add_control(
                'terms_page',
                [
                    'label' => esc_html__( 'Link', 'tuturn' ),
                    'type' => \Elementor\Controls_Manager::URL,
                    'placeholder' => esc_html__( 'https://example.com', 'tuturn' ),
                    'default' => [
                        'url' => '',
                        'is_external' => true,
                        'nofollow' => true,
                        'custom_attributes' => '',
                    ],
					'condition' => [
						'form_type' => 'register'
						],
                ]
            );
			$this->add_control(
				'tagline',
				[
				'type'        => Controls_Manager::TEXTAREA,
				'label'       => esc_html__('Short description', 'tuturn'),
				'description' => esc_html__('Add short description. leave it empty to hide.', 'tuturn'),
				]
			);

		 ;

			$this->end_controls_section();
		}

		/**
		 * Render shortcode
		 *
		 * @since 1.0.0
		 * @access protected
		 */
		protected function render() {
			$settings 	  	= $this->get_settings_for_display();
			$form_type    	= !empty( $settings['form_type'] )        ? $settings['form_type']        : '';
			$bg_image	 	= !empty( $settings['bg_image']['url'] )  ? $settings['bg_image']['url']  : '';
			$logo	      	= !empty( $settings['logo']['url'] )      ? $settings['logo']['url']      : '';
			$tagline	  	= !empty( $settings['tagline'] )          ? $settings['tagline']          : '';
			$welcome_text	= !empty( $settings['welcome_text'] )     ? $settings['welcome_text']     : '';
			$left_text	  	= !empty( $settings['left_text'] )        ? $settings['left_text']        : '';
			$left_tagline	= !empty( $settings['left_tagline'] )     ? $settings['left_tagline']     : '';
			$terms_page		= !empty( $settings['terms_page']['url'] )     ? $settings['terms_page']['url']	: '';
			$user_id	  	= is_user_logged_in()  ? get_current_user_id() : 0 ;
			$user_type		= !empty($user_id) ? apply_filters('tuturnGetUserType', $user_id ) : '';

			if(!current_user_can( 'administrator' ) && is_user_logged_in() ){

				if( !empty($user_type)){

					if($user_type ==='instructor' || $user_type === 'student'){
						$page_url	= tuturn_dashboard_page_uri($user_id);
					} else {
						$page_url	= home_url( '/' );
					}

					if(!empty($_GET['redirect'])){
						$page_url	= !empty( $_GET['redirect'] )? $_GET['redirect'] : '';
						$chat_id	= !empty( $_GET['chat_id'] )? $_GET['chat_id'] : '';
	
						if(!empty($chat_id)){
							$page_url   = add_query_arg('chat_id', $chat_id, $page_url );
						}
					}
					?>
					<script>
						jQuery(document).ready(function(){
							window.location.href = "<?php echo esc_url_raw($page_url);?>";
						});
					</script>
					<?php
				}
			} else {
				if(!current_user_can( 'administrator' ) && !empty($_GET['redirect'])){
					$tuturn_inbox_url	= !empty( $_GET['redirect'] )? $_GET['redirect'] : '';
					$chat_id	= !empty( $_GET['chat_id'] )? $_GET['chat_id'] : '';

					if(!empty($chat_id)){
						$tuturn_inbox_url   = add_query_arg('chat_id', $chat_id, $tuturn_inbox_url );
					}
					set_transient( 'tu_redirect_page_url', $tuturn_inbox_url, 200 );
				}
				?>
				<div class="tu-sc-shortcode tu-haslayout">
					<?php 
						if( !empty( $form_type ) && $form_type === 'register' ){
							echo do_shortcode('[tuturn_registration background="'.$bg_image.'" logo="'.$logo.'" tagline="'.$tagline.'" left_text="'.$left_text.'" left_tagline="'.$left_tagline.'" welcome_text="'.$welcome_text.'" terms_page="'.$terms_page.'" ]');
						} elseif( !empty( $form_type ) && $form_type === 'login' ){
							echo do_shortcode('[tuturn_signin background="'.$bg_image.'" logo="'.$logo.'" tagline="'.$tagline.'" left_text="'.$left_text.'" left_tagline="'.$left_tagline.'" welcome_text="'.$welcome_text.'" ]');
						} elseif( !empty( $form_type ) && $form_type === 'forgot' ){
							echo do_shortcode('[tuturn_forgot_password background="'.$bg_image.'" logo="'.$logo.'" tagline="'.$tagline.'" left_text="'.$left_text.'" left_tagline="'.$left_tagline.'" welcome_text="'.$welcome_text.'" ]');
						}
					?>
				</div>
				<?php
			}
		
		}

	}

	Plugin::instance()->widgets_manager->register( new Tuturn_Authentication );
}