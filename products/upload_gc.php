<?php
include("../config/db_connect.php");
include("../storage_connect.php");
include("../templates/header.php");

// If upload button is clicked
if ($_FILES) {
    if ($_FILES["uploaded_files"]["error"][0] > 0) {
        echo "Error: " . $_FILES["uploaded_files"]["error"] . "<br />";
    } else {
        for ($i = 0; $i < count($_FILES['uploaded_files']['name']); $i++) {

            // Get the file name and temp file path
            $fileName = $_FILES['uploaded_files']['name'][$i];
            $tmpFilePath = $_FILES['uploaded_files']['tmp_name'][$i];
            $pdtid = explode('.', $fileName)[0];

            if ($_FILES["uploaded_files"]["error"][$i] > 0) {
                echo "Error: " . $_FILES["uploaded_files"]["error"] . "<br />";
            }

            // Resizing image to 300 x 300
            $pic_type = strtolower(strrchr($fileName, "."));
            $pic_name = "../temp/temp$pic_type";
            move_uploaded_file($tmpFilePath, $pic_name);
            if (true !== ($pic_error = @image_resize($pic_name, $tmpFilePath, 300, 300))) {
                $tmpFilePath = $pic_name;
            }

            // Upload to google cloud storage
            upload_object($bucketName, $fileName, $tmpFilePath);

            // Create url for the uploaded image
            $url = "https://storage.cloud.google.com/" . $bucketName . "/" . $fileName . "?cloudshell=false";
            $url = mysqli_real_escape_string($conn, $url);

            // Update product image url in database
            $sql = "UPDATE product SET IMAGE='$url' WHERE PDTID='$pdtid';";
            if (mysqli_query($conn, $sql)) {
                header('Location: upload_gc.php');
            } else {
                echo 'Query Error: ' . mysqli_error($conn);
            }
        }
    }
}
?>

        <!DOCTYPE HTML>
        <html>
        <div class="container">
            <h4>Upload Files</h4>

            <form action="upload_gc.php" enctype="multipart/form-data" method="post">
                Files to upload: <br>
                <input type="file" name="uploaded_files[]" size="10000" multiple="multiple">
                <input type="submit" value="Upload All">
            </form>
        </div>

        <?php include("../templates/footer.php"); ?>

        </html>