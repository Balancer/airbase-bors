<h2 style="color: red">Сайт находится на реконструкции</h2>

<h3>Смотри также</h3>
<ul>
<li><a href="http://www.balancer.ru/forum/">Форумы</a></li>
<li><a href="http://navy.balancer.ru/">navy.balancer.ru</a> — флот и судомоделирование</li>
<li><a href="/club/">Клуб</a> — обо всём понемногу</li>
<li><a href="https://plus.google.com/111660082035127222203" rel="publisher">Авиабаза в Google+</a></li>
</ul>

{* module class="forum_blog" skip_forums="1,4,7,10-13,15,17,19,22,25-27,30,34,35-37,39,44,45,47,52,54,55,60,73,78-81,86,88,91,92,98-103,138,162-163,165,167,168,170-171,173,176,177,180,182-185,190-192,194-197,202-204,206-209,212" *}

<h2>Новости</h2>
{foreach $news as $x}
<div class="airbase-news">
<h2><a href="{$x.url}">{$x.title}</a></h2>
{$x.content}
</div>
{/foreach}
