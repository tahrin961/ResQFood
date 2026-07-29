<?php
require_once __DIR__ . '/includes/functions.php';

if (is_logged_in()) redirect('/index.php');

$errors = [];
$old = ['name' => '', 'email' => '', 'role' => 'donor', 'phone' => '', 'organization' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old['name'] = $_POST['name'] ?? '';
    $old['email'] = $_POST['email'] ?? '';
    $old['role'] = $_POST['role'] ?? 'donor';
    $old['phone'] = $_POST['phone'] ?? '';
    $old['organization'] = $_POST['organization'] ?? '';

    $name = trim($old['name']);
    $email = trim($old['email']);
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    $role = in_array($old['role'], ['donor', 'ngo']) ? $old['role'] : 'donor';
    $phone = trim($old['phone']);
    $organization = trim($old['organization']);

    if ($name === '') $errors[] = 'Name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';
    if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';
    if ($password !== $confirm) $errors[] = 'Passwords do not match.';

    if (empty($errors)) {
        $check = $conn->prepare('SELECT user_id FROM users WHERE email = ?');
        $check->bind_param('s', $email);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $errors[] = 'An account with that email already exists.';
        }
        $check->close();
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare('INSERT INTO users (name, email, password, role, phone, organization) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('ssssss', $name, $email, $hash, $role, $phone, $organization);
        if ($stmt->execute()) {
            set_flash('success', 'Account created! Log in to continue.');
            redirect('/login.php');
        } else {
            $errors[] = 'Something went wrong. Please try again.';
        }
        $stmt->close();
    }
}

$page_title = 'Register';
include __DIR__ . '/includes/header.php';
?>
<div class="auth-wrap">
  <div class="auth-card">
    <h2>Create your account</h2>
    <p class="hint" style="margin-bottom:18px;">Join as a food donor or as an NGO ready to redistribute it.</p>

    <?php foreach ($errors as $err): ?>
      <div class="alert alert-error"><?= sanitize($err) ?></div>
    <?php endforeach; ?>

    <form method="POST" action="<?= BASE_URL ?>/register.php">
      <div class="form-group">
        <label>I am a</label>
        <div class="role-picker">
          <label><input type="radio" name="role" value="donor" <?= $old['role'] === 'donor' ? 'checked' : '' ?>><span>Donor</span></label>
          <label><input type="radio" name="role" value="ngo" <?= $old['role'] === 'ngo' ? 'checked' : '' ?>><span>NGO</span></label>
        </div>
      </div>
      <div class="form-group">
        <label for="name">Full name</label>
        <input type="text" id="name" name="name" value="<?= sanitize($old['name']) ?>" required>
      </div>
      <div class="form-group">
        <label for="organization">Organization (restaurant, NGO, etc. — optional)</label>
        <input type="text" id="organization" name="organization" value="<?= sanitize($old['organization']) ?>">
      </div>
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= sanitize($old['email']) ?>" required>
      </div>
      <div class="form-group">
        <label for="phone">Phone</label>
        <input type="tel" id="phone" name="phone" value="<?= sanitize($old['phone']) ?>">
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
      </div>
      <div class="form-group">
        <label for="confirm_password">Confirm password</label>
        <input type="password" id="confirm_password" name="confirm_password" required>
      </div>
      <button type="submit" class="btn btn-accent btn-lg" style="width:100%;">Create account</button>
    </form>
    <p class="auth-switch">Already have an account? <a href="<?= BASE_URL ?>/login.php">Log in</a></p>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
