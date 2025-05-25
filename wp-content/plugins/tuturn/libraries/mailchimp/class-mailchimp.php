<?php
if (!class_exists('Tuturn_MailChimp')) {

    class Tuturn_MailChimp {

        function __construct() {
            add_action('wp_ajax_nopriv_tuturn_subscribe_mailchimp', array(&$this, 'tuturn_subscribe_mailchimp'));
            add_action('wp_ajax_tuturn_subscribe_mailchimp', array(&$this, 'tuturn_subscribe_mailchimp'));
        }
		
		public function tuturn_mailchimp_form($title,$details,$button_label) {
			$counter = 0;
            $counter++;
            ?>
                <?php if(!empty($button_label)){ ?>
                <div>
                    <?php if(!empty($title)){?>
                    <h5>
                        <?php echo esc_html($title)?> 
                    </h5>
                    <?php } ?>
                    <form class="tu-formtheme tu-formnewsletter comingsoon-newsletter" id="mailchimpwidget_<?php echo intval($counter); ?>">
                        <div class="collapse tu-collapseitem show">
                            <div class="tu-inputiconbtn tu-inputiconbtnright">
                                <input type="email"name="email" placeholder="<?php esc_attr_e('Enter your email', 'tuturn'); ?>" class="form-control tu-showplaceholder" required="">
                                <a href="javascript:void(0);" class="tu-search-icon"><i class="icon icon-mail"></i></a>
                            </div>
                            <?php if(!empty($button_label)){?>
                                <a href="javascript:void(0);" class="tu-primbtn subscribe_me tu-primbtn-icon"><?php echo esc_html($button_label)?></a>
                            <?php } ?>
                        </div>
                    </form>
                </div>
                <?php
                } else {
                    if(!empty($title)) {?>
                        <h5 class="tu-footertitle"><?php echo esc_html($title)?></h5>
                    <?php } 
                    if(!empty($details)){?>
                        <p><?php echo esc_html($details)?></p>
                    <?php } ?>
                    <form class="tu-formtheme tu-formnewsletter comingsoon-newsletter" id="mailchimpwidget_<?php echo intval($counter); ?>">
                        <div class="tu-inputbtn">
                            <input type="email"name="email" placeholder="<?php esc_attr_e('Enter your email', 'tuturn'); ?>" class="form-control">
                            <a href="javascript:void(0);" class="tu-primbtn-icon subscribe_me tu-primbtn-orange"><span><?php esc_attr_e('Subscribe now', 'tuturn'); ?></span> <i class="icon icon-send"></i></a>
                        </div>
                    </form>
                <?php }?>
              
             <?php
        }
		
        /**
         * @get Mail chimp list
         *
         */
        public function tuturn_mailchimp_list($apikey) {
			if ( $apikey <> '' && $apikey !== 'Add your key here' ) {
				$apikey	= $apikey;
			} else{
				return '';
			}
			
            $MailChimp = new Tuturn_OATH_MailChimp($apikey);
            $mailchimp_list = $MailChimp->tuturn_call('lists/list');
            return $mailchimp_list;
        }

        /**
         * @get Mail chimp list
         *
         */
        public function tuturn_subscribe_mailchimp() {
            global $tuturn_settings;
            $json               = array();
			$mailchimp_key      = !empty( $tuturn_settings['mailchimp_key'] ) ? $tuturn_settings['mailchimp_key']  : '';
			$mailchimp_list     = !empty( $tuturn_settings['mailchimp_list'] ) ? $tuturn_settings['mailchimp_list']  : '';
			
            $post_data = !empty($_POST['data']) ? $_POST['data'] : array();
            parse_str($post_data, $data);

            if (empty($data['email'])) {
                $json['type'] 		= 'error';
                $json['title'] 		= esc_html__('Email', 'tuturn');
                $json['message'] 	= esc_html__('Email address is required.', 'tuturn');
                wp_send_json($json);
            }

            if (empty(is_email( $data['email']))) {
                $json['type'] 		= 'error';
                $json['title'] 		= esc_html__('Valid Email', 'tuturn');
                $json['message'] 	= esc_html__('Please enter valid email.', 'tuturn');
                wp_send_json($json);
            }

			if (isset($data['email']) && !empty($data['email']) && $mailchimp_key != '') {
                
				if ($mailchimp_key <> '' && $mailchimp_key !== 'Add your key here') {
                    $MailChimp = new Tuturn_OATH_MailChimp($mailchimp_key);
                } else{
					$json['type'] 		= 'error';
                    $json['title'] 		= esc_html__('Mailchimp', 'tuturn');
                	$json['message'] 	= esc_html__('Some error occur,please try again later.', 'tuturn');
					wp_send_json($json);
				}

                $email = $data['email'];

                if (isset($data['fname']) && !empty($data['fname'])) {
                    $fname = $data['fname'];
                } else {
                    $fname = '';
                }

                if (isset($data['lname']) && !empty($data['lname'])) {
                    $lname = $data['lname'];
                } else {
                    $lname = '';
                }

                if (trim($mailchimp_list) == '') {
                    $json['type']       = 'error';
                    $json['title'] 		= esc_html__('Mailchimp List', 'tuturn');
                    $json['message']    = esc_html__('No list selected yet! please contact administrator', 'tuturn');
                    wp_send_json($json);
                }

                //https://apidocs.mailchimp.com/api/1.3/listsubscribe.func.php
                $result = $MailChimp->tuturn_call('lists/subscribe', array(
                    'id' => $mailchimp_list,
                    'email' => array('email' => $email),
                    'merge_vars' => array('FNAME' => $fname, 'LNAME' => $lname),
                    'double_optin' => false,
                    'update_existing' => false,
                    'replace_interests' => false,
                    'send_welcome' => true,
                ));
				
                if ($result <> '') {
                    if (isset($result['status']) and $result['status'] == 'error') {
                        $json['type'] 		= 'error';
                        $json['message'] 	= $result['error'];
                    } else {
                        $json['type'] 		= 'success';
                        $json['message'] 	= esc_html__('Subscribe Successfully', 'tuturn');
                    }
                }
				
            } else {
                $json['type'] = 'error';
                $json['title'] 		= esc_html__('Mailchimp', 'tuturn');
                $json['message'] = esc_html__('Some error occur,please try again later.', 'tuturn');
            }
			
            wp_send_json($json);
        }

    }

    new Tuturn_MailChimp();
}