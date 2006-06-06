<?
error_reporting(E_ALL);

require_once ("DataBase.php");
require_once ("global-data.php");

class DataBaseHTS
{
	var $dbh;
	var $uri;

	function DataBaseHTS($uri = NULL)
	{
		$this->uri = $uri;
		$this->dbh = new DataBase();
		if (!$this->dbh)
			exit (__FILE__.__LINE." Can't create DataBase class");
	}

	function Destroy()
	{
		$this->dbh->Destroy();
	}

	function normalize_uri($uri, $base_page = '')
	{
		if ($base_page)
		{
			if (preg_match("!^http://!", $uri))
				return $this->normalize_uri($uri);

			if ($uri {
				0 }
			!= '/')
			$uri = $base_page.$uri;

			return $this->normalize_uri("http://{$_SERVER['HTTP_HOST']}$uri");
		}

		$orig_uri = $uri;

		list ($uri, $params) = split("\?", $uri.'?');

		if (is_global_key('normalize_uri', $uri))
			return global_key('normalize_uri', $uri);

		// Удаляем двойные слеши в адресе (кроме http://)
		$uri = preg_replace("!([^:])//+!", "$1/", $uri);

		// Если последний символ не '/' и оканчивается не на имя файла
		if (substr($uri, -1) != '/')
			if (preg_match("!^.*/[^\\.\\\\]+?$!", $uri))
				$uri .= '/';

		if (isset ($GLOBALS['DOCUMENT_ROOT']))
			$uri = preg_replace("!^{$GLOBALS['DOCUMENT_ROOT']}/+!", "/", $uri);

		if (substr($uri, 0, 1) == '/')
			$uri = 'http://'.$_SERVER['HTTP_HOST'].$uri;

		$uri = preg_replace("!/index\.\w+$!", "/", $uri);
		$uri = preg_replace("!(/\w+)\.(phtml|hts)$!", "$1.php", $uri);
		$uri = preg_replace("!^http://www\.!i", "http://", $uri);

		$save_log_level = isset ($GLOBALS['log_level']) ? $GLOBALS['log_level'] : NULL;
		$GLOBALS['log_level'] = 0;

		$m = array ();
		if (preg_match("!^http://([^/]+)(/.*?)([^/]*)$!", $uri, $m))
		{
			$host = $m[1];
			$path = $m[2];
			$file = $m[3];

			if ($to_host = $this->dbh->get_value('hts_host_redirect', 'from', $host, 'to'))
				$host = preg_replace("!^$host$!i", "$to_host", $host);

			$uri = "http://$host$path$file";
		}

		while ($alias = $this->dbh->get_value('hts_aliases', 'alias', $uri, 'uri'))
			$uri = $alias;

		$GLOBALS['log_level'] = $save_log_level;

		echolog("Normalize uri '$orig_uri' to '$uri'");

		return set_global_key('normalize_uri', $uri, $uri);
	}

	function clear_data_cache($uri, $key, $default = NULL, $inherit = false, $skip = false)
	{
		clear_global_key("uri_data($uri,$inherit,$skip)", $key);
	}

	function pre_data_check($uri, $key)
	{
		if (!empty ($_GET['debug']))
			echo "<small>pre_data_check('$uri', '$key')</small><br/>\n";

		if (empty ($GLOBALS['cms']['data_prehandler'][$key]))
			return false;

		$m = array ();
		foreach ($GLOBALS['cms']['data_prehandler'][$key] as $regexp => $func)
			if (preg_match($regexp, $uri, $m))
				if (($res = $func ($uri, $m)) != NULL)
				{
					//						"Got pre $res for $key/$uri";
					return $res;
				}
		return false;
	}

	function post_data_check($uri, $key)
	{
		if (empty ($GLOBALS['cms']['data_posthandler'][$key]))
			return false;

		$m = array ();
		foreach ($GLOBALS['cms']['data_posthandler'][$key] as $regexp => $func)
		{
			//				echo "Check post_data_check($uri, $key) for $regexp<br/>\n";
			if (preg_match($regexp, $uri, $m))
			{
				if (($res = $func ($uri, $m)) != NULL)
				{
					//						"Got post $res for $key/$uri";
					return $res;
				}
			}
		}
		return false;
	}

