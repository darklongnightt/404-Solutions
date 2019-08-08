<?php
    $conn = mysqli_connect('localhost', 'xavier', 'pw1234', 'super_data');
    if (!$conn) {
        echo 'Connection Error: ' . mysqli_connect_error();
    }
?>