<?php

/*

also it is possible to make your php script resume downloads, to do this you need to check $_SERVER['HTTP_RANGE'] which may contain something like this
 "bytes=10-" - resume from position 10, and to end of file

when sending response it is also needed to send with headers
Accept-Ranges: bytes
Content-Length: {filesize}
Content-Range: bytes 10-{filesize-1}/{ffilesize}

hope its usefull



// translate file name properly for Internet Explorer.
if (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE")){
  $fileName = preg_replace('/\./', '%2e', $fileName, substr_count($fileName, '.') - 1);
}




  header("Cache-Control: ");// leave blank to avoid IE errors
  header("Pragma: ");// leave blank to avoid IE errors
  header("Content-type: application/octet-stream");
  header("Content-Disposition: attachment; filename=\"".$fileName."\"");
  header("Content-length:".(string)(filesize($fileString)));
   sleep(1);
   fpassthru($fdl);


*/

/////////////////////////////////////////////////////////////////////////////////////////////////////


define('PUN_ROOT', __DIR__.'/');
require PUN_ROOT.'include/common.php';

require PUN_ROOT.'include/attach/attach_incl.php'; //Attachment Mod row, loads variables, functions and lang file

	if(!isset($_GET['item']))
		message('No file specified, so no download possible');

	$attach_item = intval($_GET['item']);	// make it a bit more secure

	//check that there is such an item
	$result = $db->query("SELECT post_id, filename, extension, mime, location, size FROM {$db->prefix}attach_2_files WHERE id={$attach_item} LIMIT 1")
		or error('Unable to search for specified attachment',__FILE__,__LINE__,$db->error());

	if($db->num_rows($result)!=1)
		message($lang_common['Bad request']);

	list($attach_post_id,$attach_filename,$attach_extension,$attach_mime,$attach_location,$attach_size) = $db->fetch_row($result);

	$attach_allow_download = true; 	// always allowed to download 

	// so if one isn't allowed to download, give them the no permission message...
	if(!$attach_allow_download)
		message($lang_common['No permission']);

