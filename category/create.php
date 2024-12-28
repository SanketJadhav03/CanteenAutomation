<?php
include "../config/connection.php";
include "../component/header.php";
include "../component/sidebar.php";

// Check if the form is submitted
if (isset($_POST["category_create"])) {
    // Sanitize and get form data
    $category_name = mysqli_real_escape_string($conn, $_POST["category_name"]);
    $category_image = $_FILES["category_image"]["name"];
    $category_image_temp = $_FILES["category_image"]["tmp_name"];

    // Validate required fields
    if (empty($category_name)) {
        $_SESSION["error"] = "Category Name is required!";
    } else {
        // If a new image is uploaded, handle the image upload
        if (!empty($category_image)) {
            // Define the target directory for image upload
            $target_dir = "../uploads/categories/";
            $target_file = $target_dir . basename($category_image);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Check if the file is a valid image
            $check = getimagesize($category_image_temp);
            if ($check === false) {
                $_SESSION["error"] = "File is not an image!";
            } else {
                // Check if file already exists
                if (file_exists($target_file)) {
                    $_SESSION["error"] = "Sorry, the file already exists!";
                } 
                // Check file size (limit to 5MB)
                elseif ($_FILES["category_image"]["size"] > 5000000) {
                    $_SESSION["error"] = "Sorry, your file is too large!";
                }
                // Allow only certain file formats (e.g., jpg, png, jpeg)
                elseif ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
                    $_SESSION["error"] = "Sorry, only JPG, JPEG, & PNG files are allowed!";
                } else {
                    // Move the uploaded file to the target directory
                    if (move_uploaded_file($category_image_temp, $target_file)) {
                        // Insert query according to the table structure with the new image
                        $insertQuery = "INSERT INTO tbl_category (category_name, category_image) 
                                        VALUES ('$category_name', '$category_image')";

                        // Execute query
                        if (mysqli_query($conn, $insertQuery)) {
                            $_SESSION["success"] = "Category Created Successfully!";
                            echo "<script>window.location = 'index.php';</script>";
                        } else {
                            $_SESSION["error"] = "Error in creating category: " . mysqli_error($conn);
                        }
                    } else {
                        $_SESSION["error"] = "Sorry, there was an error uploading your file.";
                    }
                }
            }
        } else {
            // If no image is uploaded, just insert the category name
            $insertQuery = "INSERT INTO tbl_category (category_name) 
                            VALUES ('$category_name')";

            // Execute query
            if (mysqli_query($conn, $insertQuery)) {
                $_SESSION["success"] = "Category Created Successfully!";
                echo "<script>window.location = 'index.php';</script>";
            } else {
                $_SESSION["error"] = "Error in creating category: " . mysqli_error($conn);
            }
        }
    }
}
?>

<div class="content-wrapper p-2">
    <form action="" method="post" enctype="multipart/form-data" onsubmit="validation();">
        <div class="card">
            <div class="card-header">
                <div class="d-flex p-2 justify-content-between">
                    <div class="h5 font-weight-bold">Create New Category</div>
                    <a href="index.php" class="btn btn-info shadow font-weight-bold">
                        <i class="fa fa-eye"></i>&nbsp; Category List
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Category Name -->
                    <div class="col-6">
                        <label for="category_name">Category Name <span class="text-danger">*</span></label>
                        <input type="text" placeholder="Category Name" class="form-control font-weight-bold" name="category_name" id="category_name" required>
                    </div>

                    <!-- Category Image -->
                    <div class="col-6  ">
                        <label for="category_image">Category Image</label>
                        <input type="file" class="form-control font-weight-bold" name="category_image" id="category_image" accept="image/*">
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <div class="d-flex p-2 justify-content-end">
                    <button name="category_create" type="submit" class="btn btn-primary shadow font-weight-bold">
                        <i class="fa fa-save"></i>&nbsp; Create Category
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

<script>
    function validation() {
        var category_name = document.getElementById("category_name");
        if (category_name.value == "") {
            category_name.focus();
            event.preventDefault();
        }
    }
</script>

<?php
include "../component/footer.php";
?>
