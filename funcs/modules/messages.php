<?
	function error_message($text, $redir = false, $title="", $timeout = 5)
	{
		include_once("funcs/templates/smarty.php");

		if(!$redir)
			$text .= ec("<br /><br /><br /><center><a href=\"javascript:history.go(-1)\">вернуться на предыдущую страницу</a></center>");
		elseif($redir !== true)
			$text .= "<br /><br /><br /><center><a href=\"$redir\">".ec("дальше")."</a></center>";

		$GLOBALS['page_data']['title'] = ec("Ошибка");
		$GLOBALS['page_data']['source'] = $text;

		show_page(@$GLOBALS['main_uri']);
		
		if($redir === true)
		{
			if(!empty($_POST['ref']))
				$redir = $_POST['ref'];
			else
				$redir = user_data('level') > 3 ? "/admin/news/" : "/";
		}
		
		if($redir)
			go($redir, false, $timeout);
		
		return true;
	}

	function message($text, $redir = false, $title="", $timeout = 5)
	{
		require_once("funcs/templates/smarty.php");

		if(!$redir)
			$text .= ec("<br /><br /><br /><center><a href=\"javascript:history.go(-1)\">вернуться на предыдущую страницу</a></center>");
		elseif($redir !== true)
			$text .= "<br /><br /><br /><center><a href=\"$redir\">".ec("дальше")."</a></center>";
		
		$GLOBALS['page_data']['title'] = $title?$title:ec("Сообщение");
		$GLOBALS['page_data']['source'] = $text;

		show_page(@$GLOBALS['main_uri']);
		
		if($redir)
			if($redir !== true)
				go($redir, false, $timeout);
			else
				go(empty($_POST['ref']) ? "/admin/news/" : $_POST['ref'], false, $timeout);

		return true;
	}