// ok, if you've got to here you may download the file ...
// later add possibility to resume files ... but not in Attachment Mod 2.0 ;-)

	// Если это картинка и не установлено download=1, то рисуем страницу с картинкой.
	if(in_array(bors_lower($attach_extension), array('jpg', 'jpeg', 'gif', 'png'))
		&& !isset($_GET['download']))
	{ // show the imageview page
		$page_title = htmlspecialchars($pun_config['o_board_title']).' / Image view - '.$attach_filename.' - ';
		require 'header.php';
		$cdb = new driver_mysql(config('punbb.database'));
		if($attach_post_id == "".intval($attach_post_id))
		{
			$post = bors_load('balancer_board_post', $attach_post_id);
			$title = $post ? $post->title() : ec("утерянное сообщение $attach_post_id");
		}
		else
		{
			$hts = new DataBaseHTS();
			$title = $hts->get($attach_post_id, 'title');
		}

if(!$post || !$post->is_public() || $post->is_hidden())
{
	if(!bors()->user())
		return message('Аттач недоступен гостям');
}

if(!in_array($_SERVER['HTTP_HOST'], array('balancer.ru', 'www.balancer.ru')))
{
	echo '<div style="text-align: center; margin: 10px">';
	readfile("/var/www/bors/bors-airbase/templates/forum/ads/google-ads-2.original.html");
	echo '</div>';
}


	?>

<div id="msg" class="block">
	<h2><span>Исходная тема: <a href="<?php echo $pun_config['root_uri'];?>/viewtopic.php?pid=<?php echo "$attach_post_id#p$attach_post_id";/*"*/?>"><?php echo $title;?></a></span></h2>

	<div class="box">
		<div class="inbox">
		<div class="imgbox"><div class="scrollbox"><img src="<?php echo $pun_config['root_uri'];?>/attachment.php?item=<?php echo $_GET['item']; ?>&amp;download=1" alt="<?php echo $attach_filename;/*"*/?>" /></div></div><p>
		<?php echo $lang_attach['Download:']; ?> <a href="<?php echo $pun_config['root_uri'];?>/attachment.php?item=<?php echo $_GET['item']; ?>&amp;download=1"><?php echo $attach_filename; ?></a></p>
<?php if($post) { ?>
	<p>Сообщение с этим аттачем: <?php echo $post->titled_link(); ?></p>
<?php } ?>
		<p><a href="javascript: history.go(-1)">Go back</a></p>
		</div>
	</div>
</div>

	<?php

if(!in_array($_SERVER['HTTP_HOST'], array('balancer.ru', 'www.balancer.ru')))
{
	echo '<div style="text-align: center; margin: 10px">';
	readfile("/var/www/bors/bors-airbase/templates/forum/ads/google-ads-bottom.original.html");
	echo '</div>';
}

		require 'footer.php';
		exit();
	}
	elseif(@$_GET['download'] <> 2)
	{ 	// put the file out for download
		// update number of downloads
		ini_set('zlib.output_compression',  0);
		$result = $db->query('UPDATE '.$db->prefix.'attach_2_files SET downloads=downloads+1 WHERE id=\''.$attach_item.'\'')
			or error();

		// open a pointer to the file
		if(!file_exists($pun_config['attach_basefolder'].$attach_location))
			throw new Exception('Not exists file attach '.$attach_item.': '.$pun_config['attach_basefolder'].$attach_location);

		$fp = fopen($pun_config['attach_basefolder'].$attach_location, "rb");
		if(!$fp)
		{
			message($lang_common['Bad request']);
		}
		else
		{
			$attach_filename=rawurlencode($attach_filename);	// fix filename (spaces may still mess things up, perhaps add a specific MSIE thing later, not sure though)

			// send some headers
			header('Content-Disposition: attachment; filename='.$attach_filename);
			if(strlen($attach_mime)!=0)
				header('Content-Type: ' . $attach_mime );
			else
				header('Content-type: application/octet-stream'); // a default mime is nothing is defined for the file

			header('Pragma: no-cache'); //hmm, I suppose this might be possible to skip, to save some bw, but I'm far from sure, so I let the 'no cache stuff' be...
			header('Expires: 0'); 
			header('Connection: close'); // Thanks to Dexus for figuring out this header (on some systems there was a delay for 5-7s for downloading)
			if($attach_size!=0)
				header('Content-Length: '.$attach_size);

			// and finally send the file, fpassthru might be replaced later, rumors say fpassthru use alot of memory...
			fpassthru($fp);
		}
	}
	else // Отдаём как есть (download=2)
	{
		ini_set('zlib.output_compression',  0);

		// open a pointer to the file
		if(!file_exists($pun_config['attach_basefolder'].$attach_location))
			debug_exit('Not exists file attach '.$attach_item.': '.$pun_config['attach_basefolder'].$attach_location);

		$fp = fopen($pun_config['attach_basefolder'].$attach_location, "rb");
		if(!$fp)
			message($lang_common['Bad request']);
		else
		{
			$attach_filename=rawurlencode($attach_filename);	// fix filename (spaces may still mess things up, perhaps add a specific MSIE thing later, not sure though)

			// send some headers

			if(strlen($attach_mime)!=0)
				header('Content-Type: ' . $attach_mime );
			else
				header('Content-type: application/octet-stream'); // a default mime is nothing is defined for the file

			header('Pragma: no-cache'); //hmm, I suppose this might be possible to skip, to save some bw, but I'm far from sure, so I let the 'no cache stuff' be...
			header('Expires: 0'); 
			header('Connection: close'); // Thanks to Dexus for figuring out this header (on some systems there was a delay for 5-7s for downloading)
			if($attach_size!=0)
				header('Content-Length: '.$attach_size);

			// and finally send the file, fpassthru might be replaced later, rumors say fpassthru use alot of memory...
			fpassthru($fp);
		}
	}
