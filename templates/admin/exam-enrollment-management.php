<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

?>

<div class="wwmt-containor-row">
    <div class="wwmt-column">
        <div class="wrap">
                <h1>Exam Enrollment</h1>
                <form id="enroll-students-form">
                    <!-- Search Classes Section -->
                    <h2>Search Exams</h2>
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