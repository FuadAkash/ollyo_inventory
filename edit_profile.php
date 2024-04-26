<?php
session_start();
include('database_connection.php');
include('function.php');
$connect = connect();
$userid = $_SESSION['id'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['current_pass'])) {
        $message = '<div class="alert alert-danger">Current Password Needed!</div>';
    } else {
        $c_pass = $_POST['current_pass'];
        $sql = "SELECT * FROM user_details WHERE user_id='$userid'";
        $res = $connect->query($sql);

        if (mysqli_num_rows($res) == 1) {
            $user = mysqli_fetch_assoc($res);
            if (password_verify($c_pass, $user["user_password"])) {
                if (isset($_POST['user_name'])) {
                    if ($_POST["user_new_password"] != '') {
                        $query = "
                            UPDATE user_details SET 
                                user_name = '".$_POST["user_name"]."', 
                                user_email = '".$_POST["user_email"]."', 
                                user_password = '".password_hash($_POST["user_new_password"], PASSWORD_DEFAULT)."' 
                            WHERE user_id = '".$_SESSION["id"]."'
                        ";
                    } else {
                        $query = "
                            UPDATE user_details SET 
                                user_name = '".$_POST["user_name"]."', 
                                user_email = '".$_POST["user_email"]."'
                            WHERE user_id = '".$_SESSION["id"]."'
                        ";
                    }
                    $statement = $connect->prepare($query);
                    $statement->execute();
                    $affected_rows = $statement->affected_rows;
                    if ($affected_rows > 0) {
                        $message = '<div class="alert alert-success">Profile Edited</div>';
                    }
                }
            } else {
                $message = '<div class="alert alert-danger">Password mismatch!</div>';
            }
        } else {
            $message = '<div class="alert alert-danger">Credentials mismatch!</div>';
        }
    }
} else {
    $message = '<div class="alert alert-danger">Invalid request!</div>';
}

echo $message;
?>
