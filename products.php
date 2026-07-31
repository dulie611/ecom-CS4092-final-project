<?php
REQUIRE "config.php";
$dbh = new PDO($dsn, $user, $pw);

// Get products
$productsQ = "SELECT * FROM ecom.products;";
$productsSta = $dbh->prepare($productsQ);
$productsSta->execute();
$allProducts = $productsSta->fetchAll(PDO::FETCH_ASSOC);

//print_r($allProducts);
$productHTML = "";
foreach($allProducts as $row => $product){
	//print_r($product);
	$productHTML .= "<div class='product'><div class='pname'><b>{$product['product_name']}</b></div><div class='pdesc'>{$product['product_desc']}</div><div class='pprice'>Price: \${$product['price']}</div><div class='pstock'>Stock: {$product['stock']}</div><div class='buyNow'><form action='buy-item.php' method='POST'><input type='hidden' name='product_id' value='{$product['product_id']}'><input type='submit' value='Buy Item' class='buyItem' name='newOrder'></form></div></div>";
	//echo $productHTML;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Products</title>
<style>
body{
	margin:0;
	font-family:monospace;
}
main{
	margin-top:64px;
}
#allProducts{
	width:50%;
	margin:0 auto;
	font-size:16px;
}
.product{
	width:fit-content;
	border:1px solid #000;
	padding:8px 16px;
	margin-bottom:24px;
}
.pprice{
	margin-top:8px;
}
.buyNow{
	margin-top:8px;
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
</style>
</head>
<body>
<nav>
		<div><a href='products'>Products</a></div>
		<div><a href='add-product'>Add a Product</a></div>
		<div><a href='orders'>Orders</a></div>
	</nav>
	<main>
		<div id='allProducts'>
		<h1>All Products</h1>
		<?php echo $productHTML; ?>
		</div>
	</main>
</body>