<?php
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user data
$stmt = $pdo->prepare("SELECT id, username, email, role, profile_pic FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$is_admin = ($user['role'] == 1);

// Handle Profile Picture Upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_pic'])) {
    $upload_dir = "uploads/profile/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $file = $_FILES['profile_pic'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];

    if (in_array($ext, $allowed) && $file['size'] < 5000000) {
        $new_name = "user_" . $user_id . "_" . time() . "." . $ext;
        $target = $upload_dir . $new_name;

        if (move_uploaded_file($file['tmp_name'], $target)) {
            // Delete old picture
            if (!empty($user['profile_pic']) && file_exists($user['profile_pic'])) {
                unlink($user['profile_pic']);
            }

            $stmt = $pdo->prepare("UPDATE users SET profile_pic = ? WHERE id = ?");
            $stmt->execute([$target, $user_id]);

            $success = "Profile picture updated successfully!";
            $user['profile_pic'] = $target;

            // Update session
            $_SESSION['profile_pic'] = $target;
        }
    } else {
        $error = "Invalid image. Only JPG, PNG, WEBP under 5MB allowed.";
    }
}

// Handle Password Change
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $old_pass = $_POST['old_password'];
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    if ($new_pass === $confirm_pass && strlen($new_pass) >= 6) {
        // Verify old password
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $current_hash = $stmt->fetchColumn();

        if ($current_hash && password_verify($old_pass, $current_hash)) {
            $new_hash = password_hash($new_pass, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$new_hash, $user_id]);

            $success = "Password changed successfully!";
        } else {
            $error = "Incorrect current password.";
        }
    } else {
        $error = "New passwords do not match or must be at least 6 characters.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <style>
        .profile-container {
            max-width: 900px;
            margin: 40px auto;
            padding: 30px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .profile-pic {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid #8B4513;
        }
    </style>
</head>

<body>

    <div class="profile-container">
        <h1 class="text-center mb-4">My Profile</h1>

        <?php if (isset($success))
            echo "<div class='alert alert-success'>$success</div>"; ?>
        <?php if (isset($error))
            echo "<div class='alert alert-danger'>$error</div>"; ?>

        <div class="text-center mb-4">
            <?php if (!empty($user['profile_pic'])): ?>
                <img src="<?php echo htmlspecialchars($user['profile_pic']); ?>" alt="Profile" class="profile-pic">
            <?php else: ?>
                <div class="mx-auto"
                    style="width:180px;height:180px;border-radius:50%;background:#ddd;display:flex;align-items:center;justify-content:center;">
                    No Photo
                </div>
            <?php endif; ?>
        </div>

        <h3 class="text-center"><?php echo htmlspecialchars($user['username']); ?></h3>
        <p class="text-center text-muted"><?php echo htmlspecialchars($user['email']); ?></p>
        <p class="text-center"><span class="badge bg-<?php echo $is_admin ? 'danger' : 'success'; ?>">
                <?php echo $is_admin ? 'Administrator' : 'Member'; ?>
            </span></p>

        <!-- Profile Picture -->
        <div class="mt-5">
            <h5>Change Profile Picture</h5>
            <form method="POST" enctype="multipart/form-data">
                <input type="file" name="profile_pic" accept="image/*" class="form-control mb-3" required>
                <button type="submit" class="btn btn-primary">Upload Picture</button>
            </form>
        </div>

        <!-- Password Change -->
        <div class="mt-5">
            <h5>Change Password</h5>
            <form method="POST">
                <input type="hidden" name="change_password" value="1">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label>Current Password</label>
                        <input type="password" name="old_password" class="form-control" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>New Password</label>
                        <input type="password" name="new_password" class="form-control" minlength="6" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control" minlength="6" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-warning">Change Password</button>
            </form>
        </div>

        <?php if (!$is_admin): ?>
            <!-- Extra Services for Normal Users -->
            <div class="mt-5 border-top pt-4">
                <h5>☕ My Cafe Services</h5>
                <div class="row g-4 mt-3">
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6>📜 Order History</h6>
                                <p class="text-muted small">View all your past orders</p>
                                <a href="order-history.php" class="btn btn-outline-primary btn-sm">View History</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6>❤️ Favorites</h6>
                                <p class="text-muted small">Your favorite drinks & meals</p>
                                <a href="favorites.php" class="btn btn-outline-primary btn-sm">Manage Favorites</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6>🎟 Loyalty Points</h6>
                                <p class="text-muted small">You have <strong>245 points</strong></p>
                                <a href="#" class="btn btn-outline-success btn-sm">Redeem Rewards</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6>📅 Book a Table</h6>
                                <p class="text-muted small">Reserve your seat</p>
                                <a href="reservation.php" class="btn btn-outline-primary btn-sm">Make Reservation</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($is_admin): ?>
            <div class="mt-4 text-center">
                <a href="/cafe_website/admin/dashboard.php" class="btn btn-danger">Go to Admin Dashboard</a>
            </div>
        <?php endif; ?>
    </div>
    <?php require_once 'includes/footer.php'; ?>