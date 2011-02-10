<?php

//	#log 974565497|kron|Теперь к страницам можно добавлять фото в фотогалерею через web-интерфейс! Жмите внизу "Добавить в фотогалерею" и всё!  

function lst_log($txt)
{
	if(!($text = trim($txt)))
		return "";

	list($timestamp, $author, $text) = explode('|', $txt.'|||||');

	$txt = "<dl class=\"box\">
<dt>".date('d.m.Y H:i', $timestamp)."</dt>
<dd>
{$text}<br/><br/>
<small>// {$author}</small>
<div class=\"clear\">&nbsp;</div>
</dd>
</dl>";

	return lcml($txt);
}