	function get($key)
	{
		return $this->get_data($this->uri, $key);
	}

	function get_data($uri, $key, $default = NULL, $inherit = false, $skip = false, $fields = '`value`', $search = '`id`')
	{
		$m = array ();
		if (preg_match("!^raw:(.+)$!", $key, $m))
		{
			$key = $m[1];
			$raw = true;
		}
		else
			$raw = false;

		//			echo("Get key '$key' for '$uri'");

		$uri = $this->normalize_uri($uri);

		if (!$raw && isset ($GLOBALS['page_data_preset'][$key][$uri]))
			return $GLOBALS['page_data_preset'][$key][$uri];

		$skip_save = $skip;

		if (!$fields && !$search && !$skip && is_global_key("uri_data($uri)", $key)) // && global_key("uri_data($uri,$inherit,$skip_save)",$key)) 
			return global_key("uri_data($uri)", $key);

		if (!$raw && ($res = $this->pre_data_check($uri, $key)) !== false)
			return $res;

		if (!$raw && isset ($GLOBALS['page_data_preset'][$key][$uri]))
			return $GLOBALS['page_data_preset'][$key][$uri];

		$key_table_name = $this->create_data_table($key);

		$loops = 0;

		do
		{
			if (!$skip && $val = $this->dbh->get("SELECT $fields FROM `$key_table_name` WHERE $search='".addslashes($uri)."'", true))
				return set_global_key("uri_data($uri)", $key, $val); //stripslashes(

			if ($inherit)
			{
				$skip = false;

				if ($loops ++ > 10)
					$uri = 'http://airbase.ru';
				if ($uri == 'http://airbase.ru')
					break;
				$res = $this->get_data_array($uri, 'parent');
				if ($res)
				{
					sort($res);
					$uri = $res[0];
				}
				else
					$uri = 'http://airbase.ru';
			}
			else
				break;
		}
		while ($uri && $uri != "http://{$_SERVER['HTTP_HOST']}");

		if (!$raw && ($res = $this->post_data_check($uri, $key)) !== false)
			return set_global_key("uri_data($uri)", $key, $res);

		//			include_once("funcs/DataBaseHTS/ipb.php");
		$value = NULL; //dbhts_ipb($uri, $key, $this);

		set_global_key("uri_data($uri)", $key, $value);

		return $value ? $value : $default;
	}

	function get_data_array($uri, $key, $fields = "`value`", $search = "`id`")
	{
		echolog("Get keys array '$key' for '$uri' (fields=$fields, search=$search)");

		$uri = $this->normalize_uri($uri);

		if (($res = $this->pre_data_check($uri, $key)) !== false)
			return $res;

		$key_table_name = $this->create_data_table($key);

		$res = $this->dbh->get_array("SELECT $fields FROM `$key_table_name` WHERE $search='".addslashes($uri)."'");

		if ($res)
			return $res;

		if (($res = $this->post_data_check($uri, $key)) !== false)
			return $res;

		return array ();
	}

	function data_exists($uri, $key, $value)
	{
		foreach ($this->get_data_array($uri, $key) as $val)
		{
			if ($val == $value)
				return true;
		}

		return false;
	}

	function set_data($uri, $key, $value, $params = array (), $append = false)
	{
		echolog("Set for '$uri' as '$key'='$value'");

		if (!is_null($value) && is_global_key("uri_data($uri)", $key) && global_key("uri_data($uri)", $key) == $value)
			return;

		$uri = $this->normalize_uri($uri);

		$key_table_name = $this->create_data_table($key);

		if (is_null($value))
			$this->dbh->query("DELETE FROM $key_table_name WHERE `id`='".addslashes($uri)."'");
		else
			$this->dbh->store($key_table_name, "`id`='".addslashes($uri)."'", array ('id' => $uri, 'value' => $value) + $params, $append);

		set_global_key("uri_data($uri)", $key, $value);

		return $value;
	}

