<?php

//category_action.php

session_start();
include('database_connection.php');

$connect =connect();

if(isset($_POST['btn_action']))
{
	if($_POST['btn_action'] == 'Add')
	{
        $category = $_POST['category_name'];
        $status = $_POST['status'];
		$query = "
		INSERT INTO category (category_name, category_status) 
		VALUES ('$category','$status')
		";
		if($statement = $connect->query($query)=== true)
		{
			echo 'Category Name Added';
		}
	}
	
	if($_POST['btn_action'] == 'fetch_single')
	{
		$query = "SELECT * FROM category WHERE category_id = :category_id";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':category_id'	=>	$_POST["category_id"]
			)
		);
		$result = $statement->fetchAll();
		foreach($result as $row)
		{
			$output['category_name'] = $row['category_name'];
		}
		echo json_encode($output);
	}

	if($_POST['btn_action'] == 'Edit')
	{
		$query = "
		UPDATE category set category_name = :category_name  
		WHERE category_id = :category_id
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':category_name'	=>	$_POST["category_name"],
				':category_id'		=>	$_POST["category_id"]
			)
		);
		$result = $statement->fetchAll();
		if(isset($result))
		{
			echo 'Category Name Edited';
		}
	}

    if($_POST['btn_action'] == 'delete')
    {
        $id= $_POST['category_id'];
        $query = "
		DELETE FROM category  
		WHERE category_id = '$id'
		";

        if ($connect->query($query) === TRUE) {
            header("Location: category.php");
            $_SESSION['error_message'] = "Category Inactive Successfully!";
        }else {
            $_SESSION['error_message'] = "Error deleting Category: ";
            header("Location: category.php");
            exit;
        }
    }
}

?>