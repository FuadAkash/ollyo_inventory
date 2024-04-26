<?php

    // user_action.php
    session_start();
    include('database_connection.php');

    $connect= connect();
    if(isset($_POST['btn_action']))
    {
        if($_POST['btn_action'] == 'Add')
        {
            $uname = $_POST['user_name'];
            $uemail = $_POST['user_email'];
            $pass = $_POST["user_password"];
            $rPass = $_POST['r_pass'];
            $type = 'user';
            $status = 'active';

            // Check if passwords match
            if($pass === $rPass) {
                // Hash the password
                $pass = password_hash($pass, PASSWORD_DEFAULT);

                // Check if the email already exists
                $emailExistsQuery = "SELECT COUNT(*) AS count FROM user_details WHERE user_email = '$uemail'";
                $result = $connect->query($emailExistsQuery);
                $row = $result->fetch_assoc();
                $emailCount = $row['count'];

                if($emailCount > 0) {
                    $_SESSION['error_message'] = 'Email already exists!';
                    header('Location: user.php');
                    exit;
                } else {
                    // Insert the user into the database
                    $query = "INSERT INTO user_details (user_name, user_email, user_password, user_type, user_status) 
                    VALUES ('$uname', '$uemail', '$pass', '$type', '$status')";

                    if($connect->query($query) === true) {
                        header('Location: user.php');
                        $_SESSION['error_message'] = 'User Created successfully';
                        exit;
                    } else {
                        $_SESSION['error_message'] = 'Connection not established!';
                        header('Location: user.php');
                        exit;
                    }
                }
            } else {
                $_SESSION['error_message'] = 'Password do not match';
                header('Location: user.php');
                exit;
            }
        }
        if($_POST['btn_action'] == 'delete')
        {
            $id= $_POST['user_id'];
            $sql= "DELETE FROM user_details WHERE user_id='$id' limit 1";
            if ($connect->query($sql) === TRUE) {
                header("Location: user.php");
                $_SESSION['error_message'] = "User Deleted Successfully!";
            }else {
                $_SESSION['error_message'] = "Error deleting user: ";
                header("Location: user.php");
                exit;
            }
        }
    }

?>

