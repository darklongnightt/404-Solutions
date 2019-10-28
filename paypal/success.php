<?php 
// Include configuration file 
include_once 'config.php'; 
 
// Include database connection file 
include("../config/db_connect.php");

//Design
include("../templates/header.php"); 
 
// Pagination for all results
$currDir = "paypal/success.php";
// $sql = "SELECT * FROM product, cart
// WHERE product.PDTID = cart.PDTID AND cart.USERID = '$uid'";
// // Fetch all as assoc array
// $result = mysqli_query($conn, $sql);
// $cartList = mysqli_fetch_all($result, MYSQLI_ASSOC);

// If transaction data is available in the URL 
    if(!empty($_GET['item_number']) && !empty($_GET['tx']) && !empty($_GET['amt']) && !empty($_GET['cc']) && !empty($_GET['st'])){ 
        // Get transaction information from URL 
        $item_number = $_GET['item_number'];  
        $txn_id = $_GET['tx']; 
        $payment_gross = $_GET['amt']; 
        $currency_code = $_GET['cc']; 
        $payment_status = $_GET['st']; 
         
        // Get product info from the database 
        // $productResult = $conn->query("SELECT * FROM products WHERE id = ".$item_number); 
        // $productRow = $productResult->fetch_assoc(); 
         
        // Check if transaction data exists with the same TXN ID. 
        // $prevPaymentResult = $conn->query("SELECT * FROM payments WHERE txn_id = '".$txn_id."'"); 
     
        // if($prevPaymentResult->num_rows > 0){ 
        //     $paymentRow = $prevPaymentResult->fetch_assoc(); 
        //     $payment_id = $paymentRow['payment_id']; 
        //     $payment_gross = $paymentRow['payment_gross']; 
        //     $payment_status = $paymentRow['payment_status']; 
        // }else{ 
        //     // Insert tansaction data into the database 
        //     $insert = $conn->query("INSERT INTO payments(item_number,txn_id,payment_gross,currency_code,payment_status) VALUES('".$item_number."','".$txn_id."','".$payment_gross."','".$currency_code."','".$payment_status."')"); 
        //     $payment_id = $conn->insert_id; 
        // } 
} 
?>
<link rel="stylesheet" type="text/css" href="/css/paypal_style.css">

<div class="container">
    <div class="error-container">
        <!-- <?php if(!empty($payment_id)){ ?> 
            <p><b>Reference Number:</b> <?php echo $payment_id; ?></p>
            <h4>Product Information</h4>
            <p><b>Name:</b> <?php echo $productRow['name']; ?></p>
            <p><b>Price:</b> <?php echo $productRow['price']; ?></p>
             <?php }else{ ?>
            <h1 class="error">Your Payment has Failed</h1>
        <?php } ?> -->
            <h1 class="paypalsuccess">Your Payment has been Successful</h1>
			
            <h4>Payment Information</h4>
            
            <p><b>Transaction ID:</b> <?php echo $txn_id; ?></p>
            <p><b>Paid Amount:</b> <?php echo $payment_gross; ?></p>
            <p><b>Payment Status:</b> <?php echo $payment_status; ?></p>

        
    </div>
</div>