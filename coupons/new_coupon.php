<?php
include('../config/db_connect.php');
include('../templates/header.php');

// Access Control Check
if (substr($uid, 0, 3) != 'ADM') {
    echo "<script type='text/javascript'>window.top.location='/index.php';</script>";
}

$couponcode = $desc = $expiry = '';
$discount = 0;
$today = date('Y-m-d');
$errors = array('desc' => '', 'expiry' => '', 'discount' => '');

//Checks if button of name="submit" is clicked
if (isset($_POST['submit'])) {

    //Gets data from the POST request for error checking
    if (empty($_POST['desc'])) {
        $errors['desc'] = 'Coupon description is required!';
    } else {
        $desc = $_POST['desc'];
    }

    if (empty($_POST['expiry'])) {
        $errors['expiry'] = 'Coupon expiry is required!';
    } else {
        $expiry = $_POST['expiry'];
    }

    if (empty($_POST['discount'])) {
        $errors['discount'] = 'Coupon discount is required!';
    } else {
        $discount = $_POST['discount'];
    }

    // Checks if form is error free
    if (!array_filter($errors)) {
        // Formatting string for db security
        $desc = mysqli_real_escape_string($conn, $_POST['desc']);
        $expiry = mysqli_real_escape_string($conn, $_POST['expiry']);

        // Generate unique uid for the Coupon
        $unique = true;
        do {
            $couponcode = strtoupper(uniqid(substr($desc, 0, 3)));
            $sql = "SELECT * FROM coupon WHERE COUPONCODE = '$couponcode'";
            $result = mysqli_query($conn, $sql);
            $checkResult = mysqli_num_rows($result);
            if ($checkResult > 0) {
                $unique = false;
            }
        } while (!$unique);

        // Inserts data to db and redirects user to homepage
        $sql = "INSERT INTO coupon(COUPONCODE, DESCRIPTION, DISCOUNT, EXPIRY) 
        VALUES('$couponcode', '$desc', '$discount', '$expiry')";

        if (mysqli_query($conn, $sql)) {
            $_SESSION['LASTACTION'] = 'NEWCOUPON';
            echo "<script type='text/javascript'>window.top.location='/index.php';</script>";
        } else {
            echo 'Query Error: ' . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html>

<section class="container">
    <h4 class="center grey-text">New Coupon</h4>
    <form action="new_coupon.php" class="EditForm" method="POST">

        <label>Coupon Description: </label>
        <input type="text" name="desc" value="<?php echo htmlspecialchars($desc); ?>">
        <div class="red-text"><?php echo htmlspecialchars($errors['desc']); ?></div>

        <label>Discount: </label>
        <input type="number" name="discount" min="0" value="<?php echo htmlspecialchars($discount); ?>" step=".01">
        <div class="red-text"><?php echo htmlspecialchars($errors['discount']); ?></div>

        <label>Expiry Date: </label>
        <input type="date" name="expiry" min="<?php echo $today; ?>" max="" value="<?php echo $expiry ?>">
        <div class="red-text"><?php echo htmlspecialchars($errors['expiry']); ?></div>

        <div class="center">
            <input type="submit" name="submit" class="btn brand z-depth-0">
        </div>
    </form>
</section>

<?php include("../templates/footer.php"); ?>

</html>