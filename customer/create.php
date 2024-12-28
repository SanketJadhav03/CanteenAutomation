<?php 

include "../config/connection.php";
include "../component/header.php";
include "../component/sidebar.php";

// Check if the form is submitted
if (isset($_POST["register_customer"])) {
    // Sanitize and get form data
    $customer_name = mysqli_real_escape_string($conn, $_POST["customer_name"]);
    $customer_email = mysqli_real_escape_string($conn, $_POST["customer_email"]);
    $customer_password = mysqli_real_escape_string($conn, $_POST["customer_password"]);
    $confirm_password = mysqli_real_escape_string($conn, $_POST["confirm_password"]);
    $customer_phone = mysqli_real_escape_string($conn, $_POST["customer_phone"]);
    $customer_address = mysqli_real_escape_string($conn, $_POST["customer_address"]);
    $customer_status = 1;  // Assuming a default active status of 1 when a new customer is created
    
    // Image upload processing
    $customer_image = $_FILES["customer_image"]["name"];
    $customer_image_temp = $_FILES["customer_image"]["tmp_name"];
    $image_path = "";

    // Validate required fields
    if (empty($customer_name) || empty($customer_email) || empty($customer_password) || empty($confirm_password) || empty($customer_phone) || empty($customer_address)) {
        $_SESSION["error"] = "Please fill in all required fields!";
    } elseif ($customer_password !== $confirm_password) {
        $_SESSION["error"] = "Passwords do not match!";
    } else {
        // Check if email already exists
        $checkEmailQuery = "SELECT * FROM tbl_customer WHERE customer_email = '$customer_email'";
        $checkEmailResult = mysqli_query($conn, $checkEmailQuery);
        if (mysqli_num_rows($checkEmailResult) > 0) {
            $_SESSION["error"] = "Email is already registered!";
        } else {
            // Hash the password for security
            $hashed_password = password_hash($customer_password, PASSWORD_DEFAULT);

            // Handle image upload if a new image is uploaded
            if (!empty($customer_image)) {
                $target_dir = "../uploads/customers/";
                $target_file = $target_dir . basename($customer_image);
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                $check = getimagesize($customer_image_temp);

                if ($check === false) {
                    $_SESSION["error"] = "File is not an image!";
                } elseif (file_exists($target_file)) {
                    $_SESSION["error"] = "Sorry, the file already exists!";
                } elseif ($_FILES["customer_image"]["size"] > 5000000) {
                    $_SESSION["error"] = "Sorry, your file is too large!";
                } elseif (!in_array($imageFileType, ["jpg", "jpeg", "png"])) {
                    $_SESSION["error"] = "Only JPG, JPEG, & PNG files are allowed!";
                } else {
                    if (move_uploaded_file($customer_image_temp, $target_file)) {
                        $image_path = $customer_image;
                    } else {
                        $_SESSION["error"] = "Error uploading the image!";
                    }
                }
            }

            // Insert query with image path
            $insertQuery = "INSERT INTO tbl_customer (customer_name, customer_email, customer_password, customer_phone, customer_address, customer_status, customer_image) 
                            VALUES ('$customer_name', '$customer_email', '$hashed_password', '$customer_phone', '$customer_address', '$customer_status', '$image_path')";
            if (mysqli_query($conn, $insertQuery)) {
                $_SESSION["success"] = "Customer registered successfully!";
                echo "<script>window.location = 'index.php';</script>";
            } else {
                $_SESSION["error"] = "Error registering customer: " . mysqli_error($conn);
            }
        }
    }
}
?>

<div class="content-wrapper p-2">
    <form action="" method="post" enctype="multipart/form-data">
        <div class="card">
            <div class="card-header">
                <div class="d-flex p-2 justify-content-between">
                    <div class="h5 font-weight-bold">Create Customer</div>
                    <a href="index.php" class="btn btn-info shadow font-weight-bold">
                        <i class="fa fa-eye"></i>&nbsp; Customer List
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Display error or success messages -->
                <?php
                if (isset($_SESSION["error"])) {
                    echo "<p style='color: red;'>".$_SESSION["error"]."</p>";
                    unset($_SESSION["error"]);
                }
                if (isset($_SESSION["success"])) {
                    echo "<p style='color: green;'>".$_SESSION["success"]."</p>";
                    unset($_SESSION["success"]);
                }
                ?>
                
                <div class="row">
                    <div class="col-4">
                        <label for="customer_name">Full Name <span class="text-danger">*</span></label>
                        <input type="text" placeholder="Enter full name" class="form-control font-weight-bold" name="customer_name" id="customer_name" required>
                    </div>
                    <div class="col-4">
                        <label for="customer_email">Email <span class="text-danger">*</span></label>
                        <input type="email" placeholder="Enter email" class="form-control font-weight-bold" name="customer_email" id="customer_email" required>
                    </div>
                    <div class="col-4  ">
                        <label for="customer_phone">Phone Number <span class="text-danger">*</span></label>
                        <input type="text" placeholder="Enter phone number" class="form-control font-weight-bold" name="customer_phone" id="customer_phone" required>
                    </div>
                    
                    <div class="col-4 mt-3">
                        <label for="customer_password">Password <span class="text-danger">*</span></label>
                        <input type="password" placeholder="Enter password" class="form-control font-weight-bold" name="customer_password" id="customer_password" required>
                    </div>
                    <div class="col-4 mt-3">
                        <label for="confirm_password">Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" placeholder="Confirm password" class="form-control font-weight-bold" name="confirm_password" id="confirm_password" required>
                    </div>
                    <div class="col-4 mt-3">
                        <label for="customer_image">Profile Image</label>
                        <input type="file" class="form-control font-weight-bold" name="customer_image" id="customer_image" accept="image/*">
                    </div>
                    <div class="col-12 mt-3">
                        <label for="customer_address">Address <span class="text-danger">*</span></label>
                        <textarea rows="5" type="text" placeholder="Enter address" class="form-control font-weight-bold" name="customer_address" id="customer_address" required></textarea>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <div class="d-flex justify-content-end">
                    <button name="register_customer" type="submit" class="btn btn-primary shadow font-weight-bold">
                        <i class="fa fa-save"></i>&nbsp; Register Customer
                    </button>
                    &nbsp;
                    <button type="reset" class="btn btn-danger shadow font-weight-bold">
                        <i class="fas fa-times"></i>&nbsp; Clear
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<?php include "../component/footer.php"; ?>
