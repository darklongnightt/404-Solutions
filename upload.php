<?php
include("config/db_connect.php");
include("config/storage_connect.php");
include("templates/header.php");

// Initialize message variable
$msg = "";

// If upload button is clicked
if (isset($_POST['upload'])) {
    // Get image name and description
    $image = mysqli_real_escape_string($storage_conn, $_FILES['image']['name']);
    $description = mysqli_real_escape_string($storage_conn, $_POST['description']);

    // Image file directory
    $target = "images/" . basename($image);

    // Execute query
    $sql = "INSERT INTO image (IMAGE, DESCRIPTION) VALUES ('$image', '$description')";
    mysqli_query($storage_conn, $sql);

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        $msg = "Image uploaded successfully";
    } else {
        $msg = "Failed to upload image";
    }
}

$result = mysqli_query($storage_conn, "SELECT * FROM image");
?>

<!DOCTYPE HTML>
<html>

<div class="container">
    <h4>Upload File</h4>
    <?php
    while ($row = mysqli_fetch_array($result)) {
        echo "<div id='img_div'>";
        echo "<img src='images/" . $row['image'] . "' >";
        echo "<p>" . $row['image_text'] . "</p>";
        echo "</div>";
    }
    ?>
    <form method="POST" action="upload.php" enctype="multipart/form-data">
        <input type="hidden" name="size" value="1000000">

        <div>
            <input type="file" name="image">
        </div>

        <div>
            <textarea id="text" cols="40" rows="4" name="image_text" placeholder="Say something about this image..."></textarea>
        </div>

        <input type="submit" name="submit" value="Submit" class="btn brand z-depth-0">

        <div> <?php echo $msg; ?></div>
    </form>
</div>
<?php include("templates/footer.php"); ?>

</html>