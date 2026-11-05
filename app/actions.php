<?php
function action(string $page): void {
    if (!hash_equals($_SESSION['csrf'], field($_POST,'csrf'))) {
        http_response_code(403); fail('This form expired. Reload the page and try again.');
    }
    $get = fn($key) => field($_POST,$key);
    $op = $get('action');
    if ($op === 'register') {
        $data = [];
        foreach (rules() as $key => [$regex,$message]) {
            $data[$key] = $key === 'password' ? ($_POST[$key] ?? '') : $get($key);
            if (!is_string($data[$key]) || !preg_match($regex,$data[$key])) fail($message);
        }
        if (strlen($data['email']) > 120 || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) fail('Enter a valid classroom email.');
        try {
            query('INSERT INTO users(name,email,student_code,password_hash) VALUES(?,?,?,?)',
                [$data['name'],strtolower($data['email']),$data['student_code'],password_hash($data['password'],PASSWORD_DEFAULT)]);
        } catch (PDOException $ex) {
            if (($ex->errorInfo[1] ?? 0) == 1062) fail('That demo email or student code is already registered.');
            throw $ex;
        }
        flash('Your classroom account is ready. Sign in to continue.'); go('login');
    }
    if ($op === 'login') {
        $u = query('SELECT * FROM users WHERE email=?',[strtolower($get('email'))])->fetch();
        $pw = $_POST['password'] ?? '';
        if (!$u || !is_string($pw) || !password_verify($pw,$u['password_hash'])) fail('The demo email or password did not match.');
        unset($u['password_hash']); session_regenerate_id(true);
        $_SESSION['user'] = $u; $_SESSION['csrf'] = bin2hex(random_bytes(32));
        $next = $_SESSION['next'] ?? 'discover'; unset($_SESSION['next']); go($next);
    }
    if ($op === 'logout') {
        $_SESSION = []; setcookie(session_name(),'', ['expires'=>time()-3600,'path'=>'/','httponly'=>true,'samesite'=>'Lax']);
        session_destroy(); go('home');
    }
    if ($op === 'add') {
        $row = query('SELECT * FROM menu_items WHERE id=?',[$get('id')])->fetch();
        if (!$row || !$row['available']) fail('That dish is unavailable.');
        $lines = cartLines();
        if ($lines && $lines[0]['restaurant_id'] != $row['restaurant_id']) fail('Empty your current bag before adding dishes from another kitchen.');
        $id = (int)$row['id']; $qty = ($_SESSION['cart'][$id] ?? 0) + 1;
        if ($qty > 10) fail('Each dish is limited to 10 portions.');
        $_SESSION['cart'][$id] = $qty; flash($row['name'].' added to your bag.'); go('menu',['id'=>$row['restaurant_id']]);
    }
    if ($op === 'quantity') {
        $id = $get('id'); $qty = number($get('qty'),0,10,'Quantity');
        if (!isset($_SESSION['cart'][$id])) fail('That dish is no longer in your bag.');
        if ($qty === 0) unset($_SESSION['cart'][$id]); else $_SESSION['cart'][$id]=$qty;
        go('cart');
    }
    if ($op === 'clear') { unset($_SESSION['cart']); go('cart'); }
    if ($op === 'checkout') {
        needUser('cart'); db()->beginTransaction();
        try {
            $rows = cartLines(true);
            if (!$rows || count($rows) !== count($_SESSION['cart'])) fail('The bag changed. Review your bag before ordering.');
            foreach ($rows as $r) if (!$r['available'] || $r['restaurant_id'] != $rows[0]['restaurant_id']) fail('One or more dishes cannot be ordered. Update your bag.');
            $sum = total($rows);
            if ($sum !== number($get('quoted'),1,PHP_INT_MAX,'Bag total')) fail('Prices changed. Review the new total before confirming.');
            query('INSERT INTO orders(user_id,restaurant_id,restaurant_name,total) VALUES(?,?,?,?)',
                [user()['id'],$rows[0]['restaurant_id'],$rows[0]['restaurant'],$sum]);
            $order = db()->lastInsertId();
            foreach ($rows as $r) query('INSERT INTO order_items(order_id,menu_item_id,item_name,price,qty) VALUES(?,?,?,?,?)',
                [$order,$r['id'],$r['name'],$r['price'],$r['qty']]);
            db()->commit();
        } catch (Throwable $ex) { if (db()->inTransaction()) db()->rollBack(); throw $ex; }
        unset($_SESSION['cart']); flash("Mock order #$order confirmed."); go('orders');
    }
    if (in_array($op,['note-add','note-edit','note-delete'],true)) {
        needUser('feedback'); feedback(substr($op,5),$get('id'),$get('body')); flash('Tasting notes updated.'); go('feedback');
    }
    if (str_starts_with($op,'admin-')) {
        needAdmin(); adminAction($op); flash('Catalogue updated.'); go('admin');
    }
    fail('Unknown form action.');
}

