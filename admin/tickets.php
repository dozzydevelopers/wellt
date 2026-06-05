<?php
require_once '../includes/auth.php';
requireAdmin();
$pageTitle = 'Support Tickets';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = sanitize($_POST['action'] ?? '');
  $ticketId = intval($_POST['ticket_id'] ?? 0);
  if ($action === 'reply') {
    $msg = sanitize($_POST['message'] ?? '');
    $adminUser = getCurrentUser();
    if ($msg && $ticketId) {
      insert("INSERT INTO support_messages (ticket_id,sender_id,message,is_admin) VALUES (?,?,?,1)", [$ticketId, $adminUser['id'], $msg]);
      query("UPDATE support_tickets SET status='in_progress',updated_at=datetime('now') WHERE id=?", [$ticketId]);
    }
  } elseif ($action === 'close') {
    query("UPDATE support_tickets SET status='closed' WHERE id=?", [$ticketId]);
  }
  header('Location: tickets.php?ticket=' . $ticketId);
  exit;
}

$viewTicket = null;
if (isset($_GET['ticket'])) {
  $viewTicket = fetchOne("SELECT st.*,u.username,u.email FROM support_tickets st JOIN users u ON st.user_id=u.id WHERE st.id=?", [intval($_GET['ticket'])]);
  if ($viewTicket)
    $viewTicket['messages'] = fetchAll("SELECT sm.*,u.username,u.is_admin FROM support_messages sm JOIN users u ON sm.sender_id=u.id WHERE sm.ticket_id=? ORDER BY sm.created_at ASC", [intval($_GET['ticket'])]);
}
$status = sanitize($_GET['status'] ?? '');
$where = "1=1";
$params = [];
if ($status) {
  $where .= " AND st.status=?";
  $params[] = $status;
}
$tickets = fetchAll("SELECT st.*,u.username FROM support_tickets st JOIN users u ON st.user_id=u.id WHERE $where ORDER BY st.updated_at DESC", $params);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Tickets - Admin</title>
  <link rel="stylesheet" href="<?= assetUrl('assets/css/admin.css') ?>">
</head>

<body class="admin-body">
  <?php include 'includes/admin-sidebar.php'; ?>
  <div class="admin-main">
    <?php include 'includes/admin-topbar.php'; ?>
    <div class="admin-content">
      <h2 class="admin-page-title">Support Tickets</h2>
      <?php if ($viewTicket): ?>
        <a href="tickets.php" class="btn btn-outline mb-4">&larr; Back</a>
        <div class="admin-card">
          <h3>#<?= $viewTicket['id'] ?> - <?= sanitize($viewTicket['subject']) ?></h3>
          <p>User: <?= sanitize($viewTicket['username']) ?> (<?= sanitize($viewTicket['email']) ?>)</p>
          <div class="chat-messages">
            <?php foreach ($viewTicket['messages'] as $m): ?>
              <div class="chat-msg <?= $m['is_admin'] ? 'admin-msg' : 'user-msg' ?>">
                <div class="chat-meta"><strong><?= $m['is_admin'] ? 'Support Agent' : sanitize($m['username']) ?></strong>
                  &middot; <?= date('M j, Y H:i', strtotime($m['created_at'])) ?></div>
                <div class="chat-body"><?= nl2br(sanitize($m['message'])) ?></div>
              </div>
            <?php endforeach; ?>
          </div>
          <?php if ($viewTicket['status'] !== 'closed'): ?>
            <form method="POST" class="mt-4">
              <input type="hidden" name="action" value="reply">
              <input type="hidden" name="ticket_id" value="<?= $viewTicket['id'] ?>">
              <div class="form-group"><label>Admin Reply</label><textarea name="message" rows="3" required></textarea></div>
              <div style="display:flex;gap:8px">
                <button type="submit" class="btn btn-primary">Send Reply</button>
                <form method="POST" style="display:inline"><input type="hidden" name="action" value="close"><input
                    type="hidden" name="ticket_id" value="<?= $viewTicket['id'] ?>"><button type="submit"
                    class="btn btn-outline" onclick="return confirm('Close ticket?')">Close Ticket</button></form>
              </div>
            </form>
          <?php endif; ?>
        </div>
      <?php else: ?>
        <div class="admin-filters">
          <form method="GET" class="filter-form"><select name="status">
              <option value="">All</option><?php foreach (['open', 'in_progress', 'closed'] as $s): ?>
                <option value="<?= $s ?>" <?= $status === $s ? 'selected' : '' ?>><?= str_replace('_', ' ', ucfirst($s)) ?></option>
              <?php endforeach; ?>
            </select><button type="submit" class="btn btn-primary">Filter</button></form>
        </div>
        <div class="admin-card">
          <table class="admin-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>User</th>
                <th>Subject</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Updated</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($tickets as $t): ?>
                <tr>
                  <td>#<?= $t['id'] ?></td>
                  <td><?= sanitize($t['username']) ?></td>
                  <td><?= sanitize($t['subject']) ?></td>
                  <td><?= ucfirst($t['priority']) ?></td>
                  <td><span
                      class="badge badge-<?= $t['status'] === 'open' ? 'approved' : ($t['status'] === 'closed' ? 'pending' : 'submitted') ?>"><?= ucfirst(str_replace('_', ' ', $t['status'])) ?></span>
                  </td>
                  <td><?= date('M j, Y', strtotime($t['updated_at'])) ?></td>
                  <td><a href="tickets.php?ticket=<?= $t['id'] ?>" class="btn-table">View</a></td>
                </tr>
              <?php endforeach; ?>
              <?php if (empty($tickets)): ?>
                <tr>
                  <td colspan="7" class="empty-row">No tickets</td>
                </tr><?php endif; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>
  <style>
    .chat-messages {
      display: flex;
      flex-direction: column;
      gap: 10px;
      margin: 16px 0;
      max-height: 400px;
      overflow-y: auto
    }

    .chat-msg {
      padding: 10px 14px;
      border-radius: 8px;
      max-width: 80%
    }

    .user-msg {
      background: #EFF6FF;
      border-left: 3px solid #3B82F6
    }

    .admin-msg {
      background: #F0FDF4;
      border-left: 3px solid #22C55E;
      align-self: flex-end
    }

    .chat-meta {
      font-size: 11px;
      color: #94A3B8;
      margin-bottom: 4px
    }

    .mt-4 {
      margin-top: 14px
    }
  </style>
</body>

</html>