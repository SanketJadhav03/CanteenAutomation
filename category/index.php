<?php
include "../config/connection.php";
include "../component/header.php";
include "../component/sidebar.php";
?>

<div class="content-wrapper p-2">
    <div class="card">
        <div class="card-header">
            <div class="text-center p-3">
                <h3 class="font-weight-bold">Category Management</h3>
            </div>
            <form action="">
                <div class="row justify-content-end">
                    <div class="col-2 font-weight-bold">
                        Category Name
                        <input type="search" name="category_name" value="<?= isset($_GET["category_name"]) ? $_GET["category_name"] : "" ?>" class="form-control font-weight-bold" placeholder="Category Name">
                    </div>
                    <div class="col-1 font-weight-bold">
                        <br>
                        <button type="submit" class="shadow btn w-100 btn-info font-weight-bold"> <i class="fas fa-search"></i> &nbsp;Find</button>
                    </div>
                    <div class="col-2 font-weight-bold">
                        <br>
                        <a href="create.php" class="font-weight-bold w-100 shadow btn btn-success"> <i class="fas fa-plus"></i>&nbsp; Add Category</a>
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
            <?php
            if (isset($_SESSION["error"])) {
            ?>
                <div class="font-weight-bold alert alert-warning alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5 class="font-weight-bold "><i class="fas fa-exclamation-circle"></i>
                    Warning!</h5>
                    <?= $_SESSION["error"] ?>
                </div>
            <?php
                unset($_SESSION["error"]);
            }
            ?>

            <div class="table-responsive">
                <table class="table">
                    <tr>
                        <th>#</th>
                        <th>Category Image</th>
                        <th>Category Name</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    <?php
                    $count = 0;
                    $limit = 10;
                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    $offset = ($page - 1) * $limit;
                    $countQuery = "SELECT COUNT(*) as total FROM `tbl_category`";
                    $selectQuery = "SELECT * FROM `tbl_category` LIMIT $limit OFFSET $offset";
                    if (isset($_GET["category_name"])) {
                        $category_name = $_GET["category_name"];
                        $category_name = mysqli_real_escape_string($conn, $category_name);
                        $countQuery = "SELECT COUNT(*) as total FROM `tbl_category` WHERE `category_name` LIKE '%$category_name%'";
                        $selectQuery = "SELECT * FROM `tbl_category` WHERE `category_name` LIKE '%$category_name%' LIMIT $limit OFFSET $offset";
                    }
                    $countResult = mysqli_query($conn, $countQuery);
                    $totalRecords = mysqli_fetch_assoc($countResult)['total'];
                    $totalPages = ceil($totalRecords / $limit);
                    $result = mysqli_query($conn, $selectQuery);
                    while ($data = mysqli_fetch_array($result)) {
                    ?>
                        <tr>
                            <td><?= $count += 1 ?></td>
                            <td><img src="../uploads/categories/<?= $data['category_image'] == null ? "no_img.png" :$data['category_image'] ?>" width="100" height="100" alt="Category Image"></td>
                            <td><?= $data["category_name"] ?></td>
                            <td><?= $data["category_status"] == 1 ? 'Active' : 'Inactive' ?></td>
                            <td>
                                <a href="edit.php?category_id=<?= $data["category_id"] ?>" class="btn btn-sm shadow btn-info">
                                    <i class="fa fa-pen"></i>
                                </a>
                                <a href="delete.php?category_id=<?= $data["category_id"] ?>" onclick="if(confirm('Are you sure want to delete this category?')){return true}else{return false;}" class="btn btn-sm shadow btn-danger">
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
                            <td colspan="5" class="font-weight-bold text-center">
                                <span class="text-danger">No Category Found.</span>
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
                        <a class="btn btn-sm btn-outline-info ml-2" href="?page=<?php echo $page - 1; ?>&category_name=<?php echo isset($category_name) ? $category_name : ''; ?>">Previous</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a class="btn btn-sm <?= $page == $i ? "btn-info" : "btn-outline-info" ?>  ml-2 shadow" href="?page=<?php echo $i; ?>&category_name=<?php echo isset($category_name) ? $category_name : ''; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <a class="btn btn-sm btn-outline-info ml-2" href="?page=<?php echo $page + 1; ?>&category_name=<?php echo isset($category_name) ? $category_name : ''; ?>">Next</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include "../component/footer.php";
?>
