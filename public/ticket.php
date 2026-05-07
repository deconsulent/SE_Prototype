<?php
require_once __DIR__ . '/_layout_top.php';
require_role('USER');

$ticket_id = (int)($_GET['ticket_id'] ?? 0);
if ($ticket_id <= 0) {
  flash_set('warning', 'Missing ticket_id.');
  redirect('user.php');
}

$status = queue_ticket_status($ticket_id, (int)$u['id']);
if (!$status) {
  flash_set('error', 'Ticket not found.');
  redirect('user.php');
}
?>

<div class="card">
  <h1>Live Ticket Status</h1>
  <p>This page refreshes automatically.</p>
</div>

<div id="ticket-status"></div>

<script src="assets/app.js"></script>
<script>
  startPolling(<?= (int)$ticket_id ?>, 7000);
</script>

<?php require_once __DIR__ . '/_layout_bottom.php'; ?>
