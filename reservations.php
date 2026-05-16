<?php
require_once 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle New Reservation
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $date = $_POST['reservation_date'];
    $time = $_POST['reservation_time'];
    $guests = (int) $_POST['guests'];
    $table = $_POST['table_number'] ?? null;
    $request = $_POST['special_request'] ?? '';

    $stmt = $pdo->prepare("
        INSERT INTO reservations 
        (user_id, reservation_date, reservation_time, guests, table_number, special_request)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$user_id, $date, $time, $guests, $table, $request]);

    $success = "Reservation request submitted successfully! We will confirm soon.";
}

// Fetch user's reservations
$stmt = $pdo->prepare("
    SELECT * FROM reservations 
    WHERE user_id = ? 
    ORDER BY reservation_date ASC, reservation_time ASC
");
$stmt->execute([$user_id]);
$reservations = $stmt->fetchAll();
?>

<div class="container my-5">
    <h1 class="text-center mb-4">📅 Book a Table</h1>

    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <!-- Booking Form -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date</label>
                                <input type="date" name="reservation_date" class="form-control"
                                    min="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Time</label>
                                <input type="time" name="reservation_time" class="form-control" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Number of Guests</label>
                            <select name="guests" class="form-select" required>
                                <option value="">Select...</option>
                                <option value="1">1 Person</option>
                                <option value="2" selected>2 People</option>
                                <option value="3">3 People</option>
                                <option value="4">4 People</option>
                                <option value="5">5+ People</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Preferred Table (Optional)</label>
                            <input type="text" name="table_number" class="form-control"
                                placeholder="e.g. Window Seat, Table 5">
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Special Request</label>
                            <textarea name="special_request" class="form-control" rows="3"
                                placeholder="Birthday, Anniversary, etc."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Reserve Table</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- My Reservations -->
    <h3 class="mt-5">My Reservations</h3>
    <?php if (empty($reservations)): ?>
        <p class="text-muted">No reservations yet.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Guests</th>
                        <th>Table</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservations as $res): ?>
                        <tr>
                            <td><?= date('d M Y', strtotime($res['reservation_date'])) ?></td>
                            <td><?= date('h:i A', strtotime($res['reservation_time'])) ?></td>
                            <td><?= $res['guests'] ?></td>
                            <td><?= htmlspecialchars($res['table_number'] ?? '-') ?></td>
                            <td>
                                <span
                                    class="badge bg-<?= $res['status'] == 'confirmed' ? 'success' : ($res['status'] == 'cancelled' ? 'danger' : 'warning') ?>">
                                    <?= ucfirst($res['status']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>