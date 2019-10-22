<?php
include('../config/db_connect.php');
include('../templates/header.php');

$promotioncode = $desc = $expiry = $category = '';
$discount = 0;
$today = date('Y-m-d');
$errors = array('desc' => '', 'expiry' => '', 'discount' => '');

// Get all distinct categories
$sql = "SELECT DISTINCT CATEGORY FROM product";
$result = mysqli_query($conn, $sql);
$categories = mysqli_fetch_all($result, MYSQLI_ASSOC);

//Checks if button of name="submit" is clicked
if (isset($_POST['submit'])) {

    //Gets data from the POST request for error checking
    if (empty($_POST['desc'])) {
        $errors['desc'] = 'Promotion description is required!';
    } else {
        $desc = $_POST['desc'];
    }

    if (empty($_POST['expiry'])) {
        $errors['expiry'] = 'Promotion expiry is required!';
    } else {
        $expiry = $_POST['expiry'];
    }

    if (empty($_POST['discount'])) {
        $errors['discount'] = 'Promotion discount is required!';
    } else {
        $discount = $_POST['discount'];
    }

    // Checks if form is error free
    if (!array_filter($errors)) {
        // Formatting string for db security
        $desc = mysqli_real_escape_string($conn, $_POST['desc']);
        $expiry = mysqli_real_escape_string($conn, $_POST['expiry']);
        $category = mysqli_real_escape_string($conn, $_POST['category']);

        // Generate unique uid for the Promotion
        $unique = true;
        do {
            $promotioncode = strtoupper(uniqid(substr($desc, 0, 3)));
            $sql = "SELECT * FROM promotion WHERE PROMOCODE = '$promotioncode'";
            $result = mysqli_query($conn, $sql);
            $checkResult = mysqli_num_rows($result);
            if ($checkResult > 0) {
                $unique = false;
            }
        } while (!$unique);

        // Inserts data to db and redirects user to homepage
        $sql = "INSERT INTO promotion(PROMOCODE, CATEGORY, DESCRIPTION, DISCOUNT, DATEFROM, DATETO) 
        VALUES('$promotioncode', '$category', '$desc', '$discount', '$today', '$expiry')";

        if (mysqli_query($conn, $sql)) {
            header('Location: promotions_index.php');
        } else {
            echo 'Query Error: ' . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html>

<section class="container grey-text">
    <h4 class="center">New Promotion</h4>
    <form action="new_promotion.php" class="EditForm" method="POST">

        <label>Promotion Description: </label>
        <input type="text" name="desc" value="<?php echo htmlspecialchars($desc); ?>">
        <div class="red-text"><?php echo htmlspecialchars($errors['desc']); ?></div>

        <label>Applied Product Category: </label>
        <select class="browser-default" name="category">
            <option value="ALL">ALL</option>

            <?php foreach ($categories as $category) { ?>
                <option value="<?php echo htmlspecialchars($category['CATEGORY']); ?>"> <?php echo htmlspecialchars($category['CATEGORY']); ?> </option>
            <?php } ?>
        </select>
        <br>

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