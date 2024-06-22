<?php
add_action('wp_ajax_load_class_students', 'load_class_students');
add_action('wp_ajax_nopriv_load_class_students', 'load_class_students');

function load_class_students() {
    global $wpdb;

    $class_id = $_GET['class_id'];

    $students_table = $wpdb->prefix . 'students';
    $enrolled_table = $wpdb->prefix . 'class_enrolled_students';

    $query = $wpdb->prepare("
        SELECT s.student_id AS id, CONCAT(s.first_name, ' ', s.last_name) AS name
        FROM $students_table s
        INNER JOIN $enrolled_table e ON s.student_id = e.student_id
        WHERE e.class_id = %d
    ", $class_id);

    $students = $wpdb->get_results($query);

    wp_send_json_success($students);
}

add_action('wp_ajax_save_attendance_data', 'save_attendance_data');
add_action('wp_ajax_nopriv_save_attendance_data', 'save_attendance_data');

function save_attendance_data() {
    global $wpdb;

    $class_id = $_POST['class_id'];
    $attendance_data = $_POST['attendance_data'];
    $attendance_table = $wpdb->prefix . 'student_attendance';



    foreach ($attendance_data as $attendance) {

        // Assuming you retrieve time from form
        // $htmlTime = $_POST['time']; // Example: '10:57:09 PM'

        $result = $wpdb->insert(
            $attendance_table,
            array(
                'class_id' => $class_id,
                'student_id' => $attendance['student_id'],
                'attendance_date' => current_time('mysql'),
                'in_time' => $attendance['in_Time'],
                'attendance_status' => $attendance['attendance_status']
            ),
            array('%d', '%s', '%s', '%s')
        );

        if ($result === false) {
            wp_send_json_error(array('message' => 'Failed to save attendance data: ' . $wpdb->last_error));
            wp_die();
        }
    }

    wp_send_json_success(array('message' => 'Attendance data saved successfully!'));
    wp_die();
}
