<?php
/**
 * Plugin Name: Student Management System
 * Description: Imagine it as a digital filing system that records personal information, academic progress like test scores and grades, and even behavioral evaluations. However, it does more than just store data. The SMS also tracks attendance, assists with curriculum planning, and manages schedules.
 * Version: 1.2
 * Author: Mishan Tharindu
 */

// Prevent direct access to the file
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Activation and deactivation hooks
register_activation_hook( __FILE__, 'sms_install' );

// Register uninstall hook
register_uninstall_hook( __FILE__, 'sms_uninstall' );

// Register deactivation hook
register_deactivation_hook( __FILE__, 'sms_deactivate' );

function sms_install() {
    sms_create_sms_tables();
}

function sms_uninstall() {
   // Call the function to drop the table
   sms_drop_students_table();
}

function sms_deactivate() {
    // Call the function to drop the table
    // sms_drop_students_table();
}

// Function to create the SMS Database tabels
function sms_create_sms_tables(){
    global $wpdb;
    $table_name = $wpdb->prefix . 'students';
    
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        student_id VARCHAR(100) PRIMARY KEY,
        first_name VARCHAR(50) NOT NULL,
        last_name VARCHAR(50) NOT NULL,
        date_of_birth DATE,
        gender VARCHAR(50) NOT NULL,
        contact_info VARCHAR(100),
        address TEXT,
        email VARCHAR(100),
        student_status INT
    );
    ";
    
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}

// Function to drop the students table
function sms_drop_students_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'students';

    $sql = "DROP TABLE IF EXISTS $table_name;";
    $wpdb->query($sql);
}


// Add menu item
add_action( 'admin_menu', 'sms_menu' );

function sms_menu() {
    add_menu_page( 'Student Management', 'Student Management', 'manage_options', 'sms-student-management', 'sms_admin_page', 'dashicons-welcome-learn-more', 6 );
    
    // Submenu items
    add_submenu_page(
        'sms-student-management', // Parent slug
        'Teachers Management', // Page title
        'Teachers Management', // Menu title
        'manage_options', // Capability
        'sms-teachers-registration', // Menu slug
        'sms_teachers_registration_page' // Function to display the page
    );

    // Submenu items
    add_submenu_page(
        'sms-student-management', // Parent slug
        'Subject Management', // Page title
        'Subject Management', // Menu title
        'manage_options', // Capability
        'sms-subject-registration', // Menu slug
        'sms_subject_registration_page' // Function to display the page
    );

    // Submenu items
    add_submenu_page(
        'sms-student-management', // Parent slug
        'Class Management', // Page title
        'Class Management', // Menu title
        'manage_options', // Capability
        'sms-class-registration', // Menu slug
        'sms_class_registration_page' // Function to display the page
    );

    // Submenu items
    add_submenu_page(
        'sms-student-management', // Parent slug
        'Exam Management', // Page title
        'Exam Management', // Menu title
        'manage_options', // Capability
        'sms-class-registration', // Menu slug
        'sms_class_registration_page' // Function to display the page
    );

}

function sms_teachers_registration_page(){
    ?>
    <div class="wrap">
        <h1>Teachers Management</h1>
        <p>Here you can register teachers.</p>
        <!-- Add your teachers registration code here -->
    </div>
    <?php
}

function sms_subject_registration_page(){
    ?>
    <div class="wrap">
        <h1>Subject Management</h1>
        <p>Here you can register teachers.</p>
        <!-- Add your teachers registration code here -->
    </div>
    <?php
}

function sms_class_registration_page(){
    ?>
    <div class="wrap">
        <h1>Class Management</h1>
        <p>Here you can register teachers.</p>
        <!-- Add your teachers registration code here -->
    </div>
    <?php
}

function sms_exam_registration_page(){
    ?>
    <div class="wrap">
        <h1>Exam Management</h1>
        <p>Here you can register teachers.</p>
        <!-- Add your teachers registration code here -->
    </div>
    <?php
}

function sms_admin_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'students';

    // Handle form submission
    if ( isset( $_POST['sms_submit'] ) && $_POST['sms_submit'] === "Update Student" ) {
        sms_update_student();
        echo '<script>console.log("update")</script>';
        // echo '<script>window.location.href="?page=sms-student-management";</script>';
    } else if ( isset( $_POST['sms_submit'] ) && $_POST['sms_submit'] === "Register Student" ) {
        sms_register_student();
        
        echo '<script>window.location.href="?page=sms-student-management";</script>';
    }

    // Handle delete
    if ( isset( $_GET['delete'] ) ) {
        // echo '<p>User Data Delete :::'.$_GET['delete'].'</p>';
        sms_delete_student($_GET['delete']); // Provide student ID as argument
        echo '<script>window.location.href="?page=sms-student-management";</script>';
    }

    $student = null;
    if ( isset( $_GET['edit'] ) ) {
        $student_id = intval( $_GET['edit'] );
        $student = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE student_id = %d", $student_id ) );
    }

    // Display the form
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
        </table>
        <input type="submit" name="sms_submit" value="<?php echo isset($_GET['edit']) ? 'Update Student' : 'Register Student'; ?>" />
    </form>
    <?php

    sms_student_table();
}

