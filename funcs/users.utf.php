<?
    require_once('funcs/DataBase.php');
    require_once('funcs/security.php');
    require_once('funcs/global-data.php');

    $GLOBALS['forums_data']=array(
            'password' => 'Password',
            'first_name' => 'FirstName',
            'last_name' => 'LastName',
            'email' => 'EMail',
			'level' => 'Level',
			'AviasID' => 'Signature',
        );

	$GLOBALS['funcs_data'] = array(
		'name' => 'get_name',
		'nick' => 'get_name',
		);

	function get_name($user, $def)
	{
//		echo "Get name for '$user'";
		$first = user_data('first_name', $user);
		$last = user_data('last_name', $user);
		$name = join(' ',array($first,$last));
		if(!$name)
			$name = $def;
		return $name;
	}

    function user_data($key,$user=NULL,$def='')
	{
        if(is_global_key("user_data($user)",$key))
            return global_key("user_data($user)",$key);

        return set_global_key("user_data($user)",$key, _user_data($key, $user, $def));
	}
	
    function _user_data($key,$user,$def)
    {
        $db = new DataBase('WWW');

		if(!$user)
		{
			$avias_id = @$_COOKIE['AviasID'];
			$user = intval(user_data('id', $avias_id ? $avias_id : -1));
		}

        $id = intval($user);

		if(!$id || !preg_match("!^\d+$!", $user) || $id > 99999)
            $id = $db->get("SELECT `ID` FROM `UserDB` WHERE `Signature` = '".addslashes($user)."'");

		if(!preg_match("!^\d+$!", $id) || $id < 1)
            $id = $db->get("SELECT `ID` FROM `UserDB` WHERE `Login` = '".addslashes($user)."'");

        if($key == 'id')
			return $id;

//        echo "Try get data '$key' for user '$user'<br />\n";

		if(!$user && ($key == 'nick' || $key == 'name'))
			return $def;

		if(!$id)
			return $def;

        if(!$id)
            return isset($def)?$def:false;


        unset($value);

        if(isset($GLOBALS['funcs_data'][$key]))
        {
            $value = $GLOBALS['funcs_data'][$key]($user,$def);
        }
		else
        {
//	        echo "Try get not cached data for user $id for key '$key'<br>\n";
//			$GLOBALS['log_level']=10;		
	        if(!isset($GLOBALS['forums_data'][$key]) || !$GLOBALS['forums_data'][$key])
                $value = $db->get("SELECT `value` FROM `users_data` WHERE `user_id`=$id AND `key`='".addslashes($key)."'");
            else
                $value = $db->get("SELECT `".$GLOBALS['forums_data'][$key]."` FROM `UserDB` udb LEFT JOIN `UserPrefs` up ON (udb.ID = up.LoginID) WHERE `ID`=$id");
//			$GLOBALS['log_level'] = 2;
        }
       
        return $value ? $value : $def;
    }

    function set_user_data($key, $value, $user=NULL)
    {
//		echo "*** set data '$key'='$value' for user '$user'";
//		return;
	
        global $forums_data;
        $id = isset($_COOKIE['user_id']) ? intval($_COOKIE['user_id']) : 0;

        if(!$id)
            $id=intval($user);

        if(!$id)
            return false;

        $db = new DataBase('WWW');
        if(empty($forums_data[$key]))
            $db->store('users_data',"`user_id`=$id AND `key`='".addslashes($key)."'",array('user_id'=>$id,'key'=>$key,'value'=>$value));
        else
            die("UPDATE FORUM.ib_members SET `".$forums_data[$key]."`='".addslashes($value)."'");
        return set_global_key('user_data',$id."_".$key,$value);
    }

    function check_password()
    {
        $avias_id = @$_COOKIE['user_id'];

        if(!$avias_id || !user_data('id'))
        {
            $nick = user_data('nick');
            echo "<h3><span style=\"text-color: red;\">Не вошедший в систему или неверный пользователь!";
            die();
        }
	}
	
    function user_data_array($key,$user=NULL,$def=array())
    {
        if(!$id = get_user($user)) return isset($def)?$def:false;

        if(is_global_key('user_data_array',$id.'_'.$key))
            return global_key('user_data_array',$id.'_'.$key);

        $db = new DataBase('USERS');
        $value = $db->get_array("SELECT `value` FROM `users_data` WHERE `user_id`=$id AND `key`='".addslashes($key)."'");

        if(!$value) $value=$def;

        return set_global_key('user_data_array',$id.'_'.$key,$value);
    }

    function set_user_data_array($key,$value,$user=NULL)
    {
        if(!$id = get_user($user)) return false;

        $db = new DataBase('USERS');

        $fields=array();
        foreach($value as $v)
        {
            array_push($fields,array('user_id'=>$id,'key'=>$key,'value'=>$v));
        }

        $db->store_array('users_data',"`user_id`=$id AND `key`='".addslashes($key)."'",$fields);
        return set_global_key('user_data',$id."_".$key,$value);
    }

