<?php
include "../config/connection.php";
header("Content-Type: application/json"); 
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $result = $conn->query("SELECT * FROM tbl_wishlist_masters INNER JOIN tbl_product ON tbl_product.product_id = tbl_wishlist_masters.wishlist_product_id INNER JOIN tbl_category ON tbl_product.category_id = tbl_category.category_id   ");
    $products = [];

    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    } 
    echo json_encode(["status" => "success", "data" => $products]);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}
?>
