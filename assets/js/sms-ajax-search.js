jQuery(document).ready(function($) {
    $('#sms_student_search').on('keyup', function() {
        var searchQuery = $(this).val();

        $.ajax({
            url: sms_ajax_obj.ajax_url,
            method: 'GET',
            data: {
                action: 'sms_search_students',
                student_search: searchQuery
            },
            success: function(response) {
                $('#sms_student_table').html(response);
            }
        });
    });
});


//Student Registraion Form Validation
jQuery(document).ready(function($) {
    $('form').submit(function(event) {
        let isValid = true;
        let errorMessage = '';

        // Validate Student ID
        let studentID = $('#student_id').val().trim();
        if (studentID === '') {
            isValid = false;
            errorMessage += 'Student ID is required.\n';
        }

        // Validate First Name
        let firstName = $('#first_name').val().trim();
        if (firstName === '') {
            isValid = false;
            errorMessage += 'First Name is required.\n';
        }

        // Validate Last Name
        let lastName = $('#last_name').val().trim();
        if (lastName === '') {
            isValid = false;
            errorMessage += 'Last Name is required.\n';
        }

        // Validate Email
        let email = $('#email').val().trim();
        if (email === '') {
            isValid = false;
            errorMessage += 'Email is required.\n';
        } else {
            // Check if email format is valid
            let emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
            if (!emailPattern.test(email)) {
                isValid = false;
                errorMessage += 'Invalid email format.\n';
            }
        }

        // If the form is invalid, prevent submission and show error messages
        if (!isValid) {
            alert(errorMessage);
            event.preventDefault();
        }
    });
});
