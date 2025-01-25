<?php
include "../config/connection.php";
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(["status" => "error", "message" => "Malformed JSON input"]);
        exit;
    }

    if (
        !isset($data['customer_id'], $data['total_price'], $data['order_date'], $data['order_status'], $data['shipping_address'], $data['payment_method'], $data['payment_status'], $data['items']) ||
        !is_array($data['items'])
    ) {
        echo json_encode(["status" => "error", "message" => "Invalid input data"]);
        exit;
    }

    $customer_id = $data['customer_id'];
    $total_price = $data['total_price'];
    $order_date = $data['order_date'];
    $order_status = $data['order_status'];
    $shipping_address = $data['shipping_address'];
    $payment_method = $data['payment_method'];
    $payment_status = $data['payment_status'];
    $items = $data['items'];

    $conn->begin_transaction();

    try {
        $orderQuery = "INSERT INTO tbl_orders (customer_id, total_price, order_date, order_status, shipping_address, payment_method, payment_status, created_at) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($orderQuery);
        $stmt->bind_param("idsssss", $customer_id, $total_price, $order_date, $order_status, $shipping_address, $payment_method, $payment_status);
        if (!$stmt->execute()) {
            throw new Exception("Failed to insert order");
        }

        $order_id = $stmt->insert_id;

        $itemQuery = "INSERT INTO tbl_order_items (order_id, product_id, quantity, price, created_at) VALUES (?, ?, ?, ?, NOW())";
        $itemStmt = $conn->prepare($itemQuery);

        foreach ($items as $item) {
            if (!isset($item['product_id'], $item['quantity'], $item['price'])) {
                throw new Exception("Invalid item data");
            }

            $product_id = $item['product_id'];
            $quantity = $item['quantity'];
            $price = $item['price'];

            $itemStmt->bind_param("iiid", $order_id, $product_id, $quantity, $price);
            if (!$itemStmt->execute()) {
                throw new Exception("Failed to insert order item");
            }
        }

        $conn->commit();
        echo json_encode(["status" => "success", "message" => "Order stored successfully", "order_id" => $order_id]);

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
}

$conn->close();
?>
