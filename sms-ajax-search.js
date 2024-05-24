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
