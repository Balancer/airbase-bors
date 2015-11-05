<h2>Баланс за неделю: {$this->balance()}</h2>
(не считая ежесуточной автоматической прибыли)

<h2>Суммарная статистика за неделю</h2>
<table class="btab">
<tr>
	<th>Операция</th>
	<th>Сумма</th>
	<th>Количество операций</th>
	<th>Цена операции</th>
</tr>
{foreach $this->mstat() as $x}
<tr>
	<td>{$x->comment()}</td>
	<td>{$x->sum()}</td>
	<td>{$x->total()}</td>
	<td>{$x->amount()}</td>
</tr>
{/foreach}
</table>

<h2>Развёрнутый лог действий за неделю</h2>
<table class="btab">
<tr>
	<th>Дата операции</th>
	<th>Операция</th>
	<th>Приход</th>
	<th>Остаток</th>
	<th>Объект операции</th>
</tr>
{foreach $this->mlog() as $x}
<tr>
	<td>{$x->ctime()}</td>
	<td>{$x->comment()}</td>
	<td>{$x->amount()}</td>
	<td>{$x->result()}</td>
	<td>{object_property($x->target(), 'titled_link')}</td>
</tr>
{/foreach}
</table>

<br/>

<div class="alert alert-info">
<ul>
<li><a href="http://forums.balancer.ru/money/">Операции (переводы и поощрения)</a></li>
<li><a href="http://www.wrk.ru/support/2015/04/t91415,new--u-e-aviabazy-solnyshki.html">Обсуждение на форуме</a></li>
</ul>
</div>
