<?php
include('config/db_connect.php');
include('templates/header.php');

if (isset($_SESSION['LASTACTION'])) {
	switch ($_SESSION['LASTACTION']) {
		case 'UPDATEPROFILE':
			echo "<script>M.toast({html: 'Successfully updated personal details!'});</script>";
			break;

		case 'UPDATEPASSWORD':
			echo "<script>M.toast({html: 'Successfully updated password!'});</script>";
			break;

		case 'UPDATEADDRESS':
			echo "<script>M.toast({html: 'Successfully updated shipping address!'});</script>";
			break;
	}

	$_SESSION['LASTACTION'] = "NONE";
}

$errors = array(
	'oldpassword' => '', 'newpassword1' => '', 'newpassword2' => '',
	'fname' => '', 'lname' => '', 'email' => '', 'dob' => '', 'contactno' => '',
	'country1' => '', 'address1' => '', 'postal1' => '', 'postal2' => ''
);
$oldpassword = $newpassword1 = $newpassword2 = '';
$dob = $fname = $lname = $email = $contactno = '';
$country1 = $address1 = $postal1 = $country2 = $address2 = $postal2 = '';
$address = $address2 = array();

//Personal details of this customer
$queryPD = "SELECT * FROM customer WHERE USERID = '$uid'";
$resultPD = mysqli_query($conn, $queryPD);
$cust_details = mysqli_fetch_assoc($resultPD);
$currentEmail = $cust_details['EMAIL'];

//Address of this customer
$queryADD = "SELECT * FROM address WHERE USERID = '$uid'";
$resultADD = mysqli_query($conn, $queryADD);
$cust_address = mysqli_fetch_assoc($resultADD);

if (isset($_POST['updateDetails'])) {

	//Gets data from the POST request 
	if (empty($_POST['email'])) {
		$errors['email'] = 'Email is required!';
	} else {
		$email = mysqli_real_escape_string($conn, $_POST['email']);
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$errors['email'] = 'Email is not of valid format!';
		} else {
			// Check db for existing email
			if ($email != $currentEmail) {
				$sql = "SELECT * FROM customer WHERE EMAIL = '$email'";
				$result = mysqli_query($conn, $sql);

				if (mysqli_num_rows($result) > 0) {
					$errors['email'] = 'Entered email is already in use!';
				}
			}
		}
	}

	if (empty($_POST['firstname'])) {
		$errors['fname'] = 'First name is required!';
	} else {
		$fname = mysqli_real_escape_string($conn, $_POST['firstname']);
	}

	if (empty($_POST['lastname'])) {
		$errors['lname'] = 'Last name is required!';
	} else {
		$lname = mysqli_real_escape_string($conn, $_POST['lastname']);
	}

	if (empty($_POST['dob'])) {
		$errors['dob'] = 'Birthday is required!';
	} else {
		$dob = mysqli_real_escape_string($conn, $_POST['dob']);
	}

	if (empty($_POST['contactno'])) {
		$errors['contactno'] = 'Phone number is required!';
	} else {
		$contactno = mysqli_real_escape_string($conn, $_POST['contactno']);
		if (!preg_match('/^[0-9]*$/', $contactno)) {
			$errors['contactno'] = 'Phone number must be numeric!';
		}
	}

	if (!array_filter($errors)) {
		// Updates personal details in customers table
		$updateQ = "UPDATE customer SET FIRSTNAME = '$fname', LASTNAME = '$lname', EMAIL = '$email', PHONENO = '$contactno', DOB = '$dob' WHERE USERID = '$uid'";
		if (mysqli_query($conn, $updateQ)) {

			// Updates email address in salt table
			if ($email != $currentEmail) {
				$sql = "UPDATE salt SET EMAIL='$email' WHERE EMAIL='$currentEmail'";
				if (!mysqli_query($conn, $sql)) {
					echo 'Query Error: ' . mysqli_error($conn);
				}
			}

			$_SESSION['LASTACTION'] = 'UPDATEPROFILE';
			echo "<script type='text/javascript'>window.top.location='profile.php';</script>";
		} else {
			echo 'Query Error: ' . mysqli_error($conn);
		}
	}
}

