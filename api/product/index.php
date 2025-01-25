<?php
include "../config/connection.php";
header("Content-Type: application/json"); 

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Check if the 'product_status' parameter is passed in the request
    $product_status = isset($_GET['product_status']) ? intval($_GET['product_status']) : null;

    // Base SQL query
    $sql = "SELECT * FROM tbl_product INNER JOIN tbl_category ON tbl_category.category_id = tbl_product.category_id";

    // Add a condition if 'product_status' is provided
    if ($product_status === 1) {
        $sql .= " WHERE tbl_product.product_status = 1";
    }

    // Execute the query
    $result = $conn->query($sql);

    if ($result) {
        $products = []; 
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        } 
        echo json_encode(["status" => "success", "data" => $products]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to fetch products. Error: " . $conn->error]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}
?>
