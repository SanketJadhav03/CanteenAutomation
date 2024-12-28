<?php 

include "../config/connection.php";
include "../component/header.php";
include "../component/sidebar.php";

// Check if the offer_id is set in the URL
if (isset($_GET['offer_id'])) {
    $offer_id = $_GET['offer_id'];

    // Fetch the offer details from the database
    $offerQuery = "SELECT o.offer_id, o.offer_category, o.offer_description, o.offer_dis, o.offer_image, o.offer_status, c.category_name 
                   FROM tbl_offer o
                   LEFT JOIN tbl_category c ON o.offer_category = c.category_id
                   WHERE o.offer_id = '$offer_id'";
    $offerResult = mysqli_query($conn, $offerQuery);

    if (mysqli_num_rows($offerResult) > 0) {
        $offer = mysqli_fetch_assoc($offerResult);
    } else {
        $_SESSION["error"] = "Offer not found!";
        header("Location: index.php");
        exit();
    }
}

// Check if the form is submitted
if (isset($_POST["update_offer"])) {
    // Sanitize and get form data
    $offer_category = mysqli_real_escape_string($conn, $_POST["offer_category"]);
    $offer_description = mysqli_real_escape_string($conn, $_POST["offer_description"]);
    $offer_dis = mysqli_real_escape_string($conn, $_POST["offer_dis"]);
    $offer_status = isset($_POST["offer_status"]) ? 1 : 0; // Active if checked, otherwise inactive
    
    // Image upload processing
    $offer_image = $_FILES["offer_image"]["name"];
    $offer_image_temp = $_FILES["offer_image"]["tmp_name"];
    $image_path = $offer['offer_image']; // Keep old image by default

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
                    $image_path = $offer_image; // Use the new image path
                } else {
                    $_SESSION["error"] = "Error uploading the image!";
                }
            }
        }

        // Update query with image path
        $updateQuery = "UPDATE tbl_offer SET 
                        offer_category = '$offer_category',
                        offer_description = '$offer_description',
                        offer_dis = '$offer_dis',
                        offer_image = '$image_path',
                        offer_status = '$offer_status'
                        WHERE offer_id = '$offer_id'";

        if (mysqli_query($conn, $updateQuery)) {
            $_SESSION["success"] = "Offer updated successfully!";
            echo "<script>window.location = 'index.php';</script>";
        } else {
            $_SESSION["error"] = "Error updating offer: " . mysqli_error($conn);
        }
    }
}

?>

<div class="content-wrapper p-2">
    <form action="" method="post" enctype="multipart/form-data">
        <div class="card">
            <div class="card-header">
                <div class="d-flex p-2 justify-content-between">
                    <div class="h5 font-weight-bold">Edit Offer</div>
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
                    <div class="col-4">
                        <label for="offer_category">Offer Category <span class="text-danger">*</span></label>
                        <select class="form-control font-weight-bold" name="offer_category" id="offer_category" required>
                            <option value="">Select Category</option>
                            <?php
                            // Fetch all categories
                            $categoryQuery = "SELECT * FROM tbl_category";
                            $categoryResult = mysqli_query($conn, $categoryQuery);
                            while ($category = mysqli_fetch_assoc($categoryResult)) {
                                $selected = ($category['category_id'] == $offer['offer_category']) ? 'selected' : '';
                                echo "<option value='{$category['category_id']}' $selected>{$category['category_name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-4  ">
                        <label for="offer_dis">Offer Discount (%) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" placeholder="Enter discount value" class="form-control font-weight-bold" name="offer_dis" id="offer_dis" value="<?php echo $offer['offer_dis']; ?>" required>
                    </div>
                    <div class="col-4  ">
                        <label for="offer_image">Offer Image</label>
                        <input type="file" class="form-control font-weight-bold" name="offer_image" id="offer_image" accept="image/*">
                        <?php if (!empty($offer['offer_image'])) { ?>
                            <img src="../uploads/offers/<?php echo $offer['offer_image']; ?>" alt="Offer Image" class="mt-2" width="100">
                        <?php } ?>
                    </div>
                    <div class="col-12 mt-3">
                        <label for="offer_description">Offer Description <span class="text-danger">*</span></label>
                        <textarea rows="5" placeholder="Enter offer description" class="form-control font-weight-bold" name="offer_description" id="offer_description" required><?php echo $offer['offer_description']; ?></textarea>
                    </div>
                 
                    <div class="col-4 mt-3">
                        <label for="offer_status">Offer Status</label>
                        <br>
                        <input type="checkbox" name="offer_status" id="offer_status" value="1" <?php echo ($offer['offer_status'] == 1) ? 'checked' : ''; ?>>
                        <label for="offer_status">Active</label>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <div class="d-flex justify-content-end">
                    <button name="update_offer" type="submit" class="btn btn-primary shadow font-weight-bold">
                        <i class="fa fa-save"></i>&nbsp; Update Offer
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
