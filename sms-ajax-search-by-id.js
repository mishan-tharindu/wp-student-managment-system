jQuery(document).ready(function($) {
    $('#sms_student_id_search').on('keyup', function(event) {
        var searchQuery = $(this).val();

        $.ajax({
            url: sms_ajax_obj.ajax_url,
            method: 'GET',
            data: {
                action: 'sms_search_student_by_id',
                student_id_search: searchQuery
            },
            success: function(response) {
                $('#sms_student_by_id_table').html(response);
            }
        });
    });

    // Handle Enter key press to toggle Active status
    $('#sms_student_id_search').on('keypress', function(event) {
        if (event.which === 13) { // Enter key pressed
            event.preventDefault();
            var studentId = $(this).val();

            $.ajax({
                url: sms_ajax_obj.ajax_url,
                method: 'POST',
                data: {
                    action: 'sms_toggle_active_status',
                    student_id: studentId
                },
                success: function(response) {
                    $('#sms_student_by_id_table').html(response);
                }
            });
        }
    });

    // Handle Active button click to toggle Active status
    $(document).on('click', '.toggle-active-status', function() {
        var studentId = $(this).data('id');

        $.ajax({
            url: sms_ajax_obj.ajax_url,
            method: 'POST',
            data: {
                action: 'sms_toggle_active_status',
                student_id: studentId
            },
            success: function(response) {
                $('#sms_student_by_id_table').html(response);
            }
        });
    });
});
