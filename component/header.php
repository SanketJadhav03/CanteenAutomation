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