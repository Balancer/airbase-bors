{include file="xfile:airbase/forum/forum.tpl"
	all_new=true
	with_images=true
}

<dl class="box">
<dt>Смотри также</dt>
<dd>
<ul>
<li><a href="http://www.balancer.ru/support/2010/12/t77158--stranitsa-s-obnovivshimisya-ranee-poseschyonnymi-temami.html">Обсуждение этой страницы на форуме</a></li>
</ul>
</dd>
</dl>

{if $answers_count}
{js_ready}
$("#pers_answ_cnt").html(" ({$answers_count})")
$("#pers_answ_cont > a").addClass("red")
{/js_ready}
{/if}