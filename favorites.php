<?php
require_once 'includes/header.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Add to favorites
if (isset($_GET['add']) && is_numeric($_GET['add'])) {
    $service_id = (int) $_GET['add'];
    $stmt = $pdo->prepare("INSERT IGNORE INTO favorites (user_id, service_id) VALUES (?, ?)");
    $stmt->execute([$user_id, $service_id]);
    $success = "Added to favorites!";
}

// Remove from favorites
if (isset($_GET['remove']) && is_numeric($_GET['remove'])) {
    $fav_id = (int) $_GET['remove'];
    $stmt = $pdo->prepare("DELETE FROM favorites WHERE id = ? AND user_id = ?");
    $stmt->execute([$fav_id, $user_id]);
    $success = "Removed from favorites.";
}

// Fetch favorites
$stmt = $pdo->prepare("
    SELECT f.id as fav_id, s.* 
    FROM favorites f
    JOIN services s ON f.service_id = s.id 
    WHERE f.user_id = ?
    ORDER BY f.created_at DESC
");
$stmt->execute([$user_id]);
$favorites = $stmt->fetchAll();
?>

<div class="container my-5">
    <h1 class="text-center mb-4">❤️ My Favorites</h1>

    <?php if (isset($success)): ?>
        <div class="alert alert-success text-center"><?= $success ?></div>
    <?php endif; ?>

    <?php if (empty($favorites)): ?>
        <div class="text-center py-5">
            <h4>No favorites yet</h4>
            <a href="services.php" class="btn btn-primary">Browse Services</a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($favorites as $item): ?>
                <div class="col-md-4">
                    <div class="card h-100">
                        <img src="<?= htmlspecialchars($item['image']) ?>" class="card-img-top"
                            style="height: 220px; object-fit: cover;">
                        <div class="card-body">
                            <h5><?= htmlspecialchars($item['title']) ?></h5>
                            <p class="text-muted small"><?= htmlspecialchars($item['description']) ?></p>

                            <a href="favorites.php?remove=<?= $item['fav_id'] ?>" class="btn btn-outline-danger btn-sm">Remove
                                from Favorites</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>