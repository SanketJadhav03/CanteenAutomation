<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>
    <?php
    echo isset($title) ? $title : "Admin Dashboard";
    ?>
  </title>
  <?php
  $base_url = 'http://' . $_SERVER['HTTP_HOST'] . '/CanteenAutomation/';
  if (!isset($_SESSION["user_role"])) {
    $temp = $base_url . "authentication/";
    header("Location: $temp");
  }
  ?>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="<?php echo $base_url; ?>assets/plugins/fontawesome-free/css/all.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="<?php echo $base_url; ?>assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo $base_url; ?>assets/dist/css/adminlte.min.css">
</head>

<body class="hold-transition sidebar-mini">
  <div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
      <!-- Left navbar links -->
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
          <a href="' . $base_url . $home_url . '" class="nav-link">Home</a>
        </li>
      </ul>

      <!-- Live time in the center -->
      <ul class="navbar-nav mx-auto">
        <li class="nav-item">
          <span id="live-time" class=" font-weight-bold"></span>
        </li>
      </ul>

      <!-- Right navbar links -->
      <ul class="navbar-nav ml-auto">

        <!-- Messages Dropdown Menu -->
        <li class="nav-item dropdown">
          <a class="nav-link" data-toggle="dropdown" href="#">
            <i class="far fa-bell"></i>
            <span id="notificationCount" class="badge badge-danger navbar-badge">0</span>
          </a>
          <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
            <div id="notificationList">
              <p class="dropdown-item text-center">No new notifications</p>
            </div>
            <div class="dropdown-divider"></div>
            <a href="all_notifications.php" class="dropdown-item dropdown-footer">See All Notifications</a>
          </div>
        </li>

        <li class="nav-item ">
          <a class="btn bg-danger" href="<?= $base_url . "logout.php" ?>">
            <i class="fas fa-sign-out-alt"></i>
          </a>
        </li>
      </ul>
    </nav>



    <!-- JavaScript for live time -->
    <script>
      function updateTime() {
        var today = new Date();
        var hours = today.getHours();
        var minutes = today.getMinutes();
        var seconds = today.getSeconds();
        var ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12; // The hour '0' should be '12'
        minutes = minutes < 10 ? '0' + minutes : minutes;
        seconds = seconds < 10 ? '0' + seconds : seconds;
        var timeString = hours + ':' + minutes + ':' + seconds + ' ' + ampm;
        document.getElementById('live-time').innerText = timeString;
      }

      setInterval(updateTime, 1000); // Update time every second
      updateTime(); // Initialize the time immediately
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
      $(document).ready(function() {
        function loadNotifications() {
          $.ajax({
            url: "http://localhost/CanteenAutomation/api/notifications.php",
            method: "GET",
            dataType: "json",
            success: function(data) {
              let notificationList = $("#notificationList");
              let notificationCount = $("#notificationCount");

              notificationList.empty(); // Clear existing notifications

              if (data.length > 0) {
                notificationCount.text(data.length); // Update badge count

                data.forEach(notification => {
                  let notificationItem = `
                            <a href="#" class="dropdown-item">
                                <div class="media">
                                    <img src="http://localhost/CanteenAutomation/uploads/customers/${notification.customer_image?notification.customer_image:"no_img.png"}"  alt="User Avatar" class="img-size-50 mr-3 img-circle">
                                    <div class="media-body">
                                        <h3 class="dropdown-item-title">
                                            ${notification.customer_name}
                                            <span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span>
                                        </h3>
                                        <p class="text-sm">${notification.notification_message}</p>
                                        <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> ${timeAgo(notification.notification_date)}</p>
                                    </div>
                                </div>
                            </a>
                            <div class="dropdown-divider"></div>
                        `;
                  notificationList.append(notificationItem);
                });
              } else {
                notificationList.html('<p class="dropdown-item text-center">No new notifications</p>');
                notificationCount.text("0"); // Reset badge
              }
            }
          });
        }

        function timeAgo(datetime) {
          let time = new Date(datetime);
          let now = new Date();
          let diff = Math.floor((now - time) / 1000); // Difference in seconds

          if (diff < 60) return `${diff} seconds ago`;
          if (diff < 3600) return `${Math.floor(diff / 60)} minutes ago`;
          if (diff < 86400) return `${Math.floor(diff / 3600)} hours ago`;
          return `${Math.floor(diff / 86400)} days ago`;
        }

        loadNotifications();
        setInterval(loadNotifications, 60000); // Refresh every 60 seconds
      });
    </script>