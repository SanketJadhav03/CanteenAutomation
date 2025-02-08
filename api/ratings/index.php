<?php
include "../config/connection.php";
header("Content-Type: application/json");

// Check if it's a GET request
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Join ratings table with customers table to get customer name and image
    $query = "SELECT r.*, c.customer_name, c.customer_image 
              FROM tbl_ratings r 
              JOIN tbl_customer c ON r.customer_id = c.customer_id";

    $result = $conn->query($query);
    $products = [];

    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }

    echo json_encode(["status" => "success", "data" => $products]);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}
?>