if (isset($_POST['updateAdd'])) {

	// Gets data from the POST request 
	if (empty($_POST['address1'])) {
		$errors['address1'] = 'Main shipping address is required!';
	} else {
		$address1 = $_POST['address1'];
	}

	if (empty($_POST['postal1'])) {
		$errors['postal1'] = 'Main shipping postal code is required!';
	} else {
		$postal1 = $_POST['postal1'];
		if (!preg_match('/^[0-9]+$/', $postal1)) {
			$errors['postal1'] = 'Postal code must be in numbers only!';
		}
	}

	if (!empty($_POST['postal2'])) {
		$postal2 = mysqli_real_escape_string($conn, $_POST['postal2']);
		if (!preg_match('/^[0-9]+$/', $postal2)) {
			$errors['postal2'] = 'Postal code must be in numbers only!';
		}
	}

	if (!array_filter($errors)) {

		$country1 = mysqli_real_escape_string($conn, $_POST['country1']);
		$address1 = mysqli_real_escape_string($conn, $_POST['address1']);
		$postal1 = mysqli_real_escape_string($conn, $_POST['postal1']);
		$country2 = mysqli_real_escape_string($conn, $_POST['country2']);
		$address2 = mysqli_real_escape_string($conn, $_POST['address2']);
		$postal2 = mysqli_real_escape_string($conn, $_POST['postal2']);

		// Updates if existing address else insert
		if ($cust_address) {
			$updateQ = "UPDATE address SET ADDRESS1 = '$address1', POSTALCD1 = '$postal1', COUNTRY1 = '$country1', 
				ADDRESS2 = '$address2', POSTALCD2 = '$postal2', COUNTRY2 = '$country2' WHERE USERID = '$uid'";
		} else {
			$updateQ = "INSERT INTO address (USERID, ADDRESS1, POSTALCD1, COUNTRY1, ADDRESS2, POSTALCD2, COUNTRY2) VALUES ('$uid', '$address1' ,'$postal1','$country1', 
		'$address2', '$postal2','$country2');";
		}

		if (mysqli_query($conn, $updateQ)) {
			$_SESSION['LASTACTION'] = 'UPDATEADDRESS';
			echo "<script type='text/javascript'>window.location.href='profile.php';</script>";
		} else {
			echo 'Query Error: ' . mysqli_error($conn);
		}
	}
}

