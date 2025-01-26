<?php
include "../config/connection.php";
include "../component/header.php";
include "../component/sidebar.php";
?>

<div class="content-wrapper p-2">
    <div class="card">
        <div class="card-header">
            <div class="text-center p-3">
                <h3 class="font-weight-bold">Most Sold Products</h3>
            </div>
        </div>

        <div class="card-body">
            <?php
            // Display success message
            if (isset($_SESSION["success"])) {
                ?>
                <div class="font-weight-bold alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5 class="font-weight-bold"><i class="icon fas fa-check"></i> Success!</h5>
                    <?= $_SESSION["success"] ?>
                </div>
                <?php
                unset($_SESSION["success"]);
            }
            ?>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product Name</th>
                            <th>Total Sold</th>
                            <th>Total Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $serial_no = 0;

                        // SQL query to fetch most sold products
                        $query = "
                            SELECT 
                                p.product_name, 
                                SUM(oi.quantity) AS total_sold, 
                                SUM(oi.quantity * oi.price) AS total_revenue
                            FROM tbl_order_items oi
                            INNER JOIN tbl_orders o ON oi.order_id = o.order_id
                            INNER JOIN tbl_product p ON oi.product_id = p.product_id
                            WHERE o.order_status = 3
                            GROUP BY oi.product_id
                            ORDER BY total_sold DESC
                            LIMIT 10
                        ";
                        $result = mysqli_query($conn, $query);

                        if (!$result) {
                            echo "<tr><td colspan='4' class='text-center text-danger font-weight-bold'>
                                Error: " . mysqli_error($conn) . "</td></tr>";
                        } else {
                            while ($data = mysqli_fetch_array($result)) {
                                ?>
                                <tr>
                                    <td><?= ++$serial_no ?></td>
                                    <td><?= htmlspecialchars($data["product_name"]) ?></td>
                                    <td><?= htmlspecialchars($data["total_sold"]) ?></td>
                                    <td>₹<?= number_format($data["total_revenue"], 2) ?></td>
                                </tr>
                                <?php
                            }

                            // Display a message if no products were sold
                            if ($serial_no === 0) {
                                ?>
                                <tr>
                                    <td colspan="4" class="font-weight-bold text-center">
                                        <span class="text-danger">No Products Sold.</span>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer">
            <div class="d-flex justify-content-center">
                <a href="downloadMostSoldProducts.php" class="btn btn-info font-weight-bold">
                    <i class="fas fa-download"></i> &nbsp;Download Report
                </a>
            </div>
        </div>
    </div>
</div>

<?php
include "../component/footer.php";
?>
