<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function sms_install() {
    sms_create_sms_tables();
    error_log('Check: Good' );
}

function sms_uninstall() {
    sms_drop_students_table();
}

function sms_deactivate() {

    sms_drop_students_table(); //for Tempory

}

// function sms_create_sms_tables(){
//     global $wpdb;
//     $charset_collate = $wpdb->get_charset_collate();
    
//     $table_students = $wpdb->prefix . 'students';
//     $table_parent = $wpdb->prefix . 'parent';
//     $table_subject = $wpdb->prefix . 'subject';
//     $table_teachers = $wpdb->prefix . 'teachers';
//     $table_classes = $wpdb->prefix . 'classes';
//     $table_exams = $wpdb->prefix . 'exams';
//     $table_student_payments = $wpdb->prefix . 'student_payments';
//     $table_exam_enrolled_students = $wpdb->prefix . 'exam_enrolled_students';
//     $table_class_enrolled_students = $wpdb->prefix . 'class_enrolled_students';
//     $table_student_attendance = $wpdb->prefix . 'student_attendance';
//     $table_class_enrolled_teachers = $wpdb->prefix . 'class_enrolled_teachers';
//     $table_teacher_payments = $wpdb->prefix . 'teacher_payments';
//     $table_teacher_attendance = $wpdb->prefix . 'teacher_attendance';
    
//     $tables = [
//         "CREATE TABLE $table_students (
//             student_id VARCHAR(100) PRIMARY KEY,
//             first_name VARCHAR(50) NOT NULL,
//             last_name VARCHAR(50) NOT NULL,
//             date_of_birth DATE,
//             gender VARCHAR(50) NOT NULL,
//             contact_info VARCHAR(100),
//             address TEXT,
//             email VARCHAR(100),
//             student_status INT
//         ) $charset_collate;",
        
//         "CREATE TABLE $table_parent (
//             parent_id INT AUTO_INCREMENT PRIMARY KEY,
//             first_name VARCHAR(50) NOT NULL,
//             last_name VARCHAR(50) NOT NULL,
//             phonenumber VARCHAR(100),
//             address TEXT,
//             email VARCHAR(100),
//             student_id VARCHAR(100),
//             FOREIGN KEY (student_id) REFERENCES $table_students(student_id)
//         ) $charset_collate;",
        
//         "CREATE TABLE $table_subject (
//             subject_id INT AUTO_INCREMENT PRIMARY KEY,
//             subject_name VARCHAR(50) NOT NULL,
//             subject_description TEXT
//         ) $charset_collate;",
        
//         "CREATE TABLE $table_teachers (
//             teacher_id VARCHAR(100) PRIMARY KEY,
//             first_name VARCHAR(50) NOT NULL,
//             last_name VARCHAR(50) NOT NULL,
//             gender VARCHAR(50) NOT NULL,
//             hire_date DATE,
//             subject_id INT,
//             phonenumber VARCHAR(100),
//             address TEXT,
//             email VARCHAR(100),
//             FOREIGN KEY (subject_id) REFERENCES $table_subject(subject_id)
//         ) $charset_collate;",
        
//         "CREATE TABLE $table_classes (
//             class_id INT AUTO_INCREMENT PRIMARY KEY,
//             class_name VARCHAR(50) NOT NULL,
//             class_date DATE,
//             class_time TIME,
//             class_description TEXT,
//             subject_id INT,
//             FOREIGN KEY (subject_id) REFERENCES $table_subject(subject_id)
//         ) $charset_collate;",
        
//         "CREATE TABLE $table_exams (
//             exam_id INT AUTO_INCREMENT PRIMARY KEY,
//             exam_name VARCHAR(50) NOT NULL,
//             exam_date DATE,
//             exam_time TIME,
//             exam_status INT,
//             subject_id INT,
//             class_id INT,
//             FOREIGN KEY (subject_id) REFERENCES $table_subject(subject_id),
//             FOREIGN KEY (class_id) REFERENCES $table_classes(class_id)
//         ) $charset_collate;",
        
//         "CREATE TABLE $table_student_payments (
//             payment_id INT AUTO_INCREMENT PRIMARY KEY,
//             student_id VARCHAR(100),
//             payment_amount DECIMAL(10, 2),
//             payment_date DATE,
//             payment_time TIME,
//             payment_method VARCHAR(50),
//             FOREIGN KEY (student_id) REFERENCES $table_students(student_id)
//         ) $charset_collate;",
        
//         "CREATE TABLE $table_exam_enrolled_students (
//             enrollment_id INT AUTO_INCREMENT PRIMARY KEY,
//             exam_id INT,
//             student_id VARCHAR(100),
//             enrollment_date DATE,
//             enrollment_time TIME,
//             enrollment_status INT,
//             FOREIGN KEY (exam_id) REFERENCES $table_exams(exam_id),
//             FOREIGN KEY (student_id) REFERENCES $table_students(student_id)
//         ) $charset_collate;",
        
