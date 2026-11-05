<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?=e($pages[$page] ?? 'Page')?> · Supper Club</title><link rel="icon" href="assets/mark.svg"><link rel="stylesheet" href="assets/style.css"><script defer src="assets/app.js"></script></head>
<body><a class="skip" href="#main">Skip to content</a><header><div class="wrap bar">
<a class="brand" href="?p=home"><img src="assets/mark.svg" alt="">supper club<span>.</span></a>
<nav aria-label="Main"><a href="?p=discover">Discover</a><a href="?p=orders">My orders</a>
<?php if(user()): ?><form method="post"><?=act('logout')?><button class="textbtn">Sign out</button></form><?php else: ?><a href="?p=login">Sign in</a><?php endif ?>
<a class="baglink" href="?p=cart"><?=icon('bag')?> Bag <b><?=array_sum($_SESSION['cart'] ?? [])?></b></a></nav></div></header>
<main class="wrap" id="main">
<?php if($message): ?><p class="notice" role="status"><?=e($message)?></p><?php endif ?>
<?php if($error): ?><p class="notice error" role="alert"><?=e($error)?></p><?php endif ?>
<?=$body?></main><footer><div class="wrap foot"><div><strong>Supper Club.</strong><p>Independent classroom food guide · Bengaluru</p><small>Previous visit · <?=e($_SESSION['previous'])?></small></div>
<nav aria-label="Resources"><a href="?p=feedback">Tasting notes</a><a href="?p=lab">Interaction lab</a><?php if((user()['role'] ?? '')==='admin'): ?><a href="?p=admin">Catalogue desk</a><?php endif ?><a href="?p=register">Join the club</a></nav></div></footer></body></html>

