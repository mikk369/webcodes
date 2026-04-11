<?php
/**
 * SMTP configuration template.
 *
 * Copy this file to `config.local.php` in the same directory and fill in the
 * real SMTP password. `config.local.php` is gitignored and must never be
 * committed.
 *
 * The form handler (send_email.php) loads config.local.php at runtime.
 */

return [
    'smtp_host'   => 'smtp.zone.eu',
    'smtp_port'   => 465,
    'smtp_secure' => 'ssl',         // 'ssl' for port 465, 'tls' for port 587
    'smtp_user'   => 'info@webcodes.ee',
    'smtp_pass'   => 'REPLACE_WITH_MAILBOX_PASSWORD',
    'from_email'  => 'info@webcodes.ee',
    'from_name'   => 'WebCodes',
    'to_email'    => 'info@webcodes.ee',
];
