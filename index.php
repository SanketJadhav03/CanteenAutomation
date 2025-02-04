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
        GROUP_CONCAT(
            CONCAT(
                c.customer_name, 
                ' | Rating: ', r.rating, 
                ' / 5 | Review: ', r.review_rating, 
                ' | Date: ', DATE_FORMAT(r.created_at, '%d/%m/%Y')
            ) SEPARATOR '<br>') AS reviews
    FROM tbl_ratings r
    JOIN tbl_product p ON r.product_id = p.product_id
    JOIN tbl_customer c ON r.customer_id = c.customer_id
    GROUP BY r.product_id
    ORDER BY avg_rating DESC
    LIMIT 1";
$reviews_result = mysqli_query($conn, $reviews_query);

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-12 card-header">
          <h1 class="font-weight-bold">Canteen Automation Dashboard</h1>
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
          <div class="card  -lg border-0 rounded-3">
            <div class="card-header rounded-top">
              <h5 class="font-weight-bold mb-0">
                <i class="fas fa-star text-warning"></i> Latest Product Ratings and Reviews
              </h5>
            </div>
            <div class="card-body" style="background-color: #f9f9f9;">
              <?php if (mysqli_num_rows($reviews_result) > 0) : ?>
                <ul class="list-group list-group-flush">
                  <?php while ($review = mysqli_fetch_assoc($reviews_result)) : ?>
                    <li class="list-group-item border-0 shadow-sm p-4 rounded" style="background-color: #ffffff;">
                      <div class="d-flex justify-content-between">
                        <h5 class="text-info mb-1">
                          <i class="fas fa-box  mr-1"></i> <?= htmlspecialchars($review['product_name']) ?>
                        </h5>
                        <span class="text-warning  ">
                          <i class="fas fa-star  "></i> <?= htmlspecialchars($review['avg_rating']) ?> / 5
                        </span>
                      </div>
                      <hr class="my-2">
                      <p class="mb-2"><strong> Reviews:</strong></p>
                      <div style="padding-left: 10px; color: #555; line-height: 1.6;">
                        <?php
                        $reviews = explode('<br>', $review['reviews']);
                        foreach ($reviews as $single_review) :
                          list($customer_name, $rating_info, $review_text) = explode(' | ', $single_review, 3);
                          preg_match('/Rating: (\d+)/', $rating_info, $rating_matches);
                          $rating_value = isset($rating_matches[1]) ? $rating_matches[1] : 0;
                        ?>
                          <div class="review-item mb-3">
                            <div class="d-flex align-items-center mb-1">
                              <i class="fas fa-user-circle mr-2"></i><span class="font-weight-bold"><?= htmlspecialchars($customer_name) ?></span>
                            </div>
                            <div>
                              <span class="text-warning">
                                <?php
                                for ($i = 0; $i < $rating_value; $i++) {
                                  echo '<i class="fas fa-star"></i>';
                                }
                                for ($i = $rating_value; $i < 5; $i++) {
                                  echo '<i class="far fa-star"></i>';
                                }
                                ?>
                              </span>
                            </div>
                            <p class="mb-1"><?= htmlspecialchars($review_text) ?></p>
                          </div>
                        <?php endforeach; ?>
                      </div>
                    </li>
                  <?php endwhile; ?>
                </ul>
              <?php else : ?>
                <div class="text-center py-3">
                  <i class="fas fa-info-circle fa-2x text-muted"></i>
                  <p class="mt-2">No ratings or reviews available.</p>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>

      </div>
      <div>
        
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