<?php
include "../config/connection.php";
include "../component/header.php";
include "../component/sidebar.php";
?>

<div class="content-wrapper p-2">
    <div class="card">
        <div class="card-header">
            <div class="text-center p-3">
                <h3 class="font-weight-bold">Product Management</h3>
            </div>
            <form action="">
                <div class="row justify-content-end">
                    <div class="col-2 font-weight-bold">
                        Product Name
                        <input type="search" name="product_name" value="<?= isset($_GET["product_name"]) ? $_GET["product_name"] : "" ?>" class="form-control font-weight-bold" placeholder="Product Name">
                    </div>
                    <div class="col-1 font-weight-bold">
                        <br>
                        <button type="submit" class="shadow btn w-100 btn-info font-weight-bold"> <i class="fas fa-search"></i> &nbsp;Find</button>
                    </div>
                    <div class="col-2 font-weight-bold">
                        <br>
                        <a href="create.php" class="font-weight-bold w-100 shadow btn btn-success"> <i class="fas fa-plus"></i>&nbsp; Add Product</a>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-body">
            <?php
            if (isset($_SESSION["success"])) {
            ?>
                <div class="font-weight-bold alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5 class="font-weight-bold "><i class="icon fas fa-check"></i> Success!</h5>
                    <?= $_SESSION["success"] ?>
                </div>
            <?php
                unset($_SESSION["success"]);
            }
            ?>

            <div class="table-responsive">
                <table class="table">
                    <tr>
                        <th>#</th>
                        <th>Product Image</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Status</th> 
                        <th>Action</th>
                    </tr>
                    <?php
                    $count = 0;
                    $limit = 10;
                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    $offset = ($page - 1) * $limit;

                    // Count query to get total number of records
                    $countQuery = "SELECT COUNT(*) as total FROM `tbl_product` p INNER JOIN `tbl_category` c ON p.category_id = c.category_id";
                    $selectQuery = "SELECT p.*, c.category_name FROM `tbl_product` p INNER JOIN `tbl_category` c ON p.category_id = c.category_id LIMIT $limit OFFSET $offset";

                    // If search is applied, modify queries accordingly
                    if (isset($_GET["product_name"])) {
                        $product_name = $_GET["product_name"];
                        $product_name = mysqli_real_escape_string($conn, $product_name);
                        $countQuery = "SELECT COUNT(*) as total FROM `tbl_product` p INNER JOIN `tbl_category` c ON p.category_id = c.category_id WHERE p.product_name LIKE '%$product_name%'";
                        $selectQuery = "SELECT p.*, c.category_name FROM `tbl_product` p INNER JOIN `tbl_category` c ON p.category_id = c.category_id WHERE p.product_name LIKE '%$product_name%' LIMIT $limit OFFSET $offset";
                    }

                    // Get total records
                    $countResult = mysqli_query($conn, $countQuery);
                    $totalRecords = mysqli_fetch_assoc($countResult)['total'];
                    $totalPages = ceil($totalRecords / $limit);

                    // Get product data with category name
                    $result = mysqli_query($conn, $selectQuery);
                    while ($data = mysqli_fetch_array($result)) {
                    ?>
                        <tr>
                            <td><?= ++$count ?></td>
                            <td><img src="../uploads/products/<?= $data['product_image'] == null ? "no_img.png" : $data['product_image'] ?>" width="100" height="100" alt="Product Image"></td>

                            <td><?= $data["product_name"] ?></td>
                            <td><?= $data["category_name"] ?></td> <!-- Category Name from the JOIN -->
                            <td><?= $data["product_price"] - $data["product_dis_value"] ?></td>
                            <td><?= $data["product_status"] == 1 ? 'Active' : 'Inactive' ?></td> 
                            <td>
                                <a href="view.php?product_id=<?= $data["product_id"] ?>" class="btn btn-sm shadow btn-success">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <a href="edit.php?product_id=<?= $data["product_id"] ?>" class="btn btn-sm shadow btn-info">
                                    <i class="fa fa-pen"></i>
                                </a>
                                <a href="delete.php?product_id=<?= $data["product_id"] ?>" onclick="if(confirm('Are you sure want to delete this product?')){return true}else{return false;}" class="btn btn-sm shadow btn-danger">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                    <?php
                    if ($count == 0) {
                    ?>
                        <tr>
                            <td colspan="9" class="font-weight-bold text-center">
                                <span class="text-danger">No Product Found.</span>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </table>
            </div>
        </div>

        <div class="card-footer">
            <div class="d-flex justify-content-center">
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a class="btn btn-sm btn-outline-info ml-2" href="?page=<?php echo $page - 1; ?>&product_name=<?php echo isset($product_name) ? $product_name : ''; ?>">Previous</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a class="btn btn-sm <?= $page == $i ? "btn-info" : "btn-outline-info" ?>  ml-2 shadow" href="?page=<?php echo $i; ?>&product_name=<?php echo isset($product_name) ? $product_name : ''; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <a class="btn btn-sm btn-outline-info ml-2" href="?page=<?php echo $page + 1; ?>&product_name=<?php echo isset($product_name) ? $product_name : ''; ?>">Next</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include "../component/footer.php";
?>
