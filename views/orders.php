<?php heading('Your table','My orders','A receipt book for your simulated orders.');
$orders=query('SELECT * FROM orders WHERE user_id=? ORDER BY id DESC',[user()['id']])->fetchAll();
if(!$orders) {emptyBox('No orders yet','Your first mock receipt will appear here after checkout.'); return;}
foreach($orders as $o): ?>
<article class="panel order"><div class="between"><p class="eyebrow">Mock order #<?=$o['id']?></p><small><?=e($o['created_at'])?> · DB time</small></div><h2><?=e($o['restaurant_name'])?></h2>
<table><caption class="sr">Contents of order <?=$o['id']?></caption><thead><tr><th>Dish</th><th>Qty</th><th>Amount</th></tr></thead><tbody>
<?php foreach(query('SELECT * FROM order_items WHERE order_id=? ORDER BY id',[$o['id']]) as $line): ?><tr><td><?=e($line['item_name'])?></td><td><?=$line['qty']?></td><td><?=money($line['qty']*$line['price'])?></td></tr><?php endforeach ?>
</tbody><tfoot><tr><th colspan="2">Order total</th><td><?=money($o['total'])?></td></tr></tfoot></table></article><?php endforeach ?>

