<?php
$page_title = "Services - Cafe Bastions";
require_once 'includes/header.php';
require_once 'classes/Service.php';

$service = new Service();
$search = $_GET['search'] ?? '';
$services = $service->readAll($search);
?>

<div class="container my-5">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Our Services</h2>
        </div>
        <div class="col-md-6">
            <form method="GET" class="d-flex">
                <input type="text" name="search" class="form-control me-2" placeholder="Search services..."
                    value="<?= htmlspecialchars($search) ?>">
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
        </div>
    </div>

    <div class="row g-4">
        <?php if (empty($services)): ?>
            <p class="text-center">No services found!</p>
        <?php else: ?>
            <?php foreach ($services as $item): ?>
                <div class="col-md-4">
                    <div class="card text-center h-100">
                        <img src="<?= htmlspecialchars($item['image']) ?>" class="card-img-top"
                            style="height: 220px; object-fit: cover;">
                        <div class="card-body">
                            <h5><?= htmlspecialchars($item['title']) ?></h5>
                            <p><?= htmlspecialchars($item['description']) ?></p>

                            <!-- Add to Favorites Button -->
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <a href="favorites.php?add=<?= $item['id'] ?>" class="btn btn-outline-danger btn-sm mt-2">
                                    Add to Favorites
                                </a>
                            <?php endif; ?>

                            <?php if (isset($_SESSION['user_id'])): ?>
                                <a href="order-history.php?add=<?= $item['id'] ?>" class="btn btn-outline-success btn-sm mt-2">
                                    Order
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>