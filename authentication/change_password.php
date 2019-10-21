<?php
include("../config/db_connect.php");
include("../templates/header.php");

$errors = array('oldpassword' => '', 'newpassword1' => '', 'newpassword2' => '');
$oldpassword = $newpassword1 = $newpassword2 = '';

// Checks if change password button is pressed
if (isset($_POST['submit'])) {

    // Checks for input errors
    if (empty($_POST['oldpassword'])) {
        $errors['oldpassword'] = "Old password is required!";
    } else {
        $oldpassword = mysqli_real_escape_string($conn, $_POST['oldpassword']);
    }

    if (empty($_POST['newpassword1'])) {
        $errors['newpassword1'] = "New password is required!";
    } else {
        $newpassword1 = mysqli_real_escape_string($conn, $_POST['newpassword1']);
    }

    if (empty($_POST['newpassword2'])) {
        $errors['newpassword2'] = "Confirmed new password is required!";
    } else {
        if ($_POST['newpassword1'] != $_POST['newpassword2']) {
            $errors['newpassword2'] = "Confirmed password must be the same!";
        } else {
            $newpassword2 = mysqli_real_escape_string($conn, $_POST['newpassword2']);
        }
    }

    // No errors with the passwords
    if (!array_filter($errors)) {

        // Gets a customer record from db as a single associative array
        $sql = "SELECT * FROM customer JOIN salt on customer.EMAIL = salt.EMAIL 
        WHERE customer.USERID = '$uid'";
        $result = mysqli_query($conn, $sql);
        $customer = mysqli_fetch_assoc($result);

        // Usage of secured sha256 to hash password concat with generated salt
        $oldpassword .= $customer['SALT'];
        $hashedpassword = hash('sha256', $oldpassword);
        $checkPassword = hash_equals($customer['PASSWORD'], $hashedpassword) ? TRUE : FALSE;

        // Default deny policy
        if (!$checkPassword) {
            $errors['oldpassword'] = 'Invalid password entered!';
        } else if ($checkPassword) {

            // Correct password is entered, proceeds to update db
            $newpassword1 .= $customer['SALT'];
            $new_hashedpassword = hash('sha256', $newpassword1);
            $sql = "UPDATE customer SET PASSWORD='$new_hashedpassword' WHERE USERID='$uid'";
            if (mysqli_query($conn, $sql)) {

                // Update status of changepw to true
                $email = $customer['EMAIL'];
                $sql = "UPDATE salt SET CHANGEPW='FALSE' WHERE EMAIL='$email'";
                if (mysqli_query($conn, $sql)) {
                    header("Location: ../index.php");
                } else {
                    echo 'Query Error: ' . mysqli_error($conn);
                }
            } else {
                echo 'Query Error: ' . mysqli_error($conn);
            }
        }

        mysqli_free_result($result);
        mysqli_close($conn);
    }
}
?>

<!DOCTYPE HTML>
<html>
<div class="container grey-text">
    <h4 class="center">Change Password</h4>
    <form class="EditForm" action="change_password.php" method="POST">
        <label>Old Password: </label>
        <input type="password" name="oldpassword">
        <div class="red-text"><?php echo htmlspecialchars($errors['oldpassword']); ?></div>

        <label>New Password: </label>
        <input type="password" name="newpassword1">
        <div class="red-text"><?php echo htmlspecialchars($errors['newpassword1']); ?></div>

        <label>Confirm New Password: </label>
        <input type="password" name="newpassword2">
        <div class="red-text"><?php echo htmlspecialchars($errors['newpassword2']); ?></div>
        <br>
        <div class="center">
            <input type="submit" name="submit" value="Change Password" class="btn brand z-depth-0">
        </div>
    </form>
</div>

<?php include("../templates/footer.php"); ?>

</html>