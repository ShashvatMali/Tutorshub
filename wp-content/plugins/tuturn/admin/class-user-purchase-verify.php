<?php
/**
 * This class used to activate envato license
 *
 *
 * @package    Envato_Purchase_Verify
 * @subpackage Envato_Purchase_Verify/admin
 * @author     Amentotech <info@amentotech.com>
 */
if (!class_exists('Tuturn_Envato_Purchase_Verify_User')) {
	class Tuturn_Envato_Purchase_Verify_User
	{
		/**
		 * The ID of this plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string    $plugin_name    The ID of this plugin.
		 */
		private $plugin_name;

		/**
		 * The version of this plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string    $version    The current version of this plugin.
		 */
		private $version;

		/**
		 * The rest api url
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string    $restapiurl    rest api url
		 */

		private $restApiUrl;
		/**
		 * The rest api version
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string    $restapiversion    rest api nonce
		 */
		private $restApiVersion;

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since    1.0.0
		 * @param      string    $plugin_name       The name of this plugin.
		 * @param      string    $version    The version of this plugin.
		 */
		public function __construct()
		{
			$this->tuturn_init_actions();
			add_action('wp_ajax_tuturn_verifypurchase', array(&$this, 'tuturn_verifypurchase'));
			add_action('wp_ajax_tuturn_remove_license', array(&$this, 'tuturn_remove_license'));
			add_action('admin_notices', array(&$this, 'tuturn_license_activation_notice'));
			add_action('admin_init', array(&$this, 'tuturn_license_deactivated_menu'));
			add_action('init', array(&$this, 'tuturn_pluginloaded'));
		}

		public function tuturn_pluginloaded()
		{
			add_action('plugin_action_links_' . TUTURN_BASENAME, array(&$this, 'tuturn_settings_link'), 9999);
		}

		// Link to settings page from plugins screen
		public function tuturn_settings_link($links)
		{
			$mylinks = array(
				'<a href="' . admin_url('options-general.php?page=tuturn_verify_purchase') . '">' . esc_html__('Settings', 'tuturn') . '</a>',
			);
			return array_merge($mylinks, $links);
		}


		public function tuturn_license_deactivated_menu()
		{
			$options = get_option('tuturn_verify_settings');
			$verified	= !empty($options['verified']) ? $options['verified'] : '';
			if (empty($verified) && empty($this->isLocalhost()) && ($_SERVER["SERVER_NAME"] != 'demos.wp-guppy.com')) {
				remove_menu_page('edit.php?post_type=service-providers');
				remove_menu_page('edit.php?post_type=withdraw');
			}
		}

		/**
		 * License activation notice
		 */
		public function tuturn_license_activation_notice()
		{
			$options = get_option('tuturn_verify_settings');
			$verified	= !empty($options['verified']) ? $options['verified'] : '';
			$page	= !empty($_GET['page']) ? $_GET['page'] : '';
			if (empty($verified) && $page !== 'tuturn_verify_purchase') {
			?>
				<div class="notice notice-success is-dismissible">
					<p><?php esc_html_e('To get all the Tuturn plugin functionality, please verify your valid license copy.', 'tuturn'); ?></p>
					<p><?php echo wp_sprintf('<a href="%s">%s</a>', admin_url('options-general.php?page=tuturn_verify_purchase'),  __('Activate license', 'tuturn')); ?></p>
				</div>
			<?php
			}
		}

		/**
		 * Local server check
		 */
		public function isLocalhost($whitelist = ['127.0.0.1', '::1'])
		{
			return in_array($_SERVER['REMOTE_ADDR'], $whitelist);
		}

		/**
		 * Remove license
		 */
		public function tuturn_remove_license()
		{
			$json = array();
			//security check
			if (!wp_verify_nonce($_POST['security'], 'ajax_nonce')) {
				$json['type'] = 'error';
				$json['message'] = esc_html__('Oops!', 'tuturn');
				$json['message_desc'] = esc_html__('Security check failed, this could be because of your browser cache. Please clear the cache and check it again', 'tuturn');
				wp_send_json($json);
			}

			$purchase_code	= !empty($_POST['purchase_code']) ? sanitize_text_field($_POST['purchase_code']) : '';
			$domain			= get_home_url();

			$url = 'https://wp-guppy.com/verification/wp-json/atepv/v2/epvRemoveLicense';

			$args = array(
				'timeout'		=> 45,
				'redirection'	=> 5,
				'httpversion'	=> '1.0',
				'blocking'		=> true,
				'headers'     => array(),
				'body'		=> array(
					'purchase_code'	=> $purchase_code,
					'domain'	=> $domain
				),
				'cookies'	=> array()
			);
			$response = wp_remote_post($url, $args);
			// error check
			if (is_wp_error($response)) {
				$error_message = $response->get_error_message();
				$json['type'] 	 	= 'error';
				$json['title']		= esc_html__('Failed!', 'tuturn');
				$json['message']	= $error_message;
				wp_send_json($json);
			} else {
				$response = json_decode(wp_remote_retrieve_body($response));
				if (!empty($response->type) && $response->type == 'success') {
					delete_option('tuturn_verify_settings');
				}
				wp_send_json($response);
			}
		}

		/**
		 * Verify item purchase code
		 */
		public function tuturn_verifypurchase()
		{
			$json = array();
			//security check
			if (!wp_verify_nonce($_POST['security'], 'ajax_nonce')) {
				$json['type'] = 'error';
				$json['message'] = esc_html__('Oops!', 'tuturn');
				$json['message_desc'] = esc_html__('Security check failed, this could be because of your browser cache. Please clear the cache and check it again', 'tuturn');
				wp_send_json($json);
			}
			$purchase_code	= !empty($_POST['purchase_code']) ? sanitize_text_field($_POST['purchase_code']) : '';
			$domain			= get_home_url();

			$url = 'https://wp-guppy.com/verification/wp-json/atepv/v2/verifypurchase';
			$args = array(
				'timeout'     => 45,
				'redirection' => 5,
				'httpversion' => '1.0',
				'blocking'    => true,
				'headers'     => array(),
				'body'        => array('purchase_code' => $purchase_code, 'domain' => $domain),
				'cookies'     => array()
			);
			$response = wp_remote_post($url, $args);
			$options = get_option('tuturn_verify_settings');
			$options['purchase_code']	= $purchase_code;
			// error check
			if (is_wp_error($response)) {
				update_option('tuturn_verify_settings', $options);
				$error_message = $response->get_error_message();
				$json['type'] 	 	= 'error';
				$json['title']		= esc_html__('Failed!', 'tuturn');
				$json['message']	= $error_message;
				wp_send_json($json);
			} else {
				$response = json_decode(wp_remote_retrieve_body($response));
				$options = get_option('tuturn_verify_settings');
				$options['purchase_code']	= $purchase_code;

				if (!empty($response->type) && $response->type == 'success') {
					$options['verified']	= true;
				}
				update_option('tuturn_verify_settings', $options);
				wp_send_json($response);
			}
		}

		/**
		 * Init all the actions of admin pages
		 */
		public function tuturn_init_actions()
		{
			add_action('admin_menu', array($this, 'tuturn_purchase_verify_menu'));
			add_action('admin_init', array($this, 'tuturn_purchase_verify_init'));
		}

		/**
		 * Submenu
		 */
		public function tuturn_purchase_verify_menu()
		{

			add_submenu_page(
				'options-general.php',
				esc_html__('Tuturn License activation', 'tuturn'),
				esc_html__('Tuturn License activation', 'tuturn'),
				'manage_options',
				'tuturn_verify_purchase',
				array($this, 'tuturn_verify_purchase_section_callback')
			);
		}

		/**
		 * Purchase code verify menu
		 */
		public function tuturn_purchase_verify_init()
		{

			register_setting(
				'tuturn_verify_settings',
				'tuturn_verify_settings'
			);

			add_settings_section(
				'user_purchase_code_verify',
				esc_html__('Tuturn plugin purchase code Verify', 'tuturn'),
				array($this, 'tuturn_api_text'),
				'tuturn_verify_section'
			);

			add_settings_field(
				'purchase_code',
				esc_html__('Tuturn plugin purchase code', 'tuturn'),
				array($this, 'tuturn_purchase_code_field'),
				'tuturn_verify_section',
				'user_purchase_code_verify'
			);
		}

		/**
		 * Get purchase code text
		 */
		public function tuturn_api_text()
		{
			$options = get_option('tuturn_verify_settings');
			$verified	= !empty($options['verified']) ? $options['verified'] : '';

			if (empty($verified)) {
				$message	= sprintf(__('<p>To get all the Tuturn plugin functionality, please verify your valid license copy. <a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-">How, i can find the purchase code</a>.</p>', 'tuturn'));
			} else {
				$message	= sprintf(__('<p>One license can only be used for your one live site, you can unlink this license to use our product to another server. You can check the license detail <a href="https://themeforest.net/licenses/standard">here</a> </p>', 'tuturn'));
			}
			echo wp_kses_post($message);
		}

		/**
		 * Purchase code text field
		 */
		public function tuturn_purchase_code_field()
		{
			$options = get_option('tuturn_verify_settings');
			$purchase_code	= !empty($options['purchase_code']) ? $options['purchase_code'] : '';
			printf(
				'<input type="text" name="%s" id="tuturn_purchase_code" value="%s" title="%s" />',
				esc_attr('tuturn_verify_settings[purchase_code]'),
				esc_attr($purchase_code),
				esc_html__('Enter purchase code', 'tuturn')
			);
		}

		/**
		 * Purchase code settings form
		 * 
		 */
		public function tuturn_verify_purchase_section_callback()
		{
			$options = get_option('tuturn_verify_settings');
			$verified	= !empty($options['verified']) ? $options['verified'] : '';
			?>
			<div id="at-item-verification" class="at-wrapper">
				<div class="at-content">
					<div class="settings-section">
						<form action='options.php' method='post'>
							<?php
							do_action('tuturn_form_render_before');
							settings_fields('tuturn_verify_settings');
							do_settings_sections('tuturn_verify_section');
							if (!empty($verified)) {
							?>
								<input type="submit" name="remove" class="button button-primary" id="tuturn_remove_license_btn" value="<?php esc_attr_e('Remove license', 'tuturn'); ?>" />
							<?php
							} else {
							?>
								<input type="submit" name="submit" class="button button-primary" id="tuturn_verify_btn" value="<?php esc_attr_e('Activate license', 'tuturn'); ?>" />
							<?php
							}
							do_action('tuturn_form_render_after');
							?>
						</form>
					</div>
				</div>
			</div>
		<?php
		}
	}
	new Tuturn_Envato_Purchase_Verify_User();
}
