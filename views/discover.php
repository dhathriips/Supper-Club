<?php
$q=substr(field($_GET,'q'),0,120); $cuisine=field($_GET,'cuisine'); $sort=field($_GET,'sort');
$sorts=['featured'=>'Kitchen picks','rating'=>'Highest rated','fast'=>'Fastest first'];
if(!isset($sorts[$sort])) $sort='featured';
$like='%'.strtr($q,['!'=>'!!','%'=>'!%','_'=>'!_']).'%';
$rows=query("SELECT r.* FROM restaurants r WHERE (?='' OR cuisine=?) AND
    (r.name LIKE ? ESCAPE '!' OR r.cuisine LIKE ? ESCAPE '!' OR r.area LIKE ? ESCAPE '!' OR EXISTS
    (SELECT 1 FROM menu_items m WHERE m.restaurant_id=r.id AND m.name LIKE ? ESCAPE '!')) ORDER BY r.id",
    [$cuisine,$cuisine,$like,$like,$like,$like])->fetchAll();
if($sort==='rating') usort($rows,fn($a,$b)=>$b['rating']<=>$a['rating']);
if($sort==='fast') usort($rows,fn($a,$b)=>$a['minutes']<=>$b['minutes']);
heading('The Bengaluru index','Discover kitchens','Search the neighbourhood by kitchen, cuisine or dish.');
?>
<form class="filters" method="get"><input type="hidden" name="p" value="discover"><label>Search<input type="search" name="q" value="<?=e($q)?>" placeholder="Try dosa or Indiranagar"></label>
<label>Cuisine<select name="cuisine"><option value="">All cuisines</option><?php foreach(query('SELECT DISTINCT cuisine FROM restaurants ORDER BY cuisine') as $row): $c=$row['cuisine']; ?><option <?=($c===$cuisine?'selected':'')?>><?=e($c)?></option><?php endforeach ?></select></label>
<label>Sort by<select name="sort"><?php foreach($sorts as $key=>$label): ?><option value="<?=$key?>" <?=$sort===$key?'selected':''?>><?=$label?></option><?php endforeach ?></select></label><button>Apply</button><a href="?p=discover">Reset</a></form>
<div class="sectionhead"><h2><?=count($rows)?> <?=count($rows)===1?'kitchen':'kitchens'?></h2><span class="muted">Bengaluru, IN</span></div>
<?php if($rows) cards($rows); else emptyBox('No kitchens found','Try a broader search or clear the cuisine filter.','discover','Clear filters'); ?>

