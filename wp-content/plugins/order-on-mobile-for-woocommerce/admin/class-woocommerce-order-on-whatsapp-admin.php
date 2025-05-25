<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.intolap.com/
 * @since    1.0.0
 *
 * @package    Woocommerce_Order_On_Whatsapp
 * @subpackage Woocommerce_Order_On_Whatsapp/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    Woocommerce_Order_On_Whatsapp
 * @subpackage Woocommerce_Order_On_Whatsapp/admin
 * @author     INTOLAP <info@intolap.com>
 */
class Woocommerce_Order_On_Whatsapp_Admin {

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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
        add_filter( 'plugin_action_links', array( $this, 'woow_plugin_settings_link' ), 10, 4 );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woocommerce_Order_On_Whatsapp_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woocommerce_Order_On_Whatsapp_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woocommerce-order-on-whatsapp-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woocommerce_Order_On_Whatsapp_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woocommerce_Order_On_Whatsapp_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woocommerce-order-on-whatsapp-admin.js', array( 'jquery' ), $this->version, false );

	}

    /**
     * Add settings link to plugin actions
     *
     * @param  array  $plugin_actions
     * @param  string $plugin_file
     * @since  2.0
     * @return array
     */
    public static function woow_plugin_settings_link( $actions, $plugin_file, $plugin_data, $context ) {

        // add a 'Configure' link to the front of the actions list for this plugin
        if ( 'order-on-mobile-for-woocommerce/woocommerce-order-on-whatsapp.php' === $plugin_file ) {
            $new_actions['woow_settings'] = sprintf( __( '<a href="%s">Settings</a>', 'woocommerce-order-on-whatsapp' ), esc_url( admin_url( 'admin.php?page=wc-settings&amp;tab=woow_settings_tab' ) ) );

            $new_actions['woow_upgrade'] = sprintf( __( '<a href="%s" target="_blank">Upgrade to PRO</a>', 'woocommerce-order-on-whatsapp' ), esc_url( 'https://www.intolap.com/product/order-on-mobile-for-woocommerce-pro/' ) );

            return array_merge( $new_actions, $actions ); 
        }else{
            return $actions;
        }
    }

	/* Display a admin notice (error) if the plugin settings being empty */
	public static function woow_admin_notice_error() {
	    if(get_option('woow_whatsapp_number') == ''){
            $class = 'notice notice-error';
    	    
    	    $settings_link = " <a href='".admin_url()."admin.php?page=wc-settings&amp;tab=woow_settings_tab'>".__( 'Settings', 'woocommerce-order-on-whatsapp' )."</a>";

    	    $message = __( 'Error! Woocommerce Order On Whatsapp Plugin Settings empty.', 'woocommerce-order-on-whatsapp' );
    	 	

    	    printf( '<div class="%1$s"><p>%2$s '.$settings_link.'</p></div>', esc_attr( $class ), esc_html( $message ) ); 
        }
	}


	/* Coding for Woocmmerce Custom Setting tabs */
    public static function woow_add_settings_tab() {  
	   $current_tab = ( isset($_GET['tab']) && $_GET['tab'] == 'woow_settings_tab' ) ? 'nav-tab-active' : '';
	   echo '<a href="'.admin_url().'admin.php?page=wc-settings&amp;tab=woow_settings_tab" class="nav-tab '.$current_tab.'">'.__( "Woocommerce Order On Whatsapp", "woocommerce-order-on-whatsapp" ).'</a>';
    }

    /* Coding for Woocmmerce Custom Setting tabs */
    public static function woow_settings_tab() {
        woocommerce_admin_fields( self::get_settings() );
    }

    /* Coding for Woocmmerce Custom Setting tabs */
    public static function woow_update_settings() {
        woocommerce_update_options( self::get_settings() );
    }


    public static function get_settings() {
        $settings = array(
            'section_title' => array(
                'name'     => __( 'Settings', 'woocommerce-order-on-whatsapp' ),
                'type'     => 'title',
                'desc'     => '',
                'id'       => 'woow_settings_section_title'
            ),
            'woow_whatsapp_number' => array(
                'name' => __( 'Enter Your Whatsapp Number', 'woocommerce-order-on-whatsapp' ),
                'type' => 'text',
                'desc' => __( 'With Country Code' ),
                'id'   => 'woow_whatsapp_number',
                'desc_tip' =>  true,
            ),
            'woow_custom_message' => array(
                'name' => __( 'Enter Your Custom Message', 'woocommerce-order-on-whatsapp' ),
                'type' => 'textarea',
                'desc' => __( 'The message you will receive on WhatsApp when customer clicks on the button.' ),
                'id'   => 'woow_custom_message',
                'desc_tip' =>  true,
            ),
            'woow_hide_add_to_cart' => array(
                'name' => __( 'Hide "Add to Cart" Button?', 'woocommerce-order-on-whatsapp' ),
                'type' => 'checkbox',
                'desc' => __( 'Hide "Add to Cart" button on Shop and Single product page.' ),
                'id'   => 'woow_hide_add_to_cart',
                'desc_tip' =>  false,
            ),
            'woow_hide_proceed_to_checkout' => array(
                'name' => __( 'Hide "Proceed to Checkout" Button?', 'woocommerce-order-on-whatsapp' ),
                'type' => 'checkbox',
                'desc' => __( 'Hide "Proceed to Checkout" button on Cart page.' ),
                'id'   => 'woow_hide_proceed_to_checkout',
                'desc_tip' =>  false,
            ),
            'woow_show_on_shop_single' => array(
                'name' => __( 'Show on Shop and Single page?', 'woocommerce-order-on-whatsapp' ),
                'type' => 'checkbox',
                'desc' => __( 'This will show the Order on WhatsApp button on Shop and Single product page.' ),
                'id'   => 'woow_show_on_shop_single',
                'desc_tip' =>  false,
            ),
            'section_end' => array(
                 'type' => 'sectionend',
                 'id' => 'woow_settings_section_end'
            )
        );

        return apply_filters( 'wc_settings_tab_demo_settings', $settings );
    }

    public function woow_admin_notice_review() {        
        $woow_show_after = get_option('woow_show_after');
        $rev_notice = isset($_GET['woow_rev_notice']) ? $_GET['woow_rev_notice'] : '';
        if ($rev_notice == 'later') {
            $woow_show_after = date( "d-m-Y", strtotime( "+7 days" ) );
            update_option('woow_show_after', $woow_show_after);
            update_option('woow_rev_notice_hide', 'later');
        } else if ($rev_notice == 'never') {
            delete_option('woow_show_after');
            update_option('woow_rev_notice_hide', 'never');
        }

        $rev_notice_hide = get_option('woow_rev_notice_hide');
        /*echo '<br/>Notice: '.$rev_notice_hide;
        echo '<br/>Show after: '.$woow_show_after;
        echo '<br/>Current date: '.date('d-m-Y');*/

        if ($rev_notice_hide != 'never' && strtotime(date('d-m-Y')) > strtotime($woow_show_after)) {
            
            $class = 'notice notice-info is-dismissible';
            $url = remove_query_arg(array('taction', 'tid', 'sortby', 'sortdir', 'opt'));
            $url_later = esc_url(add_query_arg('woow_rev_notice', 'later', $url));
            $url_never = esc_url(add_query_arg('woow_rev_notice', 'never', $url));

            $notice = '<p style="font-weight:normal;color:#156315">Hey, I noticed you have been using my <b>Order On Mobile for WooCommerce</b> plugin for a while now – that’s awesome!<br>Could you please do me a BIG favor and give it a 5-star rating on WordPress?<br><br>--<br>Thanks!<br>INTOLAP.<br></p>';
            
            $notice .= '<ul style="font-weight:bold;">';
            
            $notice .= '<li><a href="https://wordpress.org/support/plugin/order-on-mobile-for-woocommerce/reviews/#new-post" target="_blank">OK, you deserve it</a></li>';
            
            $notice .= '<li><a href="' . $url_later . '">Not now, maybe later</a></li>';
            $notice .= '<li><a href="' . $url_never . '">Do not remind me again</a></li>';
            
            $notice .= '</ul>';

            $notice .= '<p>By the way, if you have been thinking about upgrading to the <a href="https://www.intolap.com/product/order-on-mobile-for-woocommerce-pro/" target="_blank">PRO</a> version</p>';

            $notice .= '<p>Let your friends know about this plugin. <a href="https://api.whatsapp.com/send?text=Hey, I really like you to try Order On Mobile for Woo Commerce (PRO) plugin. Find more details at https://www.intolap.com/product/order-on-mobile-for-woocommerce-pro" class="button button-primary" style="background-color: #06d253; color: #fff;" target="_blank">Share on WhatsApp</a></p>';

            printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), $notice);
        }
    }

    public function woow_uninstall_feedback() {
        if ('plugins.php' == $GLOBALS['pagenow']){?>
            <style>
                .overlay h1 {
                  text-align: center;
                  font-family: "Trebuchet MS", Tahoma, Arial, sans-serif;
                  color: #333;
                  text-shadow: 0 1px 0 #fff;
                  margin: 50px 0;
                }

                .overlay #wrapper {
                  width: 100px;
                  margin: 0 auto;
                  background: #fff;
                  padding: 20px;
                  border: 10px solid #aaa;
                  border-radius: 15px;
                  background-clip: padding-box;
                  text-align: center;
                }

                .overlay .button {
                  font-family: Helvetica, Arial, sans-serif;
                  font-size: 13px;
                  padding: 5px 10px;
                  border: 1px solid #aaa;
                  background-color: #eee;
                  background-image: linear-gradient(top, #fff, #f0f0f0);
                  border-radius: 2px;
                  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
                  color: #666;
                  text-decoration: none;
                  text-shadow: 0 1px 0 #fff;
                  cursor: pointer;
                  transition: all 0.2s ease-out;
                }
                .overlay .button:hover {
                  border-color: #999;
                  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25);
                }
                .overlay .button:active {
                  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25) inset;
                }

                .overlay {
                  position: absolute;
                  top: 0;
                  bottom: 0;
                  left: 0;
                  right: 0;
                  background: rgba(0, 0, 0, 0.5);
                  transition: opacity 200ms;
                  visibility: hidden;
                  opacity: 0;
                }
                .overlay.light {
                  background: rgba(255, 255, 255, 0.5);
                }
                .overlay .cancel {
                  position: absolute;
                  width: 100%;
                  height: 100%;
                  cursor: default;
                }
                .overlay:target {
                  visibility: visible;
                  opacity: 1;
                }

                .overlay .popup {
                  margin: 75px auto;
                  padding: 20px;
                  background: #fff;
                  border: 1px solid #666;
                  width: 500px;
                  box-shadow: 0 0 50px rgba(0, 0, 0, 0.5);
                  position: relative;
                }
                .overlay .light .popup {
                  border-color: #aaa;
                  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.25);
                }
                .overlay .popup h2 {
                  margin-top: 0;
                  color: #666;
                  font-family: "Trebuchet MS", Tahoma, Arial, sans-serif;
                }
                .overlay .popup .close {
                  position: absolute;
                  width: 20px;
                  height: 20px;
                  top: 20px;
                  right: 20px;
                  opacity: 0.8;
                  transition: all 200ms;
                  font-size: 24px;
                  font-weight: bold;
                  text-decoration: none;
                  color: #666;
                }
                .overlay .popup .close:hover {
                  opacity: 1;
                }
                .overlay .popup .content {
                  max-height: 400px;
                  overflow: auto;
                }
                .overlay .popup p {
                  margin: 0 0 1em;
                }
                .overlay .popup p:last-child {
                  margin: 0;
                }
            </style>
            <div id="popup1" class="overlay">
                <div class="popup">
                    <h2>We request your feedback</h2>
                    <a class="close" href="#">&times;</a>
                    <div class="content">
                        <p>If you have a moment, please let us know why you are deactivating. We would like to help you in fixing the issue.</p>
                        <form method="POST" id="uninstall-feedback">
                            <input type="hidden" value="<?php echo bloginfo('url');?>" name="site_url" />
                            <input type="hidden" value="<?php echo bloginfo('version');?>" name="site_version" />
                            <input type="hidden" value="<?php echo bloginfo('admin_email')?>" name="admin_email" />
                            <input type="hidden" value="<?php echo $this->plugin_name;?>" name="plugin_name" />
                            <ul class="reason" id="reason" style="width: 95%;">
                                <li><label><input type="radio" name="reason" value="I couldn't understand how to make it work">I couldn't understand how to make it work</label></li>
                                <li><label><input type="radio" name="reason" value="I found a better plugin">I found a better plugin</label></li>
                                <li><label><input type="radio" name="reason" value="The plugin is great, but I need specific feature that you don't support">The plugin is great, but I need specific feature that you don't support</label></li>
                                <li><label><input type="radio" name="reason" value="The plugin is not working">The plugin is not working</label></li>
                                <li><label><input type="radio" name="reason" value="It's not what I was looking for">It's not what I was looking for</label></li>
                                <li><label><input type="radio" name="reason" value="The plugin didn't work as expected">The plugin didn't work as expected</label></li>
                                <li><label><input type="radio" name="reason" value="It's a temporary deactivation. I'm just debugging as issue">It's a temporary deactivation. I'm just debugging as issue</label></li>
                                <li><label><input type="radio" name="reason" value="Other">Other</label></li>
                            </ul>
                            <textarea name="remark" id="remark" style="width: 95%" placeholder="Remarks (optional)"></textarea>
                            <br/>
                            <span style="color: red;">By clicking this button, you agree with the storage and handling of your data (site url, site version, admin email) by this website. This data we will use only to enhance the user experience (GDPR Compliance)</span>
                            <br/>
                            <input type="button" name="submit-feedback" id="submit-feedback" value="Submit Feedback"/>
                            <input type="button" name="skip-feedback" id="skip-feedback" value="Skip &amp; Deactivate"/>
                        </form>
                    </div>
                </div>
            </div>
            <script type="text/javascript">
                 (function($){
                      $(document).ready( function(){
                        var deactivationUrl = $('[data-slug=order-on-mobile-for-woocommerce] span.deactivate a').attr('href');
                        // alert(deactivationUrl);
                        $('[data-slug=order-on-mobile-for-woocommerce] span.deactivate a').attr('href','#popup1');
                        $('form#uninstall-feedback input#submit-feedback').click(function(){

                            $('input#submit-feedback').attr('disabled', true);                          
                            $.ajax({
                                url: "http://www.intolap.com/wp-json/intolap-theme/v1/submit-feedback/",
                                type: "POST",
                                // dataType: "jsonp",
                                data: $('form#uninstall-feedback').serialize(),
                                success: function(response) {
                                    // alert(response); return false;
                                    
                                    if (response == '1') {
                                        window.location = '<?php echo admin_url()?>' + deactivationUrl;
                                    } else {
                                        alert('Please refresh the page and try deactivating again.');
                                    }
                                    $('input#submit-feedback').removeAttr('disabled');
                                }
                            });

                        });

                        $('form#uninstall-feedback input#skip-feedback').click(function(){
                            window.location = '<?php echo admin_url()?>'+deactivationUrl;
                        });
                      });
                    })(jQuery);
            </script>
    <?php }
    }

}