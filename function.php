<?php
//function.php

function fill_category_list($connect)
{
	$query = "
	SELECT * FROM category 
	WHERE category_status = 'active' 
	ORDER BY category_name ASC
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	$output = '';
	foreach($result as $row)
	{
		$output .= '<option value="'.$row["category_id"].'">'.$row["category_name"].'</option>';
	}
	return $output;
}

function fill_brand_list($connect, $category_id)
{
	$query = "SELECT * FROM brand 
	WHERE brand_status = 'active' 
	AND category_id = '".$category_id."'
	ORDER BY brand_name ASC";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	$output = '<option value="">Select Brand</option>';
	foreach($result as $row)
	{
		$output .= '<option value="'.$row["brand_id"].'">'.$row["brand_name"].'</option>';
	}
	return $output;
}

function get_user_name($connect, $user_id)
{
	$query = "
	SELECT user_name FROM user_details WHERE user_id = '".$user_id."'
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		return $row['user_name'];
	}
}

function fill_product_list($connect)
{
	$query = "
	SELECT * FROM product 
	WHERE product_status = 'active' 
	ORDER BY product_name ASC
	";
	$result = $connect->query($query);
	$output = '';
    if(mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $output .= '<option value="' . $row["product_id"] . '">' . $row["product_name"] . '</option>';
        }
    }
	return $output;
}

function fetch_product_details($product_id, $connect)
{
    $query = "SELECT * FROM product WHERE product_id = '".$product_id."'";
    $statement = $connect->query($query);

    // Check if the query executed successfully
    if (!$statement) {
        return NULL; // Return NULL if query failed
    }

    // Fetch the row as an associative array
    $result = $statement->fetch_assoc();

    // Check if result is empty
    if (!$result) {
        return NULL; // Return NULL if no result found
    }

    // Extract the relevant data from the result
    $output['product_name'] = $result["product_name"];
    $output['quantity'] = $result["product_quantity"];
    $output['price'] = $result['product_base_price'];
    $output['tax'] = $result['product_tax'];

    return $output;
}


function available_product_quantity($connect, $product_id)
{
	$product_data = fetch_product_details($product_id, $connect);
	$query = "
	SELECT 	inventory_order_product.quantity FROM inventory_order_product 
	INNER JOIN inventory_order ON inventory_order.inventory_order_id = inventory_order_product.inventory_order_id
	WHERE inventory_order_product.product_id = '".$product_id."' AND
	inventory_order.inventory_order_status = 'active'
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	$total = 0;
	foreach($result as $row)
	{
		$total = $total + $row['quantity'];
	}
	$available_quantity = intval($product_data['quantity']) - intval($total);
	if($available_quantity == 0)
	{
		$update_query = "
		UPDATE product SET 
		product_status = 'inactive' 
		WHERE product_id = '".$product_id."'
		";
		$statement = $connect->prepare($update_query);
		$statement->execute();
	}
	return $available_quantity;
}

function count_total_user($connect)
{
    try {
        $query = "SELECT * FROM user_details WHERE user_status='active'";
        $statement = $connect->prepare($query);
        $statement->execute();
        $statement->store_result();
        return $statement->num_rows;
    } catch (PDOException $e) {
        // Log or display the error message
        echo "Error: " . $e->getMessage();
        return false; // or handle the error in another way
    }
}



function count_total_category($connect)
{
    try {
        $query = "SELECT * FROM category WHERE category_status='active'";
        $statement = $connect->prepare($query);
        $statement->execute();
        $statement->store_result();
        return $statement->num_rows;
    } catch (PDOException $e) {
        // Log or display the error message
        echo "Error: " . $e->getMessage();
        return false; // or handle the error in another way
    }
}

function count_total_brand($connect)
{
    try {
        $query = "SELECT * FROM brand WHERE brand_status='active'";
        $statement = $connect->prepare($query);
        $statement->execute();
        $statement->store_result();
        return $statement->num_rows;
    } catch (PDOException $e) {
        // Log or display the error message
        echo "Error: " . $e->getMessage();
        return false; // or handle the error in another way
    }
}

function count_total_product($connect)
{
    try {
        $query = "SELECT * FROM product WHERE product_status='active'";
        $statement = $connect->prepare($query);
        $statement->execute();
        $statement->store_result();
        return $statement->num_rows;
    } catch (PDOException $e) {
        // Log or display the error message
        echo "Error: " . $e->getMessage();
        return false; // or handle the error in another way
    }
}

