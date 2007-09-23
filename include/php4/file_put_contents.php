<?php

function file_put_contents($file, $content)
{
	$fh = fopen($file, "wb");
	fwrite($fh, $content);
	fclose($fh);
	return $content;
}