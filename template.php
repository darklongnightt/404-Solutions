<?php
include("templates/header.php");
include("config/db_connect.php");

require 'vendor/autoload.php';

use \Mailjet\Resources;

date_default_timezone_set("Singapore");
$phpDate = date("Y-m");
$jsDate = strtotime($phpDate) * 1000;

$dataPoints = array(
    array("x" => $jsDate, "y" => 1)
);



$mj = new \Mailjet\Client('b8fdff92aeab5a577441b2fb7e7f0d7e', 'dc5524ab4f2698ceaa1ba8109e0555a2', true, ['version' => 'v3.1']);
$body = [
    'Messages' => [
        [
            'From' => [
                'Email' => "super.data.fyp@gmail.com",
                'Name' => "Super"
            ],
            'To' => [
                [
                    'Email' => "super.data.fyp@gmail.com",
                    'Name' => "Super"
                ]
            ],
            'Subject' => "Greetings from Mailjet.",
            'TextPart' => "My first Mailjet email",
            'HTMLPart' => "<h3>Dear passenger 1, welcome to <a href='https://www.mailjet.com/'>Mailjet</a>!</h3><br />May the delivery force be with you!",
            'CustomID' => "AppGettingStartedTest"
        ]
    ]
];
$response = $mj->post(Resources::$Email, ['body' => $body]);
$response->success() && var_dump($response->getData());


?>

<!DOCTYPE HTML>
<html>

<div class="container black">
    <div class="row">
        <div class="col m4 s6">
            <div class="card z-depth-0 center">
                <div class="card-content">
                    <div class="center">
                        <img src="/img/avatar-bg.png" class="product-icon">
                    </div>
                </div>
            </div>
        </div>
        <div class="col m4 s6">
            <div class="card z-depth-0 center">
                <div class="card-content">
                    <div class="center">
                        <img src="/img/avatar-bg.png" class="product-icon">
                    </div>
                </div>
            </div>
        </div>
        <div class="col m4 s6">
            <div class="card z-depth-0 center">
                <div class="card-content">
                    <div class="center">
                        <img src="/img/avatar-bg.png" class="product-icon">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include("templates/footer.php"); ?>
</div>

</html>