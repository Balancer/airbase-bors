<?php

// http://forums.balancer.ru/users/rpg/

/*
	Рыба правил:

Уровень			Группа	Нужно очков	Собственный вес	Нужно для штрафа
Гость			0		-			0				1
Новичок			1		9			1				3
Пользователь	2		27			3				9
Старожил		3		81			9				27
Координатор		4		243			27				81
Модератор		5		729			81				243
Администратор	6		2187		243				729
Balancer		7
				n		3**(n+1)	3**(n-1)		3**(n)
*/


class balancer_board_users_rpg_main extends balancer_board_page
{
	var $title = 'Ролевая система форумов';
	var $nav_name = 'RPG';
}