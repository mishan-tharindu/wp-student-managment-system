<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function sms_register_student() {

    // Debugging: check if nonce fields are present
    if (!isset($_POST['student_form_nonce'])) {
        wp_die('Nonce field is missing.');
    }

    if (!isset($_POST['student_form_nonce']) || !wp_verify_nonce($_POST['student_form_nonce'], 'student_form_action')) {
        wp_die('Invalid nonce.');
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'students';
    
    $studentid = sanitize_text_field($_POST['student_id']);
    $first_name = sanitize_text_field($_POST['first_name']);
    $last_name = sanitize_text_field($_POST['last_name']);
    $email = sanitize_email($_POST['email']);
    
    // Additional validation if needed
    if (empty($studentid) || empty($first_name) || empty($last_name) || empty($email)) {
        wp_die('Please fill in all required fields.');
    }

    // Using $wpdb->insert which is secure against SQL injection
    $wpdb->insert( 
        $table_name, 
        array( 
            'student_id' => $studentid, 
            'first_name' => $first_name, 
            'last_name' => $last_name, 
            'email' => $email 
        ),
        array(
            '%s',
            '%s',
            '%s',
            '%s'
        )
    );

    // Define the URL to students-management.php
    // $redirect_url = plugin_dir_url(__FILE__) . '?page=sms-student-management';
    // // Redirect to the students management page
    // wp_redirect($redirect_url);
    // echo '<script>window.location.href="?page=sms-student-management";</script>';
    // exit;
}

function sms_update_student() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'students';
    
    $student_id = sanitize_text_field( $_POST['student_id'] );
    $first_name = sanitize_text_field( $_POST['first_name'] );
    $last_name = sanitize_text_field( $_POST['last_name'] );
    $email = sanitize_email( $_POST['email'] );

    $wpdb->update( 
        $table_name, 
        array( 
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email 
        ), 
        array( 'student_id' => $student_id ) 
    );
}

function sms_delete_student( $student_id ) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'students';

    $result = $wpdb->delete( $table_name, array( 'student_id' => $student_id ));

    if ( $result === false ) {
        echo "<p>Error occurred while deleting student data.</p>";
    } else {
        echo "<p>Student data deleted successfully.</p>";
    }
}

function generate_unique_student_id() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'students';
    $unique = false;
    
    while (!$unique) {
        $random_number = mt_rand(1000, 9999);
        $date_part = date('Ymd');
        $student_id = 'STU' . $date_part . $random_number;

        $existing_id = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE student_id = %s", $student_id));

        if ($existing_id == 0) {
            $unique = true;
        }
    }

    return $student_id;
}

function generate_unique_teacher_id() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'teachers';
    $unique = false;
    
    while (!$unique) {
        $random_number = mt_rand(1000, 9999);
        $date_part = date('Ymd');
        $teacher_id = 'TEC' . $date_part . $random_number;

        $existing_id = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE teacher_id = %s", $teacher_id));

        if ($existing_id == 0) {
            $unique = true;
        }
    }

    return $teacher_id;
}

function sms_enqueue_scripts() {

    $plugin_url = plugin_dir_url( __DIR__ );

    // echo plugin_dir_url( __DIR__ );

    // Enqueue front-end CSS
    wp_enqueue_style( 'sms-styles', plugin_dir_url( __DIR__ ) . 'assets/css/sms-styles.css', array(), '1.0', 'all' );

    wp_enqueue_script( 'sms-ajax-search', $plugin_url . 'assets/js/sms-ajax-search.js', array('jquery'), null, true );
    wp_localize_script( 'sms-ajax-search', 'sms_ajax_obj', array( 
        'ajax_url' => admin_url( 'admin-ajax.php' ) 
    ));

    wp_enqueue_script('sms-ajax-registration', plugin_dir_url(__DIR__) . 'assets/js/sms-ajax-registration.js', array('jquery'), '1.0', true);

    // Localize the AJAX URL for the JavaScript file
    wp_localize_script('sms-ajax-registration', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));



}

