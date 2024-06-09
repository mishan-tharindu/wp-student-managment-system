
<?php
// Function to display the student registration form
function sms_student_registration_form() {
    ob_start();
    ?>
    <form id="student-registration-form" method="post" action="">
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
            <input type="submit" id="submit_student" value="Register Student">
        </div>
    </form>
    <div id="registration-message"></div>
    <br>
        <table border="1" cellpadding="10">
            <thead>
                <tr><th>ID</th><th>Name</th><th>Email</th></tr>
            </thead>
            <tbody id="sms_student_table">
                <?php
                global $wpdb;
                $table_name = $wpdb->prefix . 'students';
                $students = $wpdb->get_results( "SELECT * FROM $table_name" );
                
                if ( $students ) {
                    foreach ( $students as $student ) {
                        echo '<tr>';
                        echo '<td>' . esc_html( $student->student_id ) . '</td>';
                        echo '<td>' . esc_html( $student->first_name ) . '</td>';
                        echo '<td>' . esc_html( $student->email ) . '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="3">No students found.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    <?php

    return ob_get_clean();
}

// AJAX handler for student registration
add_action('wp_ajax_sms_register_student', 'sms_register_student_ajax_handler');
add_action('wp_ajax_nopriv_sms_register_student', 'sms_register_student_ajax_handler');

function sms_register_student_ajax_handler() {
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'students';

    // Retrieve and sanitize form data
    $student_id = sanitize_text_field($_POST['student_id']);
    $first_name = sanitize_text_field($_POST['first_name']);
    $last_name = sanitize_text_field($_POST['last_name']);
    $email = sanitize_email($_POST['email']);

    // Insert student data into the database
    $result = $wpdb->insert(
        $table_name,
        array(
            'student_id' => $student_id,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email
        )
    );

    // echo $result;
    // Provide feedback to the user
    if ($result) {
        // echo '<p>Student registered successfully!'.$result.' !!!!!!!!!!!!!</p>';
        // echo '<script>alert("Student registered successfully!");</script>';
        $students = $wpdb->get_results("SELECT * FROM $table_name");
        wp_send_json_success($students);
        
    } else {
        // echo '<p>Failed to register student. Please try again.'.$result.' !!!!!!!!!!</p>';
        // echo '<script>alert("Failed to register student. Please try again.");</script>';
        wp_send_json_error('Failed to register student. Please try again.');
    }

    // Always exit to avoid further execution
    wp_die();
}

// Function to generate a new unique student ID
function generate_new_student_id() {
    wp_send_json_success(generate_unique_student_id());
    wp_die();
}
add_action('wp_ajax_generate_new_student_id', 'generate_new_student_id');
add_action('wp_ajax_nopriv_generate_new_student_id', 'generate_new_student_id');
