<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

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


// Handle Ajax search request
add_action( 'wp_ajax_sms_search_students', 'sms_search_students' );
add_action( 'wp_ajax_nopriv_sms_search_students', 'sms_search_students' );

function sms_search_students() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'students';

    $search_query = '';
    if ( isset( $_GET['student_search'] ) && ! empty( $_GET['student_search'] ) ) {
        $search_query = sanitize_text_field( $_GET['student_search'] );
        $students = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE first_name LIKE %s", '%' . $wpdb->esc_like( $search_query ) . '%' ) );
    } else {
        $students = $wpdb->get_results( "SELECT * FROM $table_name" );
    }

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

    wp_die();
}





