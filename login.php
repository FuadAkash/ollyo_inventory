<?php
    session_start(); // Add this line
    $_SESSION['type'] = '';
    $_SESSION['id'] = '';
    $_SESSION['name'] = '';
    include "database_connection.php";
    $connect= connect();
    $message = '';
    if(isset($_POST['login'])){
        $uName= $_POST['user_email'];
        $sql= "SELECT * FROM user_details WHERE user_email='$uName'";
        $res= $connect->query($sql);

        if(mysqli_num_rows($res)==1){
            $user= mysqli_fetch_assoc($res);
            if(password_verify($_POST["user_password"], $user["user_password"])) {

                $_SESSION['type'] = $user['user_type'];
                $_SESSION['id'] = $user['user_id'];
                $_SESSION['name'] = $user['user_name'];
                header('Location: index.php');
                exit;
            }
            else{
                $message = 'Password mismatch!';
            }
        }
        else{
            $message = 'Credentials mismatch!';
        }
    }
?>


<!DOCTYPE html>
<html>
	<head>
		<title>Inventory Management System using PHP with Ajax Jquery</title>		
		<script src="js/jquery-1.10.2.min.js"></script>
		<link rel="stylesheet" href="css/bootstrap.min.css" />
		<script src="js/bootstrap.min.js"></script>
	</head>
	<body>
		<br />
		<div class="container">
			<h2 align="center">Inventory Management System using PHP with Ajax Jquery</h2>
			<br />
			<div class="panel panel-default">
				<div class="panel-heading">Login</div>
				<div class="panel-body">
					<form method="POST">
						<?php echo $message; ?>
						<div class="form-group">
							<label>User Email</label>
							<input type="text" name="user_email" class="form-control" required />
						</div>
						<div class="form-group">
							<label>Password</label>
							<input type="password" name="user_password" class="form-control" required />
						</div>
						<div class="form-group">
							<input type="submit" name="login" value="Login" class="btn btn-info" />
						</div>
					</form>
				</div>
			</div>
		</div>
	</body>
</html>