<?php
	include_once('inc/attaches.php');

	register_action('attach-upload', 'handler_punbb_attach_upload');
	
	function handler_punbb_attach_upload($uri, $action)
	{
		$attach_basefolder = "/var/www/files.balancer.ru/files/forums/attaches/";

		require_once('funcs/modules/messages.php');
		$me = &new User();
		
		if($me->get('level') < 2)
			return error_message(ec("Недостаточный уровень доступа"));

		$name = @$_FILES['attached_file']['name'];
		$mime = @$_FILES['attached_file']['type'];
		$size =	@$_FILES['attached_file']['size'];
		$tmp_name = @$_FILES['attached_file']['tmp_name'];
		$post_id = @$GLOBALS['main_uri'];
	
		include_once("funcs/modules/uri.php");
		$unique_name = md5($name.$post_id.$size)."-".translite_uri_simple($name);

		$sub1 = substr($unique_name, 0, 2);
		$sub2 = substr($unique_name, 2, 2);

		@mkdir($attach_basefolder.$sub1, 0775);
		@mkdir($subfolder = "$attach_basefolder$sub1/$sub2", 0775);

		// move the uploaded file from temp to the attachment folder and rename the file to the unique name
		if(!move_uploaded_file($tmp_name, "$subfolder/$unique_name"))
			return error_message("Unable to move file from: $tmp_name to $subfolder/$unique_name == ".__FILE__.":".__LINE__);
			
		if(strlen($mime)==0)
			$mime = attach_create_mime(attach_find_extension($name));

		$db = &new DataBase('punbb');
		
		// update the database with this info
		$db->replace('attach_2_files', array(
			'owner' => $me->get('id'),
			'parent_uri' => $post_id,
			'filename' => $name,
			'extension' => attach_get_extension($name),
			'mime' => $mime,
			'location' => "$sub1/$sub2/".$unique_name,
			'size' => $size));

	 	include_once('Smarty/Smarty.class.php');
        $smarty = new Smarty;
        $smarty->compile_dir = $GLOBALS['cms']['cache_dir'].'/smarty-templates_c/';
        $smarty->plugins_dir = $GLOBALS['cms']['base_dir'].'/funcs/templates/plugins/';
        $smarty->cache_dir   = $GLOBALS['cms']['cache_dir'].'/smarty-cache/';
		$smarty->clear_all_cache();

		include_once("actions/recompile.php");
		recompile($GLOBALS['main_uri']);
		
		go($uri);
	}
