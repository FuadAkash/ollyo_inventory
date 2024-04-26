<?php
//order.php
    session_start();
    include('database_connection.php');

    include('function.php');
    $connect= connect();
    if(!isset($_SESSION['type']))
    {
        header('location:login.php');
    }

    $sql= "SELECT * from inventory_order";
    $res= $connect->query($sql);

    include('header.php');

    if(isset($_POST['btn_action']))
    {
        if($_POST['btn_action'] == 'delete')
        {
            $status = 'inactive';
            $inventory_order_id = $_POST['inventory_order_id'];
            $query = " DELETE FROM inventory_order
                                  WHERE inventory_order_id = '$inventory_order_id'
                                  ";
            if ($connect->query($query) === TRUE) {
                header("Location: order.php");
                $_SESSION['error_message'] = "User Deleted Successfully!";
            }else {
                $_SESSION['error_message'] = "Error deleting user: ";
                header("Location: order.php");
                exit;
            }
        }
    }


    if (isset($_SESSION['error_message'])) {
    $m = $_SESSION['error_message'];
    unset($_SESSION['error_message']); // Clear the error message after displaying it
}
?>

<html>
<head>
    <title>User Table</title>
    <link rel="stylesheet" href="css/datepicker.css">
    <script src="js/bootstrap-datepicker1.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap.min.js"></script>
    <link rel="stylesheet" href="css/dataTables.bootstrap.min.css" />
</head>
<body>
    <span id="alert_action"></span>
    <!-- Display error message if exists -->
    <?php if (!empty($m)) : ?>
        <div class="alert alert-danger"><?php echo $m; ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-lg-10 col-md-10 col-sm-8 col-xs-6">
                            <h3 class="panel-title">Order List</h3>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6" align="right">
                            <button type="button" name="add" id="add_button" data-toggle="modal" data-target="#orderModal" class="btn btn-success btn-xs">Add</button>
                        </div>
                    </div>

                    <div class="clear:both"></div>
                </div>
                <div class="panel-body">
                    <div class="row"><div class="col-sm-12 table-responsive">
                            <table id="order_data" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer Name</th>
                                    <th>Total Amount</th>
                                    <th>Payment Status</th>
                                    <th>Order Date</th>
                                    <?php
                                    if($_SESSION['type'] == 'master') {
                                        echo '<th>Created By</th>';
                                    }
                                    ?>
                                    <th>Action</th>
                                    <th>Delete</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                if(mysqli_num_rows($res) > 0) {
                                    while ($row = mysqli_fetch_assoc($res)) {
                                        echo '<tr>';
                                        echo '<td>'. $row['inventory_order_id'].'</td>';
                                        echo '<td>'. $row['inventory_order_name'].'</td>';
                                        echo '<td>'. $row['inventory_order_total'].'</td>';
                                        echo '<td>'. $row['payment_status'].'</td>';
                                        echo '<td>'. $row['inventory_order_date'].'</td>';
                                        if($_SESSION['type'] == 'master') {
                                            echo '<td>'. $row['inventory_order_created_date'].'</td>';
                                        }

                                        echo "<td><a href='view_order.php?order_id=".$row['inventory_order_id']."' class='btn btn-primary btn-xs'>PDF</a></td>";                                        // Delete button
                                        echo '<td><button type="button" class="btn btn-danger btn-xs delete" id="'. $row['inventory_order_id'] .'" data-status="'. $row['inventory_order_status'] .'">Delete</button></td>';

                                        echo '</tr>';
                                    }
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="orderModal" class="modal fade">
            <div class="modal-dialog">
                <form method="post" action="order_action.php">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title"><i class="fa fa-plus"></i> Create Order</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Enter Receiver Name</label>
                                        <input type="text" name="inventory_order_name" id="inventory_order_name" class="form-control" required />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Date</label>
                                        <input type="text" name="inventory_order_date" id="inventory_order_date" class="form-control" required />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Enter Receiver Address</label>
                                <textarea name="inventory_order_address" id="inventory_order_address" class="form-control" required></textarea>
                            </div>
                            <div class="form-group">
                                <label>Enter Product Details</label>
                                <hr />
                                <div class="row">
                                    <div class="col-md-8">
                                        <select name="product_id[]" id="product_id" class="form-control selectpicker" data-live-search="true" onchange="updateHiddenProductId(this);" required>
                                            <?php echo fill_product_list($connect); ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" name="quantity[]" class="form-control" required />
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" name="add_more" id="add-row" class="btn btn-success btn-xs add-row">+</button>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Select Payment Status</label>
                                    <select name="payment_status" id="payment_status" class="form-control">
                                        <option value="cash">Cash</option>
                                        <option value="credit">Credit</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group" style="text-align: center;">
                                <input type="hidden" name="inventory_order_id" id="inventory_order_id" />
                                <input type="hidden" name="btn_action" id="btn_action" />
                                <button type="submit" value="submit" name="submit" class="btn btn-success">Add</button>
                            </div>
                        </div>
                </form>
            </div>
        </div>
        <script>
            $('#inventory_order_date').datepicker({
                format: "yyyy-mm-dd",
                autoclose: true
            });

            $('.add-row').click(function() {
                var row = $(this).closest('.row').clone();
                // Clear input fields in the cloned row
                row.find('input[type="text"]').val('');
                // Append the cleared row before the current row
                $(this).closest('.row').before(row);
            });

            $(document).ready(function(){
                $(".delete").click(function(){
                    var inventory_order_id = $(this).attr('id');
                    var inventory_order_status = $(this).data('status');

                    $.ajax({
                        url: 'order.php',
                        type: 'POST',
                        data: {
                            btn_action: 'delete',
                            inventory_order_id: inventory_order_id,
                            inventory_order_status: inventory_order_status
                        },
                        success: function(response){
                            // Handle success, you may redirect or show a success message here
                            window.location.href = 'order.php';
                        },
                        error: function(xhr, status, error) {
                            // Handle errors here
                            console.error(xhr.responseText);
                            alert("An error occurred while processing your request.");
                        }
                    });
                });
            });
        </script>
</body>
</html>

<?php
include('footer.php');
?>
