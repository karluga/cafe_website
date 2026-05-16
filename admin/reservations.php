<?php
$page_title = "Manage Reservations - Cafe Bastions";
require_once '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: ../login.php");
    exit;
}

require_once '../includes/db.php';

$success = $error = '';

// Update reservation status
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $action = $_GET['action'];

    $new_status = ($action == 'confirm') ? 'confirmed' : 'cancelled';

    $stmt = $pdo->prepare("UPDATE reservations SET status = ? WHERE id = ?");
    if ($stmt->execute([$new_status, $id])) {
        $success = "Reservation #$id has been $new_status.";
    } else {
        $error = "Failed to update reservation.";
    }
}

// Fetch all reservations
$stmt = $pdo->prepare("
    SELECT r.*, u.username 
    FROM reservations r
    JOIN users u ON r.user_id = u.id
    ORDER BY r.reservation_date DESC, r.reservation_time DESC
");
$stmt->execute();
$reservations = $stmt->fetchAll();
?>

<div class="container my-5">
    <h2>📅 Manage Reservations</h2>

    <?php if ($success): ?>
        <div class="alert alert-success">
            <?= $success ?>
        </div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger">
            <?= $error ?>
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Guests</th>
                    <th>Table</th>
                    <th>Special Request</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($reservations)): ?>
                    <tr>
                        <td colspan="9" class="text-center">No reservations yet.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($reservations as $res): ?>
                        <tr>
                            <td>
                                <?= $res['id'] ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($res['username']) ?>
                            </td>
                            <td>
                                <?= date('d M Y', strtotime($res['reservation_date'])) ?>
                            </td>
                            <td>
                                <?= date('h:i A', strtotime($res['reservation_time'])) ?>
                            </td>
                            <td>
                                <?= $res['guests'] ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($res['table_number'] ?? '-') ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($res['special_request'] ?? '-') ?>
                            </td>
                            <td>
                                <span
                                    class="badge bg-<?= $res['status'] == 'confirmed' ? 'success' : ($res['status'] == 'cancelled' ? 'danger' : 'warning') ?>">
                                    <?= ucfirst($res['status']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($res['status'] == 'pending'): ?>
                                    <a href="reservations.php?action=confirm&id=<?= $res['id'] ?>" class="btn btn-sm btn-success"
                                        onclick="return confirm('Confirm this reservation?')">Confirm</a>
                                    <a href="reservations.php?action=cancel&id=<?= $res['id'] ?>" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Cancel this reservation?')">Cancel</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>