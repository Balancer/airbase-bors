{if $best_of_year}
<dl class="box small">
<dt>Лучшие {$best_of_year|count} сообщени{$best_of_year|count|sklon:'е,я,й'} за последний год</dt>
<dd>
	{module class="balancer_board_modules_dated" items=$best_of_year}
</dd>
</dl>
{/if}

{if $best_all}
<dl class="box small">
<dt>Лучшие {$best_all|count} сообщений {$best_all|count|sklon:'е я й'} за всю историю</dt>
<dd>
	{module class="balancer_board_modules_dated" items=$best_all}
</dd>
</dl>
{/if}

{if $worst_of_year}
<dl class="box small">
<dt>Худшие {$worst_of_year|count} сообщени{$worst_of_year|count|sklon:'е,я,й'} за последний год</dt>
<dd>
	{module class="balancer_board_modules_dated" items=$worst_of_year}
</dd>
</dl>
{/if}

{if $worst_all}
<dl class="box small">
<dt>Худшие {$worst_all|count} сообщений {$worst_all|count|sklon:'е я й'} за всю историю</dt>
<dd>
	{module class="balancer_board_modules_dated" items=$worst_all}
</dd>
</dl>
{/if}
