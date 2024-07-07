<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function sms_register_class(){

        // Check nonce
        if (!isset($_POST['class_form_nonce']) || !wp_verify_nonce($_POST['class_form_nonce'], 'class_form_action')) {
            wp_die('Invalid nonce.');
            echo '<div class="error">Nonce verification failed.</div>';
            return;
        }

        // echo '<div class="error">After Else Nonce </div>';
        global $wpdb;
        $table_name = $wpdb->prefix . 'classes';

        $subject_id = sanitize_text_field($_POST['subject_id']);
        $teacher_id = sanitize_text_field($_POST['teacher_id']);
        $date = sanitize_text_field($_POST['date']);
        $time = sanitize_text_field($_POST['time']);
        $grade = sanitize_text_field($_POST['grade']);
        $fee = sanitize_text_field($_POST['fee']);

        // echo '<div class="error">"'.$subject_id.'" ,"'.$teacher_id.'" , "'.$date.'" , "'.$time.'" , "'.$grade.'" ,"'.$fee.'"</div>';


        $wpdb->insert(
            $table_name,
            array(
                'subject_id' => $subject_id,
                'teacher_id' => $teacher_id,
                'class_date' => $date,
                'class_time' => $time,
                'class_grade' => $grade,
                'class_fee' => $fee
            )
        );

        if ($wpdb->insert_id) {
            echo '<script>alert("Class registered successfully!");</script>';
            echo '<script>window.location.href="?page=sms-class-registration";</script>';
        } else {
            echo '<script>alert("Failed to register class. Please try again.");</script>';
        }



}

function generate_unique_classID(){

    global $wpdb;
    $table_name = $wpdb->prefix . 'classes';
    $unique = false;
    
    while (!$unique) {
        $random_number = mt_rand(1000, 9999);
        $date_part = date('Ymd');
        $student_id = 'CLZ' . $date_part . $random_number;

        $existing_id = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE class_id = %s", $student_id));

        if ($existing_id == 0) {
            $unique = true;
        }
    }

    return $student_id;

}

function sms_show_classes_table(){

    global $wpdb;
    $class_table = $wpdb->prefix . 'classes';
    $teachers_table = $wpdb->prefix . 'teachers';
    $subject_table = $wpdb->prefix . 'subject';
    $classes = $wpdb->get_results("SELECT * FROM $class_table INNER JOIN $teachers_table ON $class_table.teacher_id = $teachers_table.teacher_id INNER JOIN $subject_table ON $class_table.subject_id = $subject_table.subject_id ");

    if ($classes) {
        echo '<table ID="registered-classes-table" class="widefat fixed" cellspacing="0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Subject Name</th>
                    <th>Grade</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Class Fees</th>
                    <th>Teacher Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>';

        foreach ($classes as $class) {
            // $tabel_name = $wpdb->prefix . "subject";
            // $subjects = $wpdb->get_results("SELECT * FROM WHERE subject_id = $teacher->subject_id");
            // $subject_name;
            // foreach ($subjects as $subject) {
            //     $subject_name = $subject->subject_name;
            // // }
            // $subject_name = $wpdb->get_var($wpdb->prepare("SELECT subject_name FROM {$wpdb->prefix}subject WHERE subject_id = %d", $class->subject_id));
            // $teacher_name = $wpdb->get_var($wpdb->prepare("SELECT first_name, last_name FROM {$wpdb->prefix}teachers WHERE teacher_id = %d", $class->teacher_id));
            
            echo '<tr data-class-id="' . esc_attr( $class->class_id ) . '" data-subject-name="' . esc_attr( $class->subject_id ) . '" data-class-grade="' . esc_attr( $class->class_grade) . '" data-class-date="' . esc_attr( $class->class_date) . '"  data-class-time="' . esc_attr( $class->class_time) . '" data-class-teacher="' . esc_html($class->teacher_id) . '" data-class-fee="' . esc_attr( $class->class_fee) . '">';
            echo '<td>' . esc_html($class->class_id) . '</td>';
            echo '<td>'.esc_html($class-> subject_name).'</td>';
            echo '<td>' . esc_html($class->class_grade) . '</td>';
            echo '<td>' . esc_html($class->class_date) . '</td>';
            echo '<td>' . esc_html($class->class_time) . '</td>';
            echo '<td>' . esc_html($class->class_fee) . '</td>';
            echo '<td>' . esc_html($class->first_name) . ' '.esc_html($class->last_name).'</td>';
            echo '<td><a class="edit-class" href="?page=sms-class-registration&edit=' . esc_attr($class->class_id) . '">Edit</a> | <a href="?page=sms-class-registration&delete=' . esc_attr($class->class_id) . '" onclick="return confirm(\'Are you sure you want to delete this teacher?\')">Delete</a></td>';
            echo '</tr>';
        }

        echo '</tbody></table>';
    } else {
        echo '<p>No Class found.</p>';
    }
}

