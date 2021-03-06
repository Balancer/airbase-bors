{*
	переменные:
	$post - сам постинг
	$forum - форум, если нужно показать ссылку на него
	$no_show_answers = true, если не показывать ответы.
	$show_title = "заголовок", если нужна печать заголовка.
	$skip_author_name
	$skip_avatar_block
	$skip_date
	$skip_forums - не отображать название форума
	$skip_votes - не рисовать иконки в заголовке
	$skip_message_footer
	$strip - сколько резать символов.
*}

<a name="p{$post->id()}"></a>
{assign var="owner" value=$post->owner()}
{assign var=a value=$post->answer_to()}
<div class="post pby{$post->owner_id()}{if $a} pby{$a->owner_id()}{/if}{if $post->is_moderatorial()} moderatorial_post{/if}">
<div class="head">
<div class="to-left">
{if $post->is_moderatorial()}<div class="red small">Данное сообщение является официальным предупреждением</div>{/if}
{$post->flag()}
{if $show_title}{$show_title} {/if}
{if not $skip_author_name}{if $post->joke_owner()}{$post->joke_owner()->title()}{else}{$post->author_name()}{/if}{/if}
{if not $skip_date} <a href="{$post->url_for_igo()}">#{$post->create_time()|full_time}</a>{/if}
{if not $no_show_answers and $post->answer_to_id() and $a}&nbsp;<span class="answer">@{if $a->joke_owner()}{$a->joke_owner()->title()}{else}{$a->author_name()}{/if}<a href="{$a->url_in_container()}">#{$a->create_time()|full_time}</a></span>{/if}
</div>

{if not $skip_votes}
<div class="to-right">
	{if config('is_developer')}<div id="ab_rec_{$post->new_class_name()}__{$post->id()}" class="to-left ab_rec"><img src="/_bors/i16/recommendation.gif" alt="[!]" width="16" height="16" title="Рекомендовать это сообщение" /></div>{/if}
 {if $me}
 <div class="to-left">
  {$post->tools()->favorite_link_ajax()}
  {if $me->is_coordinator()}<div id="pttools_{$post->id()}" class="to-left"></div><img src="/_bors/i/tools.gif" alt="*" onclick="sh('pttools', {$post->id()})"  width="16" height="16" title="Инструменты координатора" />{/if}
 </div>
 {/if}
 <div class="to-left"><img src="/_bors/i/thumb_up.gif" alt="+" onclick="ptrch({$post->id()},'up')"  width="16" height="16" /></div>
 <div id="ptr{$post->id()}" class="to-left">{if $post->score() != NULL}{$post->score_colorized()}{/if}</div>
 <div class="to-left"><img src="/_bors/i/thumb_down.gif" alt="-" onclick="ptrch({$post->id()},'down')" width="16" height="16" /></div>
 <div class="half-transparent to-left">{$post->imaged_edit_url()}</div>
 {if $is_first_post}<g:plusone size="small"></g:plusone>{/if}
</div>
{/if}

<div class="clear">&nbsp;</div>
</div>{*/head*}

<div class="body">
{if $owner and not $skip_avatar_block}
{if not $avatar_size}{assign var="avatar_size" value=100}{/if}
<div class="avatar_block">{include file="xfile:forum/post-avatar.html" object=$p avatar_size=$avatar_size owner=$post->joke_owner()}</div>{/if}
{assign var="keyword_links" value=$p|get:'keyword_links'}
{if $keyword_links}<div class="post_kws">{$keyword_links}</div>{/if}
{if $topic}<small class="transgray">Тема: <a href="{$topic->url_ex('new')}">{$topic->title()} (переход к непрочитанному)</a></small><br/>{/if}
{if $forum and not $skip_forums}<small class="transgray">Форум: {$forum->titled_link()}</small><br/>{/if}
{if $strip}
{assign var="body" value=$post->body()}
{$body|strip_text:$strip}
{if strlen($body) >= $strip}
<br /><br /><a href="{$post->url_in_container()}">Дальше »»»</a>
{/if}
{else}
{if $post->is_spam() && $topic->forum_id() != 191 && not $show_spam}
 <div class="box">** Это сообщение от непроверенного пользователя и похоже на спам. После проверки его координаторами оно будет или уничтожено, или разрешено к показу **</div>
{else}
 {if $post->is_hidden()}
 <div class="box">** Это сообщение было скрыто координатором **</div>
 {else}
  {$post->body()}
 {/if}
{/if}
{/if}

{if $post->attaches()}
<hr />
Прикреплённые файлы:
{foreach from=$post->attaches() item="attach"}
<div class="box float_left center" style="width: 300px; ">{$attach->thumbnail_link('300x300')}<a href="{$attach->url()}">{$attach->title()}</a> [{$attach->size()|smart_size}, {$attach->downloads()} {$attach->downloads()|sklon:'загрузка':'загрузки':'загрузок'}]</div>
{/foreach}
{/if}

{if not $skip_message_footer}

<div class="postsignature">
{if $post->joke_owner() and $post->joke_owner()->signature()}{$post->joke_owner()->signature_html()}{/if}
<span class="img_middle">
&nbsp;{$post->owner_user_agent()}
</span>
</div>

{if $post->edited() && $post->edited() > $post->create_time() + 300}<div class="edited_note">
Это сообщение редактировалось {$post->edited()|strftime:"%d.%m.%Y в %H:%M"}
</div>{/if}

{if $post->warning()}<div class="warning_note">
<b>{$post->warning()|get:moderator_name}:</b> предупреждение ({$post->warning()|get:score})
{if $post->warning()|get:type_id} по категории «<b>{$post->warning()|get:type_id|bors_list_item_name:"airbase_user_warning_type"}</b>»
{if $post->warning()|get:source}(<i>{$post->warning()|get:source}</i>){/if}
{/if}.
</div>{/if}

<div id="pfo{$post->id()}"></div>

<ul class="postfooter">
<li><a href="#" onclick="pdsh('{$post->id()}'); return false;"><img src="/_bors/i/tools.gif" align="middle" alt="" />&nbsp;инструменты</a></li>
<li class="pages_select">
 <a href="{$forum->category()->category_base_full()}post.php?tid={$post->topic_id()}&amp;qid={$post->id()}" 
  class="current_page">Ответить на сообщение</a>
</li>
</ul>

{else}
<div class="clear">&nbsp;</div>
{/if}{*/signature show*}
</div>{*/body*}
</div>{*/post*}
