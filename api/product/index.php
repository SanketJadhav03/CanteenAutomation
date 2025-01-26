<?php
include "../config/connection.php";
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $product_status = isset($_GET['product_status']) ? intval($_GET['product_status']) : null;

    // SQL Query using GROUP_CONCAT
    $sql = "
        SELECT 
            p.product_id, 
            p.product_name, 
            p.product_description, 
            p.product_price, 
            p.product_status, 
            c.category_name, 
            COALESCE(ROUND(AVG(r.rating), 1), 0) AS avg_rating,
            GROUP_CONCAT(
                CONCAT(
                    '{\"customer_id\":', cust.customer_id, 
                    ',\"customer_name\":\"', cust.customer_name, 
                    '\",\"rating\":', r.rating, 
                    ',\"review\":\"', r.review_rating, 
                    '\",\"rated_at\":\"', r.created_at, 
                    '\"}'
                )
            ) AS customer_ratings
        FROM tbl_product p
        INNER JOIN tbl_category c ON c.category_id = p.category_id
        LEFT JOIN tbl_ratings r ON r.product_id = p.product_id
        LEFT JOIN tbl_customer cust ON cust.customer_id = r.customer_id";

    if ($product_status === 1) {
        $sql .= " WHERE p.product_status = 1";
    }

    $sql .= " GROUP BY p.product_id";

    // Execute the query
    $result = $conn->query($sql);

    if ($result) {
        $products = [];
        while ($row = $result->fetch_assoc()) {
            // Parse the concatenated customer ratings into an array
            $row['customer_ratings'] = $row['customer_ratings'] ? json_decode("[" . $row['customer_ratings'] . "]", true) : [];
            $products[] = $row;
        }
        echo json_encode(["status" => "success", "data" => $products]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to fetch products. Error: " . $conn->error]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}
?>