// Student Registration Database Function
function sms_register_student() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'students';
    
    $studentid = sanitize_text_field( $_POST['student_id'] );
    $first_name = sanitize_text_field( $_POST['first_name'] );
    $last_name = sanitize_text_field( $_POST['last_name'] );
    $email = sanitize_email( $_POST['email'] );
    

    $wpdb->insert( 
        $table_name, 
        array( 
            'student_id' => $studentid, 
            'first_name' => $first_name, 
            'last_name' => $last_name, 
            'email' => $email 
        ) 
    );
}

// Student Update Database Function
// function sms_update_student( $student_id ) {
//     global $wpdb;
//     $table_name = $wpdb->prefix . 'students';
    
//     $first_name = sanitize_text_field( $_POST['first_name'] );
//     $last_name = sanitize_text_field( $_POST['last_name'] );
//     $email = sanitize_email( $_POST['email'] );

//     $wpdb->update( 
//         $table_name, 
//         array( 
//             'first_name' => $first_name,
//             'last_name' => $last_name,
//             'email' => $email 
//         ), 
//         array( 'student_id' => $student_id ) 
//     );
// }

function sms_update_student() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'students';
    
    $student_id = sanitize_text_field( $_POST['student_id'] );
    $first_name = sanitize_text_field( $_POST['first_name'] );
    $last_name = sanitize_text_field( $_POST['last_name'] );
    $email = sanitize_email( $_POST['email'] );

    $wpdb->update( 
        $table_name, 
        array( 
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email 
        ), 
        array( 'student_id' => $student_id ) 
    );
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

// Student Delete Database Function
function sms_delete_student( $student_id ) { // Accept student ID as argument
    global $wpdb;
    $table_name = $wpdb->prefix . 'students';

    $result = $wpdb->delete( $table_name, array( 'student_id' => $student_id ));
    // echo "<p>Student data ".$student_id." </p>";

    if ( $result === false ) {
        echo "<p>Error occurred while deleting student data.</p>";
    } else {
        echo "<p>Student data deleted successfully.</p>";
    }
}



//Students Details view short code
add_shortcode( 'student_list', 'sms_student_list_shortcode' );
// Enqueue JavaScript for Ajax search
add_action( 'wp_enqueue_scripts', 'sms_enqueue_scripts' );
// Handle Ajax search request
add_action( 'wp_ajax_sms_search_students', 'sms_search_students' );
add_action( 'wp_ajax_nopriv_sms_search_students', 'sms_search_students' );
// Shortcode to display student search by ID and toggle Active status
add_shortcode( 'student_search_by_id', 'sms_student_search_by_id_shortcode' );
// Handle Ajax search request by ID
add_action( 'wp_ajax_sms_search_student_by_id', 'sms_search_student_by_id' );
add_action( 'wp_ajax_nopriv_sms_search_student_by_id', 'sms_search_student_by_id' );
// Handle Ajax request to toggle Active status
add_action( 'wp_ajax_sms_toggle_active_status', 'sms_toggle_active_status' );
add_action( 'wp_ajax_nopriv_sms_toggle_active_status', 'sms_toggle_active_status' );


//View Students Details
function sms_student_list_shortcode() {
    ob_start();
    ?>
    <h2>Student List</h2>
    <form method="GET" action="">
        <input type="text" id="sms_student_search" name="student_search" placeholder="Search students by name" />
    </form>
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

function sms_enqueue_scripts() {
    wp_enqueue_script( 'sms-ajax-search', plugin_dir_url( __FILE__ ) . 'sms-ajax-search.js', array('jquery'), null, true );
    wp_localize_script( 'sms-ajax-search', 'sms_ajax_obj', array( 
        'ajax_url' => admin_url( 'admin-ajax.php' ) 
    ));


      // Enqueue the new JavaScript file
      wp_enqueue_script( 'sms-ajax-search-by-id', plugin_dir_url( __FILE__ ) . 'sms-ajax-search-by-id.js', array('jquery'), null, true );
      wp_localize_script( 'sms-ajax-search-by-id', 'sms_ajax_obj', array( 
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'sms_nonce' ) // Generate a nonce and pass it to JavaScript
    ));


}

