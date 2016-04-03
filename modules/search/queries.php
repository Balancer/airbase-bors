<?php
    include_once('funcs/DataBase.php');

    function lcml_module_search_queries()
    {
        $db = new driver_mysql('mnoGoSearch','mnogo','mnogokuku');

        $out = array();
        $res = $db->get_array('SELECT DISTINCT qwords FROM qtrack ORDER BY qtime DESC LIMIT 100');
        foreach($res as $r)
            $out[] = "<b>$r</b>";
        echo join("<span style=\"color:#808080\">,</span> ", $out);
    }

    lcml_module_search_queries();
?>