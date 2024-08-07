<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Scripts and Css Files Assignd

function sms_enqueue_admin_scripts() {

    // Enqueue Select2 CSS
    wp_enqueue_style('select2-css', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css');

    // // Enqueue Select2 JS
    wp_enqueue_script('select2-js', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', array('jquery'), null, true);


    // Enqueue the custom admin script
    wp_enqueue_script('sms-admin-script', plugin_dir_url( __DIR__ ) . 'assets/js/admin-scripts.js', array('jquery'), '1.0', true);
    wp_localize_script('sms-admin-script', 'wpApiSettings', array(
        'nonce' => wp_create_nonce('wp_rest')
    ));

    wp_localize_script('sms-admin-script', 'sms_ajax_classes_obj', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'ajax_nonce' => wp_create_nonce('sms_enroll_students_nonce')
    ));

    // You can also enqueue a CSS file if needed
    // wp_enqueue_style('sms-admin-style', plugin_dir_url(__FILE__) . 'assets/css/admin-style.css');

    // Subject js File
    wp_enqueue_script('sms-subject-management', plugin_dir_url( __DIR__ ) . 'assets/js/sms-subject-management.js', array('jquery'), '1.0', true);
    wp_localize_script('sms-subject-management', 'sms_ajax_subject_obj', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'ajax_nonce' => wp_create_nonce('sms_subject_nonce')
    ));

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

function enqueue_attendance_scripts() {
    wp_enqueue_script('attendance-management-script', plugin_dir_url( __DIR__ ) . 'assets/js/classes-data.js', array('jquery'), null, true);

    wp_localize_script('attendance-management-script', 'sms_ajax_classes_data_obj', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));
}

// CRUD Functions

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

function sms_exam_enqueue_scripts(){
    $plugin_url = plugin_dir_url( __DIR__ );
        // Subject Registration
    wp_enqueue_script('sms-exam-script', $plugin_url . 'assets/js/exam-script.js', array('jquery'), '1.0', true);
    // wp_localize_script('sms-exam-script', 'sms_exam_ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
    wp_localize_script('sms-exam-script', 'sms_exam_ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('sms_exam_nonce')
    ));
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

//Load Subject 
function sms_subject_table(){
    global $wpdb;
    $table_name = $wpdb->prefix . 'subject';
    $subjects = $wpdb->get_results("SELECT * FROM $table_name");

    foreach ( $subjects as $subject ) {
        echo '<tr data-subject-id="' . esc_attr( $subject->subject_id ) . '" data-subject-name="' . esc_attr( $subject->subject_name ) . '" data-subject-discription="' . esc_attr( $subject->subject_description ) .'">';
        echo '<td>' . esc_html( $subject->subject_id ) . '</td>';
        echo '<td>' . esc_html( $subject->subject_name ) . '</td>';
        echo '<td>' . esc_html( $subject->subject_description ) . '</td>';
        echo '<td><a href="?page=sms-subject-registration&edit=' . esc_attr ( $subject->subject_id ) . '" class="edit-subject">Edit</a> | <a class="delete-subject" href="?page=sms-subject-registration&delete=' . esc_attr ( $subject->subject_id ) . '">Delete</a></td>';
        echo '</tr>';
    }
   
}

