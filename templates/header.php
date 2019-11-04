<head>
	<title>SUPERDATA</title>
	<!-- Compiled and minified CSS -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat">

	<link rel="stylesheet" href="/css/ui-slider.css" type="text/css">
	<link rel="stylesheet" href="/css/headers.css" type="text/css">
	<link rel="stylesheet" href="/css/form_update.css" type="text/css">
	<link rel="stylesheet" href="/css/analytics.css" type="text/css">
	<link rel="stylesheet" href="/css/homepage.css" type="text/css">
	<link rel="stylesheet" href="/css/profile.css" type="text/css">
	<link rel="stylesheet" href="/css/dropdown.css" type="text/css">
	<link rel="stylesheet" href="/css/materialize.css" type="text/css">
	<link rel="stylesheet" href="/css/timeline.css" type="text/css">

	<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
	<script src="https://code.jquery.com/jquery-1.10.2.js"></script>
	<script src="https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>

	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
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
		$uid = $cookie_uid;
	} else {
		$uid = $_COOKIE['UID'];
	}
}
?>

<script>
	document.addEventListener('DOMContentLoaded', function() {
		var elems = document.querySelectorAll('.sidenav');
		var instances = M.Sidenav.init(elems, {
			edge: 'left',
			draggable: true
		});
	});

	// Toggle when user clicks a dropdown button
	function toggleDrop(e) {
		e.parentNode.getElementsByClassName("dd-content")[0].classList.toggle("show");

		// Remove dropdown content of elements that are not clicked
		var list = document.getElementsByClassName("dd-content");
		for (i = 0; i < list.length; i++) {
			if (list[i] != e.parentNode.getElementsByClassName("dd-content")[0])
				list[i].classList.remove('show');
		}
	}

	// Close the dd if the user clicks outside of it
	window.onclick = function(e) {
		if (!e.target.matches('.dropbtn')) {
			var list = document.getElementsByClassName("dd-content");

			for (i = 0; i < list.length; i++) {
				list[i].classList.remove('show');
			}
		}
	}
</script>

<body class="grey lighten-4">
	<nav class="nav-wrapper white z-depth-0" style="margin-bottom: 0px;">
		<a href="#" class="left brand-text sidenav-trigger" data-target="nav-mobile">
			<i class="material-icons">menu</i>
		</a>

		<div class="container">
			<strong>
				<?php if (substr($uid, 0, 3) == 'ANL') {
					echo '<a href="../analysis/cluster_report.php" class="brand-logo brand-text">
							Super<span class="red-text">D</span>ata
							</a>';
				} else {
					echo '<a href="../index.php" class="brand-logo brand-text">
							Super<span class="red-text">D</span>ata
							</a>';
				} ?>
			</strong>

			<ul class="right hide-on-small-and-down">

				<?php if (substr($uid, 0, 3) !== 'ANO') { ?>
					<li class="right">
						<a href="../profile.php" class="btn btn-floating red"><?php echo $_SESSION['U_INITIALS'] ?></a>
					</li>

					<li class="right">
						<a href="../authentication/logout.php" class="brand-text bold"><i class="fa fa-sign-out" aria-hidden="true"></i> Logout</a>
					</li>

					<?php if (substr($uid, 0, 3) == 'CUS') {
							include('customer_nav.php');
						} else if (substr($uid, 0, 3) == 'ADM') {
							include('admin_nav.php');
						} else if (substr($uid, 0, 3) == 'ANL') {
							include('analyst_nav.php');
						}
					} else { ?>
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



	<ul id="nav-mobile" class="right hide-on-small-and-down sidenav">

		<?php if (substr($uid, 0, 3) == 'CUS') { ?>
			<li>
				<div class="user-view">
					<div class="background">
						<img src="/img/avatar-bg1.jpg">
					</div>
					<a href="#user"><img class="circle" src="/img/avatar.jfif"></a>
					<a href="#name"><span class="white-text name"><?php echo htmlspecialchars($_SESSION['U_FIRSTNAME'] . ' ' . $_SESSION['U_LASTNAME']); ?></span></a>
					<a href="#email"><span class="white-text email"><?php echo htmlspecialchars($_SESSION['U_EMAIL']); ?></span></a>
				</div>
			</li>

			<li>
				<a href="../cart.php"><i class="material-icons">shopping_cart</i>Cart</a>
			</li>

			<li>
				<a href="../my_favourites.php"><i class="material-icons">favorite</i> Favourites</a>
			</li>

			<li>
				<a href="../my_orders.php" class="brand-text bold"><i class="material-icons">format_list_bulleted</i>My Orders</a>
			</li>

			<li>
				<a href="../authentication/logout.php" class="brand-text bold"> <i class="material-icons">exit_to_app</i>Logout</a>
			</li>
		<?php } else { ?>

			<li>
				<a href="../cart.php"><i class="material-icons">shopping_cart</i>Cart</a>
			</li>

			<li>
				<a href="../authentication/register.php" class="brand-text bold">Register</a>
			</li>

			<li>
				<a href="../authentication/login.php" class="brand-text bold">Login</a>
			</li>

		<?php } ?>

	</ul>