<?php
include "../config/connection.php";
header("Content-Type: application/json");

// Check if it's a DELETE request
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents("php://input"), true);

    $category_id = $data['category_id'] ?? null;

    if (!$category_id) {
        echo json_encode(["status" => "error", "message" => "Category ID is required"]);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM tbl_category WHERE category_id = ?");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(["status" => "success", "message" => "Category deleted successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to delete category"]);
    }
    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}
?>
