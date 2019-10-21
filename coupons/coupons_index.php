<?php
include("../templates/header.php");

?>

<!DOCTYPE HTML>
<html>
<section class="container center">
    <h4 class="center grey-text">Manage Coupons</h4>
    <div class="row">
        <br>
        <div class="col m4 s8 offset-m2">
            <a href="new_coupon.php">
                <div class="card small">
                    <div class="card-content">
                        <h4 class="brand-text bold">New Coupon</h4>
                        <i class="material-icons black-text">add</i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col m4 s8">
            <a href="view_coupons.php">
                <div class="card small">
                    <div class="card-content">
                        <h4 class="brand-text bold">View Existing Coupons</h4>
                        <i class="material-icons black-text">pageview</i>
                    </div>
                </div>
            </a>
        </div>
    </div>

</section>

<?php include("../templates/footer.php"); ?>

</html>