<?php
include("config/db_connect.php");
include("templates/header.php");
$link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ?
    "https" : "http") . "://" . $_SERVER['HTTP_HOST'] .
    $_SERVER['REQUEST_URI'];

$linkCat = (strpos($link, '?') == TRUE) ? '&' : '?';
echo $link . $linkCat;

?>

<head>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</head>

<script>
    $(document).ready(function() {
        $('.collapsible').collapsible();
    });
</script>

<!DOCTYPE HTML>
<html>

<ul class="collapsible">
    <li>
        <div class="collapsible-header"><i class="material-icons">filter_drama</i>First</div>
        <div class="collapsible-body"><span>Lorem ipsum dolor sit amet.</span></div>
    </li>
    <li>
        <div class="collapsible-header"><i class="material-icons">place</i>Second</div>
        <div class="collapsible-body"><span>Lorem ipsum dolor sit amet.</span></div>
    </li>
    <li>
        <div class="collapsible-header"><i class="material-icons">whatshot</i>Third</div>
        <div class="collapsible-body"><span>Lorem ipsum dolor sit amet.</span></div>
    </li>
</ul>

<?php include("templates/footer.php"); ?>

</html>