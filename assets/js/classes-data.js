jQuery(document).ready(function($) {

    // Load all classes on page load
    $.ajax({
        url: sms_ajax_classes_data_obj.ajax_url,
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

    // Search classes functionality
    $('#search-classes').on('keyup', function() {
        var searchQuery = $(this).val();

        $.ajax({
            url: sms_ajax_classes_data_obj.ajax_url,
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

    // Row selection and data retrieval for classes
    $('#load-classes-table').on('click', 'tbody tr', function() {
        $('#load-classes-table tbody tr').removeClass('selected-row');
        $(this).addClass('selected-row');

        var classID = $(this).find('td').eq(0).text();
        var subject = $(this).find('td').eq(1).text();
        var teacher = $(this).find('td').eq(2).text();
        var date = $(this).find('td').eq(3).text();
        var time = $(this).find('td').eq(4).text();
        var grade = $(this).find('td').eq(5).text();
        var fee = $(this).find('td').eq(6).text();

        $('#selected-class-id').val(classID);


        $.ajax({
            url: sms_ajax_classes_data_obj.ajax_url,
            method: 'GET',
            dataType: 'json',
            data: {
                action: 'load_class_students',
                class_id: classID
            },
            success: function(response) {
                var students = response.data;
                var studentRows = '';
                students.forEach(function(student) {
                    studentRows += '<tr>' +
                        '<td>' + student.id + '</td>' +
                        '<td>' + student.name + '</td>' +
                        '<td class="in-time">-</td>' +
                        '<td class="out-time">-</td>' +
                        '<td>No</td>' +
                        '<td>No</td>' +
                        '</tr>';
                });
                $('#class-students-table tbody').html(studentRows);
            },
            error: function(xhr, status, error) {
                console.error('Error:', status, error);
            }
        });
    });

    // Handle student ID search and highlight
    $('#search-student-id').on('keypress', function(e) {

        var time = getCurrentTime();

        if(e.which == 13) {
            e.preventDefault();
            var studentID = $(this).val().toLowerCase();
            $('#class-students-table tbody tr').each(function() {
                var id = $(this).find('td:first-child').text().toLowerCase();
                if(id === studentID) {
                    $(this).addClass('highlight');
                    $(this).find('td:last-child').text('Yes');
                    $(this).find('.in-time').text(time);
                } else {
                    $(this).removeClass('highlight');
                }
            });
        }
    });

    // Handle form submission
    $('#attendance-form').on('submit', function(e) {
        e.preventDefault();

        var classId = $('#selected-class-id').val();
        var attendanceData = [];

        $('#class-students-table tbody tr').each(function() {
            var studentId = $(this).find('td:first-child').text();
            var attendanceStatus = $(this).find('td:last-child').text() === 'Yes' ? 1 : 0;
            // var inTime = $(this).find('.in-time').text();
            var inTime = $(this).find('.in-time').text();
            console.log("Table Time ::"+inTime);
            attendanceData.push({ student_id: studentId, in_Time: inTime, attendance_status: attendanceStatus });
        });

        $.ajax({
            url: sms_ajax_classes_data_obj.ajax_url,
            method: 'POST',
            data: {
                action: 'save_attendance_data',
                class_id: classId,
                attendance_data: attendanceData
            },
            success: function(response) {
                alert(response.data.message);
                console.log(response);
            },
            error: function(error) {
                alert('Error saving attendance data.');
                console.error(error);
            }
        });
    });
});
