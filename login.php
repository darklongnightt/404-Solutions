<?php
include('config/db_connect.php');

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
        $hashedPassword = mysqli_real_escape_string($conn, hash('sha256', $password));

        // Gets a customer record from db as a single associative array
        $sql = "SELECT * FROM customer WHERE EMAIL = '$email'";
        $result = mysqli_query($conn, $sql);
        if ($customer = mysqli_fetch_assoc($result)) {
            // Usage of password_verify() results in constant time preventing timing attacks
            $checkPassword = password_verify($password, $customer['PASSWORD']);
        }

        mysqli_free_result($result);
        mysqli_close($conn);

        if ($checkPassword) {
            header('Location: index.php');
        } else {
            $errors['password'] = 'Invalid email or password!';
        }
    }
}
?>


<!DOCTYPE html>
<html>
<?php include("templates/header.php"); ?>

<section class="container grey-text">
    <h4 class="center">Log In</h4>
    <form action="login.php" class="white" method="POST">
        <label>Email Address: </label>
        <input type="text" name="email" value="<?php echo htmlspecialchars($email); ?>">
        <div class="red-text"><?php echo htmlspecialchars($errors['email']); ?></div>

        <label>Password: </label>
        <input type="password" name="password">
        <div class="red-text"><?php echo htmlspecialchars($errors['password']); ?></div>

        <div class="center">
            <input type="submit" name="submit" type="submit" class="btn brand z-depth-0">
        </div>
    </form>
</section>

<?php include("templates/footer.php"); ?>
</html>