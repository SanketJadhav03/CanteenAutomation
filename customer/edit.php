<?php
include "../config/connection.php";
include "../component/header.php";
include "../component/sidebar.php";

// Get the customer ID from the URL
$customer_id = $_GET['customer_id'];

// Fetch customer details
$customerQuery = "SELECT * FROM tbl_customer WHERE customer_id = $customer_id";
$customerResult = mysqli_query($conn, $customerQuery);
$customer = mysqli_fetch_assoc($customerResult);

// Check if the form is submitted
if (isset($_POST["customer_update"])) {
    // Sanitize and get form data
    $customer_name = mysqli_real_escape_string($conn, $_POST["customer_name"]);
    $customer_email = mysqli_real_escape_string($conn, $_POST["customer_email"]);
    $customer_password = mysqli_real_escape_string($conn, $_POST["customer_password"]);
    $customer_phone = mysqli_real_escape_string($conn, $_POST["customer_phone"]);
    $customer_address = mysqli_real_escape_string($conn, $_POST["customer_address"]);
    $customer_status = isset($_POST["customer_status"]) ? 1 : 0;
    $customer_image = $_FILES["customer_image"]["name"];
    $customer_image_temp = $_FILES["customer_image"]["tmp_name"];

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
        } elseif (!in_array($imageFileType, ["jpg", "png", "jpeg"])) {
            $_SESSION["error"] = "Only JPG, JPEG, & PNG files are allowed!";
        } else {
            if (move_uploaded_file($customer_image_temp, $target_file)) {
                $image_path = $customer_image;
            } else {
                $_SESSION["error"] = "Error uploading the image!";
            }
        }
    } else {
        $image_path = $customer['customer_image']; // Keep existing image if no new one is uploaded
    }

    // Update query
    $updateQuery = "UPDATE tbl_customer SET 
        customer_name = '$customer_name',
        customer_email = '$customer_email',
        customer_password = '$customer_password',
        customer_phone = '$customer_phone',
        customer_address = '$customer_address',
        customer_status = '$customer_status',
        customer_image = '$image_path'
        WHERE customer_id = $customer_id";

    if (mysqli_query($conn, $updateQuery)) {
        $_SESSION["success"] = "Profile Updated Successfully!";
        echo "<script>window.location = 'index.php';</script>"; // Redirect to profile page
    } else {
        $_SESSION["error"] = "Error updating profile: " . mysqli_error($conn);
    }
}
?>

<div class="content-wrapper p-2">
    <form action="" method="post" enctype="multipart/form-data">
        <div class="card">
            <div class="card-header">
                <div class="d-flex p-2 justify-content-between">
                    <div class="h5 font-weight-bold">Edit Customer</div>
                    <a href="index.php" class="btn btn-info shadow font-weight-bold">
                        <i class="fa fa-eye"></i>&nbsp; View Customers
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-4">
                        <label for="customer_name">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control font-weight-bold" name="customer_name" id="customer_name" value="<?= $customer['customer_name'] ?>" required>
                    </div>
                    <div class="col-4">
                        <label for="customer_email">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control font-weight-bold" name="customer_email" id="customer_email" value="<?= $customer['customer_email'] ?>" required>
                    </div>
                    <div class="col-4">
                        <label for="customer_password">Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control font-weight-bold" name="customer_password" id="customer_password" value="<?= $customer['customer_password'] ?>" required>
                    </div>
                    <div class="col-6 mt-3">
                        <label for="customer_phone">Phone Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control font-weight-bold" name="customer_phone" id="customer_phone" value="<?= $customer['customer_phone'] ?>" required>
                    </div>
                    <div class="col-6 mt-3">
                        <label for="customer_image">Profile Image</label>
                        <input type="file" class="form-control font-weight-bold" name="customer_image" id="customer_image" accept="image/*">
                        <?php if ($customer['customer_image']) { ?>
                            <small class="d-block mt-2">Current Image: <img src="../uploads/customers/<?= $customer['customer_image'] ?>" width="100px" alt="Profile Image"></small>
                        <?php } ?>
                    </div>
                    <div class="col-12 mt-3">
                        <label for="customer_address">Address <span class="text-danger">*</span></label>
                        <textarea name="customer_address" id="customer_address" rows="3" class="form-control font-weight-bold"><?= $customer['customer_address'] ?></textarea>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <div class="d-flex justify-content-end">
                    <button name="customer_update" type="submit" class="btn btn-primary shadow font-weight-bold">
                        <i class="fa fa-save"></i>&nbsp; Update Profile
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