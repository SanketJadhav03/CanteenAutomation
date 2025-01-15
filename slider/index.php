<?php 

include "../config/connection.php";
include "../component/header.php";
include "../component/sidebar.php";

// Fetch active sliders for the carousel
$sliderQuery = "SELECT slider_id, slider_image, slider_status, created_at 
                FROM tbl_slider  ";
$sliderResult = mysqli_query($conn, $sliderQuery);
$sliderQueryPanel = "SELECT slider_id, slider_image, slider_status, created_at 
                FROM tbl_slider  WHERE slider_status = 1";
$sldierPanel = mysqli_query($conn, $sliderQueryPanel);


?>

<div class="content-wrapper p-2">
    <!-- Slider Section (Carousel) -->
    <div id="homeSlider" class="carousel slide mb-4" data-ride="carousel">
        <div class="carousel-inner">
            <?php
            $isActive = true; // To mark the first slide as active
            if (mysqli_num_rows($sldierPanel) > 0) {
                mysqli_data_seek($sldierPanel, 0); // Reset the result pointer for table display
                while ($slider = mysqli_fetch_assoc($sldierPanel)) {
                    $sliderImage = "../uploads/sliders/" . $slider['slider_image'];
                    $activeClass = $isActive ? 'active' : ''; 
                    echo " 
                    <div class='carousel-item $activeClass'>
                        <img src='$sliderImage' class='d-block w-100' alt='Slider Image' style='max-height: 400px; object-fit: cover;'>
                    </div>"; 
                    $isActive = false;
                }
            } else {
                echo "
                <div class='carousel-item active'>
                    <img src='../uploads/sliders/no_img.png' class='d-block w-100' alt='No Sliders Available' style='max-height: 400px; object-fit: cover;'>
                    <div class='carousel-caption d-none d-md-block'>
                        <h5>No Active Sliders</h5>
                        <p>Please add sliders to display here.</p>
                    </div>
                </div>";
            }
            ?>
        </div>
        <a class="carousel-control-prev" href="#homeSlider" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#homeSlider" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>

    <!-- Slider Table Section -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <h5 class="font-weight-bold">Slider List</h5>
                <a href="create.php" class="btn btn-info shadow font-weight-bold">
                    <i class="fa fa-plus"></i>&nbsp; Add Slider
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Slider Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Image</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Reset the slider result pointer and fetch again for table display
                        mysqli_data_seek($sliderResult, 0); 
                        $count = 0;
                        while ($slider = mysqli_fetch_assoc($sliderResult)) {
                            $count++;
                            $sliderImage = "../uploads/sliders/" . $slider['slider_image'];
                            $statusLabel = ($slider['slider_status'] == 1) ? 'Active' : 'Inactive';
                            $statusClass = ($slider['slider_status'] == 1) ? 'badge-success' : 'badge-danger';
                            echo "
                            <tr>
                                <td>{$count}</td>
                                <td><img src='$sliderImage' alt='Slider Image' width='100'></td>
                                <td><span class='badge $statusClass'>$statusLabel</span></td>
                                <td>{$slider['created_at']}</td>
                                <td>
                                    <a href='edit.php?slider_id={$slider['slider_id']}' class='btn btn-info shadow btn-sm'>
                                        <i class='fa fa-pen'></i>  
                                    </a>
                                    <a href='delete.php?slider_id={$slider['slider_id']}' class='btn btn-danger shadow btn-sm' onclick='return confirm(\"Are you sure you want to delete this slider?\");'>
                                        <i class='fa fa-trash'></i>  
                                    </a>
                                </td>
                            </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include "../component/footer.php"; ?>
