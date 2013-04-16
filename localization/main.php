<?php
	@include_once("localization/russian-{$GLOBALS['cms']['charset_u']}.php");

	function ec($txt)
	{
		return iconv('utf-8', "{$GLOBALS['cms']['charset']}//translit", $txt);
	}

	function dc($txt)
	{
		return iconv("{$GLOBALS['cms']['charset']}", 'utf-8', $txt);
	}
