<?php
include("../config/db_connect.php");
include("../templates/header.php");

// Fetch all comments
$goodComments = array();
$fh = fopen('goodcomments.txt', 'r');
while ($line = fgets($fh)) {
    array_push($goodComments, $line);
}
fclose($fh);

$badComments = array();
$fh = fopen('badcomments.txt', 'r');
while ($line = fgets($fh)) {
    array_push($badComments, $line);
}
fclose($fh);

// Fetch all customers
$sql = "SELECT USERID FROM customer";
$result = mysqli_query($conn, $sql);
$customers = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Fetch all customers
$sql = "SELECT PDTID, CATEGORY FROM product";
$result = mysqli_query($conn, $sql);
$products = mysqli_fetch_all($result, MYSQLI_ASSOC);


foreach ($products as $product) {
    // Loop through all product id
    $pid = $product['PDTID'];
    $category = mysqli_real_escape_string($conn, $product['CATEGORY']);

    // Generate n number of reviews
    $reviewCount = random_int(2, 5);
    for ($i = 0; $i < $reviewCount; $i++) {
        // Randomly select a customer id for review
        $custIndex = random_int(0, sizeof($customers) - 1);
        $customerId = $customers[$custIndex]['USERID'];

        // Randomly generate an rating type bad, average, good
        $type = random_int(1, 100);
        $selectedComment = '';
        $p = $s = $d = 0;

        // 2 categories categories with more bad ratings 
        if ($category == 'FROZEN FOOD' || $category ==  'FRESH FOOD') {
            if ($type >= 1 && $type <= 50) {
                // Generate bad rating (50%)
                $p = random_int(1, 3);
                $s = random_int(1, 3);
                $d = random_int(1, 3);

                // Select random bad comment
                $selectComment = random_int(0, sizeof($badComments) - 1);
                $selectedComment = $badComments[$selectComment];
            } else if ($type > 70 && $type < 90) {
                // Generate average rating (30%)
                $p = random_int(2, 4);
                $s = random_int(3, 4);
                $d = random_int(3, 4);

                // Select random good comment
                $selectComment = random_int(0, sizeof($goodComments) - 1);
                $selectedComment = $goodComments[$selectComment];
            } else {
                // Generate good rating (20%)
                $p = random_int(4, 5);
                $s = random_int(4, 5);
                $d = random_int(4, 5);

                // Select random good comment
                $selectComment = random_int(0, sizeof($goodComments) - 1);
                $selectedComment = $goodComments[$selectComment];
            }
        } else if ($category == 'TIDBITS' || $category ==  'CONCESSIONAIRES' || $category == 'HOUSEHOLD CONSUMMABLES') {

            // 3 Categories with extremely good ratings
            if ($type >= 1 && $type <= 80) {
                // Generate good rating (80%)
                $p = random_int(4, 5);
                $s = random_int(4, 5);
                $d = random_int(4, 5);

                // Select random good comment
                $selectComment = random_int(0, sizeof($goodComments) - 1);
                $selectedComment = $goodComments[$selectComment];
            } else {
                // Generate average rating (30%)
                $p = random_int(2, 4);
                $s = random_int(3, 4);
                $d = random_int(3, 4);

                // Select random good comment
                $selectComment = random_int(0, sizeof($goodComments) - 1);
                $selectedComment = $goodComments[$selectComment];
            }
        } else if ($category == 'TOILETRIES') {
            // Generate bad rating
            $p = random_int(1, 2);
            $s = random_int(1, 4);
            $d = random_int(1, 1);

            // Select random good comment
            $selectComment = random_int(0, sizeof($badComments) - 1);
            $selectedComment = $badComments[$selectComment];
        } else {

            if ($type >= 1 && $type <= 70) {
                // Generate good rating (60%)
                $p = random_int(4, 5);
                $s = random_int(4, 5);
                $d = random_int(4, 5);

                // Select random good comment
                $selectComment = random_int(0, sizeof($goodComments) - 1);
                $selectedComment = $goodComments[$selectComment];
            } else if ($type > 70 && $type < 90) {
                // Generate average rating (30%)
                $p = random_int(2, 4);
                $s = random_int(3, 4);
                $d = random_int(3, 4);

                // Select random good comment
                $selectComment = random_int(0, sizeof($goodComments) - 1);
                $selectedComment = $goodComments[$selectComment];
            } else {
                // Generate bad rating (10%)
                $p = random_int(1, 3);
                $s = random_int(1, 3);
                $d = random_int(1, 3);

                // Select random bad comment
                $selectComment = random_int(0, sizeof($badComments) - 1);
                $selectedComment = $badComments[$selectComment];
            }
        }

        $reviewid = uniqid('RNR');
        $selectedComment = mysqli_real_escape_string($conn, $selectedComment);

        $sql = "INSERT INTO review(REVIEWID, PDTID, USERID, PRATING, SRATING, DRATING, COMMENT, CATEGORY) 
VALUES ('$reviewid', '$pid', '$customerId', '$p', '$s', '$d', '$selectedComment', '$category')";
        if (!mysqli_query($conn, $sql)) {
            echo "Query Error: " . mysqli_error($conn);
            exit();
        }
    }
}


mysqli_free_result($result);
mysqli_close($conn);
?>

<!DOCTYPE HTML>
<html>




<?php include("../templates/footer.php"); ?>

</html>