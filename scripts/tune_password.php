<?php
include("../config/db_connect.php");
include('../templates/header.php');
$sql = "SELECT * FROM customer";
$result = mysqli_query($conn, $sql);
$userList = mysqli_fetch_all($result, MYSQLI_ASSOC);
$uid = '';

// Hash password for each user
foreach ($userList as $user) {
    $userid = mysqli_real_escape_string($conn, $user['USERID']);
    $password = mysqli_real_escape_string($conn, $user['PASSWORD']);
    $hashedpassword = mysqli_real_escape_string($conn, password_hash($password, PASSWORD_DEFAULT));

    $sql = "UPDATE customer SET PASSWORD='$hashedpassword' 
    WHERE USERID='$userid'";
    if (!mysqli_query($conn, $sql)) {
        echo 'Query Error: ' . mysqli_error($conn);
    }
}

// Generate unique uid for the customer
$unique = true;
do {
    $uid = uniqid('CUS', true);
    $sql = "SELECT * FROM CUSTOMER WHERE USERID = '$uid'";
    $result = mysqli_query($conn, $sql);
    $checkResult = mysqli_num_rows($result);
    if ($checkResult > 0) {
        $unique = false;
    }
} while (!$unique);

mysqli_free_result($result);
mysqli_close($conn);

?>

<!DOCTYPE HTML>
<html>


<?php include("templates/footer.php"); ?>

</html>