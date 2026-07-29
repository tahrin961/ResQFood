<?php

require_once __DIR__ . '/includes/functions.php';

$check = $conn->query("SELECT COUNT(*) AS c FROM users WHERE role = 'admin'");
$adminExists = $check->fetch_assoc()['c'] > 0;

$errors = [];
$done = false;

if (!$adminExists && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($name === '') $errors[] = 'Name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';
    if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'admin')");
        $stmt->bind_param('sss', $name, $email, $hash);
        if ($stmt->execute()) {
            $done = true;
        } else {
            $errors[] = 'Could not create admin (is the email already used?).';
        }
        $stmt->close();
    }
}

$page_title = 'Admin Setup';
include __DIR__ . '/includes/header.php';
?>
<div class="auth-wrap">
  <div class="auth-card">
    <?php if ($adminExists): ?>
      <h2>Admin already set up</h2>
      <p class="hint">An admin account already exists. Please delete <code>create_admin.php</code> from the project now.</p>
      <p class="auth-switch"><a href="<?= BASE_URL ?>/login.php">Go to login</a></p>
    <?php elseif ($done): ?>
      <h2>Admin account created</h2>
      <p class="hint">You can now log in. For safety, delete <code>create_admin.php</code> from the project before submitting/demoing.</p>
      <p class="auth-switch"><a href="<?= BASE_URL ?>/login.php">Go to login</a></p>
    <?php else: ?>
      <h2>Create the admin account</h2>
      <p class="hint" style="margin-bottom:18px;">This form only works once — until an admin exists.</p>
      <?php foreach ($errors as $err): ?>
        <div class="alert alert-error"><?= sanitize($err) ?></div>
      <?php endforeach; ?>
      <form method="POST" action="<?= BASE_URL ?>/create_admin.php">
        <div class="form-group">
          <label for="name">Name</label>
          <input type="text" id="name" name="name" required>
        </div>
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary btn-lg" style="width:100%;">Create admin</button>
      </form>
    <?php endif; ?>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
