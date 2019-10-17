<?php
include("../config/db_connect.php");
include("../templates/header.php");

// Getting data from table: cluster 
$sql = "SELECT * FROM cluster ORDER BY CREATED_AT DESC";
$result = mysqli_query($conn, $sql);
$cluster_list = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Get the latest clustering result
$curr_cluster = $cluster_list[0];
$age = $gender = $transactions = $total_spendings = $last_purchase = array();
$size = $cluster = 1;

// Checking and formattings for input factors
if ($curr_cluster["AGE"] != "") {
    $age = explode(" ", $curr_cluster["AGE"]);
    $size = count($age);
}

if ($curr_cluster["GENDER"] != "") {
    $gender = explode(" ", $curr_cluster["GENDER"]);
    $size = count($gender);
}

if ($curr_cluster["TOTAL_SPENDINGS"] != "") {
    $total_spendings = explode(" ", $curr_cluster["TOTAL_SPENDINGS"]);
    $size = count($total_spendings);
}

if ($curr_cluster["LAST_PURCHASE"] != "") {
    $last_purchase = explode(" ", $curr_cluster["LAST_PURCHASE"]);
    $size = count($last_purchase);
}

if ($curr_cluster["TRANSACTIONS"] != "") {
    $transactions = explode(" ", $curr_cluster["TRANSACTIONS"]);
    $size = count($transactions);
}


if (isset($_POST['profile'])) {
    $cluster = $_POST['profile'];
}

if (isset($_POST['submit'])) {
    if (isset($_POST['cluster'])) {
        echo $_POST['cluster'];
    }

    echo '<br>';

    if (isset($_POST['coupon'])) {
        echo $_POST['coupon'];
    }
}

// Getting data from table: customers
$sql = "SELECT * FROM customer WHERE CLUSTER='$cluster'";
$result = mysqli_query($conn, $sql);
$cus_list = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Get distinct coupon types
$sql = "SELECT * FROM coupon WHERE DESCRIPTION IN (SELECT DISTINCT DESCRIPTION FROM coupon);";
$result = mysqli_query($conn, $sql);
$coupons = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Free memory of result and close connection
mysqli_free_result($result);
mysqli_close($conn);

function getAge($dob)
{
    // Explode the date to get month, day and year
    $birthDate = explode("-", $dob);

    // Return age from date or birthdate
    $age = (date("md", date("U", mktime(0, 0, 0, $birthDate[1], $birthDate[2], $birthDate[0]))) > date("md")
        ? ((date("Y") - $birthDate[0]) - 1)
        : (date("Y") - $birthDate[0]));
    return $age;
}

?>

