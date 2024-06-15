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

        echo '<div class="error">"'.$subject_id.'" ,"'.$teacher_id.'" , "'.$date.'" , "'.$time.'" , "'.$grade.'" ,"'.$fee.'"</div>';


        // $wpdb->insert(
        //     $table_name,
        //     array(
        //         'subject_id' => $subject_id,
        //         'teacher_id' => $teacher_id,
        //         'date' => $date,
        //         'time' => $time,
        //         'grade' => $grade,
        //         'fee' => $fee
        //     )
        // );

        // if ($wpdb->insert_id) {
        //     echo '<script>alert("Class registered successfully!");</script>';
        // } else {
        //     echo '<script>alert("Failed to register class. Please try again.");</script>';
        // }



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