function sms_register_teacher() {

    // Debugging: check if nonce fields are present
    if (!isset($_POST['teacher_form_nonce'])) {
        wp_die('Nonce field is missing.');
    }

    // Check nonce
    if (!isset($_POST['teacher_form_nonce']) || !wp_verify_nonce($_POST['teacher_form_nonce'], 'teacher_form_action')) {
        wp_die('Invalid nonce.');
        echo '<div class="error">Nonce verification failed.</div>';
        return;
    }

    // Check permissions
    if (!current_user_can('manage_options')) {
        echo '<div class="error">You do not have permission to perform this action.</div>';
        return;
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'teachers';

    $teacehrid = sanitize_text_field($_POST['teacher_id']);
    $firstname = sanitize_text_field($_POST['first_name']);
    $lastname = sanitize_text_field($_POST['last_name']);
    $gender = sanitize_text_field($_POST['gender']);
    $hire_date = sanitize_text_field($_POST['hire_date']);
    $phonenumber = sanitize_text_field($_POST['phone_number']);
    $address = sanitize_text_field($_POST['address']);
    $email = sanitize_text_field($_POST['email']);
    
    // Additional validation if needed
    if (empty($teacehrid) || empty($firstname) || empty($lastname) || empty($email)) {
        wp_die('Please fill in all required fields.');
    }

    // echo "Results = ".$teacehrid." -- ";

    // Using $wpdb->insert which is secure against SQL injection
    $result = $wpdb->insert( 
        $table_name, 
        array( 
            'teacher_id' => $teacehrid,
            'first_name' => $firstname,
            'last_name' => $lastname,
            'gender' => $gender,
            'hire_date' => $hire_date,
            'phonenumber' => $phonenumber,
            'address' => $address,
            'email' => $email
        ),
        array(
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s'
        )
    );

    // Provide feedback to the user
    if ($result) {
        echo '<div class="success">Teacher registered successfully!</div>';
        echo '<script>window.location.href="?page=sms-teachers-registration";</script>';
    } else {
        echo '<div class="error">Failed to register teacher. Please try again.</div>';
    }
    // Define the URL to students-management.php
    // $redirect_url = plugin_dir_url(__FILE__) . '?page=sms-student-management';
    // // Redirect to the students management page
    // wp_redirect($redirect_url);
    // echo '<script>window.location.href="?page=sms-student-management";</script>';
    // exit;
}

function sms_teacher_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'teachers';
    $teachers = $wpdb->get_results("SELECT * FROM $table_name");

    if ($teachers) {
        echo '<table ID="teachers-table" class="widefat fixed" cellspacing="0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Gender</th>
                    <th>Hire Date</th>
                    <th>Phone Number</th>
                    <th>Address</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>';

        foreach ($teachers as $teacher) {
            // $tabel_name = $wpdb->prefix . "subject";
            // $subjects = $wpdb->get_results("SELECT * FROM WHERE subject_id = $teacher->subject_id");
            // $subject_name;
            // foreach ($subjects as $subject) {
            //     $subject_name = $subject->subject_name;
            // }
            
            
            echo '<tr data-teacher-id="' . esc_attr( $teacher->teacher_id ) . '" data-first-name="' . esc_attr( $teacher->first_name ) . '" data-last-name="' . esc_attr( $teacher->last_name) . '" data-gender="' . esc_attr( $teacher->gender ) . '" data-hire-date="' . esc_attr( $teacher->hire_date ) . '"  data-phonenumber="' . esc_attr( $teacher->phonenumber ) . '" data-address="' . esc_attr( $teacher->address ) . '" data-email="' . esc_attr( $teacher->email ) . '">';
            echo '<td>' . esc_html($teacher->teacher_id) . '</td>';
            echo '<td>' . esc_html($teacher->first_name) . '</td>';
            echo '<td>' . esc_html($teacher->last_name) . '</td>';
            echo '<td>' . esc_html($teacher->gender) . '</td>';
            echo '<td>' . esc_html($teacher->hire_date) . '</td>';
            echo '<td>' . esc_html($teacher->phonenumber) . '</td>';
            echo '<td>' . esc_html($teacher->address) . '</td>';
            echo '<td>' . esc_html($teacher->email) . '</td>';
            echo '<td><a class="edit-teacher" href="?page=sms-teacher-registration&edit=' . esc_attr($teacher->teacher_id) . '">Edit</a> | <a href="?page=sms-teachers-registration&delete=' . esc_attr($teacher->teacher_id) . '" onclick="return confirm(\'Are you sure you want to delete this teacher?\')">Delete</a></td>';
            echo '</tr>';
        }

        echo '</tbody></table>';
    } else {
        echo '<p>No teachers found.</p>';
    }
}

function sms_update_teacher(){
    global $wpdb;
    $table_name = $wpdb->prefix . 'teachers';

    $teacehrid = sanitize_text_field($_POST['teacher_id']);
    $firstname = sanitize_text_field($_POST['first_name']);
    $lastname = sanitize_text_field($_POST['last_name']);
    $gender = sanitize_text_field($_POST['gender']);
    $hire_date = sanitize_text_field($_POST['hire_date']);
    $phonenumber = sanitize_text_field($_POST['phone_number']);
    $address = sanitize_text_field($_POST['address']);
    $email = sanitize_text_field($_POST['email']);

    $result = $wpdb->update( 
        $table_name, 
        array( 
            'first_name' => $firstname,
            'last_name' => $lastname,
            'gender' => $gender,
            'hire_date' => $hire_date,
            'phonenumber' => $phonenumber,
            'address' => $address,
            'email' => $email 
        ), 
        array( 'teacher_id' => $teacehrid ) 
    );

        // Provide feedback to the user
        if ($result) {
            echo '<div class="success">Teacher Updated successfully!</div>';
            echo '<script>window.location.href="?page=sms-teachers-registration";</script>';
        } else {
            echo '<div class="error">Failed to updated teacher. Please try again.</div>';
        }
}

function sms_delete_teacher($teacherID){
    global $wpdb;
    $table_name = $wpdb->prefix . 'teachers';

    $result = $wpdb->delete( $table_name, array( 'teacher_id' => $teacherID ));

    if ( $result === false ) {
        echo '<div class="error">Error occurred while deleting student data.</div>';
    } else {
        echo '<div class="success">Student data deleted successfully.</div>';
        echo '<script>window.location.href="?page=sms-teachers-registration";</script>';
    }
}

?>
