document.addEventListener('DOMContentLoaded', function() {
    const rows = document.querySelectorAll('#registered-classes-table tr[data-class-id]');
    const form = document.querySelector('form');
    // const classIDField = form.querySelector('input[name="teacher_id"]');
    const subjectnameField = form.querySelector('select[name="subject_id"]');
    const gradeField = form.querySelector('input[name="grade"]');
    const timeField = form.querySelector('input[name="time"]');
    const dateField = form.querySelector('input[name="date"]');
    const classfeeField = form.querySelector('input[name="fee"]');
    const teachernameField = form.querySelector('select[name="teacher_id"]');
   

    rows.forEach(row => {
        row.querySelector('.edit-class').addEventListener('click', function(event) {
            event.preventDefault();

            const classID = row.getAttribute('data-class-id');
            const subjectName = row.getAttribute('data-subject-name');
            const classGrade = row.getAttribute('data-class-grade');
            const classTime = row.getAttribute('data-class-time');
            const classDate = row.getAttribute('data-class-date');
            const classTeacher = row.getAttribute('data-class-teacher');
            const classFee = row.getAttribute('data-class-fee');
            const submitButton = form.querySelector('input[name="sms_submit"]');
            
            subjectnameField.value = subjectName;
            gradeField.value = classGrade;
            timeField.value = classTime;
            dateField.value = classDate;
            classfeeField.value = classFee;
            teachernameField.value = classTeacher;

             // Set the hire date
            // const hireDate = row.getAttribute('data-hire-date');
            // const hireDateField = form.querySelector('input[name="hire_date"]');
            // if (hireDateField) {
            //     hireDateField.value = hireDate;
            // }
           
            form.action = "?page=sms-class-registration&edit=" + classID;
            submitButton.value = "Update Class";
        });
    });
});


/*
*
    Student Enrolled Class
*
*/

jQuery(document).ready(function($) {



    $('#search-students').select2({
        ajax: {
            url: sms_ajax_classes_obj.ajax_url,
            method: 'GET',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    action: 'sms_search_class_students',
                    search: params.term
                };
            },
            processResults: function(data) {
                return {
                    results: data
                };
            },
            cache: true
        },
        placeholder: 'Search for a student',
        minimumInputLength: 1
    });


    // Load All Class

    $.ajax({
        url: sms_ajax_classes_obj.ajax_url,
        method: 'GET',
        dataType: 'html',
        data: {
            action: 'load_all_classes'
        },
        success: function(response) {
            $('#load-classes-table tbody').html(response);
        },
        error: function(xhr, status, error) {
            console.error('Error:', status, error);
        }
    });

    $('#search-classes').on('keyup', function() {
        var searchQuery = $(this).val();

        $.ajax({
            url: sms_ajax_classes_obj.ajax_url,
            method: 'GET',
            dataType: 'html',
            data: {
                action: 'sms_search_classes',
                student_search: searchQuery
            },
            success: function(response) {
                $('#load-classes-table tbody').html(response);
            },
            error: function(xhr, status, error) {
                console.error('Error:', status, error);
            }
        });
    });


    // Select Row 

    $('#load-classes-table').on('click', 'tbody tr', function() {
        $('#load-classes-table tbody tr').removeClass('selected-row');
        $(this).addClass('selected-row');
        var classID = $(this).find('td').eq(0).text();
        var form = document.querySelector('form');
        var classIDField = form.querySelector('input[name="class_id"]');
        classIDField.value = classID;
    });

    $('#search-students').on('select2:select', function(e) {
        var data = e.params.data;
        var studentID = data.id;
        var studentName = data.text;

        $('#enrolled-students-table tbody').append(
            '<tr>' +
            '<td>' + studentID + '</td>' +
            '<td>' + studentName + '</td>' +
            '<td><button class="delete-student" type="button">Delete</button></td>' +
            '</tr>'
        );
    });

    // Delete Student

    $('#enrolled-students-table').on('click', '.delete-student', function() {
        $(this).closest('tr').remove();
    });


    // Handel Form

    $('#enroll-students-form').on('submit', function(e) {
        e.preventDefault();
        var classId = $('#load-classes-table tbody tr.selected-row').find('td').eq(0).text();
        var enrolledStudents = [];
        $('#enrolled-students-table tbody tr').each(function() {
            var studentId = $(this).find('td:first-child').text();
            enrolledStudents.push(studentId);
        });

        $.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
                action: 'save_enrollment_data_callback',
                class_id: classId,
                student_ids: enrolledStudents
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                    $('#enrolled-students-table tbody').empty();
                    window.location.href = '?page=sms-student-enrolled'; // Redirect to the desired page
                } else {
                    alert('Error: ' + response.data.message);
                }
            },
            error: function(error) {
                alert('Error saving enrollment data.');
                console.error(error);
            }
        });
    });
});


function getCurrentTime() {
    // Create a new Date object
    const now = new Date();

    // Get hours, minutes, and seconds
    let hours = now.getHours();
    let minutes = now.getMinutes();
    let seconds = now.getSeconds();

    // Add leading zeros to hours, minutes, and seconds if needed
    hours = hours < 10 ? '0' + hours : hours;
    minutes = minutes < 10 ? '0' + minutes : minutes;
    seconds = seconds < 10 ? '0' + seconds : seconds;

    // Format the time as "HH:MM:SS"
    const currentTime = `${hours}:${minutes}:${seconds}`;

    return currentTime;
}


