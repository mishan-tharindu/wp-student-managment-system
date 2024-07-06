<div class="wrap">
        <h1>Schedule Exam</h1>
        <div id="exam-form">
            <div class="form-group">
                <label for="class-select">Select Class</label>
                <select id="class-select">
                    <option>Select Class</option>
                </select>
            </div>
            <div class="form-group">
                <label for="teacher-name">Teacher Name</label>
                <input type="text" id="teacher-name" readonly>
            </div>
            <div class="form-group">
                <label for="exam-date">Select Date</label>
                <input type="date" id="exam-date">
            </div>
            <div class="form-group">
                <label for="exam-time">Select Time</label>
                <input type="time" id="exam-time">
            </div>
            <div class="form-group">
                <label for="exam-active">Active</label>
                <input type="checkbox" id="exam-active" checked>
            </div>
            <div class="form-group">
                <button id="save-exam">Save</button>
            </div>
        </div>
        <table id="exam-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Exam ID</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Active</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Exam records will be loaded here -->
            </tbody>
        </table>
    </div>

    <!-- Update Exam Modal -->
    <div id="updateExamModal" style="display: none;">
        <form id="updateExamForm">
            <label for="updateClassSelect">Select Class:</label>
            <select id="updateClassSelect" name="class_id"></select><br>

            <label for="updateTeacherName">Teacher Name:</label>
            <input type="text" id="updateTeacherName" name="teacher_name" readonly><br>

            <label for="updateExamDate">Select Date:</label>
            <input type="date" id="updateExamDate" name="exam_date"><br>

            <label for="updateExamTime">Select Time:</label>
            <input type="time" id="updateExamTime" name="exam_time"><br>

            <label for="updateExamActive">Active:</label>
            <input type="checkbox" id="updateExamActive" name="exam_status"><br>

            <input type="hidden" id="updateExamId" name="exam_id">
            <button type="submit">Update</button>
        </form>
    </div>



    <?php
