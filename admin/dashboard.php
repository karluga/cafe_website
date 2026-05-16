<?php
$page_title = "Dashboard - Cafe Bastions";
require_once '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

require_once '../classes/Service.php';
$serviceObj = new Service();

$success = $error = '';

// Check if user just registered
$just_registered = isset($_GET['registered']) && $_GET['registered'] == '1';

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    if ($serviceObj->delete($id)) {
        $success = "Service deleted successfully!";
    } else {
        $error = "Failed to delete service.";
    }
}

$services = $serviceObj->readAll();
?>

<div class="container my-5">

    <!-- Custom Message: Success for new registration OR Welcome back -->
    <?php if ($just_registered): ?>
        <div class="alert alert-success text-center">
            <strong>Account created successfully!</strong><br>
            Welcome to Cafe Bastions Admin Panel, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>!
        </div>
    <?php elseif (isset($_COOKIE['username'])): ?>
        <?php
        $last_visit = isset($_COOKIE['last_visit']) ? date("d M Y H:i", $_COOKIE['last_visit']) : "first time";
        ?>
        <div class="alert alert-info text-center">
            Welcome back, <strong><?= htmlspecialchars($_COOKIE['username']) ?></strong>!
            Last visit: <?= $last_visit ?>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Admin Dashboard</h2>
        <div>
            <a href="add_service.php" class="btn btn-success me-2">+ Add New Service</a>
            <a href="reservations.php" class="btn btn-info me-2">📅 Reservations</a>
            <a href="orders.php" class="btn btn-warning">📦 Orders</a>
        </div>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($services as $s): ?>
                    <tr>
                        <td><?= $s['id'] ?></td>
                        <td>
                            <img src="/cafe_website/<?= htmlspecialchars($s['image']) ?>" width="80" height="60"
                                style="object-fit:cover;">
                        </td>
                        <td><?= htmlspecialchars($s['title']) ?></td>
                        <td><?= htmlspecialchars(substr($s['description'], 0, 100)) ?>...</td>
                        <td>
                            <a href="edit_service.php?id=<?= $s['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="dashboard.php?delete=<?= $s['id'] ?>" class="btn btn-sm btn-danger"
                                onclick="return confirm('Delete this service?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>