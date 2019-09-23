<?php
include('../config/db_connect.php');
include("../templates/header.php");

$password = $email = $checkPassword = '';
$errors = array('password' => '', 'email' => '');

//Checks if button of name="submit" is clicked
if (isset($_POST['submit'])) {
    //Gets data from the POST request 
    if (empty($_POST['email'])) {
        $errors['email'] = 'Email is required!';
    } else {
        $email = $_POST['email'];
    }

    if (empty($_POST['password'])) {
        $errors['password'] = 'Password is required!';
    } else {
        $password = $_POST['password'];
    }

    // Checks if form is error free
    if (!array_filter($errors)) {

        // Formatting string for db security
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);

        // Gets a customer record from db as a single associative array
        $sql = "SELECT * FROM customer JOIN salt on customer.EMAIL = salt.EMAIL 
        WHERE customer.EMAIL = '$email'";
        if ($result = mysqli_query($conn, $sql)) {
            $customer = mysqli_fetch_assoc($result);

            // Usage of secured sha256 to hash password concat with generated salt
            $password .= $customer['SALT'];
            $password = hash('sha256', $password);
            echo $password;
            $checkPassword = ($password == $customer['PASSWORD'] ? TRUE : FALSE);

            // Default deny policy
            if (!$checkPassword) {
                $errors['password'] = 'Invalid email or password!';
            } else if ($checkPassword) {
                // Set session variables
                $_SESSION['U_UID'] = $customer['USERID'];
                $_SESSION['U_FIRSTNAME'] = $customer['FIRSTNAME'];
                $_SESSION['U_LASTNAME'] = $customer['LASTNAME'];
                $_SESSION['U_EMAIL'] = $customer['EMAIL'];
                $_SESSION['U_GENDER'] = $customer['GENDER'];
                $_SESSION['U_DOB'] = $customer['DOB'];
                $_SESSION['U_INITIALS'] = $customer['FIRSTNAME'][0] . $customer['LASTNAME'][0];

                header('Location: ../index.php?login=success');
            }
        } else {
            echo 'Query Error: ' . mysqli_error($conn);
        }

        mysqli_free_result($result);
        mysqli_close($conn);
    }
}
?>


<!DOCTYPE html>
<html>

<section class="container grey-text">
    <h4 class="center">Login</h4>
    <form action="login.php" class="EditForm" method="POST">
        <label>Email Address: </label>
        <input type="text" name="email" value="<?php echo htmlspecialchars($email); ?>">
        <div class="red-text"><?php echo htmlspecialchars($errors['email']); ?></div>

        <label>Password: </label>
        <input type="password" name="password">
        <div class="red-text"><?php echo htmlspecialchars($errors['password']); ?></div>

        <div class="center">
            <input type="submit" name="submit" value="Login" class="btn brand z-depth-0">
        </div>
    </form>
</section>

<?php include("../templates/footer.php"); ?>

</html>