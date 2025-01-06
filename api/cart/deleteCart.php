<?php
header("Content-Type: application/json");  
include "../config/connection.php";  
$response = [];  
$input = json_decode(file_get_contents("php://input"), true); 
if (empty($input['cart_id'])) {
    http_response_code(400);  
    $response = [
        'status' => 'error',
        'message' => 'Missing required parameter: cart_id.'
    ];
    echo json_encode($response);
    exit();
} 
$cart_id = intval($input['cart_id']); 
$sql_delete = "DELETE FROM tbl_cart_masters WHERE cart_id = ?";
$stmt_delete = $conn->prepare($sql_delete);
$stmt_delete->bind_param("i", $cart_id); 
if ($stmt_delete->execute()) {
    if ($stmt_delete->affected_rows > 0) {
        http_response_code(200);  
        $response = [
            'status' => 'success',
            'message' => 'Cart item deleted successfully for the given cart_id.'
        ];
    } else {
        http_response_code(404);  
        $response = [
            'status' => 'error',
            'message' => 'No cart item found for the given cart_id.'
        ];
    }
} else {
    http_response_code(500);  
    $response = [
        'status' => 'error',
        'message' => 'Failed to delete the cart item.'
    ];
} 
echo json_encode($response);
?>
