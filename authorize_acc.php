<?php
include("config/db_connect.php");
include('templates/header.php');

$password = $checkPassword = $email_to_auth = $acc_type = '';
$email = $_SESSION['U_EMAIL'];
$errors = array('password' => '', 'email_to_auth' => '', 'acc_type' => '');

//Checks if button of name="submit" is clicked
if (isset($_POST['submit'])) {

    if (empty($_POST['acc_type'])) {
        $errors['acc_type'] = 'Account type is required!';
    } else {
        $acc_type = $_POST['acc_type'];
    }

    if (empty($_POST['password'])) {
        $errors['password'] = 'Your password is required!';
    } else {
        $password = $_POST['password'];
    }

    if (empty($_POST['email_to_auth'])) {
        $errors['email_to_auth'] = 'Email of user to authorize is required!';
    } else {
        $email_to_auth = $_POST['email_to_auth'];
    }

    // Checks if form is error free
    if (!array_filter($errors)) {

        // Formatting string for db security
        $email = mysqli_real_escape_string($conn, $email);
        $email_to_auth = mysqli_real_escape_string($conn, $_POST['email_to_auth']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);

        // Checks if customer exists
        $sql = "SELECT * FROM customer where EMAIL = '$email_to_auth'";
        $result = mysqli_query($conn, $sql);
        $checkResult = mysqli_num_rows($result);

        if ($checkResult > 0) {
            // Gets admin user account to check password
            $sql = "SELECT * FROM customer JOIN salt on customer.EMAIL = salt.EMAIL 
            WHERE customer.EMAIL = '$email'";
            if ($result = mysqli_query($conn, $sql)) {
                $customer = mysqli_fetch_assoc($result);

                // Usage of secured sha256 to hash password concat with generated salt
                $password .= $customer['SALT'];
                $password = hash('sha256', $password);
                $checkPassword = hash_equals($customer['PASSWORD'], $password) ? TRUE : FALSE;

                // Default deny policy
                if (!$checkPassword) {
                    $errors['password'] = 'Password is wrong!';
                } else if ($checkPassword) {
                    
                    // Generate new unique uid for the customer
                    $unique = true;
                    $userid = '';
                    do {
                        $userid = uniqid($acc_type, true);
                        $sql = "SELECT * FROM customer WHERE USERID = '$userid'";
                        $result = mysqli_query($conn, $sql);
                        $checkResult = mysqli_num_rows($result);
                        if ($checkResult > 0) {
                            $unique = false;
                        }
                    } while (!$unique);

                    // Update uid to authorize selected selected user
                    $sql = "UPDATE customer SET USERID='$userid' WHERE EMAIL = '$email_to_auth'";

                    if (mysqli_query($conn, $sql)) {
                        header("Location: index.php");
                    } else {
                        $errors['email_to_auth'] = 'Invalid user email!';
                    }
                }
            } else {
                echo 'Query Error: ' . mysqli_error($conn);
            }
        } else {
            $errors['email_to_auth'] = 'Invalid user email!';
        }

        mysqli_free_result($result);
        mysqli_close($conn);
    }
}

?>

<!DOCTYPE HTML>
<html>

<section class="container grey-text">
    <h4 class="center">Authorize Account</h4>
    <form action="authorize_acc.php" class="white" method="POST">
        <label>Account Email: </label>
        <input type="text" name="email_to_auth" value="<?php echo htmlspecialchars($email_to_auth); ?>">
        <div class="red-text"><?php echo htmlspecialchars($errors['email_to_auth']); ?></div>

        <label>Set Account Type: </label>
        <p>
            <label>
                <input name="acc_type" type="radio" value="ADM" <?php if (isset($acc_type) && $acc_type == "ADM") echo "checked"; ?>>
                <span>Admin</span>
            </label>
        </p>
        <p>
            <label>
                <input name="acc_type" type="radio" value="ANL" <?php if (isset($acc_type) && $acc_type == "ANL") echo "checked"; ?>>
                <span>Analyst</span>
            </label>
        </p>
        <div class="red-text"><?php echo htmlspecialchars($errors['acc_type']); ?></div>


        <label>Your Password: </label>
        <input type="password" name="password">
        <div class="red-text"><?php echo htmlspecialchars($errors['password']); ?></div>

        <div class="center">
            <input type="submit" name="submit" value="Authorize" class="btn brand z-depth-0">
        </div>
    </form>
</section>


<?php include("templates/footer.php"); ?>

</html>