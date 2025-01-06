<?php
include "../config/connection.php";
header("Content-Type: application/json");

$response = []; // Initialize response array

// Check if the request method is DELETE
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Decode JSON input from the user
    $input = json_decode(file_get_contents("php://input"), true);

    // Validate the input parameter
    if (empty($input['wishlist_id'])) {
        http_response_code(400); // Bad Request
        $response = [
            'status' => 'error',
            'message' => 'Missing required parameter: wishlist_id.'
        ];
        echo json_encode($response);
        exit();
    }

    // Extract the wishlist_id from input
    $wishlist_id = intval($input['wishlist_id']);

    // Prepare the SQL DELETE query
    $sql_delete = "DELETE FROM tbl_wishlist_masters WHERE wishlist_id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $wishlist_id);

    // Execute the DELETE query
    if ($stmt_delete->execute()) {
        if ($stmt_delete->affected_rows > 0) {
            http_response_code(200); // OK
            $response = [
                'status' => 'success',
                'message' => 'Wishlist item deleted successfully for the given wishlist_id.'
            ];
        } else {
            http_response_code(404); // Not Found
            $response = [
                'status' => 'error',
                'message' => 'No wishlist item found for the given wishlist_id.'
            ];
        }
    } else {
        http_response_code(500); // Internal Server Error
        $response = [
            'status' => 'error',
            'message' => 'Failed to delete wishlist item.'
        ];
    }

    echo json_encode($response);
} else {
    http_response_code(405); // Method Not Allowed
    $response = [
        'status' => 'error',
        'message' => 'Invalid request method. Use DELETE.'
    ];
    echo json_encode($response);
}
?>