// Checks if change password button is pressed
if (isset($_POST['changePass'])) {

	// Checks for input errors
	if (empty($_POST['oldpassword'])) {
		$errors['oldpassword'] = "Old password is required!";
	} else {
		$oldpassword = mysqli_real_escape_string($conn, $_POST['oldpassword']);
	}

	if (empty($_POST['newpassword1'])) {
		$errors['newpassword1'] = "New password is required!";
	} else {
		$newpassword1 = mysqli_real_escape_string($conn, $_POST['newpassword1']);

		// Password enforcement policy
        $letters = preg_match('@[a-zA-Z]@', $newpassword1);
        $numbers = preg_match('@[0-9]@', $newpassword1);

        if (!$letters || !$numbers || strlen($newpassword1) < 6) {
            $errors['newpassword1'] = 'Password length must be at least 6 characters long and be a mixture of numeric and alphabetic characters!';
        }
	}

	if (empty($_POST['newpassword2'])) {
		$errors['newpassword2'] = "Confirmed new password is required!";
	} else {
		if ($_POST['newpassword1'] != $_POST['newpassword2']) {
			$errors['newpassword2'] = "Confirmed password must be the same!";
		} else {
			$newpassword2 = mysqli_real_escape_string($conn, $_POST['newpassword2']);
		}
	}

	// No errors with the passwords
	if (!array_filter($errors)) {

		// Gets a customer record from db as a single associative array
		$sql = "SELECT * FROM customer JOIN salt on customer.EMAIL = salt.EMAIL 
        WHERE customer.USERID = '$uid'";
		$result = mysqli_query($conn, $sql);
		$customer = mysqli_fetch_assoc($result);

		// Usage of secured sha256 to hash password concat with generated salt
		$oldpassword .= $customer['SALT'];
		$hashedpassword = hash('sha256', $oldpassword);
		$checkPassword = hash_equals($customer['PASSWORD'], $hashedpassword) ? TRUE : FALSE;

		// Default deny policy
		if (!$checkPassword) {
			$errors['oldpassword'] = 'Invalid password entered!';
		} else if ($checkPassword) {

			// Correct password is entered, proceeds to update db
			$newpassword1 .= $customer['SALT'];
			$new_hashedpassword = hash('sha256', $newpassword1);
			$sql = "UPDATE customer SET PASSWORD='$new_hashedpassword' WHERE USERID='$uid'";
			if (mysqli_query($conn, $sql)) {

				// Update status of changepw to true
				$email = $customer['EMAIL'];
				$sql = "UPDATE salt SET CHANGEPW='FALSE' WHERE EMAIL='$email'";
				if (mysqli_query($conn, $sql)) {
					$_SESSION['LASTACTION'] = 'UPDATEPASSWORD';
					echo "<script type='text/javascript'>window.top.location='/profile.php';</script>";
				} else {
					echo 'Query Error: ' . mysqli_error($conn);
				}
			} else {
				echo 'Query Error: ' . mysqli_error($conn);
			}
		}

		mysqli_free_result($result);
		mysqli_close($conn);
	}
}


