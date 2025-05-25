<?php
$gdpr_dns_prefetch_options = get_option('thegem_gdpr_dns_prefetch');
$dns_prefetch_value = !empty($gdpr_dns_prefetch_options['value']) ? $gdpr_dns_prefetch_options['value'] : '';
$dns_prefetch_state = !empty($gdpr_dns_prefetch_options['state']) ? $gdpr_dns_prefetch_options['state'] : 'disabled';

$gdpr_theme_fonts_options = get_option('thegem_gdpr_theme_fonts');
$theme_fonts_value = !empty($gdpr_theme_fonts_options['value']) ? $gdpr_theme_fonts_options['value'] : '';
$theme_fonts_state = !empty($gdpr_theme_fonts_options['state']) ? $gdpr_theme_fonts_options['state'] : 'disabled';
?>

<div class="thegem-gdpr-forms">
    <div class="thegem-gdpr-forms-item">
        <div class="thegem-gdpr-form-box">
            <div class="thegem-gdpr-field-box">
                <label><?= __('Google Fonts', 'thegem'); ?></label>
                <div class="field">
                    <button type="button" class="button thegem-gdpr-extras-google-fonts-btn">
                        <span <?php if ($theme_fonts_state == 'enabled'): ?>style="display: none;"<?php endif; ?> data-fonts-disabled>
                            <?= __('Disable', 'thegem'); ?>
                        </span>
                        <span <?php if ($theme_fonts_state == 'disabled'): ?>style="display: none;"<?php endif; ?> data-fonts-enabled>
                            <?= __('Enable', 'thegem'); ?>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="thegem-gdpr-forms-item">
        <div class="thegem-gdpr-form-box">
            <div class="thegem-gdpr-field-box">
                <label><?= __('DNS prefetch', 'thegem'); ?></label>
                <div class="field">
                    <button type="button" class="button thegem-gdpr-extras-dns-prefetch-btn">
                         <span <?php if ($dns_prefetch_state == 'enabled'): ?>style="display: none;"<?php endif; ?> data-dns-disabled>
                            <?= __('Disable', 'thegem'); ?>
                        </span>
                        <span <?php if ($dns_prefetch_state == 'disabled'): ?>style="display: none;"<?php endif; ?> data-dns-enabled>
                            <?= __('Enable', 'thegem'); ?>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="thegem-gdpr-extras-google-fonts-popup" class="thegem-gdpr-popup">
    <form id="thegemGdprExtrasGoogleFonts" method="post" novalidate="novalidate">
        <div class="thegem-gdpr-popup-title">
            <span class="thegem-gdpr-popup-title-text" <?php if ($theme_fonts_state == 'enabled'): ?>style="display: none;"<?php endif; ?> data-fonts-disabled>
                 <?= __('Step 1: Disable Google Fonts', 'thegem'); ?>
            </span>
            <span class="thegem-gdpr-popup-title-text" <?php if ($theme_fonts_state == 'disabled'): ?>style="display: none;"<?php endif; ?> data-fonts-enabled>
                 <?= __('Enable Google Fonts', 'thegem'); ?>
            </span>
            <a href="javascript:void(0);" class="thegem-gdpr-popup-close" data-modal-close></a>
        </div>
        <div class="thegem-gdpr-popup-body">
            <div class="text">
                <p <?php if ($theme_fonts_state == 'enabled'): ?>style="display: none;"<?php endif; ?> data-fonts-disabled>
	                <?= __('By proceeding all Google fonts will be disabled in TheGem. The Google fonts selected in your current typography settings in Theme Options can be automatically replaced by the embedded default TheGem fonts (Montserrat, Source Sans Pro) or by the system font (Arial). Please select one of this replacement options by clicking on the appropriate button below. <br/><br/><b>Please note:</b><br/><br/>', 'thegem'); ?>
                </p>
                <ul <?php if ($theme_fonts_state == 'enabled'): ?>style="display: none;"<?php endif; ?> data-fonts-disabled>
                    <li>
                        <p>
			                <?= __('We strongly recommend to backup your current TheGem Theme Options settings before proceeding. <a href="' . get_site_url() . '/wp-admin/admin.php?page=thegem-theme-options#/backup#autoBackup" target="_blank">Backup Now</a>', 'thegem'); ?>
                        </p>
                    </li>
                    <li>
                        <p>
			                <?= __('You can always upload and use your own self-hosted fonts by using <a href="' . get_site_url() . '/wp-admin/admin.php?page=fonts-manager" target="_blank">"Self-Hosted Fonts"</a> tool in TheGem.', 'thegem'); ?>
                        </p>
                    </li>
                    <li>
                        <p>
			                <?= __('Only Google fonts used in TheGem theme will be disabled. This action doesn`t affect any third-party plugins. In case you will still have requests to Google fonts server after performing this action, please check your third-party plugins settings and disable Google fonts in this plugins manually.', 'thegem'); ?>
                        </p>
                    </li>
                </ul>
                <p <?php if ($theme_fonts_state == 'disabled'): ?>style="display: none;"<?php endif; ?> data-fonts-enabled>
	                <?= __('By proceeding Google fonts will be re-enabled in typography controls in TheGem Theme Options.', 'thegem'); ?>
                </p>
            </div>
            <div class="btns">
                <ul>
                    <li <?php if ($theme_fonts_state == 'enabled'): ?>style="display: none;"<?php endif; ?> data-fonts-disabled>
                        <button class="btn btn-cyan" type="submit" data-state="enabled" data-value="thegem_fonts">
		                    <?= __('Replace with embedded fonts', 'thegem'); ?>
                        </button>
                    </li>
                    <li <?php if ($theme_fonts_state == 'enabled'): ?>style="display: none;"<?php endif; ?> data-fonts-disabled>
                        <button class="btn btn-dark" type="submit" data-state="enabled" data-value="default_fonts">
		                    <?= __('Replace with system font', 'thegem'); ?>
                        </button>
                    </li>
                    <li <?php if ($theme_fonts_state == 'disabled'): ?>style="display: none;"<?php endif; ?> data-fonts-enabled>
                        <button class="btn btn-cyan" type="submit" data-state="disabled" data-value="all_fonts">
		                    <?= __('Enable Google Fonts', 'thegem'); ?>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </form>
