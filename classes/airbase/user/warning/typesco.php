<?php

class airbase_user_warning_typesco extends base_list
{
	function named_list()
	{
		return array(
			'0' => ec('Сделайте выбор причины ниже:<br />'),

			'1' => ec('Флуд или офтопик [+1]'),
			'2' => ec('Оверквотинг [п.15, +1]'),
			'3' => ec('Флейм [+1]'),
			'4' => ec('Использование транслитерации [п.7, +1]'),
			'11' => ec('Пренебрежительное высокомерие [п.11.4, +1]'),
			'12' => ec('Необоснованный кащенизм [п.14, +1]'),
			'13' => ec('Обширная цитата на иностранном языке без перевода [п.16, +1]'),
			'16' => ec('Переход на личности [+1]'),
			'22' => ec('Нарушение форматирования страниц или связности цепочки ответов [п.13, +1]'),
			'15' => ec('Другое [п.2.1, описание в комментарии, +1]<br />'),

			'8' => ec('Троллинг [п.11.1, +2]'),
			'17' => ec('Немаскированные нецензурные выражения [+2]'),
			'5' => ec('Обсуждение модераториала или политики модерации [п.8, +2]<br />'),
			'10' => ec('Бездоказательное оскорбление человека, не являющегося участником форума [п.11.3, +2]'),
			'7' => ec('Категоричное сомнительное заявление, не подтверждённое фактом [п.11, +2] '),

			'18' => ec('Оскорбления национальностей, этносов, религиозных и политических вглядов, шовинистические высказывания [+3]<br />'),

			'9' => ec('Оскорбление участника форума, +4'),
			'23' => ec('Политический офтопик за пределами политфорумов, +4<br />'),

			'24' => ec('Нарушение авторских прав, +5<br />'),

			'19' => ec('Пропаганда нацизма, +6<br />'),

			'6' => ec('Заведение второго аккаунта при R/O первого [п.9.1, +10]'),
			'14' => ec('Нецензурные обращения и оскорбления к администрации сайта [п.5.3, +10]'),
			'21' => ec('Угроза причинения физического вреда участнику форума [п.11.6, +10]'),
			'20' => ec('Спам, +10<br />'),

			// При добавлении новых типов штрафов не забывай прописывать их вес ниже!
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
			'20' => 10,
			'21' => 10,
			'22' => 1,
			'23' => 4,
			'24' => 5,
		);

		return $scores[$id];
	}
}