?>

	<style>
		.EditFormSide {
			border: 2px solid;
			border-radius: 5px;
			padding: 20px;
		}
	</style>

	<html>
	<script>
		$(document).ready(function() {
			if (window.location.href.indexOf("#address") > -1)
				$("#address-div").show().siblings().hide();
			else if (window.location.href.indexOf("#password") > -1)
				$("#changePass-div").show().siblings().hide();
			else
				$("#personal-div").show().siblings().hide();

			$("#details").click(function() {
				$("#personal-div").show().siblings().hide();
			});

			$("#address").click(function() {
				$("#address-div").show().siblings().hide();
			});

			$("#changePass").click(function() {
				$("#changePass-div").show().siblings().hide();
			});
		});
	</script>

	<body>
		<div class="sidenav sidenav-fixed ">
			<div class="center top-padding">
				<span class="btn profile-logo btn-floating red"> <?php echo $_SESSION['U_INITIALS'] ?></span>
			</div>
			<br>
			<ul class="center">
				<a href="#">
					<li id="details" class="black-text"> My Personal Details </li>
				</a>
				<?php if (substr($uid, 0, 3) == 'CUS') { ?>
					<a href="#">
						<li id="address" class="black-text"> My Address </li>
					</a>
				<?php } ?>
				<a href="#">
					<li id="changePass" class="black-text"> Change Password </li>
				</a>

			</ul>
		</div>

		<div class="right-contents">
			<div id="personal-div" class="prof-detail-container">
				<h4 class="center top-padding"> Personal Details</h4>
				<br>
				<form method="post" class="EditForm">
					<label>First Name: </label>
					<input type="text" name="firstname" value="<?php echo htmlspecialchars($cust_details['FIRSTNAME']); ?>">
					<div class="red-text"><?php echo htmlspecialchars($errors['fname']); ?></div>

					<label>Last Name:</label>
					<input type="text" name="lastname" value="<?php echo htmlspecialchars($cust_details['LASTNAME']); ?>">
					<div class="red-text"><?php echo htmlspecialchars($errors['lname']); ?></div>

					<label>Email: </label>
					<input type="text" name="email" value="<?php echo htmlspecialchars($cust_details['EMAIL']); ?>">
					<div class="red-text"><?php echo htmlspecialchars($errors['email']); ?></div>

					<label>Date Of Birth: </label>
					<input type="date" name="dob" value="<?php echo htmlspecialchars($cust_details['DOB']); ?>">
					<div class="red-text"><?php echo htmlspecialchars($errors['dob']); ?></div>

					<label>Contact Number:</label>
					<input type="text" name="contactno" value="<?php echo htmlspecialchars($cust_details['PHONENO']); ?>" maxlength="8">
					<div class="red-text"><?php echo htmlspecialchars($errors['contactno']); ?></div>

					<br>
					<div class="center">
						<input type="submit" name="updateDetails" value="update" class="btn brand z-depth-0">
					</div>
				</form>
			</div>

			<div id="address-div">
				<h4 class="center top-padding"> Shipping Address </h4>

				<div class="row">
					<div class="col s12 m6">
						<form method="post" action="profile.php?#address">
							<div class="card z-depth-0 EditFormSide">
								<h6>Primary Address</h6>
								<br>

								<label>Address:</label>
								<input type="text" name="address1" value="<?php echo htmlspecialchars($cust_address['ADDRESS1']); ?>">
								<div class="red-text"><?php echo htmlspecialchars($errors['address1']); ?></div>


								<label>Postal Code:</label>
								<input type="text" name="postal1" value="<?php echo htmlspecialchars($cust_address['POSTALCD1']); ?>" maxlength="6">
								<div class="red-text"><?php echo htmlspecialchars($errors['postal1']); ?></div>

								<label>Country: </label>
								<select class="browser-default" name="country1">
									<option value="Singapore" <?php if ($cust_address['COUNTRY1'] == 'Singapore') echo 'selected="selected"'; ?>>Singapore</option>
									<option value="Malaysia" <?php if ($cust_address['COUNTRY1'] == 'Malaysia') echo 'selected="selected"'; ?>>Malaysia</option>
								</select>
							</div>
					</div>

					<div class="col s12 m6">
						<div class="card z-depth-0 EditFormSide">
							<h6>Secondary Address</h6>
							<br>

							<label>Address:</label>
							<input type="text" name="address2" value="<?php echo htmlspecialchars($cust_address['ADDRESS2']); ?>">

							<label>Postal Code:</label>
							<input type="text" name="postal2" value="<?php echo htmlspecialchars($cust_address['POSTALCD2']); ?>" maxlength="6">
							<div class="red-text"><?php echo htmlspecialchars($errors['postal2']); ?></div>

							<label>Country: </label>
							<select class="browser-default" name="country2">
								<option value="Singapore" <?php if ($cust_address['COUNTRY2'] == 'Singapore') echo 'selected="selected"'; ?>>Singapore</option>
								<option value="Malaysia" <?php if ($cust_address['COUNTRY2'] == 'Malaysia') echo 'selected="selected"'; ?>>Malaysia</option>
							</select>
						</div>
					</div>
				</div>

				<div class="center">
					<input type="submit" name="updateAdd" value="update" class="btn brand z-depth-0">
				</div>

				</form>
			</div>

			<div id="changePass-div">
				<h4 class="center top-padding">Change Password</h4>
				<br>
				<form class="EditForm" method="POST" action="profile.php?#password">
					<label>Old Password: </label>
					<input type="password" name="oldpassword">
					<div class="red-text"><?php echo htmlspecialchars($errors['oldpassword']); ?></div>

					<label>New Password: </label>
					<input type="password" name="newpassword1">
					<div class="red-text"><?php echo htmlspecialchars($errors['newpassword1']); ?></div>

					<label>Confirm New Password: </label>
					<input type="password" name="newpassword2">
					<div class="red-text"><?php echo htmlspecialchars($errors['newpassword2']); ?></div>
					<br>
					<div class="center">
						<input type="submit" name="changePass" value="Change Password" class="btn brand z-depth-0">
					</div>
				</form>
			</div>
		</div>
	</body>

	</html>