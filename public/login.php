<?php
require_once __DIR__ . '/_layout_top.php';

if ($u) redirect('index.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!csrf_validate($_POST['csrf'] ?? null)) {
    flash_set('error', 'Invalid CSRF token.');
    redirect('login.php');
  }

  $email = trim($_POST['email'] ?? '');
  $password = (string)($_POST['password'] ?? '');

  if (auth_login($email, $password)) {
    $role = auth_role();
    if ($role === 'USER') redirect('user.php');
    if ($role === 'STAFF') redirect('staff.php');
    redirect('admin.php');
  } else {
    flash_set('error', 'Invalid email or password.');
    redirect('login.php');
  }
}
?>

<div class="card">
  <h1>Log in</h1>
  <form method="post">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>" />
    <label>Email</label>
    <input name="email" type="email" required />
    <label>Password</label>
    <input name="password" type="password" required />
    <button class="btn" type="submit">Log in</button>
    <p><small>No account? <a href="register.php">Register</a>.</small></p>
  </form>
</div>

<?php require_once __DIR__ . '/_layout_bottom.php'; ?>
