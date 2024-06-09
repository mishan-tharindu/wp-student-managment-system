<?php
/**
 * Plugin Name: Student Management System
 * Description: Imagine it as a digital filing system that records personal information, academic progress like test scores and grades, and even behavioral evaluations. However, it does more than just store data. The SMS also tracks attendance, assists with curriculum planning, and manages schedules.
 * Version: 1.2
 * Author: Mishan Tharindu
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'SMS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) ); //Here is the my plugin URl

register_activation_hook( __FILE__, 'sms_install' );
register_uninstall_hook( __FILE__, 'sms_uninstall' );
register_deactivation_hook( __FILE__, 'sms_deactivate' );

require_once plugin_dir_path( __FILE__ ) . 'includes/sms-functions.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/sms-database.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/sms-admin-pages.php';
require_once plugin_dir_path( __FILE__ ) . 'templates/public/student-search-by-name.php';
require_once plugin_dir_path( __FILE__ ) . 'templates/public/students-registration.php';
require_once plugin_dir_path( __FILE__ ) . 'templates/public/students-update-delete.php';

require_once SMS_PLUGIN_DIR . 'includes/sms-shortcode.php';

// Enqueue scripts and styles
add_action( 'wp_enqueue_scripts', 'sms_enqueue_scripts' );
add_action( 'wp_enqueue_scripts', 'sms_subject_enqueue_scripts' );

// Enqueue Admin scripts and styles
add_action( 'admin_enqueue_scripts', 'sms_enqueue_admin_styles' );



// Add menu item
add_action( 'admin_menu', 'sms_menu' );

// Add shortcodes
// add_shortcode( 'student_list', 'sms_student_list_shortcode' );
// add_shortcode( 'student_search_by_id', 'sms_student_search_by_id_shortcode' );


// add_action( 'wp_enqueue_scripts', 'sms_enqueue_scripts' );

// function sms_enqueue_scripts() {
//     wp_enqueue_script( 'sms-ajax-search', plugin_dir_url( __FILE__ ) . 'assets/js/sms-ajax-search.js', array('jquery'), null, true );
//     wp_localize_script( 'sms-ajax-search', 'sms_ajax_obj', array( 
//         'ajax_url' => admin_url( 'admin-ajax.php' ) 
//     ));
// }


?>
