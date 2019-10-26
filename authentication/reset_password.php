<?php
include("../config/db_connect.php");
include("../templates/header.php");

$email = '';
$errors = array('email' => '');

// Checks if reset button is pressed
if (isset($_POST['submit'])) {
    if (empty($_POST['email'])) {
        $errors['email'] = "Email address is required!";
    } else {

        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email is not of valid format!';
        } else {
            $email = mysqli_real_escape_string($conn, $_POST['email']);
        }
    }

    if (!array_filter($errors)) {
        // Checks if email entered is registered
        $sql = "SELECT * FROM customer WHERE EMAIL='$email'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) < 1) {
            $errors['email'] = "Email address entered is not registered!";
        } else {
            // Generate new password and salt
            $new_password = uniqid();
            $new_salt = uniqid();
            $password = $new_password . $new_salt;
            $hashedpassword = hash('sha256', $password);

            // Update customer password and salt
            $sql = "UPDATE customer SET PASSWORD='$hashedpassword' WHERE EMAIL='$email'";
            if (mysqli_query($conn, $sql)) {
                $sql = "UPDATE salt SET SALT='$new_salt', CHANGEPW='TRUE' WHERE EMAIL='$email'";
                if (!mysqli_query($conn, $sql)) {
                    echo 'Query Error: ' . mysqli_error($conn);
                } else {
                    // Get current singapore time
                    date_default_timezone_set('Singapore');
                    $date = date('m/d/Y, h:i:s a', time());

                    sendEmail($email, $new_password, $date);
                    echo "<script type='text/javascript'>window.top.location='login.php';</script>";
                }
            } else {
                echo 'Query Error: ' . mysqli_error($conn);
            }
        }

        mysqli_free_result($result);
        mysqli_close($conn);
    }
}

function sendEmail($to, $new_password, $date)
{
    $subject = "SuperData Password Reset";
    $message = '
<html>
<head>
    <title>Account Password Reset</title>
</head>

<body>';
    $message .= '<h4>Dear SuperData User, </h4>';
    $message .= '<div>You have requested to reset your password at ' . htmlspecialchars($date) . '. Please contact us at super.data.fyp@gmail.com if you did not make the following request. </div>';
    $message .= '<div>Note: You will be prompted to change your password upon login.</div>';
    $message .= '<div class="container" style="border-radius: 15px; border: 5px solid; max-width: 850px; text-align: center;">';
    $message .= '<h4 style="padding: 15px;">NEW PASSWORD: ' . $new_password . '</h4>
    </div> <br>';
    $message .= '<h4>SuperData Security Team</h4>
</body>
</html>';

    // Always set content-type when sending HTML email
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: <super.data.fyp@gmail.com>' . "\r\n";

    mail($to, $subject, $message, $headers);
}

?>

<!DOCTYPE HTML>
<html>
<div class="container grey-text">
    <h4 class="center">Reset Password</h4>
    <form class="EditForm" action="reset_password.php" method="POST">
        <label>Email Address:</label>
        <input type="text" name="email">
        <div class="red-text"><?php echo htmlspecialchars($errors['email']); ?></div>

        <div class="grey-text">Note: An email containing the new password will be sent to your email address.</div>
        <br>

        <div class="center">
            <input type="submit" name="submit" value="Reset" class="btn brand z-depth-0">
        </div>
    </form>
</div>

<?php include("../templates/footer.php"); ?>

</html>