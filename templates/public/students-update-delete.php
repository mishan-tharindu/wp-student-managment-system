<?php
// Function to display the student registration form
function sms_student_update_delete_form() {
    ob_start();
    ?>
    <form id="student-update-delete-form" method="post" action="">
        <div>
            <label for="student_id">Student Id:</label>
            <input type="text" id="student_id" name="student_id" value="<?php echo generate_unique_student_id(); ?>" required readonly>
        </div>
        <div>
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" required>
        </div>
        <div>
            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" required>
        </div>
        <div>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div>
            <input type="hidden" id="action_type" name="action_type" value="register">
            <input type="submit" id="submit_student" value="Register Student">
        </div>
    </form>
    <div id="registration-message"></div>
    <br>
    <table border="1" cellpadding="10">
        <thead>
            <tr><th>ID</th><th>Name</th><th>Email</th><th>Actions</th></tr>
        </thead>
        <tbody id="sms_student_table">
            <?php
            global $wpdb;
            $table_name = $wpdb->prefix . 'students';
            $students = $wpdb->get_results("SELECT * FROM $table_name");

            if ($students) {
                foreach ($students as $student) {
                    echo '<tr>';
                    echo '<td>' . esc_html($student->student_id) . '</td>';
                    echo '<td>' . esc_html($student->first_name) . '</td>';
                    echo '<td>' . esc_html($student->email) . '</td>';
                    echo '<td><button class="edit-student" data-id="' . esc_attr($student->student_id) . '">Edit</button> <button class="delete-student" data-id="' . esc_attr($student->student_id) . '">Delete</button></td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="4">No students found.</td></tr>';
            }
            ?>
        </tbody>
    </table>
    <!-- <div id="delete-confirmation" style="display:none;">
        <p>Are you sure you want to delete this student?</p>
        <p>ID: <span id="delete-student-id"></span></p>
        <p>Name: <span id="delete-student-name"></span></p>
        <input type="checkbox" id="confirm-delete" name="confirm-delete" value="yes"> Confirm
        <button id="confirm-delete-button">Delete</button>
    </div> -->

    <!-- Delete Confirmation Popup -->
    <div id="popup-overlay"></div>
    <div id="delete-confirmation">
        <button id="close-popup" style="float: right;">&times;</button>
        <h2>Delete Student</h2>
        <div class="popup-content">
            <p>Are you sure you want to delete the following student?</p>
            <p><strong>ID:</strong> <span id="delete-student-id"></span></p>
            <p><strong>Name:</strong> <span id="delete-student-name"></span></p>
            <label><input type="checkbox" id="confirm-delete"> Yes, I want to delete this student</label>
        </div>
        <button id="confirm-delete-button">Delete</button>
        <button id="cancel-delete-button">Cancel</button>
    </div>

    <?php

    return ob_get_clean();
}

// Shortcode for student registration form
add_shortcode('sms_student_update_delete_form', 'sms_student_update_delete_form');


//Delete Update Funtions

// AJAX handler for student update
add_action('wp_ajax_sms_update_student', 'sms_update_student_ajax_handler');
add_action('wp_ajax_nopriv_sms_update_student', 'sms_update_student_ajax_handler');

function sms_update_student_ajax_handler() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'students';

    // Retrieve and sanitize form data
    $student_id = sanitize_text_field($_POST['student_id']);
    $first_name = sanitize_text_field($_POST['first_name']);
    $last_name = sanitize_text_field($_POST['last_name']);
    $email = sanitize_email($_POST['email']);

    // Update student data in the database
    $result = $wpdb->update(
        $table_name,
        array(
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email
        ),
        array('student_id' => $student_id)
    );

    // Provide feedback to the user
    if ($result !== false) {
        // Get updated list of students and send it back as JSON response
        $students = $wpdb->get_results("SELECT * FROM $table_name");
        wp_send_json_success($students);
    } else {
        wp_send_json_error('Failed to update student. Please try again.');
    }

    // Always exit to avoid further execution
    wp_die();
}

// AJAX handler for getting a single student
add_action('wp_ajax_sms_get_student', 'sms_get_student_ajax_handler');
add_action('wp_ajax_nopriv_sms_get_student', 'sms_get_student_ajax_handler');

function sms_get_student_ajax_handler() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'students';

    // Retrieve and sanitize form data
    $student_id = sanitize_text_field($_POST['student_id']);

    // Get student data from the database
    $student = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE student_id = %s", $student_id));

    // Provide feedback to the user
    if ($student) {
        wp_send_json_success($student);
    } else {
        wp_send_json_error('Failed to retrieve student data. Please try again.');
    }

    // Always exit to avoid further execution
    wp_die();
}

// AJAX handler for student deletion
add_action('wp_ajax_sms_delete_student', 'sms_delete_student_ajax_handler');
add_action('wp_ajax_nopriv_sms_delete_student', 'sms_delete_student_ajax_handler');

function sms_delete_student_ajax_handler() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'students';

    // Retrieve and sanitize form data
    $student_id = sanitize_text_field($_POST['student_id']);

    // Delete student data from the database
    $result = $wpdb->delete($table_name, array('student_id' => $student_id));

    // Provide feedback to the user
    if ($result) {
        // Get updated list of students and send it back as JSON response
        $students = $wpdb->get_results("SELECT * FROM $table_name");
        wp_send_json_success($students);
    } else {
        wp_send_json_error('Failed to delete student. Please try again.');
    }

    // Always exit to avoid further execution
    wp_die();
}
