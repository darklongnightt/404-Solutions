<?php
include("../config/db_connect.php");
include("../storage_connect.php");
include("../templates/header.php");

// Access Control Check
if (substr($uid, 0, 3) != 'ADM') {
    echo "<script type='text/javascript'>window.top.location='/index.php';</script>";
}

$files = array();

if (isset($_SESSION['LASTACTION'])) {
    if ($_SESSION['LASTACTION'] == 'UPLOADGC') {
        echo "<script>M.toast({html: 'Successfully uploaded image(s) to product(s)!'});</script>";
    }

    $_SESSION['LASTACTION'] = 'NONE';
}

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

            // Resize image
            $pic_error = @image_resize($tmpFilePath, $tmpFilePath, 300, 300);

            // Upload to google cloud storage
            upload_object($bucketName, $fileName, $tmpFilePath);

            // Create url for the uploaded image
            $url = "https://storage.googleapis.com/" . $bucketName . "/" . $fileName;
            $url = mysqli_real_escape_string($conn, $url);

            // Update product image url in database
            $sql = "UPDATE product SET IMAGE='$url' WHERE PDTID='$pdtid';";
            if (mysqli_query($conn, $sql)) {
                $_SESSION['LASTACTION'] = 'UPLOADGC';
                echo "<script type='text/javascript'>window.top.location='upload_gc.php';</script>";
            } else {
                echo 'Query Error: ' . mysqli_error($conn);
            }
        }
    }
}
?>

<script>
    function triggerClick(e) {
        displayImage(e);
    }

    function displayImage(e) {
        if (e.files[0]) {
            var reader = new FileReader();
            var count = e.files.length;

            reader.onload = function(e) {
                document.querySelector('#preview').setAttribute('src', e.target.result);
                document.getElementById('fileCount').innerHTML = 'Total ' + count + ' files selected.';
            }
            reader.readAsDataURL(e.files[0]);
        }
    }
</script>

<!DOCTYPE HTML>
<html>
<div class="container">
    <h4 class="center">Batch Image Upload</h4>

    <div class="row">

        <div class="col m4 s8">
            <div class="card z-depth-0 small">
                <div class="card-content">
                    <h6 class="bold brand-text center">Before You Upload</h6>
                    <ol>
                        <li>To associate product with image, <u>rename image to product id</u></li>
                        <li>Each product can only be associated with one image</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="col m4 s8">
            <div class="card z-depth-0 small">
                <div class="card-content center">
                    <h6 class="brand-text bold">SuperData - Google Cloud Storage</h6>

                    <br>
                    <img src="../img/upload_cloud.png" class="method-icon" style="width: 150px; height: auto;">
                </div>
            </div>
        </div>

        <div class="col m4 s8">
            <div class="card z-depth-0 small">
                <div class="card-content">

                    <h6 class="bold brand-text">Extensions Supported</h6>
                    <ul>
                        <li style="list-style-type: initial; margin-left: 15px">bmp</li>
                        <li style="list-style-type: initial; margin-left: 15px">gif</li>
                        <li style="list-style-type: initial; margin-left: 15px">jpg</li>
                        <li style="list-style-type: initial; margin-left: 15px">jpeg</li>
                        <li style="list-style-type: initial; margin-left: 15px">png</li>
                    </ul>

                </div>
            </div>
        </div>

    </div>

    <div class="card z-depth-0 EditForm">
        <div class="card-content center">
            <form action="upload_gc.php" enctype="multipart/form-data" method="post">
                <h6 class="bold"> Upload Images </h6>
                <div class="center">
                    <label for="imageUpload"> <img src="/img/upload_placeholder1.png" id="preview" onclick="triggerClick()" style="width: 200px; margin: 20px; border-style: dotted; border-radius: 5px;"> </label>
                    <input type="file" name="uploaded_files[]" id="imageUpload" size="10000" multiple="multiple" onchange="displayImage(this)" style="display: none;">
                    <div id="fileCount"></div>
                    <br>
                </div>

                <input type="submit" value="Upload All" class="btn brand">
            </form>
        </div>
    </div>

</div>

<?php include("../templates/footer.php"); ?>

</html>