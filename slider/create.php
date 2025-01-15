<?php 

include "../config/connection.php";
include "../component/header.php";
include "../component/sidebar.php";

// Check if the form is submitted
if (isset($_POST["create_slider"])) {
    // Default active status for new sliders
    $slider_status = 1; 

    // Image upload processing
    $slider_image = $_FILES["slider_image"]["name"];
    $slider_image_temp = $_FILES["slider_image"]["tmp_name"];
    $image_path = "";

    // Validate required fields
    if (empty($slider_image)) {
        $_SESSION["error"] = "Please upload a slider image!";
    } else {
        // Handle image upload
        $target_dir = "../uploads/sliders/";
        $target_file = $target_dir . basename($slider_image);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $check = getimagesize($slider_image_temp);

        if ($check === false) {
            $_SESSION["error"] = "File is not an image!";
        } elseif (file_exists($target_file)) {
            $_SESSION["error"] = "Sorry, the file already exists!";
        } elseif ($_FILES["slider_image"]["size"] > 5000000) {
            $_SESSION["error"] = "Sorry, your file is too large!";
        } elseif (!in_array($imageFileType, ["jpg", "jpeg", "png"])) {
            $_SESSION["error"] = "Only JPG, JPEG, & PNG files are allowed!";
        } else {
            if (move_uploaded_file($slider_image_temp, $target_file)) {
                $image_path = $slider_image;
            } else {
                $_SESSION["error"] = "Error uploading the image!";
            }
        }

        // Insert query with image path
        if (!empty($image_path)) {
            $insertQuery = "INSERT INTO tbl_slider (slider_image, slider_status) VALUES ('$image_path', '$slider_status')";
            if (mysqli_query($conn, $insertQuery)) {
                $_SESSION["success"] = "Slider created successfully!";
                echo "<script>window.location = 'index.php';</script>";
            } else {
                $_SESSION["error"] = "Error creating slider: " . mysqli_error($conn);
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
                    <div class="h5 font-weight-bold">Create Slider</div>
                    <a href="index.php" class="btn btn-info shadow font-weight-bold">
                        <i class="fa fa-eye"></i>&nbsp; Slider List
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
                    <!-- Slider Image Upload -->
                    <div class="col-6">
                        <label for="slider_image">Slider Image <span class="text-danger">*</span></label>
                        <input type="file" class="form-control font-weight-bold" name="slider_image" id="slider_image" accept="image/*" required>
                    </div>
                    <!-- Slider Status -->
                    <div class="col-6">
                        <label for="slider_status">Slider Status</label>
                        <select class="form-control font-weight-bold" name="slider_status" id="slider_status">
                            <option value="1" selected>Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <div class="d-flex justify-content-end">
                    <button name="create_slider" type="submit" class="btn btn-primary shadow font-weight-bold">
                        <i class="fa fa-save"></i>&nbsp; Save Slider
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
