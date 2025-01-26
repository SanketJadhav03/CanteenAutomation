<?php
include "../config/connection.php";

// Set headers for CSV download
header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=Customer_Purchase_Frequency_Report.csv");

// Open output stream
$output = fopen("php://output", "w");

// Add column headers to CSV
fputcsv($output, ["Customer Name", "Total Orders", "Last Order Date"]);

// Query to fetch customer purchase frequency
$query = "
    SELECT c.customer_name, COUNT(o.order_id) AS total_orders, MAX(o.order_date) AS last_order_date
    FROM tbl_orders o
    INNER JOIN tbl_customer c ON o.customer_id = c.customer_id
    WHERE o.order_status = 3
    GROUP BY o.customer_id
    ORDER BY total_orders DESC
";
$result = mysqli_query($conn, $query);

// Add data rows to CSV
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, [
            $row["customer_name"],
            $row["total_orders"],
            date("d/m/Y", strtotime($row["last_order_date"]))
        ]);
    }
} else {
    fputcsv($output, ["No data found"]);
}

// Close output stream
fclose($output);
exit;
?>
