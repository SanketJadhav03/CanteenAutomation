<?php
include "../config/connection.php";

// Set the response format to JSON
header('Content-Type: application/json');

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Extract the POST data
    $customer_name = mysqli_real_escape_string($conn, $_POST["customer_name"]);
    $customer_email = mysqli_real_escape_string($conn, $_POST["customer_email"]);
    $customer_password = mysqli_real_escape_string($conn, $_POST["customer_password"]);
    $customer_phone = mysqli_real_escape_string($conn, $_POST["customer_phone"]);
    $customer_address = mysqli_real_escape_string($conn, $_POST["customer_address"]);
    $customer_status = 1; // Assuming default active status is 1 when creating a new customer

    // Initialize image path
    $image_path = "";

    // Image upload processing
    if (isset($_FILES["customer_image"])) {
        $customer_image = $_FILES["customer_image"]["name"];
        $customer_image_temp = $_FILES["customer_image"]["tmp_name"];
        
        // Set the target directory where the file will be uploaded
        $target_dir = "../../uploads/customers/";
        $target_file = $target_dir . basename($customer_image);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if the file is not empty
        if ($_FILES["customer_image"]["size"] == 0) {
            echo json_encode(["error" => "No file uploaded!"]);
            exit;
        }

        // Check if the file is an image by using getimagesize()
        $check = getimagesize($customer_image_temp);
        if ($check === false) {
            echo json_encode(["error" => "File is not an image!"]);
            exit;
        }

        // Check if file already exists
        if (file_exists($target_file)) {
            echo json_encode(["error" => "Sorry, the file already exists!"]);
            exit;
        }

        // Check file size (limit to 5MB)
        if ($_FILES["customer_image"]["size"] > 5000000) {
            echo json_encode(["error" => "Sorry, your file is too large!"]);
            exit;
        }

        // Allow certain file formats (JPG, JPEG, PNG)
        if (!in_array($imageFileType, ["jpg", "jpeg", "png"])) {
            echo json_encode(["error" => "Only JPG, JPEG, & PNG files are allowed!"]);
            exit;
        }

        // Attempt to move the uploaded file to the target directory
        if (move_uploaded_file($customer_image_temp, $target_file)) {
            $image_path = $customer_image;
        } else {
            echo json_encode(["error" => "Error uploading the image!"]);
            exit;
        }
    }

    // Validate required fields
    if (empty($customer_name) || empty($customer_email) || empty($customer_password) || empty($customer_phone) || empty($customer_address)) {
        echo json_encode(["error" => "Please fill in all required fields!"]);
        exit;
    } else {
        // Check if email already exists
        $checkEmailQuery = "SELECT * FROM tbl_customer WHERE customer_email = '$customer_email'";
        $checkEmailResult = mysqli_query($conn, $checkEmailQuery);
        if (mysqli_num_rows($checkEmailResult) > 0) {
            echo json_encode(["error" => "Email is already registered!"]);
            exit;
        } else {
            // Hash the password for security
            $hashed_password = password_hash($customer_password, PASSWORD_DEFAULT);

            // Insert query with image path
            $insertQuery = "INSERT INTO tbl_customer (customer_name, customer_email, customer_password, customer_phone, customer_address, customer_status, customer_image) 
                            VALUES ('$customer_name', '$customer_email', '$hashed_password', '$customer_phone', '$customer_address', '$customer_status', '$image_path')";
            if (mysqli_query($conn, $insertQuery)) {
                echo json_encode(["success" => "Customer registered successfully!"]);
            } else {
                echo json_encode(["error" => "Error registering customer: " . mysqli_error($conn)]);
            }
        }
    }
} else {
    echo json_encode(["error" => "Invalid request method!"]);
}
?>
