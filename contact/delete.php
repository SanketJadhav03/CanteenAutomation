<?php
session_start();
include "../config/connection.php";
$contact_id = $_GET["contact_id"];
$deleteQuery = "DELETE FROM `tbl_contact` WHERE `contact_id` = '$contact_id'";
if(mysqli_query($conn,$deleteQuery)){
    $_SESSION["success"] = "Deleted Contact Details Successfully!";
    echo "<script>window.location = 'index.php';</script>";
}

?>