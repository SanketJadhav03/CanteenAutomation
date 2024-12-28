<?php
include "../config/connection.php";
header("Content-Type: application/json");

// Check if it's a GET request
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $result = $conn->query("SELECT * FROM tbl_product INNER JOIN tbl_category ON tbl_category.category_id = tbl_product.category_id ");
    $products = [];

    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }

    echo json_encode(["status" => "success", "data" => $products]);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}
?>
