<?php

function smarty_modifier_post_attaches($post)
{
	return balancer_board_attach::show_attaches($post);
}
