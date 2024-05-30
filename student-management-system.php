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

register_deactivation_hook( __FILE__, 'sms_uninstall' );
// Register deactivation hook
register_deactivation_hook( __FILE__, 'sms_deactivate' );

function sms_install() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'students';
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name tinytext NOT NULL,
        email varchar(100) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";
    
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}

function sms_uninstall() {
   // Call the function to drop the table
   sms_drop_students_table();
}



function sms_deactivate() {
    // Call the function to drop the table
    sms_drop_students_table();
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
    add_menu_page( 'Student Management', 'Student Management', 'manage_options', 'student-management', 'sms_admin_page', 'dashicons-welcome-learn-more', 6 );
}

function sms_admin_page() {
    if ( isset( $_POST['sms_submit'] ) ) {
        if ( isset( $_GET['edit'] ) ) {
            sms_update_student( $_GET['edit'] );
        } else {
            sms_register_student();
        }
    }

    if ( isset( $_GET['delete'] ) ) {
        sms_delete_student( $_GET['delete'] );
    }

    $student = null;
    if ( isset( $_GET['edit'] ) ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'students';
        $student_id = intval( $_GET['edit'] );
        $student = $wpdb->get_row( "SELECT * FROM $table_name WHERE id = $student_id" );
    }



    ?>
    <h1>Student Management System</h1>
    <form method="post" action="">
        <table>
            <tr>
                <th>Name:</th>
                <td><input type="text" name="name" value="<?php echo $student ? esc_attr( $student->name ) : ''; ?>" required /></td>
            </tr>
            <tr>
                <th>Email:</th>
                <td><input type="email" name="email" value="<?php echo $student ? esc_attr( $student->email ) : ''; ?>" required /></td>
            </tr>
        </table>
        <input type="submit" name="sms_submit" value="<?php echo $student ? 'Update Student' : 'Register Student'; ?>" />
    </form>
    <?php

    sms_student_table();
}


function sms_register_student() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'students';
    
    $name = sanitize_text_field( $_POST['name'] );
    $email = sanitize_email( $_POST['email'] );

    $wpdb->insert( 
        $table_name, 
        array( 
            'name' => $name, 
            'email' => $email 
        ) 
    );
}

function sms_update_student( $student_id ) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'students';
    
    $name = sanitize_text_field( $_POST['name'] );
    $email = sanitize_email( $_POST['email'] );

    $wpdb->update( 
        $table_name, 
        array( 
            'name' => $name,
            'email' => $email 
        ), 
        array( 'id' => $student_id ) 
    );
}


function sms_delete_student( $id ) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'students';

    $wpdb->delete( $table_name, array( 'id' => $id ) );
}


function sms_student_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'students';
    
    $students = $wpdb->get_results( "SELECT * FROM $table_name" );
    
    echo '<h2>Student List</h2>';
    echo '<table border="1" cellpadding="10">';
    echo '<tr><th>ID</th><th>Name</th><th>Email</th><th>Actions</th></tr>';
    
    foreach ( $students as $student ) {
        echo '<tr>';
        echo '<td>' . esc_html( $student->id ) . '</td>';
        echo '<td>' . esc_html( $student->name ) . '</td>';
        echo '<td>' . esc_html( $student->email ) . '</td>';
        echo '<td>';
        echo '<a href="?page=student-management&edit=' . esc_html( $student->id ) . '">Edit</a> | ';
        echo '<a href="?page=student-management&delete=' . esc_html( $student->id ) . '">Delete</a>';
        echo '</td>';
        echo '</tr>';
    }
    
    echo '</table>';
}

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
                    echo '<td>' . esc_html( $student->id ) . '</td>';
                    echo '<td>' . esc_html( $student->name ) . '</td>';
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



//Students Details view short code
add_shortcode( 'student_list', 'sms_student_list_shortcode' );

// Enqueue JavaScript for Ajax search
add_action( 'wp_enqueue_scripts', 'sms_enqueue_scripts' );

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

// Handle Ajax search request
add_action( 'wp_ajax_sms_search_students', 'sms_search_students' );
add_action( 'wp_ajax_nopriv_sms_search_students', 'sms_search_students' );

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



// Shortcode to display student search by ID and toggle Active status
add_shortcode( 'student_search_by_id', 'sms_student_search_by_id_shortcode' );

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


// Handle Ajax search request by ID
add_action( 'wp_ajax_sms_search_student_by_id', 'sms_search_student_by_id' );
add_action( 'wp_ajax_nopriv_sms_search_student_by_id', 'sms_search_student_by_id' );

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

// Handle Ajax request to toggle Active status
add_action( 'wp_ajax_sms_toggle_active_status', 'sms_toggle_active_status' );
add_action( 'wp_ajax_nopriv_sms_toggle_active_status', 'sms_toggle_active_status' );

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



