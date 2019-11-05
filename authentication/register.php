<?php
include('../config/db_connect.php');
include('../templates/header.php');

$password = $retypedpassword = $hashedpassword = $firstname = $lastname = $dob = $email = $gender = $userid = $phoneno = '';
$errors = array('password' => '', 'retypedpassword' => '', 'firstname' => '', 'lastname' => '', 'dob' => '', 'email' => '', 'gender' => '', 'phoneno' => '');
$today = date('Y-m-d');

//Checks if button of name="submit" is clicked
if (isset($_POST['submit'])) {
    //Gets data from the POST request 
    if (empty($_POST['email'])) {
        $errors['email'] = 'Email is required!';
    } else {
        $email = $_POST['email'];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email is not of valid format!';
        } else {
            // Check db for existing email
            $email = mysqli_real_escape_string($conn, $email);
            $sql = "SELECT * FROM customer WHERE EMAIL = '$email'";
            $result = mysqli_query($conn, $sql);

            if (mysqli_num_rows($result) > 0) {
                $errors['email'] = 'Entered email is already in use!';
            }
        }
    }

    if (empty($_POST['firstname'])) {
        $errors['firstname'] = 'First name is required!';
    } else {
        $firstname = $_POST['firstname'];
    }

    if (empty($_POST['lastname'])) {
        $errors['lastname'] = 'Last name is required!';
    } else {
        $lastname = $_POST['lastname'];
    }

    if (empty($_POST['gender'])) {
        $errors['gender'] = 'Gender is required!';
    } else {
        $gender = $_POST['gender'];
    }

    if (empty($_POST['dob'])) {
        $errors['dob'] = 'Birthday is required!';
    } else {
        $dob = $_POST['dob'];
    }

    if (empty($_POST['phoneno'])) {
        $errors['phoneno'] = 'Phone number is required!';
    } else {
        $phoneno = $_POST['phoneno'];
        if (!preg_match('/^[0-9]*$/', $phoneno)) {
            $errors['phoneno'] = 'Phone number must be numeric!';
        }
    }

    if (empty($_POST['password'])) {
        $errors['password'] = 'Password is required!';
    } else {
        $password = $_POST['password'];
    }

    if (empty($_POST['retypedpassword'])) {
        $errors['retypedpassword'] = 'Confirmed password is required!';
    } else {
        $retypedpassword = $_POST['retypedpassword'];

        if ($password != $retypedpassword) {
            $errors['retypedpassword'] = 'Confirmed password must be the same!';
        }
    }

    // Checks if form is error free
    if (!array_filter($errors)) {

        // Formatting string for db security
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $firstname = mysqli_real_escape_string($conn, $_POST['firstname']);
        $lastname = mysqli_real_escape_string($conn, $_POST['lastname']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);
        $phoneno = mysqli_real_escape_string($conn, $_POST['phoneno']);

        // Usage of secured sha256 to hash password concat with generated salt
        $salt = uniqid();
        $password .= $salt;
        $hashedpassword = hash('sha256', $password);

        // Generate unique uid for the customer
        $unique = true;
        do {
            $userid = uniqid('CUS');
            $sql = "SELECT * FROM customer WHERE USERID = '$userid'";
            $result = mysqli_query($conn, $sql);
            $checkResult = mysqli_num_rows($result);
            if ($checkResult > 0) {
                $unique = false;
            }
        } while (!$unique);

        // Inserts data to db and redirects user to homepage
        $sql = "INSERT INTO customer(EMAIL, FIRSTNAME, LASTNAME, PASSWORD, DOB, GENDER, USERID, PHONENO) 
        VALUES('$email', '$firstname', '$lastname', '$hashedpassword', '$dob', '$gender', '$userid', '$phoneno')";

        if (mysqli_query($conn, $sql)) {
            $sql = "INSERT INTO salt(SALT, EMAIL) VALUES('$salt', '$email')";
            if (mysqli_query($conn, $sql)) {
                // Successfully registered user into db, set session variables
                $_SESSION['U_UID'] = $userid;
                $_SESSION['U_FIRSTNAME'] = $firstname;
                $_SESSION['U_LASTNAME'] = $lastname;
                $_SESSION['U_EMAIL'] = $email;
                $_SESSION['U_GENDER'] = $gender;
                $_SESSION['U_DOB'] = $dob;
                $_SESSION['U_INITIALS'] = $firstname[0] . $lastname[0];
                $_SESSION['U_CLUSTER'] = 0;

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
                    setcookie('LASTACTION', 'REGISTER', time() + (120), "/");
                    echo "<script type='text/javascript'>window.top.location='shipping_details.php';</script>";
                } else {
                    echo 'Query Error: ' . mysqli_error($conn);
                }
            } else {
                echo 'Query Error: ' . mysqli_error($conn);
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

<section class="container">
    <h4 class="center grey-text">Register Account</h4>

    <div class="row">
        <div class="col s12 m8 offset-m2">
            <form action="register.php" class="EditForm" method="POST" style="width:100%;">
                <label>Email Address: </label>
                <input type="text" name="email" value="<?php echo htmlspecialchars($email); ?>">
                <div class="red-text"><?php echo htmlspecialchars($errors['email']); ?></div>

                <label>First Name: </label>
                <input type="text" name="firstname" value="<?php echo htmlspecialchars($firstname); ?>">
                <div class="red-text"><?php echo htmlspecialchars($errors['firstname']); ?></div>

                <label>Last Name: </label>
                <input type="text" name="lastname" value="<?php echo htmlspecialchars($lastname); ?>">
                <div class="red-text"><?php echo htmlspecialchars($errors['lastname']); ?></div>

                <label>Gender: </label>
                <p>
                    <label>
                        <input name="gender" type="radio" value="M" <?php if (isset($gender) && $gender == "M") echo "checked"; ?>>
                        <span>Male</span>
                    </label>
                </p>
                <p>
                    <label>
                        <input name="gender" type="radio" value="F" <?php if (isset($gender) && $gender == "F") echo "checked"; ?>>
                        <span>Female</span>
                    </label>
                </p>
                <p>
                    <label>
                        <input name="gender" type="radio" value="O" <?php if (isset($gender) && $gender == "O") echo "checked"; ?>>
                        <span>Other</span>
                    </label>
                </p>
                <div class="red-text"><?php echo htmlspecialchars($errors['gender']); ?></div>

                <label>Birthday: </label>
                <input type="date" name="dob" min="1900-01-01" max="<?php echo $today ?>" value="<?php echo $dob ?>">
                <div class="red-text"><?php echo htmlspecialchars($errors['dob']); ?></div>

                <label>Phone Number: </label>
                <input type="text" name="phoneno" value="<?php echo htmlspecialchars($phoneno); ?>">
                <div class="red-text"><?php echo htmlspecialchars($errors['phoneno']); ?></div>

                <label>Password: </label>
                <input type="password" name="password" value="<?php echo htmlspecialchars($password); ?>">
                <div class="red-text"><?php echo htmlspecialchars($errors['password']); ?></div>

                <label>Confirm Password: </label>
                <input type="password" name="retypedpassword" value="<?php echo htmlspecialchars($retypedpassword); ?>">
                <div class="red-text"><?php echo htmlspecialchars($errors['retypedpassword']); ?></div>

                <div class="center">
                    <input type="submit" name="submit" value="register" class="btn brand z-depth-0 form-btn">
                </div>
            </form>
        </div>
    </div>

</section>

<?php include("../templates/footer.php"); ?>

</html>