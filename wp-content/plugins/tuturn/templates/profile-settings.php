<?php

/**
 *
 * The template used for user profile settings
 *
 * @package     Tuturn
 * @subpackage  Tuturn/Templates
 * @author      Amentotech <info@amentotech.com>
 * @link        https://themeforest.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
 */

global $current_user, $tuturn_settings;
$current_page_link  = get_permalink();
$url_identity       = !empty($_GET['useridentity']) ? intval($_GET['useridentity']) : '';
$invoice_page_hide  = !empty($tuturn_settings['invoice_page_hide']) ? $tuturn_settings['invoice_page_hide'] : 'show';
$show_subject_btn   = !empty($tuturn_settings['sub_categories_price']) ? $tuturn_settings['sub_categories_price'] : 0;
$identity_verification  = !empty($tuturn_settings['identity_verification']) ? $tuturn_settings['identity_verification'] : array();
$resubmit_verification  = !empty($tuturn_settings['resubmit_verification']) ? $tuturn_settings['resubmit_verification'] : false;

$user_identity  = intval($current_user->ID);

do_action('download_tutoring_log_csv', $user_identity);

if (!is_user_logged_in()) {
    if (!empty($_GET['tab'])) {
        $current_page_link  = add_query_arg('tab', esc_html($_GET['tab']), $current_page_link);
    }

    $redirect_url = tuturn_get_page_uri('login');
    if (empty($redirect_url)) {
        $redirect_url   = get_home_url();
    }

    set_transient('tu_redirect_page_url', esc_url_raw($current_page_link), 200);

    if (!empty($redirect_url)) {
        wp_redirect($redirect_url);
        exit;
    }
} elseif (is_user_logged_in() && (!empty($url_identity)) && $url_identity != $user_identity) {
    wp_redirect(get_home_url());
    exit;
}

do_action('tuturn_dashboard_head');
$user_id = $current_user->ID;

if (!empty($_GET['useridentity'])) {
    $user_id = intval($_GET['useridentity']);
}

if (empty($url_identity)) {
    $url_identity   = $user_id;
}

if (!empty($_GET['tab'])) {
    $current_page_link  = add_query_arg(array('useridentity' => (int)$user_id, 'tab' => esc_html($_GET['tab'])), $current_page_link);
}

$profile_tab        = !empty($_GET['tab']) ? esc_html($_GET['tab']) : 'personal_details';
$profile_mode       = !empty($_GET['mode']) ? esc_html($_GET['mode']) : '';
$id                 = !empty($_GET['id']) ? intval($_GET['id']) : '';
$profile_id         = tuturn_get_linked_profile_id($user_id);
$userType             = apply_filters('tuturnGetUserType', $user_id);
$user_name            = tuturn_get_username($profile_id);
$profile_details    = get_post_meta($profile_id, 'profile_details', true);
$package_info       = apply_filters('tuturn_user_package', $user_id);
$package_info        = !empty($package_info) ? $package_info : array();

$tuturn_args        = array(
    'profile_id'        => $profile_id,
    'user_identity'     => $user_id,
    'user_name'         => $user_name,
    'profile_details'   => $profile_details,
    'package_info'      => $package_info,
    'userType'          => $userType,
    'id' => $id,
    'current_page_link' => $current_page_link,
);

if (empty($userType) || $userType == 'administrator') {
    $userType   = 'instructor';
}

