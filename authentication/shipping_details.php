<?php
include("../config/db_connect.php");
include("../templates/header.php");

$country1 = $country2 = $addr1 = $addr2 = $postal1 = $postal2 = '';
$errors = array('addr1' => '', 'postal1' => '', 'postal2' => '');

// If redirect to register page if user is not logged in
$userId = mysqli_real_escape_string($conn, $_SESSION['U_UID']);

if (isset($_POST['submit'])) {
    // Gets data from the POST request 
    if (empty($_POST['addr1'])) {
        $errors['addr1'] = 'Main shipping address is required!';
    } else {
        $addr1 = $_POST['addr1'];
    }

    if (empty($_POST['postal1'])) {
        $errors['postal1'] = 'Main shipping postal code is required!';
    } else {
        $postal1 = $_POST['postal1'];
        if (!preg_match('/^[0-9]+$/', $postal1)) {
            $errors['postal1'] = 'Postal code must be in numbers only!';
        }
    }

    if (empty($_POST['country1'])) {
        $errors['country1'] = 'Main shipping country is required!';
    } else {
        $country1 = $_POST['country1'];
    }

    // If address 2 is not empty, register rest of the address 2
    if (!empty($_POST['addr2'])) {
        $addr2 = $_POST['addr2'];
        $postal2 = $_POST['postal2'];
        $country2 = $_POST['country2'];

        if (!preg_match('/^[0-9]+$/', $postal2)) {
            $errors['postal2'] = 'Postal code must be in numbers only!';
        }
    }


    // Checks if form is error free
    if (!array_filter($errors)) {
        $addr1 = mysqli_real_escape_string($conn, $addr1);
        $addr2 = mysqli_real_escape_string($conn, $addr2);
        $postal1 = mysqli_real_escape_string($conn, $postal1);
        $postal2 = mysqli_real_escape_string($conn, $postal2);
        $country1 = mysqli_real_escape_string($conn, $country1);
        $country2 = mysqli_real_escape_string($conn, $country2);

        // Insert into address table
        $sql = "INSERT INTO address(USERID, ADDRESS1, ADDRESS2, POSTALCD1, POSTALCD2, COUNTRY1, COUNTRY2)
        VALUES('$userId', '$addr1', '$addr2', '$postal1', '$postal2', '$country1', '$country2')";

        if (mysqli_query($conn, $sql)) {
            setcookie('LASTACTION', 'REGISTER', time() + (120), "/");
            echo "<script type='text/javascript'>window.top.location='/index.php';</script>";
        } else {
            echo 'Query Error: ' . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE HTML>
<html>
<section class="container grey-text">
    <h4 class="center">Shipping Address</h4>

    <form action="shipping_details.php" class="EditForm" method="POST">
        <label>Main Shipping Address: </label>
        <input type="text" name="addr1" value="<?php echo htmlspecialchars($addr1); ?>">
        <div class="red-text"><?php echo htmlspecialchars($errors['addr1']); ?></div>

        <label>Postal: </label>
        <input type="text" name="postal1" value="<?php echo htmlspecialchars($postal1); ?>">
        <div class="red-text"><?php echo htmlspecialchars($errors['postal1']); ?></div>

        <label>Country: </label>
        <select class="browser-default" name="country1">
            <option value="Singapore">Singapore</option>
            <option value="Malaysia">Malaysia</option>
        </select>

        <div class="divider"></div>

        <label>Secondary Shipping Address: </label>
        <input type="text" name="addr2" value="<?php echo htmlspecialchars($addr2); ?>">

        <label>Postal: </label>
        <input type="text" name="postal2" value="<?php echo htmlspecialchars($postal2); ?>">
        <div class="red-text"><?php echo htmlspecialchars($errors['postal2']); ?></div>

        <label>Country: </label>
        <select class="browser-default" name="country2">
            <option value="Singapore">Singapore</option>
            <option value="Malaysia">Malaysia</option>
        </select>

        <div class="center">
            <input type="submit" name="submit" value="Confirm" class="btn brand z-depth-0">
        </div>
    </form>
</section>


<?php include("../templates/footer.php"); ?>

</html>