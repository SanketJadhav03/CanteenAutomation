<?php
include "../config/connection.php";
header("Content-Type: application/json");

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $category_name = $data['category_name'] ?? null;
    $category_image = $data['category_image'] ?? null;

    if (!$category_name) {
        echo json_encode(["status" => "error", "message" => "Category name is required"]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO tbl_category (category_name, category_image) VALUES (?, ?)");
    $stmt->bind_param("ss", $category_name, $category_image);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(["status" => "success", "message" => "Category created successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to create category"]);
    }
    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}
?>
