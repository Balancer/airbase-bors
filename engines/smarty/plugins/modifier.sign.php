<?php

function smarty_modifier_sign($num)
{
	return $num > 0 ? "+$num" : "$num";
}
