<?
    function lcml_tables($txt)
    {
        if(!preg_match("!^#i?table!m", $txt))
            return $txt;

        $file = "/tmp/lcml_tables_".time().rand();
        $fh = fopen($file, "wt");
        fwrite($fh, $txt);
        fclose($fh);

//        echo "http://airbase.ru/cgi-bin/inc/table.cgi?file=$file";
        echo file_get_contents("http://airbase.ru/cgi-bin/inc/table.cgi?file=$file");

        $fh = fopen($file, "rt");
        $txt = fread($fh, filesize($file));
        fclose($fh);
    
        unlink($file);

        return $txt;
    }
?>
