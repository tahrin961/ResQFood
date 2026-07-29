<?php
require_once __DIR__ . '/includes/functions.php';

if (is_logged_in()) redirect('/index.php');

$errors = [];
$old_email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old_email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare('SELECT user_id, name, password, role FROM users WHERE email = ?');
    $stmt->bind_param('s', $old_email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] === 'donor') redirect('/donor/dashboard.php');
        if ($user['role'] === 'ngo') redirect('/ngo/dashboard.php');
        redirect('/admin/dashboard.php');
    } else {
        $errors[] = 'Incorrect email or password.';
    }
}

$page_title = 'Log in';
include __DIR__ . '/includes/header.php';
?>
<div class="auth-wrap">
  <div class="auth-card">
    <h2>Welcome back</h2>
    <p class="hint" style="margin-bottom:18px;">Log in to post, browse, or manage food rescues.</p>

    <?php foreach ($errors as $err): ?>
      <div class="alert alert-error"><?= sanitize($err) ?></div>
    <?php endforeach; ?>

    <form method="POST" action="<?= BASE_URL ?>/login.php">
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= sanitize($old_email) ?>" required autofocus>
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
      </div>
      <button type="submit" class="btn btn-accent btn-lg" style="width:100%;">Log in</button>
    </form>
    <p class="auth-switch">New to ResQFood? <a href="<?= BASE_URL ?>/register.php">Create an account</a></p>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
