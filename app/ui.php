<?php
function icon(string $name): string {
    $paths=['pin'=>'<path d="M19 10c0 5-7 11-7 11S5 15 5 10a7 7 0 1 1 14 0Z"/><circle cx="12" cy="10" r="2"/>',
        'search'=>'<circle cx="10" cy="10" r="6"/><path d="m15 15 5 5"/>',
        'bag'=>'<path d="M4 8h16l-1 13H5Z M8 9V7a4 4 0 0 1 8 0v2"/>',
        'clock'=>'<circle cx="12" cy="12" r="9"/><path d="M12 6v6l4 2"/>'];
    return '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">'.$paths[$name].'</svg>';
}
function money($n): string { return '₹'.number_format((int)$n); }
function heading(string $kicker,string $title,string $subtitle=''): void { ?>
    <div class="heading"><p class="eyebrow"><?=e($kicker)?></p><h1><?=e($title)?></h1><?php if($subtitle): ?><p><?=e($subtitle)?></p><?php endif ?></div>
<?php }
function cards(array $rows): void { ?><div class="cards">
<?php foreach($rows as $r): ?><a class="card" href="?p=menu&amp;id=<?=(int)$r['id']?>">
    <div class="cover"><img src="<?=artPath($r['art'])?>" alt=""><span class="time"><?=icon('clock')?> <?=(int)$r['minutes']?> min</span></div>
    <div class="cardbody"><div class="between"><h3><?=e($r['name'])?></h3><span class="rating">★ <?=e($r['rating'])?></span></div>
    <p><?=e($r['cuisine'])?> · <?=e($r['area'])?></p></div></a><?php endforeach ?></div><?php }
function emptyBox(string $title,string $copy,string $page='discover',string $link='Explore kitchens'): void { ?>
    <div class="empty"><h2><?=e($title)?></h2><p><?=e($copy)?></p><a class="button" href="?p=<?=e($page)?>"><?=e($link)?> →</a></div><?php }

