<?php
header("Content-Type: application/json"); 
include "../config/connection.php";  
$response = [];  
$input = json_decode(file_get_contents("php://input"), true); 
if (empty($input['customer_id'])) {
    http_response_code(400); // Bad Request
    $response = [
        'status' => 'error',
        'message' => 'Missing required parameter: customer_id.'
    ];
    echo json_encode($response);
    exit();
} 
$customer_id = intval($input['customer_id']); 
$sql_delete = "DELETE FROM tbl_cart_masters WHERE customer_id = ?";
$stmt_delete = $conn->prepare($sql_delete);
$stmt_delete->bind_param("i", $customer_id); 
if ($stmt_delete->execute()) {
    if ($stmt_delete->affected_rows > 0) {
        http_response_code(200); 
        $response = [
            'status' => 'success',
            'message' => 'All cart items deleted successfully for the given customer_id.'
        ];
    } else {
        http_response_code(404);  
        $response = [
            'status' => 'error',
            'message' => 'No cart items found for the given customer_id.'
        ];
    }
} else {
    http_response_code(500); 
    $response = [
        'status' => 'error',
        'message' => 'Failed to delete cart items.'
    ];
} 
echo json_encode($response);
?>