function sms_update_class($classID){

    global $wpdb;
    $table_name = $wpdb->prefix . 'classes';

    $subject_id = sanitize_text_field($_POST['subject_id']);
    $teacher_id = sanitize_text_field($_POST['teacher_id']);
    $date = sanitize_text_field($_POST['date']);
    $time = sanitize_text_field($_POST['time']);
    $grade = sanitize_text_field($_POST['grade']);
    $fee = sanitize_text_field($_POST['fee']);


    $result = $wpdb->update( 
        $table_name, 
        array( 
            'subject_id' => $subject_id,
            'teacher_id' => $teacher_id,
            'class_date' => $date,
            'class_time' => $time,
            'class_grade' => $grade,
            'class_fee' => $fee
        ), 
        array( 'class_id' => $classID ) 
    );

        // Provide feedback to the user
        if ($result) {
            echo '<div class="success">Class Updated successfully!</div>';
            echo '<script>window.location.href="?page=sms-class-registration";</script>';
        } else {
            echo '<div class="error">Failed to updated Class. Please try again.</div>';
        }

}

function sms_delete_class($classID){
    global $wpdb;
    $table_name = $wpdb->prefix . 'classes';

    $result = $wpdb->delete( $table_name, array( 'class_id' => $classID ));

    if ( $result === false ) {
        echo '<div class="error">Error occurred while deleting student data.</div>';
    } else {
        echo '<div class="success">Student data deleted successfully.</div>';
        echo '<script>window.location.href="?page=sms-class-registration";</script>';
    }
}

// Handle Ajax search request
add_action( 'wp_ajax_sms_search_classes', 'sms_search_classes' );
// add_action( 'wp_ajax_nopriv_sms_search_classes', 'sms_search_classes' );

