<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<?php
    if(isset( $_POST['sms_submit'] ) && $_POST['sms_submit'] === "Save Enrollment"){
        enroll_students_to_class();
        echo '<script>window.location.href="?page=sms-student-enrolled";</script>';
        // error_log("Calling Teacher Registration !!!");
    }else if(isset( $_POST['sms_submit'] ) && $_POST['sms_submit'] === "Update Enrollment"){
        // sms_update_teacher();
    }

    // if ( isset( $_GET['delete'] ) ) {
    //     sms_delete_teacher($_GET['delete']);
    //     // echo '<script>window.location.href="?page=sms-teachers-registration"";</script>';
    // }


?>

<div class="wwmt-containor-row">
    <div class="wwmt-column">
        <div class="wrap">
                <h1>Enroll Students in Classes</h1>
                <form id="enroll-students-form">
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

                    <!-- Enroll Students Section -->
                    <h2>Enroll Students</h2>
                    <select id="search-students" style="width: 100%;" placeholder="Search students by name">
                        <!-- Students will be loaded here -->
                    </select>
                    <table id="enrolled-students-table" class="widefat">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Enrolled students will be added here -->
                        </tbody>
                    </table>
                    <input type="hidden" id="selected-class-id" name="class_id" value="" />
                    <input type="submit" value="Save Enrollment" name="sms_submit" class="button button-primary" />
                </form>
            </div>
        </div>
    </div>
    <div class="wwmt-right">
        <div class="wrap">
            <h2>Selected Class Students</h2>
            <table id="load-selected-class-data-table" class="widefat">

            </table>
        </div>
    </div>
</div>
