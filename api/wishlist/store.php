<?php
// Include database connection
include '../config/connection.php'; // Replace with your database connection file

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the raw POST data
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate input fields
    $wishlist_product_id = isset($data['wishlist_product_id']) ? trim($data['wishlist_product_id']) : null;
    $customer_id = isset($data['customer_id']) ? trim($data['customer_id']) : null;

    // Check if required fields are provided
    if (empty($wishlist_product_id) || empty($customer_id)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Product ID and Customer ID are required.'
        ]);
        exit;
    }

    // Prepare the SQL statement
    $sql = "INSERT INTO tbl_wishlist_masters (wishlist_product_id, customer_id, created_at) VALUES (?, ?, NOW())";

    if ($stmt = $conn->prepare($sql)) {
        // Bind parameters
        $stmt->bind_param('ii', $wishlist_product_id, $customer_id);

        // Execute the statement
        if ($stmt->execute()) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Wishlist item added successfully.'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to add item to the wishlist.'
            ]);
        }

        // Close the statement
        $stmt->close();
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Database query preparation failed.'
        ]);
    }

    // Close the database connection
    $conn->close();
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method.'
    ]);
}
?>
