<?php

include_once("navleft.inc.php");
@$GLOBALS['module_data']['skip'] = @split(" ", @$GLOBALS['module_data']['skip']);
echo modules_design_navleft_get($GLOBALS['main_uri']);