function count_total_order_value($connect)
{
    // Check if 'user_id' is set in $_SESSION
    if(isset($_SESSION['user_id']) && $_SESSION['type'] == 'user')
    {
        $user_id = $_SESSION["user_id"];
        $query = "
            SELECT sum(inventory_order_total) as total_order_value 
            FROM inventory_order 
            WHERE inventory_order_status='active' AND user_id = ?";
        $statement = $connect->prepare($query);
        $statement->bind_param("s", $user_id);
    }
    else
    {
        $query = "
            SELECT sum(inventory_order_total) as total_order_value 
            FROM inventory_order 
            WHERE inventory_order_status='active'";
        $statement = $connect->prepare($query);
    }

    $statement->execute();
    $result = $statement->get_result();
    $row = $result->fetch_assoc();
    return number_format($row['total_order_value'], 2);
}



function count_total_cash_order_value($connect)
{
    // Check if 'user_id' is set in $_SESSION
    if(isset($_SESSION['user_id']) && $_SESSION['type'] == 'user')
    {
        $user_id = $_SESSION["user_id"];
        $query = "
            SELECT sum(inventory_order_total) as total_order_value 
            FROM inventory_order 
            WHERE payment_status = 'cash' 
            AND inventory_order_status='active' 
            AND user_id = ?";
        $statement = $connect->prepare($query);
        $statement->bind_param("s", $user_id);
    }
    else
    {
        $query = "
            SELECT sum(inventory_order_total) as total_order_value 
            FROM inventory_order 
            WHERE payment_status = 'cash' 
            AND inventory_order_status='active'";
        $statement = $connect->prepare($query);
    }

    $statement->execute();
    $result = $statement->get_result();
    $row = $result->fetch_assoc();
    return number_format($row['total_order_value'], 2);
}


function count_total_credit_order_value($connect)
{
    // Check if 'user_id' is set in $_SESSION
    if(isset($_SESSION['user_id']) && $_SESSION['type'] == 'user')
    {
        $user_id = $_SESSION["user_id"];
        $query = "
            SELECT sum(inventory_order_total) as total_order_value 
            FROM inventory_order 
            WHERE payment_status = 'credit' 
            AND inventory_order_status='active' 
            AND user_id = ?";
        $statement = $connect->prepare($query);
        $statement->bind_param("s", $user_id);
    }
    else
    {
        $query = "
            SELECT sum(inventory_order_total) as total_order_value 
            FROM inventory_order 
            WHERE payment_status = 'credit' 
            AND inventory_order_status='active'";
        $statement = $connect->prepare($query);
    }

    $statement->execute();
    $result = $statement->get_result();
    $row = $result->fetch_assoc();
    return number_format($row['total_order_value'], 2);
}


function get_user_wise_total_order($connect)
{
    $query = '
	SELECT sum(inventory_order.inventory_order_total) as order_total, 
	SUM(CASE WHEN inventory_order.payment_status = "cash" THEN inventory_order.inventory_order_total ELSE 0 END) AS cash_order_total, 
	SUM(CASE WHEN inventory_order.payment_status = "credit" THEN inventory_order.inventory_order_total ELSE 0 END) AS credit_order_total, 
	user_details.user_name 
	FROM inventory_order 
	INNER JOIN user_details ON user_details.user_id = inventory_order.user_id 
	WHERE inventory_order.inventory_order_status = "active" GROUP BY inventory_order.user_id
	';
    $statement = $connect->prepare($query);
    $statement->execute();
    $result = $statement->get_result();
    $output = '
	<div class="table-responsive">
		<table class="table table-bordered table-striped">
			<tr>
				<th>User Name</th>
				<th>Total Order Value</th>
				<th>Total Cash Order</th>
				<th>Total Credit Order</th>
			</tr>
	';

    $total_order = 0;
    $total_cash_order = 0;
    $total_credit_order = 0;
    while($row = $result->fetch_assoc())
    {
        $output .= '
		<tr>
			<td>'.$row['user_name'].'</td>
			<td align="right">$ '.$row["order_total"].'</td>
			<td align="right">$ '.$row["cash_order_total"].'</td>
			<td align="right">$ '.$row["credit_order_total"].'</td>
		</tr>
		';

        $total_order = $total_order + $row["order_total"];
        $total_cash_order = $total_cash_order + $row["cash_order_total"];
        $total_credit_order = $total_credit_order + $row["credit_order_total"];
    }
    $output .= '
	<tr>
		<td align="right"><b>Total</b></td>
		<td align="right"><b>$ '.$total_order.'</b></td>
		<td align="right"><b>$ '.$total_cash_order.'</b></td>
		<td align="right"><b>$ '.$total_credit_order.'</b></td>
	</tr></table></div>
	';
    return $output;
}


?>