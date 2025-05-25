<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.intolap.com/
 * @since    1.0.0
 *
 * @package    Woocommerce_Order_On_Whatsapp
 * @subpackage Woocommerce_Order_On_Whatsapp/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Woocommerce_Order_On_Whatsapp
 * @subpackage Woocommerce_Order_On_Whatsapp/public
 * @author     INTOLAP <info@intolap.com>
 */
class Woocommerce_Order_On_Whatsapp_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;


	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * An instance of this class should be passed to the run() function
		 * defined in Woocommerce_Order_On_Whatsapp_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woocommerce_Order_On_Whatsapp_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woocommerce-order-on-whatsapp-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * An instance of this class should be passed to the run() function
		 * defined in Woocommerce_Order_On_Whatsapp_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woocommerce_Order_On_Whatsapp_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woocommerce-order-on-whatsapp-public.js', array( 'jquery' ), $this->version, false );

		
		wp_localize_script( 
			$this->plugin_name, 
			'ajax_object', 
			array( 
				'ajaxurl' => admin_url( 'admin-ajax.php' ), 
				// 'woow_thankyou' => get_post_permalink( get_option( 'woow_select_thankyou' ) ) 
			) 
		);

	}


	/**
	 * This function creates the Order On WhatsApp button on shop page's product loop.
	 * This function also removes the proceed to checkout button from cart page and mini cart 
	 * widget based on the admin settings of this plugin.
	 * @since    1.0.0
	 */
	public function woow_whatsapp_button_shop_single(){
		if (get_option('woow_show_on_shop_single')=='yes') {

			global $product;

			$currency= get_option('woocommerce_currency');
			
			$custom_message= get_option('woow_custom_message');
		
			if($custom_message== '') $message= "I want to buy->";
		
			else $message= $custom_message;

			$message.= "\r\nProduct Name: ".$product->get_name()."\r\nQuantity: 1\r\nPrice: ".$product->get_price()." ".$currency."\r\nUrl: ".get_post_permalink($product->get_id())."\r\n";
			
			$whatsapp_number= get_option('woow_whatsapp_number');

			$url = 'https://api.whatsapp.com/send?phone='.$whatsapp_number.'&text='.urlencode($message);
	?>
			<a target="_blank"
			href="<?php echo $url;?>"
			class="woow_whatsapp_button" data-source="shop"><img src="<?php echo plugin_dir_url(__FILE__); ?>/img/whatsapp-button.png"></a>
	<?php
		}
	}


	public function woow_remove_add_to_cart_buttons(){
		if (get_option('woow_hide_add_to_cart')=='yes') {
			$priority = has_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart');
			remove_action(
				'woocommerce_after_shop_loop_item', 
				'woocommerce_template_loop_add_to_cart',
				$priority
			);
			
			$priority = has_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart');
			remove_action(
				'woocommerce_single_product_summary', 
				'woocommerce_template_single_add_to_cart',
				$priority
			);
		}
	}

	public function woow_remove_proceed_to_checkout_buttons(){
		if (get_option('woow_hide_proceed_to_checkout')=='yes') {
			$priority = has_action('woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout');
			remove_action( 
	        	'woocommerce_proceed_to_checkout',
	        	'woocommerce_button_proceed_to_checkout', 
	        	$priority 
	        );

			$priority = has_action('woocommerce_widget_shopping_cart_buttons', 'woocommerce_widget_shopping_cart_button_view_cart');
			remove_action( 
				'woocommerce_widget_shopping_cart_buttons', 
				'woocommerce_widget_shopping_cart_button_view_cart', 
				$priority
			);

			$priority = has_action('woocommerce_widget_shopping_cart_buttons', 'woocommerce_widget_shopping_cart_proceed_to_checkout');
			remove_action( 
				'woocommerce_widget_shopping_cart_buttons', 
				'woocommerce_widget_shopping_cart_proceed_to_checkout', 
				$priority
			);
		}
	}


	/**
	 * This function creates the Order On WhatsApp button on cart page.
	 * This function also removes the proceed to checkout button from cart page and mini cart 
	 * widget based on the admin settings of this plugin.
	 * @since    1.0.0
	 */
	public function woow_whatsapp_button_cart(){
	?>
		<div class="loader">
			<div class="lds-dual-ring"></div>
			<p>Processing information</p>
		</div>
		<a id="order_on_whatsapp_cart" class="woow_whatsapp_button" data-source="cart"><img src="<?php echo plugin_dir_url(__FILE__); ?>/img/whatsapp-button.png"></a>
	<?php
	}


	/**
	 * This function creates the Order On WhatsApp button API URL grabing the cart contents.
	 * @since    1.0.0
	 */
	public function woow_get_cart_contents_callback(){

		$items= WC()->cart->get_cart();

		$custom_message= get_option('woow_custom_message');

		if($custom_message== '') $message= "I want to buy->";

		else $message= $custom_message;

		$currency= get_option('woocommerce_currency');

        foreach($items as $item ) { 
            $_product =  wc_get_product( $item['product_id']);     
            $product_name= $_product->get_name();
            $qty= $item['quantity'];            
            $price= $item['line_subtotal'];
            $product_url= get_post_permalink($item['product_id']);
        
        	$message.= "Product Name: ".$product_name."\r\nQuantity: ".$qty."\r\nPrice: ".$price." ".$currency."\r\nUrl: ".$product_url."\r\n";
        }
        
        $message.="Thank You";

        $whatsapp_number= get_option('woow_whatsapp_number');
        
        $url = 'https://api.whatsapp.com/send?phone='.$whatsapp_number.'&text='.urlencode($message);

        // WC()->cart->empty_cart();

        wp_die($url);
     }
}


