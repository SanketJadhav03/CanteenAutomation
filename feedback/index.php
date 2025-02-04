<?php
$title = "Admin Dashboard";
include "../config/connection.php";
include("../component/header.php");
include "../component/sidebar.php";

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

// Fetch reviews with product ratings
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
    ORDER BY avg_rating DESC ";
$reviews_result = mysqli_query($conn, $reviews_query);
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
  <div class="container-fluid pt-3">
    
    

    <!-- Customer Feedback Section -->
    <div class="row  ">
      <div class="col-lg-12 mx-auto">
        <div class="  border-0 rounded-3 p-4">
          <h4 class="font-weight-bold text-primary"><i class="fas fa-comments"></i> Customer Reviews</h4>
          <hr>

          <?php if (mysqli_num_rows($reviews_result) > 0) : ?>
            <ul class="list-group">
              <?php while ($review = mysqli_fetch_assoc($reviews_result)) : ?>
                <li class="list-group-item border-0 shadow-sm p-4 mb-3 rounded">
                  <div class="d-flex justify-content-between align-items-center">
                    <h5 class="text-dark mb-1">
                      <i class="fas fa-box mr-1"></i> <?= htmlspecialchars($review['product_name']) ?>
                    </h5>
                    <span class="badge bg-warning text-dark">
                      <i class="fas fa-star"></i> <?= htmlspecialchars($review['avg_rating']) ?> / 5
                    </span>
                  </div>
                  <hr class="my-2">
                  
                  <p class="mb-2"><strong>Reviews:</strong></p>
                  <div style="padding-left: 10px; line-height: 1.6;">
                    <?php
                    $reviews = explode('<br>', $review['reviews']);
                    foreach ($reviews as $single_review) :
                      list($customer_name, $rating_info, $review_text) = explode(' | ', $single_review, 3);
                      preg_match('/Rating: (\d+)/', $rating_info, $rating_matches);
                      $rating_value = isset($rating_matches[1]) ? $rating_matches[1] : 0;
                    ?>
                      <div class="review-item mb-3 p-3 rounded bg-light">
                        <div class="d-flex align-items-center mb-1">
                          <i class="fas fa-user-circle mr-2 text-primary"></i> 
                          <span class="font-weight-bold"><?= htmlspecialchars($customer_name) ?></span>
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
</div>

<?php include "../component/footer.php"; ?>

<!-- ChartJS for Product Sales -->
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
        backgroundColor: 'rgba(54, 162, 235, 0.7)',
        borderColor: 'rgba(54, 162, 235, 1)',
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: { mode: 'index', intersect: false }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: { color: "#333", font: { weight: "bold" } }
        },
        x: {
          ticks: { color: "#333", font: { weight: "bold" } }
        }
      }
    }
  });
</script>
