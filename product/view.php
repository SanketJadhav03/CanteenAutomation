<?php
include "../config/connection.php";
include "../component/header.php";
include "../component/sidebar.php";

// Get the product ID from the URL
$product_id = $_GET['product_id'];

// Fetch product details
$productQuery = "SELECT p.*, c.category_name FROM tbl_product p
                 LEFT JOIN tbl_category c ON p.category_id = c.category_id
                 WHERE p.product_id = $product_id";
$productResult = mysqli_query($conn, $productQuery);
$product = mysqli_fetch_assoc($productResult);

if (!$product) {
    $_SESSION["error"] = "Product not found!";
    echo "<script>window.location = 'index.php';</script>";
    exit();
}
?>

<div class="content-wrapper p-3">
    <div class="card shadow-lg">
        <div class="card-header bg-light text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="font-weight-bold mb-0">Product Details</h5>
                <a href="index.php" class="btn btn-light font-weight-bold">
                    <i class="fa fa-arrow-left"></i>&nbsp; Back to Products
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Product Image and Details -->
                <div class="col-lg-4 text-center">
                    <?php if ($product['product_image']) { ?>
                        <img src="../uploads/products/<?= htmlspecialchars($product['product_image']) ?>" class="img-fluid rounded mb-3" alt="Product Image">
                    <?php } else { ?>
                        <img src="../uploads/products/no_img.png" class="img-fluid rounded mb-3" alt="No Image Available">
                    <?php } ?>
                  </div>

                <!-- Product Information -->
                <div class="col-lg-8">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <td class="font-weight-bold">Product Name:</td>
                                    <td><?= htmlspecialchars($product['product_name']) ?></td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Category:</td>
                                    <td><?= htmlspecialchars($product['category_name']) ?></td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Stock:</td>
                                    <td><?= htmlspecialchars($product['product_stock']) ?> units</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Price:</td>
                                    <td>₹ <?= htmlspecialchars($product['product_price']) ?></td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Discount:</td>
                                    <td><?= htmlspecialchars($product['product_dis']) ?>  %  </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Final Price:</td>
                                    <td>₹ <?= htmlspecialchars($product['product_price']-$product['product_dis_value']) ?>  </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Description:</td>
                                    <td><?= nl2br(htmlspecialchars($product['product_description'])) ?></td>
                                </tr>
                                <tr> 
                                    <td class="font-weight-bold">Status:</td>
                                    <td>
                                        <span class="badge <?= $product['product_status'] ? 'badge-success' : 'badge-danger' ?>">
                                            <?= $product['product_status'] ? 'Active' : 'Inactive' ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Created At:</td>
                                    <td><?= date("d/m/Y h:i A", strtotime($product['created_at'])) ?></td>
                                    </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer text-right">
            <a href="edit.php?product_id=<?= $product['product_id'] ?>" class="btn btn-outline-primary font-weight-bold">
                <i class="fa fa-edit"></i>&nbsp; Edit Product
            </a>
        </div>
    </div>
</div>

<?php include "../component/footer.php"; ?>