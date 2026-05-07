<?php
require_once __DIR__ . '/_layout_top.php';
require_role('USER');

$services = services_all_active();
?>

<div class="card">
  <h1>User Dashboard</h1>
  <p>Select a service to join the virtual queue.</p>
</div>

<?php foreach ($services as $s): ?>
  <?php if (!(int)$s['is_active']) continue; ?>
  <?php
    $ticket = queue_get_user_active_ticket((int)$u['id'], (int)$s['id']);
  ?>
  <div class="card">
    <div class="row">
      <div class="col">
        <h2><?= e($s['name']) ?></h2>
        <small><?= e($s['location']) ?> • Hours: <?= e($s['open_time']) ?>–<?= e($s['close_time']) ?></small><br/>
        <small>Avg service time: <?= (int)$s['avg_service_minutes'] ?> min</small>
      </div>
      <div class="col" style="text-align:right;">
        <?php if ($ticket): ?>
          <span class="badge">You are in queue (Ticket #<?= (int)$ticket['ticket_no'] ?>)</span><br/>
          <a class="btn secondary" href="ticket.php?ticket_id=<?= (int)$ticket['id'] ?>">View live status</a>
        <?php else: ?>
          <form method="post" action="api/join_queue.php" style="margin:0;">
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>" />
            <input type="hidden" name="service_id" value="<?= (int)$s['id'] ?>" />
            <button class="btn" type="submit">Join queue</button>
          </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
<?php endforeach; ?>

<?php require_once __DIR__ . '/_layout_bottom.php'; ?>
