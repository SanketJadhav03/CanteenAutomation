<?php
session_start();
include "../config/connection.php";

// Get product_id from the URL
$product_id = $_GET["product_id"];

// Use a prepared statement to delete the product
$stmt = $conn->prepare("DELETE FROM `tbl_product` WHERE `product_id` = ?");
$stmt->bind_param("i", $product_id);

if ($stmt->execute()) {
    $_SESSION["success"] = "Product deleted successfully!";
    echo "<script>window.location = 'index.php';</script>";
} else {
    $_SESSION["error"] = "Error deleting product: " . $stmt->error;
    echo "<script>window.location = 'index.php';</script>";
}

$stmt->close();
$conn->close();
?>