/*    function get_user($user)
    {
        if($user) 
			return intval($user);

        return user_data('id',NULL,false);
    }

    function get_id_by_name($name)
    {
        $db = new DataBase('FORUM');
        return intval($db->get("SELECT `id` FROM `ib_members` WHERE `name`='".addslashes($name)."'"));
    }

    function user_md5_check($user=NULL)
    {
        if(user_data('id',$user))
        {
            return md5(user_data('email',$user).'&'.user_data('member_login_key',$user).'&'.user_data('joined',$user));
        }
        else
        {
            return md5("this is only here to prevent it breaking on guests");
        }
    }*/

    function access_allowed($page, $hts=NULL)
    {
        if(empty($hts))
            $hts = new DataBaseHTS;

        $base_page_access = $hts->base_value('default_access_level', 3);
        $ul = user_data('level',NULL,1);

        $pl = $hts->get_data($page, 'access_level', $base_page_access, true);
        return $ul >= $pl;
    }

    function access_warn($page, $hts=NULL)
    {
        if(empty($hts))
            $hts = new DataBaseHTS;

        $base_page_access = $hts->base_value('default_access_level', 3);
        $ul = user_data('level', NULL, 1);

//        echo "access_check: $base_page_access/$ul";

        $pl = $hts->get_data($page, 'access_level', $base_page_access, true);
        if($ul < $pl)
		{
            echo "<span style=\"color: red; font-weight: bold;\">Внимание! Ваш уровень доступа ($ul) ниже необходимого ($pl) для сохранения изменений! Изменения не будут сохранены!</span>";    
			return true;
		}
		return false;
    }

    function check_access($pl, $hts=NULL, $def=1)
    {   
//        check_password();

        // Если первый параметр число - уровень доступа пользователя должен быть не ниже его.
        // Если указано не число - то этот параметр считается страницей, с которой и считывается требуемый уровень доступа.
        // третий опциональный параметр - уровень доступа пользователя по умолчанию.

        if(!preg_match("!^\d+$!", $pl))
        {
            if(!$hts)
                $hts = new DataBaseHTS;
            $base_page_access = $hts->base_value('default_access_level', 3);
            $pl = $hts->get_data($pl, 'access_level', $base_page_access, true);
        }

        $ul = intval(user_data('level', NULL, $def));

//        echo("pl=$pl, ul=$ul, def=$def");

        if($ul<$pl)
        {
            $nick=user_data('nick');
            echo "<b><font color=\"red\">Уровень доступа пользователя $nick ($ul) недостаточен для этой ($pl) операции!</font></h3>";
            die();
        }
    }

    class User
    {
    	var $id;

		function User($_login = NULL)
		{
			$this->id = user_data('id', $_login);
		}
    	
    	function data($data, $default=NULL)
    	{
    		return user_data($data, $this->id, $default);
    	}

		function set_data($key, $value)
		{
			set_user_data($key, $value, $this->id);
		}

	    function do_login($user, $password, $show_success=true)
    	{
        	$this->id = user_data('id', $user);
			
			if(!$this->id)
			{
				echo "<b>Неизвестный пользователь '$user'</b>'";
				return false;
			}

			$pw = user_data('password', $user);

//			echo "pw=$password, md=".md5($password).", lp=$lp;";
			
			if($password != $pw)
			{
				echo "<b>Неправильный пароль пользователя '$user'</b>'";
				return false;
			}

			$avias_id = user_data('AviasID', $this->id);
			
			$_COOKIE['AviasID'] = $avias_id;

			SetCookie("AviasID", $avias_id, time()+2592000,"/", $_SERVER['HTTP_HOST']);
			
			if($show_success)
				echo "<b>Вы успешно вошли в систему!</b>";
		}

		function do_logout()
		{
			SetCookie("AviasID","",0,"/");
			$_COOKIE['AviasID'] = "";
		}
		
		function get_page()
		{
			return $GLOBALS['cms']['main_host_uri'] . "/users/~".$this->id."/";
		}
		
		function check_access($uri)
		{
			if(!$this->id)
			{
				$ret['title'] = "Ошибка входа";
				$ret['source'] = 'Вы не зашли в систему.';

				return $ret;
			}

			if(!access_allowed($uri))
			{
				$ret['title'] = "Ошибка доступа";
				$ret['source'] = 'У Вас недостаточно прав для выполнения операции';

				return $ret;
			}
			
			return NULL;
		}
	}
?>