	function update_data($uri, $key, $fields, $search = "`id`")
	{
		echolog("Set for '$uri' as '$key' $fields => $search");

		$uri = $this->normalize_uri($uri);

		$key_table_name = $this->create_data_table($key);

		//			$GLOBALS['log_level'] = 9;
		$this->dbh->store($key_table_name, "$search='".addslashes($uri)."'", $fields);
		//			$GLOBALS['log_level'] = 2;

	}

	function append_data($uri, $key, $value, $params = array ())
	{
		echolog("Append for '$uri' as '$key'='$value'");

		$uri = $this->normalize_uri($uri);

		$key_table_name = $this->create_data_table($key);

		$this->dbh->store($key_table_name, "`id`='".addslashes($uri)."'", array ('id' => $uri, 'value' => $value) + $params, true);
		//			$this->dbh->query("DELETE FROM `$key_table_name` WHERE `id`=$page_id AND `value`='$value'");
		//			$this->dbh->query("INSERT INTO `$key_table_name` (`id`,`value`) VALUES ($page_id,'".mysql_real_escape_string($value,$this->dbh->dbh)."')");
		//			echo 'charset='.$this->dbh->get("SELECT @@character_set_client");
	}

	function remove_data($uri, $key, $value = NULL)
	{
		$uri = $this->normalize_uri($uri);

		$key_table_name = $this->create_data_table($key);

		if (is_null($value))
			$this->dbh->query("DELETE FROM `$key_table_name` WHERE `id`='".addslashes($uri)."'");
		else
			$this->dbh->query("DELETE FROM `$key_table_name` WHERE `id`='".addslashes($uri)."' AND `value`='".addslashes($value)."'");
	}

	function nav_link($iparent, $ichild)
	{
		//			echo "try linked $iparent, $ichild<br>";
		$parent = $this->normalize_uri($iparent);
		$child = $this->normalize_uri($ichild);

		if (!$parent || !$child)
		{
			debug(__FILE__.':'.__LINE__." Can't nav pair: $iparent-$ichild to $parent-$child", 1);
			return;
		}

		$GLOBALS['tmp_dbhts_nav_check_count'] = 0;

		if (!$this->parent_check($parent, $child))
		{
			debug(__FILE__.':'.__LINE__." Try to cycle parents-link: $child to $parent", 1);
			return;
		}

		$this->append_data($parent, 'child', $child);
		$this->append_data($child, 'parent', $parent);
	}

	function add_child($parent, $child)
	{
		$parent = $this->normalize_uri($parent);
		$child = $this->normalize_uri($child);

		if (!$parent || !$child)
		{
			debug(__FILE__.':'.__LINE__." Can't add child link: $parent-$child", 1);
			return;
		}

		$GLOBALS['tmp_dbhts_nav_check_count'] = 0;

		if (!$this->parent_check($parent, $child))
		{
			debug(__FILE__.':'.__LINE__." Try to cycle parents-link: $child to $parent", 1);
			return;
		}

		//			echo "Add $child as child for $parent";
		$this->append_data($parent, 'child', $child);
	}

	function parent_add($uri, $parent)
	{
		$uri = $this->normalize_uri($uri);
		$parent = $this->normalize_uri($parent);

		if (!$parent || !$uri)
		{
			debug(__FILE__.':'.__LINE__." Can't add parent link $uri-$parent", 1);
			return;
		}

		if (!$this->parent_check($parent, $uri))
		{
			debug(__FILE__.':'.__LINE__." Try to cycle parents-link: $parent to $uri", 1);
			return;
		}

		//			echo "Add $child as child for $parent";
		$this->append_data($uri, 'parent', $parent);
	}

