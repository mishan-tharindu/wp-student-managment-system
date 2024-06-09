jQuery(document).ready(function($) {
    $('#student-registration-form').on('submit', function(e) {
        e.preventDefault();

        console.log("Ajax Student Data ::: "+$('#student_id').val(), $('#first_name').val(),$('#last_name').val(),$('#email').val())

        var formData = {
            action: 'sms_register_student',
            student_id: $('#student_id').val(),
            first_name: $('#first_name').val(),
            last_name: $('#last_name').val(),
            email: $('#email').val(),
        };

        $.ajax({
            url: ajax_object.ajax_url,
            type: 'POST',
            data: formData,
            success: function(response) {
                // $('#registration-message').html(response);
                // $('#student-registration-form')[0].reset();
                // console.log(response);

                if (response.success) {
                    // Clear the table body
                    $('#sms_student_table').empty();

                    // Append the new student data to the table
                    $.each(response.data, function(index, student) {
                        var row = '<tr>' +
                            '<td>' + student.student_id + '</td>' +
                            '<td>' + student.first_name + '</td>' +
                            '<td>' + student.email + '</td>' +
                            '</tr>';
                        $('#sms_student_table').append(row);
                    });

                    // Display success message
                    
                    $('#registration-message').html('<script>alert("Student registered successfully!");</script>');
                    $('#student-registration-form')[0].reset();

                    // Request a new student ID and update the field
                    $.ajax({
                        url: ajax_object.ajax_url,
                        type: 'POST',
                        data: { action: 'generate_new_student_id' },
                        success: function(newIdResponse) {
                            if (newIdResponse.success) {
                                $('#student_id').val(newIdResponse.data);
                            }
                        }
                    });
                    
                } else {
                    // Display error message
                    $('#registration-message').html(''+ response.data +'");</script');
                }


            }
        });


    });
});


// Student update delete Function

jQuery(document).ready(function($) {
    // Handle form submission for registration and update
    $('#student-update-delete-form').on('submit', function(e) {
        e.preventDefault();

        var actionType = $('#action_type').val();
        var ajaxAction = actionType === 'register' ? 'sms_register_student' : 'sms_update_student';

        var formData = {
            action: ajaxAction,
            student_id: $('#student_id').val(),
            first_name: $('#first_name').val(),
            last_name: $('#last_name').val(),
            email: $('#email').val(),
        };

        $.ajax({
            url: ajax_object.ajax_url,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    // Clear the table body
                    $('#sms_student_table').empty();

                    // Append the new student data to the table
                    $.each(response.data, function(index, student) {
                        var row = '<tr>' +
                            '<td>' + student.student_id + '</td>' +
                            '<td>' + student.first_name + '</td>' +
                            '<td>' + student.email + '</td>' +
                            '<td><button class="edit-student" data-id="' + student.student_id + '">Edit</button> <button class="delete-student" data-id="' + student.student_id + '">Delete</button></td>' +
                            '</tr>';
                        $('#sms_student_table').append(row);
                    });

                    // Display success message
                    if (actionType === 'register') {
                        alert('Student registered successfully!');
                    } else {
                        alert('Student updated successfully!');
                    }

                    // Clear form fields and reset the form
                    $('#student_id').val(response.new_id);
                    $('#first_name').val('');
                    $('#last_name').val('');
                    $('#email').val('');
                    $('#submit_student').val('Register Student');
                    $('#action_type').val('register');
                } else {
                    // Display error message
                    alert("Student registered Unsuccessfully!");
                }
            }
        });
    });

    // Handle edit button click
    $(document).on('click', '.edit-student', function() {
        var studentId = $(this).data('id');

        $.ajax({
            url: ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'sms_get_student',
                student_id: studentId
            },
            success: function(response) {
                if (response.success) {
                    var student = response.data;
                    $('#student_id').val(student.student_id);
                    $('#first_name').val(student.first_name);
                    $('#last_name').val(student.last_name);
                    $('#email').val(student.email);
                    $('#submit_student').val('Update Student');
                    $('#action_type').val('update');
                } else {
                    alert("Student Update Unsuccessfully!");
                }
            }
        });
    });

    // Handle delete button click
    $(document).on('click', '.delete-student', function() {
        var studentId = $(this).data('id');
        var studentName = $(this).closest('tr').find('td:nth-child(2)').text();

        $('#delete-student-id').text(studentId);
        $('#delete-student-name').text(studentName);
        $('#delete-confirmation').show();
    });

    // Handle delete confirmation
    $('#confirm-delete-button').on('click', function() {
        if ($('#confirm-delete').is(':checked')) {
            var studentId = $('#delete-student-id').text();

            $.ajax({
                url: ajax_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'sms_delete_student',
                    student_id: studentId
                },
                success: function(response) {
                    if (response.success) {
                        // Refresh the student table
                        $('#sms_student_table').empty();

                        // Append the updated student data to the table
                        $.each(response.data, function(index, student) {
                            var row = '<tr>' +
                                '<td>' + student.student_id + '</td>' +
                                '<td>' + student.first_name + '</td>' +
                                '<td>' + student.email + '</td>' +
                                '<td><button class="edit-student" data-id="' + student.student_id + '">Edit</button> <button class="delete-student" data-id="' + student.student_id + '">Delete</button></td>' +
                                '</tr>';
                            $('#sms_student_table').append(row);
                        });

                        // Hide delete confirmation and clear checkbox
                        $('#delete-confirmation').hide();
                        $('#confirm-delete').prop('checked', false);

                        // Display success message
                        alert('Student deleted successfully!');

                    } else {
                        // alert(response.message);
                        alert("Student Delete Unsuccessfully!");
                    }
                }
            });
        } else {
            alert('Please confirm the deletion.');
        }
    });

        // Handle cancel button click
        $('#cancel-delete-button, #close-popup').on('click', function() {
            $('#popup-overlay').hide();
            $('#delete-confirmation').hide();
            $('#confirm-delete').prop('checked', false);
        });
    
        // Hide the delete confirmation box when clicking outside of it
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#delete-confirmation, .delete-student').length) {
                $('#popup-overlay').hide();
                $('#delete-confirmation').hide();
            }
        });

});



