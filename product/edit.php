<?php
include "../config/connection.php";
include "../component/header.php";
include "../component/sidebar.php";

// Get the product ID from the URL
$product_id = $_GET['product_id'];

// Fetch product details
$productQuery = "SELECT * FROM tbl_product WHERE product_id = $product_id";
$productResult = mysqli_query($conn, $productQuery);
$product = mysqli_fetch_assoc($productResult);

// Check if the form is submitted
if (isset($_POST["product_update"])) {
    // Sanitize and get form data
    $product_name = mysqli_real_escape_string($conn, $_POST["product_name"]);
    $product_description = mysqli_real_escape_string($conn, $_POST["product_description"]);
    $product_price = mysqli_real_escape_string($conn, $_POST["product_price"]); 
    $category_id = mysqli_real_escape_string($conn, $_POST["category_id"]);
    $product_dis = mysqli_real_escape_string($conn, $_POST["product_dis"]);
    $product_dis_value = mysqli_real_escape_string($conn, $_POST["product_dis_value"]);
    $product_status = mysqli_real_escape_string($conn, $_POST["product_status"]);
    $product_image = $_FILES["product_image"]["name"];
    $product_image_temp = $_FILES["product_image"]["tmp_name"];
    
    // Handle image upload if a new image is uploaded
    if (!empty($product_image)) {
        $target_dir = "../uploads/products/";
        $target_file = $target_dir . basename($product_image);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $check = getimagesize($product_image_temp);

        if ($check === false) {
            $_SESSION["error"] = "File is not an image!";
        } elseif (file_exists($target_file)) {
            $_SESSION["error"] = "Sorry, the file already exists!";
        } elseif ($_FILES["product_image"]["size"] > 5000000) {
            $_SESSION["error"] = "Sorry, your file is too large!";
        } elseif (!in_array($imageFileType, ["jpg", "png", "jpeg"])) {
            $_SESSION["error"] = "Only JPG, JPEG, & PNG files are allowed!";
        } else {
            if (move_uploaded_file($product_image_temp, $target_file)) {
                $image_path = $product_image;
            } else {
                $_SESSION["error"] = "Error uploading the image!";
            }
        }
    } else {
        $image_path = $product['product_image']; // Keep existing image if no new one is uploaded
    }

    // Update query
    $updateQuery = "UPDATE tbl_product SET 
        product_name = '$product_name',
        product_description = '$product_description',
        product_price = '$product_price', 
        category_id = '$category_id',
        product_image = '$image_path',
        product_dis = '$product_dis',
        product_dis_value = '$product_dis_value',
        product_status = '$product_status'
        WHERE product_id = $product_id";

    if (mysqli_query($conn, $updateQuery)) {
        $_SESSION["success"] = "Product Updated Successfully!";
        echo "<script>window.location = 'index.php';</script>";
    } else {
        $_SESSION["error"] = "Error updating product: " . mysqli_error($conn);
    }
}
?>

<div class="content-wrapper p-2">
    <form action="" method="post" enctype="multipart/form-data">
        <div class="card">
            <div class="card-header">
                <div class="d-flex p-2 justify-content-between">
                    <div class="h5 font-weight-bold">Edit Product</div>
                    <a href="index.php" class="btn btn-info shadow font-weight-bold">
                        <i class="fa fa-eye"></i>&nbsp; Product List
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <label for="product_name">Product Name <span class="text-danger">*</span></label>
                        <input type="text" value="<?= $product['product_name'] ?>" placeholder="Product Name" class="form-control font-weight-bold" name="product_name" id="product_name" required>
                    </div>
                    <div class="col-4">
                        <label for="category_id">Category <span class="text-danger">*</span></label>
                        <select name="category_id" id="category_id" class="form-control font-weight-bold" required>
                            <option value="">Select Category</option>
                            <?php
                            $categoryQuery = "SELECT * FROM tbl_category WHERE category_status = 1";
                            $categoryResult = mysqli_query($conn, $categoryQuery);
                            while ($category = mysqli_fetch_assoc($categoryResult)) {
                                $selected = $product['category_id'] == $category['category_id'] ? "selected" : "";
                                echo "<option value='{$category['category_id']}' $selected>{$category['category_name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-2">
                        <label for="product_status">Food Type <span class="text-danger">*</span></label>
                        <select name="product_status" id="product_status" class="form-control font-weight-bold" required>
                            <option value="">Select Food Type</option>
                            <option value="1" <?= $product['product_status'] == 1 ? "selected" : "" ?>>Veg</option>
                            <option value="2" <?= $product['product_status'] == 2 ? "selected" : "" ?>>Non-Veg</option>
                        </select>
                    </div>
                    <div class="col-3 mt-3">
                        <label for="product_price">Product Price <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" value="<?= $product['product_price'] ?>" placeholder="Product Price" class="form-control font-weight-bold" name="product_price" id="product_price" required oninput="calculateDiscountValue()">
                    </div>
                    <div class="col-3 mt-3">
                        <label for="product_dis">Product Discount (%)</label>
                        <input type="number" value="<?= $product['product_dis'] ?>" placeholder="Discount Percentage" class="form-control font-weight-bold" name="product_dis" id="product_dis" oninput="calculateDiscountValue()">
                    </div>
                    <div class="col-3 mt-3">
                        <label for="product_dis_value">Discount Value</label>
                        <input type="number" step="0.01" value="<?= $product['product_dis_value'] ?>" placeholder="Discount Value" class="form-control font-weight-bold" name="product_dis_value" id="product_dis_value" readonly>
                    </div>
                    <div class="col-3 mt-3">
                        <label for="product_image">Product Image</label>
                        <input type="file" class="form-control font-weight-bold" name="product_image" id="product_image" accept="image/*">
                        <?php if (!empty($product['product_image'])): ?>
                            <img src="../uploads/products/<?= $product['product_image'] ?>" alt="Product Image" width="100" class="mt-2">
                        <?php endif; ?>
                    </div>
                    <div class="col-12 mt-3">
                        <label for="product_description">Product Description</label>
                        <textarea rows="10" name="product_description" id="product_description" class="form-control font-weight-bold" placeholder="Enter product description"><?= $product['product_description'] ?></textarea>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-end">
                    <button name="product_update" type="submit" class="btn btn-primary shadow font-weight-bold">
                        <i class="fa fa-save"></i>&nbsp; Update Product
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
    function calculateDiscountValue() {
        const price = parseFloat(document.getElementById("product_price").value);
        const discountPercentage = parseFloat(document.getElementById("product_dis").value);
        
        if (!isNaN(price) && !isNaN(discountPercentage)) {
            const discountValue = (price * discountPercentage) / 100;
            document.getElementById("product_dis_value").value = discountValue.toFixed(2);
        } else {
            document.getElementById("product_dis_value").value = '';
        }
    }
</script>

<?php include "../component/footer.php"; ?>
