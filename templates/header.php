<?php
session_start();
?>

<head>
	<title>Super Data</title>
	<!-- Compiled and minified CSS -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<script src="https://code.jquery.com/jquery-1.10.2.js"></script>
	<script src="https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>

	<style type="text/css">
		.brand {
			background: #cbb09c !important;
		}

		.brand-text {
			color: #cbb09c !important;
		}

		form {
			max-width: 460px;
			margin: 20px auto;
			padding: 20px;
		}

		.product-icon {
			width: 100px;
			margin: 40px auto -30px;
			display: block;
			position: relative;
			top: -30px;
		}

		.bold {
			font-weight: bold;
		}

		.flex {
			display: flex;
		}

		.no-pad {
			margin: 0px;
			padding: 0px;
		}

		/* for index.php */
		.sidebar {
			height: 100%;
			width: 245px;
			position: fixed;
			z-index: 1;
			overflow-x: hidden;
			border-right: 1px solid white;
		}

		.container {
			margin-left: 250px;
		}

		/* slider range for index.php */
		.ui-slider {
			position: relative;
			text-align: left;
		}

		.ui-slider .ui-slider-handle {
			position: absolute;
			z-index: 2;
			width: 1.2em;
			height: 1.2em;
		}

		.ui-slider .ui-slider-range {
			position: absolute;
			z-index: 1;
			font-size: 0.7em;
			display: block;
		}

		.ui-slider-horizontal {
			height: 0.8em;
		}

		.ui-slider-horizontal .ui-slider-handle {
			top: -0.3em;
			margin-left: -0.6em;
		}

		.ui-slider-horizontal .ui-slider-range {
			height: 100%;
		}

		.ui-widget.ui-widget-content {
			border: 1px solid #c5c5c5;
		}

		.ui-widget-header {
			background: #e9e9e9;
		}

		.ui-widget-content .ui-state-default,
		.ui-widget-header .ui-state-default,
		html .ui-button.ui-state-disabled:active {
			border: 1px solid #c5c5c5;
			background: #f6f6f6;
			color: #454545;
		}
	</style>
</head>

<body class="grey lighten-4">
	<nav class="white z-depth-0">
		<div class="container">
			<strong>
				<a href="../index.php" class="brand-logo brand-text left">SuperData</a>
			</strong>
			<ul id="nav-mobile" class="right hide-on-small-and-down">

				<?php if (isset($_SESSION['U_UID'])) { ?>
					<li class="right">
						<a href="../index.php" class="btn btn-floating red lighten-2"><?php echo $_SESSION['U_INITIALS'] ?></a>
					</li>

					<li>
						<a href="../authentication/logout.php" class="btn brand z-depth-0">Logout</a>
					</li>

					<li>
						<a href="../cart.php" class="btn brand z-depth-0">Cart</a>
					</li>

					<li>
						<a href="../my_orders.php" class="btn brand z-depth-0">Orders</a>
					</li>

					<li>
						<a href="../inventory_management.php" class="btn brand z-depth-0">Inventory Management</a>
					</li>

					<li>
						<a href="../my_favourites.php" class="btn brand z-depth-0">Favourites</a>
					</li>

				<?php } else { ?>
					<li>
						<a href="../authentication/register.php" class="btn brand z-depth-0">Register</a>
					</li>

					<li>
						<a href="../authentication/login.php" class="btn brand z-depth-0">Login</a>
					</li>
				<?php } ?>

				<li>
					<a href="/list_product.php" class="btn brand z-depth-0">List Product</a>
				</li>
			</ul>
		</div>
	</nav>