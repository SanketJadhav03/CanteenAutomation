<?php
header("Content-Type: application/json");  
include "../config/connection.php"; 
$response = [];  
$input = json_decode(file_get_contents("php://input"), true); 
if (
    empty($input['customer_id']) || empty($input['product_id']) || empty($input['product_qty'])
) {
    http_response_code(400);  
    $response = [
        'status' => 'error',
        'message' => 'Missing required parameters: customer_id, product_id, or product_qty.'
    ];
    echo json_encode($response);
    exit();
} 
$customer_id = intval($input['customer_id']);
$product_id = intval($input['product_id']);
$product_qty = intval($input['product_qty']);
$cart_status = 0;  
$sql_check = "SELECT cart_id FROM tbl_cart_masters WHERE cart_product_id = ? AND customer_id = ? AND cart_status = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("iis", $product_id, $customer_id, $cart_status);
$stmt_check->execute();
$result_check = $stmt_check->get_result(); 
if ($result_check->num_rows > 0) { 
    $sql_update = "UPDATE tbl_cart_masters 
                   SET cart_product_qty = cart_product_qty + ? 
                   WHERE cart_product_id = ? AND customer_id = ? AND cart_status = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("iiis", $product_qty, $product_id, $customer_id, $cart_status); 
    if ($stmt_update->execute()) {
        http_response_code(200);  
        $response = [
            'status' => 'success',
            'message' => 'Product quantity updated in the cart.'
        ];
    } else {
        http_response_code(500);  
        $response = [
            'status' => 'error',
            'message' => 'Failed to update product in the cart.'
        ];
    }
} else { 
    $sql_insert = "INSERT INTO tbl_cart_masters (cart_product_id, cart_product_qty, customer_id, cart_status) 
                   VALUES (?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("iiis", $product_id, $product_qty, $customer_id, $cart_status);

    if ($stmt_insert->execute()) {
        http_response_code(201);  
        $response = [
            'status' => 'success',
            'message' => 'Product added to the cart with status 0.'
        ];
    } else {
        http_response_code(500);  
        $response = [
            'status' => 'error',
            'message' => 'Failed to add product to the cart.'
        ];
    }
} 
echo json_encode($response);
?>
