<?php
include "../config/connection.php";
include "../component/header.php";
include "../component/sidebar.php";

if (isset($_GET['customer_id'])) {
    $customer_id = $_GET['customer_id'];
    $customer_id = mysqli_real_escape_string($conn, $customer_id);

    // Fetch customer details from the database
    $query = "SELECT * FROM tbl_customer WHERE customer_id = '$customer_id'";
    $result = mysqli_query($conn, $query);

    // Check if customer exists
    if (mysqli_num_rows($result) > 0) {
        $customer = mysqli_fetch_assoc($result);
    } else {
        echo "<script>alert('Customer not found.'); window.location.href = 'index.php';</script>";
        exit();
    }
} else {
    echo "<script>alert('Invalid request.'); window.location.href = 'index.php';</script>";
    exit();
}
?>

<div class="content-wrapper p-2">
    <div class="card">
        <div class="card-header">
            <div class="text-center p-3">
                <h3 class="font-weight-bold">Customer Identity Card</h3>
            </div>
        </div>

        <div class="card-body">
            <div class="identity-card">
                <div class="text-center">
                    <!-- Profile Image -->
                    <img src="../uploads/customers/<?= $customer['customer_image'] ?: 'no_img.png' ?>" class="customer-img" alt="Customer Image">
                </div>
                <div class="customer-info">
                    <p><strong>Customer Name:</strong> <?= $customer['customer_name'] ?></p>
                    <p><strong>Email:</strong> <?= $customer['customer_email'] ?></p>
                    <p><strong>Phone:</strong> <?= $customer['customer_phone'] ?></p>
                    <p><strong>Address:</strong> <?= $customer['customer_address'] ?></p>
                    <p><strong>Status:</strong> <?= $customer['customer_status'] == 1 ? 'Active' : 'Inactive' ?></p>
                </div>
            </div>

            <div class="text-center mt-3">
                <a href="edit.php?customer_id=<?= $customer['customer_id'] ?>" class="btn btn-info shadow"> <i class="fa fa-pen"></i></a>
                <a href="delete.php?customer_id=<?= $customer['customer_id'] ?>" onclick="return confirm('Are you sure you want to delete this customer?');" class="btn btn-danger shadow"> <i class="fa fa-trash"></i> </a>
                <a href="index.php" class="btn shadow btn-success"> <i class="fa fa-eye"></i>&nbsp;&nbsp;Back to List</a>
            </div>
        </div>
    </div>
</div>

<?php
include "../component/footer.php";
?>

<style>
    .identity-card {
        width: 350px;
        margin: 0 auto;
        padding: 20px;
        border-radius: 15px;
        border: 1px solid #ddd;
        background-color: #f9f9f9;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .customer-img {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        border: 3px solid #ddd;
        object-fit: cover;
        margin-bottom: 20px;
    }

    .customer-info {
        text-align: left;
        font-size: 14px;
    }

    .customer-info p {
        margin: 8px 0;
        font-weight: bold;
    }

    .customer-info strong {
        color: #333;
    }

    .identity-card .btn {
        margin: 5px;
    }
</style>
