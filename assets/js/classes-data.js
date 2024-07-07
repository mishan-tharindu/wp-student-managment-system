
//Without Check box

// jQuery(document).ready(function($) {

//     // Load all classes on page load
//     $.ajax({
//         url: sms_ajax_classes_data_obj.ajax_url,
//         method: 'GET',
//         dataType: 'html',
//         data: {
//             action: 'load_all_classes'
//         },
//         success: function(response) {
//             $('#load-classes-table tbody').html(response);
//         },
//         error: function(xhr, status, error) {
//             console.error('Error:', status, error);
//         }
//     });

//     // Search classes functionality
//     $('#search-classes').on('keyup', function() {
//         var searchQuery = $(this).val();

//         $.ajax({
//             url: sms_ajax_classes_data_obj.ajax_url,
//             method: 'GET',
//             dataType: 'html',
//             data: {
//                 action: 'sms_search_classes',
//                 student_search: searchQuery
//             },
//             success: function(response) {
//                 $('#load-classes-table tbody').html(response);
//             },
//             error: function(xhr, status, error) {
//                 console.error('Error:', status, error);
//             }
//         });
//     });


//     // Row selection and data retrieval for classes
//     $('#load-classes-table').on('click', 'tbody tr', function() {
//         $('#load-classes-table tbody tr').removeClass('selected-row');
//         $(this).addClass('selected-row');

//         var classID = $(this).find('td').eq(0).text();
//         var subject = $(this).find('td').eq(1).text();
//         var teacher = $(this).find('td').eq(2).text();
//         var date = $(this).find('td').eq(3).text();
//         var time = $(this).find('td').eq(4).text();
//         var grade = $(this).find('td').eq(5).text();
//         var fee = $(this).find('td').eq(6).text();

//         $('#selected-class-id').val(classID);


//         $.ajax({
//             url: sms_ajax_classes_data_obj.ajax_url,
//             method: 'GET',
//             dataType: 'json',
//             data: {
//                 action: 'load_class_students',
//                 class_id: classID
//             },
//             success: function(response) {
//                 var students = response.data;
//                 var studentRows = '';
//                 students.forEach(function(student) {
//                     studentRows += '<tr>' +
//                         '<td>' + student.id + '</td>' +
//                         '<td>' + student.name + '</td>' +
//                         '<td class="class-date">' + getFormattedDate() + '</td>' +
//                         '<td class="in-time">-</td>' +
//                         '<td class="out-time">-</td>' +
//                         '<td>No</td>' +
//                         '<td>No</td>' +
//                         '</tr>';
//                 });
//                 $('#class-students-table tbody').html(studentRows);
//             },
//             error: function(xhr, status, error) {
//                 console.error('Error:', status, error);
//             }
//         });
//     });

//     // Handle student ID search and highlight
//     $('#search-student-id').on('keypress', function(e) {

//         var time = getCurrentTime();

//         if(e.which == 13) {
//             e.preventDefault();
//             var studentID = $(this).val().toLowerCase();
//             $('#class-students-table tbody tr').each(function() {
//                 var id = $(this).find('td:first-child').text().toLowerCase();
//                 if(id === studentID) {
//                     $(this).addClass('highlight');
//                     $(this).find('td:last-child').text('Yes');
//                     $(this).find('.in-time').text(time);
//                 } else {
//                     $(this).removeClass('highlight');
//                 }
//             });
//         }
//     });

//     // Handle form submission
//     $('#attendance-form').on('submit', function(e) {
//         e.preventDefault();

//         var classId = $('#selected-class-id').val();
//         var attendanceData = [];

//         $('#class-students-table tbody tr').each(function() {
//             var studentId = $(this).find('td:first-child').text();
//             var attendanceStatus = $(this).find('td:last-child').text() === 'Yes' ? 1 : 0;
//             // var inTime = $(this).find('.in-time').text();
//             var classDate = $(this).find('.class-date').text();
//             var inTime = $(this).find('.in-time').text();
//             var outTime = $(this).find('.out-time').text();
//             console.log("Table Time ::"+inTime);
//             attendanceData.push({ student_id: studentId, class_date: classDate, in_Time: inTime, out_Time: outTime, attendance_status: attendanceStatus });
//         });

