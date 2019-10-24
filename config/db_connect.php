<?php
    $conn = mysqli_connect('localhost', 'root', '', 'test_super_data');
    if (!$conn) {
        echo 'Connection Error: ' . mysqli_connect_error();
    }
?>