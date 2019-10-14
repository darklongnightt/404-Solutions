<head>
	<title>SUPERDATA</title>
	<!-- Compiled and minified CSS -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat">

	<link rel="stylesheet" href="../css/ui-slider.css" type="text/css">
	<link rel="stylesheet" href="../css/headers.css" type="text/css">
	<link rel="stylesheet" href="../css/form_update.css" type="text/css">
	<link rel="stylesheet" href="../css/analytics.css" type="text/css">
	<link rel="stylesheet" href="../css/homepage.css" type="text/css">

	<script src="https://code.jquery.com/jquery-1.10.2.js"></script>
	<script src="https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
</head>

<?php
session_start();
$uid = '';
if (isset($_SESSION['U_UID'])) {
	$uid = $_SESSION['U_UID'];
} else {
	// Set cookie for guest users
	if (!isset($_COOKIE['UID'])) {
		$cookie_uid = uniqid('ANO');
		setcookie('UID', $cookie_uid, time() + (86400 * 7), "/"); // 86400 = 1 day
	}

	$uid = $_COOKIE['UID'];
}
?>

<body class="grey lighten-4">
	<nav class="white z-depth-0">
		<div class="container">
			<strong>
				<?php if (substr($uid, 0, 3) == 'ANL') {
					echo '<a href="../analysis/cluster_report.php" class="brand-logo brand-text left ">
							Super<span class="red-text">D</span>ata
							</a>';
				} else {
					echo '<a href="../index.php" class="brand-logo brand-text left ">
							Super<span class="red-text">D</span>ata
							</a>';
				} ?>

			</strong>
			<ul id="nav-mobile" class="right hide-on-small-and-down">

				<?php if (substr($uid, 0, 3) !== 'ANO') { ?>
					<li class="right">
						<a href="../index.php" class="btn btn-floating red"><?php echo $_SESSION['U_INITIALS'] ?></a>
					</li>

					<li class="right">
						<a href="../authentication/logout.php" class="brand-text bold">Logout</a>
					</li>

					<?php if (substr($uid, 0, 3) == 'CUS') {
							include('customer_nav.php');
						} else if (substr($uid, 0, 3) == 'ADM') {
							include('admin_nav.php');
						} else if (substr($uid, 0, 3) == 'ANL') {
							include('analyst_nav.php');
						}
						?>
				<?php } else { ?>
					<li>
						<a href="../cart.php">
							<div class="brand-text bold">
								<i class="fa fa-shopping-cart"></i> Cart
							</div>
						</a>
					</li>

					<li>
						<a href="../authentication/register.php" class="brand-text bold">Register</a>
					</li>
					
					<li>
						<a href="../authentication/login.php" class="brand-text bold">Login</a>
					</li>
				<?php } ?>

			</ul>
		</div>
	</nav>