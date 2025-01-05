<?php
// Include database connection
include '../config/connection.php'; // Replace with your database connection file

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the raw POST data
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate input fields
    $contact_name = isset($data['contact_name']) ? trim($data['contact_name']) : null;
    $contact_email = isset($data['contact_email']) ? trim($data['contact_email']) : null;
    $contact_phone = isset($data['contact_phone']) ? trim($data['contact_phone']) : null;
    $contact_message = isset($data['contact_message']) ? trim($data['contact_message']) : null;

    // Check if required fields are provided
    if (empty($contact_name) || empty($contact_email) || empty($contact_phone) || empty($contact_message)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'All fields are required.'
        ]);
        exit;
    }

    // Prepare the SQL statement
    $sql = "INSERT INTO tbl_contact (contact_name, contact_email, contact_phone, contact_message, created_at) 
            VALUES (?, ?, ?, ?, NOW())";

    if ($stmt = $conn->prepare($sql)) {
        // Bind parameters
        $stmt->bind_param('ssss', $contact_name, $contact_email, $contact_phone, $contact_message);

        // Execute the statement
        if ($stmt->execute()) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Contact details added successfully.'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to add contact details.'
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
