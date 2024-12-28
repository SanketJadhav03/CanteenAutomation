<?php
include "../config/connection.php";
header("Content-Type: application/json");

// Check if it's a DELETE request
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents("php://input"), true);

    $product_id = $data['product_id'] ?? null;

    if (!$product_id) {
        echo json_encode(["status" => "error", "message" => "Product ID is required"]);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM tbl_product WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(["status" => "success", "message" => "Product deleted successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to delete product"]);
    }
    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}
?>
