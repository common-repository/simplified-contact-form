<?php
/*
Plugin Name: Simplified Contact Form
Plugin URI: https://www.renzramos.com/plugins/wordpress-simple-contact-form
Description: Simplified Contact Form for WordPress
Version: 1.0.0
Author: Renz Ramos
Author URI: https://profiles.wordpress.org/imrenzramos
License: GPL2
*/

$version = '1.0.0';

require_once('includes/simplified-contact-form-page.php');
require_once('includes/simplified-contact-form-handler.php');

// Defines
define('SCF_PLUGIN', __FILE__ );
define('SCF_PLUGIN_BASENAME', plugin_basename( SCF_PLUGIN ) );
define('SCF_PLUGIN_NAME', trim( dirname( SCF_PLUGIN_BASENAME ), '/' ) );
define('SCF_PLUGIN_DIR', untrailingslashit( dirname( SCF_PLUGIN ) ) );
define('SCF_URL', plugins_url(trim( dirname( SCF_PLUGIN_BASENAME ), '/' )) );
define('SCF_VERSION', $version );

function scf_enqueue($hook) {
	
    wp_enqueue_style( 'simplified-contact-form-style', SCF_URL . '/assets/css/style.css' );
    wp_enqueue_script('simplified-contact-form-script', SCF_URL .'/assets/js/script.js', array(), null, true);

    $data = array( 
    	'ajaxURL' => admin_url( 'admin-ajax.php' ),
    ) ;
	wp_localize_script( 'simplified-contact-form-script', 'cpVars', $data);
	
}
add_action( 'wp_enqueue_scripts', 'scf_enqueue' );

function scf_setting_link( $links ) {
	$links[] = '<a href="' . admin_url( 'admin.php?page=simplified-contact-form' ) .'">' . __('Settings') . '</a>';
	return $links;
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__) , 'scf_setting_link');

?>
