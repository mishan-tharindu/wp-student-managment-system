<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<?php
    if(isset( $_POST['sms_submit'] ) && $_POST['sms_submit'] === "Register"){
        sms_register_teacher();
        // error_log("Calling Teacher Registration !!!");
    }else if(isset( $_POST['sms_submit'] ) && $_POST['sms_submit'] === "Update Teacher"){
        sms_update_teacher();
    }

    if ( isset( $_GET['delete'] ) ) {
        sms_delete_teacher($_GET['delete']);
        // echo '<script>window.location.href="?page=sms-teachers-registration"";</script>';
    }


?>
<form method="post" action="">
        <table>
            <tr>
                <th>ID:</th>
                <td><input type="text" name="teacher_id" value="<?php echo generate_unique_teacher_id(); ?>" readonly required/></td>
            </tr>
            <tr>
                <th>First Name:</th>
                <td><input type="text" name="first_name" value="" required /></td>
            </tr>
            <tr>
                <th>Last Name:</th>
                <td><input type="text" name="last_name" value="" required /></td>
            </tr>
            <tr>
                <th>Gender:</th>
                <td>
                    <input type="radio" id="male" name="gender" value="male" required>
                    <label for="male">Male</label><br>

                    <input type="radio" id="female" name="gender" value="female" required>
                    <label for="female">Female</label><br>

                    <input type="radio" id="other" name="gender" value="other" required>
                    <label for="other">Other</label><br>


                </td>
            </tr>
            <tr>
                <th>Hire Date</th>
                <td>
                <input type="date" id="hire_date" name="hire_date" required><br>
                </td>
            </tr>
            <tr>
                <th>Phone Number:</th>
                <td><input type="text" name="phone_number" value="" required /></td>
            </tr>
            <tr>
                <th>Address:</th>
                <td><input type="text" name="address" value="" required /></td>
            </tr>
            <tr>
                <th>Email:</th>
                <td><input type="email" name="email" value="" required /></td>
            </tr>
            <tr>
                <td colspan="2">
                <?php wp_nonce_field('teacher_form_action', 'teacher_form_nonce'); ?>
                <input type="hidden" name="action" value="sms_register_teacher">
                    <input type="submit" name="sms_submit" value="Register" />
                </td>
            </tr>
        </table>
    </form>
    <div id="registration-message"></div>
<br>
<h3> Teachers Table</h3>
<?php echo sms_teacher_table(); 

add_action('admin_footer', 'sms_add_admin_scripts');
    
    function sms_add_admin_scripts() {
        ?>
        <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function() {
                const rows = document.querySelectorAll('#teachers-table tr[data-teacher-id]');
                const form = document.querySelector('form');
                const teacherIDField = form.querySelector('input[name="teacher_id"]');
                const firstNameField = form.querySelector('input[name="first_name"]');
                const lastNameField = form.querySelector('input[name="last_name"]');
                // const genderField = form.querySelector('input[name="gender"]');
                // const hireDateField = form.querySelector('input[name="hire_date"]');
                const phone_numberField = form.querySelector('input[name="phone_number"]');
                const addressField = form.querySelector('input[name="address"]');
                const emailField = form.querySelector('input[name="email"]');
                const submitButton = form.querySelector('input[name="sms_submit"]');
               
    
                rows.forEach(row => {
                    row.querySelector('.edit-teacher').addEventListener('click', function(event) {
                        event.preventDefault();
    
                        const teacherId = row.getAttribute('data-teacher-id');
                        const firstName = row.getAttribute('data-first-name');
                        const lastName = row.getAttribute('data-last-name');
                        
                        const phonenumber = row.getAttribute('data-phonenumber');
                        const address = row.getAttribute('data-address');
                        const email = row.getAttribute('data-email');

                        const gender = row.getAttribute('data-gender');
                        const genderField = form.querySelector(`input[name="gender"][value="${gender}"]`);
                        if (genderField) {
                            genderField.checked = true;
                        }
                        
                        teacherIDField.value = teacherId;
                        firstNameField.value = firstName;
                        lastNameField.value = lastName;
                        phone_numberField.value = phonenumber;
                        addressField.value = address;
                        emailField.value = email;

                         // Set the hire date
                        const hireDate = row.getAttribute('data-hire-date');
                        const hireDateField = form.querySelector('input[name="hire_date"]');
                        if (hireDateField) {
                            hireDateField.value = hireDate;
                        }
                       
                        form.action = "?page=sms-teachers-registration&edit=" + teacherId;
                        submitButton.value = "Update Teacher";
                    });
                });
            });
        </script>
        <?php
    }

