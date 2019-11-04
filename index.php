<?php
include('config/db_connect.php');
include('templates/header.php');

$name = 'Guest';
$now = time();

// Add an item to cart
function addCart($conn, $id, $qty)
{
    $uid = $GLOBALS['uid'];
    if ($uid) {
        $uid = mysqli_real_escape_string($conn, $uid);
        $id = mysqli_real_escape_string($conn, $id);

        // Check that cart item exists 
        $sql = "SELECT * FROM cart WHERE PDTID='$id' AND USERID='$uid'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            // Increment product qty by 1
            $sql = "UPDATE cart SET CARTQTY=CARTQTY+'$qty' WHERE PDTID='$id' AND USERID='$uid'";
        } else {
            // Add to db cart
            $sql = "INSERT INTO cart(PDTID, USERID, CARTQTY) VALUES('$id', '$uid', '$qty')";
        }

        if (mysqli_query($conn, $sql)) {
            echo "<script>M.toast({html: 'Successfully added to cart!'});</script>";
        } else {
            echo 'Query Error: ' . mysqli_error($conn);
        }
    }
}

// Checks if search button is pressed
if (isset($_POST['submit'])) {
    $search = $_POST['search'];
    $link = '/products/search.php?Filter=all&sort=default&priceRange=%240.6+-+%2417.91&check=1&searchItem=' . $search . '&submit=';
    echo "<script type='text/javascript'>window.top.location='$link';</script>";
}

// Checks if recommended cart is clicked
if (isset($_GET['cart'])) {
    addCart($conn, $_GET['cart'], 1);
}

// Checks if user is logged in
if (isset($_SESSION['U_UID'])) {
    $name = $_SESSION['U_FIRSTNAME'] . ' ' . $_SESSION['U_LASTNAME'];
    $cluster = $_SESSION['U_CLUSTER'];

    if ($cluster == 0) {
        $sql = "SELECT MAX(CLUSTER) FROM customer";
        $result = mysqli_query($conn, $sql);
        $max = mysqli_fetch_assoc($result)['MAX(CLUSTER)'];
        $cluster = random_int(1, $max);
    }
} else {
    $sql = "SELECT MAX(CLUSTER) FROM customer";
    $result = mysqli_query($conn, $sql);
    $max = mysqli_fetch_assoc($result)['MAX(CLUSTER)'];
    $cluster = random_int(1, $max);
}

// Get all promotions and banners
$sql = "SELECT * FROM promotion ORDER BY DATETO";
$result = mysqli_query($conn, $sql);
$promotions = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Get all recent views for this user
$uid = mysqli_real_escape_string($conn, $uid);
$sql = "SELECT * FROM recent_views JOIN product ON recent_views.PDTID = product.PDTID
WHERE USERID='$uid' ORDER BY VIEWED_AT DESC";
$result = mysqli_query($conn, $sql);
$recent_views = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Get all cluster recommendations for this user
$sql = "SELECT * FROM cluster_recommendation JOIN product ON cluster_recommendation.PDTID = product.PDTID 
WHERE CLUSTER='$cluster' ORDER BY FREQUENCY DESC";
$result = mysqli_query($conn, $sql);
$cluster_recommendations = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Get top 12 most popular products from database
$sql = "SELECT * FROM top_products ORDER BY FREQUENCY DESC";
$result = mysqli_query($conn, $sql);
$top_products = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Select random items from each category
$sql = "SELECT DISTINCT CATEGORY FROM product";
$result = mysqli_query($conn, $sql);
$categories = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Render toast popups
if (isset($_COOKIE['LASTACTION']) && substr($uid, 0, 3) != 'ADM') {

    switch ($_COOKIE['LASTACTION']) {
        case 'LOGOUT':
            echo "<script>M.toast({html: 'You are logged out!'});</script>";
            break;
        case 'REGISTER':
            echo "<script>M.toast({html: 'You are successfully registered!'});</script>";
            break;
    }

    setcookie('LASTACTION', 'NONE', time() + (120), "/");
}

