<?php
include "../config/connection.php";
include "../component/header.php";
include "../component/sidebar.php";

// Fetch ratings and reviews grouped by product
$query = "
    SELECT 
        p.product_id,
        p.product_name,
        r.rating,
        r.review_rating,
        r.created_at,
        c.customer_name
    FROM tbl_ratings r
    JOIN tbl_product p ON r.product_id = p.product_id
    JOIN tbl_customer c ON r.customer_id = c.customer_id
    ORDER BY p.product_name, r.created_at DESC
";
$result = $conn->query($query);

$ratings = [];
while ($row = $result->fetch_assoc()) {
    $ratings[$row['product_id']]['product_name'] = $row['product_name'];
    $ratings[$row['product_id']]['reviews'][] = [
        'customer_name' => $row['customer_name'],
        'rating' => $row['rating'],
        'review' => $row['review_rating'],
        'created_at' => $row['created_at']
    ];
}
?>

<div class="content-wrapper p-3">
    <div class="card shadow border-0">
        <div class="card-header ">
            <h3 class="text-center font-weight-bold py-2 mb-0">Product Ratings & Reviews Report</h3>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <input type="text" id="searchInput" class="form-control" placeholder="Search by Product Name..." onkeyup="filterTable()">
            </div>

            <div class="table-responsive">
                <table class="table table-bordered" id="ratingsTable">
                    <thead class="thead- ">
                        <tr>
                            <th>Product Name</th>
                            <th>Customer Name</th>
                            <th>Rating</th>
                            <th>Review</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ratings as $product_id => $product_data) { ?>
                            <tr class="bg-light product-row">
                                <td class="font-weight-bold" colspan="5"><?= htmlspecialchars($product_data['product_name']) ?></td>
                            </tr>
                            <?php foreach ($product_data['reviews'] as $review) { ?>
                                <tr class="review-row">
                                    <td></td>
                                    <td><?= htmlspecialchars($review['customer_name']) ?></td>
                                    <td>
                                        <?php for ($i = 1; $i <= 5; $i++) {
                                            echo $i <= $review['rating'] ? '⭐' : '☆';
                                        } ?>
                                    </td>
                                    <td><?= htmlspecialchars($review['review']) ?></td>
                                    <td><?= date("d/m/Y h:i A", strtotime($review['created_at'])) ?></td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function filterTable() {
        const searchInput = document.getElementById('searchInput').value.toLowerCase();
        const productRows = document.querySelectorAll('.product-row');
        const reviewRows = document.querySelectorAll('.review-row');

        productRows.forEach((productRow) => {
            const productName = productRow.textContent.toLowerCase();
            const isVisible = productName.includes(searchInput);

            productRow.style.display = isVisible ? '' : 'none';

            // Show/hide associated review rows
            let sibling = productRow.nextElementSibling;
            while (sibling && sibling.classList.contains('review-row')) {
                sibling.style.display = isVisible ? '' : 'none';
                sibling = sibling.nextElementSibling;
            }
        });
    }
</script>

<?php include "../component/footer.php"; ?>
