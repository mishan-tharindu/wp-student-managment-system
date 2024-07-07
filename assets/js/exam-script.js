// var selectedclass_teachername;

jQuery(document).ready(function($) {
    
    // Load Classes
    function loadClasses() {
        $.ajax({
            url: sms_exam_ajax_object.ajax_url,
            method: 'POST',
            data: {
                action: 'smp_load_classes',
                _ajax_nonce: sms_exam_ajax_object.nonce
            },
            success: function(response) {
                console.log("Classess Data ::: "+response.data);
                if (response.success) {
                    var classes = response.data;
                    $('#class-select').empty();
                    var x=0;
                    $.each(classes, function(index, classData) {
                        $('#class-select').append($('<option>', {
                            value: classData.class_id,
                            text: classData.subject_name
                        }));
                        if(x < 1){
                            loadTeacherName(classData.class_id);
                        }
                        x++;
                        
                    });
                    
                } else {
                    console.error('Error:', response.data);
                }
            },
            error: function(xhr, status, error) {
                // console.error('Error:', status, error);
                console.success(error);
                console.log(xhr.responseText);
            }
        });
    }

    //Load Teacher name
    function loadTeacherName(classId) {
        $.ajax({
            url: sms_exam_ajax_object.ajax_url,
            method: 'GET',
            data: {
                action: 'smp_get_teacher_name',
                class_id: classId
            },
            dataType: 'json',
            success: function(response) {
                console.log("Load Teacher name :: "+response.data.first_name);
                $('#teacher-name').val(response.data.first_name);
            }
        });
    }

    //Load Exams
    function loadExams() {
        $.ajax({
            url: sms_exam_ajax_object.ajax_url,
            method: 'GET',
            data: { action: 'smp_load_exams' },
            dataType: 'json',
            success: function(response) {
                if(response.success){
                    var examTableBody = $('#exam-table tbody');
                    var exams_results = response.data;
                    examTableBody.empty();
                    $.each(exams_results, function(index, exam) {
                        $('#exam-table tbody').append(
                            '<tr data-exam-id="'+ exam.exam_id +'" data-exam-teachername="'+ exam.teacher_name +'" data-exam-subject="'+ exam.subject_id +'" data-exam-date="'+ exam.exam_date +'" data-exam-time="'+ exam.exam_time +'" data-exam-status="'+ exam.exam_status +'">' +
                            '<td>' + (index + 1) + '</td>' +
                            '<td>' + exam.exam_id + '</td>' +
                            '<td>' + exam.teacher_name + '</td>' +
                            '<td>' + exam.subject_name + '</td>' +
                            '<td>' + exam.exam_date + '</td>' +
                            '<td>' + exam.exam_time + '</td>' +
                            '<td>' + exam.exam_status + '</td>' +
                            '<td>' +
                            '<button class="update-exam" data-exam-id="' + exam.exam_id + '" >Update</button>' +
                            '<button class="delete-exam" data-exam-id="' + exam.exam_id + '">Delete</button>' +
                            '</td>' +
                            '</tr>'
                        );
                    });
                    // console.log('Info:', response.data);
                }else{
                    // console.log('Info:', response.data);
                    var examTableBody = $('#exam-table tbody');
                    examTableBody.append(
                        '<tr>' +
                        '<td colspan="6">' + response.data + '</td>'+
                        '</tr>'
                    );

                }

            }
        });
    }

    $('#class-select').change(function() {
        var classId = $(this).val();
        console.log("class ID ::: "+classId);
        loadTeacherName(classId);
    });

    // $('#save-exam').click(function() {

    //     if( $('#save-exam').val() == "Save Exam"){

    //         var classId = $('#class-select').val();
    //         var examDate = $('#exam-date').val();
    //         var examTime = $('#exam-time').val();
    //         var examActive = $('#exam-active').is(':checked') ? 1 : 0;
    
    //         $.ajax({
    //             url: sms_exam_ajax_object.ajax_url,
    //             method: 'POST',
    //             data: {
    //                 action: 'smp_save_exam',
    //                 class_id: classId,
    //                 exam_date: examDate,
    //                 exam_time: examTime,
    //                 exam_status: examActive
    //             },
    //             success: function(response) {
    //                 alert('Exam saved successfully!');
    //                 // loadExams();
    //             }
    //         });


    //     }else if( $('#save-exam').val() == "Update Exam"){


    //         // // console.log("Update Function !!!");
    //         // var examID = $('#selected-exam-id').val();
    //         // var classId = $('#class-select').val();
    //         // var examDate = $('#exam-date').val();
    //         // var examTime = $('#exam-time').val();
    //         // var examActive = $('#exam-active').is(':checked') ? 1 : 0;


    //         // console.log("Update Values !!!"+examID+"- -"+classId+"- -"+examDate+" - "+examTime+" - "+examActive);

    //         // $.ajax({
    //         //     url: sms_exam_ajax_object.ajax_url,
    //         //     method: 'POST',
    //         //     data: {
    //         //         action: 'sms_update_exam',
    //         //         exam_id: examID,
    //         //         class_id: classId,
    //         //         exam_date: examDate,
    //         //         exam_time: examTime,
    //         //         exam_status: examActive
    //         //     },
    //         //     success: function(response) {
                    
    //         //         // loadExams();
    //         //         if (response.success) {
    //         //             alert('Exam saved successfully!');
    //         //         }else{
    //         //             alert('Exam saved faild!');
    //         //         }

    //         //     },
    //         //     error: function(xhr, status, error) {
    //         //         console.error('Error:', status, error);
    //         //         console.log(xhr.responseText);
    //         //     }
    //         // });


    //     }

       
    // });

    $('#save-exam').click(function() {
        var classId = $('#class-select').val();
        var examDate = $('#exam-date').val();
        var examTime = $('#exam-time').val();
        var examActive = $('#exam-active').is(':checked') ? 1 : 0;
        var examId = $('#selected-exam-id').val();

        if (examId) {
            // Update exam
            $.ajax({
                url: sms_exam_ajax_object.ajax_url,
                method: 'POST',
                data: {
                    action: 'sms_update_exam',
                    exam_id: examId,
                    class_id: classId,
                    exam_date: examDate,
                    exam_time: examTime,
                    exam_status: examActive,
                    _ajax_nonce: sms_exam_ajax_object.nonce
                },
                success: function(response) {
                    if (response.success) {
                        alert('Exam updated successfully!');
                        $('#save-exam').text('Save Exam');
                        $('#exam-form')[0].reset();
                        $('#selected-exam-id').val('');
                        loadExams();
                    } else {
                        alert('Failed to update exam.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', status, error);
                    console.log(xhr.responseText);
                }
            });
        } else {
            // Save new exam
            $.ajax({
                url: sms_exam_ajax_object.ajax_url,
                method: 'POST',
                data: {
                    action: 'smp_save_exam',
                    class_id: classId,
                    exam_date: examDate,
                    exam_time: examTime,
                    exam_status: examActive
                },
                success: function(response) {
                    alert('Exam saved successfully!');
                    loadExams();
                },
                error: function(xhr, status, error) {
                    console.error('Error:', status, error);
                    console.log(xhr.responseText);
                }
            });
        }
    });


    $(document).on('click', '.delete-exam', function() {
        var examId = $(this).data('exam-id');
        $.ajax({
            url: sms_exam_ajax_object.ajax_url,
            method: 'POST',
            data: {
                action: 'smp_delete_exam',
                exam_id: examId
            },
            success: function(response) {
                alert('Exam deleted successfully!');
                location.reload();
                // loadExams();
            }
        });
    });

    // // Initial load
    loadClasses();
    loadExams();
});

// window.addEventListener("load", (event) => {
//     console.log("page is fully loaded");
// });

// //Update Functions
jQuery(document).ready(function($) {
    // Open Update Modal and Load Exam Details
    $(document).on('click', '.update-exam', function() {
        var examId = $(this).data('exam-id');

        console.log("Update Function");
        $.ajax({
            url: sms_exam_ajax_object.ajax_url,
            method: 'POST',
            data: {
                action: 'smp_load_exam_details',
                exam_id: examId,
                _ajax_nonce: sms_exam_ajax_object.nonce
            },
            success: function(response) {
                if (response.success) {
                    var exam = response.data;
                    console.log("Data "+response.data);
                    
                    $('#class-select').val(exam.subject_id);
                    $('#teacher-name').val(exam.teacher_name);
                    $('#exam-date').val(exam.exam_date);
                    $('#exam-time').val(exam.exam_time);
                    $('#selected-exam-id').val(exam.exam_id);
                    $('#save-exam').val('Update Exam');

                    console.log("Value Button ::: "+$('#save-exam').val());
                    // $('#class-select').val(exam.teacher_name);
                    // $('#updateExamDate').val(exam.exam_date);
                    // $('#updateExamTime').val(exam.exam_time);
                    // $('#updateExamActive').prop('checked', exam.exam_status == 1);
                    // $('#updateExamId').val(exam.exam_id);
                    // $('#updateExamModal').show();
                } else {
                    alert('Failed to load exam details.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', status, error);
                console.log(xhr.responseText);
            }
        });
    });

    // Handle Update Exam Form Submission
    $('#updateExamForm').submit(function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            url: sms_exam_ajax_object.ajax_url,
            method: 'POST',
            data: formData + '&action=smp_update_exam&_ajax_nonce=' + sms_exam_ajax_object.nonce,
            success: function(response) {
                if (response.success) {
                    alert('Exam updated successfully.');
                    $('#updateExamModal').hide();
                    location.reload(); // Reload the page to reflect changes
                } else {
                    alert('Failed to update exam.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', status, error);
                console.log(xhr.responseText);
            }
        });
    });
});


