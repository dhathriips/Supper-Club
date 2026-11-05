<?php $r=query('SELECT * FROM restaurants WHERE id=?',[field($_GET,'id')])->fetch();
if(!$r) {http_response_code(404);fail('Kitchen not found.');}
$items=query('SELECT * FROM menu_items WHERE restaurant_id=? ORDER BY id',[$r['id']])->fetchAll(); ?>
<a class="back" href="?p=discover">← All kitchens</a><section class="menuhero"><div><p class="eyebrow"><?=e($r['cuisine'])?> kitchen</p><h1><?=e($r['name'])?></h1><p><?=e($r['tagline'])?></p>
<div class="tags"><span class="rating">★ <?=e($r['rating'])?></span><span><?=e($r['area'])?></span><span><?=e($r['minutes'])?> min</span></div></div><img src="<?=artPath($r['art'])?>" alt=""></section>
<div class="sectionhead"><h2>On the menu</h2><a class="arrow" href="?p=cart">View bag →</a></div>
<label class="quick">Filter dishes as you type<input type="search" id="dish-filter" placeholder="Type a dish or ingredient"></label><p id="dish-count" aria-live="polite"><?=count($items)?> dishes</p>
<div class="rows"><?php foreach($items as $m): ?><article class="row dish" data-search="<?=e($m['name'].' '.$m['description'])?>"><div><p class="eyebrow"><?=$m['veg']?'Vegetarian':'Non-vegetarian'?></p><h3><?=e($m['name'])?></h3><p><?=e($m['description'])?></p><strong><?=money($m['price'])?></strong></div><form method="post"><?=act('add')?><input type="hidden" name="id" value="<?=$m['id']?>"><button class="light" <?=$m['available']?'':'disabled'?>><?=$m['available']?'+ Add':'Sold out'?></button></form></article><?php endforeach ?></div>

