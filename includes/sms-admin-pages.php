<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function sms_menu() {
    add_menu_page( 'Student Management', 'Student Management', 'manage_options', 'sms-student-management', 'sms_students_management_page', 'dashicons-welcome-learn-more', 6 );
    
    add_submenu_page(
        'sms-student-management',
        'Teachers Management',
        'Teachers Management',
        'manage_options',
        'sms-teachers-registration',
        'sms_teachers_registration_page'
    );

    add_submenu_page(
        'sms-student-management',
        'Subject Management',
        'Subject Management',
        'manage_options',
        'sms-subject-registration',
        'sms_subject_registration_page'
    );

    add_submenu_page(
        'sms-student-management',
        'Class Management',
        'Class Management',
        'manage_options',
        'sms-class-registration',
        'sms_class_registration_page'
    );

    add_submenu_page(
        'sms-student-management',
        'Exam Management',
        'Exam Management',
        'manage_options',
        'sms-exam-registration',
        'sms_exam_registration_page'
    );
}

function sms_students_management_page() {
    include plugin_dir_path( __DIR__ ) . 'templates/admin/students-management.php';
}

function sms_teachers_registration_page() {
    ?>
    <div class="wrap">
        <h1>Teachers Management</h1>
        <p>Content for Teachers Management page.</p>
    </div>
    <?php
    include plugin_dir_path( __DIR__ ) . 'templates/admin/teachers-management.php';
}

function sms_subject_registration_page() {
    ?>
    <div class="wrap">
        <h1>Subject Management</h1>
        <p>Content for Subject Management page.</p>
    </div>
    <?php
    include plugin_dir_path( __DIR__ ) . 'templates/admin/subject-management.php';
}

function sms_class_registration_page() {
    ?>
    <div class="wrap">
        <h1>Class Management</h1>
        <p>Content for Class Management page.</p>
    </div>
    <?php
    // echo sms_class_registration_form();
    include plugin_dir_path( __DIR__ ) . 'templates/admin/class-management.php';
}

function sms_exam_registration_page() {
    ?>
    <div class="wrap">
        <h1>Exam Management</h1>
        <p>Content for Exam Management page.</p>
    </div>
    <?php
}




