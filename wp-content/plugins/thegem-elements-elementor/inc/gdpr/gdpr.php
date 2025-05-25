<?php

if (!defined('ABSPATH')) exit;

define('THEGEM_GDPR_PLUGIN_ROOT_FILE', __FILE__);
define('THEGEM_GDPR_PLUGIN_ROOT_DIR', __DIR__);

require plugin_dir_path(__FILE__).'inc/classes/thegem-gdpr.php';
require plugin_dir_path(__FILE__).'inc/classes/thegem-gdpr-cf7.php';
require plugin_dir_path(__FILE__).'inc/classes/thegem-gdpr-wp.php';

function thegem_gdpr_module_load() {
$thegem_gdpr = new TheGemGdpr();
$thegem_gdpr->run();
}
add_action('init', 'thegem_gdpr_module_load', 1);

if (!function_exists('boolval')) {
	function boolval($var){
		return !! $var;
	}
}
