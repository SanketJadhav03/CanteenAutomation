<?php
header("Content-Type: application/json");
include "../config/connection.php"; // Database connection

$requestMethod = $_SERVER["REQUEST_METHOD"];

switch ($requestMethod) {
    case 'GET':
        if (isset($_GET['id'])) {
            getNotification($_GET['id']);
        } else {
            getAllNotifications();
        }
        break;
        
    case 'POST':
        createNotification();
        break;
        
    case 'PUT':
        updateNotification();
        break;
        
    case 'DELETE':
        if (isset($_GET['id'])) {
            deleteNotification($_GET['id']);
        } else {
            echo json_encode(["error" => "ID parameter missing"]);
        }
        break;
        
    default:
        echo json_encode(["error" => "Invalid Request Method"]);
}

// Function to fetch all notifications
function getAllNotifications() {
    global $conn;
    $query = "SELECT * FROM tbl_notification INNER JOIN tbl_customer ON tbl_customer.customer_id = tbl_notification.notification_customer_id ORDER BY notification_date DESC";
    $result = mysqli_query($conn, $query);
    
    $notifications = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $notifications[] = $row;    
    }
    
    echo json_encode($notifications);
}

// Function to fetch a single notification by ID
function getNotification($id) {
    global $conn;
    $query = "SELECT * FROM tbl_notification INNER JOIN tbl_customer ON tbl_customer.customer_id = tbl_notification.notification_customer_id WHERE notification_id = ?";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    $notification = mysqli_fetch_assoc($result);
    
    echo json_encode($notification ?: ["error" => "Notification not found"]);
}

// Function to create a new notification
function createNotification() {
    global $conn;
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($data["notification_customer_id"]) || !isset($data["notification_message"])) {
        echo json_encode(["error" => "Missing required fields"]);
        return;
    }

    $customer_id = $data["notification_customer_id"];
    $message = $data["notification_message"];

    $query = "INSERT INTO tbl_notification (notification_customer_id, notification_message) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "is", $customer_id, $message);
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(["success" => "Notification created successfully"]);
    } else {
        echo json_encode(["error" => "Failed to create notification"]);
    }
}

// Function to update a notification
function updateNotification() {
    global $conn;
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($data["notification_id"]) || !isset($data["notification_message"])) {
        echo json_encode(["error" => "Missing required fields"]);
        return;
    }

    $id = $data["notification_id"];
    $message = $data["notification_message"];

    $query = "UPDATE tbl_notification SET notification_message = ? WHERE notification_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "si", $message, $id);
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(["success" => "Notification updated successfully"]);
    } else {
        echo json_encode(["error" => "Failed to update notification"]);
    }
}

// Function to delete a notification
function deleteNotification($id) {
    global $conn;
    $query = "DELETE FROM tbl_notification WHERE notification_id = ?";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(["success" => "Notification deleted successfully"]);
    } else {
        echo json_encode(["error" => "Failed to delete notification"]);
    }
}
?>
