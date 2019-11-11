<?php
include("../config/db_connect.php");
include("../templates/header.php");

// Checks if search comment is clicked
$commentFilter = '';
if (isset($_POST['submit'])) {
    if (isset($_POST['comment'])) {
        $commentFilter = $_POST['comment'];
    }
}

// Retrieve product categories
$sql = "SELECT DISTINCT CATEGORY FROM review";
$result = mysqli_query($conn, $sql);
$categories = mysqli_fetch_all($result, MYSQLI_ASSOC);
$categoryReviews = $comments = array();

// Retrieve product reviews from each category
foreach ($categories as $cat) {
    // Init variables for associative array for each category
    // Structure: $categoryReviews['BAZAAR']['PRATING'][1] 
    // returns freq of 1 star PRODUCT ratings for BAZAAR CATEGORY
    $pBar =  array_fill(1, 5, 0);
    $sBar = array_fill(1, 5, 0);
    $dBar = array_fill(1, 5, 0);
    $data = array('CATEGORY' => $cat['CATEGORY'], 'PRATINGS' => $pBar, 'SRATINGS' => $sBar, 'DRATINGS' => $dBar, 'TOTAL' => 0, 'SIZE' => 0);
    $categoryReviews[$cat['CATEGORY']] = $data;
}

// Fetch all reviews
$sql = "SELECT * FROM review JOIN customer ON review.USERID = customer.USERID 
ORDER BY review.CREATED_AT DESC";
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
        $cat = $rating['CATEGORY'];
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

        // Update frequency of each category -> rating type -> rating freq
        $categoryReviews[$cat]['SRATINGS'][$s] += 1;
        $categoryReviews[$cat]['PRATINGS'][$p] += 1;
        $categoryReviews[$cat]['DRATINGS'][$d] += 1;
        $categoryReviews[$cat]['TOTAL'] += $score;
        $categoryReviews[$cat]['SIZE'] += 1;

        // Check if comment contains a keyword
        if ($commentFilter) {
            $comment = $rating['COMMENT'];
            if (strpos(strtolower($comment), strtolower($commentFilter)) !== false) {
                $rating['SCORE'] = $score;
                array_push($comments, $rating);
            }
        }

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

                        <h5 class="bold">Storewide Ratings Analysis </h5>
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
            <div class="col m12 s24">
                <div class="card z-depth-0">
                    <div class="card-content">

                        <table class="highlight responsive-table centered">
                            <thead>
                                <tr>
                                    <th>Product Category</th>
                                    <th>Overall Rating</th>
                                    <th>Statistics</th>
                                    <th>Satisfaction</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    foreach ($categoryReviews as $catReview) {
                                        // Get overall mean
                                        $mean = $catReview['TOTAL'] / $catReview['SIZE'];
                                        $pMean = $sMean = $dMean = 0;

                                        // Get mean for each rating type
                                        for ($i = 1; $i <= 5; $i++) {
                                            $pMean += $catReview['PRATINGS'][$i] * $i;
                                            $sMean += $catReview['SRATINGS'][$i] * $i;
                                            $dMean += $catReview['DRATINGS'][$i] * $i;
                                        }
                                        $pMean /= $catReview['SIZE'];
                                        $sMean /= $catReview['SIZE'];
                                        $dMean /= $catReview['SIZE'];

                                        echo '<tr>';
                                        echo '<td> <a href="/products/search.php?Filter=' .  str_replace(' ', '-', $catReview['CATEGORY']) . '&sort=default&priceRange=%240+-+%2410000&check=&searchItem=&submit=Search">';
                                        echo '<div class="bold">' . $catReview['CATEGORY'] . '</div> </a>';
                                        echo '<div class="grey-text">(' . $catReview['SIZE'] . ' Ratings' . ')</div></td> ';

                                        echo '<td>';
                                        echo '<div class="bold" style="font-size: 30; margin-bottom: 15px;">' . round($mean, 1) . '<span class="grey-text" style="font-size: 20;">/ 5</span></div>';
                                        for ($i = 1; $i <= 5; $i++) {
                                            if ($i <= $mean) {
                                                echo '<i class="fa fa-star star" aria-hidden="true"></i>';
                                            } else if ($i <= $mean + 0.5) {
                                                echo '<i class="fa fa-star-half-o star" aria-hidden="true"></i>';
                                            } else if ($i > $mean) {
                                                echo '<i class="fa fa-star-o star" aria-hidden="true"></i>';
                                            }
                                        }
                                        echo '</td>';

                                        echo '<td>';
                                        echo '<div><span style="width: 75px; display: inline-block;">Product:</span> <span class="bold" style="font-size: 23;">'
                                            . number_format($pMean, 1, '.', '') .
                                            '</spanProduct:> <span class="grey-text" style="font-size: 15;">/ 5</span> </div>';

                                        echo '<div><span style="width: 75px; display: inline-block;">Delivery:</span> <span class="bold" style="font-size: 23;">' . number_format($dMean, 1, '.', '') . '</span> <span class="grey-text" style="font-size: 15;">/ 5</span> </div>';
                                        echo '<div><span style="width: 75px; display: inline-block;">Service:</span> <span class="bold" style="font-size: 23;">' . number_format($sMean, 1, '.', '') . '</span> <span class="grey-text" style="font-size: 15;">/ 5</span> </div>';
                                        echo '</td>';

                                        echo '<td>';
                                        if ($mean >= 3.5) :
                                            echo '<h3 class="bold green-text"><i class="fa fa-smile-o" aria-hidden="true"></i></h3>';
                                        elseif ($mean < 3.5 && $mean >= 2.5) :
                                            echo '<h3 class="bold grey-text"><i class="fa fa-meh-o" aria-hidden="true"></i></h3>';
                                        else :
                                            echo '<h3 class="bold red-text"><i class="fa fa-frown-o" aria-hidden="true"></i></h3>';
                                        endif;

                                        echo '</td>';
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
            <div class="col m12 s12">
                <div class="card z-depth-0">
                    <div class="card-content">
                        <h5>Search Comments</h5>

                        <form action="/analysis_report/review_analysis.php" method="POST" style="margin-bottom: 0%;">
                            <div class="row" style="border-radius: 20px; border-style: solid; color: grey; border-width: thin; background: white; ">
                                <div class="col m11 s11">
                                    <input type="text" name="comment" placeholder="Search Comments" style="width: 103%;">
                                </div>

                                <div class="col m1 s1">
                                    <button type="submit" name="submit" class="btn white black-text z-depth-0" style="width: 90%;">
                                        <i class="material-icons" style="font-size: 26px; margin-top: 5px; margin-left: 5px;">search</i>
                                    </button>
                                </div>
                            </div>
                        </form>


                        <?php if ($comments) : ?>

                            <table class="striped responsive-table">
                                <tbody>
                                    <?php
                                            foreach ($comments as $rating) {
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
                        <?php endif ?>

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