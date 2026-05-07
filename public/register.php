<?php
require_once __DIR__ . '/_layout_top.php';

if ($u) redirect('index.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!csrf_validate($_POST['csrf'] ?? null)) {
    flash_set('error', 'Invalid CSRF token.');
    redirect('register.php');
  }

  $name = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $password = (string)($_POST['password'] ?? '');

  if ($name === '' || $email === '' || $password === '') {
    flash_set('warning', 'All fields are required.');
    redirect('register.php');
  }

  $res = auth_register($name, $email, $password);
  if ($res['ok']) {
    flash_set('success', 'Registration successful. Please log in.');
    redirect('login.php');
  } else {
    flash_set('error', $res['error'] ?? 'Registration failed.');
    redirect('register.php');
  }
}
?>

<div class="card">
  <h1>Create account</h1>
  <form method="post">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>" />
    <label>Name</label>
    <input name="name" required />
    <label>Email</label>
    <input name="email" type="email" required />
    <label>Password</label>
    <input name="password" type="password" minlength="8" required />
    <button class="btn" type="submit">Register</button>
    <p><small>Already have an account? <a href="login.php">Log in</a>.</small></p>
  </form>
</div>

<?php require_once __DIR__ . '/_layout_bottom.php'; ?>
