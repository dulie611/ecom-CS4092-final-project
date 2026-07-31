<?php
REQUIRE "config.php";
$dbh = new PDO($dsn, $user, $pw);

// List all orders
/*
Nicer visual format:
SELECT
	p.purchase_id,
	p.purchase_date,
	p.total,

	c.customer_id,
	c.first_name,
	c.last_name,
	c.email,

	cc.card_id,
	cc.card_number,
	cc.expiry,

	pi.product_id,
	pi.price_at_purchase,
	pi.amount,

	pr.product_name
FROM purchases AS p
JOIN customers AS c
	ON p.customer_id = c.customer_id
JOIN credit_cards AS cc
	ON c.customer_id = cc.customer_id
JOIN purchase_items AS pi
	ON p.purchase_id = pi.purchase_id
JOIN products AS pr
	ON pi.product_id = pr.product_id
ORDER BY p.purchase_id DESC;
*/
$ordersQ = "SELECT p.purchase_id, p.purchase_date, p.total, c.customer_id, c.first_name, c.last_name, c.email, cc.card_id, cc.card_number, cc.expiry, pi.product_id, pi.price_at_purchase, pi.amount, pr.product_name FROM purchases AS p JOIN customers AS c ON p.customer_id = c.customer_id JOIN credit_cards AS cc ON c.customer_id = cc.customer_id JOIN purchase_items AS pi ON p.purchase_id = pi.purchase_id JOIN products AS pr ON pi.product_id = pr.product_id ORDER BY p.purchase_id DESC;";
$ordersSta = $dbh->prepare($ordersQ);
$ordersSta->execute();
$allOrdersHTML = "";
if($ordersSta->rowCount() > 0){
	$allOrders = $ordersSta->fetchAll(PDO::FETCH_ASSOC);
	foreach($allOrders as $row => $order){
		$allOrdersHTML .= "<div class='orderId'><b>Order: {$order['purchase_id']}</b></div><div class='order'><div class='orderRow'><span><b>Purchase Date:</b> {$order['purchase_date']}</span><span><b>Total:</b> \${$order['total']}</span></div><div class='orderRow'><span><b>Name:</b> {$order['first_name']} {$order['last_name']}</span><span><b>Email:</b> {$order['email']}</span></div><div class='orderRow'><span><b>Credit Card:</b> {$order['card_number']}</span><span><b>Expiry:</b> {$order['expiry']}</span></div><div class='orderRow'><span><b>Product Name:</b> {$order['product_name']}</span><span><b>Price at Purchase:</b> {$order['price_at_purchase']}</span><span><b>Amount:</b> {$order['amount']}</span></div></div>";
	}
}
else{
	echo "<h1 style='font-family:monospace;'>No orders on record</h1><div><a href='/'>Return to Homepage</a></div>";
	die();
}

?>
<!DOCTYPE html>
<html>
<head>
<title>Orders</title>
<style>
body{
	margin:0;
	font-family:monospace;
}
main{
	margin-top:64px;
}
#allOrders{
	width:80%;
	margin:0 auto;
	font-size:16px;
}
.order{
	width:fit-content;
	border:1px solid #000;
	padding:8px 16px;
	margin-bottom:24px;
}
nav{
	display:flex;
	justify-content:space-between;
	align-items:center;
	width:50%;
	margin:0 auto;
}
nav div a{
	text-decoration:none;
	font-family:monospace;
	font-size:18px;
	color:#000;
	font-weight:bold;
}
nav div a:hover{
	color:blue;
}
.orderId{
	font-size:18px;
}
.orderRow{
	border-bottom:1px solid #000;
	padding-bottom:4px;
	margin-bottom:8px;
}
.orderRow:last-child{
	border-bottom:none;
	padding-bottom:0;
	margin-bottom:0;
}
.orderRow span{
	display:block;
	margin-right:8px;
}
</style>
</head>
<body>
	<nav>
		<div><a href='products'>Products</a></div>
		<div><a href='add-product'>Add a Product</a></div>
		<div><a href='orders'>Orders</a></div>
	</nav>
	<main>
		<div id='allOrders'>
		<h1>All Orders</h1>
		<?php echo $allOrdersHTML; ?>
		</div>
	</main>
</body>