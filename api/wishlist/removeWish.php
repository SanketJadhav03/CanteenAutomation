<?php
include "../config/connection.php";
header("Content-Type: application/json"); 
$response = []; 
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') { 
    $input = json_decode(file_get_contents("php://input"), true); 
    if (empty($input['customer_id'])) {
        http_response_code(400);  
        $response = [
            'status' => 'error',
            'message' => 'Missing required parameter: customer_id.'
        ];
        echo json_encode($response);
        exit();
    } 
    $customer_id = intval($input['customer_id']); 
    $sql_delete = "DELETE FROM tbl_wishlist_masters WHERE customer_id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $customer_id); 
    if ($stmt_delete->execute()) {
        if ($stmt_delete->affected_rows > 0) {
            http_response_code(200); // OK
            $response = [
                'status' => 'success',
                'message' => 'All wishlist items deleted successfully for the given customer_id.'
            ];
        } else {
            http_response_code(404); // Not Found
            $response = [
                'status' => 'error',
                'message' => 'No wishlist items found for the given customer_id.'
            ];
        }
    } else {
        http_response_code(500); // Internal Server Error
        $response = [
            'status' => 'error',
            'message' => 'Failed to delete wishlist items.'
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
