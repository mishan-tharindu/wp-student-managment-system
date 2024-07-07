


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

function getFormattedDate() {
    var today = new Date();
    var year = today.getFullYear();
    var month = (today.getMonth() + 1).toString().padStart(2, '0'); // Months are zero-based, so add 1
    var day = today.getDate().toString().padStart(2, '0');

    return `${year}-${month}-${day}`;
}



