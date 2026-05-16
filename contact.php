<?php
$page_title = "Contact - Cafe Bastions";
require_once 'includes/header.php';

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($message)) {
        $error = "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO messages (name, email, message) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $message]);
            $success = "Thank you! Your message has been received.";
        } catch (Exception $e) {
            $error = "Failed to send message. Try again later.";
        }
    }
}
?>

<div class="container my-5">
    <h2 class="text-center mb-4">Contact Us</h2>

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

    <form method="POST" onsubmit="return validateInput()">
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Message</label>
            <textarea name="message" id="message" rows="6" class="form-control" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Send Message</button>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>