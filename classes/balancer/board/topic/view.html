<div class="pages_select right">
{if $topic->is_closed()}
	Тема закрыта
{else}
	<a href="{$topic->forum()->category()->category_base_full()}post.php?tid={$topic->id()}" 
		class="select_page" 
		onclick="return confirm('Внимание! Вы выбираете ответ не на конкретное сообщение (кнопкой «Ответить на сообщение»), а непосредственно в тему. В этом случае будет отсутствовать привязка ответа к отвечаемому сообщению. И, если Вы своим ответом отклоняетесь от основной темы обсуждения, то при возможном разделении темы создадите лишнюю работу модераторам. За что можете получить штрафной балл. Вы уверены, что хотите ответить именно в тему, без привязки к конкретному сообщению?');"
	>Ответ в эту тему</a>
{/if}
<a href="{$topic->forum()->category()->category_base_full()}post.php?fid={$topic->forum()|get:id}" class="select_page">Создать новую тему</a>
</div>

{$this->pages_links_nul()}

{if not $topic->is_public_access()}<div class="yellow_box">Тема с ограниченным доступом</div>{/if}

{foreach from=$posts item=p}
{module class="balancer_board_module_post" post=$p topic=$topic forum=$forum}
{/foreach}

{if $last_actions}
<dl class="box small">
<dt>Последние действия над темой</dt>
<dd><ul>
{foreach from=$last_actions item="a"}
<li{$a->type_class()} style="font-size: 8pt">{$a->owner()->title()} [{$a->create_time()|full_time}]: {$a->message()}</li>
{/foreach}
</ul></dd>
</dl>
{/if}

{$this->pages_links_nul()}
