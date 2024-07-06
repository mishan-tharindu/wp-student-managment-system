// // assets/js/sms-subject-management.js
// jQuery(document).ready(function($) {
//     // Load all subjects on page load
//     function loadSubjects() {
//         $.ajax({
//             url: ajax_object.ajax_url,
//             type: 'POST',
//             data: { action: 'sms_get_all_subjects' },
//             success: function(response) {
//                 if (response.success) {
//                     $('#sms_subject_table').empty();
//                     $.each(response.data, function(index, subject) {
//                         var row = '<tr>' +
//                             '<td>' + subject.subject_id + '</td>' +
//                             '<td>' + subject.subject_name + '</td>' +
//                             '<td>' + subject.subject_description + '</td>' +
//                             '<td>' +
//                             '<button class="edit-subject" data-id="' + subject.subject_id + '">Edit</button> ' +
//                             '<button class="delete-subject" data-id="' + subject.subject_id + '">Delete</button>' +
//                             '</td>' +
//                             '</tr>';
//                         $('#sms_subject_table').append(row);
//                     });
//                 } else {
//                     alert('Failed to load subjects.');
//                 }
//             },
//             error: function() {
//                 alert('Failed to load subjects.');
//             }
//         });
//     }

//     loadSubjects(); // Initial load

//     // Handle form submission for registration and update
//     $('#subject-registration-form').on('submit', function(e) {
//         e.preventDefault();

//         var actionType = $('#subject_id').val() ? 'sms_update_subject' : 'sms_register_subject';

//         var formData = {
//             action: actionType,
//             subject_id: $('#subject_id').val(),
//             subject_name: $('#subject_name').val(),
//             subject_description: $('#subject_description').val()
//         };

//         $.ajax({
//             url: ajax_object.ajax_url,
//             type: 'POST',
//             data: formData,
//             success: function(response) {
//                 if (response.success) {
//                     loadSubjects();
//                     alert(actionType === 'sms_register_subject' ? 'Subject registered successfully!' : 'Subject updated successfully!');
//                     $('#subject-registration-form')[0].reset();
//                     $('#submit_subject').val('Register Subject');
//                     $('#subject_id').val('');
//                 } else {
//                     alert(response.data.message || 'Failed to register/update subject.');
//                 }
//             },
//             error: function() {
//                 alert('Failed to register/update subject.');
//             }
//         });
//     });

//     // Handle edit button click
//     $(document).on('click', '.edit-subject', function() {
//         var subjectId = $(this).data('id');

//         $.ajax({
//             url: ajax_object.ajax_url,
//             type: 'POST',
//             data: { action: 'sms_get_subject', subject_id: subjectId },
//             success: function(response) {
//                 if (response.success) {
//                     var subject = response.data;
//                     $('#subject_id').val(subject.subject_id);
//                     $('#subject_name').val(subject.subject_name);
//                     $('#subject_description').val(subject.subject_description);
//                     $('#submit_subject').val('Update Subject');
//                 } else {
//                     alert(response.data.message || 'Failed to fetch subject details.');
//                 }
//             },
//             error: function() {
//                 alert('Failed to fetch subject details.');
//             }
//         });
//     });

//     // Handle delete button click
//     $(document).on('click', '.delete-subject', function() {
//         var subjectId = $(this).data('id');

//         if (confirm('Are you sure you want to delete this subject?')) {
//             $.ajax({
//                 url: ajax_object.ajax_url,
//                 type: 'POST',
//                 data: { action: 'sms_delete_subject', subject_id: subjectId },
//                 success: function(response) {
//                     if (response.success) {
//                         loadSubjects();
//                         alert('Subject deleted successfully!');
//                     } else {
//                         alert(response.data.message || 'Failed to delete subject.');
//                     }
//                 },
//                 error: function() {
//                     alert('Failed to delete subject.');
//                 }
//             });
//         }
//     });
// });

document.addEventListener('DOMContentLoaded', function() {
    const rows = document.querySelectorAll('#subject-table tr[data-subject-id]');
    const form = document.querySelector('form');
    const subject_id = form.querySelector('input[name="subject_id"]');
    const subject_name = form.querySelector('input[name="subject_name"]');
    const subject_description = form.querySelector('textarea[name="subject_description"]');
    const submitButton = form.querySelector('input[name="sms_submit"]');

    rows.forEach(row => {
        row.querySelector('.edit-subject').addEventListener('click', function(event) {
            event.preventDefault();

            const subjectid = row.getAttribute('data-subject-id');
            const subjectname = row.getAttribute('data-subject-name');
            const subjectdiscription = row.getAttribute('data-subject-discription');

            subject_id.value = subjectid;
            subject_name.value = subjectname;
            subject_description.value = subjectdiscription;

            form.action = "?page=sms-subject-registration&edit=" + subjectid;
            submitButton.value = "Update Subject";
        });
    });
});

