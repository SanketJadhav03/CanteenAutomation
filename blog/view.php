<?php
include "../config/connection.php";
include "../component/header.php";
include "../component/sidebar.php";

if (isset($_GET['blog_id'])) {
    $blog_id = $_GET['blog_id'];
    $blog_id = mysqli_real_escape_string($conn, $blog_id);

    // Fetch blog details based on blog_id
    $selectQuery = "SELECT * FROM `tbl_blog` WHERE `blog_id` = '$blog_id'";
    $result = mysqli_query($conn, $selectQuery);
    $blog = mysqli_fetch_array($result);
}

if (!$blog) {
    echo "<div class='alert alert-danger text-center font-weight-bold'>Blog not found.</div>";
    exit;
}
?>

<div class="content-wrapper p-4">
    <div class="card shadow-lg rounded-lg">
        <div class="card-header text-center p-4" style="background-color: #f8f9fa;">
            <h3 class="font-weight-bold text-dark"><?= htmlspecialchars($blog['blog_title']) ?></h3>
            <p class="text-muted"><?= date('F j, Y', strtotime($blog['blog_created_at'])) ?></p>
        </div>
        <div class="card-body">
            <div class="text-center mb-4">
                <img src="../uploads/blogs/<?= $blog['blog_image_path'] == null ? 'no_img.png' : $blog['blog_image_path'] ?>" width="350" height="250" alt="Blog Image" class="rounded shadow-sm">
            </div>
            <p><strong>Status:</strong> <span class="badge <?= $blog['blog_status'] == 1 ? 'badge-success' : 'badge-danger' ?>"><?= $blog['blog_status'] == 1 ? 'Active' : 'Inactive' ?></span></p>
            <hr>
            <h5 class="font-weight-bold text-dark">Content:</h5>
            <p><?= nl2br(htmlspecialchars($blog['blog_content'])) ?></p>
        </div>
        <div class="card-footer text-center">
            <a href="index.php" class="btn btn-sm btn-outline-primary rounded-pill shadow">
                <i class="fa fa-arrow-left"></i> Back to Blogs
            </a>
        </div>
    </div>
</div>

<?php
include "../component/footer.php";
?>
