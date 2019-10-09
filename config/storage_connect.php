<?php
    $storage_conn = mysqli_connect('localhost', 'root', '', 'image_storage');
    if (!$storage_conn) {
        echo 'Connection Error: ' . mysqli_connect_error();
    }
?>