<?php
add_action('wp_ajax_load_class_students', 'load_class_students');
add_action('wp_ajax_nopriv_load_class_students', 'load_class_students');

// function load_class_students() {
//     global $wpdb;

//     $class_id = $_GET['class_id'];

//     $students_table = $wpdb->prefix . 'students';
//     $enrolled_table = $wpdb->prefix . 'class_enrolled_students';

//     $query = $wpdb->prepare("
//         SELECT s.student_id AS id, CONCAT(s.first_name, ' ', s.last_name) AS name
//         FROM $students_table s
//         INNER JOIN $enrolled_table e ON s.student_id = e.student_id
//         WHERE e.class_id = %d
//     ", $class_id);

//     $students = $wpdb->get_results($query);

//     wp_send_json_success($students);
// }

// "SELECT s.student_id, s.first_name, s.last_name, a.in_time, a.out_time
// FROM $table_students s
// INNER JOIN $table_attendance a ON s.student_id = a.student_id
// WHERE a.class_id = %d AND a.attendance_date = %s AND a.attendance_status = '1'",

function load_class_students() {
    global $wpdb;

    $class_id = isset($_GET['class_id']) ? intval($_GET['class_id']) : 0;
    $only_present = isset($_GET['only_present']) && $_GET['only_present'] === 'true';

    if ($class_id <= 0) {
        wp_send_json_error(array('message' => 'Invalid class ID'));
        wp_die();
    }

    $table_students = $wpdb->prefix . 'students';
    $table_attendance = $wpdb->prefix . 'student_attendance';

    if ($only_present) {
        $current_date = current_time('Y-m-d');
        $query = $wpdb->prepare(
            "SELECT s.student_id, s.first_name, s.last_name, a.attendance_id, a.in_time, a.out_time, a.attendance_status
            FROM $table_students s
            INNER JOIN $table_attendance a ON s.student_id = a.student_id
            WHERE a.class_id = %d AND a.attendance_date = %s AND a.attendance_status = '1'",
            $class_id, $current_date
        );
    } else {
        $query = $wpdb->prepare(
            "SELECT s.student_id, s.first_name, s.last_name, NULL as in_time, NULL as out_time, NULL as attendance_status
            FROM $table_students s
            INNER JOIN {$wpdb->prefix}class_enrolled_students ces ON s.student_id = ces.student_id
            WHERE ces.class_id = %d",
            $class_id
        );
    }

    $students = $wpdb->get_results($query);

    if (!empty($students)) {
        wp_send_json_success($students);
    } else {
        wp_send_json_error(array('message' => 'No students found.'));
    }

    wp_die();
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
        //'attendance_date' => current_time('mysql'),

        $result = $wpdb->insert(
            $attendance_table,
            array(
                'class_id' => $class_id,
                'student_id' => $attendance['student_id'],
                'attendance_date' => $attendance['class_date'],
                'in_time' => $attendance['in_Time'],
                'out_time' => $attendance['out_Time'],
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

// add_action('wp_ajax_save_attendance', 'save_attendance');
// add_action('wp_ajax_update_checkouts', 'update_checkouts');

// function save_attendance() {
//     global $wpdb;

//     $class_id = $_POST['class_id'];
//     $attendance_data = $_POST['attendance_data'];

//     foreach ($attendance_data as $attendance) {
//         $result = $wpdb->insert(
//             $wpdb->prefix . 'student_attendance',
//             array(
//                 'class_id' => $class_id,
//                 'student_id' => $attendance['student_id'],
//                 'attendance_date' => $attendance['class_date'],
//                 'in_time' => $attendance['in_time'],
//                 'out_time' => $attendance['out_time'],
//                 'attendance_status' => $attendance['attendance_status']
//             ),
//             array('%d', '%s', '%s', '%s', '%s', '%d')
//         );

//         if ($result === false) {
//             wp_send_json_error(array('message' => 'Failed to save attendance for student ID ' . $attendance['student_id']));
//             wp_die();
//         }
//     }

//     wp_send_json_success(array('message' => 'Attendance data saved successfully!'));
//     wp_die();
// }


add_action('wp_ajax_update_checkouts', 'update_checkouts');
add_action('wp_ajax_nopriv_update_checkouts', 'update_checkouts');

function update_checkouts() {
    global $wpdb;

    $class_id = $_POST['class_id'];
    $attendance_data = $_POST['attendance_data'];

    foreach ($attendance_data as $attendance) {
        $result = $wpdb->update(
            $wpdb->prefix . 'student_attendance',
            array(
                'out_time' => $attendance['out_Time'], // Make sure the array key matches the case in your JavaScript
            ),
            array(
                'attendance_id' => $attendance['student_attend_id'],
                'class_id' => $class_id,
                'student_id' => $attendance['student_id'],
                'in_time' => $attendance['in_Time'],
                'attendance_date' => $attendance['class_date']
            ),
            array('%s'), // Adjust based on your column types
            array('%d', '%d', '%s', '%s', '%s') // Adjust based on your WHERE clause columns
        );

        if ($result === false) {
            wp_send_json_error(array('message' => 'Failed to update checkout for student ID ' . $attendance['student_id']));
            wp_die();
        }
    }

    wp_send_json_success(array('message' => 'Checkout data updated successfully!'));
    wp_die();
}

