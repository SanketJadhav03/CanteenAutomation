<?php
include "../config/connection.php";
header("Content-Type: application/json");

// Check if it's a GET request
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $result = $conn->query("SELECT * FROM tbl_category");
    $categories = [];
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
    echo json_encode(["status" => "success", "data" => $categories]);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}
?>
