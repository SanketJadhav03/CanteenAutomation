<?php
include "../config/connection.php";
include "../component/header.php";
include "../component/sidebar.php";

$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : null;

if (!$order_id) {
    $_SESSION['error'] = 'Order ID is missing!';
    header("Location: index.php");
    exit;
}

// Fetch order details
$query = "SELECT * FROM tbl_orders WHERE order_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $order_id);
$stmt->execute();
$order_result = $stmt->get_result();
$order = $order_result->fetch_assoc();

if (!$order) {
    $_SESSION['error'] = 'Order not found!';
    header("Location: index.php");
    exit;
}

// Fetch products for the order from tbl_order_items
$product_query = "SELECT *
                  FROM tbl_order_items INNER JOIN tbl_product ON tbl_product.product_id = tbl_order_items.product_id 
                  WHERE order_id = ?";
$product_stmt = $conn->prepare($product_query);
$product_stmt->bind_param('i', $order_id);
$product_stmt->execute();
$product_result = $product_stmt->get_result();
?>

<div class="content-wrapper p-3">
    <div class="card shadow border-0">
        <div class="card-header bg-">
            <h3 class="text-center font-weight-bold mb-0">Order Details - <span class="text-warning">#<?= htmlspecialchars($order['order_id']) ?></span></h3>
        </div>
        <div class="card-body">
            <?php if (isset($_SESSION['success'])) { ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $_SESSION['success'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php unset($_SESSION['success']);
            } ?>
            <?php if (isset($_SESSION['error'])) { ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $_SESSION['error'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php unset($_SESSION['error']);
            } ?>

            <div class="row mb-4">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th>Order ID</th>
                            <td><?= htmlspecialchars($order['order_id']) ?></td>
                        </tr>
                        <tr>
                            <th>Customer ID</th>
                            <td><?= htmlspecialchars($order['customer_id']) ?></td>
                        </tr>
                        <tr>
                            <th>Order Date</th>
                            <td><?= date("d/m/Y h:i A", strtotime($order['order_date'])) ?></td>
                        </tr>
                        <tr>
                            <th>Total Price</th>
                            <td>&#8377; <?= number_format($order['total_price'], 2) ?></td>
                        </tr>
                        <tr>
                            <th>Shipping Address</th>
                            <td><?= htmlspecialchars($order['shipping_address']) ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th>Payment Method</th>
                            <td><?= htmlspecialchars($order['payment_method']) == "1" ? "Cash On Delivery" : "Online" ?></td>
                        </tr>
                        <tr>
                            <th>Payment Status</th>
                            <td><?= htmlspecialchars($order['payment_status']) == 1 ? "<span class='text-success font-weight-bold'>Paid</span>"  : "<span class='text-danger font-weight-bold'>Unpaid</span>" ?></td>
                        </tr>
                        <tr>
                            <th>Order Status</th>
                            <td><?= htmlspecialchars($order['order_status']) == 1 ? "Pending" : ($order['order_status'] == 2 ? "Out For Delivery" : "Delivered") ?></td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td><?= date("d/m/Y h:i A", strtotime($order['created_at'])) ?></td>
                        </tr>
                        <tr>
                            <th>Updated At</th>
                            <td><?= date("d/m/Y h:i A", strtotime($order['updated_at'])) ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Display products -->
            <div class="mt-4">
                <h4>Products in Order</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($product = $product_result->fetch_assoc()) { ?>
                            <tr>
                                <td><?= htmlspecialchars($product['product_name']) ?></td>
                                <td><?= htmlspecialchars($product['quantity']) ?></td>
                                <td>&#8377; <?= number_format($product['price'], 2) ?></td>
                                <td>&#8377; <?= number_format($product['quantity'] * $product['price'], 2) ?></td>
                                <td><?= date("d/m/Y h:i A", strtotime($product['created_at'])) ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div class="text-right mt-3">
                <a href="sales_report.php" class="mx-2 btn btn-success shadow"> <i class="fas fa-arrow-left"></i> Back</a>
            </div>
        </div>
    </div>
</div>

<?php include "../component/footer.php"; ?>
