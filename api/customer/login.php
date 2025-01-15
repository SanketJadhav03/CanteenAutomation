<?php
include "../config/connection.php";

// Set response format to JSON
header("Content-Type: application/json"); 

// Function to generate a simple token
function generateToken($email, $id) {
    $key = "your_simple_secret_key"; // Replace with your secure secret key
    $time = time();
    return base64_encode($email . '|' . $id . '|' . $time . '|' . hash_hmac('sha256', $email . $id . $time, $key));
}

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get JSON data from request body
    $input = json_decode(file_get_contents("php://input"), true);

    // Check if 'email' and 'password' exist in the JSON input
    if (isset($input["email"]) && isset($input["password"])) {
        $customer_email = mysqli_real_escape_string($conn, $input["email"]);
        $customer_password = mysqli_real_escape_string($conn, $input["password"]);

        // Validate required fields
        if (empty($customer_email) || empty($customer_password)) {
            echo json_encode(["error" => "Please fill in both email and password!"]);
            exit;
        }

        // Query to check if the customer exists in the database
        $query = "SELECT * FROM tbl_customer WHERE customer_email = '$customer_email' AND customer_status = 1";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            // Fetch customer data
            $customer = mysqli_fetch_assoc($result);

            // Verify the password
            if (password_verify($customer_password, $customer['customer_password'])) {
                // Generate a simple token
                $token = generateToken($customer_email, $customer['customer_id']);

                // If login is successful, return success message with token and customer data (excluding password)
                unset($customer['customer_password']);
                echo json_encode([
                    "success" => "Login successful!",
                    "token" => $token,
                    "customer" => $customer
                ]);
            } else {
                echo json_encode(["error" => "Incorrect password!"]);
            }
        } else {
            echo json_encode(["error" => "Customer not found or account is inactive!"]);
        }
    } else {
        echo json_encode(["error" => "Please provide both email and password."]);
    }
} else {
    echo json_encode(["error" => "Invalid request method!"]);
}
?>
