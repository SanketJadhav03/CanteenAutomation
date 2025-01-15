<?php
session_start();
include "../config/connection.php";

// Get slider_id from the URL
$slider_id = $_GET["slider_id"];

// Fetch the current image of the slider to delete the file from the server
$imageQuery = "SELECT slider_image FROM `tbl_slider` WHERE `slider_id` = '$slider_id'";
$imageResult = mysqli_query($conn, $imageQuery);
$imageData = mysqli_fetch_assoc($imageResult);

if ($imageData && !empty($imageData['slider_image'])) {
    $imagePath = "../uploads/sliders/" . $imageData['slider_image'];
    if (file_exists($imagePath)) {
        unlink($imagePath); // Delete the image file from the server
    }
}

// Delete query to remove the slider
$deleteQuery = "DELETE FROM `tbl_slider` WHERE `slider_id` = '$slider_id'";

if (mysqli_query($conn, $deleteQuery)) {
    $_SESSION["success"] = "Slider deleted successfully!";
    echo "<script>window.location = 'index.php';</script>";
} else {
    $_SESSION["error"] = "Error deleting slider: " . mysqli_error($conn);
    echo "<script>window.location = 'index.php';</script>";
}
?>
    