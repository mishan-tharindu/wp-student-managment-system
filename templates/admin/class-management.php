<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

    global $wpdb;

    if(isset( $_POST['sms_submit'] ) && $_POST['sms_submit'] === "Register Class"){
        sms_register_class();
        // error_log("Calling Teacher Registration !!!");
    }else if(isset( $_POST['sms_submit'] ) && $_POST['sms_submit'] === "Update Class"){
        sms_update_class($_GET['edit']);
    }

    if ( isset( $_GET['delete'] ) ) {
        sms_delete_class($_GET['delete']);
        // echo '<script>window.location.href="?page=sms-teachers-registration"";</script>';
    }

    // Fetch subjects from the database
    $subjects_table = $wpdb->prefix . 'subject';
    $subjects = $wpdb->get_results("SELECT * FROM $subjects_table");

    // Fetch teachers from the database
    $teachers_table = $wpdb->prefix . 'teachers';
    $teachers = $wpdb->get_results("SELECT * FROM $teachers_table");
    ?>
    <form id="class-registration-form" method="post" action="">
        <div>
            <?php wp_nonce_field('class_form_action', 'class_form_nonce'); ?>
            <label for="subject_id">Subject:</label>
            <select id="subject_id" name="subject_id" required>
                <?php
                if ($subjects) {
                    foreach ($subjects as $subject) {
                        echo '<option value="' . esc_attr($subject->subject_id) . '">' . esc_html($subject->subject_name) . '</option>';
                    }
                }
                ?>
            </select>
        </div>
        <div>
            <label for="teacher_id">Teacher:</label>
            <select id="teacher_id" name="teacher_id" required>
                <?php
                if ($teachers) {
                    foreach ($teachers as $teacher) {
                        echo '<option value="' . esc_attr($teacher->teacher_id) . '">' . esc_html($teacher->first_name . ' ' . $teacher->last_name) . '</option>';
                    }
                }
                ?>
            </select>
        </div>
        <div>
            <label for="date">Date:</label>
            <input type="date" id="date" name="date" required>
        </div>
        <div>
            <label for="time">Time:</label>
            <input type="time" id="time" name="time" required>
        </div>
        <div>
            <label for="grade">Grade:</label>
            <input type="text" id="grade" name="grade" required>
        </div>
        <div>
            <label for="fee">Fee:</label>
            <input type="number" id="fee" name="fee" required>
        </div>
        <div>
            <input type="submit" id="submit_class" name="sms_submit" value="Register Class">
        </div>
    </form>
    <div id="registration-message"></div>          
                
    <?php

    echo sms_show_classes_table();

    // return ob_get_clean();
?>