	function parent_check($page, $parent_check)
	{
		if ($page == $parent_check)
			return false;

		if (empty ($GLOBALS['tmp_dbhts_nav_check_count']))
			$GLOBALS['tmp_dbhts_nav_check_count'] = 0;

		if ($GLOBALS['tmp_dbhts_nav_check_count']++ > 10)
		{
			debug(__FILE__.':'.__LINE__." Cycled parents-link: $page to $parent_check", 1);
			return false;
		}

		$no_circuit = true;
		foreach ($this->get_data_array($page, 'parent') as $p)
			$no_circuit = $no_circuit && $this->parent_check($p, $parent_check);

		return $no_circuit;
	}

	function remove_nav_link($iparent, $ichild = NULL)
	{
		$parent = $this->normalize_uri($iparent);
		if ($ichild)
		{
			$child = $this->normalize_uri($ichild);
			$t_c = $t_p = array ();
			if ($parent)
			{
				$t_c[] = "`id`	= '".addslashes($parent)."'";
				$t_p[] = "`value` = '".addslashes($parent)."'";
			}
			if ($child)
			{
				$t_c[] = "`value` = '".addslashes($child)."'";
				$t_p[] = "`id`	= '".addslashes($child)."'";
			}
			if ($parent || $child)
			{
				$t_c = join(' AND ', $t_c);
				$t_p = join(' AND ', $t_p);
				if ($t_c)
					$this->dbh->query("DELETE FROM `hts_data_child`  WHERE $t_c");
				if ($t_p)
					$this->dbh->query("DELETE FROM `hts_data_parent` WHERE $t_p");
			}
		}
		else
		{
			$this->dbh->query("DELETE FROM `hts_data_child`  WHERE `id` = '".addslashes($parent)."' OR `value` = '".addslashes($parent)."'");
			$this->dbh->query("DELETE FROM `hts_data_parent` WHERE `id` = '".addslashes($parent)."' OR `value` = '".addslashes($parent)."'");
		}
	}

	function child_remove($uri, $child)
	{
		if (!$uri || !$child)
		{
			debug(__FILE__.':'.__LINE__." Can't remove child link: $uri-$child", 1);
			return;
		}
		$uri = $this->normalize_uri($uri);
		$child = $this->normalize_uri($child);
		$this->dbh->query("DELETE FROM `hts_data_child`  WHERE `id`	= '".addslashes($uri)."' AND `value` = '".addslashes($child)."'");
	}

	function parent_remove($uri, $parent)
	{
		if (!$parent || !$uri)
		{
			debug(__FILE__.':'.__LINE__." Can't remove parent link: $uri-$parent", 1);
			return;
		}
		$uri = $this->normalize_uri($uri);
		$parent = $this->normalize_uri($parent);
		$this->dbh->query("DELETE FROM `hts_data_parent`  WHERE `id` = '".addslashes($uri)."' AND `value` = '".addslashes($parent)."'");
	}

	function page_uri_by_value($key, $value)
	{
		$key_table_name = $this->create_data_table($key);
		return $this->dbh->get("SELECT `id` FROM `$key_table_name` WHERE `value`='".addslashes($value)."'");
	}

	function uri_array_by_value($key, $value)
	{
		$key_table_name = $this->create_data_table($key);
		return $this->dbh->get_array("SELECT `id` FROM `$key_table_name` WHERE `value` = '".addslashes($value)."'");
	}

	function uri_array_by_condition($key, $condition)
	{
		$key_table_name = $this->create_data_table($key);
		return $this->dbh->get_array("SELECT `id` FROM `$key_table_name` WHERE $condition");
	}