//         "CREATE TABLE $table_class_enrolled_students (
//             enrollment_id INT AUTO_INCREMENT PRIMARY KEY,
//             class_id INT,
//             student_id VARCHAR(100),
//             enrollment_date DATE,
//             FOREIGN KEY (class_id) REFERENCES $table_classes(class_id),
//             FOREIGN KEY (student_id) REFERENCES $table_students(student_id)
//         ) $charset_collate;",
        
//         "CREATE TABLE $table_student_attendance (
//             attendance_id INT AUTO_INCREMENT PRIMARY KEY,
//             class_id INT,
//             student_id VARCHAR(100),
//             attendance_date DATE,
//             attendance_status INT,
//             FOREIGN KEY (class_id) REFERENCES $table_classes(class_id),
//             FOREIGN KEY (student_id) REFERENCES $table_students(student_id)
//         ) $charset_collate;",
        
//         "CREATE TABLE $table_class_enrolled_teachers (
//             enrollment_id INT AUTO_INCREMENT PRIMARY KEY,
//             class_id INT,
//             teacher_id VARCHAR(100),
//             enrollment_date DATE,
//             enrollment_time TIME,
//             enrollment_status INT,
//             FOREIGN KEY (class_id) REFERENCES $table_classes(class_id),
//             FOREIGN KEY (teacher_id) REFERENCES $table_teachers(teacher_id)
//         ) $charset_collate;",
        
//         "CREATE TABLE $table_teacher_payments (
//             payment_id INT AUTO_INCREMENT PRIMARY KEY,
//             teacher_id VARCHAR(100),
//             payment_amount DECIMAL(10, 2),
//             payment_date DATE,
//             payment_time TIME,
//             payment_method VARCHAR(50),
//             payment_status INT,
//             FOREIGN KEY (teacher_id) REFERENCES $table_teachers(teacher_id)
//         ) $charset_collate;",
        
//         "CREATE TABLE $table_teacher_attendance (
//             attendance_id INT AUTO_INCREMENT PRIMARY KEY,
//             class_id INT,
//             teacher_id VARCHAR(100),
//             attendance_date DATE,
//             attendance_status INT,
//             FOREIGN KEY (class_id) REFERENCES $table_classes(class_id),
//             FOREIGN KEY (teacher_id) REFERENCES $table_teachers(teacher_id)
//         ) $charset_collate;"
//     ];
    
//     require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

//     // Execute each table creation query separately
//     foreach ($tables as $table_query) {
//         $wpdb->query($table_query);
//     }
    

//     // require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
//     // dbDelta( $sql );
// }

