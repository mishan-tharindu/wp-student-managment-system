<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<?php
    if(isset( $_POST['attendance_submit'] ) && $_POST['attendance_submit'] === "Save Attendance"){
        save_student_attendance();
    }
?>

<div class="wrap">
    <h1>Mark Student Attendance</h1>
    <form id="attendance-form" method="POST">
        <!-- Search Classes Section -->
        <h2>Search Classes</h2>
        <input type="text" id="search-classes" placeholder="Search classes by name" />
        <table id="load-classes-table" class="widefat">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Subject</th>
                    <th>Teacher</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Grade</th>
                    <th>Fee</th>
                </tr>
            </thead>
            <tbody>
                <!-- Classes will be loaded here -->
            </tbody>
        </table>

        <!-- Mark Attendance Section -->
        <h2>Mark Attendance - Check IN </h2>
        <input type="text" id="search-student-id" placeholder="Search student by ID" />
        <table id="class-students-table" class="widefat">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Date</th>
                    <th>Check In Time</th>
                    <th>Check Out Time</th>
                    <th>Paid</th>
                    <th>Attendance Status</th>
                </tr>
            </thead>
            <tbody>
                <!-- Students will be loaded here -->
            </tbody>
        </table>
        <input type="hidden" id="selected-class-id" name="class_id" value="" />
        <input type="submit" value="Save Attendance" name="attendance_submit" class="button button-primary" />
    </form>
</div>
