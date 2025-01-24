<?php
include "../config/connection.php";

// Set response headers
header("Content-Type: application/json");

// Determine the HTTP request method
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET': // Fetch orders
        fetchOrders($conn);
        break;

    case 'POST': // Update order
        updateOrder($conn);
        break;

    case 'DELETE': // Delete order
        deleteOrder($conn);
        break;

    default:
        echo json_encode(["error" => "Invalid request method"]);
}
function fetchOrders($conn) {
    $customerId = isset($_GET['customer_id']) ? mysqli_real_escape_string($conn, $_GET['customer_id']) : '';

    // Ensure customer_id is provided
    if ($customerId === '') {
        echo json_encode(["error" => "customer_id is required"]);
        return;
    }

    $query = "SELECT * FROM `tbl_orders` 
              INNER JOIN `tbl_customer` ON `tbl_customer`.`customer_id` = `tbl_orders`.`customer_id` 
              WHERE `tbl_orders`.`customer_id` = '$customerId'";

    $result = mysqli_query($conn, $query);
    $orders = [];

    if ($result) {
        while ($order = mysqli_fetch_assoc($result)) {
            // Fetch child orders for this order_id
            $orderId = $order['order_id'];
            $childQuery = "SELECT * FROM `tbl_order_items` 
            INNER JOIN tbl_product ON tbl_product.product_id = tbl_order_items.product_id
            INNER JOIN tbl_category ON tbl_category.category_id = tbl_product.category_id 
            WHERE `order_id` = '$orderId'";
            $childResult = mysqli_query($conn, $childQuery);
            $orderItems = [];

            if ($childResult) {
                while ($item = mysqli_fetch_assoc($childResult)) {
                    $orderItems[] = $item;
                }
            }

            // Add child orders to the main order
            $order['order_items'] = $orderItems;
            $orders[] = $order;
        }

        echo json_encode($orders);
    } else {
        echo json_encode(["error" => "Failed to fetch orders."]);
    }
}



function updateOrder($conn) {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['order_id']) && isset($data['order_status'])) {
        $orderId = mysqli_real_escape_string($conn, $data['order_id']);
        $orderStatus = mysqli_real_escape_string($conn, $data['order_status']);

        $query = "UPDATE `tbl_orders` SET `order_status` = '$orderStatus' WHERE `order_id` = '$orderId'";
        if (mysqli_query($conn, $query)) {
            echo json_encode(["success" => "Order status updated successfully."]);
        } else {
            echo json_encode(["error" => "Failed to update order status."]);
        }
    } else {
        echo json_encode(["error" => "Invalid input."]);
    }
}

function deleteOrder($conn) {
    parse_str(file_get_contents("php://input"), $data);

    if (isset($data['order_id'])) {
        $orderId = mysqli_real_escape_string($conn, $data['order_id']);
        $query = "DELETE FROM `tbl_orders` WHERE `order_id` = '$orderId'";

        if (mysqli_query($conn, $query)) {
            echo json_encode(["success" => "Order deleted successfully."]);
        } else {
            echo json_encode(["error" => "Failed to delete order."]);
        }
    } else {
        echo json_encode(["error" => "Invalid input."]);
    }
}
?>
