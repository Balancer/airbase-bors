<?php

function lcml_external_code_ab($text)
{
	$text = preg_replace('!<script[^>]+src="[^"]+forvo\.com[^"]+id=(\d+)"></script>!', '[forvo]$1[/forvo]', $text);

	return $text;
}
