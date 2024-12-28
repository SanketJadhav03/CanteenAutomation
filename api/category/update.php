<?php
include "../config/connection.php";
header("Content-Type: application/json");

// Check if it's a PUT request
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);

    $category_id = $data['category_id'] ?? null;
    $category_name = $data['category_name'] ?? null;
    $category_image = $data['category_image'] ?? null;

    if (!$category_id || !$category_name) {
        echo json_encode(["status" => "error", "message" => "Category ID and name are required"]);
        exit;
    }

    $stmt = $conn->prepare("UPDATE tbl_category SET category_name = ?, category_image = ? WHERE category_id = ?");
    $stmt->bind_param("ssi", $category_name, $category_image, $category_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(["status" => "success", "message" => "Category updated successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update category"]);
    }
    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}
?>
