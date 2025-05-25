<?php
/**
 *
 * The template used for displaying student data
 *
 * @package     Tuturn
 * @subpackage  Tuturn/Templates/Single_tuturn_Instructor
 * @author      Amentotech <info@amentotech.com>
 * @link       https://themeforest.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
*/
global $post,$tuturn_settings,$current_user;
$student_user_id        = tuturn_get_linked_profile_id($post->ID, 'post');
$teaching_preference    = get_post_meta( $post->ID, 'teaching_preference', true );
$teaching_preference    = !empty($teaching_preference) ? $teaching_preference :  array(); 
$contact_info           = !empty($profile_details['contact_info']) ? $profile_details['contact_info'] : '';
$conteact_details       = !empty($tuturn_settings['hide_conteact_details']) ? $tuturn_settings['hide_conteact_details'] : false;
?>
<aside class="tu-asidedetail">
    <a href="javascript:void(0)" class="tu-dbmenu"><i class="icon icon-headphones"></i></a>
    <div class="tu-asidebar">
        <div class="tu-asideinfo text-center">
            <h6><?php esc_html_e('I would like to get teaching service direct at','tuturn')?></h6>
        </div>
        <ul class="tu-featureinclude">
            <?php if(!empty($teaching_preference) && in_array('home', $teaching_preference)){ ?>
                <li>
                    <span class="icon icon-home tu-colorgreen"> <i><?php esc_html_e('My home','tuturn')?></i> </span>
                    <em class="fa fa-check-circle tu-colorgreen"></em>
                </li>
                <?php
            }

            if(!empty($teaching_preference) && in_array('student_home', $teaching_preference)){?>
               <li>
                    <span class="icon icon-map-pin tu-colorblue"> <i><?php esc_html_e('Teacher\'s home','tuturn')?></i> </span>
                    <em class="fa fa-check-circle tu-colorgreen"></em>
                </li>
                <?php
            }
            
            if(!empty($teaching_preference) && in_array('online', $teaching_preference)){?>       
                <li>
                    <span class="icon icon-video tu-colororange"> <i><?php esc_html_e('Online','tuturn')?></i> </span>
                    <em class="fa fa-check-circle tu-colorgreen"></em>
                </li>
            <?php }?>
        </ul>
        <?php if(!empty($contact_info) && is_array($contact_info) ){?>
            <div class="tu-contactbox">
                <h6><?php esc_html_e('Contact details','tuturn')?></h6>
                <?php if(!empty($contact_info)){?>
                    <ul class="tu-listinfo">
                        <?php
                        
                        $phone              = !empty($contact_info['phone']) ? esc_html($contact_info['phone']) : '' ;
                        $skypeid            = !empty($contact_info['skypeid']) ? esc_html($contact_info['skypeid']) : '' ;
                        $website            = !empty($contact_info['website']) ? esc_url($contact_info['website']) : '' ;
                        $website_lebel      = tuturn_removeProtocol($website);
                        $email_address      = !empty($contact_info['email_address']) ? esc_html($contact_info['email_address']) : '' ;
                        $whatsapp_number    = !empty($contact_info['whatsapp_number']) ? esc_html($contact_info['whatsapp_number']) : '' ;
                        
                        $webiste_href	    = 'href="javascript:void(0);"';      
                        $subject            = "This is subject";
                        $user               = get_current_user_id();
                        $user_info          = get_userdata($user);
                        if(!empty($website)){
                            $webiste_href	= 'href="'.esc_url($website).'" target="_blank"';
                        }
                        
                        $show_conteact_details  = 'show';
                        if( !empty($conteact_details) ){
                            if( is_user_logged_in() && $args['userType'] == 'instructor' ){
                                $post_meta_data	= array(
                                    'instructor_id' 		=> $user,
                                    'student_id'            => $student_user_id
                                );
                                $previous_order_key_query   = tuturn_get_total_posts_by_multiple_meta('shop_order',array('wc-completed'),$post_meta_data);
                                $show_conteact_details      = isset($previous_order_key_query->found_posts) ? intval($previous_order_key_query->found_posts) : 0;
                                if(empty($show_conteact_details)){
                                    $show_conteact_details  = 'hide';
                                }
                            }
                        }

                        if( 
                            (!empty($show_conteact_details) && $show_conteact_details === 'hide') 
                                || !is_user_logged_in() 
                                || (empty($args['package_info']['allowed']) &&  $args['userType'] == 'instructor' ) 
                                || ($args['userType'] == 'student' && $current_user->ID !== $student_user_id)
                            ){

                            $whatsapp_number    = tuturn_maskPhone($whatsapp_number);
                            $phone              = tuturn_maskPhone($phone);
                            $skypeid            = tuturn_maskSkypeAddress($skypeid);
                            $email_address      = tuturn_maskEmailAddress($email_address);    
                            $website_lebel      = tuturn_maskwebisteURL($website_lebel); 
                            $webiste_href       = '';
                        }
                        ?>
                        <?php if(!empty($phone)){?>
                            <li>
                                <span class="tu-bg-maroon"><i class="icon icon-phone-call "></i></span>
                                <h6><?php echo do_shortcode($phone)?></h6>
                            </li>
                        <?php }
                        if(!empty($email_address)) {?> 
                            <li>
                                <span class="tu-bg-voilet"><i class="icon icon-mail"></i></span>
                                <h6><?php echo do_shortcode($email_address)?></h6>
                            </li>
                        <?php } 
                        if(!empty($skypeid)){?>
                            <li>
                                <span class="tu-bg-blue"><i class="fab fa-skype"></i></span>
                                <h6><?php echo do_shortcode($skypeid)?></h6>
                            </li>
                        <?php } 
                        if(!empty($whatsapp_number)){?>
                            <li>
                                <span class="tu-bg-green"><i class="fab fa-whatsapp"></i></span>
                                <h6><?php echo do_shortcode($whatsapp_number)?></h6>
                            </li>
                        <?php } 
                        if(!empty($website_lebel)){?>
                            <li>
                                <span class="tu-bg-orange"><i class="icon icon-printer"></i></span>
                                <a <?php echo do_shortcode($webiste_href);?> ><?php echo do_shortcode($website_lebel)?></a>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
</aside>