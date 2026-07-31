<?php
REQUIRE "config.php";
$dbh = new PDO($dsn, $user, $pw);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
//print_r($_POST);

// Add order to database
if(ISSET($_POST['placeOrder']) && $_POST['placeOrder'] == "Make Purchase"){
	$dbh->beginTransaction();
	// Add customer to database, email is UNIQUE to prevent duplicates
	$custQ = "INSERT INTO customers (first_name, last_name, email) VALUES (?, ?, ?);";
	$custSta = $dbh->prepare($custQ);
	$custSta->bindParam(1, $_POST['customer_first_name'], PDO::PARAM_STR);
	$custSta->bindParam(2, $_POST['customer_last_name'], PDO::PARAM_STR);
	$custSta->bindParam(3, $_POST['customer_email'], PDO::PARAM_STR);
	$success = $custSta->execute();
	// If customer already exists
	if(!$success){
		$getCustQ = "SELECT customer_id FROM customers WHERE email = ?;";
		$getCustSta = $dbh->prepare($getCustQ);
		$getCustSta->bindParam(1, $_POST['customer_email'], PDO::PARAM_STR);
		$getCustSta->execute();
		$custId = $getCustSta->fetchAll(PDO::FETCH_ASSOC)[0]['customer_id'];
	}
	else{
		$custId = $dbh->lastInsertId();
	}
	
	// Add credit card
	$ccQ = "INSERT INTO credit_cards (customer_id, card_number, expiry) VALUES (?, ?, ?);";
	$ccSta = $dbh->prepare($ccQ);
	$ccSta->bindParam(1, $custId, PDO::PARAM_INT);
	$ccSta->bindParam(2, $_POST['card_number'], PDO::PARAM_INT);
	$expiry = $_POST['expiry'] . "-01";
	$ccSta->bindParam(3, $expiry, PDO::PARAM_STR);
	$ccSta->execute();
	
	// Add purchase
	$purchaseQ = "INSERT INTO purchases (customer_id, total) VALUES (?, ?);";
	$purchaseSta = $dbh->prepare($purchaseQ);
	$purchaseSta->bindParam(1, $custId, PDO::PARAM_INT);
	$purchaseSta->bindParam(2, $_POST['product_price']);
	$purchaseSta->execute();
	$purchaseId = $dbh->lastInsertId();
	
	// Update junction table
	$junctionQ = "INSERT INTO purchase_items (purchase_id, product_id, price_at_purchase, amount) VALUES (?, ?, ?, ?);";
	$junctionSta = $dbh->prepare($junctionQ);
	$junctionSta->bindParam(1, $purchaseId, PDO::PARAM_INT);
	$junctionSta->bindParam(2, $_POST['product_id'], PDO::PARAM_INT);
	$junctionSta->bindParam(3, $_POST['product_price'], PDO::PARAM_INT);
	$amt = 1;
	$junctionSta->bindParam(4, $amt, PDO::PARAM_INT);
	
	//$debugJunction = [$purchaseId, $custId, $_POST['product_price'], $amt];
	
	$junctionSta->execute();
	
	// Update stock amount
	// Get current stock
	$getStockQ = "SELECT stock FROM products WHERE product_id = ?;";
	$getStockSta = $dbh->prepare($getStockQ);
	$getStockSta->bindParam(1, $_POST['product_id'], PDO::PARAM_INT);
	$getStockSta->execute();
	$stockAmt = $getStockSta->fetchAll(PDO::FETCH_ASSOC)[0]['stock'];
	
	// Set new stock amount
	$stockQ = "UPDATE products SET stock = ? WHERE product_id = ?;";
	$stockSta = $dbh->prepare($stockQ);
	$newAmt = $stockAmt - 1;
	$stockSta->bindParam(1, $newAmt, PDO::PARAM_INT);
	$stockSta->bindParam(2, $_POST['product_id']);
	$stockSta->execute();
	
	$dbh->commit();
	
	$purchaseSuccessful = true;
}

//if(ISSET($debugJunction)){print_r($debugJunction);}

// Place order
if(ISSET($_POST['newOrder']) && $_POST['newOrder'] == "Buy Item"){
	// Get info about item being bought
	$itemId = $_POST['product_id'];
	$itemQ = "SELECT * FROM products WHERE product_id = ?;";
	$itemSta = $dbh->prepare($itemQ);
	$itemSta->bindParam(1, $itemId, PDO::PARAM_INT);
	$itemSta->execute();
	if($itemSta->rowCount() > 0){
		$itemRes = $itemSta->fetchAll(PDO::FETCH_ASSOC)[0];
		$itemName = $itemRes['product_name'];
		$itemPrice = $itemRes['price'];
		$itemInStock = ($itemRes['stock'] > 0) ? true : false;
		if(!$itemInStock){
			echo "<h1 style='font-family:monospace;'>Selected item is out of stock</h1><div><a href='products.php'>Return to Products</a></div>";
			die();
		}
		$itemInfoHTML = "<div id='itemInfo'><div>You are buying <b>{$itemName}</b> for <b>\${$itemPrice}</b></div></div>";
	}
	else{
		echo "<h1 style='font-family:monospace;'>Selected item does not exist</h1><div><a href='products.php'>Return to Products</a></div>";
		die();
	}
}
elseif(!ISSET($purchaseSuccessful)){
	echo "<h1 style='font-family:monospace;'>You have to select an item to buy it</h1><div><a href='products.php'>Return to Products</a></div>";
	die();
}
if(!ISSET($itemInfoHTML) && !ISSET($purchaseSuccessful)){
	echo "<h1 style='font-family:monospace;'>You have to select an item to buy it - isset check</h1><div><a href='products.php'>Return to Products</a></div>";
	die();
}
if(ISSET($purchaseSuccessful)){
	echo "<div style='font-family:monospace!important;'><h1>Purchse was successful</h1><div><a href='products.php'>Return to Products</a></div></div>";
	die();
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Buy Item</title>
<style>
body{
	margin:0;
	font-family:monospace;
}
main{
	margin-top:64px;
}
#buyItemCont{
	margin:0 auto;
	width:50%;
	font-size:16px;
}
#itemInfo{
	font-size:18px;
	margin-bottom:24px;
}
form{
	width:fit-content;
	border:1px solid #000;
	padding:8px 16px;
	font-size:16px!important;
}
form div{
	display:flex;
	justify-content:space-between;
	align-items:center;
	margin-bottom:4px;
}
form label{
	display:inline-block;
	margin-right:16px;
}
form input[type='text']{
	width:232px;
}
</style>
</head>
<body>
	<main>
		<div id='buyItemCont'>
			<h1>Checkout</h1>
			<?php echo $itemInfoHTML; ?>
			<form id='checkoutInfo' action='' method='POST'>
				<div><label for='cname'>First Name</label><input id='cfname' name='customer_first_name' type='text' required></div>
				<div><label for='cname'>Last Name</label><input id='clname' name='customer_last_name' type='text' required></div>
				<div><label for='cemail'>Email</label><input id='cemail' name='customer_email' type='text' required></div>
				<div><label for='cnum'>Card Number</label><input id='cnum' name='card_number' type='number' required></div>
				<div><label for='cexp'>Expiry</label><input id='cexp' name='expiry' type='month' required></div>
				<div><input id='pid' name='product_id' type='hidden' value='<?php echo $itemId; ?>'></div>
				<div><input id='pprice' name='product_price' type='hidden' value='<?php echo $itemPrice; ?>'></div>
				<div style='margin-top:12px;'><input name='placeOrder' value='Make Purchase' type='submit'></div>
			</form>
		</div>
	</main>
</body>
