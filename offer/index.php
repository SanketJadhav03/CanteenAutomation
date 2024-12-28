<?php 

include "../config/connection.php";
include "../component/header.php";
include "../component/sidebar.php";



// Fetch the offers from the database
$offerQuery = "SELECT o.offer_id, o.offer_category, o.offer_description, o.offer_dis, o.offer_image, o.offer_status, c.category_name 
               FROM tbl_offer o
               LEFT JOIN tbl_category c ON o.offer_category = c.category_id";
$offerResult = mysqli_query($conn, $offerQuery);

// Get the total number of offers
$totalOffersQuery = "SELECT COUNT(*) AS total_offers FROM tbl_offer";
$totalOffersResult = mysqli_query($conn, $totalOffersQuery);
$totalOffers = mysqli_fetch_assoc($totalOffersResult)['total_offers'];

?>

<div class="content-wrapper p-2">
    <div class="card">
        <div class="card-header">
            <div class="d-flex p-2 justify-content-between">
                <div class="h5 font-weight-bold">Offer List</div>
                <a href="create.php" class="btn btn-info shadow font-weight-bold">
                    <i class="fa fa-plus"></i>&nbsp; Create Offer
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Display error or success messages -->
            <?php
            if (isset($_SESSION["success"])) {
            ?>
                <div class="font-weight-bold alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5 class="font-weight-bold"><i class="icon fas fa-check"></i> Success!</h5>
                    <?= $_SESSION["success"] ?>
                </div>
            <?php
                unset($_SESSION["success"]);
            }
            ?>

            <!-- Display offer count -->
            <p><strong>Total Offers:</strong> <?php echo $totalOffers; ?></p>

            <!-- Offer Table -->
            <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Image</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Discount (%)</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $count = 0;
                    if (mysqli_num_rows($offerResult) > 0) {
                        while ($offer = mysqli_fetch_assoc($offerResult)) {
                            $count = $count + 1;
                            // Set status label based on the offer status
                            $statusLabel = ($offer['offer_status'] == 1) ? 'Active' : 'Inactive';
                            $statusClass = ($offer['offer_status'] == 1) ? 'badge-success' : 'badge-danger';
                            $offerImage = !empty($offer['offer_image']) ? "../uploads/offers/".$offer['offer_image'] : "../uploads/offers/no_img.png";
                            echo "<tr>
                            <td>{$count}</td>
                            <td><img src='$offerImage' alt='Offer Image' width='100'></td>
                                    <td>{$offer['category_name']}</td>
                                    <td>{$offer['offer_description']}</td>
                                    <td>{$offer['offer_dis']}%</td>
                                    <td><span class='badge $statusClass'>$statusLabel</span></td>
                                    <td>
                                        <a href='edit.php?offer_id={$offer['offer_id']}' class='btn btn-info shadow btn-sm'>
                                            <i class='fa fa-pen'></i>  
                                        </a>
                                        <a href='delete.php?offer_id={$offer['offer_id']}' class='btn btn-danger shadow btn-sm' onclick='return confirm(\"Are you sure you want to delete this offer?\");'>
                                            <i class='fa fa-trash'></i>  
                                        </a>
                                    </td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7' class='text-center'>No offers found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>

<?php include "../component/footer.php"; ?>
