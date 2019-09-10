<?php
include('config/db_connect.php');

$password = $retypedpassword = $hashedpassword = $firstname = $lastname = $dob = $email = $gender = $userid = '';
$errors = array('password' => '', 'retypedpassword' => '', 'firstname' => '', 'lastname' => '', 'dob' => '', 'email' => '', 'gender' => '', 'phoneno' => '');

//Checks if button of name="submit" is clicked
if (isset($_POST['submit'])) {
    //Gets data from the POST request 
    if (empty($_POST['email'])) {
        $errors['email'] = 'Email is required!';
    } else {
        $email = $_POST['email'];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email is not valid!';
        } else {
            // Check db for existing email
            $email = mysqli_real_escape_string($conn, $email);
            $sql = "SELECT * FROM customer WHERE EMAIL = '$email'";
            $result = mysqli_query($conn, $sql);
            //$checkResult = mysqli_num_rows($result);
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

        // Usage of password_hash() that with inbuild default generated salt
        $hashedpassword = mysqli_real_escape_string($conn, password_hash($password, PASSWORD_DEFAULT));

        // Generate unique uid for the customer
        $unique = true;
        do {
            $userid = uniqid('CUS', true);
            $sql = "SELECT * FROM CUSTOMER WHERE USERID = $userid";
            $result = mysqli_query($conn, $sql);
            $checkResult = mysqli_num_rows($result);
            if ($checkResult > 0) {
                $unique = false;
            }
        } while (!$unique);

        // Inserts data to db and redirects user to homepage
        $sql = "INSERT INTO customer(EMAIL, FIRSTNAME, LASTNAME, PASSWORD, DOB, GENDER, USERID) 
		VALUES('$email', '$firstname', '$lastname', '$hashedpassword', '$dob', '$gender', '$userid')";
        if (mysqli_query($conn, $sql)) {
            header('Location: index.php');
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
<?php include("templates/header.php"); ?>

<section class="container grey-text">
    <h4 class="center">Register New Account</h4>
    <form action="register.php" class="white" method="POST">
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
                <input name="gender" type="radio" value="M" <?php if (isset($gender) && $gender=="m") echo "checked";?> />
                <span>Male</span>
            </label>
        </p>
        <p>
            <label>
                <input name="gender" type="radio" value="F" <?php if (isset($gender) && $gender=="f") echo "checked";?>/>
                <span>Female</span>
            </label>
        </p>
        <p>
            <label>
                <input name="gender" type="radio" value="O" <?php if (isset($gender) && $gender=="o") echo "checked";?>/>
                <span>Other</span>
            </label>
        </p>
        <div class="red-text"><?php echo htmlspecialchars($errors['gender']); ?></div>

        <label>Birthday: </label>
        <input type="date" name="dob" value="<?php echo htmlspecialchars($dob); ?>">
        <div class="red-text"><?php echo htmlspecialchars($errors['dob']); ?></div>

        <label>Password: </label>
        <input type="password" name="password" value="<?php echo htmlspecialchars($password); ?>">
        <div class="red-text"><?php echo htmlspecialchars($errors['password']); ?></div>

        <label>Confirm Password: </label>
        <input type="password" name="retypedpassword" value="<?php echo htmlspecialchars($retypedpassword); ?>">
        <div class="red-text"><?php echo htmlspecialchars($errors['retypedpassword']); ?></div>

        <div class="center">
            <input type="submit" name="submit" type="submit" class="btn brand z-depth-0">
        </div>
    </form>
</section>

<?php include("templates/footer.php"); ?>

</html>