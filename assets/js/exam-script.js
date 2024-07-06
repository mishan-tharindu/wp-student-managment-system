var selectedclass_teachername;

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
                if (response.success) {
                    var classes = response.data;
                    $('#class-select').empty();
                    $.each(classes, function(index, classData) {
                        $('#class-select').append($('<option>', {
                            value: classData.class_id,
                            text: classData.subject_name
                        }));
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
                            '<tr>' +
                            '<td>' + (index + 1) + '</td>' +
                            '<td>' + exam.exam_id + '</td>' +
                            '<td>' + exam.exam_date + '</td>' +
                            '<td>' + exam.exam_time + '</td>' +
                            '<td>' + exam.exam_status + '</td>' +
                            '<td>' +
                            '<button class="update-exam" data-exam-id="' + exam.exam_id + '">Update</button>' +
                            '<button class="delete-exam" data-exam-id="' + exam.exam_id + '">Delete</button>' +
                            '</td>' +
                            '</tr>'
                        );
                    });
                    console.log('Info:', response.data);
                }else{
                    console.log('Info:', response.data);
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

    $('#save-exam').click(function() {
        var classId = $('#class-select').val();
        var examDate = $('#exam-date').val();
        var examTime = $('#exam-time').val();
        var examActive = $('#exam-active').is(':checked') ? 1 : 0;

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
            }
        });
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

window.addEventListener("load", (event) => {
    console.log("page is fully loaded");
});

// function handleClassChange(){

//     selectedclass_teachername =document.getElementById("class-select").value;
//     console.log("Seleceted Class Teacher Name :: "+selectedclass_teachername);
//     loadTeacherName(selectedclass_teachername);
// }

// function loadTeacherName(teachername) {
//     document.getElementById("teacher-name").value = teachername;
// }

//Update Functions
jQuery(document).ready(function($) {
    // Open Update Modal and Load Exam Details
    $(document).on('click', '.update-exam', function() {
        var examId = $(this).data('exam-id');
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
                    $('#updateClassSelect').val(exam.class_id);
                    $('#updateTeacherName').val(exam.teacher_name);
                    $('#updateExamDate').val(exam.exam_date);
                    $('#updateExamTime').val(exam.exam_time);
                    $('#updateExamActive').prop('checked', exam.exam_status == 1);
                    $('#updateExamId').val(exam.exam_id);
                    $('#updateExamModal').show();
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