function sms_subject_enqueue_scripts(){
    $plugin_url = plugin_dir_url( __DIR__ );
        // Subject Registration
    // wp_enqueue_script('sms-subject-management', plugins_url('assets/js/sms-subject-management.js', __FILE__), array('jquery'), null, true);
    wp_enqueue_script('sms-ajax-registration', $plugin_url . 'assets/js/sms-subject-management.js', array('jquery'), '1.0', true);
    wp_localize_script('sms-subject-management', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
}

function sms_enqueue_admin_styles() {
    $plugin_url = plugin_dir_url( __DIR__ );

    // Enqueue admin CSS file
    wp_enqueue_style( 'sms-admin-style', $plugin_url . 'assets/css/admin-style.css' );
}

// Register subject
// add_action('wp_ajax_sms_register_subject', 'sms_register_subject');
function sms_register_subject() {

        // Debugging: check if nonce fields are present
        if (!isset($_POST['subject_form_nonce'])) {
            wp_die('Nonce field is missing.');
        }
    
        if (!isset($_POST['subject_form_nonce']) || !wp_verify_nonce($_POST['subject_form_nonce'], 'subject_form_action')) {
            wp_die('Invalid nonce.');
        }

        // error_log('Check: sms_register_subject methode !!!' );
        // var_dump('Check: sms_register_subject methode !!!');


    global $wpdb;

    $subject_name = sanitize_text_field($_POST['subject_name']);
    $subject_description = sanitize_textarea_field($_POST['subject_description']);
    $table_name = $wpdb->prefix . 'subject';

    $result = $wpdb->insert(
        $table_name,
        array(
            'subject_name' => $subject_name,
            'subject_description' => $subject_description),
        array(
            '%s',
            '%s'
        )
    );

    if ($result) {
        sms_get_all_subjects(true); // Fetch all subjects and return as success response
    } else {
        wp_send_json_error(['message' => 'Failed to register subject.']);
    }
}

// Update subject
// add_action('wp_ajax_sms_update_subject', 'sms_update_subject');
function sms_update_subject() {
    global $wpdb;

    $subject_id = intval($_POST['subject_id']);
    $subject_name = sanitize_text_field($_POST['subject_name']);
    $subject_description = sanitize_textarea_field($_POST['subject_description']);
    $table_name = $wpdb->prefix . 'subject';

    $result = $wpdb->update($table_name, [
        'subject_name' => $subject_name,
        'subject_description' => $subject_description
    ], ['subject_id' => $subject_id]);

    if ($result !== false) {
        sms_get_all_subjects(true); // Fetch all subjects and return as success response
    } else {
        wp_send_json_error(['message' => 'Failed to update subject.']);
    }
}

// Delete subject
// add_action('wp_ajax_sms_delete_subject', 'sms_delete_subject');
function sms_delete_subject($subject_id) {
    global $wpdb;

    // $subject_id = intval($_POST['subject_id']);
    $table_name = $wpdb->prefix . 'subject';

    $result = $wpdb->delete($table_name, ['subject_id' => $subject_id]);

    if ($result) {
        sms_get_all_subjects(true); // Fetch all subjects and return as success response
    } else {
        wp_send_json_error(['message' => 'Failed to delete subject.']);
    }
}

// Get a single subject
// add_action('wp_ajax_sms_get_subject', 'sms_get_subject');
function sms_get_subject() {
    global $wpdb;

    $subject_id = intval($_POST['subject_id']);
    $table_name = $wpdb->prefix . 'subject';

    $subject = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE subject_id = %d", $subject_id));

    if ($subject) {
        wp_send_json_success($subject);
    } else {
        wp_send_json_error(['message' => 'Subject not found.']);
    }
}

// Get all subjects
// add_action('wp_ajax_sms_get_all_subjects', 'sms_get_all_subjects');
function sms_get_all_subjects($send_response = false) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'subject';
    $subjects = $wpdb->get_results("SELECT * FROM $table_name");

    if ($send_response) {
        // wp_send_json_success($subjects);
    } else {
        return $subjects;
    }
}

?>
