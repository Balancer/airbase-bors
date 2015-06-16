<form action="/images/similar/" method="get">
Введите ссылку с изображением: <input name="url" value="{$this->get_url()}" size="100" />
<input type="submit" value="Найти похожие" />
</form>

{if $image}
<h2>Исходное изображение:</h2>
{$image->thumbnail('640x640')->html_code()}
<ul>
		{foreach $image->find_containers() as $x}
	<li>{$x->titled_link_in_container()}</li>
		{/foreach}
</ul>

{/if}

{if $similar_images}
<h2>Похожие изображения:</h2>
	{foreach $similar_images as $i}
		{$posts=$i->find_containers()}
			{if $posts}
<div style="width: 300px; float: left;" class="rs_box">
				{$i->thumbnail('300x300')->html_code()}
	<ul>
				{foreach $posts as $x}
		<li><a href="{$x->url_for_igo()}">{$x->title()}</a></li>
				{/foreach}
	</ul>
</div>
			{/if}
	{/foreach}
<div class="clear">&nbsp;</div>

{elseif $image}
<h2>Похожие изображения не найдены</h2>
{/if}

<dl class="box w100p">
<dt>Ссылки</dt>
<dd>
	<ul>
		<li><a href="http://www.balancer.ru/g/p3455319">Обсуждение поиска картинок на форуме</a></li>
	</ul>
</dd>
</dl>