// document.addEventListener('DOMContentLoaded', function() {

//     const rows = document.querySelectorAll('#exam-table tr[data-exam-id]');
//     const form = document.querySelector('form');
//     // const exam_id = form.querySelector('input[name="selected-exam-id"]');
//     // const exam_class = form.querySelector('select[name="exam-class"]');
//     // const exam_date = form.querySelector('input[name="exam-date"]');
//     // const exam_time = form.querySelector('input[name="exam-time"]');
//     // const exam_teacher = form.querySelector('input[name="exam-teacher-name"]');
//     // // const exam_active = form.querySelector('input[name="exam-active"]');
//     // const exam_submit_btn = form.querySelector('input[name="sms-exam-submit"]');

//     rows.forEach(row => {
//         row.querySelector('.update-exam').addEventListener('click', function(event) {
//             event.preventDefault();

//             const examID = row.getAttribute('data-exam-id');
//             const examsubjectID = row.getAttribute('data-exam-subject');
//             const examDate = row.getAttribute('data-exam-date');
//             const examTime = row.getAttribute('data-exam-time');
//             const examStatus = row.getAttribute('data-exam-status');
//             const examteacherName = row.getAttribute('data-exam-teachername');

//             console.log(" -- "+ examID +" -- "+ examsubjectID +" -- "+ examDate +" -- "+ examTime +" -- "+ examStatus +" -- "+ examteacherName +" -- ");

//             // exam_id.value = examID;
//             // exam_class.value = examsubjectID;
//             // exam_date.value = examDate;
//             // exam_time.value = examTime;
//             // exam_teacher.value = examteacherName;
//             // exam_active.value = examStatus;

//             form.action = "?page=sms-exam-registration&edit=" + examID;
//             exam_submit_btn.value = "Update Exam";
//         });
//     });

// });


