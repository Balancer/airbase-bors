<?php
function bors_punbb_end($content)
{
	$dbt = debug_backtrace();

	$data = array(
		'user_ip' => $_SERVER['REMOTE_ADDR'],
		'user_id' => bors()->user_id(),
		'server_uri' => 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'],
		'referer' => @$_SERVER['HTTP_REFERER'],
		'access_time' => round($GLOBALS['stat']['start_microtime']),
		'operation_time' =>  str_replace(',', '.', microtime(true) - $GLOBALS['stat']['start_microtime']),
		'user_agent' => @$_SERVER['HTTP_USER_AGENT'],
		'is_bot' => bors()->client()->is_bot(),
		'object_class_name' => str_replace($_SERVER['DOCUMENT_ROOT'], '/', $dbt[count($dbt)-1]['file']),
		'object_id' => @$_SERVER['QUERY_STRING'],
	);

	$x = object_new_instance('bors_access_log', $data);
	$x->store();

	if(!config('is_developer'))
		return $content;

    $time = microtime(true) - $GLOBALS['stat']['start_microtime'];

	$deb = "<!--\n=== debug-info ===\n"
		."created = ".date('r')."\n";

	$deb .= "\n=== debug counting: ===\n";
	$deb .= debug_count_info_all();

	$deb .= "\n=== debug timing: ===\n";
	$deb .= debug_timing_info_all();
	$deb .= "Total time: $time sec.\n";
	$deb .= "-->\n";

	$content = str_replace('</body>', $deb.'</body>', $content);

	return $content;
}
