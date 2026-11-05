<?php
function adminAction(string $op): void {
    $f = fn($key)=>field($_POST,$key);
    $id = $f('id'); $isNew = $id === '';
    if (!$isNew) number($id,1,2147483647,'Record ID');
    if ($op === 'admin-delete-restaurant' || $op === 'admin-delete-dish') {
        $stmt = $op === 'admin-delete-restaurant' ? 'DELETE FROM restaurants WHERE id=?' : 'DELETE FROM menu_items WHERE id=?';
        if (!query($stmt,[$id])->rowCount()) fail('Record no longer exists.');
        return;
    }
    $name = bounded($f('name'),80,'Name');
    if ($op === 'admin-restaurant') {
        $rating = $f('rating');
        if (!preg_match('/^[1-5](?:[.][0-9])?$/D',$rating) || (float)$rating > 5) fail('Rating must be between 1.0 and 5.0.');
        $art = $f('art'); if (!in_array($art,artNames(),true)) fail('Choose a cover illustration.');
        $args = [$name,bounded($f('cuisine'),40,'Cuisine'),bounded($f('area'),60,'Area'),$rating,
            number($f('minutes'),1,180,'Minutes'),$art,bounded($f('tagline'),160,'Tagline')];
        $sql = $isNew ? 'INSERT INTO restaurants(name,cuisine,area,rating,minutes,art,tagline) VALUES(?,?,?,?,?,?,?)' :
            'UPDATE restaurants SET name=?,cuisine=?,area=?,rating=?,minutes=?,art=?,tagline=? WHERE id=?';
    } elseif ($op === 'admin-dish') {
        $rid = $f('restaurant_id');
        if (!query('SELECT id FROM restaurants WHERE id=?',[$rid])->fetch()) fail('Choose an existing kitchen.');
        $args = [$name,bounded($f('description'),160,'Description'),number($f('price'),1,10000,'Price'),(int)isset($_POST['veg']),(int)isset($_POST['available'])];
        if ($isNew) { $args[]=$rid; $sql='INSERT INTO menu_items(name,description,price,veg,available,restaurant_id) VALUES(?,?,?,?,?,?)'; }
        else $sql='UPDATE menu_items SET name=?,description=?,price=?,veg=?,available=? WHERE id=? AND restaurant_id=?';
    } else fail('Unknown catalogue action.');
    if (!$isNew) {
        $args[]=$id;
        if ($op === 'admin-dish') $args[]=$rid;
    }
    query($sql,$args);
}

