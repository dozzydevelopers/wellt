<?php
require_once '../includes/auth.php';
requireLogin();
$user = getCurrentUser();
$pageTitle = 'Support';
$success=''; $error='';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $action=sanitize($_POST['action']??'create');
    if ($action==='create') {
        $subject=sanitize($_POST['subject']??'');$msg=sanitize($_POST['message']??'');$priority=sanitize($_POST['priority']??'normal');
        if (!$subject||!$msg) { $error='Subject and message are required.'; }
        else {
            $ticketId=insert("INSERT INTO support_tickets (user_id,subject,priority) VALUES (?,?,?)",[$user['id'],$subject,$priority]);
            insert("INSERT INTO support_messages (ticket_id,sender_id,message) VALUES (?,?,?)",[$ticketId,$user['id'],$msg]);
            $success='Ticket #'.$ticketId.' created! We will respond within 24-48 hours.';
        }
    } elseif ($action==='reply') {
        $ticketId=intval($_POST['ticket_id']??0);$msg=sanitize($_POST['message']??'');
        $ticket=fetchOne("SELECT * FROM support_tickets WHERE id=? AND user_id=?",[$ticketId,$user['id']]);
        if ($ticket&&$msg) {
            insert("INSERT INTO support_messages (ticket_id,sender_id,message) VALUES (?,?,?)",[$ticketId,$user['id'],$msg]);
            query("UPDATE support_tickets SET status='open',updated_at=datetime('now') WHERE id=?",[$ticketId]);
            $success='Reply sent!';
        }
    }
}

$viewTicket=null;
if (isset($_GET['ticket'])) {
    $viewTicket=fetchOne("SELECT * FROM support_tickets WHERE id=? AND user_id=?",[intval($_GET['ticket']),$user['id']]);
    if ($viewTicket) $viewTicket['messages']=fetchAll("SELECT sm.*,u.username,u.is_admin FROM support_messages sm JOIN users u ON sm.sender_id=u.id WHERE sm.ticket_id=? ORDER BY sm.created_at ASC",[intval($_GET['ticket'])]);
}
$tickets=fetchAll("SELECT * FROM support_tickets WHERE user_id=? ORDER BY updated_at DESC",[$user['id']]);
include '../includes/header.php';
?>
<div class="dashboard-wrap">
  <?php include '../includes/sidebar.php'; ?>
  <div class="main-content">
    <?php include '../includes/topbar.php'; ?>
    <div class="page-content">
      <h2 class="page-title">Support</h2>
      <?php if ($success): ?><div class="alert alert-success"><?=$success?></div><?php endif; ?>
      <?php if ($error): ?><div class="alert alert-error"><?=$error?></div><?php endif; ?>

      <?php if ($viewTicket): ?>
        <a href="support.php" class="btn btn-outline mb-4">&larr; Back</a>
        <div class="card">
          <h3><?=sanitize($viewTicket['subject'])?> <span class="badge badge-<?=$viewTicket['status']==='open'?'approved':'pending'?>"><?=ucfirst($viewTicket['status'])?></span></h3>
          <div class="chat-messages">
          <?php foreach($viewTicket['messages'] as $m): ?>
          <div class="chat-msg <?=$m['is_admin']?'admin-msg':'user-msg'?>">
            <div class="chat-meta"><strong><?=$m['is_admin']?'Support Agent':sanitize($m['username'])?></strong> &middot; <?=date('M j, Y H:i',strtotime($m['created_at']))?></div>
            <div class="chat-body"><?=nl2br(sanitize($m['message']))?></div>
          </div>
          <?php endforeach; ?>
          </div>
          <?php if ($viewTicket['status']!=='closed'): ?>
          <form method="POST" class="mt-4">
            <input type="hidden" name="action" value="reply">
            <input type="hidden" name="ticket_id" value="<?=$viewTicket['id']?>">
            <div class="form-group"><label>Reply</label><textarea name="message" rows="3" required></textarea></div>
            <button type="submit" class="btn btn-primary">Send Reply</button>
          </form>
          <?php endif; ?>
        </div>
      <?php else: ?>
        <div class="card mb-4">
          <h3>New Support Ticket</h3>
          <form method="POST">
            <input type="hidden" name="action" value="create">
            <div class="form-row">
              <div class="form-group"><label>Subject</label><input type="text" name="subject" required></div>
              <div class="form-group"><label>Priority</label><select name="priority"><option value="low">Low</option><option value="normal" selected>Normal</option><option value="high">High</option><option value="urgent">Urgent</option></select></div>
            </div>
            <div class="form-group"><label>Message</label><textarea name="message" rows="4" required></textarea></div>
            <button type="submit" class="btn btn-primary">Submit Ticket</button>
          </form>
        </div>
        <div class="table-card">
          <table class="data-table">
            <thead><tr><th>ID</th><th>Subject</th><th>Priority</th><th>Status</th><th>Updated</th><th>Action</th></tr></thead>
            <tbody>
            <?php foreach($tickets as $t): ?>
            <tr><td>#<?=$t['id']?></td><td><?=sanitize($t['subject'])?></td><td><?=ucfirst($t['priority'])?></td>
            <td><span class="badge badge-<?=$t['status']==='open'?'approved':'pending'?>"><?=ucfirst($t['status'])?></span></td>
            <td><?=date('M j, Y',strtotime($t['updated_at']))?></td>
            <td><a href="support.php?ticket=<?=$t['id']?>" class="btn btn-sm btn-outline">View</a></td></tr>
            <?php endforeach; ?>
            <?php if(empty($tickets)): ?><tr><td colspan="6" class="empty-row">No tickets yet</td></tr><?php endif; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
<style>
.chat-messages{display:flex;flex-direction:column;gap:12px;margin:16px 0}
.chat-msg{padding:12px 14px;border-radius:8px;max-width:80%}
.user-msg{background:#EFF6FF;border-left:3px solid #3B82F6;align-self:flex-start}
.admin-msg{background:#F0FDF4;border-left:3px solid #22C55E;align-self:flex-end;text-align:right}
.chat-meta{font-size:11px;color:#94A3B8;margin-bottom:4px}
.chat-body{font-size:14px}
</style>
<?php include '../includes/footer.php'; ?>
