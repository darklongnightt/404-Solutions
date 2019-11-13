<?php
include("../config/db_connect.php");
include("../templates/header.php");

// Check for filtered category
$category = '';
if (isset($_GET['category'])) {
    $category = $_GET['category'];
}

// Fetch all reviews
$sql = "SELECT * FROM review JOIN customer ON review.USERID = customer.USERID 
WHERE review.CATEGORY = '$category' ORDER BY review.CREATED_AT DESC";
$result = mysqli_query($conn, $sql);
$ratings = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Calculate the mean rating for each section
$sratings = $pratings = $dratings = array();
$overall = $smean = $dmean = $pmean = 0;

// Init variables for bar chart width
$pBar =  array_fill(1, 5, 0);
$sBar = array_fill(1, 5, 0);
$dBar = array_fill(1, 5, 0);

if ($ratings) {
    $index = 0;
    foreach ($ratings as $rating) {
        $s = $rating['SRATING'];
        $d = $rating['DRATING'];
        $p = $rating['PRATING'];
        array_push($sratings, $s);
        array_push($dratings, $d);
        array_push($pratings, $p);

        // Update frequency of each rating type
        $pBar[$rating['PRATING']] += 1;
        $sBar[$rating['SRATING']] += 1;
        $dBar[$rating['DRATING']] += 1;

        $score = ($rating['PRATING'] + $rating['SRATING'] + $rating['DRATING']) / 3;
        $ratings[$index]['SCORE'] = $score;
        $overall += $score;

        ++$index;
    }

    $smean = array_sum($sratings) / sizeof($sratings);
    $dmean = array_sum($dratings) / sizeof($dratings);
    $pmean = array_sum($pratings) / sizeof($pratings);
    $overall /= sizeof($ratings);
}

?>

<style>
    .main-image {
        width: auto;
        max-height: 200px;
        margin-right: 25px;
        margin-left: 145px;
    }

    .overall-card {
        height: 200px;
        margin-bottom: 0px;
    }

    .sub-card {
        height: 300px;
        margin-top: 0px;
    }

    .star {
        color: gold;
        margin-left: 3px;
        font-size: 18px;
    }

    .rating-font {
        font-size: 16px;
        margin-left: 5px;
    }

    .graph-index {
        width: 10px;
        display: inline-block;
    }

    .bar {
        height: 20px;
        width: 200px;
        margin: 0 auto 10px auto;
        line-height: 20px;
        font-size: 16px;
        color: grey;
        text-align: center;
        padding: 0 0 0 10px;
        position: relative;
        display: inline-block;
    }

    .bar::before {
        content: '';
        width: 100%;
        position: absolute;
        left: 0;
        height: 20px;
        top: 0;
        z-index: -2;
        background: #ecf0f1;
    }

    .bar::after {
        content: '';
        background: gold;
        height: 20px;
        transition: 0.7s;
        display: block;
        width: 100%;
        -webkit-animation: bar-before 1 1.8s;
        position: absolute;
        top: 0;
        left: 0;
        z-index: -1;
    }

    @-webkit-keyframes bar-before {
        0% {
            width: 0px;
        }

        100% {
            width: 100%;
        }
    }

    .p1::after {
        max-width: <?php echo round($pBar[1] / sizeof($ratings) * 100, 0); ?>%;
    }

    .p2::after {
        max-width: <?php echo round($pBar[2] / sizeof($ratings) * 100, 0); ?>%;
    }

    .p3::after {
        max-width: <?php echo round($pBar[3] / sizeof($ratings) * 100, 0); ?>%;
    }

    .p4::after {
        max-width: <?php echo round($pBar[4] / sizeof($ratings) * 100, 0); ?>%;
    }

    .p5::after {
        max-width: <?php echo round($pBar[5] / sizeof($ratings) * 100, 0); ?>%;
    }

    .d1::after {
        max-width: <?php echo round($dBar[1] / sizeof($ratings) * 100, 0); ?>%;
    }

    .d2::after {
        max-width: <?php echo round($dBar[2] / sizeof($ratings) * 100, 0); ?>%;
    }

    .d3::after {
        max-width: <?php echo round($dBar[3] / sizeof($ratings) * 100, 0); ?>%;
    }

    .d4::after {
        max-width: <?php echo round($dBar[4] / sizeof($ratings) * 100, 0); ?>%;
    }

    .d5::after {
        max-width: <?php echo round($dBar[5] / sizeof($ratings) * 100, 0); ?>%;
    }

    .s1::after {
        max-width: <?php echo round($sBar[1] / sizeof($ratings) * 100, 0); ?>%;
    }

    .s2::after {
        max-width: <?php echo round($sBar[2] / sizeof($ratings) * 100, 0); ?>%;
    }

    .s3::after {
        max-width: <?php echo round($sBar[3] / sizeof($ratings) * 100, 0); ?>%;
    }

    .s4::after {
        max-width: <?php echo round($sBar[4] / sizeof($ratings) * 100, 0); ?>%;
    }

    .s5::after {
        max-width: <?php echo round($sBar[5] / sizeof($ratings) * 100, 0); ?>%;
    }
