<?php heading('Staff access','Catalogue desk','Manage local kitchens, menu prices and dish availability.');
$restaurants=query('SELECT * FROM restaurants ORDER BY id')->fetchAll();
function adminInput(string $label,string $key,array $r,int $max): void { ?><label><?=e($label)?><input name="<?=e($key)?>" maxlength="<?=$max?>" value="<?=e($r[$key] ?? '')?>" required></label><?php }
function restaurantForm(array $r=[]): void { ?>
<form class="formstack" method="post"><?=act('admin-restaurant')?><input type="hidden" name="id" value="<?=e($r['id'] ?? '')?>">
<div class="fields"><?php foreach([['Name','name',80],['Cuisine','cuisine',40],['Area','area',60],['Tagline','tagline',160]] as [$label,$key,$max]) adminInput($label,$key,$r,$max); ?>
<label>Rating<input name="rating" type="number" min="1" max="5" step=".1" value="<?=e($r['rating'] ?? '4.0')?>" required></label>
<label>Minutes<input name="minutes" type="number" min="1" max="180" value="<?=e($r['minutes'] ?? '25')?>" required></label>
<label>Cover<select name="art"><?php foreach(artNames() as $a): ?><option <?=($r['art'] ?? '')===$a?'selected':''?>><?=$a?></option><?php endforeach ?></select></label></div><button>Save kitchen</button></form><?php }
function dishForm($rid,array $d=[]): void { ?>
<form class="formstack" method="post"><?=act('admin-dish')?><input type="hidden" name="id" value="<?=e($d['id'] ?? '')?>"><input type="hidden" name="restaurant_id" value="<?=e($rid)?>">
<div class="fields"><?php adminInput('Dish name','name',$d,80); adminInput('Description','description',$d,160); ?>
<label>Price in rupees<input type="number" min="1" max="10000" name="price" value="<?=e($d['price'] ?? '')?>" required></label></div>
<div class="inline"><label><input type="checkbox" name="veg" <?=($d['veg'] ?? 1)?'checked':''?>> Vegetarian</label><label><input type="checkbox" name="available" <?=($d['available'] ?? 1)?'checked':''?>> Available</label></div><button>Save dish</button></form><?php }
function deleteForm(string $type,int $id): void { ?>
<form method="post"><?=act('admin-delete-'.$type)?><input type="hidden" name="id" value="<?=$id?>"><button class="textbtn" data-confirm="Delete this <?=$type?><?=$type==='restaurant'?' and its entire menu':''?>?">Delete <?=$type?></button></form><?php } ?>
<details class="panel"><summary>＋ Add a kitchen</summary><?php restaurantForm(); ?></details>
<?php foreach($restaurants as $r): ?><section class="panel"><p class="eyebrow"><?=e($r['cuisine'])?> / <?=e($r['area'])?></p><h2><?=e($r['name'])?></h2>
<details><summary>Edit kitchen</summary><?php restaurantForm($r); deleteForm('restaurant',$r['id']); ?></details>
<details><summary>Manage dishes</summary><?php foreach(query('SELECT * FROM menu_items WHERE restaurant_id=? ORDER BY id',[$r['id']]) as $d): ?><details><summary><?=e($d['name'])?> · <?=money($d['price'])?><?=$d['available']?'':' · Sold out'?></summary><?php dishForm($r['id'],$d); deleteForm('dish',$d['id']); ?></details><?php endforeach ?><details><summary>＋ Add dish</summary><?php dishForm($r['id']); ?></details></details></section><?php endforeach ?>

