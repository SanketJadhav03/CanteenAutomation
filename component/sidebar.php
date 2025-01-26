<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 pl-3">
            <a href="<?= $base_url ?>" class="d-block">
                Welcome, Admin
            </a>
        </div>
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="<?= $base_url ?>index.php" class="nav-link">
                        <i class="nav-icon fas fa-chart-pie"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <!-- Customer Management -->
                <li class="nav-item">
                    <a href="<?= $base_url ?>customer/index.php" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Customers</p>
                    </a>
                </li>
                <!-- Category Management -->
                <li class="nav-item">
                    <a href="<?= $base_url ?>category/index.php" class="nav-link">
                        <i class="nav-icon fas fa-list-alt"></i>
                        <p>Category</p>
                    </a>
                </li>
                <!-- Product Management -->
                <li class="nav-item">
                    <a href="<?= $base_url ?>product/index.php" class="nav-link">
                        <i class="nav-icon fas fa-box-open"></i>
                        <p>Product</p>
                    </a>
                </li>
                <!-- Order Management -->
                <li class="nav-item">
                    <a href="<?= $base_url ?>order/index.php" class="nav-link">
                        <i class="nav-icon fas fa-truck"></i>
                        <p>Orders</p>
                    </a>
                </li>
                <!-- <li class="nav-item">
                    <a href="<?= $base_url ?>offer/index.php" class="nav-link">
                        <i class="nav-icon fas fa-gift"></i>
                        <p>Offers</p>
                    </a>
                </li>  -->
                <li class="nav-item">
                    <a href="<?= $base_url ?>#" class="nav-link">
                        <i class="fas fa-chart-bar nav-icon"></i>
                        <p>
                            Reports
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= $base_url ?>reports/product_ratings_report.php" class="nav-link">
                                <i class="fas fa-long-arrow-alt-right nav-icon"></i>
                                <p>Customer Ratings</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= $base_url ?>reports/sales_report.php" class="nav-link">
                                <i class="fas fa-long-arrow-alt-right nav-icon"></i>
                                <p>Sales Report</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= $base_url ?>reports/most_sold_products.php" class="nav-link">
                                <i class="fas fa-long-arrow-alt-right nav-icon"></i>
                                <p>Most Sold Products</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= $base_url ?>reports/customer_frequency.php" class="nav-link">
                                <i class="fas fa-long-arrow-alt-right nav-icon"></i>
                                <p>Customer Frequency</p>
                            </a>
                        </li> 
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="<?= $base_url ?>#" class="nav-link">
                        <i class="fas fa-tools nav-icon"></i>
                        <p>
                            Setting's
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= $base_url ?>slider/index.php" class="nav-link">
                                <i class="fas fa-long-arrow-alt-right nav-icon"></i>
                                <p>Slider</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= $base_url ?>blog/index.php" class="nav-link">
                                <i class="fas fa-long-arrow-alt-right nav-icon"></i>
                                <p>Blog's</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= $base_url ?>contact/index.php" class="nav-link">
                                <i class="fas fa-long-arrow-alt-right nav-icon"></i>
                                <p>Contact Us</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <!-- Logout -->
                <li class="nav-item">
                    <a href="<?= $base_url ?>logout.php" class="nav-link">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Logout</p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>