<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
    // error_log('Check: Subject Registration !!!' );

    // var_dump("Subject Managment.php loaded !!!");

    global $wpdb;
    $table_name = $wpdb->prefix . 'subject';

    if ( isset( $_POST['sms_submit'] ) && $_POST['sms_submit'] === "Update Subject" ) {
        sms_update_subject();
        echo '<script>window.location.href="?page=sms-subject-registration";</script>';
    } else if ( isset( $_POST['sms_submit'] ) && $_POST['sms_submit'] === "Register Subject" ) {
        // error_log('Check: Subject Registration !!!' );
        sms_register_subject();
        echo '<script>window.location.href="?page=sms-subject-registration";</script>';
    }

    // if ( isset( $_GET['delete'] ) ) {
    //     sms_delete_subject($_GET['delete']);
    //     echo '<script>window.location.href="?page=sms-subject-registration";</script>';
    // }


    if ( isset( $_GET['delete'] ) ) {
        // Check if the delete parameter is set
        $subject_id = $_GET['delete'];
    
        // Display a confirmation alert
        echo "<script>
                if (confirm('Are you sure you want to delete this subject?')) {
                    window.location.href = '?page=sms-subject-registration&action=delete&subject_id=$subject_id';
                } else {
                    window.location.href = '?page=sms-subject-registration';
                }
              </script>";
    } elseif ( isset( $_GET['action'] ) && $_GET['action'] === 'delete' && isset( $_GET['subject_id'] ) ) {
        // Perform subject deletion
        $result = sms_delete_subject($_GET['subject_id']);
        echo "<script>window.location.href='?page=sms-subject-registration';</script>";
        
        // Check if deletion was successful
        // if ($result) {
        //     // Redirect after deletion
        //     echo "<script>window.location.href='?page=sms-subject-registration';</script>";
        // } else {
        //     // Display an error message
        //     echo "<script>alert('Failed to delete subject.');</script>";
        // }
    }

?>
<!-- templates/admin/subject-management.php -->
<h1>Subject Management</h1>
<form id="subject-registration-form" method="post" action="">
    <input type="hidden" id="subject_id" name="subject_id">
    <div>
        <label for="subject_name">Subject Name:</label>
        <input type="text" id="subject_name" name="subject_name" value="" required>
    </div>
    <div>
        <label for="subject_description">Subject Description:</label>
        <textarea id="subject_description" name="subject_description" required></textarea>
    </div>
    <div>
    <?php wp_nonce_field('subject_form_action', 'subject_form_nonce'); ?>
        <input type="submit" name="sms_submit" id="submit_subject" value="Register Subject">
    </div>
</form>
<div id="registration-message"></div>
<br>
<table id="subject-table" border="1" cellpadding="10">
    <thead>
        <tr><th>ID</th><th>Name</th><th>Description</th><th>Actions</th></tr>
    </thead>
    <tbody id="sms_subject_table">
        <!-- Subject data will be loaded here -->
        <?php sms_subject_table(); ?>
    </tbody>
</table>

<?php


