<?
    require_once("smarty-resource-file.php");

    $smarty->register_resource("xfile", array("smarty_resource_file_get_template",
                                       "smarty_resource_file_get_timestamp",
                                       "smarty_resource_file_get_secure",
                                       "smarty_resource_file_get_trusted"));
