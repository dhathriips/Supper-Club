<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__.'/../app/core.php';
$email = $argv[1] ?? '';
if (!preg_match('/@example[.]test$/iD',$email) || !query('SELECT id FROM users WHERE email=?',[$email])->fetch()) {
    fwrite(STDERR, "Register the demo account first, then run: php bin/make-admin.php name@example.test\n"); exit(1);
}
query("UPDATE users SET role='admin' WHERE email=?",[$email]);
echo "Administrator role assigned. Sign out and back in.\n";

