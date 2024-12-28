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

// Update payment_status and order_status
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_status = $_POST['payment_status'];
    $order_status = $_POST['order_status'];

    $update_query = "UPDATE tbl_orders SET payment_status = ?, order_status = ?, updated_at = NOW() WHERE order_id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param('ssi', $payment_status, $order_status, $order_id);

    if ($update_stmt->execute()) {
        $_SESSION['success'] = 'Order updated successfully!';
        echo "<script>window.location = 'view.php?order_id=$order_id';</script>";
        exit;
    } else {
        $_SESSION['error'] = 'Failed to update order!';
    }
}
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

            <form method="POST" class="p-3 bg-light rounded shadow-sm">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="payment_status">Payment Status</label>
                            <select name="payment_status" id="payment_status" class="form-control">
                                <option value="0" <?= $order['payment_status'] === 0 ? 'selected' : '' ?>>Unpaid</option>
                                <option value="1" <?= $order['payment_status'] === 1 ? 'selected' : '' ?>>Paid</option>
 
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="order_status">Order Status</label>
                            <select name="order_status" class="form-control">
                                <option value="1" <?= $order["order_status"] == "1" ? "selected" : "" ?>>Pending</option>
                                <option value="2" <?= $order["order_status"] == "2" ? "selected" : "" ?>>Out For Delivery</option>
                                <option value="3" <?= $order["order_status"] == "3" ? "selected" : "" ?>>Delivered</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="text-right mt-3">
                    <button type="submit" class="btn btn-primary shadow">Update</button>
                    <a href="index.php" class="mx-2 btn btn-success shadow"> <i class="fas fa-arrow-left"></i>
                    Back</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include "../component/footer.php"; ?>