<?php
session_start();
include "../config/connection.php";

$category_id = $_GET["category_id"];

// Check if the category is in use
$productCheckQuery = "SELECT COUNT(*) AS product_count FROM `tbl_product` WHERE `category_id` = '$category_id'";
$productCheckResult = mysqli_query($conn, $productCheckQuery);
$productData = mysqli_fetch_assoc($productCheckResult);

if ($productData['product_count'] > 0) {
    // Category is in use
    $_SESSION["error"] = "Category is in use and cannot be deleted!";
    echo "<script>window.location = 'index.php';</script>";
    exit();
}

// Attempt to delete the category
$deleteQuery = "DELETE FROM `tbl_category` WHERE `category_id` = '$category_id'";
if (mysqli_query($conn, $deleteQuery)) {
    $_SESSION["success"] = "Deleted Category Successfully!";
} else {
    // Handle general errors
    $_SESSION["error"] = "Failed to delete category. Please try again!";
}

echo "<script>window.location = 'index.php';</script>";
?>