function sms_create_sms_tables()
{
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $table_students = $wpdb->prefix . 'students';
    $table_parent = $wpdb->prefix . 'parent';
    $table_subject = $wpdb->prefix . 'subject';
    $table_teachers = $wpdb->prefix . 'teachers';
    $table_classes = $wpdb->prefix . 'classes';
    $table_exams = $wpdb->prefix . 'exams';
    $table_student_payments = $wpdb->prefix . 'student_payments';
    $table_exam_enrolled_students = $wpdb->prefix . 'exam_enrolled_students';
    $table_class_enrolled_students = $wpdb->prefix . 'class_enrolled_students';
    $table_student_attendance = $wpdb->prefix . 'student_attendance';
    $table_class_enrolled_teachers = $wpdb->prefix . 'class_enrolled_teachers';
    $table_teacher_payments = $wpdb->prefix . 'teacher_payments';
    $table_teacher_attendance = $wpdb->prefix . 'teacher_attendance';

    $sql = "CREATE TABLE $table_students(
        student_id VARCHAR(100) PRIMARY KEY,
        first_name VARCHAR(50) NOT NULL,
        last_name VARCHAR(50) NOT NULL,
        date_of_birth DATE,
        gender VARCHAR(50) NOT NULL,
        contact_info VARCHAR(100),
        address TEXT,
        email VARCHAR(100),
        student_status INT
    ); CREATE TABLE $table_parent(
        parent_id INT AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(50) NOT NULL,
        last_name VARCHAR(50) NOT NULL,
        phonenumber VARCHAR(100),
        address TEXT,
        email VARCHAR(100),
        student_id VARCHAR(100),
        FOREIGN KEY(student_id) REFERENCES $table_students(student_id)
    ); CREATE TABLE $table_subject(
        subject_id INT AUTO_INCREMENT PRIMARY KEY,
        subject_name VARCHAR(50) NOT NULL,
        subject_description TEXT
    ); CREATE TABLE $table_teachers(
        teacher_id VARCHAR(100) PRIMARY KEY,
        first_name VARCHAR(50) NOT NULL,
        last_name VARCHAR(50) NOT NULL,
        gender VARCHAR(50) NOT NULL,
        hire_date DATE,
        subject_id INT,
        phonenumber VARCHAR(100),
        address TEXT,
        email VARCHAR(100),
        FOREIGN KEY(subject_id) REFERENCES $table_subject(subject_id)
    ); CREATE TABLE $table_classes(
        class_id INT AUTO_INCREMENT PRIMARY KEY,
        subject_id INT NOT NULL,
        teacher_id VARCHAR(100) NOT NULL,
        class_date date NOT NULL,
        class_time time NOT NULL,
        class_grade varchar(10) NOT NULL,
        class_fee decimal(10,2) NOT NULL,
        FOREIGN KEY(subject_id) REFERENCES $table_subject(subject_id),
        FOREIGN KEY(teacher_id) REFERENCES $table_teachers(teacher_id)
    ); CREATE TABLE $table_exams(
        exam_id INT AUTO_INCREMENT PRIMARY KEY,
        exam_name VARCHAR(50) NOT NULL,
        exam_date DATE,
        exam_time TIME,
        exam_status INT,
        subject_id INT,
        class_id INT,
        FOREIGN KEY(subject_id) REFERENCES $table_subject(subject_id),
        FOREIGN KEY(class_id) REFERENCES $table_classes(class_id)
    ); CREATE TABLE $table_student_payments(
        payment_id INT AUTO_INCREMENT PRIMARY KEY,
        student_id VARCHAR(100),
        payment_amount DECIMAL(10, 2),
        payment_date DATE,
        payment_time TIME,
        payment_method VARCHAR(50),
        FOREIGN KEY(student_id) REFERENCES $table_students(student_id)
    ); CREATE TABLE $table_exam_enrolled_students(
        enrollment_id INT AUTO_INCREMENT PRIMARY KEY,
        exam_id INT,
        student_id VARCHAR(100),
        enrollment_date DATE,
        enrollment_time TIME,
        enrollment_status INT,
        FOREIGN KEY(exam_id) REFERENCES $table_exams(exam_id),
        FOREIGN KEY(student_id) REFERENCES $table_students(student_id)
    ); CREATE TABLE $table_class_enrolled_students(
        enrollment_id INT AUTO_INCREMENT PRIMARY KEY,
        class_id INT,
        student_id VARCHAR(100),
        enrollment_date DATE,
        FOREIGN KEY(class_id) REFERENCES $table_classes(class_id),
        FOREIGN KEY(student_id) REFERENCES $table_students(student_id)
    ); CREATE TABLE $table_student_attendance(
        attendance_id INT AUTO_INCREMENT PRIMARY KEY,
        class_id INT,
        student_id VARCHAR(100),
        attendance_date DATE,
        attendance_status INT,
        FOREIGN KEY(class_id) REFERENCES $table_classes(class_id),
        FOREIGN KEY(student_id) REFERENCES $table_students(student_id)
    ); CREATE TABLE $table_class_enrolled_teachers(
        enrollment_id INT AUTO_INCREMENT PRIMARY KEY,
        class_id INT,
        teacher_id VARCHAR(100),
        enrollment_date DATE,
        enrollment_time TIME,
        enrollment_status INT,
        FOREIGN KEY(class_id) REFERENCES $table_classes(class_id),
        FOREIGN KEY(teacher_id) REFERENCES $table_teachers(teacher_id)
    ); CREATE TABLE $table_teacher_payments(
        payment_id INT AUTO_INCREMENT PRIMARY KEY,
        teacher_id VARCHAR(100),
        payment_amount DECIMAL(10, 2),
        payment_date DATE,
        payment_time TIME,
        payment_method VARCHAR(50),
        payment_status INT,
        FOREIGN KEY(teacher_id) REFERENCES $table_teachers(teacher_id)
    ); CREATE TABLE $table_teacher_attendance(
        attendance_id INT AUTO_INCREMENT PRIMARY KEY,
        class_id INT,
        teacher_id VARCHAR(100),
        attendance_date DATE,
        attendance_status INT,
        FOREIGN KEY(class_id) REFERENCES $table_classes(class_id),
        FOREIGN KEY(teacher_id) REFERENCES $table_teachers(teacher_id)
    );";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    // Check for any errors
    if ($wpdb->last_error) {
        error_log('DB Error: ' . $wpdb->last_error);
    }
}

// register_activation_hook(__FILE__, 'create_tables');


function sms_drop_students_table() {

    global $wpdb;

    $tables = [
        $wpdb->prefix . 'parent',
        $wpdb->prefix . 'students',
        $wpdb->prefix . 'subject',
        $wpdb->prefix . 'teachers',
        $wpdb->prefix . 'classes',
        $wpdb->prefix . 'exams',
        $wpdb->prefix . 'student_payments',
        $wpdb->prefix . 'exam_enrolled_students',
        $wpdb->prefix . 'class_enrolled_students',
        $wpdb->prefix . 'student_attendance',
        $wpdb->prefix . 'class_enrolled_teachers',
        $wpdb->prefix . 'teacher_payments',
        $wpdb->prefix . 'teacher_attendance'
    ];

    foreach ($tables as $table) {
        // $wpdb->query("SET FOREIGN_KEY_CHECKS = 0; DROP TABLE IF EXISTS $table; SET FOREIGN_KEY_CHECKS = 1;");
        $wpdb->query("SET FOREIGN_KEY_CHECKS = 0;");
        $wpdb->query("DROP TABLE IF EXISTS $table;");
        $wpdb->query("SET FOREIGN_KEY_CHECKS = 1;");
    }


}
?>
