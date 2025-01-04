<?php
include "../config/connection.php";
include "../component/header.php";
include "../component/sidebar.php";
?>

<div class="content-wrapper p-2">
    <div class="card">
        <div class="card-header">
            <div class="text-center p-3">
                <h3 class="font-weight-bold">Order Management</h3>
            </div>
            <form action="">
                <div class="row justify-content-end">
                    <div class="col-3 font-weight-bold">
                        Order Status
                        <select name="order_status" class="form-control">
                            <option value="">All</option>
                            <option value="1" <?= isset($_GET["order_status"]) && $_GET["order_status"] == "1" ? "selected" : "" ?>>Pending</option>
                            <option value="2" <?= isset($_GET["order_status"]) && $_GET["order_status"] == "2" ? "selected" : "" ?>>Out For Delivery</option>
                            <option value="3" <?= isset($_GET["order_status"]) && $_GET["order_status"] == "3" ? "selected" : "" ?>>Delivered</option>
                            <!-- <option value="4" <?= isset($_GET["order_status"]) && $_GET["order_status"] == "4" ? "selected" : "" ?>>Cancelled</option> -->
                        </select>
                    </div>
                    <div class="col-2 font-weight-bold">
                        Payment Status
                        <select name="payment_status" class="form-control">
                            <option value="">All</option>
                            <option value="1" <?= isset($_GET["payment_status"]) && $_GET["payment_status"] == "1" ? "selected" : "" ?>>Paid</option>
                            <option value="0" <?= isset($_GET["payment_status"]) && $_GET["payment_status"] == "2" ? "selected" : "" ?>>Unpaid</option>
                        </select>
                    </div>
                    <div class="col-2 font-weight-bold">
                        <br>
                        <button type="submit" class="shadow btn w-100 btn-info font-weight-bold">
                            <i class="fas fa-search"></i> &nbsp;Find
                        </button>
                    </div>
                </div>
            </form>
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
                            <th>Order ID</th>
                            <th>Customer Name</th>
                            <th>Order Date</th>
                            <th>Status</th>
                            <th>Total Price</th>
                            <th>Shipping Address</th>
                            <th>Payment Method</th>
                            <th>Payment Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $count = 0;
                        $limit = 10;
                        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                        $offset = ($page - 1) * $limit;

                        $whereConditions = [];
                        if (isset($_GET["order_status"]) && $_GET["order_status"] != "") {
                            $whereConditions[] = "`order_status` = '" . mysqli_real_escape_string($conn, $_GET["order_status"]) . "'";
                        }
                        if (isset($_GET["payment_status"]) && $_GET["payment_status"] != "") {
                            $whereConditions[] = "`payment_status` = '" . mysqli_real_escape_string($conn, $_GET["payment_status"]) . "'";
                        }

                        $whereClause = count($whereConditions) > 0 ? "WHERE " . implode(" AND ", $whereConditions) : "";
                        $countQuery = "SELECT COUNT(*) as total FROM `tbl_orders` INNER JOIN tbl_customer ON tbl_customer.customer_id = tbl_orders.customer_id $whereClause";
                        $selectQuery = "SELECT * FROM `tbl_orders` INNER JOIN tbl_customer ON tbl_customer.customer_id = tbl_orders.customer_id $whereClause LIMIT $limit OFFSET $offset";

                        $countResult = mysqli_query($conn, $countQuery);
                        $totalRecords = mysqli_fetch_assoc($countResult)['total'];
                        $totalPages = ceil($totalRecords / $limit);

                        $result = mysqli_query($conn, $selectQuery);
                        while ($data = mysqli_fetch_array($result)) {
                        ?>
                            <tr>
                                <td><?= $count += 1 ?></td>
                                <td><?= $data["order_id"] ?></td>
                                <td><?= $data["customer_name"] ?></td>
                                <td><?= date("d/m/Y h:i A", strtotime($data["order_date"])) ?></td>

                                <td><?= $data['order_status'] == 1 ? "Pending" : ($data['order_status'] == 2 ? "Out For Delivery" : "Delivered") ?></td>
                                <td>₹<?= number_format($data["total_price"], 2) ?></td>
                                <td><?= $data["shipping_address"] ?></td>
                                <td><?= $data["payment_method"] == 1 ? "Cash On Delivery" : "Online" ?></td>
                                <td><?= $data["payment_status"] == "1" ? '<span class="text-success">Paid</span>' : '<span class="text-danger">Unpaid</span>' ?></td>
                                <td>
                                    <a href="view.php?order_id=<?= $data["order_id"] ?>" class="btn btn-sm shadow btn-info">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    <a href="delete.php?order_id=<?= $data["order_id"] ?>" onclick="if(confirm('Are you sure want to delete this order?')){return true}else{return false;}" class="btn btn-sm shadow btn-danger">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                        <?php
                        if ($count == 0) {
                        ?>
                            <tr>
                                <td colspan="10" class="font-weight-bold text-center">
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
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a class="btn btn-sm btn-outline-info ml-2" href="?page=<?= $page - 1; ?>&order_status=<?= isset($_GET["order_status"]) ? $_GET["order_status"] : ''; ?>&payment_status=<?= isset($_GET["payment_status"]) ? $_GET["payment_status"] : ''; ?>">Previous</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a class="btn btn-sm <?= $page == $i ? "btn-info" : "btn-outline-info" ?> ml-2 shadow" href="?page=<?= $i; ?>&order_status=<?= isset($_GET["order_status"]) ? $_GET["order_status"] : ''; ?>&payment_status=<?= isset($_GET["payment_status"]) ? $_GET["payment_status"] : ''; ?>"><?= $i; ?></a>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <a class="btn btn-sm btn-outline-info ml-2" href="?page=<?= $page + 1; ?>&order_status=<?= isset($_GET["order_status"]) ? $_GET["order_status"] : ''; ?>&payment_status=<?= isset($_GET["payment_status"]) ? $_GET["payment_status"] : ''; ?>">Next</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include "../component/footer.php";
?>
