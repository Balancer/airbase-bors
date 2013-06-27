{extends file="xfile:bootstrap/index.tpl"}

{block name="configure"}
	{assign var="use_system_menu" value=true}
{/block}

{block name="system_menu"}
<div class="well sidebar-nav">
	<ul class="nav nav-list">
		<li class="nav-header">Разделы</li>
		<li class="active"><a href="/hangar/">Ангар</a></li>
		<li><a href="#">Link</a></li>
		<li><a href="#">Link</a></li>
		<li><a href="#">Link</a></li>
	</ul>
</div>
{/block}
