<?php
/***********************************************************************

  Copyright (C) 2002-2005  Rickard Andersson (rickard@punbb.org)

  This file is part of PunBB.

  PunBB is free software; you can redistribute it and/or modify it
  under the terms of the GNU General Public License as published
  by the Free Software Foundation; either version 2 of the License,
  or (at your option) any later version.

  PunBB is distributed in the hope that it will be useful, but
  WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 59 Temple Place, Suite 330, Boston,
  MA  02111-1307  USA

************************************************************************/

$GLOBALS['stat']['start_microtime'] = microtime(true);
$GLOBALS['stat']['start_time'] = time();

// Enable DEBUG mode by removing // from the following line
define('PUN_DEBUG', 1);

// This displays all executed queries in the page footer.
// DO NOT enable this in a production environment!
//define('PUN_SHOW_QUERIES', 1);

if (!defined('PUN_ROOT'))
{
	debug_trace();
	exit('The constant PUN_ROOT must be defined and point to a valid PunBB installation root directory.');
}

ini_set('include_path', ini_get('include_path') . ":/var/www/balancer.ru/htdocs/forum/punbb");

require_once('bors_config.php');

if(defined('PUN_ADMIN_CONSOLE'))
	if(bors_stop_bots('__nobots_testing', 'PUN_ADMIN_CONSOLE'))
		return;

// Load the functions script
require PUN_ROOT.'include/functions.php';

// Reverse the effect of register_globals
if (@ini_get('register_globals'))
	unregister_globals();

@include PUN_ROOT.'config.php';

// If PUN isn't defined, config.php is missing or corrupt
if (!defined('PUN'))
	exit('The file \'config.php\' doesn\'t exist or is corrupt. Please run <a href="install.php">install.php</a> to install PunBB first.');

// Record the start time (will be used to calculate the generation time for the page)
list($usec, $sec) = explode(' ', microtime());
$pun_start = ((float)$usec + (float)$sec);

$GLOBALS['main_uri'] = "http://".@$_SERVER[HTTP_HOST].@$_SERVER['REQUEST_URI'];

// Make sure PHP reports all errors except E_NOTICE. PunBB supports E_ALL, but a lot of scripts it may interact with, do not.
error_reporting(E_ALL ^ E_NOTICE);

// Turn off magic_quotes_runtime
@set_magic_quotes_runtime(0);

// Strip slashes from GET/POST/COOKIE (if magic_quotes_gpc is enabled)
if (get_magic_quotes_gpc())
{
	function stripslashes_array($array)
	{
		return is_array($array) ? array_map('stripslashes_array', $array) : stripslashes($array);
	}

	$_GET = stripslashes_array($_GET);
	$_POST = stripslashes_array($_POST);
	$_COOKIE = stripslashes_array($_COOKIE);
}

// Seed the random number generator
mt_srand((double)microtime()*1000000);

// Define a few commonly used constants
define('PUN_UNVERIFIED', 3);
define('PUN_ADMIN', 1);
define('PUN_MOD', 2);
define('PUN_GUEST', 3);
define('PUN_MEMBER', 4);

// Load DB abstraction layer and connect
require PUN_ROOT.'include/dblayer/common_db.php';

// Start a transaction
$db->start_transaction();

// Load cached config
include PUN_ROOT.'cache/cache_config.php';
$dir = dirname($_SERVER['PHP_SELF']);
if($dir == "/")
	$dir = "";
$pun_config['root_uri'] = $pun_config['o_base_url'] = "http://{$_SERVER['HTTP_HOST']}$dir";
$pun_config['root_dir'] = $_SERVER['DOCUMENT_ROOT']."$dir";

if (!defined('PUN_CONFIG_LOADED'))
{
	require PUN_ROOT.'include/cache.php';
	generate_config_cache();
	require PUN_ROOT.'cache/cache_config.php';
}

// Enable output buffering
if (!defined('PUN_DISABLE_BUFFERING'))
{
	// For some very odd reason, "Norton Internet Security" unsets this
	$_SERVER['HTTP_ACCEPT_ENCODING'] = isset($_SERVER['HTTP_ACCEPT_ENCODING']) ? $_SERVER['HTTP_ACCEPT_ENCODING'] : '';

	// Should we use gzip output compression?
	if ($pun_config['o_gzip'] && extension_loaded('zlib') && (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false || strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'deflate') !== false))
		ob_start('ob_gzhandler');
	else
		ob_start();
}


// Check/update/set cookie and fetch user info
$pun_user = array();
check_cookie($pun_user);

// Attempt to load the common language file
@include PUN_ROOT.'lang/'.$pun_user['language'].'/common.php';
if (!isset($lang_common))
	exit('There is no valid language pack \''.pun_htmlspecialchars($pun_user['language']).'\' installed. Please reinstall a language of that name.');

// Check if we are to display a maintenance message
if ($pun_config['o_maintenance'] && $pun_user['g_id'] > PUN_ADMIN && !defined('PUN_TURN_OFF_MAINT'))
	maintenance_message();

// Load cached bans
@include PUN_ROOT.'cache/cache_bans.php';
if (!defined('PUN_BANS_LOADED'))
{
	require_once PUN_ROOT.'include/cache.php';
	generate_bans_cache();
	require PUN_ROOT.'cache/cache_bans.php';
}

// Check if current user is banned
check_bans();

@define('WARNING_DAYS', 14);

$cms_db = new driver_mysql('punbb');
$warn_count	= intval($pun_user['warnings']);
$ban_expire = 0;

if($is_banned	= ($warn_count >= 10))
{
	$total = 0;
	foreach($cms_db->get_array("SELECT score, time FROM warnings WHERE user_id = ".intval($pun_user['id'])." ORDER BY time DESC LIMIT 20") as $w)
	{
		$total += $w['score'];
		if($total >= 10)
		{
			$ban_expire = $w['time'] + 3600;
			break;
		}
	}
}

$cat_ids = "";

require_once(PUN_ROOT.'tools/inc.php');

foreach($cms_db->get_array("SELECT * FROM categories ORDER BY parent, disp_position") as $r)
	if($r['base_uri'] && preg_match("!^{$r['base_uri']}!", $GLOBALS['main_uri']))
	{
		$cat_ids = punbb_get_all_subcategories(intval($r['id']));
		$cat_ids[] = intval($r['id']);
		break;
	}

if($cat_ids)
	$cat_ids = join(",", $cat_ids);
