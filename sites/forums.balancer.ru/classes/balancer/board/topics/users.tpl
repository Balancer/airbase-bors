<table class="btab">
<tr>
	<th>&nbsp;</th>
	<th>Участник</th>
	<th>Число сообщений в теме</th>
	<th>Последний визит участника в теме</th>
	<th>Последний визит участника на форуме</th>
<tr>
{foreach $users as $u}
<tr>
	<td>

{$avatar_size=50}
{if $u->use_avatar()}
<div style="height: {$u->avatar_height($avatar_size)}px; margin: {math equation="max(0,floor((s-w)/2))" w=$u->avatar_width() s=$avatar_size}px 0">
<a href="{$u->url()}" class="avatar-image">
<img src="http://s.wrk.ru/a/{$u->use_avatar()}" width="{$u->avatar_width($avatar_size)}" height="{$u->avatar_height($avatar_size)}" alt="" />
{if $u->get('is_dead')}<span class="mourning-ribbon"></span>{/if}
</a>
</div>
{else}
<a href="{$u->url()}" class="avatar-image">
<img src="http://www.gravatar.com/avatar/{$u->email()|trim|bors_lower|md5}?d=wavatar" width="{$avatar_size}" height="{$avatar_size}" alt="" />
{if $u->get('is_dead')}<span class="mourning-ribbon"></span>{/if}
</a>
{/if}

	</td>
	<td>{$u->titled_link()}</td>
	<td><a href="http://www.balancer.ru/forum/user-{$u->id()}-posts-in-topic-{$this->id()}/">{$u->num_posts()}</a></td>
	<td>{$u->last_topic_visit()}
{if $u->is_dead()}<div style="color:black">участник мёртв</div>
	{elseif $u->is_deleted()}<div style="color:#999">аккаунт удалён</div>
	{elseif $u->is_admin_banned()}<div class="s" style="color:red">аккаунт забанен</div>
	{elseif $u->is_banned()}<div style="color:orange">аккаунт во временном R/O</div>
{/if}
	</td>
	<td>{$u->last_visit_time()|airbase_time}</td>
</tr>
{/foreach}
</table>
