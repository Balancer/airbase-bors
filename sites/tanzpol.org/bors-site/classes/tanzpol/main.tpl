<div class="alert"><i class="icon-info-sign"></i> Сайт находится в процессе создания и переноса. Пока — только рыба</div>

<h3>Смотри также</h3>
<ul>
<li><a href="http://balancer.ru/forum/">Форумы Balancer'а</a></li>
<li><a href="http://club.balancer.ru/">Клуб Авиабазы</a></li>
<li><a href="http://ls.balancer.ru/">Блоги Авиабазы</a></li>
<li><a href="http://airbase.ru/">airbase.ru</a> — Авиабаза</li>
<li><a href="http://navy.balancer.ru/">navy.balancer.ru</a> — флот и судомоделирование</li>
</ul>

{* module class="forum_blog" cat_names=$this->cat_names() *}
{* module class="balancer_board_module_blog" forum_ids="10,78,80,101,102,173,180,181,194,195,202,206-208" *}
{* module class="balancer_board_module_blog" cat_names=$this->cat_names() blog_body_template='bootstrap' *}

<style>
.tanz-news {
    margin: 0 0 8px 0;
    padding: 8px;

}

.clear, .tanz-news:after {
   content: ".";
   display: block;
   clear: both;
   visibility: hidden;
   height: 0;
}

.tanz-news h2 {
    box-shadow: 0 0 8px rgba(0,0,0,0.5);
    border-radius: 4px;
    background-color: #2489DB;
    margin: 0 0 8px 0;
    padding: 4px;
}

.tanz-news h2 a {
    color: white;
}

.tanz-news h3 {
    margin: 0 0 8px 0;
    padding: 0;
}

.round_box {
    margin: 0 0 8px 0;
    box-shadow: 0 0 8px rgba(0,0,0,0.5);
    border-radius: 4px;
    padding: 4px;
}

.float_left {
    float: left;
    margin: 0 8px 0 0;
}
</style>

{foreach $news as $x}
<div class="tanz-news">
<h2><a href="{$x.url}">{$x.title}</a></h2>
{$x.content}
</div>
{/foreach}
