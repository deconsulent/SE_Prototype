<?php require_once __DIR__ . '/_layout_top.php'; ?>

<div class="card">
  <h1>Virtual Queue & Service Desk Management</h1>
  <p>
    QueueLess lets users join a virtual queue, see an estimated waiting time (ETA),
    and get notified when they are being called. Staff can serve the next person with one click.
  </p>

  <?php if (!$u): ?>
    <p><a class="btn" href="register.php">Create account</a> <a class="btn secondary" href="login.php">Log in</a></p>
  <?php else: ?>
    <?php if ($u['role'] === 'USER'): ?>
      <p><a class="btn" href="user.php">Go to User Dashboard</a></p>
    <?php elseif ($u['role'] === 'STAFF'): ?>
      <p><a class="btn" href="staff.php">Go to Staff Dashboard</a></p>
    <?php else: ?>
      <p><a class="btn" href="admin.php">Go to Admin Dashboard</a></p>
    <?php endif; ?>
  <?php endif; ?>
</div>

<div class="row">
  <div class="col card">
    <h2>User</h2>
    <ul>
      <li>Browse services</li>
      <li>Join queue</li>
      <li>Track ticket status + ETA</li>
    </ul>
  </div>
  <div class="col card">
    <h2>Staff</h2>
    <ul>
      <li>View waiting list</li>
      <li>Call next</li>
      <li>Mark served / no-show</li>
    </ul>
  </div>
  <div class="col card">
    <h2>Admin</h2>
    <ul>
      <li>Create services (name/location/hours)</li>
      <li>Activate/deactivate service</li>
      <li>View simple daily stats</li>
    </ul>
  </div>
</div>

<?php require_once __DIR__ . '/_layout_bottom.php'; ?>
