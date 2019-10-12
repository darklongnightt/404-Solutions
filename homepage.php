<?php
include("config/db_connect.php");
include("templates/header.php");

$name = 'Guest';

if (isset($_SESSION['U_FIRSTNAME'])) {
    $name = $_SESSION['U_FIRSTNAME'] . ' ' . $_SESSION['U_LASTNAME'];
}

?>

<head>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</head>

<!DOCTYPE HTML>
<html>
<script>
    $(document).ready(function() {
        $('.slider').slider();
    });
</script>

<div class="container">
    <br>
    <div class="row">
        <div class="col s24 m12">
            <div class="slider">
                <ul class="slides">
                    <li>
                        <img src="/img/banner1.jpg">
                        <div class="caption center-align">
                            <h3>This is our big Tagline!</h3>
                            <h5 class="light grey-text text-lighten-3">Here's our small slogan.</h5>
                        </div>
                    </li>
                    <li>
                        <img src="/img/banner2.jpg">
                        <div class="caption left-align">
                            <h3>Left Aligned Caption</h3>
                            <h5 class="light grey-text text-lighten-3">Here's our small slogan.</h5>
                        </div>
                    </li>
                    <li>
                        <img src="/img/banner3.jpg">
                        <div class="caption right-align">
                            <h3>Right Aligned Caption</h3>
                            <h5 class="light grey-text text-lighten-3">Here's our small slogan.</h5>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col s16 m8">
            <div class="card z-depth-0 medium">
                <div class="card-content center">
                    <h6 class="brand-text bold">Top Selling</h6>
                </div>
                <div class="bold center">display some products</div>
            </div>
        </div>

        <div class="col s8 m4">
            <div class="card z-depth-0 medium">
                <div class="card-content center">
                    <h6 class="brand-text bold">Welcome, <?php echo $name . '!' ?> </h6>
                </div>
                <div class="bold center">Recently Viewed</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col s24 m12">
            <div class="card z-depth-0 medium">
                <div class="card-content center">
                    <h6 class="brand-text bold">Recommended For You</h6>
                </div>
            </div>
        </div>
    </div>

</div>
<?php include("templates/footer.php"); ?>

</html>