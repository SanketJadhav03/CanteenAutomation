<?php
include "../config/connection.php";
header("Content-Type: application/json");

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $product_name = $data['product_name'] ?? null;
    $product_description = $data['product_description'] ?? null;
    $product_image = $data['product_image'] ?? null;
    $product_price = $data['product_price'] ?? null;
    $product_dis = $data['product_dis'] ?? null;
    $product_dis_value = $data['product_dis_value'] ?? null;
    $category_id = $data['category_id'] ?? null;

    if (!$product_name || !$product_price || !$category_id) {
        echo json_encode(["status" => "error", "message" => "Product name, price, and category are required"]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO tbl_product (product_name, product_description, product_image, product_price, product_dis, product_dis_value, category_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssdssi", $product_name, $product_description, $product_image, $product_price, $product_dis, $product_dis_value, $category_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(["status" => "success", "message" => "Product created successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to create product"]);
    }
    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}
?>
