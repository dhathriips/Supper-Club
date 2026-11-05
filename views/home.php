<?php $picks=query('SELECT * FROM restaurants ORDER BY id LIMIT 3')->fetchAll(); ?>
<section class="hero"><div class="hero-copy"><p class="eyebrow"><span class="dot"></span> Bengaluru food guide</p><h1>Good food,<br><i>close by.</i></h1>
<p>Find your next favourite kitchen in Bengaluru.</p>
<form class="searchbar" method="get"><input type="hidden" name="p" value="discover"><span class="city"><?=icon('pin')?> Bengaluru</span><?=icon('search')?><input aria-label="Restaurant, cuisine or dish" name="q" placeholder="Restaurant, cuisine or dish"><button>Search</button></form></div>
<div class="hero-art" aria-hidden="true"><span class="plate-no">THE TASTING SERIES &nbsp; / &nbsp; 01</span><img src="assets/hero.svg" alt=""><span class="caption">Fig. 01 &nbsp; • &nbsp; Benne dosa</span></div></section>
<section><div class="sectionhead"><div><p class="eyebrow">From the neighbourhood</p><h2>Kitchen picks</h2></div><a class="arrow" href="?p=discover">All kitchens →</a></div>
<?php cards($picks); ?></section><section class="browse"><p class="eyebrow">Choose a craving</p><div class="chips"><?php foreach(query('SELECT DISTINCT cuisine FROM restaurants ORDER BY cuisine')->fetchAll() as $c): ?><a href="?p=discover&amp;cuisine=<?=urlencode($c['cuisine'])?>"><?=e($c['cuisine'])?> ↗</a><?php endforeach ?></div></section>