//         $.ajax({
//             url: sms_ajax_classes_data_obj.ajax_url,
//             method: 'POST',
//             data: {
//                 action: 'save_attendance_data',
//                 class_id: classId,
//                 attendance_data: attendanceData
//             },
//             success: function(response) {
//                 alert(response.data.message);
//                 console.log(response);
//             },
//             error: function(error) {
//                 alert('Error saving attendance data.');
//                 console.error(error);
//             }
//         });
//     });
// });

// var classesTable = $('#load-classes-table tbody');
// var classStudentsTable = $('#class-students-table tbody');


// After Add Check Box

jQuery(document).ready(function($) {
    // Initialize necessary elements
    var toggleButton = $('#toggle-attendance-view');
    var classesTable = $('#load-classes-table tbody');
    var classStudentsTable = $('#class-students-table tbody');
    var searchClasses = $('#search-classes');
    var searchStudent = $('#search-student');
    var smsAttendanceBtn = $('#sms-attendance-submit');

    // Load classes on page load
    loadAllClasses();

    // Toggle Button Change Event
    toggleButton.change(function() {
        var isChecked = $(this).is(':checked');
        var selectedClassId = classesTable.find('tr.selected-row').data('data-class-id');
        
        if(isChecked){
            console.log("Checked !!!");
            smsAttendanceBtn.value = "Update Check Outs";
        }else
        {
            console.log("Un Checked !!!");
        }


        
        if (selectedClassId) {
            loadClassStudents(selectedClassId, isChecked);
            smsAttendanceBtn.value = "Update Check Outs";
        }
    });

    // Search Classes Event
    searchClasses.on('keyup', function() {
        var searchQuery = $(this).val();
        searchClassesByName(searchQuery);
    });

    // Class Row Click Event
    classesTable.on('click', 'tr', function() {
        classesTable.find('tr').removeClass('selected-row');
        $(this).addClass('selected-row');
        
        var classId = $(this).data('class-id');
        var isChecked = toggleButton.is(':checked');
        loadClassStudents(classId, isChecked);
    });

    // Search Student Event
    searchStudent.on('keyup', function(e) {
        if (e.key === 'Enter') {
            var studentId = $(this).val();
            highlightStudentRow(studentId);
        }
    });


    // Handle student ID search and highlight
    $('#search-student-id').on('keypress', function(e) {

        var time = getCurrentTime();

        // console.log("Submit Button Value ::: "+$('#sms-attendance-submit').val());


        if(e.which == 13) {

            if($('#sms-attendance-submit').val() == "Update Check outs"){
                e.preventDefault();
                var studentID = $(this).val().toLowerCase();
                $('#class-students-table tbody tr').each(function() {
                    var id = $(this).find('td:first-child').text().toLowerCase();
                    if(id === studentID) {
                        $(this).addClass('highlight');
                        $(this).find('td:last-child').text('Yes');
                        $(this).find('.out-time').text(time);
                    } else {
                        $(this).removeClass('highlight');
                    }
            });
            }else
            {
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

            
        }
    });

    // Function to Load All Classes
    function loadAllClasses() {
        $.ajax({
            url: sms_ajax_classes_obj.ajax_url,
            method: 'GET',
            dataType: 'html',
            data: { action: 'load_all_classes' },
            success: function(response) {
                classesTable.html(response);
            },
            error: function(xhr, status, error) {
                console.error('Error loading classes:', status, error);
            }
        });
    }

    // Function to Search Classes by Name
    function searchClassesByName(searchQuery) {
        $.ajax({
            url: sms_ajax_classes_obj.ajax_url,
            method: 'GET',
            dataType: 'html',
            data: { action: 'sms_search_classes', student_search: searchQuery },
            success: function(response) {
                classesTable.html(response);
            },
            error: function(xhr, status, error) {
                console.error('Error searching classes:', status, error);
            }
        });
    }

    // Function to Load Class Students
    function loadClassStudents(classId, onlyPresent) {
        $.ajax({
            url: sms_ajax_classes_obj.ajax_url,
            method: 'GET',
            // dataType: 'html',
            dataType: 'json', // Ensure we're expecting JSON
            data: {
                action: 'load_class_students',
                class_id: classId,
                only_present: onlyPresent
            },
            success: function(response) {
                console.log( "Resopnse Data ::: "+response.success);
                
                if (response.success) {
                    var students = response.data;
                    var studentRows = '';
                    
                    students.forEach(function(student) {
                        var attendanceStatus = student.attendance_status == 1 ? 'Yes' : 'No';
                        // var attendanceID = student.attendance_id == "undefined" ? 'No' : student.attendance_id;
                        studentRows += '<tr student-class-attend-id="'+student.attendance_id+'">' +
                            '<td>' + student.student_id + '</td>' +
                            '<td>' + student.first_name + ' ' + student.last_name + '</td>' +
                            '<td class="class-date">' + getFormattedDate() + '</td>' +
                            '<td class="in-time">' + (student.in_time || '-') + '</td>' +
                            '<td class="out-time">' + (student.out_time || '-') + '</td>' +
                            '<td>No</td>' +
                            '<td>' + (attendanceStatus) + '</td>' +
                            '</tr>';
                    });
                    classStudentsTable.html(studentRows);
                } else {
                    // console.error('Error:', response.data.message);
                    classStudentsTable.html('<tr><td colspan="6">No students found.</td></tr>');
                }
                // classStudentsTable.html(response);
            },
            error: function(xhr, status, error) {
                console.error('Error loading class students:', status, error);
            }
        });
    }

    // Function to Highlight Student Row
    function highlightStudentRow(studentId) {
        var rows = classStudentsTable.find('tr');
        rows.removeClass('highlighted-row');
        
        rows.each(function() {
            var row = $(this);
            var id = row.find('td:first').text();
            if (id === studentId) {
                row.addClass('highlighted-row');
                // Update attendance status to 'Yes'
                row.find('td:eq(2)').text('Yes');
            }
        });
    }

    // Handle Toggle 
    $('#toggle-attendance-view').change(function() {
        var isChecked = $(this).is(':checked');
        var selectedClassId = classesTable.find('tr.selected-row').data('class-id');
        var submitButton = $('#sms-attendance-submit');
    
        if (isChecked) {
            console.log("Checked!");
            submitButton.val('Update Check outs');
    
            $.ajax({
                url: sms_ajax_classes_data_obj.ajax_url,
                method: 'GET',
                dataType: 'json', // Ensure we're expecting JSON
                data: {
                    action: 'load_class_students',
                    class_id: selectedClassId,
                    only_present: isChecked
                },
                success: function(response) {
                    if (response.success) {
                        var students = response.data;
                        var studentRows = '';
    
                        students.forEach(function(student) {
                            var attendanceStatus = student.attendance_status == 1 ? 'Yes' : 'No';
    
                            studentRows += '<tr student-class-attend-id="'+student.attendance_id+'">' +
                                '<td>' + student.student_id + '</td>' +
                                '<td>' + student.first_name + ' ' + student.last_name + '</td>' +
                                '<td class="class-date">' + getFormattedDate() + '</td>' +
                                '<td class="in-time">' + (student.in_time ? student.in_time : '-') + '</td>' +
                                '<td class="out-time">' + (student.out_time ? student.out_time : '-') + '</td>' +
                                '<td >No</td>' +
                                '<td>' + attendanceStatus + '</td>' +
                                '</tr>';
                        });
    
                        $('#class-students-table tbody').html(studentRows);
                    } else {
                        console.error('Error:', response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading class students:', status, error);
                }
            });
        } else {
            console.log("Unchecked!");
            submitButton.val('Save Attendance');
            // Handle the case when toggle is off, if necessary
        }
    });

    // Handle form submission
    $('#attendance-form').on('submit', function(e) {
                e.preventDefault();
        
                
                var classId = $('#selected-class-id').val();
                var attendanceData = [];
        
               
        
                if($('#sms-attendance-submit').val() == "Update Check outs"){

                    $('#class-students-table tbody tr').each(function() {
                        // var atttendId = $(this).data('student-class-attend-id'); // Adjust based on your HTML structure
                        var attendId = $(this).attr('student-class-attend-id'); // Use attr to get the attribute value
                        var studentId = $(this).find('td:first-child').text();
                        var attendanceStatus = $(this).find('td:last-child').text() === 'Yes' ? 1 : 0;
                        // var inTime = $(this).find('.in-time').text();
                        var classDate = $(this).find('.class-date').text();
                        var inTime = $(this).find('.in-time').text();
                        var outTime = $(this).find('.out-time').text();
                        console.log("Attend Id  ::"+attendId);
                        console.log("Table In Time ::"+inTime);
                        console.log("Table Out Time ::"+outTime);
                        attendanceData.push({ student_attend_id: attendId ,student_id: studentId, class_date: classDate, in_Time: inTime, out_Time: outTime, attendance_status: attendanceStatus });
                    });
        
                    console.log('update function Here');
                    $.ajax({
                        url: sms_ajax_classes_data_obj.ajax_url,
                        method: 'POST',
                        data: {
                            action: 'update_checkouts',
                            class_id: classId,
                            attendance_data: attendanceData
                        },
                        success: function(response) {
                            alert(response.data.message);
                            console.log(response);
                        },
                        error: function(error) {
                            alert('Error Updating attendance data.');
                            console.error(error);
                        }
                    });
        
                }else{
        
                    console.log('Save function Here');

                    $('#class-students-table tbody tr').each(function() {
                        // var atttendId = $(this).data('student-class-attend-id'); // Adjust based on your HTML structure
                        var attendId = $(this).attr('student-class-attend-id'); // Use attr to get the attribute value
                        var studentId = $(this).find('td:first-child').text();
                        var attendanceStatus = $(this).find('td:last-child').text() === 'Yes' ? 1 : 0;
                        // var inTime = $(this).find('.in-time').text();
                        var classDate = $(this).find('.class-date').text();
                        var inTime = $(this).find('.in-time').text();
                        var outTime = $(this).find('.out-time').text();
                        console.log("Attend Id  ::"+attendId);
                        console.log("Table In Time ::"+inTime);
                        console.log("Table Out Time ::"+outTime);
                        attendanceData.push({ student_attend_id: attendId ,student_id: studentId, class_date: classDate, in_Time: inTime, out_Time: outTime, attendance_status: attendanceStatus });
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
                    
                }
        
        
    });
        

    
    // Handle form submission
    // $('#sms-attendance-form').on('submit', function(e) {
    //     e.preventDefault();
    
    //     var submitButton = $('#sms-attendance-submit');
    //     var action = submitButton.val() === 'Update Check outs' ? 'update_checkouts' : 'save_attendance';
    //     var classId = classesTable.find('tr.selected-row').data('class-id');
    //     var attendanceData = [];

    //     if(submitButton.val() == "Save Attendance"){
    //         //Save Function is Here
    //         console.log('Save function Here');
    //     }else
    //     {
    //         //Update Funtion is here
    //         console.log('Update function Here');
    //     }
    
    //     // $('#class-students-table tbody tr').each(function() {
    //     //     var studentId = $(this).find('td:first').text();
    //     //     var inTime = $(this).find('.in-time').text();
    //     //     var outTime = $(this).find('.out-time').text();
    //     //     var attendanceStatus = $(this).find('td').eq(5).text() === 'Yes' ? 1 : 0;
    
    //     //     attendanceData.push({
    //     //         student_id: studentId,
    //     //         in_time: inTime,
    //     //         out_time: outTime,
    //     //         attendance_status: attendanceStatus
    //     //     });
    //     // });
    
    //     // $.ajax({
    //     //     url: sms_ajax_classes_data_obj.ajax_url,
    //     //     method: 'POST',
    //     //     dataType: 'json',
    //     //     data: {
    //     //         action: action,
    //     //         class_id: classId,
    //     //         attendance_data: attendanceData
    //     //     },
    //     //     success: function(response) {
    //     //         if (response.success) {
    //     //             alert('Attendance data saved successfully!');
    //     //             console.log(response);
    //     //             // Optionally, reset the form or perform other actions
    //     //         } else {
    //     //             alert('Error saving attendance data.');
    //     //             console.error(response.message);
    //     //         }
    //     //     },
    //     //     error: function(xhr, status, error) {
    //     //         console.error('Error saving attendance data:', status, error);
    //     //     }
    //     // });
    // });



});

// Class Table Edit Funtion

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
           
            form.action = "?page=sms-class-registration&edit=" + classID;
            submitButton.value = "Update Class";
        });
    });
});




