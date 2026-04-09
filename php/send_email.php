<?php
/**
 * WebCodes lead generation form handler.
 * Hardened: validation, header-injection protection, honeypot, rate limiting.
 */

header('Content-Type: application/json; charset=utf-8');

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

// 1. Only POST allowed.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    respond(false, 'Lubatud on ainult POST päringud.');
}

// 2. Honeypot: if filled, silently return success (bot trap).
if (!empty($_POST['website'])) {
    respond(true, 'Aitäh! Võtame sinuga peagi ühendust.');
}

// 3. Collect & sanitize inputs.
$fullName = isset($_POST['full-name']) ? clean_line($_POST['full-name']) : '';
$email    = isset($_POST['email']) ? clean_line($_POST['email']) : '';
$phone    = isset($_POST['phone']) ? clean_line($_POST['phone']) : '';
$message  = isset($_POST['message']) ? clean_body($_POST['message']) : '';

// 4. Length limits (truncate where specified).
if (mb_strlen($fullName) > 100) {
    $fullName = mb_substr($fullName, 0, 100);
}
if (mb_strlen($phone) > 30) {
    $phone = mb_substr($phone, 0, 30);
}
if (mb_strlen($message) > 5000) {
    $message = mb_substr($message, 0, 5000);
}

// 5. Validation.
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

// 6. Rate limit: 1 submission per IP per 30 seconds (file-based). After validation.
$ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'unknown';
$rateFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'wc_nl_' . md5($ip) . '.txt';
$now = time();
if (is_file($rateFile)) {
    $last = (int)@file_get_contents($rateFile);
    if ($last && ($now - $last) < 30) {
        http_response_code(429);
        respond(false, 'Palun oota hetk enne uut katset.');
    }
}
@file_put_contents($rateFile, (string)$now, LOCK_EX);

// 7. Build mail.
$to      = 'info@webcodes.ee';
$subject = 'Uus päring: ' . $fullName;

$body  = "Nimi: " . $fullName . "\n";
$body .= "Email: " . $email . "\n";
$body .= "Telefon: " . ($phone !== '' ? $phone : '-') . "\n";
$body .= "\n";
$body .= "Sõnum:\n";
$body .= $message . "\n";

$headers   = [];
$headers[] = 'From: WebCodes <info@webcodes.ee>';
$headers[] = 'Reply-To: ' . $email;
$headers[] = 'X-Mailer: PHP/' . phpversion();
$headers[] = 'MIME-Version: 1.0';
$headers[] = 'Content-Type: text/plain; charset=UTF-8';

$sent = @mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $body, implode("\r\n", $headers));

if ($sent) {
    respond(true, 'Aitäh! Võtame sinuga peagi ühendust.');
} else {
    http_response_code(500);
    respond(false, 'Kirja saatmine ebaõnnestus. Palun proovi hiljem uuesti.');
}
