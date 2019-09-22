<?php
include("../config/db_connect.php");
include('../templates/header.php');
$sql = "SELECT * FROM customer";
$result = mysqli_query($conn, $sql);
$userList = mysqli_fetch_all($result, MYSQLI_ASSOC);
$uid = '';

// Hash password for each user
foreach ($userList as $user) {
    $email = mysqli_real_escape_string($conn, $user['EMAIL']);
    $userid = mysqli_real_escape_string($conn, $user['USERID']);
    $password = mysqli_real_escape_string($conn, $user['PASSWORD']);

    // Generate random salt value and hash password using sha256
    $salt = uniqid();
    $password .= $salt;
    $hashedpassword = mysqli_real_escape_string($conn, hash('sha256', $password));

    // Update db
    $sql = "UPDATE customer SET PASSWORD='$hashedpassword' 
    WHERE USERID='$userid'";
    if (!mysqli_query($conn, $sql)) {
        echo 'Query Error: ' . mysqli_error($conn);
    } else {
        $sql = "INSERT INTO salt(SALT, EMAIL) VALUES('$salt', '$email')";
        if (!mysqli_query($conn, $sql)) {
            echo 'Query Error: ' . mysqli_error($conn);
        }
    }
}

mysqli_free_result($result);
mysqli_close($conn);

?>

<!DOCTYPE HTML>
<html>

<h4 class="center grey-text">Successfully hashed and salted all password in the database!</h4>

<?php include("../templates/footer.php"); ?>

</html>