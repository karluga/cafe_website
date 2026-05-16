<?php
$page_title = "Manage Orders - Cafe Bastions";
require_once '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: ../login.php");
    exit;
}

require_once '../includes/db.php';

$success = '';

// Update order status
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $action = $_GET['action'];
    $new_status = ($action == 'complete') ? 'completed' : 'cancelled';

    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    if ($stmt->execute([$new_status, $id])) {
        $success = "Order #$id marked as $new_status.";
    }
}

// Fetch orders
$stmt = $pdo->prepare("
    SELECT o.*, u.username,
           GROUP_CONCAT(CONCAT(s.title, ' (x', oi.quantity, ')') SEPARATOR ', ') as items
    FROM orders o
    JOIN users u ON o.user_id = u.id
    LEFT JOIN order_items oi ON o.id = oi.order_id
    LEFT JOIN services s ON oi.menu_id = s.id
    GROUP BY o.id
    ORDER BY o.order_date DESC
");
$stmt->execute();
$orders = $stmt->fetchAll();
?>

<div class="container my-5">
    <h2>📦 Manage Orders</h2>

    <?php if ($success): ?>
        <div class="alert alert-success">
            <?= $success ?>
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Order ID</th>
                    <th>User</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="7" class="text-center">No orders yet.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#
                                <?= str_pad($order['id'], 5, '0', STR_PAD_LEFT) ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($order['username']) ?>
                            </td>
                            <td>
                                <?= date('d M Y H:i', strtotime($order['order_date'])) ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($order['items'] ?? 'N/A') ?>
                            </td>
                            <td><strong>€
                                    <?= number_format($order['total_amount'], 2) ?>
                                </strong></td>
                            <td>
                                <span
                                    class="badge bg-<?= $order['status'] == 'completed' ? 'success' : ($order['status'] == 'cancelled' ? 'danger' : 'warning') ?>">
                                    <?= ucfirst($order['status']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($order['status'] == 'pending'): ?>
                                    <a href="orders.php?action=complete&id=<?= $order['id'] ?>" class="btn btn-sm btn-success"
                                        onclick="return confirm('Mark as completed?')">Complete</a>
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