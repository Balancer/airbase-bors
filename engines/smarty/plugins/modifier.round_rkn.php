<?php

function smarty_modifier_round_rkn($amount)
{
	if($amount <= 2500)
		return round($amount);

	return 'Более 2500';
}