<!DOCTYPE HTML>
<html>
<h4 class="center grey-text">Customer Demographics Report</h4>
<div class="container">
    <div class="row">
        <div class="col m4 s8">
            <div class="card z-depth-0 small">
                <div class="card-content">
                    <h4 class="red-text bold center">
                        <?php echo $curr_cluster['NUM_CUSTOMERS']; ?>
                    </h4>
                    <div class="bold center"> Customers </div>
                    <br>
                    <div class="divider"></div>
                    <h4 class="red-text bold center">
                        <?php echo $curr_cluster['NUM_CLUSTERS']; ?>
                    </h4>
                    <div class="bold center"> Cluster Profiles </div>
                </div>
            </div>
        </div>

        <div class="col m4 s8">
            <div class="card z-depth-0 small">
                <div class="card-content">
                    <h6 class="brand-text bold ">
                        <?php echo "Method: " . $curr_cluster['METHOD']; ?>
                    </h6>
                    <h6 class="brand-text bold ">
                        <?php echo "Distance: " . $curr_cluster['DISTANCE']; ?>
                    </h6>
                    <h6 class="brand-text bold ">
                        <?php echo "Data: ";
                        if ($curr_cluster['NORMALIZE'] == 'TRUE') {
                            echo "Normalized";
                        } else {
                            echo "Non-Normalized";
                        }; ?>
                    </h6>
                    <br>

                    <?php if ($curr_cluster['METHOD'] == 'K-Means Clustering') {
                        echo '<img src="../img/k-means.png" class="product-icon">';
                    } else {
                        echo '<img src="../img/hclust.png" class="product-icon">';
                    }?>
                </div>
            </div>
        </div>

        <div class="col m3 s6">
            <div class="card z-depth-0 small">
                <div class="card-content">
                    <h6 class="brand-text bold red-text">
                        <?php echo "Input Factors" ?>
                    </h6>
                    <ul>
                        <?php
                        if ($age) {
                            echo '<li style="list-style-type: initial; margin-left: 15px">Age</li>';
                        }

                        if ($gender) {
                            echo '<li style="list-style-type: initial; margin-left: 15px">Gender</li>';
                        }

                        if ($transactions) {
                            echo '<li style="list-style-type: initial; margin-left: 15px">Number of Transactions</li>';
                        }

                        if ($total_spendings) {
                            echo '<li style="list-style-type: initial; margin-left: 15px">Total Spendings</li>';
                        }

                        if ($last_purchase) {
                            echo '<li style="list-style-type: initial; margin-left: 15px">Days Since Last Purchase</li>';
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>

    </div>
    <div class="row">
        <div class="col m11 s22">
            <div class="card z-depth-0">
                <div class="card-content">
                    <h5 class="bold">Cluster Statistic Means</h5>
                    <table class="striped responsive-table">
                        <thead>
                            <tr>
                                <?php
                                echo '<th>Cluster Profile</th>';

                                if ($age) {
                                    echo '<th>Age</th>';
                                }

                                if ($gender) {
                                    echo '<th>Gender</th>';
                                }

                                if ($transactions) {
                                    echo '<th>Transactions</th>';
                                }

                                if ($total_spendings) {
                                    echo '<th>Total Spendings</th>';
                                }

                                if ($last_purchase) {
                                    echo '<th>Days Since Last Purchase</th>';
                                }
                                ?>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            for ($i = 1; $i < $size; $i++) {
                                echo '<tr>';
                                echo '<td>' . $i . '</td>';
                                if ($age) {
                                    echo '<td>' . $age[$i] . '</td>';
                                }

                                if ($gender) {
                                    echo '<td>' . $gender[$i] . '</td>';
                                }

                                if ($transactions) {
                                    echo '<td>' . $transactions[$i] . '</td>';
                                }

                                if ($total_spendings) {
                                    echo '<td>' . $total_spendings[$i] . '</td>';
                                }

                                if ($last_purchase) {
                                    echo '<td>' . $last_purchase[$i] . '</td>';
                                }
                                echo '</tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col m11 s22">
            <div class="card z-depth-0">
                <div class="card-content">

                    <form action="cluster_report.php" method="POST">
                        <h5 class="bold">Select Profile Group</h5>
                        <select class="browser-default" name="profile" onchange='this.form.submit()'>

                            <?php
                            for ($i = 1; $i < $size; $i++) {
                                echo '<option value=' . $i;
                                if ($cluster == $i) echo ' selected';
                                echo '>Profile ' . $i . '</option>';
                            }
                            ?>

                        </select>
                        <noscript><input type="submit" value="submit"></noscript>
                    </form>

                    <table class="striped responsive-table">
                        <thead>
                            <tr>
                                <?php
                                echo '<th>Cluster Profile</th>';
                                echo '<th>Name</th>';
                                echo '<th>Age</th>';
                                echo '<th>Gender</th>';
                                echo '<th>Email</th>';
                                ?>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            foreach ($cus_list as $cus) {
                                echo '<tr>';
                                echo '<td>' . $cus['CLUSTER'] . '</td>';
                                echo '<td>' . htmlspecialchars($cus['FIRSTNAME'] . ' ' . $cus['LASTNAME']) . '</td>';
                                echo '<td>' . htmlspecialchars(getAge($cus['DOB'])) . '</td>';
                                echo '<td>' . htmlspecialchars($cus['GENDER']) . '</td>';
                                echo '<td>' . htmlspecialchars($cus['EMAIL']) . '</td>';
                                echo '</tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <div class="card-action right-align">

                <form action="cluster_report.php" method="POST">
                        <h6 class="bold left">Select Coupon</h6>
                        <select class="browser-default" name="coupon">

                            <?php
                            foreach ($coupons as $coupon) {
                                echo '<option value="' . htmlspecialchars($coupon['DESCRIPTION']);
                                echo '">' . htmlspecialchars($coupon['DESCRIPTION']) . ' - ' . $coupon['DISCOUNT'] . '%' . '</option>';
                            }
                            ?>

                        </select>
                        <input type="hidden" name="cluster" value="<?php echo $cluster; ?>">
                        <br>
                        <button type="submit" name="submit" class=" btn brand z-depth-0">Send Coupon</button>
                    </form>
                </div>

            </div>
        </div>
    </div>

</div>

<?php include("../templates/footer.php"); ?>

</html>