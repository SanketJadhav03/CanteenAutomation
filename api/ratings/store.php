<?php
// Include database connection
include '../config/connection.php'; // Update with your database connection file

header('Content-Type: application/json');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the raw POST data
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate input fields
    $order_id = isset($data['order_id']) ? trim($data['order_id']) : null;
    $product_id = isset($data['product_id']) ? trim($data['product_id']) : null;
    $customer_id = isset($data['customer_id']) ? trim($data['customer_id']) : null;
    $rating = isset($data['rating']) ? trim($data['rating']) : null;

    // Check if required fields are provided
    if (empty($order_id) || empty($product_id) || empty($customer_id) || empty($rating)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Order ID, Product ID, Customer ID, and Rating are required.'
        ]);
        exit;
    }

    // Validate the rating value (e.g., between 1 and 5)
    if (!is_numeric($rating) || $rating < 1 || $rating > 5) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Rating must be a number between 1 and 5.'
        ]);
        exit;
    }

    // SQL query to insert data into tbl_ratings
    $sql = "INSERT INTO tbl_ratings (order_id, product_id, customer_id, rating, created_at) 
            VALUES (?, ?, ?, ?, NOW())";

    // Prepare the SQL statement
    if ($stmt = $conn->prepare($sql)) {
        // Bind parameters (i = integer, d = double, s = string)
        $stmt->bind_param('iiii', $order_id, $product_id, $customer_id, $rating);

        // Execute the statement
        if ($stmt->execute()) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Rating added successfully.'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to add the rating. Error: ' . $stmt->error
            ]);
        }

        // Close the statement
        $stmt->close();
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to prepare the SQL query.'
        ]);
    }

    // Close the database connection
    $conn->close();
} else {
    // Invalid request method
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method. Only POST is allowed.'
    ]);
}
?>
