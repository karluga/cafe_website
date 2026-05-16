<?php
require_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch all orders with items
$stmt = $pdo->prepare("
    SELECT o.*, 
           GROUP_CONCAT(CONCAT(m.name, ' (x', oi.quantity, ')') SEPARATOR ', ') as items
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    LEFT JOIN menu m ON oi.menu_id = m.id
    WHERE o.user_id = ?
    GROUP BY o.id
    ORDER BY o.order_date DESC
");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();
?>

<div class="container my-5">
    <h1 class="text-center mb-5">📜 Order History</h1>

    <?php if (empty($orders)): ?>
        <div class="text-center py-5">
            <h4>No orders yet</h4>
            <a href="menu.php" class="btn btn-primary mt-3">Order Now</a>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($orders as $order): ?>
                <div class="col-lg-8 mx-auto mb-4">
                    <div class="card order-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <strong>Order #
                                <?= str_pad($order['id'], 6, '0', STR_PAD_LEFT) ?>
                            </strong>
                            <span
                                class="badge bg-<?= $order['status'] == 'completed' ? 'success' : ($order['status'] == 'cancelled' ? 'danger' : 'warning') ?>">
                                <?= ucfirst($order['status']) ?>
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Date:</strong>
                                        <?= date('d M Y • h:i A', strtotime($order['order_date'])) ?>
                                    </p>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <p><strong>Total:</strong> <span class="fw-bold">₹
                                            <?= number_format($order['total_amount'], 2) ?>
                                        </span></p>
                                </div>
                            </div>
                            <p><strong>Items:</strong>
                                <?= htmlspecialchars($order['items']) ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
    
<?php include 'includes/footer.php'; ?>