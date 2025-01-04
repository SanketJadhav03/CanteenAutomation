<?php
header("Content-Type: application/json"); // Set content type to JSON
include "../config/connection.php"; // Include the database connection file

$response = []; // Array to store API response

// Fetch all cart items from the database
$sql = "SELECT c.*, p.* 
        FROM tbl_cart_masters AS c 
        INNER JOIN tbl_product AS p ON c.cart_product_id = p.product_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $cart_items = [];
    while ($row = $result->fetch_assoc()) {
        $cart_items[] = $row;
    }
    http_response_code(200); // Success
    $response['status'] = 'success';
    $response['message'] = 'All cart items fetched successfully.';
    $response['data'] = $cart_items;
} else {
    http_response_code(404); // Not found
    $response['status'] = 'error';
    $response['message'] = 'No cart items found.';
}

// Return the JSON response
echo json_encode($response);
?>
