<?php
/* ============================================================
   VisionSync IT — Contact Form Handler
   ============================================================ */

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

// Allow only POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  exit(json_encode(['success' => false, 'message' => 'Method not allowed.']));
}

require_once __DIR__ . '/db.php';

// ── Sanitize & Validate ──────────────────────────────────────
function clean(string $val): string {
  return htmlspecialchars(strip_tags(trim($val)), ENT_QUOTES, 'UTF-8');
}

$firstName = clean($_POST['firstName'] ?? '');
$lastName  = clean($_POST['lastName']  ?? '');
$email     = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
$phone     = clean($_POST['phone']   ?? '');
$service   = clean($_POST['service'] ?? '');
$budget    = clean($_POST['budget']  ?? '');
$message   = clean($_POST['message'] ?? '');

// Required fields
if (!$firstName || !$lastName || !$email || !$service || !$message) {
  http_response_code(400);
  exit(json_encode(['success' => false, 'message' => 'Please fill in all required fields.']));
}

if (strlen($message) < 10) {
  http_response_code(400);
  exit(json_encode(['success' => false, 'message' => 'Message is too short. Please provide more details.']));
}

// ── Insert into database ─────────────────────────────────────
try {
  $db = getDB();
  $stmt = $db->prepare("
    INSERT INTO contact_messages 
      (first_name, last_name, email, phone, service, budget, message, created_at)
    VALUES 
      (:first_name, :last_name, :email, :phone, :service, :budget, :message, NOW())
  ");
  $stmt->execute([
    ':first_name' => $firstName,
    ':last_name'  => $lastName,
    ':email'      => $email,
    ':phone'      => $phone,
    ':service'    => $service,
    ':budget'     => $budget,
    ':message'    => $message,
  ]);

  // ── Send email notification (optional) ──────────────────────
  $to      = 'info@visionsyncit.com';
  $subject = "New Project Inquiry from $firstName $lastName";
  $body    = "Name: $firstName $lastName\n"
           . "Email: $email\n"
           . "Phone: $phone\n"
           . "Service: $service\n"
           . "Budget: $budget\n\n"
           . "Message:\n$message\n";
  $headers = "From: noreply@visionsyncit.com\r\nReply-To: $email\r\nX-Mailer: PHP/" . phpversion();
  @mail($to, $subject, $body, $headers); // @ suppresses error if mail not configured

  exit(json_encode([
    'success' => true,
    'message' => "Thank you, $firstName! We received your message and will reply within 24 hours.",
  ]));

} catch (PDOException $e) {
  http_response_code(500);
  exit(json_encode(['success' => false, 'message' => 'Failed to save your message. Please try again.']));
}