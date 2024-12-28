<?php
include "config/connection.php";
include "component/header.php";
include "component/sidebar.php";

$adminId = $_SESSION["admin_id"];
$message = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $mobile_number = $_POST["mobile_number"];
    $password = $_POST["password"];
    $updated_at = date('Y-m-d H:i:s'); // Current time in UTC

    // Update query based on whether a new password is provided
    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $updateQuery = "UPDATE admins SET username = '$username', email = '$email', mobile_number = '$mobile_number', password = '$hashedPassword', updated_at = '$updated_at' WHERE id = $adminId";
    } else {
        $updateQuery = "UPDATE admins SET username = '$username', email = '$email', mobile_number = '$mobile_number', updated_at = '$updated_at' WHERE id = $adminId";
    }

    // Execute the update query and set the message
    if (mysqli_query($conn, $updateQuery)) {
        $message = "Profile updated successfully!";
    } else {
        $message = "Error updating profile: " . mysqli_error($conn);
    }
}

// Fetch current admin data to display in the form
$query = "SELECT * FROM admins WHERE id = $adminId";
$result = mysqli_query($conn, $query);
$admin = mysqli_fetch_assoc($result);
?>

<div class="content-wrapper">
    <div class="container pt-4">
        <div class="card shadow-lg">
            <div class="card-header bg-light  text-white text-center">
                <h3 class="font-weight-bold">Update Profile</h3>
            </div>
            <div class="card-body">
                <?php if (!empty($message)): ?>
                    <div class="alert alert-info"><?php echo $message; ?></div>
                <?php endif; ?>
                <form action="" method="POST" class="row g-3">
                    <div class="col-md-6">
                        <label for="username" class="form-label">Username:</label>
                        <input class="form-control" type="text" name="username" value="<?php echo htmlspecialchars($admin['username']); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email:</label>
                        <input class="form-control" type="email" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" required>
                    </div>
                    <div class="col-md-6 mt-3">
                        <label for="mobile_number" class="form-label">Mobile Number:</label>
                        <input class="form-control" type="text" name="mobile_number" value="<?php echo htmlspecialchars($admin['mobile_number']); ?>" required>
                    </div>
                    <div class="col-md-6 mt-3">
                        <label for="password" class="form-label">Password:</label>
                        <input class="form-control" type="password" name="password" placeholder="Enter new password (optional)">
                    </div>
                    <div class="col-md-6 mt-3">
                        <label for="created_at" class="form-label">Created At:</label>
                        <?php
                        $utcDate = $admin['created_at'];
                        $date = new DateTime($utcDate, new DateTimeZone('UTC'));
                        $date->setTimezone(new DateTimeZone('Asia/Kolkata'));
                        ?>
                        <input class="form-control" type="text" name="created_at" value="<?php echo $date->format('d/m/Y h:i:s A'); ?>" readonly>
                    </div>
                    <div class="col-md-6 mt-3">
                        <label for="updated_at" class="form-label">Last Updated At:</label>
                        <?php
                        if (!empty($admin['updated_at'])) {
                            // Assuming 'updated_at' is stored in UTC, convert it to IST (Asia/Kolkata)
                            $updatedUtcDate = $admin['updated_at'];
                            $updatedDate = new DateTime($updatedUtcDate, new DateTimeZone('UTC')); // Original date in UTC
                            $updatedDate->setTimezone(new DateTimeZone('Asia/Kolkata')); // Convert to IST
                            // Format the date in 'd/m/Y h:i:s A' (AM/PM format)
                            $formattedUpdatedAt = $updatedDate->format('d/m/Y h:i:s A');
                        } else {
                            $formattedUpdatedAt = "N/A";
                        }
                        ?>
                        <input class="form-control" type="text" name="updated_at" value="<?php echo $formattedUpdatedAt; ?>" readonly>
                    </div>


                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-success mt-4">Update Profile</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
include "component/footer.php";
?>