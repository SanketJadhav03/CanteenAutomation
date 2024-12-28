<?php 

include "../config/connection.php";
include "../component/header.php";
include "../component/sidebar.php";

// Check if the form is submitted
if (isset($_POST["create_offer"])) {
    // Sanitize and get form data
    $offer_category = mysqli_real_escape_string($conn, $_POST["offer_category"]);
    $offer_description = mysqli_real_escape_string($conn, $_POST["offer_description"]);
    $offer_dis = mysqli_real_escape_string($conn, $_POST["offer_dis"]);
    $offer_status = 1; // Default active status for new offers
    
    // Image upload processing
    $offer_image = $_FILES["offer_image"]["name"];
    $offer_image_temp = $_FILES["offer_image"]["tmp_name"];
    $image_path = "";

    // Validate required fields
    if (empty($offer_category) || empty($offer_description) || empty($offer_dis)) {
        $_SESSION["error"] = "Please fill in all required fields!";
    } else {
        // Handle image upload if a new image is uploaded
        if (!empty($offer_image)) {
            $target_dir = "../uploads/offers/";
            $target_file = $target_dir . basename($offer_image);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $check = getimagesize($offer_image_temp);

            if ($check === false) {
                $_SESSION["error"] = "File is not an image!";
            } elseif (file_exists($target_file)) {
                $_SESSION["error"] = "Sorry, the file already exists!";
            } elseif ($_FILES["offer_image"]["size"] > 5000000) {
                $_SESSION["error"] = "Sorry, your file is too large!";
            } elseif (!in_array($imageFileType, ["jpg", "jpeg", "png"])) {
                $_SESSION["error"] = "Only JPG, JPEG, & PNG files are allowed!";
            } else {
                if (move_uploaded_file($offer_image_temp, $target_file)) {
                    $image_path = $offer_image;
                } else {
                    $_SESSION["error"] = "Error uploading the image!";
                }
            }
        }

        // Insert query with image path
        $insertQuery = "INSERT INTO tbl_offer (offer_category, offer_description, offer_image, offer_dis, offer_status) 
                        VALUES ('$offer_category', '$offer_description', '$image_path', '$offer_dis', '$offer_status')";
        if (mysqli_query($conn, $insertQuery)) {
            $_SESSION["success"] = "Offer created successfully!";
            echo "<script>window.location = 'index.php';</script>";
        } else {
            $_SESSION["error"] = "Error creating offer: " . mysqli_error($conn);
        }
    }
}
?>

<div class="content-wrapper p-2">
    <form action="" method="post" enctype="multipart/form-data">
        <div class="card">
            <div class="card-header">
                <div class="d-flex p-2 justify-content-between">
                    <div class="h5 font-weight-bold">Create Offer</div>
                    <a href="index.php" class="btn btn-info shadow font-weight-bold">
                        <i class="fa fa-eye"></i>&nbsp; Offer List
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
                    <!-- Offer Category Dropdown -->
                    <div class="col-4">
                        <label for="offer_category">Offer Category <span class="text-danger">*</span></label>
                        <select class="form-control font-weight-bold" name="offer_category" id="offer_category" required>
                            <option value="">Select Category</option>
                            <?php
                            // Fetch categories from tbl_category
                            $categoryQuery = "SELECT * FROM tbl_category WHERE category_status = 1"; // Assuming category_status = 1 means active categories
                            $categoryResult = mysqli_query($conn, $categoryQuery);
                            if (mysqli_num_rows($categoryResult) > 0) {
                                while ($category = mysqli_fetch_assoc($categoryResult)) {
                                    echo "<option value='".$category['category_id']."'>".$category['category_name']."</option>";
                                }
                            } else {
                                echo "<option value=''>No categories available</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-4  ">
                        <label for="offer_dis">Offer Discount (%) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" placeholder="Enter discount value" class="form-control font-weight-bold" name="offer_dis" id="offer_dis" required>
                    </div>
                    <div class="col-4">
                        <label for="offer_image">Offer Image</label>
                        <input type="file" class="form-control font-weight-bold" name="offer_image" id="offer_image" accept="image/*">
                    </div>
                    <div class="col-12 mt-3">
                        <label for="offer_description">Offer Description <span class="text-danger">*</span></label>
                        <textarea rows="5" placeholder="Enter offer description" class="form-control font-weight-bold" name="offer_description" id="offer_description" required></textarea>
                    </div>
                    
                    
                </div>
            </div>

            <div class="card-footer">
                <div class="d-flex justify-content-end">
                    <button name="create_offer" type="submit" class="btn btn-primary shadow font-weight-bold">
                        <i class="fa fa-save"></i>&nbsp; Save Offer
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
