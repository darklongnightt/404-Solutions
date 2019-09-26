<?php
// Get number of rows from a sepcified query to derive total pages
$result = mysqli_query($conn, $query);
$resultsPerPage = 39;
$totalResults = mysqli_num_rows($result);
$totalPages = ceil($totalResults / $resultsPerPage);

// Determine which page visitor is on
if (!isset($_GET['page'])) {
	$currPage = 1;
} else {
	$currPage = $_GET['page'];
}
$startingLimit = ($currPage - 1) * $resultsPerPage;
?>