	function create_data_table($key, $create_table = true)
	{
			//			echo "Create table name for $key";

	if (is_global_key('key_table_name', $key) && global_key('key_table_name', $key))
			return global_key('key_table_name', $key);

		$key_table_name = "hts_data_$key";

		set_global_key('key_table_name', $key, $key_table_name);

		if (1 || !$create_table)
			return $key_table_name;

		$res = $this->dbh->get("SELECT * FROM `hts_keys` WHERE `name`='".addslashes($key)."'");
		$type = $res['type'];

		if (!$type)
			return;

		$params_fields = '';
		$params_key = '';

		if ($res['params'])
		{
			foreach (split(",", $res['params']) as $p)
			{
				list ($f, $t) = split("=", $p);
				$params_fields .= "`$f` $t NOT NULL,\n";
				if ($t != 'TEXT' && substr($t, 0, 7) != 'VARCHAR')
					$params_key .= ", `$f`";
			}
		}

		$inc = $res['autoinc_value'] ? ' AUTO_INCREMENT ' : '';
		$index_id = !$res['array'] ? ' PRIMARY KEY `id` (`id`), ' : ' KEY `id` (`id`), ';

		$charset = (substr($type, 0, 3) == 'INT' ? '' : 'CHARACTER SET utf8');
		$index = '';
		$length = '';
		switch (substr($type, 0, 3))
		{
			case 'TEX' :
				$index = 'FULLTEXT KEY `value` (`value`)';
				$length = "(166)";
				break;
			case 'VAR' :
				$index = 'FULLTEXT KEY `value` (`value`)';
				break;
			case 'INT' :
				$index = 'KEY `value` (`value`)';
				break;
		}

		//			$GLOBALS['log_level']=9;
		$q = "
								CREATE TABLE IF NOT EXISTS `$key_table_name` (
									`id` VARCHAR(166) NOT NULL,
									`value` $type $charset NOT NULL $inc,
									$params_fields
									$index_id
									UNIQUE KEY `pair` ( `id` , `value` $length $params_key),
									$index
								);"; //  CHARACTER SET = utf8
		//			echo $q;
		$this->dbh->query($q);

		//			$GLOBALS['log_level']=2;

		/*
		CREATE TABLE `hts_keys` (
		`name` VARCHAR( 255 ) CHARACTER SET utf8 NOT NULL ,
		`type` VARCHAR( 255 ) CHARACTER SET utf8 NOT NULL ,
		`protected` TINYINT NOT NULL 
		) CHARACTER SET = utf8;
		*/
		return $key_table_name;
	}

	function delete_by_mask($uri)
	{
		foreach ($this->dbh->get_array("SELECT `name` FROM `hts_keys`") as $key)
		{
			$key_table_name = $this->create_data_table($key);
			$this->dbh->query("DELETE FROM `$key_table_name` WHERE `id` LIKE '".addslashes($uri)."'");
		}

		foreach ($this->dbh->get_array("SELECT `name` FROM `hts_keys` WHERE `id_in_value` = 1") as $key)
		{
			$key_table_name = $this->create_data_table($key);
			$this->dbh->query("DELETE FROM `$key_table_name` WHERE `value` LIKE '".addslashes($uri)."'");
		}

		$this->dbh->query("DELETE FROM `hts_aliases` WHERE `alias` LIKE '".addslashes($uri)."' OR `uri` LIKE '".addslashes($uri)."'");
	}

	function delete_page($uri)
	{
		$uri = $this->normalize_uri($uri);

		foreach ($this->dbh->get_array("SELECT `name` FROM `hts_keys`") as $key)
		{
			$key_table_name = $this->create_data_table($key);
			$this->dbh->query("DELETE FROM `$key_table_name` WHERE `id` = '".addslashes($uri)."'");
		}

		foreach ($this->dbh->get_array("SELECT `name` FROM `hts_keys` WHERE `id_in_value` = 1") as $key)
		{
			$key_table_name = $this->create_data_table($key);
			$this->dbh->query("DELETE FROM `$key_table_name` WHERE `value` = '".addslashes($uri)."'");
		}

		$this->dbh->query("DELETE FROM `hts_aliases` WHERE `alias` = '".addslashes($uri)."' OR `uri` = '".addslashes($uri)."'");
	}

