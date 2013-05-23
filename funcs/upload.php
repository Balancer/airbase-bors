<?php
	// Модуль загрузки указанных файлов и/или картинок на сервер

//	require_once("{$_SERVER['DOCUMENT_ROOT']}/cms/config.php");
//	require_once("funcs/log.php");

	function upload($page)
	{
		require_once("funcs/DataBaseHTS.php");
//		require_once("funcs/navigation/go.php");
		require_once("actions/recompile.php");

		require_once("inc/filesystem.php");
		require_once("funcs/users.php");

		$hts = new DataBaseHTS;		

		check_access($page, $hts);

		for($i=0, $asize=sizeof($_FILES['upload_file']['name']); $i<$asize; $i++)
		{
			$name = @$_POST['upload_names'][$i];
			$realname = $_FILES['upload_file']['name'][$i];

			if(!$name)
				$name = $realname;

			$type	 = $_FILES['upload_file']['type'][$i];
			$tmp_name = $_FILES['upload_file']['tmp_name'][$i];
			$error	= $_FILES['upload_file']['error'][$i];
			$size	 = $_FILES['upload_file']['size'][$i];

			$real_ext = preg_replace('!^.*\.(\w+?)$!', '$1', $realname);
			$name = preg_replace('!\.%real_ext%$!', ".$real_ext", $name);
				
//			exit("name = $name, realname = $realname<br/>");

			if(!$error && is_uploaded_file($tmp_name))
			{
				$parse = $hts->parse_uri($page);
//				if(preg_match("!image/!", $type))
				$parse['uri'] .= $name;

				mkpath($parse['local_path']);

//				print_r($parse); exit();

				if(move_uploaded_file($tmp_name, $parse['local_path'].$name))
				{
					$hts->set_data($parse['uri'], 'modify_time', time());
					$hts->set_data($parse['uri'], 'author', user_data('member_id'));
					$hts->nav_link($page, $parse['uri']);
//					append_log($parse['uri'], 'upload_files', $page);
				}

			}
		}

//		echo "<xmp>"; print_r($_FILES); print_r($_POST); exit("</xmp>");

		$hts->set_data($page, 'modify_time', time());
		recompile($page);
//		go("$page?");
	}
?>
