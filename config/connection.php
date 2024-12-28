<?php
$conn = mysqli_connect("localhost", "root", "", "canteenautomationsystem");

if (!$conn) {
    echo "<script>alert('Something Went Wrong!')</script>";
} else { 
    mysqli_query($conn, "SET time_zone = '+05:30'");
}
?>
