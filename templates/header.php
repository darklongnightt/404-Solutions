<?php
session_start();
$uid = '';
if (isset($_SESSION['U_UID']))
	$uid = $_SESSION['U_UID'];
?>

<head>
	<title>Super Data</title>
	<!-- Compiled and minified CSS -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat">

	<script src="https://code.jquery.com/jquery-1.10.2.js"></script>
	<script src="https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>

	<link href="../css/ui-slider.css" type="text/css" rel="stylesheet"/>
	<link href="../css/headers.css" type="text/css" rel="stylesheet"/>
	<link href="../css/form.css" type="text/css" rel="stylesheet"/>
</head>

<body class="grey lighten-4">
	<nav class="white z-depth-0">
		<div class="container">
			<strong>
				<a href="../index.php" class="brand-logo grey-text left ">SuperData</a>
			</strong>
			<ul id="nav-mobile" class="right hide-on-small-and-down">

				<?php if ($uid) { ?>
					<li class="right">
						<a href="../index.php" class="btn btn-floating red lighten-2"><?php echo $_SESSION['U_INITIALS'] ?></a>
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
						<a href="../authentication/register.php" class="brand-text bold">Register</a>
					</li>
					<li>
						<a href="../authentication/login.php" class="brand-text bold">Login</a>
					</li>
				<?php } ?>

			</ul>
		</div>
	</nav>