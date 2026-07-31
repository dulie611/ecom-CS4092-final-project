<?php
REQUIRE "config.php";

?>
<!DOCTYPE html>
<html>
<head>
<title>Homepage</title>
<style>
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
</body>