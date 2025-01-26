<?php
$title = "Admin Dashboard";
include "config/connection.php";
include("component/header.php");
include "component/sidebar.php";

// Fetch counts for info boxes (existing code)

// Fetch product sales data
$product_sales_query = "
  SELECT 
    p.product_name,
    SUM(oi.quantity) AS total_quantity
  FROM tbl_order_items oi
  JOIN tbl_product p ON oi.product_id = p.product_id
  GROUP BY oi.product_id
  ORDER BY total_quantity DESC
  LIMIT 10";
$product_sales_result = mysqli_query($conn, $product_sales_query);

$product_names = [];
$product_quantities = [];

while ($row = mysqli_fetch_assoc($product_sales_result)) {
  $product_names[] = $row['product_name'];
  $product_quantities[] = $row['total_quantity'];
}
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

$reviews_query = "
    SELECT 
        p.product_name, 
        ROUND(AVG(r.rating), 1) AS avg_rating, 
        GROUP_CONCAT(CONCAT('Rating: ', r.rating, ' / 5 | Review: ', r.review_rating, ' | Date: ', DATE_FORMAT(r.created_at, '%d/%m/%Y')) SEPARATOR '<br>') AS reviews
    FROM tbl_ratings r
    JOIN tbl_product p ON r.product_id = p.product_id
    GROUP BY r.product_id
    ORDER BY AVG(r.rating) DESC
    LIMIT 5";
$reviews_result = mysqli_query($conn, $reviews_query);
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-12 text-">
          <h1 class="h1 font-weight-bold">Dashboard</h1>
        </div>
      </div>
    </div>
  </div>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
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
          </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
          <div class="info-box mb-3">
            <a href="<?= $base_url . "orders/" ?>" class="info-box-icon bg-danger elevation-1"><i class="fas fa-shopping-cart"></i></a>
            <div class="info-box-content">
              <span class="info-box-text">Orders</span>
              <span class="info-box-number"><?= $order_count ?></span>
            </div>
          </div>
        </div>
        <div class="clearfix hidden-md-up"></div>
        <div class="col-12 col-sm-6 col-md-3">
          <div class="info-box mb-3">
            <a href="<?= $base_url . "category/" ?>" class="info-box-icon bg-success elevation-1"><i class="fas fa-th-list"></i></a>
            <div class="info-box-content">
              <span class="info-box-text">Available Categories</span>
              <span class="info-box-number"><?= $category_count ?></span>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-6">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Top 10 Sold Products</h3>
            </div>
            <div class="card-body">
              <canvas id="productSalesChart" width="400" height="200"></canvas>
            </div>
          </div>
        </div>

        <div class="col-6">
          <div class="card shadow-lg">
            <div class="card-header ">
              <h5 class="font-weight-bold"><i class="fas fa-star"></i> Latest Product Ratings and Reviews</h3>
            </div>
            <div class="card-body">
              <?php if (mysqli_num_rows($reviews_result) > 0) : ?>
                <ul class="list-group">
                  <?php while ($review = mysqli_fetch_assoc($reviews_result)) : ?>
                    <li class="list-group-item">
                      <h5 class="text-info">
                        <i class="fas fa-box"></i> <?= htmlspecialchars($review['product_name']) ?>
                      </h5>
                      <p><strong>Average Rating:</strong> <span class="text-warning"><i class="fas fa-star"></i> <?= htmlspecialchars($review['avg_rating']) ?> / 5</span></p>
                      <hr>
                      <p><strong>Customer Reviews:</strong></p>
                      <div style="padding-left: 10px; color: #333;">
                        <?= $review['reviews'] ?>
                      </div>
                    </li>
                  <?php endwhile; ?>
                </ul>
              <?php else : ?>
                <p class="text-center">No ratings or reviews available.</p>
              <?php endif; ?>
            </div>
          </div>
        </div>

      </div>

    </div>
  </section>
</div>

<?php include "component/footer.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const productNames = <?= json_encode($product_names) ?>;
  const productQuantities = <?= json_encode($product_quantities) ?>;

  const ctx = document.getElementById('productSalesChart').getContext('2d');
  const productSalesChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: productNames,
      datasets: [{
        label: 'Quantity Sold',
        data: productQuantities,
        backgroundColor: 'rgba(54, 162, 235, 0.5)',
        borderColor: 'rgba(54, 162, 235, 1)',
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
</script>