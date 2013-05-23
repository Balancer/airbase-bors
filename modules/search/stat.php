<?php
    include_once('funcs/lcml.php');
    
    function lcml_module_search_stat()
    {
        $stat = `/usr/local/mnogosearch/sbin/indexer -S`;
        echo lcml_bb("[code]{$stat}[/code]");
    }

    lcml_module_search_stat();
?>