if (isset($_SESSION['LASTACTION'])) {
    switch ($_SESSION['LASTACTION']) {
        case 'LOGIN':
            echo "<script>M.toast({html: 'You are successfully logged in!'});</script>";
            break;

        case 'CHANGEPW':
            echo "<script>M.toast({html: 'Successfully updated password!'});</script>";
            break;

        case 'CREATESTAFF':
            echo "<script>M.toast({html: 'Successfully created staff account!'});</script>";
            break;

        case 'NEWPDT':
            echo "<script>M.toast({html: 'Successfully created new product!'});</script>";
            break;

        case 'DELETEPDT':
            echo "<script>M.toast({html: 'Successfully deleted product!'});</script>";
            break;

        case 'NEWPROMO':
            echo "<script>M.toast({html: 'Successfully created new promotion!'});</script>";
            break;

        case 'NEWCOUPON':
            echo "<script>M.toast({html: 'Successfully created new coupon!'});</script>";
            break;
    }

    $_SESSION['LASTACTION'] = "NONE";
}

// Free memory of result and close connection
mysqli_free_result($result);
mysqli_close($conn);
?>

        <head>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
        </head>

        <script>
            $(document).ready(function() {
                $('.slider').slider({
                    height: 230
                });


                $('input.autocomplete').autocomplete({
                    data: {
                        "BAZAAR": null,
                        "BEVERAGE": null,
                        "CONCESSIONAIRES": null,
                        "HEALTH CARE": null,
                        "FRESH FOOD": null,
                        "FROZEN FOOD": null,
                        "GROCERIES": null,
                        "HOUSEHOLD CONSUMMABLES": null,
                        "COSMETICS": null,
                        "TEXTILE": null,
                        "TIDBITS": null,
                        "TOILETRIES": null
                    }
                });
            });
        </script>

        <!DOCTYPE HTML>
        <html>


        <div class="container">

            <div class="row" style="margin-top: 15px;">
                <div class="col s12 m12">
                    <div class="slider">
                        <ul class="slides">

                            <?php foreach ($promotions as $promo) {
                                $expiry = strtotime($promo['DATETO']);
                                $diffDay = round(($expiry - $now) / (60 * 60 * 24), 0);
                                ?>
                                <li>
                                    <img src="<?php echo $promo['IMAGE']; ?>">

                                    <div class="caption right-align brand-text">
                                        <h4 class="bold"><?php echo $promo['DISCOUNT'] . '% OFF ' . $promo['CATEGORY']; ?></h4>
                                        <h6 class="bold"><?php echo $diffDay . ' DAYS LEFT' ?></h6>
                                    </div>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col s12 m8">
                    <div class="card z-depth-0">
                        <div class="card-content">

                            <h6 class="brand-text bold center">Most Popular Products</h6>

                            <div class="row">
                                <?php foreach ($top_products as $product) { ?>
                                    <div class="col s4 m2 center">

                                        <a href="/products/product_details.php?id=<?php echo $product['PDTID']; ?>">
                                            <img src="<?php if ($product['IMAGE']) {
                                                                echo $product['IMAGE'];
                                                            } else {
                                                                echo 'img/product_icon.svg';
                                                            } ?>" class="top-icon">

                                            <div class="white-text discount-label">
                                                <?php echo '$' . number_format(htmlspecialchars($product['PDTPRICE']) / 100 * htmlspecialchars(100 - $product['PDTDISCOUNT']), 2, '.', ''); ?>
                                            </div>
                                        </a>

                                    </div>

                                <?php } ?>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="col s12 m4">
                    <div class="card z-depth-0" style="min-height: 356px;">
                        <div class="card-content center">
                            <a href="/profile.php">
                                <h6 class="white-text bold welcome-label z-depth-1"><i class="fa fa-user-o" aria-hidden="true" style="margin-right: 10px;"></i>Welcome, <?php echo $name . '!' ?> </h6>
                            </a>
                            <div class="bold center">Recently Viewed</div>
                            <div class="row">
                                <?php foreach ($recent_views as $product) { ?>
                                    <div class="col m4 s4 center">
                                        <a href="/products/product_details.php?id=<?php echo $product['PDTID']; ?>">
                                            <span class="img-container">
                                                <img src="<?php if ($product['IMAGE']) {
                                                                    echo $product['IMAGE'];
                                                                } else {
                                                                    echo 'img/product_icon.svg';
                                                                } ?>" class="recent-icon">
                                            </span>
                                        </a>
                                    </div>
                                <?php } ?>

                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="row " style="margin-bottom: 0px;">
                <form action="index.php" method="POST" style="margin-bottom: 0%;">

                    <div class="input-field search-bar" style="border-radius: 25px; border-style: solid; color: grey; border-width: thin; background: white;">
                        <input type="text" name="search" placeholder="Search Products" style="margin-left: 15px; width: 92%;">
                        <button type="submit" name="submit" class="btn white z-depth-0 hide-on-med-and-down">
                            <i class="material-icons prefix black-text">search</i>
                        </button>
                    </div>
                </form>
            </div>

            <div class="row">
                <h5 class="brand-text bold">&nbsp&nbspShop By Category</h5>
                <?php foreach ($categories as $category) { ?>
                    <a href="products/search.php?Filter=<?php echo str_replace(' ', '-', $category['CATEGORY']); ?>&sort=default&priceRange=%240+-+%2410000&check=&searchItem=&submit=Search">
                        <div class="col s6 m3">
                            <div class="card z-depth-0 category-card">
                                <img src="img/category/<?php echo $category['CATEGORY'] . '.jpg'; ?>" class="category-icon">

                                <div class="middle">
                                    <h5 class="category-text bold"><?php echo $category['CATEGORY']; ?></h5>
                                </div>
                            </div>
                        </div>
                    </a>

                <?php } ?>
            </div>

            <div class="row"></div>

            <?php if ($uid && $cluster > 0) { ?>
                <div class="row">
                    <h5 class="brand-text bold">&nbsp&nbspRecommended For You (<?php echo $cluster; ?>)</h5>
                    <?php $count = 0;
                        foreach ($cluster_recommendations as $recommendation) {
                            if ($count >= 12) break;
                            ?>
                        <div class="col s12 m3">
                            <a href="/products/product_details.php?id=<?php echo $recommendation['PDTID']; ?>">
                                <div class="card z-depth-0 small">

                                    <img src="<?php if ($recommendation['IMAGE']) {
                                                            echo $recommendation['IMAGE'];
                                                        } else {
                                                            echo 'img/product_icon.svg';
                                                        } ?>" class="product-icon circle">
                                    <div class="card-content center">
                                        <h6 class="black-text"> <?php echo htmlspecialchars($recommendation['PDTNAME']); ?> <label> <?php echo htmlspecialchars($recommendation['WEIGHT']); ?> </label></h6>
                                        <?php if ($recommendation['PDTDISCNT'] > 0) { ?>

                                            <label>
                                                <strike> <?php echo htmlspecialchars('$' . $recommendation['PDTPRICE']); ?> </strike>
                                            </label>
                                            &nbsp
                                            <label class="white-text discount-label">
                                                <?php echo htmlspecialchars('-' . $recommendation['PDTDISCNT'] . '% OFF'); ?>
                                            </label>

                                        <?php } ?>


                                        <div class="black-text flow-text">
                                            <?php echo '$' . number_format(htmlspecialchars($recommendation['PDTPRICE']) / 100 * htmlspecialchars(100 - $recommendation['PDTDISCNT']), 2, '.', ''); ?>
                                        </div>

                            </a>

                            <div class="card-action right-align">
                                <?php if (substr($uid, 0, 3) == 'CUS' || substr($uid, 0, 3) == 'ANO') { ?>
                                    <a href="index.php?<?php echo 'cart=' . $recommendation['PDTID']; ?>">
                                        <div class="red-text"><i class="fa fa-shopping-cart"></i> Cart</div>
                                    </a>
                                <?php } ?>
                            </div>
                        </div>
                </div>
        </div>
    <?php $count++;
        } ?>


    </div>
<?php } ?>
</div>
<?php include("templates/footer.php"); ?>

</div>

        </html>