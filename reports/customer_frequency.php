<?php
include "../config/connection.php";
include "../component/header.php";
include "../component/sidebar.php";
?>

<div class="content-wrapper p-2">
    <div class="card">
        <div class="card-header">
            <div class="text-center p-3">
                <h3 class="font-weight-bold">Customer Purchase Frequency</h3>
            </div>
        </div>

        <div class="card-body">
            <?php
            if (isset($_SESSION["success"])) {
            ?>
                <div class="font-weight-bold alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5 class="font-weight-bold "><i class="icon fas fa-check"></i> Success!</h5>
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
                            <th>Customer Name</th>
                            <th>Total Orders</th>
                            <th>Last Order Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $count = 0;

                        // Query to fetch customer frequency based on orders
                        $query = "
                            SELECT o.customer_id, c.customer_name, COUNT(o.order_id) AS total_orders, MAX(o.order_date) AS last_order_date
                            FROM tbl_orders o
                            INNER JOIN tbl_customer c ON o.customer_id = c.customer_id
                            WHERE o.order_status = 3
                            GROUP BY o.customer_id
                            ORDER BY total_orders DESC
                            LIMIT 10
                        ";
                        $result = mysqli_query($conn, $query);

                        while ($data = mysqli_fetch_array($result)) {
                        ?>
                            <tr>
                                <td><?= $count += 1 ?></td>
                                <td><?= htmlspecialchars($data["customer_name"]) ?></td>
                                <td><?= htmlspecialchars($data["total_orders"]) ?></td>
                                <td><?= date("d/m/Y", strtotime($data["last_order_date"])) ?></td>
                            </tr>
                        <?php
                        }
                        ?>

                        <?php
                        if ($count == 0) {
                        ?>
                            <tr>
                                <td colspan="4" class="font-weight-bold text-center">
                                    <span class="text-danger">No Orders Found.</span>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer">
            <div class="d-flex justify-content-center">
                <a href="downloadCustomerFrequencyReport.php" class="btn btn-info font-weight-bold">
                    <i class="fas fa-download"></i> &nbsp;Download Report
                </a>
            </div>
        </div>
    </div>
</div>

<?php
include "../component/footer.php";
?>
