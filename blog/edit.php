<?php
include "../config/connection.php";
include "../component/header.php";
include "../component/sidebar.php";

// Get the blog ID from the URL
$blog_id = $_GET['blog_id'];

// Fetch blog details
$blogQuery = "SELECT * FROM tbl_blog WHERE blog_id = $blog_id";
$blogResult = mysqli_query($conn, $blogQuery);
$blog = mysqli_fetch_assoc($blogResult);

// Check if the form is submitted
if (isset($_POST["blog_update"])) {
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
        // If a new image is uploaded, handle the image upload
        if (!empty($blog_image)) {
            $target_dir = "../uploads/blogs/";
            $target_file = $target_dir . basename($blog_image);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Check if the file is a valid image
            $check = getimagesize($blog_image_temp);
            if ($check === false) {
                $_SESSION["error"] = "File is not an image!";
            } else {
                if (file_exists($target_file)) {
                    $_SESSION["error"] = "Sorry, the file already exists!";
                } elseif ($_FILES["blog_image"]["size"] > 5000000) {
                    $_SESSION["error"] = "Sorry, your file is too large!";
                } elseif ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
                    $_SESSION["error"] = "Sorry, only JPG, JPEG, & PNG files are allowed!";
                } else {
                    if (move_uploaded_file($blog_image_temp, $target_file)) {
                        $updateQuery = "UPDATE tbl_blog SET 
                            blog_title = '$blog_title', 
                            blog_content = '$blog_content', 
                            blog_post_date = '$blog_post_date', 
                            blog_category = '$blog_category', 
                            blog_image_path = '$blog_image' 
                            WHERE blog_id = $blog_id";
                        if (mysqli_query($conn, $updateQuery)) {
                            $_SESSION["success"] = "Blog Updated Successfully!";
                            echo "<script>window.location = 'index.php';</script>";
                        } else {
                            $_SESSION["error"] = "Error in updating blog: " . mysqli_error($conn);
                        }
                    } else {
                        $_SESSION["error"] = "Sorry, there was an error uploading your file.";
                    }
                }
            }
        } else {
            $updateQuery = "UPDATE tbl_blog SET 
                blog_title = '$blog_title', 
                blog_content = '$blog_content', 
                blog_post_date = '$blog_post_date', 
                blog_category = '$blog_category' 
                WHERE blog_id = $blog_id";
            if (mysqli_query($conn, $updateQuery)) {
                $_SESSION["success"] = "Blog Updated Successfully!";
                echo "<script>window.location = 'index.php';</script>";
            } else {
                $_SESSION["error"] = "Error in updating blog: " . mysqli_error($conn);
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
                    <div class="h5 font-weight-bold">Edit Blog Post</div>
                    <a href="index.php" class="btn btn-info shadow font-weight-bold">
                        <i class="fa fa-eye"></i>&nbsp; Blog List
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <label for="blog_title">Blog Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control font-weight-bold" name="blog_title" id="blog_title" value="<?= $blog['blog_title'] ?>" required>
                    </div>

                    <div class="col-6">
                        <label for="blog_category">Blog Category</label>
                        <input type="text" class="form-control font-weight-bold" name="blog_category" id="blog_category" value="<?= $blog['blog_category'] ?>">
                    </div>

                    <div class="col-6 mt-3">
                        <label for="blog_post_date">Post Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control font-weight-bold" name="blog_post_date" id="blog_post_date" value="<?= $blog['blog_post_date'] ?>" required>
                    </div>

                    <div class="col-6 mt-3">
                        <label for="blog_image">Blog Image</label>
                        <input type="file" class="form-control font-weight-bold" name="blog_image" id="blog_image" accept="image/*">
                        <?php if ($blog['blog_image_path']) { ?>
                            <small class="d-block mt-2">Current Image: <img src="../uploads/blogs/<?= $blog['blog_image_path'] ?>" width="100px" alt="Blog Image"></small>
                        <?php } ?>
                    </div>

                    <div class="col-12 mt-3">
                        <label for="blog_content">Blog Content <span class="text-danger">*</span></label>
                        <textarea class="form-control font-weight-bold" name="blog_content" id="blog_content" rows="6" required><?= $blog['blog_content'] ?></textarea>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <div class="d-flex p-2 justify-content-end">
                    <button name="blog_update" type="submit" class="btn btn-primary shadow font-weight-bold">
                        <i class="fa fa-save"></i>&nbsp; Update Blog
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
