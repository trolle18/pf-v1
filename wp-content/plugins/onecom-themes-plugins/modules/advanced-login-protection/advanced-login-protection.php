<?php
// Exit if file accessed directly.
defined( "WPINC" ) or die(); // No Direct Access

// Autoload ALM classes
function alp_autoloader($class) {
	$file_name = strtolower(str_replace('_', '-', $class));

    // Only include if ALP files
    if (strpos($file_name, 'onecom-alp') === 0) {
        require 'classes' . DIRECTORY_SEPARATOR . 'class-' . $file_name . '.php';
    }
}
spl_autoload_register('alp_autoloader');

// Load ALM if old login masking class does not exists in validator
if (!class_exists('OnecomLoginMasking')) {
    $alp = new Onecom_ALP();
    $alp->init();
}

// ALM notice for new users
$alm_notice = new Onecom_ALP_Notice();
$alm_notice->init();

// Trigger Password Reset mail when disable ALP
$alm_reset_password = new Onecom_ALP_Reset_Password();
$alm_reset_password->init();

// "Login with one.com" button on WordPress login form
$alm_onecom_login = new Onecom_ALP_Onecom_Login();
$alm_onecom_login->init();

// Nudge ALP repeat popup in dashboard
add_action('admin_init', 'alp_popup_init');
function alp_popup_init(){
    $alm_onecom_popup = new Onecom_ALP_Popup();
    $alm_onecom_popup->init();
}