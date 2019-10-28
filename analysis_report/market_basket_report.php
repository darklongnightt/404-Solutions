<?php
include("../config/db_connect.php");
include("../templates/header.php");

// Pagination for all results
$currDir = "market_basket_report.php";
$query = "SELECT * FROM product_recommendation";
include('../templates/pagination_query.php');

// Fetch product recommendations
$sql = "SELECT * FROM product_recommendation LIMIT $startingLimit, $resultsPerPage";
$result = mysqli_query($conn, $sql);
$products = mysqli_fetch_all($result, MYSQLI_ASSOC);

$sql = "SELECT COUNT(*) FROM product";
$result = mysqli_query($conn, $sql);
$product_count = mysqli_fetch_assoc($result)['COUNT(*)'];

$sql = "SELECT COUNT(*) FROM orders";
$result = mysqli_query($conn, $sql);
$orders_count = mysqli_fetch_assoc($result)['COUNT(*)'];
?>

<style>
    .table-wrapper {
        overflow-x: scroll;
        width: 100%;
    }

    td {
        margin: 20px;
    }

    td img {
        max-width: 100px;
        height: 100px;
        text-align: center;
    }

    .method-icon {
        width: 200px;
        height: auto;
    }

    .longer {
        width: 200px;
    }
</style>

<!DOCTYPE HTML>
<html>
<h4 class="grey-text center">Market Basket Analysis Report</h4>
<div class="container">
    <div class="row">
        <div class="col m4 s8">
            <div class="card z-depth-0 small">
                <div class="card-content center">
                    <h4 class="red-text bold center">
                        <?php echo $product_count; ?>
                    </h4>
                    <div class="bold center"> Products </div>
                    <br>
                    <div class="divider"></div>
                    <h4 class="red-text bold center">
                        <?php echo $orders_count; ?>
                    </h4>
                    <div class="bold center"> Transactions </div>
                </div>
            </div>
        </div>

        <div class="col m4 s8">
            <div class="card z-depth-0 small">
                <div class="card-content center">
                    <h6 class="brand-text bold">Method: Apriori Algorithm</h6>

                    <h6 class="brand-text bold">Output: Frequent Itemsets</h6>

                    <br>
                    <img src="../img/arules.png" class="method-icon">
                </div>
            </div>
        </div>

        <div class="col m4 s8">
            <div class="card z-depth-0 small">
                <div class="card-content">

                    <h6 class="bold brand-text">Algorithm Inputs</h6>
                    <ul>
                        <li style="list-style-type: initial; margin-left: 15px">Transactions Dataset</li>
                        <li style="list-style-type: initial; margin-left: 15px">Minimum Support</li>
                        <li style="list-style-type: initial; margin-left: 15px">Minimum Confidence</li>

                    </ul>

                </div>
            </div>
        </div>
    </div>
    <?php if ($products) : ?>
        <div class="table-wrapper white">
            <table class="responsive-table tabble-wrapper centered">
                <thead>
                    <tr>
                        <?php
                            echo '<th>Products Popular With</th>';

                            for ($i = 1; $i <= 12; $i++) {
                                echo '<th>' . $i . '</th>';
                            };
                            ?>
                    </tr>
                </thead>

                <tbody>
                    <?php
                        for ($i = 0; $i < sizeof($products); $i++) {

                            $id = $products[$i]['PDTID'];
                            $sql = "SELECT * FROM product WHERE PDTID = '$id'";
                            $result = mysqli_query($conn, $sql);
                            $product = mysqli_fetch_assoc($result);


                            echo '<tr>';
                            echo '<td class="center longer"><a href="/products/product_details.php?id=' . $product['PDTID'] . '"><img src="' . $product['IMAGE'] . '"></a>';
                            echo '<div class="bold">' . $product['PDTNAME'] . '</div>
                            </td>';

                            $recommendations = explode(' ', $products[$i]['RECOMMENDATIONS']);
                            array_splice($recommendations, 0, 1);

                            foreach ($recommendations as $reco) {

                                $sql = "SELECT * FROM product WHERE PDTID = '$reco'";
                                $result = mysqli_query($conn, $sql);
                                $recommended_product = mysqli_fetch_assoc($result);
                                echo  '<td class="center"><a href="/products/product_details.php?id=' . $recommended_product['PDTID'] . '"><img src="' . $recommended_product['IMAGE'] . '"></a></td>';
                            }
                            echo '</tr>';
                        }
                        ?>
                </tbody>
            </table>
        </div>
        <?php include("../templates/pagination_output.php"); ?>

    <?php else : ?>
        <div>No Market Basket Report Generated!</div>
    <?php endif ?>

</div>

<?php
// Free memory of result and close connection
mysqli_free_result($result);
mysqli_close($conn);
include("../templates/footer.php");
?>

</html>