if (!empty($_GET['pdfDownload']) && $_GET['pdfDownload'] == 1 && !empty($id) && $profile_tab == 'invoices' && $profile_mode == 'detail') {
    $current_locale = get_locale();
    /* create new PDF document */
    $pdf_invoice = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

    /* set some default terms for pdf */
    $pdf_invoice->SetCreator(PDF_CREATOR);
    $pdf_invoice->SetAuthor('Tuturn');
    $pdf_invoice->SetTitle('Invoice');
    $pdf_invoice->SetSubject('Invoice');
    $pdf_invoice->SetKeywords('TCPDF, PDF');
    $pdf_invoice->setRTL(false);

    /* disable default header and footer */
    $pdf_invoice->setPrintHeader(false);
    $pdf_invoice->setPrintFooter(false);

    /* set the monospaced font as default */
    $pdf_invoice->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    /* Set RTL direction language like arabic */
    if (is_rtl()) {
        $pdf_invoice->setRTL(true);
        $pdf_invoice->SetFont('aefurat', '', 15);
    }

    if ($current_locale == 'ka_GE') {
        /* set font for Georgian language */
        $pdf_invoice->SetFont('dejavusans', '', 10);
    }

    /* set all extra spacing to 0 */
    $tagvs = array('td' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n' => 0)));
    $pdf_invoice->setHtmlVSpace($tagvs);
    /* set table cell spacing */
    $pdf_invoice->setCellHeightRatio(2.0);
    /* add pdf page */
    $pdf_invoice->AddPage();

    ob_start();
    tuturn_get_template('profile-settings/' . $userType . '/invoice-pdf.php', $tuturn_args);
    $invoice_body   = ob_get_clean();

    $file_name  = 'invoice-' . $id . '.pdf';
    $pdf_invoice->WriteHTML($invoice_body,  true, false, true, true, '');

    /* close and output PDF document */
    $pdf_invoice->Output($file_name, 'D');
    exit;
}
get_header();
while (have_posts()) : the_post(); ?>
    <section class="tu-main-section">
        <div class="container">
            <?php do_action('tuturn_profile_settings_notice', $tuturn_args); ?>
            <div class="row gy-4">
                <div class="col-12 col-xl-4 col-xxl-3">
                    <?php tuturn_get_template('profile-settings/' . $userType . '/sidebar.php', $tuturn_args); ?>
                </div>
                <div class="col-12 col-xl-8 col-xxl-9">
                    <div class="tu-profilewrapper">
                        <?php if ($profile_tab == 'personal_details' || $profile_tab == 'contact_details' || $profile_tab == 'education' || $profile_tab == 'subjects') { ?>
                            <form id="tu-profile-settings" name="profile-settings" class="tu-themeform tu-dhbform" action="" enctype="multipart/form-data">
                            <?php } ?>
                            <div class="tu-boxwrapper">
                                <?php if ($profile_tab == 'personal_details' && $url_identity === $user_identity) {
                                    tuturn_get_template('profile-settings/' . $userType . '/personal-details.php', $tuturn_args);
                                } elseif ($profile_tab == 'contact_details' && $url_identity === $user_identity) {
                                    tuturn_get_template('profile-settings/' . $userType . '/contact-details.php', $tuturn_args);
                                } elseif ($profile_tab == 'security' && $url_identity === $user_identity) {
                                    tuturn_get_template('profile-settings/security.php', $tuturn_args);
                                } elseif ($profile_tab == 'education' && $userType == 'instructor' && !empty($package_info['education'])  && $url_identity === $user_identity) {
                                    tuturn_get_template('profile-settings/' . $userType . '/education-details.php', $tuturn_args);
                                } elseif ($profile_tab == 'subjects' && $userType == 'instructor' && !empty($package_info['teaching'])) {
                                    if (!empty($tuturn_settings['sub_categories_price'])) {
                                        tuturn_get_template('profile-settings/' . $userType . '/sub-categories.php', $tuturn_args);
                                    } else {
                                        tuturn_get_template('profile-settings/' . $userType . '/skills.php', $tuturn_args);
                                    }
                                } elseif ($profile_tab == 'bookings' && $userType == 'instructor' && $url_identity === $user_identity) {
                                    tuturn_get_template('profile-settings/' . $userType . '/booking-details.php', $tuturn_args);
                                } elseif ($profile_tab == 'media' && $userType == 'instructor' && !empty($package_info['gallery'])  && $url_identity === $user_identity) {
                                    tuturn_get_template('profile-settings/' . $userType . '/media-gallery.php', $tuturn_args);
                                } elseif ($profile_tab == 'saved' && $userType == 'student' && $url_identity === $user_identity) {
                                    tuturn_get_template('profile-settings/student/favourites.php', $tuturn_args);
                                } elseif ($profile_tab == 'booking-listings' && $url_identity === $user_identity) {
                                    tuturn_get_template('profile-settings/' . $userType . '/booking-listings.php', $tuturn_args);
                                } elseif ($profile_tab == 'hours'  && $url_identity === $user_identity) {
                                    tuturn_get_template('profile-settings/' . $userType . '/volunteer-hours.php', $tuturn_args);
                                } elseif ($profile_tab == 'user-verification'  && $url_identity === $user_identity) {
                                    /* same file for both users */
                                    tuturn_get_template('profile-settings/user-verification.php', $tuturn_args);
                                } elseif ($profile_tab == 'verfication-listing' && $url_identity === $user_identity) {
                                    /* same file for both users */
                                    tuturn_get_template('profile-settings/verfication-listing.php', $tuturn_args);
                                } elseif ($profile_tab == 'earnings' && $userType == 'instructor'  && $url_identity === $user_identity) {
                                    tuturn_get_template('profile-settings/' . $userType . '/earnings.php', $tuturn_args);
                                } elseif ($profile_tab == 'invoices' && $url_identity === $user_identity) {
                                    if (!empty($invoice_page_hide) && $invoice_page_hide == 'hide') {
                                        esc_html_e('You are not allowed to access this page', 'tuturn');
                                        return;
                                    }

                                    if (!empty($profile_mode) && $profile_mode == 'detail') {
                                        tuturn_get_template('profile-settings/' . $userType . '/invoice-detail.php', $tuturn_args);
                                    } else {
                                        tuturn_get_template('profile-settings/user-invoices.php', $tuturn_args);
                                    }
                                } ?>
                            </div>
                            <?php if ($profile_tab == 'personal_details' || $profile_tab == 'contact_details' || $profile_tab == 'education' || ($profile_tab == 'subjects' && $show_subject_btn == 0)) { ?>
                                <div class="tu-btnarea-two">
                                    <span><?php esc_html_e('Save & update the latest changes to the live', 'tuturn'); ?></span>
                                    <a href="javascript:void(0)" class="tu-primbtn-lg tu-save-settings"><?php esc_html_e('Save & update', 'tuturn'); ?></a>
                                </div>
                                <input type="hidden" name="profile_settings_tab" value="<?php echo esc_attr($profile_tab); ?>">
                            <?php } ?>
                            <input type="hidden" name="profile_id" id="profile_id" value="<?php echo intval($profile_id); ?>">
                            <?php if ($profile_tab == 'personal_details' || $profile_tab == 'contact_details' || $profile_tab == 'education' || $profile_tab == 'subjects') { ?>
                            </form>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php
endwhile;
get_footer();
