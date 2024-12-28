<?php
session_start();
include "../config/connection.php";

// Get customer_id from the URL
$customer_id = $_GET["customer_id"];

// Fetch the current status of the customer
$statusQuery = "SELECT customer_status FROM `tbl_customer` WHERE `customer_id` = '$customer_id'";
$statusResult = mysqli_query($conn, $statusQuery);
$customer = mysqli_fetch_assoc($statusResult);

// Toggle the status
$new_status = ($customer['customer_status'] == 1) ? 2 : 1;  // If 1, change to 2; if 2, change to 1

// Update query to change the status
$updateQuery = "UPDATE `tbl_customer` SET `customer_status` = '$new_status' WHERE `customer_id` = '$customer_id'";

if (mysqli_query($conn, $updateQuery)) {
    $_SESSION["success"] = "Customer status updated successfully!";
    echo "<script>window.location = 'index.php';</script>";
} else {
    $_SESSION["error"] = "Error updating customer status: " . mysqli_error($conn);
    echo "<script>window.location = 'index.php';</script>";
}
?>
