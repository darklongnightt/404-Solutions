<?php
include("../templates/header.php");

?>

<!DOCTYPE HTML>
<html>
<section class="container center">
    <h4 class="center grey-text">Manage Promotions</h4>
    <div class="row">
        <br>
        <div class="col m4 s8 offset-m2">
            <a href="new_promotion.php">
                <div class="card small">
                    <div class="card-content">
                        <h4 class="brand-text bold">New Promotion</h4>
                        <i class="material-icons black-text">add</i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col m4 s8">
            <a href="view_promotions.php">
                <div class="card small">
                    <div class="card-content">
                        <h4 class="brand-text bold">View Existing Promotions</h4>
                        <i class="material-icons black-text">pageview</i>
                    </div>
                </div>
            </a>
        </div>
    </div>

</section>

<?php include("../templates/footer.php"); ?>

</html>