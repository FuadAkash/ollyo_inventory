<?php
    $user= $_SESSION['name'];
    $userid= $_SESSION['id'];
    if(!$_SESSION['id']){
        header("Location: login.php");
    }
    $sq= "SELECT * FROM user_details WHERE id='$userid'";
    //$thisUser= mysqli_fetch_assoc($conn->query($sq));

    //$conn->close();
?>


<!DOCTYPE html>
<html>
	<head>
		<title>Inventory Management System</title>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
		<link rel="stylesheet" href="css/bootstrap.min.css" />
        <script src="js/jquery.dataTables.min.js"></script>
        <script src="js/dataTables.bootstrap.min.js"></script>
        <link rel="stylesheet" href="css/dataTables.bootstrap.min.css" />
		<script src="js/bootstrap.min.js"></script>
    </head>
	<body>
		<br />
		<div class="container">
			<h2 align="center">Inventory Management System</h2>

			<nav class="navbar navbar-inverse">
				<div class="container-fluid">
					<div class="navbar-header">
						<a href="index.php" class="navbar-brand">Home</a>
					</div>
					<ul class="nav navbar-nav">
					<?php
					if($_SESSION['type'] == 'master')
					{
					?>
						<li><a href="user.php">User</a></li>
						<li><a href="category.php">Category</a></li>
						<li><a href="brand.php">Brand</a></li>
						<li><a href="product.php">Product</a></li>
					<?php
					}
					?>
						<li><a href="order.php">Order</a></li>
					</ul>
					<ul class="nav navbar-nav navbar-right">
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="label label-pill label-danger count"></span> <?php echo $user; ?></a>
							<ul class="dropdown-menu">
								<li><a href="profile.php">Profile</a></li>
								<li><a href="logout.php">Logout</a></li>
							</ul>
						</li>
					</ul>

				</div>
			</nav>

			