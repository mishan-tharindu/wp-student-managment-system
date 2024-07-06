<?php


// Handle AJAX request to save exam data
function smp_save_exam() {
    global $wpdb;
    $class_id = intval($_POST['class_id']);
    $exam_date = sanitize_text_field($_POST['exam_date']);
    $exam_time = sanitize_text_field($_POST['exam_time']);
    $exam_status = intval($_POST['exam_status']);

    $wpdb->insert(
        $wpdb->prefix . 'exams',
        array(
            'class_id' => $class_id,
            'exam_date' => $exam_date,
            'exam_time' => $exam_time,
            'exam_status' => $exam_status
        )
    );

    wp_send_json_success();
}
add_action('wp_ajax_smp_save_exam', 'smp_save_exam');
add_action('wp_ajax_nopriv_smp_save_exam', 'smp_save_exam');

// Handle AJAX request to load classes
function smp_load_classes() {
    check_ajax_referer('sms_exam_nonce', '_ajax_nonce');

    global $wpdb;
    // $classes = $wpdb->get_results("SELECT class_id, name FROM {$wpdb->prefix}classes", ARRAY_A);
    $class_table = $wpdb->prefix . 'classes';
    $teachers_table = $wpdb->prefix . 'teachers';
    $subject_table = $wpdb->prefix . 'subject';
    $classes = $wpdb->get_results("SELECT * FROM $class_table INNER JOIN $teachers_table ON $class_table.teacher_id = $teachers_table.teacher_id INNER JOIN $subject_table ON $class_table.subject_id = $subject_table.subject_id ");


    if ($classes === false) {
        error_log('Failed to load classes from database');
        wp_send_json_error('Failed to load classes from database');
    }

    if (empty($classes)) {
        error_log('No classes found');
        wp_send_json_error('No classes found');
    }

    error_log('Classes loaded successfully');
    wp_send_json_success($classes);
}

add_action('wp_ajax_smp_load_classes', 'smp_load_classes');
add_action('wp_ajax_nopriv_smp_load_classes', 'smp_load_classes');

// Handle AJAX request to get teacher name
function smp_get_teacher_name() {
    global $wpdb;
    $class_id = intval($_GET['class_id']);

    $class_table = $wpdb->prefix . 'classes';
    $teachers_table = $wpdb->prefix . 'teachers';
    $subject_table = $wpdb->prefix . 'subject';
    $teachers_results = $wpdb->get_row($wpdb->prepare("SELECT first_name, last_name FROM $class_table INNER JOIN $teachers_table ON $class_table.teacher_id = $teachers_table.teacher_id WHERE $class_table.class_id = %d", $class_id));
    // $teacher = $wpdb->get_row($wpdb->prepare("SELECT first_name, last_name FROM {$wpdb->prefix}teachers WHERE class_id = %d", $class_id));
    // wp_send_json($teacher);

    if($teachers_results === false){
        error_log('Failed to load classes teacher from database');
        wp_send_json_error('Failed to load classes teachers from database');
    }

    if(empty($teachers_results)){
        error_log('No classes Teacher found');
        wp_send_json_error('No classes Teacher found');
    }

    error_log('Classes teacher loaded successfully');
    wp_send_json_success($teachers_results);


}
add_action('wp_ajax_smp_get_teacher_name', 'smp_get_teacher_name');
add_action('wp_ajax_nopriv_smp_get_teacher_name', 'smp_get_teacher_name');

// Handle AJAX request to load exams
function smp_load_exams() {
    global $wpdb;
    $exams = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}exams WHERE exam_status = 1");

    if($exams === false){
        error_log('Failed to load classes from database');
        wp_send_json_error('Failed to load classes from database');
    }

    if(empty($exams)){
        error_log('No Exams Found');
        wp_send_json_error('No classes found');
    }

    error_log('Exam loaded successfully !!!');
    wp_send_json_success($exams);
    // wp_send_json($exams);
}
add_action('wp_ajax_smp_load_exams', 'smp_load_exams');

// Handle AJAX request to delete exam
function smp_delete_exam() {
    global $wpdb;
    $exam_id = intval($_POST['exam_id']);
    $wpdb->delete($wpdb->prefix . 'exams', array('exam_id' => $exam_id));
    wp_send_json_success();
}
add_action('wp_ajax_smp_delete_exam', 'smp_delete_exam');

// Handle AJAX request to load exam details
function smp_load_exam_details() {
    check_ajax_referer('smp_nonce', '_ajax_nonce');

    if (!isset($_POST['exam_id'])) {
        wp_send_json_error('Exam ID is required.');
    }

    global $wpdb;
    $exam_id = intval($_POST['exam_id']);
    $exam = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}exams WHERE exam_id = %d", $exam_id), ARRAY_A);

    if (empty($exam)) {
        wp_send_json_error('Exam not found.');
    }

    // Get the class teacher name
    $class_id = $exam['class_id'];
    $teacher_name = $wpdb->get_var($wpdb->prepare("SELECT teacher_id FROM {$wpdb->prefix}classes WHERE id = %d", $class_id));
    $exam['teacher_id'] = $teacher_name;

    wp_send_json_success($exam);
}
add_action('wp_ajax_smp_load_exam_details', 'smp_load_exam_details');

// Handle AJAX request to update exam details
function smp_update_exam() {
    check_ajax_referer('smp_nonce', '_ajax_nonce');

    if (!isset($_POST['exam_id'], $_POST['class_id'], $_POST['exam_date'], $_POST['exam_time'], $_POST['exam_status'])) {
        wp_send_json_error('All fields are required.');
    }

    global $wpdb;
    $exam_id = intval($_POST['exam_id']);
    $class_id = intval($_POST['class_id']);
    $exam_date = sanitize_text_field($_POST['exam_date']);
    $exam_time = sanitize_text_field($_POST['exam_time']);
    $exam_status = isset($_POST['exam_status']) ? 1 : 0;

    $updated = $wpdb->update(
        "{$wpdb->prefix}exams",
        array(
            'class_id' => $class_id,
            'exam_date' => $exam_date,
            'exam_time' => $exam_time,
            'exam_status' => $exam_status
        ),
        array('exam_id' => $exam_id),
        array('%d', '%s', '%s', '%d'),
        array('%d')
    );

    if ($updated === false) {
        wp_send_json_error('Failed to update exam.');
    }

    wp_send_json_success('Exam updated successfully.');
}
add_action('wp_ajax_smp_update_exam', 'smp_update_exam');
