<?php
require_once __DIR__ . '/_layout_top.php';
require_role('STAFF');

$services = services_all_active();
$service_id = (int)($_GET['service_id'] ?? 0);
if ($service_id <= 0 && count($services) > 0) {
  $service_id = (int)$services[0]['id'];
}

$service = $service_id ? service_get($service_id) : null;
$list = $service_id ? queue_list_waiting($service_id) : [];
$summary = $service_id ? analytics_service_summary($service_id) : null;
?>

<div class="card">
  <h1>Staff Dashboard</h1>
  <form method="get">
    <label>Choose service</label>
    <select name="service_id" onchange="this.form.submit()">
      <?php foreach ($services as $s): ?>
        <option value="<?= (int)$s['id'] ?>" <?= ((int)$s['id'] === $service_id) ? 'selected' : '' ?>>
          <?= e($s['name']) ?><?= ((int)$s['is_active'] ? '' : ' (inactive)') ?>
        </option>
      <?php endforeach; ?>
    </select>
  </form>
</div>

<?php if ($service): ?>
  <div class="row">
    <div class="col card">
      <h2><?= e($service['name']) ?></h2>
      <div><small><?= e($service['location']) ?> • Hours: <?= e($service['open_time']) ?>–<?= e($service['close_time']) ?></small></div>
      <?php if ($summary): ?>
        <div style="margin-top:10px;">
          <span class="badge">Waiting: <?= (int)$summary['waiting'] ?></span>
          <span class="badge">Called: <?= (int)$summary['called'] ?></span>
          <span class="badge">Served: <?= (int)$summary['served'] ?></span>
          <span class="badge">Avg wait: <?= (int)$summary['avg_total_wait_min'] ?> min</span>
        </div>
      <?php endif; ?>
    </div>
    <div class="col card" style="text-align:right;">
      <form method="post" action="api/staff_call_next.php" style="margin:0;">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>" />
        <input type="hidden" name="service_id" value="<?= (int)$service_id ?>" />
        <button class="btn" type="submit">Call next</button>
      </form>
      <small>Calls the next WAITING ticket (priority, then earliest ticket).</small>
    </div>
  </div>

  <div class="card">
    <h2>Current queue (today)</h2>
    <?php if (count($list) === 0): ?>
      <p><small>No active tickets.</small></p>
    <?php else: ?>
      <table>
        <thead>
          <tr>
            <th>Ticket</th>
            <th>User</th>
            <th>Status</th>
            <th>Joined</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($list as $t): ?>
            <tr>
              <td>#<?= (int)$t['ticket_no'] ?></td>
              <td><?= e($t['user_name']) ?></td>
              <td><span class="badge"><?= e($t['status']) ?></span></td>
              <td><small><?= e($t['joined_at']) ?></small></td>
              <td>
                <form method="post" action="api/staff_update_ticket.php" style="display:inline;">
                  <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>" />
                  <input type="hidden" name="ticket_id" value="<?= (int)$t['id'] ?>" />
                  <input type="hidden" name="service_id" value="<?= (int)$service_id ?>" />
                  <input type="hidden" name="status" value="SERVED" />
                  <button class="btn" type="submit">Served</button>
                </form>
                <form method="post" action="api/staff_update_ticket.php" style="display:inline;">
                  <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>" />
                  <input type="hidden" name="ticket_id" value="<?= (int)$t['id'] ?>" />
                  <input type="hidden" name="service_id" value="<?= (int)$service_id ?>" />
                  <input type="hidden" name="status" value="NOSHOW" />
                  <button class="btn secondary" type="submit">No-show</button>
                </form>
                <form method="post" action="api/staff_update_ticket.php" style="display:inline;">
                  <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>" />
                  <input type="hidden" name="ticket_id" value="<?= (int)$t['id'] ?>" />
                  <input type="hidden" name="service_id" value="<?= (int)$service_id ?>" />
                  <input type="hidden" name="status" value="CANCELLED" />
                  <button class="btn danger" type="submit">Cancel</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
<?php endif; ?>

<?php require_once __DIR__ . '/_layout_bottom.php'; ?>
