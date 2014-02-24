<?php

define('BORS_CORE', '/var/www/bors/bors-core');
define('BORS_LOCAL', '/var/www/bors/bors-airbase');
include_once(BORS_CORE.'/init.php');

$entry_id = 9161;
config_set('is_debug', true);
config_set('lcml_cache_disable', true);

$entry = bors_load('bors_external_feeds_entry', $entry_id);
$entry->update_target(true);
echo $entry->target()->url_in_container()."\n";
