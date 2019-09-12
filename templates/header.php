<?php
session_start();
?>

<head>
	<title>Super Data</title>
	<!-- Compiled and minified CSS -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">

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
	</style>
</head>

<body class="grey lighten-4">
	<nav class="white z-depth-0">
		<div class="container">
			<a href="../index.php" class="brand-logo brand-text center">Super Data</a>
			<ul id="nav-mobile" class="right hide-on-small-and-down">

				<?php if (isset($_SESSION['U_UID'])) { ?>
					<li>
						<a href="../index.php" class="btn btn-floating red lighten-2"><?php echo $_SESSION['U_INITIALS'] ?></a>
					</li>

					<li>
						<a href="../authentication/logout.php" class="btn brand z-depth-0">Logout</a>
					</li>

					<li>
						<a href="../cart.php" class="btn brand z-depth-0">Cart</a>
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