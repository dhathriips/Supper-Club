<?php
// Reconcile items removed by an administrator before presenting the next quote.
$lines=cartLines(); $cart=$_SESSION['cart'] ?? [];
if(count($lines)!==count($cart)) {
    $_SESSION['cart']=array_intersect_key($cart,array_flip(array_column($lines,'id')));
    echo '<p class="notice">A removed menu item was taken out of your bag.</p>';
}
heading('Mock checkout','Your bag','All prices are in whole rupees. This classroom order stays on your computer.');
if(!$lines) { emptyBox('Your bag is empty','Find a kitchen and add a dish to start your tasting order.'); return; }
?>
<div class="split"><section class="panel"><p class="eyebrow">Your kitchen</p><h2><?=e($lines[0]['restaurant'])?></h2>
<?php foreach($lines as $m): ?><div class="bagrow"><div><h3><?=e($m['name'])?></h3><p><?=money($m['price'])?> each <?=$m['available']?'':' · Sold out'?></p></div>
<form class="inline" method="post"><?=act('quantity')?><input type="hidden" name="id" value="<?=$m['id']?>"><label>Quantity<input type="number" name="qty" min="0" max="10" value="<?=$m['qty']?>" required></label><button class="light">Update</button></form><strong><?=money($m['qty']*$m['price'])?></strong></div><?php endforeach ?>
<p class="muted">Set a quantity to 0 to remove that dish.</p><form method="post"><?=act('clear')?><button class="textbtn">Empty bag</button></form></section>
<aside class="panel receipt"><p class="eyebrow">Order summary</p><h2>Mock receipt</h2><div class="between"><span>Items</span><span><?=money(total($lines))?></span></div><div class="between"><span>Extra charges</span><span>₹0</span></div><hr><div class="between"><strong>Total</strong><strong><?=money(total($lines))?></strong></div><p>No payment or delivery details required.</p><form method="post"><?=act('checkout')?><input type="hidden" name="quoted" value="<?=total($lines)?>"><button><?=user()?'Place mock order':'Sign in to check out'?> →</button></form></aside></div>

