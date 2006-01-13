<?
    require_once("funcs/DataBaseHTS.php");
    require_once("funcs/Cache.php");
    require_once("funcs/lcml.php");

    function recompile($page)
    {
//		echo "Recompile page '$page'";		exit();

        if(!$page)
            return;

        $ch = new Cache();
        $ch->clear($page);

        $hts = new DataBaseHTS;

		$source = $hts->get_data($page, 'source');
//		exit($source);

        if($source)
        {
			$ch_type = 'lcml-compiled';
			$ch_key = md5($source);

			$ch->set($ch_type,$ch_key,NULL);

            $body = lcml($source, array(
                'page' => $page, 
                'cr_type' => $hts->get_data($page, 'cr_type'),
				'with_html' => true,
                ));
//            $GLOBALS['log_level'] = 9;

            $hts->set_data($page, 'body', $body);
            $hts->set_data($page, 'compile_time', time());

			$description_source = $hts->get_data($page, 'description_source');
			if($description_source)
			{
	            $description = lcml($description_source, array('page' => $page));
				$hts->set_data($page, 'description', $description);
			}

            $out = '';

            if(!empty($GLOBALS['cms_images']))
            {
                foreach($GLOBALS['cms_images'] as $img)
                    $out .= "<li>$img:&nbsp;<input type=\"hidden\" name=\"upload_names[]\" value=\"$img\"><input type=\"file\" size=\"10\" name=\"upload_file[]\">\n";
                if($out)
                    $out = <<<__EOT__
<form action="/admin/upload.php" method="POST" enctype="multipart/form-data">
<dl class="box">
<dt>Files load</dt>
<dd>
<div align="left"><ul>$out</ul></div>
<input type="submit" value="Load">
</dd></dl>
<input type="hidden" name="page" value="$page">
</form>
__EOT__;
                $GLOBALS['cms_right_column'][] = $out;
            }

            $hts->set_data($page, 'right_column', !empty($GLOBALS['cms_right_column']) ? join("\n",$GLOBALS['cms_right_column']) : NULL);
        }
        else
        {
            debug(__FILE__."[".__LINE__."] Not found page_id for '$page'!");
            exit("Не найден идентификатор страницы '$page'!");
        }
    }
?>
