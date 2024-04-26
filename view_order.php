<?php
//view_order.php
session_start();
require_once 'pdf.php';
if(isset($_GET['order_id'])) {
    include('database_connection.php');
    include('function.php');
    if(!isset($_SESSION['type'])) {
        header('location:login.php');
    }
    $connect = connect();
    $output = '';
    $pdfid = '';
    $inventory_order_id = $_GET['order_id'];
    $statement = "SELECT * FROM inventory_order WHERE inventory_order_id = '$inventory_order_id' LIMIT 1";
    $result = $connect->query($statement);

    if($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $pdfid = $row["inventory_order_id"];
            $output .= '<table width="100%" border="1" cellpadding="5" cellspacing="0">
                            <tr>
                                <td colspan="2" align="center" style="font-size:18px"><b>Invoice</b></td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                <table width="100%" cellpadding="5">
                                    <tr>
                                        <td width="65%">
                                            To,<br />
                                            <b>RECEIVER (BILL TO)</b><br />
                                            Name : '.$row["inventory_order_name"].'<br />    
                                            Billing Address : '.$row["inventory_order_address"].'<br />
                                        </td>
                                        <td width="35%">
                                            Reverse Charge<br />
                                            Invoice No. : '.$row["inventory_order_id"].'<br />
                                            Invoice Date : '.$row["inventory_order_date"].'<br />
                                        </td>
                                    </tr>
                                </table>
                                <br />
                                <table width="100%" border="1" cellpadding="5" cellspacing="0">
                                    <tr>
                                        <th>Sr No.</th>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Actual Amt.</th>
                                        <th>Tax (%)</th>
                                        <th>Total</th>
                                    </tr>';

            $statement = "SELECT * FROM inventory_order_product WHERE inventory_order_id = $inventory_order_id";
            $product_result = $connect->query($statement);

            if($product_result && $product_result->num_rows > 0) {
                $count = 0;
                $total = 0;
                $total_actual_amount = 0;
                $total_tax_amount = 0;

                while($sub_row = $product_result->fetch_assoc()) {
                    $count++;
                    $product_data = fetch_product_details($sub_row['product_id'], $connect);
                    $actual_amount = $sub_row["quantity"] * $sub_row["price"];
                    $tax_amount = ($actual_amount * $sub_row["tax"]) / 100;
                    $total_product_amount = $actual_amount + $tax_amount;
                    $total_actual_amount += $actual_amount;
                    $total_tax_amount += $tax_amount;
                    $total += $total_product_amount;

                    $output .= '
                        <tr>
                            <td>'.$count.'</td>
                            <td>'.$product_data['product_name'].'</td>
                            <td>'.$sub_row["quantity"].'</td>
                            <td>'.$sub_row["price"].'</td>
                            <td>'.number_format($actual_amount, 2).'</td>
                            <td>'.$sub_row["tax"].'%</td>
                            <td>'.number_format($total_product_amount, 2).'</td>
                        </tr>';
                }
            }

            $output .= '<tr>
                                <td colspan="5" align="right">Total Actual Amount:</td>
                                <td>'.number_format($total_actual_amount, 2).'</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="5" align="right">Total Tax Amount:</td>
                                <td>'.number_format($total_tax_amount, 2).'</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="5" align="right">Grand Total:</td>
                                <td>'.number_format($total, 2).'</td>
                                <td></td>
                            </tr>
                        </table>
                            <br />
                            <br />
                            <br />
                            <br />
                            <br />
                            <br />
                            <p align="right">----------------------------------------
                            <br />Receiver Signature</p>
                            <br />
                            <br />
                            <br />
                            </td>
                            </tr>
                            </table>
                            ';

        }
    }
    $pdf = new \Dompdf\Dompdf();
    $file_name = 'Order_' . $pdfid . '.pdf';

    $pdf->loadHtml($output);
    $pdf->setPaper('A4', 'landscape');
    $pdf->render();
    $pdf->stream($file_name, array("Attachment" => false));
}
?>
