<?php
require_once __DIR__ . '/../includes/functions.php';
require_role('admin');

$role_filter = $_GET['role'] ?? '';
$valid_roles = ['donor', 'ngo', 'admin'];

$sql = "SELECT * FROM users";
if (in_array($role_filter, $valid_roles)) {
    $sql .= " WHERE role = ?";
}
$sql .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
if (in_array($role_filter, $valid_roles)) {
    $stmt->bind_param('s', $role_filter);
}
$stmt->execute();
$users = $stmt->get_result();

$page_title = 'All Users';
include __DIR__ . '/../includes/header.php';
?>

<div class="filter-bar">
  <form method="GET" action="<?= BASE_URL ?>/admin/users.php">
    <select name="role" onchange="this.form.submit()">
      <option value="">All roles</option>
      <?php foreach ($valid_roles as $r): ?>
        <option value="<?= $r ?>" <?= $role_filter === $r ? 'selected' : '' ?>><?= ucfirst($r) ?></option>
      <?php endforeach; ?>
    </select>
  </form>
</div>

<div class="table-wrap">
  <table>
    <thead>
      <tr><th>Name</th><th>Organization</th><th>Email</th><th>Phone</th><th>Role</th><th>Joined</th></tr>
    </thead>
    <tbody>
      <?php while ($row = $users->fetch_assoc()): ?>
        <tr>
          <td><?= sanitize($row['name']) ?></td>
          <td><?= sanitize($row['organization'] ?: '—') ?></td>
          <td><?= sanitize($row['email']) ?></td>
          <td><?= sanitize($row['phone'] ?: '—') ?></td>
          <td><span class="role-tag"><?= sanitize(ucfirst($row['role'])) ?></span></td>
          <td><?= sanitize(time_ago($row['created_at'])) ?></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
