<?php
session_start();
include "../config/connection.php";

// Initialize error messages
$error_message = '';
$success_message = '';

// Generate or retrieve the random numbers for the math question
if (!isset($_SESSION['num1']) || !isset($_SESSION['num2'])) {
    $_SESSION['num1'] = rand(1, 10);
    $_SESSION['num2'] = rand(1, 10);
}

// Retrieve numbers from session
$num1 = $_SESSION['num1'];
$num2 = $_SESSION['num2'];

// Calculate the correct answer
$correct_answer = $num1 + $num2;

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form input
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    $user_answer = $_POST['human_test_answer']; // Human test answer

    // Validate username
    if (empty($username)) {
        $error_message = "Username is required!";
    }
    // Validate password
    else if (empty($password)) {
        $error_message = "Password is required!";
    }
    // Validate human test answer
    else if (empty($user_answer)) {
        $error_message = "Please answer the math question!";
    }
    else {
        // Verify if the human test is passed
        if ($user_answer != $correct_answer) { // Check if the answer matches the correct answer
            $error_message = "Please answer the math question correctly to prove you are human!";
        } else {
            // Query to fetch the user by username
            $query = "SELECT * FROM admins WHERE username = '$username' LIMIT 1";
            $result = mysqli_query($conn, $query);

            // If user is found
            if (mysqli_num_rows($result) > 0) {
                $user = mysqli_fetch_assoc($result);

                // Verify the password using password_hash
                if (password_verify($password, $user['password'])) {
                    // Login success
                    $_SESSION['user_role'] = 1;
                    $_SESSION['admin_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    
                    // Clear session math question
                    unset($_SESSION['num1']);
                    unset($_SESSION['num2']);
                    
                    header("Location: ../index.php"); // Redirect to the dashboard
                    exit();
                } else {
                    // Incorrect password
                    $error_message = "Invalid username or password!";
                }
            } else {
                // User not found
                $error_message = "Invalid username or password!";
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        /* Custom Styles */
        body {
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow: hidden;
        }

        /* Moving Background Animation */
        @keyframes moveBackground {
            0% { background-position: 0 0; }
            50% { background-position: 100% 100%; }
            100% { background-position: 0 0; }
        }

        .login-container {
            background-color: rgba(0, 0, 0, 0.6); /* Dark transparent background */
            width: 450px;
            padding: 3rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            border-radius: 10px;
            color: white;
            z-index: 1;
            position: relative;
            overflow: hidden;
        }

        /* Apply the moving background */
        .moving-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('cart-transformed.jpg'); /* Replace with your image path */
            background-size: cover;
            background-position: 0 0;
            animation: moveBackground 30s linear infinite;
            z-index: -1;
        }

        .login-title {
            font-weight: bold;
            font-size: 24px;
            margin-bottom: 1.5rem;
            text-align: center;
            color: #fff;
        }
        .form-control {
            height: 50px;
            font-size: 16px;
            border-radius: 5px;
            border: 2px solid #ccc;
        }
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.5);
        }
        .btn-login {
            background-color: #007bff;
            color: white;
            font-size: 18px;
            padding: 14px;
            width: 100%;
            border-radius: 30px;
        }
        .alert {
            margin-bottom: 1rem;
            padding: 0.75rem;
            border-radius: 5px;
            font-size: 16px;
        }
        .login-footer {
            text-align: center;
            margin-top: 2rem;
            color: lightblue;
            font-weight: bold;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="moving-background"></div>

    <div class="login-container">
        <h2 class="login-title">Admin Login</h2>

        <!-- Display error message if any -->
        <?php if ($error_message) { ?>
            <div class="alert alert-danger" role="alert">
                <?= $error_message ?>
            </div>
        <?php } ?>

        <!-- Login Form -->
        <form action="" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label fw-bold">Username</label>
                <input type="text" class="form-control fw-bold" id="username" name="username" placeholder="Enter username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label fw-bold">Password</label>
                <input type="password" class="form-control fw-bold" id="password" name="password" placeholder="Enter password" required>
            </div>

            <!-- Human Test Question -->
            <div class="mb-3">
                <label for="human_test_answer" class="form-label fw-bold">
                    What is <?= $num1 ?> + <?= $num2 ?>?
                </label>
                <input type="text" class="form-control fw-bold" id="human_test_answer" name="human_test_answer" placeholder="Enter your answer" required>
            </div>

            <button type="submit" class="btn btn-login">Login</button>
        </form>

        <div class="login-footer">
            <blockquote> "&nbsp; This Page Only For Administration &nbsp;"</blockquote>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
