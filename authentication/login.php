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
            if ($customer) {
                $checkPassword = hash_equals($customer['PASSWORD'], $password) ? TRUE : FALSE;
            } else {
                $errors['password'] = 'Invalid email or password!';
            }

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
                $_SESSION['U_CLUSTER'] = $customer['CLUSTER'];

                if (substr($_SESSION['U_UID'], 0, 3) == "CUS") {
                    // Update cart from cookies
                    $ano = $_COOKIE['UID'];
                    $cus = $_SESSION['U_UID'];

                    // Get cart items from guest user
                    $sql = "SELECT * FROM cart WHERE USERID='$ano'";
                    $result = mysqli_query($conn, $sql);
                    $anoCart = mysqli_fetch_all($result, MYSQLI_ASSOC);

                    // Transfer item quantity over to the identified customer
                    foreach ($anoCart as $item) {
                        // Check that cart item exists 
                        $id = $item['PDTID'];
                        $qty = $item['CARTQTY'];

                        $sql = "SELECT * FROM cart WHERE PDTID='$id' AND USERID='$cus'";
                        $result = mysqli_query($conn, $sql);
                        if (mysqli_num_rows($result) > 0) {
                            // Update product qty
                            $sql = "UPDATE cart SET CARTQTY=CARTQTY+'$qty' WHERE PDTID='$id' AND USERID='$cus'";
                        } else {
                            // Add to db cart
                            $sql = "INSERT INTO cart(PDTID, USERID, CARTQTY) VALUES('$id', '$cus', '$qty')";
                        }

                        if (!mysqli_query($conn, $sql)) {
                            echo 'Query Error: ' . mysqli_error($conn);
                        }
                    }

                    // Delete guest items from cart
                    $sql = "DELETE FROM cart WHERE USERID='$ano'";
                    if (mysqli_query($conn, $sql)) {

                        if ($customer['CHANGEPW'] == 'FALSE') {
                            echo "<script type='text/javascript'>window.top.location='/index.php';</script>";
                        } else {
                            echo "<script type='text/javascript'>window.top.location='change_password.php';</script>";
                        }
                    } else {
                        echo 'Query Error: ' . mysqli_error($conn);
                    }
                } else {
                    echo "<script type='text/javascript'>window.top.location='/analysis_report/cluster_report.php';</script>";
                }
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

        <label class="right"> New member?
            <u><a href="register.php" class="cyan-text">Register</a></u> </label>

        <?php if (array_filter($errors)) : ?>
            <label class="left"> Forget password?
                <u><a href="reset_password.php" class="cyan-text">Reset Password</a></u> </label>
        <?php endif ?>

        <br>

        <div class="center">
            <input type="submit" name="submit" value="Login" class="btn brand z-depth-0">
        </div>
    </form>
</section>

<?php include("../templates/footer.php"); ?>

</html>