<?php

/**
 * Instructor booking details
 *
 * @package     Tuturn
 * @subpackage  Tuturn/templates/dashboard
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
 */
if (!empty($args) && is_array($args)) {
    extract($args);
}
$time_format            = get_option('time_format');
$booked_information     = !empty($booked_data) ? ($booked_data) : array();
$products               = !empty($booked_information['product']) ? $booked_information['product'] : array();
$user_info              = !empty($booked_information['information']) ? $booked_information['information'] : array();
$booked_slots           = !empty($booked_information['booked_slots']) ? tuturn_get_choosed_date_slots($booked_information['booked_slots']) : array();
if (!empty($products)) { ?>
    <div class="tu-wizserviceslist mCustomScrollbar">
        <div class="tu-box">
            <?php if (!empty($products['name']) && $products['price'] > 0) { ?>
                <ul class="tu-checkout">
                    <li>
                        <h5 class="tu-checkout_title"><i class="icon icon-briefcase"></i><?php esc_html_e('Booking Service','tuturn'); ?></h5>
                    </li>
                    <li>
                        <span><?php echo esc_html($products['name']);?></span>
                        <h6><?php tuturn_price_format($products['price']);?></h6>
                    </li>
                </ul>
            <?php } ?>
            <?php do_action('tuturn_add_extra_checkout',$booked_information );?>
            <?php if(!empty($booked_slots) && is_array($booked_slots)){ ?>
                <ul class="tu-checkout">
                    <li>
                        <h5 class="tu-checkout_title"><i class="icon icon-calendar"></i><?php esc_html_e('Booking date and slots','tuturn'); ?></h5>
                    </li>
                    <?php foreach($booked_slots as $date=>$time_slot){ ?>
                        <li>
                            <?php if(!empty($time_slot)){ ?>
                            <ul class="tu-innter-slots">
                                <li><?php echo $date; ?></li>
                                <?php
                                foreach($time_slot as $innterTime_slots){ ?>
                                    <li>
                                        <span><?php echo esc_html($innterTime_slots['slotStart_time']); ?> - <?php echo esc_html($innterTime_slots['slotEnd_time']); ?></span>
                                    </li>
                                    <?php } ?>
                            </ul>
                            <?php } ?>
                        </li>
                    <?php } ?>
                </ul>
            <?php } ?>
            <?php if (!empty($user_info['info_someone_else']) && $user_info['info_someone_else'] === 'on' ) {
                $info_first_name    = !empty($user_info['info_first_name']) ? $user_info['info_first_name'] : '';
                $info_last_name     = !empty($user_info['info_last_name']) ? $user_info['info_last_name'] : '';
                $info_full_name     = !empty($user_info['info_full_name']) ? $user_info['info_full_name'] : '';
                $info_phone         = !empty($user_info['info_phone']) ? $user_info['info_phone'] : '';
                $info_relation      = !empty($user_info['info_relation']) ? $user_info['info_relation'] : '';
                $info_desc          = !empty($user_info['info_desc']) ? $user_info['info_desc'] : '';
                ?>
                <ul class="tu-checkout">
                    <li>
                        <h5 class="tu-checkout_title"><i class="icon icon-user"></i><?php esc_html_e('User personal details','tuturn'); ?></h5>
                    </li>
                    <?php if (!empty($info_first_name)) { ?>
                        <li>
                            <span><?php esc_html_e('First name','tuturn'); ?>:</span>
                            <h6><?php echo esc_html($info_first_name);?></h6>
                        </li>
                    <?php } ?>
                    <?php if (!empty($info_last_name)) { ?>
                        <li>
                            <span><?php esc_html_e('Last name','tuturn'); ?>:</span>
                            <h6><?php echo esc_html($info_last_name);?></h6>
                        </li>
                    <?php } ?>
                    <?php if (!empty($info_full_name)) { ?>
                        <li>
                            <span><?php esc_html_e('Full name','tuturn'); ?>:</span>
                            <h6><?php echo esc_html($info_full_name);?></h6>
                        </li>
                    <?php } ?>
                    <?php if (!empty($info_email)) { ?>
                        <li>
                            <span><?php esc_html_e('Email address','tuturn'); ?>:</span>
                            <h6><?php echo esc_html($info_email);?></h6>
                        </li>
                    <?php } ?>
                    <?php if (!empty($info_phone)) { ?>
                        <li>
                            <span><?php esc_html_e('Phone name','tuturn'); ?>:</span>
                            <h6><?php echo esc_html($info_phone);?></h6>
                        </li>
                    <?php } ?>
                    <?php if (!empty($info_relation)) { ?>
                        <li>
                            <span><?php esc_html_e('Relation with you','tuturn'); ?>:</span>
                            <h6><?php echo esc_html($info_relation);?></h6>
                        </li>
                    <?php } ?>
                    <?php if (!empty($info_desc)) { ?>
                        <li>
                            <p><?php echo esc_html($info_desc);?></p>
                        </li>
                    <?php } ?>
                </ul>
            <?php } ?>
        </div>
    </div>    
<?php }
