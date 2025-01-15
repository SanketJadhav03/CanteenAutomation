<?php
include "../config/connection.php";

// Set the response content type to JSON
header('Content-Type: application/json');

// Fetch active sliders for the carousel
$sliderQuery = "SELECT slider_id, slider_image, slider_status, created_at 
                FROM tbl_slider WHERE slider_status = 1";
$sliderResult = mysqli_query($conn, $sliderQuery);

$sliders = array();

while ($slider = mysqli_fetch_assoc($sliderResult)) {
    $sliders[] = $slider;
}

// Return JSON response
echo json_encode([
    'status' => 'success',
    'data' => $sliders
]);

// Close the database connection
mysqli_close($conn);
?>
