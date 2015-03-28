<div class="container-fluid">
	<div class="row-fluid">
		<div class="span9">

{$pagination}

{foreach $this->posts() as $p}
{if $use_bootstrap}
	{$p->view()->template('bootstrap')->html()}
{else}
	{$topic=$p->topic()}
	{$forum=$topic->forum()}
	{module class="balancer_board_module_post"
		post=$p
		topic=$topic
		forum=$forum}
{/if}
{/foreach}

{if $this->posts()}
{$pagination}
{/if}

		</div>
	</div>
</div>

<dl class="box">
<dd>
<a href="http://www.wrk.ru/support/2014/05/t69496--otvety-na-vashi-soobscheniya.1289.html">Обсуждение страницы</a>
</dd>
</dl>
