<?php
require_once __DIR__ . '/_layout_top.php';
require_role('ADMIN');

$services = services_all_active();
?>

<div class="card">
  <h1>Admin Dashboard</h1>
  <p>Create and manage services.</p>
</div>

<div class="row">
  <div class="col card">
    <h2>Create service</h2>
    <form method="post" action="api/admin_create_service.php">
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>" />
      <label>Name</label>
      <input name="name" required placeholder="e.g., Dean's Office" />
      <label>Location</label>
      <input name="location" required placeholder="Building A, Room 101" />
      <label>Open time (HH:MM:SS)</label>
      <input name="open_time" required value="09:00:00" />
      <label>Close time (HH:MM:SS)</label>
      <input name="close_time" required value="17:00:00" />
      <label>Avg service time (minutes)</label>
      <input name="avg_service_minutes" type="number" min="1" value="5" required />
      <button class="btn" type="submit">Create</button>
    </form>
  </div>

  <div class="col card">
    <h2>Services</h2>
    <?php if (count($services) === 0): ?>
      <p><small>No services yet.</small></p>
    <?php else: ?>
      <table>
        <thead><tr><th>Name</th><th>Active</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($services as $s): ?>
          <tr>
            <td>
              <b><?= e($s['name']) ?></b><br/>
              <small><?= e($s['location']) ?></small>
            </td>
            <td><?= ((int)$s['is_active'] ? 'Yes' : 'No') ?></td>
            <td>
              <form method="post" action="api/admin_toggle_service.php" style="display:inline;">
                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>" />
                <input type="hidden" name="service_id" value="<?= (int)$s['id'] ?>" />
                <input type="hidden" name="active" value="<?= ((int)$s['is_active'] ? 0 : 1) ?>" />
                <button class="btn secondary" type="submit">
                  <?= ((int)$s['is_active'] ? 'Deactivate' : 'Activate') ?>
                </button>
              </form>
              <a class="btn" href="staff.php?service_id=<?= (int)$s['id'] ?>">Open staff view</a>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/_layout_bottom.php'; ?>