</div>

<div id="thegem-gdpr-extras-google-fonts-confirm-popup" class="thegem-gdpr-popup">
    <div class="thegem-gdpr-popup-title">
         <span class="thegem-gdpr-popup-title-text" <?php if ($theme_fonts_state == 'enabled'): ?>style="display: none;"<?php endif; ?> data-fonts-enabled>
             <?= __('Step 2: Regenerate CSS', 'thegem'); ?>
         </span>
        <span class="thegem-gdpr-popup-title-text" <?php if ($theme_fonts_state == 'disabled'): ?>style="display: none;"<?php endif; ?> data-fonts-disabled>
            <?= __('Google Fonts Enabled', 'thegem'); ?>
        </span>
        <a href="javascript:void(0);" class="thegem-gdpr-popup-close" data-modal-close></a>
    </div>
    <div class="thegem-gdpr-popup-body">
        <div class="text">
            <p <?php if ($theme_fonts_state == 'enabled'): ?>style="display: none;"<?php endif; ?> data-fonts-enabled>
		        <?= __('To finish the disabling of Google fonts in TheGem, please regenerate CSS for styles set in TheGem Theme Options. All your settings will remain, this action will not affect your setup. <br/><br/><b>Please note:</b> you will be redirected to Theme Options.<br/><br/>', 'thegem'); ?>
            </p>
            <p <?php if ($theme_fonts_state == 'disabled'): ?>style="display: none;"<?php endif; ?> data-fonts-disabled>
		        <?= __('Google Fonts have been successfully re-enabled in TheGem. Please use <a href="' . get_site_url() . '/wp-admin/admin.php?page=thegem-theme-options#/typography" target="_blank">Theme Options</a> to setup your typography.', 'thegem'); ?>
            </p>
        </div>
        <div class="btns">
            <ul>
                <li <?php if ($theme_fonts_state == 'enabled'): ?>style="display: none;"<?php endif; ?> data-fonts-enabled>
                    <a href="<?= get_site_url() ?>/wp-admin/admin.php?page=thegem-theme-options#/extras#regenerateCss" class="btn btn-cyan">
                        <?= __('Regenerate CSS', 'thegem'); ?>
                    </a>
                </li>
                <li <?php if ($theme_fonts_state == 'disabled'): ?>style="display: none;"<?php endif; ?> data-fonts-disabled>
                    <a href="javascript:void(0);" class="btn btn-cyan" data-modal-close>
		                <?= __('OK', 'thegem'); ?>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

<div id="thegem-gdpr-extras-dns-prefetch-popup" class="thegem-gdpr-popup">
    <form id="thegemGdprExtrasDnsPrefetch" method="post" novalidate="novalidate">
        <div class="thegem-gdpr-popup-title">
            <span class="thegem-gdpr-popup-title-text" <?php if ($dns_prefetch_state == 'enabled'): ?>style="display: none;"<?php endif; ?> data-dns-disabled>
                <?= __('Disable DNS prefetch', 'thegem'); ?>
            </span>
            <span class="thegem-gdpr-popup-title-text" <?php if ($dns_prefetch_state == 'disabled'): ?>style="display: none;"<?php endif; ?> data-dns-enabled>
                <?= __('Enable DNS prefetch', 'thegem'); ?>
            </span>
            <a href="javascript:void(0);" class="thegem-gdpr-popup-close" data-modal-close></a>
        </div>
        <div class="thegem-gdpr-popup-body">
            <div class="text">
                <p>
	                <?= __('DNS prefetching is an attempt to resolve domain names before a user tries to follow a link. This is done using the computer’s normal DNS resolution mechanism.The main reason for rel=dns-prefetch to exist is to speed up the way web pages load when they are using different domains for page resources. This process is often called “DNS prefetching“.', 'thegem'); ?>
                </p>
                <p <?php if ($dns_prefetch_state == 'enabled'): ?>style="display: none;"<?php endif; ?> data-dns-disabled>
	                <?= __('By default WordPress adds DNS prefetching via rel=dns-prefetch to preload Google fonts, emojis etc. Here you have an option to disable DNS prefetching for your website.', 'thegem'); ?>
                </p>
                <p <?php if ($dns_prefetch_state == 'disabled'): ?>style="display: none;"<?php endif; ?> data-dns-enabled>
		            <?= __('By default WordPress adds DNS prefetching via rel=dns-prefetch to preload Google fonts, emojis etc. Here you have an option to enable DNS prefetching for your website.', 'thegem'); ?>
                </p>
            </div>
            <div class="btns">
                <ul>
                    <li <?php if ($dns_prefetch_state == 'enabled'): ?>style="display: none;"<?php endif; ?> data-dns-disabled>
                        <button class="btn btn-cyan" type="submit" data-state="enabled" data-value="disabled">
			                <?= __('Disable DNS prefetch', 'thegem'); ?>
                        </button>
                    </li>
                    <li <?php if ($dns_prefetch_state == 'disabled'): ?>style="display: none;"<?php endif; ?> data-dns-enabled>
                        <button class="btn btn-cyan" type="submit" data-state="disabled" data-value="enabled">
			                <?= __('Enable DNS prefetch', 'thegem'); ?>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </form>
</div>
