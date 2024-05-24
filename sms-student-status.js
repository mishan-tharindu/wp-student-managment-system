jQuery(document).ready(function($) {
    $('#sms_student_status_form').on('submit', function(e) {
        e.preventDefault();
        var studentId = $('#sms_student_id').val();

        $.ajax({
            url: ajaxurl,
            method: 'GET',
            data: {
                action: 'sms_get_student_details',
                student_id: studentId
            },
            success: function(response) {
                $('#sms_student_details').html(response);
            }
        });
    });

    $(document).on('click', '#sms_toggle_status', function() {
        var studentId = $(this).data('student-id');

        $.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
                action: 'sms_toggle_student_status',
                student_id: studentId
            },
            success: function(response) {
                if (response == 1) {
                    alert('Student status changed to Active');
                } else {
                    alert('Student status changed to Inactive');
                }
            }
        });
    });
});
