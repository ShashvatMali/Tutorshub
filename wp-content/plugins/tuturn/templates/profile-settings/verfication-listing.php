<?php

/**
 * verfication listing
 *
 * @package     Tuturn
 * @subpackage  Tuturn/Templates/Profile_Settings
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
 */
global $tuturn_settings,$current_user;
$is_resubmit = !empty($tuturn_settings['resubmit_verification']) ? $tuturn_settings['resubmit_verification'] : false;
$identity_verification          = !empty($tuturn_settings['identity_verification']) ? $tuturn_settings['identity_verification'] : '';

$userType            = apply_filters('tuturnGetUserType', $current_user->ID);
if (!empty($userType) && $userType == 'student') {
    if(!empty($identity_verification) && ($identity_verification === 'tutors' || $identity_verification === 'none')){
        return;
    }
}else{
    if(!empty($identity_verification) && ($identity_verification === 'students' || $identity_verification === 'none')){
        return;
    }
}

if (!empty($args) && is_array($args)) {
    extract($args);
}
$sort_by        = !empty($_GET['sort_by']) ? esc_html($_GET['sort_by']) : '';
$posts_per_page = get_option('posts_per_page') ? get_option('posts_per_page') : 10;
$pg_page        = get_query_var('page') ? get_query_var('page') : 1;
$pg_paged       = get_query_var('paged') ? get_query_var('paged') : 1;
$paged          = max($pg_page, $pg_paged);

$args = array(
    'posts_per_page' => $posts_per_page,
    'paged'          => $paged,
    'post_type'      => 'user-verification',
    'post_status'    => 'any',
    'orderby'        => 'date',
    'author__in'     => $user_identity,
);
$user_verfication_list  = new WP_Query($args);
$total_posts            = $user_verfication_list->found_posts;
$add_page_url           = tuturn_dashboard_page_uri($user_identity, 'user-verification');

$identity_verified  = get_user_meta($user_identity, 'identity_verified', true);
$identity_verified  = !empty($identity_verified) ? $identity_verified : '';

$button_show = '';
if (!empty($is_resubmit) && ($total_posts == 0 || $total_posts >= 1)) {
    $button_show = true;
} elseif (empty($is_resubmit) && $total_posts == 0) {
    $button_show = true;
} elseif (empty($is_resubmit) && $total_posts >= 1) {
    $button_show = false;
}

/* Array for verification messages */
$tuturn_args        = array(
    'profile_id'        => $profile_id,
    'user_identity'     => $user_identity,
    'user_name'         => $user_name,
    'profile_details'   => $profile_details,
    'userType'          => $userType,
    'current_page_link' => $current_page_link,
);

?>
<div class="tu-invoice-listings">
<?php do_action('tuturn_profile_identity_verification_notice', $tuturn_args); ?>
    <div class="tu-dbwrapper">
        <div class="tu-dbtitle">
            <h3><?php esc_html_e('Identity Verification', 'tuturn'); ?></h3>
            <?php if (!empty($button_show)) { ?>
                <a href="<?php echo esc_url($add_page_url) ?>" class="tu-primbtn"><?php esc_html_e('Add new', 'tuturn'); ?></a>
            <?php } ?>
        </div>
        <?php
        if (!empty($total_posts) && $total_posts > 0) {
        ?>
            <div class="tu-invoicestable">
                <table class="table dhb-table">
                    <thead>
                        <tr>
                            <th scope="col"><?php esc_html_e('Name', 'tuturn'); ?></th>
                            <th scope="col"><?php esc_html_e('Date', 'tuturn'); ?></th>
                            <th scope="col"><?php esc_html_e('Email', 'tuturn'); ?></th>
                            <th scope="col"><?php esc_html_e('Status', 'tuturn'); ?></th>
                            <th scope="col"><?php esc_html_e('Option', 'tuturn'); ?></th>
                            <?php if (!empty($is_resubmit)) { ?>
                                <th scope="col"><?php esc_html_e('Action', 'tuturn'); ?></th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($user_verfication_list->have_posts()) {
                            while ($user_verfication_list->have_posts()) {
                                $user_verfication_list->the_post();
                                global $post;
                                $verification_info = get_post_meta($post->ID, 'verification_info', true);
                                if (!empty($verification_info['info'])) {
                                    $author_name    = !empty($verification_info['info']['name']) ? esc_html($verification_info['info']['name']) : '';
                                    $author_email   = !empty($verification_info['info']['email_address']) ? esc_html($verification_info['info']['email_address']) : '';
                                    $post_date      = get_the_date('F j, Y', get_the_ID());
                                    $post_status    = !empty($post->ID) ?  get_post_status($post->ID) : '';
                                    $post_status_key    = !empty($post->ID) ?  get_post_status($post->ID) : '';
                                    switch ($post_status) {
                                        case 'draft':
                                            $post_status    = esc_html('Pending', 'tuturn');
                                            break;
                                        case 'pending':
                                            $post_status    = esc_html('Decline', 'tuturn');
                                            break;
                                        case 'publish':
                                            $post_status    = esc_html('Approved', 'tuturn');
                                            break;
                                    }
                                } ?>
                                <tr>
                                    <td data-label="<?php esc_attr_e('Author name', 'tuturn'); ?>"><?php echo esc_html($author_name); ?></td>
                                    <td data-label="<?php esc_attr_e('Date', 'tuturn'); ?>"><?php echo esc_html($post_date) ?></td>
                                    <td data-label="<?php esc_attr_e('Email', 'tuturn'); ?>"><?php echo esc_html($author_email) ?></td>
                                    <td data-label="<?php esc_attr_e('Status', 'tuturn'); ?>"><?php echo esc_html($post_status) ?></td>
                                    <td data-label="<?php esc_attr_e('Option', 'tuturn'); ?>"><a class="tu-linksm" href="<?php echo esc_url($add_page_url); ?>&post_id=<?php echo intval($post->ID); ?>"><?php esc_html_e('View detail', 'tuturn'); ?><i class="icon icon-chevron-right"></i></a></td>

                                    <?php if (!empty($is_resubmit) && ( $post_status_key == 'pending' ||  $post_status_key == 'draft' )) { ?>
                                        <td data-label="<?php esc_attr_e('Cancel', 'tuturn'); ?>"><a class="tu-linksm tu-cancel-verification" id="tu-cancel-identity-verifi" href="javascript:void(0)" data-post_id="<?php echo esc_attr($post->ID); ?>"><?php esc_html_e('Cancel', 'tuturn'); ?></a></td>
                                    <?php } ?>
                                </tr> <?php
                                    }
                                }
                                        ?>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <div class="tu-booking-epmty-field">
                <h4><?php esc_html_e('Uh ho!', 'tuturn'); ?></h4>
                <p><?php esc_html_e('We\'re sorry but there is no verfication log available to show', 'tuturn'); ?></p>
            </div>
        <?php
            $args['message']   = esc_html__('Oops!! record not found', 'tuturn');
            tuturn_get_template_part('dashboard/dashboard', 'empty-record', $args);
        } ?>
    </div>
    <?php
    if (!empty($total_posts)) {
        tuturn_paginate($user_verfication_list, 'tu-pagination');
    } ?>
</div>