	function rename_host($from, $to)
	{
		foreach ($this->dbh->get_array("SELECT `name` FROM `hts_keys`") as $key)
		{
			$key_table_name = $this->create_data_table($key);
			$this->dbh->query("UPDATE IGNORE `$key_table_name` SET `id` = REPLACE(`id`, '".addslashes($from)."', '".addslashes($to)."');");
		}

		foreach ($this->dbh->get_array("SELECT `name` FROM `hts_keys` WHERE `id_in_value` = 1") as $key)
		{
			$key_table_name = $this->create_data_table($key);
			$this->dbh->query("UPDATE `$key_table_name` SET `value` = REPLACE(`value`, '".addslashes($from)."', '".addslashes($to)."');");
		}

		$this->dbh->query("UPDATE `hts_aliases` SET `alias` = REPLACE(`alias`, '".addslashes($from)."', '".addslashes($to)."');");
	}

	function parse_uri($uri)
	{
		$uri = $this->normalize_uri($uri);
		$data = parse_url($uri);

		if (empty ($data['host']))
			$data['host'] = $_SERVER['HTTP_HOST'];

		if (preg_match("!^{$_SERVER['HTTP_HOST']}$!", $data['host']))
			$data['root'] = $_SERVER['DOCUMENT_ROOT'];
		else
			$data['root'] = $this->dbh->get("SELECT `doc_root`  as `root` FROM `hts_hosts` WHERE `host` = '".addslashes($data['host'])."'");

		$data['local'] = !empty ($data['root']);
		$data['local_path'] = $data['root'].str_replace('http://'.$data['host'], '', $uri);
		$data['uri'] = "http://".@ $data['host'].@ $data['path'];
		return $data;
	}

	function base_value($key, $def = NULL)
	{
		if (is_global_key('base_value', $key))
			return global_key('base_value', $key);

		$val = $this->dbh->get("SELECT `$key` FROM `hts_hosts` WHERE `host` LIKE '{$_SERVER['HTTP_HOST']}'");

		return set_global_key('base_value', $key, $val ? $val : $def);
	}

	/*	function move_page($old_name, $new_name)
		{
			$new_name = $this->normalize_uri($new_name);
			$old_name = $this->normalize_uri($old_name);
	
			if (!$new_name || !$old_name)
				return false;
	
			$this->dbh->insert('hts_aliases', array ('alias' => $old_name, 'uri' => $new_name));
			return true;
		}
	*/
	function viewses_inc($uri)
	{
		if (!$this->get_data($uri, 'views_first'))
			$this->set_data($uri, 'views_first', time());

		$this->set_data($uri, 'views_last', time());

		$this->set_data($uri, 'views', $this->get_data($uri, 'views') + 1);
	}

	function sys_var($key, $default = NULL)
	{
		$ret = $this->dbh->get("SELECT `value` FROM `hts_ext_system_data` WHERE `key`='".addslashes($key)."'");

		if (!$ret)
			$ret = $default;

		return $ret;
	}

	function set_flag($uri, $flag)
	{
		$this->append_data($uri, 'flags', $flag);
	}

	function drop_flag($uri, $flag)
	{
		$this->remove_data($uri, 'flags', $flag);
	}

	function is_flag($uri, $flag)
	{
		return $this->data_exists($uri, 'flags', $flag);
	}

	function pages_with_flag($uri_like, $flag)
	{
		return $this->dbh->get_array("SELECT id FROM hts_data_flags WHERE value LIKE '".addslashes($flag)."' AND id RLIKE '".addslashes($uri_like)."'");
	}

	function export($uri)
	{
		$save = '';
		foreach (split(' ', 'title source') as $i)
			$save .= "$i = ".$this->get_data($uri, $i).";\n\n";
		return $save;
	}

	function getNextId($uri)
	{
		$uri = addslashes($this->normalize_uri($uri));
		$this->dbh->query("INSERT INTO `hts_ids` SET `uri` = '$uri'");
		$id = $this->dbh->get_last_id();
		if (!$id)
			exit ("Unknown error. Can't get next ID for " + $uri);
		return $id;
	}

	function pages_with_parent($uri)
	{
		return $this->dbh->get_array("SELECT id FROM hts_data_parent WHERE value LIKE '".addslashes($uri)."'");
	}

