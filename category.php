<?php
session_start();
include('database_connection.php');
include('header.php');
include('function.php');

$connect = connect();

if (!isset($_SESSION['type'])) {
    header('location:login.php');
}

if ($_SESSION['type'] != 'master') {
    header("location:index.php");
}

$sql = "SELECT * FROM category";
$res = $connect->query($sql);
?>

<span id="alert_action"></span>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="col-lg-10 col-md-10 col-sm-8 col-xs-6">
                    <div class="row">
                        <h3 class="panel-title">Category List</h3>
                    </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
                    <div class="row" align="right">
                        <button type="button" name="add" id="add_button" data-toggle="modal" data-target="#categoryModal" class="btn btn-success btn-xs">Add</button>
                    </div>
                </div>
                <div style="clear:both"></div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-12 table-responsive">
                        <table id="category_data" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Category Name</th>
                                <th>Status</th>
                                <th>Edit</th>
                                <th>Delete</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if ($res->num_rows > 0) {
                                while ($row = $res->fetch_assoc()) {
                                    echo '<tr>';
                                    echo '<td>' . $row['category_id'] . '</td>';
                                    echo '<td>' . $row['category_name'] . '</td>';
                                    echo '<td>' . $row['category_status'] . '</td>';
                                    echo "<td><button type='button' class='btn btn-primary btn-xs edit' data-id='" . $row['category_id'] . "'>Edit</button></td>";
                                    echo '<td><button type="button" class="btn btn-danger btn-xs delete" id="'. $row['category_id'] .'" data-status="'. $row['category_status'] .'">Delete</button></td>';
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
</div>

<div id="categoryModal" class="modal fade">
    <div class="modal-dialog">
        <form method="post" id="category_form">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-plus"></i> Add Category</h4>
                </div>
                <div class="modal-body">
                    <label>Enter Category Name</label>
                    <input type="text" name="category_name" id="category_name" class="form-control" required />
                    <br>
                    <label>Select Status</label>
                    <select name="status" id="status" class="form-control">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="category_id" id="category_id" />
                    <input type="hidden" name="btn_action" id="btn_action" />
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <input type="submit" name="action" id="action" class="btn btn-info" value="Add" />
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        var categorydataTable; // Declare dataTable variable

        // Function to initialize DataTable
        function initializeDataTable() {
            if ($.fn.DataTable.isDataTable('#category_data')) {
                $('#category_data').DataTable().destroy();
            }
            categorydataTable = $('#category_data').DataTable({
                "processing":true,
                "serverSide":true,
                "order":[],
                "ajax":{
                    url:"category_fetch.php",
                    type:"POST"
                },
                "columnDefs":[
                    {
                        "targets":[3, 4],
                        "orderable":false,
                    },
                ],
                "pageLength": 25
            });
        }

        // Call initializeDataTable function to initialize DataTable
        initializeDataTable();

        $('#add_button').click(function() {
            $('#category_form')[0].reset();
            $('.modal-title').html("<i class='fa fa-plus'></i> Add Category");
            $('#action').val('Add');
            $('#btn_action').val('Add');
        });

        $(document).on('submit', '#category_form', function(event) {
            event.preventDefault();
            var form_data = $(this).serialize();
            $.ajax({
                url: "category_action.php",
                method: "POST",
                data: form_data,
                success: function(data) {
                    $('#categoryModal').modal('hide');
                    $('#alert_action').fadeIn().html('<div class="alert alert-success">' + data + '</div>');
                    $('#category_form')[0].reset(); // Clear the form
                    initializeDataTable(); // Reinitialize DataTable
                }
            });
        });

        $(document).on('click', '.edit', function() {
            var category_id = $(this).data("id");
            $('#category_id').val(category_id);
            $('#btn_action').val('fetch_single');
            $.ajax({
                url: "category_action.php",
                method: "POST",
                data: {
                    category_id: category_id,
                    btn_action: 'fetch_single'
                },
                dataType: "json",
                success: function(data) {
                    $('#categoryModal').modal('show');
                    $('#category_name').val(data.category_name);
                    $('#status').val(data.category_status);
                    $('.modal-title').html("<i class='fa fa-pencil-square-o'></i> Edit Category");
                    $('#action').val('Edit');
                }
            });
        });

        $(document).on('click', '.delete', function(){
            var category_id = $(this).attr('id');
            var status = $(this).data("status");
            var btn_action = 'delete';
            if(confirm("Are you sure you want to change status?"))
            {
                $.ajax({
                    url:"category_action.php",
                    method:"POST",
                    data:{category_id:category_id, status:status, btn_action:btn_action},
                    success: function(data) {
                        $('#categoryModal').modal('hide');
                        $('#alert_action').fadeIn().html('<div class="alert alert-success">' + data + '</div>');
                        $('#category_form')[0].reset(); // Clear the form
                        initializeDataTable(); // Reinitialize DataTable
                    }
                })
            }
            else
            {
                return false;
            }
        });
    });
</script>



<?php
include('footer.php');
?>
