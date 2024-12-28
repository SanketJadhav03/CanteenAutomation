<?php
$title = "Admin Dashboard";
include "config/connection.php";
include("component/header.php");
include "component/sidebar.php";

// Fetch the counts from the database
// Count Customers
$customer_query = "SELECT COUNT(*) AS customer_count FROM tbl_customer";
$customer_result = mysqli_query($conn, $customer_query);
$customer_count = mysqli_fetch_assoc($customer_result)['customer_count'];

// Count Products
$product_query = "SELECT COUNT(*) AS product_count FROM tbl_product";
$product_result = mysqli_query($conn, $product_query);
$product_count = mysqli_fetch_assoc($product_result)['product_count'];

// Count Orders
$order_query = "SELECT COUNT(*) AS order_count FROM tbl_orders";  
$order_result = mysqli_query($conn, $order_query);
$order_count = mysqli_fetch_assoc($order_result)['order_count'];

// Count Categories
$category_query = "SELECT COUNT(*) AS category_count FROM tbl_category";  
$category_result = mysqli_query($conn, $category_query);
$category_count = mysqli_fetch_assoc($category_result)['category_count'];
 
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-12 text-center">
          <h1 class="h1">Admin Dashboard </h1>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content-header -->

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <!-- Info boxes -->
      <div class="row">
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <a href="<?= $base_url . "customer/" ?>" class="info-box-icon bg-warning elevation-1"><i class="fas fa-user-friends"></i></a>
            <div class="info-box-content">
                <span class="info-box-text">Customers</span>
                <span class="info-box-number"><?= $customer_count ?></span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box">
            <a href="<?= $base_url . "product/" ?>" class="info-box-icon bg-info elevation-1"><i class="fas fa-box-open"></i></a>
            <div class="info-box-content">
                <span class="info-box-text">Products</span>
                <span class="info-box-number"><?= $product_count ?></span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <a href="<?= $base_url . "orders/" ?>" class="info-box-icon bg-danger elevation-1"><i class="fas fa-shopping-cart"></i></a>
            <div class="info-box-content">
                <span class="info-box-text">Orders</span>
                <span class="info-box-number"><?= $order_count ?></span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->

    <!-- fix for small devices only -->
    <div class="clearfix hidden-md-up"></div>

    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <a href="<?= $base_url . "category/" ?>" class="info-box-icon bg-success elevation-1"><i class="fas fa-th-list"></i></a>
            <div class="info-box-content">
                <span class="info-box-text">Available Categories</span>
                <span class="info-box-number"><?= $category_count ?></span>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->
</div>

      <!-- /.row -->

    </div><!--/. container-fluid -->
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php
include "component/footer.php";
?>
