<?php 

include "../config/connection.php";
include "../component/header.php";
include "../component/sidebar.php";

// Check if the slider_id is set in the URL
if (isset($_GET['slider_id'])) {
    $slider_id = $_GET['slider_id'];

    // Fetch the slider details from the database
    $sliderQuery = "SELECT slider_id, slider_image, slider_status 
                    FROM tbl_slider 
                    WHERE slider_id = '$slider_id'";
    $sliderResult = mysqli_query($conn, $sliderQuery);

    if (mysqli_num_rows($sliderResult) > 0) {
        $slider = mysqli_fetch_assoc($sliderResult);
    } else {
        $_SESSION["error"] = "Slider not found!";
        header("Location: index.php");
        exit();
    }
}

// Check if the form is submitted
if (isset($_POST["update_slider"])) {
    // Sanitize and get form data
    $slider_status = isset($_POST["slider_status"]) ? 1 : 0; // Active if checked, otherwise inactive
    
    // Image upload processing
    $slider_image = $_FILES["slider_image"]["name"];
    $slider_image_temp = $_FILES["slider_image"]["tmp_name"];
    $image_path = $slider['slider_image']; // Keep old image by default

    // Validate image upload if a new image is uploaded
    if (!empty($slider_image)) {
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
                $image_path = $slider_image; // Use the new image path
            } else {
                $_SESSION["error"] = "Error uploading the image!";
            }
        }
    }

    // Update query with image path
    $updateQuery = "UPDATE tbl_slider SET 
                    slider_image = '$image_path',
                    slider_status = '$slider_status'
                    WHERE slider_id = '$slider_id'";

    if (mysqli_query($conn, $updateQuery)) {
        $_SESSION["success"] = "Slider updated successfully!";
        echo "<script>window.location = 'index.php';</script>";
    } else {
        $_SESSION["error"] = "Error updating slider: " . mysqli_error($conn);
    }
}

?>

<div class="content-wrapper p-2">
    <form action="" method="post" enctype="multipart/form-data">
        <div class="card">
            <div class="card-header">
                <div class="d-flex p-2 justify-content-between">
                    <div class="h5 font-weight-bold">Edit Slider</div>
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
                    <div class="col-6">
                        <label for="slider_image">Slider Image</label>
                        <input type="file" class="form-control font-weight-bold" name="slider_image" id="slider_image" accept="image/*">
                        <?php if (!empty($slider['slider_image'])) { ?>
                            <img src="../uploads/sliders/<?php echo $slider['slider_image']; ?>" alt="Slider Image" class="mt-2" width="100">
                        <?php } ?>
                    </div>

                    <div class="col-6 mt-3">
                        <label for="slider_status">Slider Status</label>
                        <br>
                        <input type="checkbox" name="slider_status" id="slider_status" value="1" <?php echo ($slider['slider_status'] == 1) ? 'checked' : ''; ?>>
                        <label for="slider_status">Active</label>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <div class="d-flex justify-content-end">
                    <button name="update_slider" type="submit" class="btn btn-primary shadow font-weight-bold">
                        <i class="fa fa-save"></i>&nbsp; Update Slider
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
