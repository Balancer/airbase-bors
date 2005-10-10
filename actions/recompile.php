<?
    require_once("funcs/DataBaseHTS.php");
    require_once("funcs/Cache.php");
    require_once("funcs/lcml.php");

    function recompile($page)
    {
//		echo "Recompile page '$page'";
	
        if(!$page)
            return;

        $ch = new Cache();
        $ch->clear($page);

        $hts = new DataBaseHTS;

        if($hts->get_data($page,'source'))
        {
            $body = lcml($hts->get_data($page, 'source'), array(
                'page' => $page, 
                'cr_type' => $hts->get_data($page, 'cr_type'),
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
            echo("Не найден идентификатор страницы '$page'!");
        }
    }
?>
