<?php
include '../config/connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $order_id = isset($data['order_id']) ? trim($data['order_id']) : null;
    $product_id = isset($data['product_id']) ? trim($data['product_id']) : null;
    $customer_id = isset($data['customer_id']) ? trim($data['customer_id']) : null;
    $rating = isset($data['rating']) ? trim($data['rating']) : null;
    $review_rating = isset($data['review_rating']) ? trim($data['review_rating']) : null;

    // Validate required fields
    if (empty($order_id) || empty($product_id) || empty($customer_id) || empty($rating)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Order ID, Product ID, Customer ID, and Rating are required.'
        ]);
        exit;
    }

    // Validate rating range
    if (!is_numeric($rating) || $rating < 1 || $rating > 5) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Rating must be a number between 1 and 5.'
        ]);
        exit;
    }

    // Insert query
    $sql = "INSERT INTO tbl_ratings (order_id, product_id, customer_id, rating, review_rating, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('iiiis', $order_id, $product_id, $customer_id, $rating, $review_rating);

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
        $stmt->close();
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to prepare the SQL query. Error: ' . $conn->error
        ]);
    }

    $conn->close();
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method. Only POST is allowed.'
    ]);
}
