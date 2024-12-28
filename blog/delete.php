<?php
session_start();
include "../config/connection.php";
$blog_id = $_GET["blog_id"];
$deleteQuery = "DELETE FROM `tbl_blog` WHERE `blog_id` = '$blog_id'";
if(mysqli_query($conn,$deleteQuery)){
    $_SESSION["success"] = "Deleted Blog Successfully!";
    echo "<script>window.location = 'index.php';</script>";
}

?>