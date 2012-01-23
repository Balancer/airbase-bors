<?php

/**
	Базовый класс для возврата html без заголовков
	Для всяких AJAX и т.п.
*/

class balancer_board_ajax extends bors_page
{
	function template() { return 'null.html'; }
}
