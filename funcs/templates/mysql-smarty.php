<?
    require_once("mysql-smarty-funcs.php");

    $smarty->register_resource("hts", array("db_get_template",
                                       "db_get_timestamp",
                                       "db_get_secure",
                                       "db_get_trusted"));
?>
