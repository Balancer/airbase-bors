<?
    include_once('funcs/lcml.php');
    
    function lcml_module_search_stat()
    {
        $stat = `/usr/local/mnogosearch/sbin/indexer -S`;
        echo lcml("[code]{$stat}[/code]",array('cr_type'=>'save_cr'));
    }

    lcml_module_search_stat();
?>