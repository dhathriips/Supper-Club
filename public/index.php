<?php
declare(strict_types=1);
require __DIR__.'/../app/core.php';
require __DIR__.'/../app/feedback.php';
require __DIR__.'/../app/admin.php';
require __DIR__.'/../app/actions.php';
require __DIR__.'/../app/ui.php';
ini_set('display_errors','0');
ini_set('session.use_strict_mode','1');
session_name('supper_session');
$secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
session_set_cookie_params(['path'=>'/','httponly'=>true,'secure'=>$secure,'samesite'=>'Lax']);
session_start();
$_SESSION['csrf'] ??= bin2hex(random_bytes(32));
if (!isset($_SESSION['started'])) {
    $_SESSION['started']=true;
    $last = filter_var($_COOKIE['supper_visit'] ?? null,FILTER_VALIDATE_INT);
    $_SESSION['previous'] = $last && $last > 0 && $last <= time() ? date('d M Y, H:i',$last).' IST' : 'First visit';
    setcookie('supper_visit',(string)time(),['expires'=>time()+2592000,'path'=>'/','httponly'=>true,'secure'=>$secure,'samesite'=>'Lax']);
}
header("Content-Security-Policy: default-src 'self'; object-src 'none'; base-uri 'none'; frame-ancestors 'none'; form-action 'self'");
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store');
$pages = ['home'=>'City guide','discover'=>'Discover','menu'=>'Kitchen menu','cart'=>'Your bag','login'=>'Sign in','register'=>'Join the club','orders'=>'My orders','feedback'=>'Tasting notes','lab'=>'Interaction lab','admin'=>'Catalogue desk'];
$page = field($_GET,'p') ?: 'home';
$error = ''; $message = $_SESSION['message'] ?? ''; unset($_SESSION['message']);
ob_start();
try {
    if (!isset($pages[$page])) { http_response_code(404); fail('This page does not exist.'); }
    if ($page === 'admin') needAdmin();
    if (in_array($page,['orders','feedback'],true)) needUser($page);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try { action($page); } catch (DomainException $ex) { $error=$ex->getMessage(); }
    }
    require __DIR__.'/../views/'.(in_array($page,['login','register'],true) ? 'auth' : $page).'.php';
    $body = ob_get_clean();
} catch (DomainException $ex) {
    ob_end_clean(); $error=$ex->getMessage(); $body='<p><a href="?p=home">Return to the city guide →</a></p>';
} catch (Throwable $ex) {
    ob_end_clean(); error_log((string)$ex); http_response_code(500);
    $error='The local service could not finish this request. Check the database, schema import and storage permissions in README.md.';
    $body='';
}
require __DIR__.'/../views/layout.php';

