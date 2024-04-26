<?php

    //order_action.php

    session_start();

    include('database_connection.php');

    include('function.php');

    $connect = connect();

    if (isset($_POST['submit'])) {
        $id = $_SESSION["id"];
        $order_date = $_POST['inventory_order_date'];
        $order_name = $_POST['inventory_order_name'];
        $order_address = $_POST['inventory_order_address'];
        $payment = $_POST['payment_status'];
        $order_status = 'active';
        $created_date = date("Y-m-d");
        $query = "INSERT INTO inventory_order (user_id, inventory_order_date, inventory_order_name, inventory_order_address, payment_status, inventory_order_status, inventory_order_created_date)
                      VALUES ('$id', '$order_date', '$order_name', '$order_address', '$payment', '$order_status', '$created_date')";

        if ($connect->query($query) === true) {
            $statement = $connect->query("SELECT LAST_INSERT_ID()");
            $row = $statement->fetch_assoc(); // Fetch the row
            $inventory_order_id = $row['LAST_INSERT_ID()']; // Retrieve the last insert ID

            if (isset($inventory_order_id)) {
                $total_amount = 0;
                for ($count = 0; $count < count($_POST["product_id"]); $count++) {
                    $product_details = fetch_product_details($_POST["product_id"][$count], $connect);

                    if ($product_details === NULL) {
                        $_SESSION['error_message'] = 'Product Cannot Be Found!';
                        header('Location: order.php');
                        exit;
                    }
                    $order_id = $inventory_order_id;
                    $product_id = $_POST["product_id"][$count];
                    $quantity = $_POST["quantity"][$count];
                    $price = $product_details['price'];
                    $tax = $product_details['tax'];

                    $sub_query = "INSERT INTO inventory_order_product (inventory_order_id, product_id, quantity, price, tax)
                    VALUES ('$order_id', '$product_id', '$quantity', '$price', '$tax')";
                    if ($connect->query($sub_query) === false) {
                        echo "Error: " . $connect->error; // Output any errors that occur
                        exit; // Terminate the script if there's an error
                    }else{
                        $base_price = $product_details['price'] * $_POST["quantity"][$count];
                        $tax = ($base_price / 100) * $product_details['tax'];
                        $total_amount = $total_amount + ($base_price + $tax);
                    }

                }
                $update_query = "UPDATE inventory_order SET inventory_order_total = '".$total_amount."'
                                    WHERE inventory_order_id = '".$inventory_order_id."'";
                if ($connect->query($update_query) === true) {
                    $_SESSION['error_message'] = 'Order Creation Successful!';
                    header('Location: order.php');
                    exit;
                }
            } else {
                $_SESSION['error_message'] = 'Inventory Order ID NOT FOUND!';
                header('Location: order.php');
                exit;
            }
        } else {
            $_SESSION['error_message'] = 'Inventory Order Cannot Be Created!';
            header('Location: order.php');
            exit;
        }
    } else {
        $_SESSION['error_message'] = 'Invalid Request!';
        header('Location: order.php');
        exit;
    }

?>