<?php
/**
 * WebCodes lead generation form handler.
 *
 * Delivery: authenticated SMTP via PHPMailer (Zone.eu).
 * SMTP credentials are loaded from config.local.php (gitignored).
 *
 * Hardening: validation, header-injection protection, honeypot, rate limiting.
 */

header('Content-Type: application/json; charset=utf-8');

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

function respond($success, $message) {
    echo json_encode(['success' => (bool)$success, 'message' => $message]);
    exit;
}

function clean_line($value) {
    // Strip CR/LF and NULL to prevent header injection. For single-line fields.
    $value = (string)$value;
    $value = str_replace(["\r", "\n", "\0"], '', $value);
    return trim($value);
}

function clean_body($value) {
    // For multi-line body content: allow \n, strip \r and NULL.
    $value = (string)$value;
    $value = str_replace(["\r", "\0"], '', $value);
    return trim($value);
}

/**
 * Log a server-side error without leaking the reason to the client.
 * Writes to PHP's error_log — viewable in Zone's control panel or the host's log.
 */
function log_error($context, $detail) {
    // Never log credentials or full user payloads. Context + short detail only.
    error_log('[webcodes lead form] ' . $context . ': ' . $detail);
}

// ---------------------------------------------------------------------------
// 1. Method check — only POST allowed.
// ---------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    respond(false, 'Lubatud on ainult POST päringud.');
}

// ---------------------------------------------------------------------------
// 2. Honeypot — if filled, silently return success (bot trap).
// ---------------------------------------------------------------------------
if (!empty($_POST['website'])) {
    respond(true, 'Aitäh! Võtame sinuga peagi ühendust.');
}

// ---------------------------------------------------------------------------
// 3. Collect & sanitize inputs.
// ---------------------------------------------------------------------------
$fullName = isset($_POST['full-name']) ? clean_line($_POST['full-name']) : '';
$email    = isset($_POST['email'])     ? clean_line($_POST['email'])     : '';
$phone    = isset($_POST['phone'])     ? clean_line($_POST['phone'])     : '';
$message  = isset($_POST['message'])   ? clean_body($_POST['message'])   : '';

// ---------------------------------------------------------------------------
// 4. Length limits (truncate where specified).
// ---------------------------------------------------------------------------
if (mb_strlen($fullName) > 100)  { $fullName = mb_substr($fullName, 0, 100); }
if (mb_strlen($phone)    > 30)   { $phone    = mb_substr($phone,    0, 30); }
if (mb_strlen($message)  > 5000) { $message  = mb_substr($message,  0, 5000); }

// ---------------------------------------------------------------------------
// 5. Validation.
// ---------------------------------------------------------------------------
if ($fullName === '') {
    respond(false, 'Palun sisesta oma nimi.');
}
if (strlen($email) > 254) {
    respond(false, 'E-posti aadress on liiga pikk.');
}
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond(false, 'Palun sisesta kehtiv e-posti aadress.');
}
if ($message === '') {
    respond(false, 'Palun kirjelda oma projekti.');
}

// ---------------------------------------------------------------------------
// 6. Rate limit: 1 submission per IP per 30 seconds (file-based).
// ---------------------------------------------------------------------------
$ip       = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'unknown';
$rateFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'wc_nl_' . md5($ip) . '.txt';
$now      = time();
if (is_file($rateFile)) {
    $last = (int)@file_get_contents($rateFile);
    if ($last && ($now - $last) < 30) {
        http_response_code(429);
        respond(false, 'Palun oota hetk enne uut katset.');
    }
}
@file_put_contents($rateFile, (string)$now, LOCK_EX);

// ---------------------------------------------------------------------------
// 7. Load SMTP config.
// ---------------------------------------------------------------------------
$configPath = __DIR__ . '/config.local.php';
if (!is_file($configPath)) {
    log_error('config_missing', 'config.local.php not found');
    http_response_code(500);
    respond(false, 'Serveri konfiguratsioon puudub. Palun võta ühendust otse: info@webcodes.ee');
}
$config = require $configPath;
$requiredKeys = ['smtp_host', 'smtp_port', 'smtp_secure', 'smtp_user', 'smtp_pass', 'from_email', 'from_name', 'to_email'];
foreach ($requiredKeys as $key) {
    if (!isset($config[$key]) || $config[$key] === '') {
        log_error('config_incomplete', 'missing key: ' . $key);
        http_response_code(500);
        respond(false, 'Serveri konfiguratsioon on puudulik. Palun võta ühendust otse: info@webcodes.ee');
    }
}

// ---------------------------------------------------------------------------
// 8. Load PHPMailer (vendored, no Composer needed).
// ---------------------------------------------------------------------------
require_once __DIR__ . '/vendor/PHPMailer/Exception.php';
require_once __DIR__ . '/vendor/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/vendor/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

// ---------------------------------------------------------------------------
// 9. Build & send the message.
// ---------------------------------------------------------------------------
$subject = 'Uus päring: ' . $fullName;

$body  = "Nimi: "    . $fullName . "\n";
$body .= "Email: "   . $email    . "\n";
$body .= "Telefon: " . ($phone !== '' ? $phone : '-') . "\n";
$body .= "\n";
$body .= "Sõnum:\n";
$body .= $message    . "\n";

$mail = new PHPMailer(true); // true = throw exceptions on failure

try {
    // SMTP transport
    $mail->isSMTP();
    $mail->Host       = $config['smtp_host'];
    $mail->Port       = (int)$config['smtp_port'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $config['smtp_user'];
    $mail->Password   = $config['smtp_pass'];
    $mail->SMTPSecure = $config['smtp_secure']; // 'ssl' or 'tls'
    $mail->Timeout    = 15;

    // Character set
    $mail->CharSet  = 'UTF-8';
    $mail->Encoding = 'base64';

    // Addressing
    $mail->setFrom($config['from_email'], $config['from_name']);
    $mail->addAddress($config['to_email']);
    $mail->addReplyTo($email, $fullName);

    // Content
    $mail->isHTML(false);
    $mail->Subject = $subject;
    $mail->Body    = $body;

    $mail->send();
    respond(true, 'Aitäh! Võtame sinuga peagi ühendust.');
} catch (PHPMailerException $e) {
    // PHPMailer's own errorInfo is safer to log than the exception message (which may include the reply).
    log_error('smtp_send_failed', $mail->ErrorInfo);
    http_response_code(500);
    respond(false, 'Kirja saatmine ebaõnnestus. Palun proovi hiljem uuesti või kirjuta otse: info@webcodes.ee');
} catch (\Throwable $e) {
    log_error('unexpected_error', $e->getMessage());
    http_response_code(500);
    respond(false, 'Kirja saatmine ebaõnnestus. Palun proovi hiljem uuesti või kirjuta otse: info@webcodes.ee');
}
