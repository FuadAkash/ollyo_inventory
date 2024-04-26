<?php
session_start();
include ('database_connection.php');
include('header.php');
$conn = connect();

$res = []; // Initialize $res to avoid undefined variable warning

if(isset($_GET['id'])){
    $id = $_GET['id'];
    $sql = "SELECT * FROM user_details WHERE user_id='$id' LIMIT 1";
    $res = mysqli_fetch_assoc($conn->query($sql));
} else if(isset($_POST['Submit'])) {
    $id = $_POST['user_id']; // Add this line to get the user id from the form
    $user_name = $_POST['user_name'];
    $user_email = $_POST['user_email'];
    if ($_POST["user_new_password"] != '') {
        $update_sql = "
                UPDATE user_details SET 
                    user_name = '".$_POST["user_name"]."', 
                    user_email = '".$_POST["user_email"]."', 
                    user_password = '".password_hash($_POST["user_new_password"], PASSWORD_DEFAULT)."' 
                WHERE user_id = '$id'
            ";
    } else {
        $update_sql = "
                UPDATE user_details SET 
                    user_name = '".$_POST["user_name"]."', 
                    user_email = '".$_POST["user_email"]."'
                WHERE user_id = '$id'
            ";
    }
    if ($conn->query($update_sql) === TRUE) {
        echo "Record updated successfully";
        // Redirect to a page after successful update
        header("Location: user.php");
        exit;
    } else {
        echo "Error updating record: " . $conn->error;
    }
} else {
    echo "User not found";
    exit;
}
?>

<div class="panel panel-default">
    <div class="panel-heading">Edit Profile</div>
    <div class="panel-body">
        <form method="POST" action="useredit.php">
            <span id="message"></span>
            <input type="hidden" name="user_id" value="<?php echo $id; ?>"> <!-- Add this line to pass user id -->
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="user_name" id="user_name" class="form-control" value="<?php echo isset($res['user_name']) ? $res['user_name'] : ''; ?>" required />
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="user_email" id="user_email" class="form-control" required value="<?php echo isset($res['user_email']) ? $res['user_email'] : ''; ?>" />
            </div>
            <hr />
            <label>Leave Password blank if you do not want to change</label>
            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="user_new_password" id="user_new_password" class="form-control" />
            </div>
            <div class="form-group">
                <label>Re-enter Password</label>
                <input type="password" name="user_re_enter_password" id="user_re_enter_password" class="form-control" />
                <span id="error_password"></span>
            </div>
            <div class="row">
                <div class="text-center">
                    <input class="btn btn-success" type="submit" name="Submit" value="Submit">
                </div>
            </div>
        </form>
    </div>
</div>
