<?php
declare(strict_types=1);
date_default_timezone_set('Asia/Kolkata');

function db(): PDO {
    static $pdo;
    if (!$pdo) {
        $cfg = require __DIR__ . '/../config.php';
        $pdo = new PDO($cfg['dsn'], $cfg['user'], $cfg['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }
    return $pdo;
}
function query(string $sql, array $args = []): PDOStatement {
    $stmt = db()->prepare($sql);
    $stmt->execute($args);
    return $stmt;
}
function e($s): string { return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function field(array $data, string $key): string {
    return is_string($data[$key] ?? null) ? trim($data[$key]) : '';
}
function fail(string $message): never { throw new DomainException($message); }
function bounded(string $s, int $max, string $label): string {
    if ($s === '' || strlen($s) > $max) fail("$label must contain 1–$max bytes of text.");
    return $s;
}
function number(string $s, int $min, int $max, string $label): int {
    if (!ctype_digit($s) || (int)$s < $min || (int)$s > $max) fail("$label must be between $min and $max.");
    return (int)$s;
}
function go(string $page, array $args = []): never {
    header('Location: ?' . http_build_query(['p'=>$page] + $args), true, 303);
    exit;
}
function token(): string {
    return '<input type="hidden" name="csrf" value="'.e($_SESSION['csrf']).'">';
}
function act(string $name): string {
    return token().'<input type="hidden" name="action" value="'.e($name).'">';
}
function flash(string $text): void { $_SESSION['message'] = $text; }
function user(): ?array { return $_SESSION['user'] ?? null; }
function needUser(string $next): void {
    if (!user()) { $_SESSION['next'] = $next; flash('Sign in with a classroom account to continue.'); go('login'); }
}
function needAdmin(): void {
    needUser('admin');
    if (user()['role'] !== 'admin') { http_response_code(403); fail('Administrator access is required.'); }
}
function rules(): array {
    return [
      'name'=>["~^[A-Za-z][A-Za-z .'-]{1,59}$~D", 'Use 2–60 letters, spaces, apostrophes, dots or hyphens.'],
      'email'=>['~^[A-Za-z0-9._%+-]+@example[.]test$~iD', 'Use a fictional address ending in @example.test.'],
      'student_code'=>['~^SC-[0-9]{4}$~D', 'Use SC- followed by four digits.'],
      'password'=>['#^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])[!-~]{8,64}$#D', 'Use 8–64 printable non-space ASCII characters with uppercase, lowercase and a number.'],
    ];
}
function cartLines(bool $lock = false): array {
    $cart = $_SESSION['cart'] ?? [];
    if (!$cart) return [];
    $marks = implode(',', array_fill(0, count($cart), '?'));
    $rows = query("SELECT m.*, r.name AS restaurant FROM menu_items m JOIN restaurants r ON r.id=m.restaurant_id
        WHERE m.id IN ($marks) ORDER BY m.id".($lock ? ' FOR UPDATE' : ''), array_keys($cart))->fetchAll();
    foreach ($rows as &$row) $row['qty'] = $cart[$row['id']];
    return $rows;
}
function total(array $rows): int {
    return array_sum(array_map(fn($r) => $r['price'] * $r['qty'], $rows));
}
function artNames(): array { return ['dosa','pizza','rice','noodles','burger','kulfi']; }
function artPath(string $art): string { return 'assets/'.(in_array($art,artNames(),true) ? $art : 'rice').'.svg'; }

