<?php
include("../templates/header.php");

?>

<!DOCTYPE HTML>
<html>
<section class="container center">
    <h4 class="center grey-text">Manage Products Inventory</h4>
    <div class="row">
        <br>
        <div class="col m4 s8 offset-m2">
            <a href="new_product.php">
                <div class="card small">
                    <div class="card-content">
                        <h4 class="brand-text bold">New Product</h4>
                        <i class="material-icons black-text">add</i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col m4 s8">
            <a href="inventory_management.php">
                <div class="card small">
                    <div class="card-content">
                        <h4 class="brand-text bold">Existing Products</h4>
                        <i class="material-icons black-text">pageview</i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col m4 s8 offset-m2">
            <a href="product_management.php">
                <div class="card small">
                    <div class="card-content">
                        <h4 class="brand-text bold">Manage Products</h4>
                        <i class="material-icons black-text">pageview</i>
                    </div>
                </div>
            </a>
        </div>
    </div>

</section>

<?php include("../templates/footer.php"); ?>

</html>