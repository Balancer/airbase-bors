<?php

class airbase_user_warning_type extends base_list
{
	function named_list()
	{
		return array(
			'0' => ec('Причина не указана'),

			'1' => ec('Флуд или офтопик'),
			'2' => ec('Оверквотинг[п.15]'),
			'3' => ec('Флейм'),
			'4' => ec('Использование транслитерации [п.7]'),
			'11' => ec('Пренебрежительное высокомерие [п.11.4]'),
			'12' => ec('Необоснованный кащенизм [п.14]'),
			'13' => ec('Обширная цитата на иностранном языке без перевода [п.16]'),
			'16' => ec('Переход на личности'),
			'15' => ec('Другое [п.2.1, описание в комментарии]'),

			'8' => ec('Троллинг [п.11.1]'),
			'5' => ec('Обсуждение модераториала или политики модерации [п.8]'),
			'10' => ec('Бездоказательное оскорбление человека, не являющегося участником форума [п.11.3]'),
			'7' => ec('Категоричное сомнительное заявление, не подтверждённое фактом [п.11] '),

			'18' => ec('Оскорбления национальностей и этносов, шовинистические высказывания'),

			'9' => ec('Оскорбление участника форума'),

			'19' => ec('Пропаганда нацизма'),

			'6' => ec('Заведение второго аккаунта при R/O первого [п.9.1]'),
			'14' => ec('Нецензурные обращения и оскорбления к администрации сайта [п.5.3]'),

			'17' => ec('Немаскированные нецензурные выражения'),
			'20' => ec('Спам'),
		);
	}
	
	static function score($id)
	{
		$scores = array(
			'0' => 0,
			'1' => 1,
			'2' => 1,
			'3' => 1,
			'4' => 1,
			'5' => 2,
			'6' => 10,
			'7' => 2,
			'8' => 2,
			'9' => 4,
			'10' => 2,
			'11' => 1,
			'12' => 1,
			'13' => 1,
			'14' => 10,
			'15' => 1,
			'16' => 1,
			'17' => 2,
			'18' => 3,
			'19' => 6,
		);
		
		return $scores[$id];
	}
}
