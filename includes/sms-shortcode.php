<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_shortcode( 'student_list', 'sms_student_list_shortcode' );

// Register the shortcode for the student registration form
add_shortcode('student_registration', 'sms_student_registration_form');

