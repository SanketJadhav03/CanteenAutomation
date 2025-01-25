<?php
include "../config/connection.php";
header("Content-Type: application/json");

// API: Store Order with Items
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read the input JSON body
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate required fields
    if (
        empty($data['customer_id']) || 
        empty($data['total_price']) || 
        empty($data['order_date']) || 
        empty($data['order_status']) || 
        empty($data['shipping_address']) || 
        empty($data['payment_method']) || 
        empty($data['payment_status']) || 
        empty($data['items']) || 
        !is_array($data['items'])
    ) {
        echo json_encode(["status" => "error", "message" => "Invalid input data"]);
        exit;
    }

    // Extract order data
    $customer_id = $data['customer_id'];
    $total_price = $data['total_price'];
    $order_date = $data['order_date'];
    $order_status = $data['order_status'];
    $shipping_address = $data['shipping_address'];
    $payment_method = $data['payment_method'];
    $payment_status = $data['payment_status'];
    $items = $data['items'];

    // Start a database transaction
    $conn->begin_transaction();

    try {
        // Insert into tbl_orders
        $orderQuery = "INSERT INTO tbl_orders (customer_id,total_price, order_date, order_status, shipping_address, payment_method, payment_status, created_at) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($orderQuery);
        $stmt->bind_param("isssss", $customer_id, $total_price, $order_date, $order_status, $shipping_address, $payment_method, $payment_status);
        $stmt->execute();
        $order_id = $stmt->insert_id; // Get the inserted order ID

        // Insert into tbl_order_items
        $itemQuery = "INSERT INTO tbl_order_items (order_id, product_id, quantity, price, created_at) 
                      VALUES (?, ?, ?, ?, NOW())";
        $itemStmt = $conn->prepare($itemQuery);

        foreach ($items as $item) {
            if (empty($item['product_id']) || empty($item['quantity']) || empty($item['price'])) {
                throw new Exception("Invalid item data");
            }
            $product_id = $item['product_id'];
            $quantity = $item['quantity'];
            $price = $item['price'];

            $itemStmt->bind_param("iiid", $order_id, $product_id, $quantity, $price);
            $itemStmt->execute();
        }

        // Commit transaction
        $conn->commit();
        echo json_encode(["status" => "success", "message" => "Order and items stored successfully", "order_id" => $order_id]);

    } catch (Exception $e) {
        // Rollback transaction in case of error
        $conn->rollback();
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
}

$conn->close();
?>
