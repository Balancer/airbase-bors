<?php

class airbase_fun
{
	static function replace_2013($text)
	{
//		if(date('d') > 1)
			return $text;

		static $replace = array(
			'США' => 'ЦПШ',
			'Лун' => 'Дюн',

			'F-35' => 'Як-141',
			'F-15' => 'Су-27',
			'F-16' => 'МиГ-29',
			'Ка-52' => 'Ми-26',

			'Ан-22' => 'F-16',

			' миль' => ' вёрст',
			' миля' => ' верста',
			' Иран' => ' Ватикан',
			' Ирак' => ' Учкудук',
			'КНДР' => 'США',
			'Сочи' => 'Нью-Васюки',
			'ИБА' => 'КГБ',
			'1 апрел' => '59 феврал',
			'1-е апрел' => '59-е феврал',
			'1е апрел' => '59е феврал',
			'1-го апрел' => '59-го феврал',
			'апрел' => 'фепрел',
			'тоталитарн' => 'либеральн',
			'диктатур' => 'аспирантур',
			'1917' => '2017',
			'форум' => 'базар',
			' депозит' => ' реквизит',
			'кошк' => 'мышк',
			'Кошк' => 'Мышк',
			' кош' => ' мыш',
			' Кош' => ' Мыш',
			'собак' => 'кошк',
			' да ' => ' может быть ',
			' да,' => ' может быть,',
			' нет ' => ' точно ',
			' нет,' => ' точно,',
			'ВМС' => 'РХБЗ',
			' 2013' => ' 2017',
			'.2013' => '.2017',
			'Россию' => 'Солнечную систему',
			'России' => 'Солнечной системы',
			'пиво' => 'кофе',
			'Пиво' => 'Кофе',
			'Европ' => 'Жмеринк',
			'Путин' => 'Талейран',
			'Израиль' => 'Путивль',
			'Израиле' => 'Путивле',
			'Китай' => 'Ривендел',
			'китай' => 'ривендел',
			'Китаю' => 'Ривенделу',
			'орск' => 'ирск',

			'Море' => 'Пиво',
			'море' => 'мире',

//			'ракет' => 'букет',
//			'Ракет' => 'Букет',
			'двигател' => 'преобразовател',
			'Двигател' => 'Преобразовател',

			'РПЦ' => 'ВДНХ',
			'экономи' => 'логи',
			'Экономи' => 'Логи',

			'Моск' => 'Морд',

			'лодк' => 'будк',
			' лод' => ' буд',
			'корабл' => 'дирижабл',
			'корабeл' => 'дирижабел',
			'рыб' => 'годзил',
			'крейсер' => 'драйвер',
			'гидро' => 'алко',
			'СССР' => 'РПЦ',
			'Паровоз' => 'Луноход',
			'паровоз' => 'луноход',
			'светофор' => 'Люцифер',
			'Светофор' => 'Люцифер',
			'тепловоз' => 'марсоход',
			'Тепловоз' => 'Марсоход',
			'неоднород' => 'высокород',
			'полити' => 'каллиграфи',
			'Полити' => 'Каллиграфи',
			'выбор' => 'указ',
			'Выбор' => 'Указ',
			'танк' => 'бутик',
			'Танк' => 'Бутик',
			'стори' => 'стери',
			'фотограф' => 'биограф',
			'Фотограф' => 'Биограф',
			'Сири' => 'Мори',
			'Навальн' => 'Опальн',
//			'Коре' => 'Меганези',
			' война' => ' операция по принуждению к миру',

			'Интернет' => 'Фидонет',
			'интернет' => 'фидонет',
			'будуще' => 'внезапно',
			'Будуще' => 'Внезапно',
			'будущи' => 'внезапны',
			'Будущи' => 'Внезапны',

			'Сухой' => 'Морской',
//			'флот' => 'ход',
//			'Флот' => 'Ход',
			'Голосов' => 'Колесов',
			'голосов' => 'колесов',
			'Голосу' => 'Колесу',
			'голосу' => 'колесу',

			'Кадыров' => 'Сидоров',

			'Бяк' => 'Бук',
			'Брон' => 'Земл',
			'брон' => 'земл',
			'картин' => 'икон',
			'Картин' => 'Икон',
			'флуд' => 'блуд',
			'Флуд' => 'Блуд',

			'Праздник' => 'Субботник',
			'праздник' => 'субботник',

			'юрист' => 'юморист',
			'Юрист' => 'Юморист',

			'Модера' => 'Морализа',
			'модера' => 'морализа',

			'Координ' => 'Ликвид',
			'координ' => 'ликвид',
			'блонди' => 'боло',
			'Блонди' => 'Боло',
			'либер' => 'инферн',
			'Либер' => 'Инферн',
			'поезд' => 'крейсер',
			'Поезд' => 'Крейсер',

			'страна' => 'планета',
			'Страна' => 'Планета',

			'пенс' => 'аттестац',
			'Пенс' => 'Аттестац',

			'Патриар' => 'Падиша',
			'патриар' => 'падиша',

			'Пароход' => 'Луноход',
			'пароход' => 'луноход',

			'компьютер' => 'звездочёт',
			'Компьютер' => 'Звездочёт',
		);

		return str_replace(array_keys($replace), array_values($replace), $text);
	}
}