function sms_search_students() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'students';

    $search_query = '';
    if ( isset( $_GET['student_search'] ) && ! empty( $_GET['student_search'] ) ) {
        $search_query = sanitize_text_field( $_GET['student_search'] );
        $students = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE name LIKE %s", '%' . $wpdb->esc_like( $search_query ) . '%' ) );
    } else {
        $students = $wpdb->get_results( "SELECT * FROM $table_name" );
    }

    if ( $students ) {
        foreach ( $students as $student ) {
            echo '<tr>';
            echo '<td>' . esc_html( $student->id ) . '</td>';
            echo '<td>' . esc_html( $student->name ) . '</td>';
            echo '<td>' . esc_html( $student->email ) . '</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="3">No students found.</td></tr>';
    }

    wp_die();
}

function sms_student_search_by_id_shortcode() {
    ob_start();
    ?>
    <h2>Student Search by ID</h2>
    <form method="GET" action="" id="sms_student_search_by_id_form">
        <input type="text" id="sms_student_id_search" name="student_id_search" placeholder="Enter student ID" />
    </form>
    <table border="1" cellpadding="10">
        <thead>
            <tr><th>ID</th><th>Name</th><th>Email</th><th>Active</th></tr>
        </thead>
        <tbody id="sms_student_by_id_table">
            <!-- Search results will be displayed here -->
            <?php
            global $wpdb;
            $table_name = $wpdb->prefix . 'students';
            $students = $wpdb->get_results( "SELECT * FROM $table_name" );
            
            if ( $students ) {
                foreach ( $students as $student ) {
                    echo '<tr>';
                    echo '<td>' . esc_html( $student->id ) . '</td>';
                    echo '<td>' . esc_html( $student->name ) . '</td>';
                    echo '<td>' . esc_html( $student->email ) . '</td>';
                    echo '<td>' . ( $student->active ? 'Active' : 'Inactive' ) . '</td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="4">No students found.</td></tr>';
            }
            ?>
        </tbody>
    </table>
    <?php
    return ob_get_clean();
}

function sms_search_student_by_id() {

    // Check nonce
    if ( !isset( $_GET['_wpnonce'] ) || !wp_verify_nonce( $_GET['_wpnonce'], 'sms_nonce' ) ) {
        wp_die( 'Nonce verification failed', 'Error', array( 'response' => 403 ) );
    }


    global $wpdb;
    $table_name = $wpdb->prefix . 'students';

    $search_query = '';
    if ( isset( $_GET['student_id_search'] ) && ! empty( $_GET['student_id_search'] ) ) {
        $search_query = intval( $_GET['student_id_search'] );
        $student = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $search_query ) );
    }

    if ( $student ) {
        echo '<tr>';
        echo '<td>' . esc_html( $student->id ) . '</td>';
        echo '<td>' . esc_html( $student->name ) . '</td>';
        echo '<td>' . esc_html( $student->email ) . '</td>';
        echo '<td><button class="toggle-active-status" data-id="' . esc_html( $student->id ) . '">' . ( $student->active ? 'Deactivate' : 'Activate' ) . '</button></td>';
        echo '</tr>';
    } else {
        echo '<tr><td colspan="4">No student found.</td></tr>';
    }

    wp_die();
}

function sms_toggle_active_status() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'students';

    if ( isset( $_POST['student_id'] ) ) {

        
        // $student_id = intval( $_POST['student_id'] );
        $student_id = intval( sanitize_text_field($_POST['student_id']));
        $student = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $student_id ) );

        if ( $student ) {
            $new_status = $student->active ? 0 : 1;
            $wpdb->update(
                $table_name,
                array( 'active' => $new_status ),
                array( 'id' => $student_id )
            );

            // Return updated row
            $student = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $student_id ) );
            echo '<tr>';
            echo '<td>' . esc_html( $student->id ) . '</td>';
            echo '<td>' . esc_html( $student->name ) . '</td>';
            echo '<td>' . esc_html( $student->email ) . '</td>';
            echo '<td><button class="toggle-active-status" data-id="' . esc_html( $student->id ) . '">' . ( $student->active ? 'Deactivate' : 'Activate' ) . '</button></td>';
            echo '</tr>';
        }
    }

    wp_die();
}

function generate_unique_student_id() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'students';
    $unique = false;
    
    while (!$unique) {
        // Generate student ID with "STU" prefix, current date, and a random number
        $random_number = mt_rand(1000, 9999); // You can adjust the range for random number as needed
        $date_part = date('Ymd');
        $student_id = 'STU' . $date_part . $random_number;

        // Check if this ID already exists in the database
        $existing_id = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE id = %s", $student_id));

        if ($existing_id == 0) {
            $unique = true;
        }
    }

    return $student_id;
}







