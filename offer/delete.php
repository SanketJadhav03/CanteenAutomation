<?php
session_start();
include "../config/connection.php";

// Get offer_id from the URL
$offer_id = $_GET["offer_id"];

// Fetch the current image of the offer to delete the file from the server
$imageQuery = "SELECT offer_image FROM `tbl_offer` WHERE `offer_id` = '$offer_id'";
$imageResult = mysqli_query($conn, $imageQuery);
$imageData = mysqli_fetch_assoc($imageResult);

if ($imageData && !empty($imageData['offer_image'])) {
    $imagePath = "../uploads/offers/" . $imageData['offer_image'];
    if (file_exists($imagePath)) {
        unlink($imagePath); // Delete the image file from the server
    }
}

// Delete query to remove the offer
$deleteQuery = "DELETE FROM `tbl_offer` WHERE `offer_id` = '$offer_id'";

if (mysqli_query($conn, $deleteQuery)) {
    $_SESSION["success"] = "Offer deleted successfully!";
    echo "<script>window.location = 'index.php';</script>";
} else {
    $_SESSION["error"] = "Error deleting offer: " . mysqli_error($conn);
    echo "<script>window.location = 'index.php';</script>";
}
?>