	function get_children_array_ex($parent, $params = array ())
	{
		$show_hidden = @ $params['hidden'];
		$show_deleted = @ $params['deleted'];
		$limit = intval(empty ($params['limit']) ? 20 : $params['limit']);
		$range = intval(empty ($params['range']) ? 86400 : $params['range']);

		$stop_time = time();
		$start_time = $stop_time - $range;

		$deleted_join = $deleted_cond = $hidden_join = $hidden_cond = "";

		if (empty ($params['hidden']))
		{
			$hidden_join = "
																	LEFT JOIN hts_data_flags fh ON (c.value = fh.id AND fh.value='hidden')";
			$hidden_cond = "
																	AND fh.id IS NULL";
		}

		if (empty ($params['deleted']))
		{
			$deleted_join = "
																	LEFT JOIN hts_data_flags fd ON (c.value = fd.id AND fd.value='deleted')";
			$deleted_cond = "
																	AND fd.id IS NULL";
		}

		$query = "SELECT c.value as uri
												FROM hts_data_child c
													LEFT JOIN hts_data_modify_time mt ON (c.value = mt.id)
													$hidden_join
													$deleted_join
												WHERE c.id LIKE '".addslashes($parent)."'
													AND mt.value >= $start_time
													AND mt.value <	$stop_time
													$hidden_cond
													$deleted_cond
												ORDER BY mt.value DESC
												LIMIT $limit;";

		$ret = $this->dbh->get_array($query);

		return $ret;
	}

	function get_array_ex($regexp, $params = array ())
	{
		$show_hidden = @ $params['hidden'];
		$show_deleted = @ $params['deleted'];
		$limit = intval(empty ($params['limit']) ? 20 : $params['limit']);
		$range = intval(empty ($params['range']) ? 86400 : $params['range']);

		$stop_time = intval(empty ($params['stop_time']) ? time() : $params['stop_time']);
		$start_time = intval(empty ($params['start_time']) ? $stop_time - $range : $params['start_time']);

		$deleted_join = $deleted_cond = $hidden_join = $hidden_cond = "";

		$like_type = empty ($params['like_type']) ? 'like' : addslashes($params['like_type']);

		if (empty ($params['hidden']))
		{
			$hidden_join = "
																	LEFT JOIN hts_data_flags fh ON (ct.value = fh.id AND fh.value='hidden')";
			$hidden_cond = "
																	AND fh.id IS NULL";
		}

		if (empty ($params['deleted']))
		{
			$deleted_join = "
																	LEFT JOIN hts_data_flags fd ON (ct.value = fd.id AND fd.value='deleted')";
			$deleted_cond = "
																	AND fd.id IS NULL";
		}

		$joined = array ();
		$join_cnt = 0;

		if (is_array($params['where']))
		{
			$where_join = "";
			$where_cond = "";

			$m2 = array ();
			foreach ($params['where'] as $field => $value)
			{
				if (preg_match("!^(.+)\s+(.+?)$!", $field, $m2))
				{
					$field = $m2[1];
					$cond = addslashes($m2[2]);
				}
				else
					$cond = "=";

				$field = addslashes($field);

				if (empty ($joined[$field]))
				{
					$join_cnt ++;
					$joined[$field] = $jt = "j$join_cnt";
					$where_join .= "
																									LEFT JOIN hts_data_$field $jt ON (ct.id = $jt.id) ";
				}
				else
					$jt = "j".$joined[$field];
				$where_cond .= "
																					AND $jt.value $cond ".addslashes($value)." ";
			}
		}

		$query = "SELECT ct.id as uri
												FROM hts_data_create_time ct
													LEFT JOIN hts_data_modify_time mt ON (ct.id = mt.id)
													$where_join
													$hidden_join
													$deleted_join
												WHERE ct.id $like_type '".addslashes($regexp)."'
													$where_cond
													$hidden_cond
													$deleted_cond
												ORDER BY mt.value DESC
												LIMIT $limit;";

		//			$GLOBALS['log_level'] = 10;
		$ret = $this->dbh->get_array($query);
		//			$GLOBALS['log_level'] = 2;

		return $ret;
	}

}
?>




