<?php

class balancer_board_tasks extends bors_object
{
	static function send_email($data)
	{
		bors_ext_mail::send($data['to'], array("Ответ на Ваше сообщение на форумах Balancer'а", $data['text'], NULL), 'noreply@airbase.ru');
//		bors_ext_mail::send('balancer@balancer.ru', array("Ответ на Ваше сообщение на форумах Balancer'а", $data['text'], NULL), 'noreply@airbase.ru');
		bors_debug::syslog('__test_task', print_r($data, true), false);
	}
}
