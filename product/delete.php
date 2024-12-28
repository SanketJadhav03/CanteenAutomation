<?php
session_start();
include "../config/connection.php";

// Get product_id from the URL
$product_id = $_GET["product_id"];

// Fetch the current status of the product
$statusQuery = "SELECT product_status FROM `tbl_product` WHERE `product_id` = '$product_id'";
$statusResult = mysqli_query($conn, $statusQuery);
$product = mysqli_fetch_assoc($statusResult);

// Toggle the status
$new_status = ($product['product_status'] == 1) ? 2 : 1;  // If 1, change to 2; if 2, change to 1

// Update query to change the status
$updateQuery = "UPDATE `tbl_product` SET `product_status` = '$new_status' WHERE `product_id` = '$product_id'";

if (mysqli_query($conn, $updateQuery)) {
    $_SESSION["success"] = "Product status updated successfully!";
    echo "<script>window.location = 'index.php';</script>";
} else {
    $_SESSION["error"] = "Error updating product status: " . mysqli_error($conn);
    echo "<script>window.location = 'index.php';</script>";
}
?>
