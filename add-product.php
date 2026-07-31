<?php
REQUIRE "config.php";
$dbh = new PDO($dsn, $user, $pw);

if(ISSET($_POST['newProduct']) && $_POST['newProduct'] == "Add Product"){
	$pname = $_POST['name'];
	$pdesc = $_POST['desc'];
	$pprice = $_POST['price'];
	$pstock = $_POST['stock'];
	
	$newProductQ = "INSERT INTO products (product_name, product_desc, price, stock) VALUES (?, ?, ?, ?);";
	$newProductSta = $dbh->prepare($newProductQ);
	$newProductSta->bindParam(1, $pname, PDO::PARAM_STR);
	$newProductSta->bindParam(2, $pdesc, PDO::PARAM_STR);
	$newProductSta->bindParam(3, $pprice, PDO::PARAM_INT);
	$newProductSta->bindParam(4, $pstock, PDO::PARAM_INT);
	$newProductSta->execute();
}


?>
<!DOCTYPE html>
<html>
<head>
<title>Add Product</title>
<style>
body{
	margin:0;
	font-family:monospace;
}
#formCont{
	width:fit-content;
	margin:0 auto;
	margin-top:64px;
}
form{
	width:fit-content;
	border:1px solid #000;
	padding:16px 32px;
	font-size:16px!important;
}
form input{
	font-size:16px;
}
form div{
	display:flex;
	justify-content:space-between;
	align-items:center;
	margin-bottom:8px;
}
form div label{
	display:inline-block;
	margin-right:16px;
}
#submitRow{
	margin-top:16px;
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
	<div id='formCont'>
	<h1>Add a Product</h1>
		<form id='addProduct' method='POST' action=''>
			<div><label for='pname'>Name: </label><input id='pname' name='name' type='text' required></div>
			<div><label for='pdesc'>Description: </label><input id='pdesc' name='desc' type='text' required></div>
			<div><label for='pprice'>Price: </label><input id='pprice' name='price' type='number' required></div>
			<div><label for='pstock'>Stock: </label><input id='pstock' name='stock' type='number' required></div>
			<div id='submitRow'><input name='newProduct' type='submit' value='Add Product'></div>
		</form>
	</div>
</main>
</body>