</style>

<!DOCTYPE HTML>
<html>

<?php if ($ratings) : ?>
    <div class="container">
        <div class="row">
            <div class="col m3 s6">
                <div class="card z-depth-0">
                    <div class="card-content center overall-card">
                        <img src="/img/rating_analysis.png" style="margin-top: 30px; width: 160px; height: auto;">
                    </div>
                </div>
            </div>

            <div class="col m6 s12">
                <div class="card z-depth-0">
                    <div class="card-content center overall-card">

                        <h5 class="bold"><?php echo $category; ?></h5>
                        <div class="bold" style="font-size: 30;"><?php echo round($overall, 1); ?> <span class="grey-text" style="font-size: 20;">/ 5</span></div>

                        <?php
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $overall) {
                                    echo '<i class="fa fa-star star" aria-hidden="true"></i>';
                                } else if ($i <= $overall + 0.5) {
                                    echo '<i class="fa fa-star-half-o star" aria-hidden="true"></i>';
                                } else if ($i > $overall) {
                                    echo '<i class="fa fa-star-o star" aria-hidden="true"></i>';
                                }
                            } ?>

                        <span class="rating-font"> (<?php echo sizeof($ratings); ?> Ratings) </span>

                    </div>
                </div>
            </div>

            <div class="col m3 s6">
                <div class="card z-depth-0">
                    <div class="card-content center overall-card">
                        <h5 class="bold gold-text">Satisfaction</h5>
                        <?php if ($overall >= 3.5) : ?>
                            <h3 class="bold green-text"><i class="fa fa-smile-o" aria-hidden="true"></i></h3>
                        <?php elseif ($overall < 3.5 && $overall >= 2.5) : ?>
                            <h3 class="bold grey-text"><i class="fa fa-meh-o" aria-hidden="true"></i></h3>
                        <?php else : ?>
                            <h3 class="bold red-text"><i class="fa fa-frown-o" aria-hidden="true"></i></h3>
                        <?php endif ?>
                    </div>
                </div>
            </div>

        </div>
        <div class="row">
            <div class="col m4 s8">
                <div class="card z-depth-0 sub-card" style="z-index: -3;">
                    <div class="card-content">
                        <h5 class="bold">Product Rating: </h5>
                        <span class="bold" style="font-size: 30; margin-right: 10px;"><?php echo round($pmean, 1); ?> <span class="grey-text" style="font-size: 20;">/ 5</span></span>

                        <?php
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $pmean) {
                                    echo '<i class="fa fa-star star" aria-hidden="true"></i>';
                                } else if ($i <= $pmean + 0.5) {
                                    echo '<i class="fa fa-star-half-o star" aria-hidden="true"></i>';
                                } else if ($i > $pmean) {
                                    echo '<i class="fa fa-star-o star" aria-hidden="true"></i>';
                                }
                            } ?>

                        <?php for ($i = 5; $i >= 1; $i--) {
                                echo '<div class="left">';
                                echo '<span class="graph-index">' . $i
                                    . '</span>' . ': <span class="bar p' . $i . '">&nbsp</span>'
                                    . '<span class="grey-text"> ('  . $pBar[$i] . ')</span>';;
                                echo '</div>';
                            } ?>
                    </div>
                </div>
            </div>

            <div class="col m4 s8">
                <div class="card z-depth-0 sub-card" style="z-index: -3;">
                    <div class="card-content">
                        <h5 class="bold">Delivery Rating: </h5>
                        <span class="bold" style="font-size: 30; margin-right: 10px;"><?php echo round($dmean, 1); ?> <span class="grey-text" style="font-size: 20;">/ 5</span></span>

                        <?php
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $dmean) {
                                    echo '<i class="fa fa-star star" aria-hidden="true"></i>';
                                } else if ($i <= $dmean + 0.5) {
                                    echo '<i class="fa fa-star-half-o star" aria-hidden="true"></i>';
                                } else if ($i > $dmean) {
                                    echo '<i class="fa fa-star-o star" aria-hidden="true"></i>';
                                }
                            } ?>

                        <?php for ($i = 5; $i >= 1; $i--) {
                                echo '<div class="left">';
                                echo '<span class="graph-index">' . $i
                                    . '</span>' . ': <span class="bar d' . $i . '">&nbsp</span>'
                                    . '<span class="grey-text"> ('  . $dBar[$i] . ')</span>';;
                                echo '</div>';
                            } ?>
                    </div>
                </div>
            </div>

            <div class="col m4 s8">
                <div class="card z-depth-0 sub-card" style="z-index: -3;">
                    <div class="card-content">
                        <h5 class="bold">Service Rating: </h5>
                        <span class="bold" style="font-size: 30; margin-right: 10px;"><?php echo round($smean, 1); ?> <span class="grey-text" style="font-size: 20;">/ 5</span></span>

                        <?php
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $smean) {
                                    echo '<i class="fa fa-star star" aria-hidden="true"></i>';
                                } else if ($i <= $smean + 0.5) {
                                    echo '<i class="fa fa-star-half-o star" aria-hidden="true"></i>';
                                } else if ($i > $smean) {
                                    echo '<i class="fa fa-star-o star" aria-hidden="true"></i>';
                                }
                            } ?>

                        <?php for ($i = 5; $i >= 1; $i--) {
                                echo '<div class="left">';
                                echo '<span class="graph-index">' . $i
                                    . '</span>' . ': <span class="bar s' . $i . '">&nbsp</span>'
                                    . '<span class="grey-text"> ('  . $sBar[$i] . ')</span>';;
                                echo '</div>';
                            } ?>
                    </div>
                </div>
            </div>

        </div>

        <div class="row">
            <div class="col m12 s12">
                <div class="card z-depth-0">
                    <div class="card-content">
                        <h5>Comments</h5>

                        <table class="striped responsive-table">
                            <tbody>
                                <?php
                                    foreach ($ratings as $rating) {
                                        $custScore = $rating['SCORE'];

                                        echo '<tr style="height: 120px;">';
                                        echo '<td class="center">';
                                        for ($i = 1; $i <= 5; $i++) {
                                            if ($i <= $custScore) {
                                                echo '<i class="fa fa-star star" aria-hidden="true"></i>';
                                            } else if ($i <= $custScore + 0.5) {
                                                echo '<i class="fa fa-star-half-o star" aria-hidden="true"></i>';
                                            } else if ($i > $custScore) {
                                                echo '<i class="fa fa-star-o star" aria-hidden="true"></i>';
                                            }
                                        }
                                        echo '<div>by ' . $rating['FIRSTNAME'] . '</div>';
                                        echo '<div><i class="material-icons" style="margin-top:5px;">verified_user</i></div></td>';
                                        echo '<td>' . $rating['COMMENT'];
                                        echo '<a href="/products/product_reviews.php?id=' . $rating['PDTID'] . '">' . '<div class="grey-text" style="font-size: 14px; margin-top: 5px;">' . $rating['PDTID'] . '</div></a>';
                                        echo '<div class="grey-text" style="font-size: 14px; margin-top: 5px;">' . date('d-M-Y H:i', strtotime($rating['CREATED_AT'])) . '</div></td>';
                                        echo '</tr>';
                                    }
                                    ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>



    </div>
<?php else : ?>
    <h5 class="center">Error: No product ratings found!</h5>
<?php endif ?>





<?php include("../templates/footer.php"); ?>

</html>