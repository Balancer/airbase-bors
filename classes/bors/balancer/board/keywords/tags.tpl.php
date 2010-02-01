<?=$self->pages_links_nul()?>

<table class="btab">
<tr>
	<th>*</th>
	<th>Тема</th>
	<th>Создано</th>
	<th>Обновлено</th>
	<th>Информация</th>
</tr>
<?foreach($items as $x):?>
<tr>
  <td>*</td>
  <td>
	<big><?=bors_hypher($x->titled_url())?></big>
	<?if($x->total_pages() > 1) echo $x->pages_links_nul('pginlist', '', '', false)?>
	<?if($x->description()) echo "<small><br/>".bors_hypher($x->description())."</small>"?>
  </td>
  <td class="small nobr">
	<b><?=$x->author_name()?></b><br/>
	<?=full_time($x->create_time())?>
  </td>
  <td class="small nobr">
	<b><?=$x->last_poster_name()?></b><br/>
	<?=full_time($x->modify_time())?>
  </td>
  <td class="small">
  	Форум: <b><?=$x->forum()->titled_url()?></b><br/>
	Тэги: <?=airbase_keywords_linkify($x->keywords_string())?>
  </td>
</tr>
<?endforeach?>
</table>

<?=$self->pages_links_nul()?>
