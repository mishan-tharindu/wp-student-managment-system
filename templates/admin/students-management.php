<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

    global $wpdb;
    $table_name = $wpdb->prefix . 'students';

    if ( isset( $_POST['sms_submit'] ) && $_POST['sms_submit'] === "Update Student" ) {
        sms_update_student();
        echo '<script>window.location.href="?page=sms-student-management";</script>';
    } else if ( isset( $_POST['sms_submit'] ) && $_POST['sms_submit'] === "Register Student" ) {
        sms_register_student();
        echo '<script>window.location.href="?page=sms-student-management";</script>';
    }

    if ( isset( $_GET['delete'] ) ) {
        sms_delete_student($_GET['delete']);
        echo '<script>window.location.href="?page=sms-student-management";</script>';
    }

    $student = null;
    if ( isset( $_GET['edit'] ) ) {
        $student_id = intval( $_GET['edit'] );
        $student = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE student_id = %d", $student_id ) );
    }

    // echo esc_url(admin_url('admin-post.php'));
    ?>
    <h1>Student Management System</h1>
    <form method="post" action="">
        <table>
            <tr>
                <th>ID:</th>
                <td><input type="text" name="student_id" value="<?php echo isset($_GET['edit']) ? esc_attr($_GET['edit']) : generate_unique_student_id(); ?>" readonly required/></td>
            </tr>
            <tr>
                <th>First Name:</th>
                <td><input type="text" name="first_name" value="<?php echo isset($_POST['first_name']) ? esc_attr($_POST['first_name']) : ''; ?>" required /></td>
            </tr>
            <tr>
                <th>Last Name:</th>
                <td><input type="text" name="last_name" value="<?php echo isset($_POST['last_name']) ? esc_attr($_POST['last_name']) : ''; ?>" required /></td>
            </tr>
            <tr>
                <th>Email:</th>
                <td><input type="email" name="email" value="<?php echo isset($_POST['email']) ? esc_attr($_POST['email']) : ''; ?>" required /></td>
            </tr>
            <tr>
                <td colspan="2">
                <?php wp_nonce_field('student_form_action', 'student_form_nonce'); ?>
                <input type="hidden" name="action" value="sms_register_student">
                    <input type="submit" name="sms_submit" value="<?php echo isset( $_GET['edit'] ) ? 'Update Student' : 'Register Student'; ?>" />
                </td>
            </tr>
        </table>
    </form>


    <?php
    // echo plugin_dir_url( __FILE__ );
    // echo plugin_dir_url( __DIR__ );
    sms_student_table();

    function sms_search_students_ajax_handler() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'students';
    
        // Retrieve search query from AJAX request
        $search_query = sanitize_text_field( $_POST['search_query'] );
    
        // SQL query to retrieve student data with search functionality
        $sql = "SELECT * FROM $table_name WHERE first_name LIKE %s OR last_name LIKE %s";
        $prepared_sql = $wpdb->prepare( $sql, "%{$search_query}%", "%{$search_query}%" );
        $students = $wpdb->get_results( $prepared_sql );
    
        // Output search results as HTML table
        if ( $students ) {
            $output = '<table>';
            $output .= '<thead><tr><th>ID</th><th>First Name</th><th>Last Name</th><th>Email</th></tr></thead>';
            $output .= '<tbody>';
    
            foreach ( $students as $student ) {
                $output .= '<tr>';
                $output .= '<td>' . esc_html( $student->student_id ) . '</td>';
                $output .= '<td>' . esc_html( $student->first_name ) . '</td>';
                $output .= '<td>' . esc_html( $student->last_name ) . '</td>';
                $output .= '<td>' . esc_html( $student->email ) . '</td>';
                $output .= '</tr>';
            }
    
            $output .= '</tbody>';
            $output .= '</table>';
    
            echo $output;
        } else {
            echo '<p>No students found.</p>';
        }
    
        // Always exit to avoid further execution
        wp_die();
    }

    

    function sms_student_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'students';
        $students = $wpdb->get_results("SELECT * FROM $table_name");
        
        echo '<h2>Students List</h2>';
        echo '<table id="students-table" border="1" cellpadding="10">';
        echo '<tr><th>ID</th><th>First Name</th><th>Last Name</th><th>Email</th><th>Actions</th></tr>';
        foreach ( $students as $student ) {
            echo '<tr data-student-id="' . esc_attr( $student->student_id ) . '" data-first-name="' . esc_attr( $student->first_name ) . '" data-last-name="' . esc_attr( $student->last_name ) . '" data-email="' . esc_attr( $student->email ) . '">';
            echo '<td>' . esc_html( $student->student_id ) . '</td>';
            echo '<td>' . esc_html( $student->first_name ) . '</td>';
            echo '<td>' . esc_html( $student->last_name ) . '</td>';
            echo '<td>' . esc_html( $student->email ) . '</td>';
            echo '<td><a href="?page=sms-student-management&edit=' . esc_attr ( $student->student_id ) . '" class="edit-student">Edit</a> | <a href="?page=sms-student-management&delete=' . esc_attr ( $student->student_id ) . '">Delete</a></td>';
            echo '</tr>';
        }
        echo '</table>';
    }
    
    
    add_action('admin_footer', 'sms_add_admin_scripts');
    
    function sms_add_admin_scripts() {
        ?>
        <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function() {
                const rows = document.querySelectorAll('#students-table tr[data-student-id]');
                const form = document.querySelector('form');
                const studentIdField = form.querySelector('input[name="student_id"]');
                const firstNameField = form.querySelector('input[name="first_name"]');
                const lastNameField = form.querySelector('input[name="last_name"]');
                const emailField = form.querySelector('input[name="email"]');
                const submitButton = form.querySelector('input[name="sms_submit"]');
    
                rows.forEach(row => {
                    row.querySelector('.edit-student').addEventListener('click', function(event) {
                        event.preventDefault();
    
                        const studentId = row.getAttribute('data-student-id');
                        const firstName = row.getAttribute('data-first-name');
                        const lastName = row.getAttribute('data-last-name');
                        const email = row.getAttribute('data-email');
    
                        studentIdField.value = studentId;
                        firstNameField.value = firstName;
                        lastNameField.value = lastName;
                        emailField.value = email;
    
                        form.action = "?page=sms-student-management&edit=" + studentId;
                        submitButton.value = "Update Student";
                    });
                });
            });
        </script>
        <?php
    }



    
    ?>