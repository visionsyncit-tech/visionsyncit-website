<?php
/* ============================================================
   VisionSync IT — Admin Dashboard
   Simple password-protected messages viewer
   ============================================================ */

session_start();

define('ADMIN_PASS', 'VisionSync@2025'); // ← غيّر هذا الباسوورد

// ── Auth ─────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
  if ($_POST['password'] === ADMIN_PASS) {
    $_SESSION['admin_auth'] = true;
  } else {
    $authError = 'Incorrect password.';
  }
}

if (isset($_POST['logout'])) {
  session_destroy();
  header('Location: dashboard.php');
  exit;
}

$authed = !empty($_SESSION['admin_auth']);

// ── Fetch messages ───────────────────────────────────────────
$messages = [];
$stats    = ['total' => 0, 'today' => 0, 'unread' => 0];

if ($authed) {
  require_once __DIR__ . '/../php/db.php';
  try {
    $db = getDB();

    // Create table if not exists
    $db->exec("CREATE TABLE IF NOT EXISTS contact_messages (
      id         INT AUTO_INCREMENT PRIMARY KEY,
      first_name VARCHAR(100) NOT NULL,
      last_name  VARCHAR(100) NOT NULL,
      email      VARCHAR(255) NOT NULL,
      phone      VARCHAR(50),
      service    VARCHAR(100),
      budget     VARCHAR(50),
      message    TEXT NOT NULL,
      is_read    TINYINT(1) DEFAULT 0,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Mark as read
    if (isset($_GET['read'])) {
      $db->prepare("UPDATE contact_messages SET is_read=1 WHERE id=?")->execute([(int)$_GET['read']]);
      header('Location: dashboard.php');
      exit;
    }

    // Delete message
    if (isset($_GET['delete'])) {
      $db->prepare("DELETE FROM contact_messages WHERE id=?")->execute([(int)$_GET['delete']]);
      header('Location: dashboard.php');
      exit;
    }

    $messages = $db->query("SELECT * FROM contact_messages ORDER BY created_at DESC")->fetchAll();
    $stats['total'] = count($messages);
    $stats['today'] = count(array_filter($messages, fn($m) => date('Y-m-d', strtotime($m['created_at'])) === date('Y-m-d')));
    $stats['unread'] = count(array_filter($messages, fn($m) => !$m['is_read']));
  } catch (PDOException $e) {
    $dbError = $e->getMessage();
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard — VisionSync IT</title>
  <link rel="stylesheet" href="../css/style.css" />
  <style>
    body{min-height:100vh}
    .dash-layout{display:grid;grid-template-columns:240px 1fr;min-height:100vh}
    .dash-sidebar{
      background:var(--bg2);border-right:1px solid var(--border);
      padding:32px 20px;position:sticky;top:0;height:100vh;
    }
    .dash-main{padding:40px;flex:1}
    .dash-stat-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:36px}
    .dash-stat{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:24px}
    .dash-stat-num{font-family:var(--font-disp);font-size:2.2rem;font-weight:700}
    .dash-stat-label{font-size:0.82rem;color:var(--text2);margin-top:4px}
    .msg-table{width:100%;border-collapse:collapse}
    .msg-table th{text-align:left;padding:12px 16px;font-size:0.78rem;text-transform:uppercase;letter-spacing:0.08em;color:var(--text3);border-bottom:1px solid var(--border)}
    .msg-table td{padding:16px;border-bottom:1px solid var(--border);font-size:0.9rem;vertical-align:top}
    .msg-table tr:hover td{background:var(--surface)}
    .badge{display:inline-flex;padding:3px 10px;border-radius:100px;font-size:0.72rem;font-weight:600}
    .badge-new{background:rgba(79,110,247,0.15);color:var(--accent2);border:1px solid var(--border2)}
    .badge-read{background:var(--surface2);color:var(--text3)}
    .action-link{font-size:0.8rem;color:var(--accent2);padding:4px 8px;border-radius:var(--radius-sm);transition:var(--trans)}
    .action-link:hover{background:rgba(79,110,247,0.1)}
    .action-link.danger{color:#f87171}
    .action-link.danger:hover{background:rgba(239,68,68,0.1)}
    .login-box{max-width:400px;margin:120px auto;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-xl);padding:48px}
    @media(max-width:768px){.dash-layout{grid-template-columns:1fr}.dash-sidebar{height:auto;position:static}}
  </style>
</head>
<body>
<div class="grid-bg"></div>

<?php if (!$authed): ?>
<!-- ── LOGIN ─────────────────────────────────────────── -->
<div class="relative">
  <div class="login-box">
    <div style="text-align:center;margin-bottom:32px">
      <div class="logo__icon" style="margin:0 auto 16px;width:52px;height:52px;font-size:26px">⚡</div>
      <h1 class="h3">Admin Dashboard</h1>
      <p class="small" style="margin-top:8px">VisionSync IT</p>
    </div>
    <?php if (isset($authError)): ?>
      <div class="form-message error" style="display:block;margin-bottom:20px"><?= htmlspecialchars($authError) ?></div>
    <?php endif; ?>
    <form method="POST">
      <div class="form-group">
        <label class="form-label">Password</label>
        <input class="form-input" type="password" name="password" placeholder="Enter admin password" autofocus required />
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:8px">Login →</button>
    </form>
  </div>
</div>

<?php else: ?>
<!-- ── DASHBOARD ─────────────────────────────────────── -->
<div class="dash-layout relative">
  <aside class="dash-sidebar">
    <div class="logo" style="margin-bottom:40px">
      <div class="logo__icon" style="width:32px;height:32px;font-size:16px">⚡</div>
      VisionSync IT
    </div>
    <nav style="display:flex;flex-direction:column;gap:4px">
      <a href="dashboard.php" class="nav-link active" style="padding:10px 14px">📬 Messages <span style="margin-left:6px;padding:2px 8px;background:var(--accent);color:#fff;border-radius:100px;font-size:0.72rem"><?= $stats['unread'] ?></span></a>
      <a href="../index.html" class="nav-link" style="padding:10px 14px" target="_blank">🌐 View Site</a>
    </nav>
    <div style="position:absolute;bottom:24px;left:20px;right:20px">
      <form method="POST">
        <button type="submit" name="logout" class="btn btn-secondary btn-sm" style="width:100%;justify-content:center">Logout</button>
      </form>
    </div>
  </aside>

  <main class="dash-main">
    <div style="margin-bottom:32px">
      <h1 class="h3">Messages</h1>
      <p class="small">All incoming project inquiries from your website contact form.</p>
    </div>

    <!-- Stats -->
    <div class="dash-stat-grid">
      <div class="dash-stat">
        <div class="dash-stat-num" style="color:var(--accent2)"><?= $stats['total'] ?></div>
        <div class="dash-stat-label">Total Messages</div>
      </div>
      <div class="dash-stat">
        <div class="dash-stat-num" style="color:var(--teal)"><?= $stats['today'] ?></div>
        <div class="dash-stat-label">Received Today</div>
      </div>
      <div class="dash-stat">
        <div class="dash-stat-num" style="color:#f59e0b"><?= $stats['unread'] ?></div>
        <div class="dash-stat-label">Unread</div>
      </div>
    </div>

    <?php if (isset($dbError)): ?>
      <div class="form-message error" style="display:block;margin-bottom:24px">DB Error: <?= htmlspecialchars($dbError) ?></div>
    <?php endif; ?>

    <?php if (empty($messages)): ?>
      <div style="text-align:center;padding:80px 40px;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg)">
        <div style="font-size:3rem;margin-bottom:16px">📭</div>
        <h3 class="h4" style="margin-bottom:8px">No messages yet</h3>
        <p class="small">Messages from your contact form will appear here.</p>
      </div>
    <?php else: ?>
      <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden">
        <table class="msg-table">
          <thead>
            <tr>
              <th>Status</th>
              <th>Name</th>
              <th>Email</th>
              <th>Service</th>
              <th>Message</th>
              <th>Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($messages as $msg): ?>
            <tr>
              <td><span class="badge <?= $msg['is_read'] ? 'badge-read' : 'badge-new' ?>"><?= $msg['is_read'] ? 'Read' : 'New' ?></span></td>
              <td style="font-weight:500"><?= htmlspecialchars($msg['first_name'] . ' ' . $msg['last_name']) ?></td>
              <td><a href="mailto:<?= htmlspecialchars($msg['email']) ?>" style="color:var(--accent2)"><?= htmlspecialchars($msg['email']) ?></a></td>
              <td style="color:var(--text2)"><?= htmlspecialchars($msg['service']) ?></td>
              <td style="max-width:300px;color:var(--text2)"><?= nl2br(htmlspecialchars(substr($msg['message'], 0, 120))) ?>…</td>
              <td style="color:var(--text3);white-space:nowrap"><?= date('d M Y', strtotime($msg['created_at'])) ?></td>
              <td style="white-space:nowrap">
                <?php if (!$msg['is_read']): ?>
                  <a href="?read=<?= $msg['id'] ?>" class="action-link">✓ Mark Read</a>
                <?php endif; ?>
                <a href="?delete=<?= $msg['id'] ?>" class="action-link danger" onclick="return confirm('Delete this message?')">✕ Delete</a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </main>
</div>
<?php endif; ?>

</body>
</html>