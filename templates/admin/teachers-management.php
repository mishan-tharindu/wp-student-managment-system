<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<h1>Teachers Management</h1>
<p>Content for Teachers Management page.</p>

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
                <th>Subject:</th>
                <td>
                    <select id="subject_id" name="subject_id" required>
                        <?php
                        // Fetch subjects from the database and populate the dropdown
                        global $wpdb;
                        $tabel_name = $wpdb->prefix . "subject";
                        $subjects = $wpdb->get_results("SELECT * FROM $tabel_name");
                        foreach ($subjects as $subject) {
                            echo "<option value='{$subject->subject_id}'>{$subject->subject_name}</option>";
                        }
                        ?>
                    </select>
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
                    <input type="submit" name="sms_submit" value="<?php echo isset( $_GET['edit'] ) ? 'Update Student' : 'Register Student'; ?>" />
                </td>
            </tr>
        </table>
    </form>