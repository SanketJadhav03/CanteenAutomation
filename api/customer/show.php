<?php
include "../config/connection.php";
header("Content-Type: application/json");

// Check if it's a GET request
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Check if customer_id is passed as a query parameter
    if (isset($_GET['customer_id'])) {
        $customer_id = mysqli_real_escape_string($conn, $_GET['customer_id']);
        
        // Query to fetch the customer details
        $query = "SELECT * FROM tbl_customer WHERE customer_id = '$customer_id'";
        $result = $conn->query($query);
        
        // If the customer is found, return the details
        if ($result->num_rows > 0) {
            $customer = $result->fetch_assoc();
            echo json_encode([
                "status" => "success",
                "data" => [
                    "customer_id" => $customer['customer_id'],
                    "customer_name" => $customer['customer_name'],
                    "customer_email" => $customer['customer_email'],
                    "customer_phone" => $customer['customer_phone'],
                    "customer_address" => $customer['customer_address'],
                    "customer_status" => $customer['customer_status'] == 1 ? 'Active' : 'Inactive',
                    "customer_image" => !empty($customer['customer_image']) ? $customer['customer_image'] : "no_img.png"
                ]
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Customer not found"
            ]);
        }
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Customer ID is required"
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method"
    ]);
}
?>
