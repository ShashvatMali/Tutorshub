<?php
/**
 *
 * Student profile personal information
 *
 * @package     Tuturn
 * @subpackage  Tuturn/Templates/Single_tuturn_Instructor
 * @author      Amentotech <info@amentotech.com>
 * @link       https://themeforest.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
*/
global $post, $tuturn_settings;
$page_url      = set_url_scheme(get_permalink());
$show_address_type          = !empty($tuturn_settings['show_address_type']) ? $tuturn_settings['show_address_type'] : 'address';
$profile_state              = !empty($tuturn_settings['profile_state']) ? $tuturn_settings['profile_state'] : false;
$username       = tuturn_get_username($post->ID);
$tagline       = !empty($profile_details['tagline']) ? $profile_details['tagline'] : '';
$languages     = !empty($profile_details['languages']) ? $profile_details['languages'] : '';
$contact_info  = !empty($profile_details['contact_info']) ? $profile_details['contact_info'] : array();
$phone         = !empty($contact_info['phone']) ? $contact_info['phone'] : '';
$email_address = !empty($contact_info['email_address']) ? $contact_info['email_address'] : '';
$skypeid       = !empty($contact_info['skypeid']) ? $contact_info['skypeid'] : '';
$whatsapp      = !empty($contact_info['whatsapp_number']) ? $contact_info['whatsapp_number'] : '';
$website       = !empty($contact_info['website']) ? $contact_info['website'] : '';
$location      = get_post_meta( $post->ID,'_address',true );
$country       = get_post_meta( $post->ID,'_country',true );
$zipcode       = get_post_meta( $post->ID,'_zipcode',true );
$is_verified   = get_post_meta( $post->ID,'_is_verified',true );
$avatar = apply_filters(
    'tuturn_avatar_fallback', tuturn_get_user_avatar(array('width' => 400, 'height' => 400), $post->ID), array('width' => 100, 'height' => 100)
);

if (!empty($show_address_type) && $show_address_type != 'address' && $profile_state == true) {
    $list_adress        = array();
    $country_region     = get_post_meta( $post->ID, '_country_region', true);
    $_country           = get_post_meta( $post->ID, '_country', true);
    $_state             = get_post_meta( $post->ID, '_state', true);
    $_city              = get_post_meta( $post->ID, '_city', true);

    if(!empty($_city)){
        $list_adress[]    = $_city;
    }

    $states             = !empty($country_region) ? tuturn_country_array($country_region,'') : array();

    if(!empty($states) && !empty($states[strtoupper($_state)])){
        $list_adress[]      = $states[strtoupper($_state)];
    }
   
    if (!empty($show_address_type) && $show_address_type == 'city_state_country' && !empty($_country)){
        $countries 		    = tuturn_country_array();
        $list_adress[]      = !empty($countries[strtoupper($_country)]) ? $countries[strtoupper($_country)] : $_country;
    }
    
    $location   = !empty($list_adress) ? implode(', ',$list_adress) : $location;
}

?>
<div class="tu-tutorprofilewrapp">
    <div class="tu-profileview">
        <?php if(!empty($avatar)){?>
            <figure>
                <img src="<?php echo esc_url($avatar);?>" alt="<?php echo esc_attr($username);?>">
            </figure>
        <?php }?>
        <div class="tu-protutorinfo">
            <div class="tu-protutordetail">
                <div class="tu-productorder-content">
                    <?php if(!empty($avatar)){?>
                        <figure>
                            <img src="<?php echo esc_url($avatar);?>" alt="<?php echo esc_attr($username);?>">
                        </figure>
                    <?php }?>
                    <div class="tu-product-title">
                        <?php if(!empty($username) || !empty($is_verified)){ ?>
                            <h3>
                                <?php if(!empty($username)){ echo esc_html($username); }?>
                                <?php if(!empty($is_verified)){?><i class="icon icon-check-circle  tu-icongreen"></i><?php } ?>
                            </h3>
                        <?php }

                         if(!empty($tagline)){?>
                            <h5><?php echo esc_html($tagline)?></h5>
                        <?php } ?>
                    </div>
                </div>
                <div class="tu-infoprofile">
                    <?php
                    if(!empty($location)){?>
                        <ul class="tu-tutorreview">                   
                            <li>
                                <span><i class="icon icon-map-pin"></i><em><?php echo esc_html($location)?></em></span>
                            </li>
                        </ul>
                    <?php } ?>
                    <?php if(!empty($languages)){?>
                        <div class="tu-detailitem">
                            <h6><?php esc_html_e('Languages I know','tuturn')?> </h6>
                            <div class="tu-languagelist">
                                <ul class="tu-languages">
                                    <?php 
                                    $count          = 6;
                                    $counter_langs  = 0;
                                    $language_arr 	= array();
                                    foreach($languages as $language ){
                                        $counter_langs++;
                                        if($counter_langs <= $count){
                                        ?>
                                            <li> <?php echo esc_html($language); ?> </li>
                                        <?php } else{
                                            $language_arr[]  = 	esc_html($language);
                                        }
                                    }    
                                    if(($counter_langs) > $count && !empty($language_arr)){?>
                                        <li>
                                            <a class="tu-showmore tu-tooltip-tags" href="javascript:void(0);"  data-tippy-trigger="click" data-template="tu-industrypro" data-tippy-interactive="true" data-tippy-placement="top-start"> <?php echo sprintf( esc_html__( '+%02d more', 'tuturn' ), intval($counter_langs) - $count  ); ?></a>
                                            <div id="tu-industrypro" class="tu-tippytooltip d-none">
                                                <div class="tu-selecttagtippy tu-tooltip ">
                                                    <ul class="tu-posttag tu-posttagv2">
                                                        <?php foreach($language_arr as $item){ ?>
                                                            <li>
                                                                <a href="javascript:void(0);"><?php echo esc_html($item) ?></a>
                                                            </li>
                                                        <?php } ?>
                                                    </ul>
                                                </div>
                                            </div>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <div class="tu-actionbts">
        <?php if(!empty($page_url ) ){?>
            <a href="javascript:void(0);" class="tu-tiny-url"><i class="icon icon-globe"></i><span><p id="urlcopy"><?php echo esc_html($post->post_name)?></p><i id="copyurl" class="icon icon-copy copytext"></i></span></a>
        <?php } ?>
        <ul class="tu-profilelinksbtn"> 
            <?php if(apply_filters( 'tuturn_chat_solution_guppy',false ) === true){
                if(is_user_logged_in()){
                    $tuturn_inbox_url   = apply_filters('tuturn_guppy_inbox_url', $user_id);
                } else {
                    $tuturn_inbox_url   = tuturn_get_page_uri('login');
                }?>
                <li><a href="<?php echo esc_url($tuturn_inbox_url);?>" class="tu-secbtn"><?php esc_html_e('Let\'s talk now','tuturn') ?></a></li>
            <?php }?>  
        </ul>
    </div>
</div>
<?php
if(!empty($page_url ) ){
    $script = '
        function makeTinyUrl(url)
        {
            jQuery.get("https://tinyurl.com/api-create.php?url=" + url, function(shorturl){        
                jQuery("#urlcopy").html("<a target=_blank href="+shorturl+">"+shorturl+"</a>");
            }); 
            
        }
        makeTinyUrl("'.$page_url.'");
        
    ';
    wp_add_inline_script('tuturn-public', $script, 'after');
}
