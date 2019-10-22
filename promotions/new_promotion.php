<?php
include('../config/db_connect.php');
include('../templates/header.php');
include('../storage_connect.php');

$promotioncode = $desc = $expiry = $category = '';
$discount = 0;
$today = date('Y-m-d');
$errors = array('desc' => '', 'expiry' => '', 'discount' => '', 'image' => '');

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

    // Check for image selected
    if ($_FILES["fileToUpload"]["size"] !== 0) {
		// Checks if file is an image
		$imageCheck = getimagesize($_FILES["fileToUpload"]["tmp_name"]);

		if (!$imageCheck) {
			$errors['image'] = "Invalid image file selected!";
		} else {
			// Get the file name and temp file path
			$fileName = $_FILES['fileToUpload']['name'];
			$tmpFilePath = $_FILES['fileToUpload']['tmp_name'];
		}
	} else {
		$errors['image'] = "Product image is required!";
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

        // Resizing image to 300 x 300
		$pic_type = strtolower(strrchr($fileName, "."));
		$pic_name = "../temp/temp$pic_type";
		move_uploaded_file($tmpFilePath, $pic_name);
		if (true !== ($pic_error = @image_resize($pic_name, $tmpFilePath, 800, 300))) {
			$tmpFilePath = $pic_name;
        }

		// Upload to google cloud storage
		upload_object($bucketName, $fileName, $tmpFilePath);

		// Create url for the uploaded image
		$url = "https://storage.cloud.google.com/" . $bucketName . "/" . $fileName . "?cloudshell=false";
		$url = mysqli_real_escape_string($conn, $url);

        // Inserts data to db and redirects user to homepage
        $sql = "INSERT INTO promotion(PROMOCODE, CATEGORY, DESCRIPTION, DISCOUNT, DATEFROM, DATETO, IMAGE) 
        VALUES('$promotioncode', '$category', '$desc', '$discount', '$today', '$expiry', '$url')";

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

<script>
    function triggerClick(e) {
        displayImage(e);
    }

    function displayImage(e) {
        if (e.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.querySelector('#preview').setAttribute('src', e.target.result);
            }
            reader.readAsDataURL(e.files[0]);
        }
    }
</script>

<section class="container grey-text">
    <h4 class="center">New Promotion</h4>
    <form action="new_promotion.php" enctype="multipart/form-data" class="EditForm" method="POST">

        <div class="center">
            <label for="imageUpload"> <img src="/img/upload_placeholder1.png" id="preview" onclick="triggerClick()" style="width: 200px; margin: 20px; border-style: dotted; border-radius: 5px;"> </label>
            <input type="file" name="fileToUpload" id="imageUpload" onchange="displayImage(this)" style="display: none;">
        </div>
        <div class="red-text center"><?php echo htmlspecialchars($errors['image']); ?></div>
        <br>

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