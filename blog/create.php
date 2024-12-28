<?php
include "../config/connection.php";
include "../component/header.php";
include "../component/sidebar.php";

// Check if the form is submitted
if (isset($_POST["blog_create"])) {
    // Sanitize and get form data
    $blog_title = mysqli_real_escape_string($conn, $_POST["blog_title"]);
    $blog_content = mysqli_real_escape_string($conn, $_POST["blog_content"]);
    $blog_category = mysqli_real_escape_string($conn, $_POST["blog_category"]);
    $blog_post_date = mysqli_real_escape_string($conn, $_POST["blog_post_date"]);
    $blog_image = $_FILES["blog_image"]["name"];
    $blog_image_temp = $_FILES["blog_image"]["tmp_name"];

    // Validate required fields
    if (empty($blog_title) || empty($blog_content) || empty($blog_post_date)) {
        $_SESSION["error"] = "Title, Content, and Post Date are required!";
    } else {
        // If an image is uploaded, handle the image upload
        if (!empty($blog_image)) {
            // Define the target directory for image upload
            $target_dir = "../uploads/blogs/";
            $target_file = $target_dir . basename($blog_image);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Check if the file is a valid image
            $check = getimagesize($blog_image_temp);
            if ($check === false) {
                $_SESSION["error"] = "File is not an image!";
            } else {
                // Check if file already exists
                if (file_exists($target_file)) {
                    $_SESSION["error"] = "Sorry, the file already exists!";
                }
                // Check file size (limit to 5MB)
                elseif ($_FILES["blog_image"]["size"] > 5000000) {
                    $_SESSION["error"] = "Sorry, your file is too large!";
                }
                // Allow only certain file formats (e.g., jpg, png, jpeg)
                elseif ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
                    $_SESSION["error"] = "Sorry, only JPG, JPEG, & PNG files are allowed!";
                } else {
                    // Move the uploaded file to the target directory
                    if (move_uploaded_file($blog_image_temp, $target_file)) {
                        // Insert query with image
                        $insertQuery = "INSERT INTO tbl_blog (blog_title, blog_content, blog_post_date, blog_category, blog_image_path) 
                                        VALUES ('$blog_title', '$blog_content', '$blog_post_date', '$blog_category', '$blog_image')";

                        // Execute query
                        if (mysqli_query($conn, $insertQuery)) {
                            $_SESSION["success"] = "Blog Created Successfully!";
                            echo "<script>window.location = 'index.php';</script>";
                        } else {
                            $_SESSION["error"] = "Error in creating blog: " . mysqli_error($conn);
                        }
                    } else {
                        $_SESSION["error"] = "Sorry, there was an error uploading your file.";
                    }
                }
            }
        } else {
            // If no image is uploaded, just insert the data without image
            $insertQuery = "INSERT INTO tbl_blog (blog_title, blog_content, blog_post_date, blog_category) 
                            VALUES ('$blog_title', '$blog_content', '$blog_post_date', '$blog_category')";

            // Execute query
            if (mysqli_query($conn, $insertQuery)) {
                $_SESSION["success"] = "Blog Created Successfully!";
                echo "<script>window.location = 'index.php';</script>";
            } else {
                $_SESSION["error"] = "Error in creating blog: " . mysqli_error($conn);
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
                    <div class="h5 font-weight-bold">Create New Blog Post</div>
                    <a href="index.php" class="btn btn-info shadow font-weight-bold">
                        <i class="fa fa-eye"></i>&nbsp; Blog List
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Blog Title -->
                    <div class="col-6">
                        <label for="blog_title">Blog Title <span class="text-danger">*</span></label>
                        <input type="text" placeholder="Blog Title" class="form-control font-weight-bold" name="blog_title" id="blog_title" required>
                    </div>

                    <!-- Blog Category -->
                    <div class="col-6">
                        <label for="blog_category">Blog Category</label>
                        <input type="text" placeholder="Blog Category" class="form-control font-weight-bold" name="blog_category" id="blog_category">
                    </div>

                    

                    <!-- Blog Post Date -->
                    <div class="col-6 mt-3">
                        <label for="blog_post_date">Post Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control font-weight-bold" name="blog_post_date" id="blog_post_date" required>
                    </div>

                    <!-- Blog Image -->
                    <div class="col-6 mt-3">
                        <label for="blog_image">Blog Image</label>
                        <input type="file" class="form-control font-weight-bold" name="blog_image" id="blog_image" accept="image/*">
                    </div>
                    <!-- Blog Content -->
                    <div class="col-12 mt-3">
                        <label for="blog_content">Blog Content <span class="text-danger">*</span></label>
                        <textarea placeholder="Blog Content" class="form-control font-weight-bold" name="blog_content" id="blog_content" rows="6" required></textarea>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <div class="d-flex p-2 justify-content-end">
                    <button name="blog_create" type="submit" class="btn btn-primary shadow font-weight-bold">
                        <i class="fa fa-save"></i>&nbsp; Create Blog
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
        var blog_title = document.getElementById("blog_title");
        var blog_content = document.getElementById("blog_content");
        var blog_post_date = document.getElementById("blog_post_date");

        if (blog_title.value == "" || blog_content.value == "" || blog_post_date.value == "") {
            alert("Please fill all required fields!");
            event.preventDefault();
        }
    }
</script>

<?php
include "../component/footer.php";
?>
