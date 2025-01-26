<?php
include "../config/connection.php";

// Query to fetch most sold products based on order items
$query = "
    SELECT 
        p.product_name, 
        SUM(oi.quantity) AS total_sold, 
        SUM(oi.quantity * oi.price) AS total_revenue
    FROM tbl_order_items oi
    INNER JOIN tbl_orders o ON oi.order_id = o.order_id
    INNER JOIN tbl_product p ON oi.product_id = p.product_id
    WHERE o.order_status = 3
    GROUP BY oi.product_id
    ORDER BY total_sold DESC
    LIMIT 10
";

$result = mysqli_query($conn, $query);

// Check if query executed successfully
if (!$result) {
    die("Error executing query: " . mysqli_error($conn));
}

// Set headers for downloading CSV
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="most_sold_products.csv"');

// Open output stream for writing CSV
$output = fopen('php://output', 'w');

// Add CSV headers
fputcsv($output, ['Product Name', 'Total Sold', 'Total Revenue']);

// Fetch data and write to CSV
while ($data = mysqli_fetch_array($result)) {
    // Write each row to the CSV
    fputcsv($output, [
        htmlspecialchars($data['product_name']), // Ensure safe output
        $data['total_sold'],
        number_format($data['total_revenue'], 2)
    ]);
}

// Close the output stream
fclose($output);
exit();
?>