function sms_search_classes(){
    echo ("sms_search_classes Function !!! ");


    if (!isset($_GET['student_search'])) {
        wp_send_json_error('Missing search parameter');
        return;
    }

    global $wpdb;
    $search_query = sanitize_text_field($_GET['student_search']);

    $query = $wpdb->prepare(
        "SELECT c.class_id, s.subject_name AS subject, CONCAT(t.first_name, ' ', t.last_name) AS teacher, c.class_date, c.class_time, c.class_grade, c.class_fee
         FROM {$wpdb->prefix}classes c
         JOIN {$wpdb->prefix}subject s ON c.subject_id = s.subject_id
         JOIN {$wpdb->prefix}teachers t ON c.teacher_id = t.teacher_id
         WHERE s.subject_name LIKE %s", '%' . $wpdb->esc_like($search_query) . '%'
    );

    $classes = $wpdb->get_results($query);

    if ($wpdb->last_error) {
        wp_send_json_error($wpdb->last_error);
        return;
    }

    if ($classes) {
        foreach ($classes as $class) {
            echo '<tr>';
            echo '<td>' . esc_html($class->class_id) . '</td>';
            echo '<td>' . esc_html($class->subject) . '</td>';
            echo '<td>' . esc_html($class->teacher) . '</td>';
            echo '<td>' . esc_html($class->class_date) . '</td>';
            echo '<td>' . esc_html($class->class_time) . '</td>';
            echo '<td>' . esc_html($class->class_grade) . '</td>';
            echo '<td>' . esc_html($class->class_fee) . '</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="7">No classes found.</td></tr>';
    }

    wp_die();



}

add_action('wp_ajax_load_all_classes', 'load_all_classes');
add_action('wp_ajax_nopriv_load_all_classes', 'load_all_classes');

function load_all_classes() {
    global $wpdb;
    $classes_table = $wpdb->prefix . 'classes';
    $subjects_table = $wpdb->prefix . 'subject';
    $teachers_table = $wpdb->prefix . 'teachers';

    $classes = $wpdb->get_results("
        SELECT c.*, s.subject_name, t.first_name AS teacher_first_name, t.last_name AS teacher_last_name
        FROM $classes_table c
        JOIN $subjects_table s ON c.subject_id = s.subject_id
        JOIN $teachers_table t ON c.teacher_id = t.teacher_id
    ");

    if ($wpdb->last_error) {
        echo $wpdb->last_error;
        wp_die();
    }

    if ($classes) {
        foreach ($classes as $class) {
            echo '<tr data-class-id="'.esc_html($class->class_id).'">';
            echo '<td>' . esc_html($class->class_id) . '</td>';
            echo '<td>' . esc_html($class->subject_name) . '</td>';
            echo '<td>' . esc_html($class->teacher_first_name . ' ' . $class->teacher_last_name) . '</td>';
            echo '<td>' . esc_html($class->class_date) . '</td>';
            echo '<td>' . esc_html($class->class_time) . '</td>';
            echo '<td>' . esc_html($class->class_grade) . '</td>';
            echo '<td>' . esc_html($class->class_fee) . '</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="7">No classes found.</td></tr>';
    }

    wp_die();
}

function enroll_students() {
    global $wpdb;
    $data = json_decode(file_get_contents('php://input'), true);
    $class_id = intval($data['class_id']);
    $students = $data['students'];
    $existing_students = $wpdb->get_col($wpdb->prepare(
        "SELECT student_id FROM {$wpdb->prefix}class_enrolled_students WHERE class_id = %d", $class_id
    ));

    $new_students = [];
    $duplicate_students = [];

    foreach ($students as $student) {
        if (in_array($student['student_id'], $existing_students)) {
            $duplicate_students[] = $student['student_id'];
        } else {
            $new_students[] = [
                'class_id' => $class_id,
                'student_id' => $student['student_id'],
                'enrollment_date' => current_time('mysql')
            ];
        }
    }

    if (!empty($new_students)) {
        $wpdb->insert_batch("{$wpdb->prefix}class_enrolled_students", $new_students);
    }

    if (empty($duplicate_students)) {
        wp_send_json_success();
    } else {
        wp_send_json_error(['duplicates' => $duplicate_students]);
    }
}

// Function to search students
function sms_search_class_students() {
    global $wpdb;

    $search_term = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';

    if (empty($search_term)) {
        wp_send_json([]);
        return;
    }

    $table_name = $wpdb->prefix . 'students';
    $results = $wpdb->get_results($wpdb->prepare(
        "SELECT student_id, CONCAT(first_name, ' ', last_name) AS student_name 
         FROM $table_name 
         WHERE first_name LIKE %s OR last_name LIKE %s",
        '%' . $wpdb->esc_like($search_term) . '%',
        '%' . $wpdb->esc_like($search_term) . '%'
    ));

    $students = [];
    foreach ($results as $result) {
        $students[] = [
            'id' => $result->student_id,
            'text' => $result->student_name
        ];
    }

    wp_send_json($students);
}

add_action('wp_ajax_sms_search_class_students', 'sms_search_class_students');
add_action('wp_ajax_nopriv_sms_search_class_students', 'sms_search_class_students');

// Function to enroll students to a class
function enroll_students_to_class() {
    // global $wpdb;

    // // Verify the nonce for security
    // // check_ajax_referer('sms_enroll_students', 'security');
    
    //     // Verify the nonce for security
    //     if (!isset($_POST['security']) || !wp_verify_nonce($_POST['security'], 'sms_enroll_students_nonce')) {
    //         wp_send_json_error('Nonce verification failed.');
    //         return;
    //     }

    //     $class_id = isset($_POST['class_id']) ? intval($_POST['class_id']) : 0;
    //     $students = isset($_POST['students']) ? $_POST['students'] : [];

    //     if ($class_id === 0 || empty($students)) {
    //         wp_send_json_error('Invalid data.');
    //         return;
    //     }

    //     $table_name = $wpdb->prefix . 'class_enrolled_students';
    //     $already_enrolled_students = [];
    //     $enrolled_students = [];
    //     $errors = [];

    //     foreach ($students as $student) {
    //         $student_id = intval($student['student_id']);
    //         $enrollment_date = current_time('mysql');

    //         if ($student_id === 0) {
    //             $errors[] = "Invalid student ID $student_id.";
    //             continue;
    //         }

    //         // Check if the student ID exists in the wp_students table
    //         $student_exists = $wpdb->get_var($wpdb->prepare(
    //             "SELECT COUNT(*) FROM {$wpdb->prefix}students WHERE student_id = %d",
    //             $student_id
    //         ));

    //         if ($student_exists == 0) {
    //             $errors[] = "Student ID $student_id does not exist.";
    //             continue;
    //         }

    //         // Check if the student is already enrolled in the class
    //         $already_enrolled = $wpdb->get_var($wpdb->prepare(
    //             "SELECT COUNT(*) FROM $table_name WHERE class_id = %d AND student_id = %d",
    //             $class_id, $student_id
    //         ));

    //         if ($already_enrolled == 0) {
    //             $inserted = $wpdb->insert($table_name, [
    //                 'class_id' => $class_id,
    //                 'student_id' => $student_id,
    //                 'enrollment_date' => $enrollment_date
    //             ]);

    //             if ($inserted !== false) {
    //                 $enrolled_students[] = $student_id;
    //             } else {
    //                 // Log the error for debugging
    //                 $errors[] = "Failed to insert student ID $student_id: " . $wpdb->last_error;
    //             }
    //         } else {
    //             $already_enrolled_students[] = $student_id;
    //         }
    //     }

    //     if (!empty($errors)) {
    //         wp_send_json_error($errors);
    //         return;
    //     }

    //     $response = [
    //         'message' => 'Students enrolled successfully.',
    //         'already_enrolled' => $already_enrolled_students,
    //         'enrolled' => $enrolled_students
    //     ];

    //     wp_send_json_success($response);

}

add_action('wp_ajax_save_enrollment_data_callback', 'save_enrollment_data_callback');
add_action('wp_ajax_nopriv_save_enrollment_data_callback', 'save_enrollment_data_callback');

function save_enrollment_data_callback() {

    global $wpdb;

    $class_id = $_POST['class_id'];
    $student_ids = $_POST['student_ids'];

    $invalid_students = [];
    $already_enrolled_students = [];
    $enrolled_students = [];

    // Validate student IDs
    foreach ($student_ids as $student_id) {
        if (!validate_student_id($student_id)) {
            $invalid_students[] = $student_id;
        }
    }

    // If there are invalid students, handle the error
    if (!empty($invalid_students)) {
        wp_send_json_error(array('message' => 'Invalid student IDs: ' . implode(', ', $invalid_students)));
        wp_die();
    }

    // Insert valid students into database
    $table_name = $wpdb->prefix . 'class_enrolled_students';

    foreach ($student_ids as $student_id) {
        $query = $wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE student_id = %s AND class_id = %s", $student_id, $class_id);
        $search_result_count = $wpdb->get_var($query);

        if ($search_result_count > 0) {
            $already_enrolled_students[] = $student_id;
        } else {
            $result = $wpdb->insert(
                $table_name,
                array(
                    'class_id' => $class_id,
                    'student_id' => $student_id,
                    'enrollment_date' => current_time('mysql')
                ),
                array('%d', '%s', '%s')
            );

            if ($result === false) {
                wp_send_json_error(array('message' => 'Failed to insert student ID ' . $student_id . ': ' . $wpdb->last_error));
                wp_die();
            } else {
                $enrolled_students[] = $student_id;
            }
        }
    }

    // Success message
    $message = 'Enrollment data saved successfully!';
    if (!empty($already_enrolled_students)) {
        $message .= ' However, these students were already enrolled: ' . implode(', ', $already_enrolled_students);
    }
    wp_send_json_success(array('message' => $message));
    wp_die();

}

function validate_student_id($student_id) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'students';
    $query = $wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE student_id = %s", $student_id);
    $count = $wpdb->get_var($query);

    return $count > 0;
}

