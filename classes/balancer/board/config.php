<?php

class balancer_board_config extends bors_config
{
	function object_data()
	{
		return array_merge(parent::object_data(), array(
			'template' => 'forum/_header.html',
		));
	}

	function view_data()
	{
		$view = $this->id();

		$user_bar = array(
			'Новое' => 'http://www.balancer.ru/forum/punbb/search.php?action=show_new',
			'Блог' => 'http://www.balancer.ru/user/'.bors()->user_id().'/blog/',
			'Избранное' => 'http://www.balancer.ru/users/favorites/',
			'Темы с участием' => 'http://www.balancer.ru/user/'.bors()->user_id().'/use-topics.html',
			'Сообщения' => 'http://www.balancer.ru/user/'.bors()->user_id().'/posts/',
			'Ответы Вам' => 'http://forums.balancer.ru/personal/answers/',
			'Репутация' => 'http://www.balancer.ru/user/'.bors()->user_id().'/reputation/',
			'Оценки' => 'http://www.balancer.ru/users/'.bors()->user_id().'/votes/',
			'Профиль пользователя' => object_property(bors()->user(), 'url'),
			'Профили браузера' => 'http://forums.balancer.ru/personal/clients/',
			'Выход'	=> "/?logout",
		);

		$nav_bar = array(
			'Форум' => array(
				'Правила' => 'http://forums.airbase.ru/guidelines/',
				'Помощь' => 'http://forums.balancer.ru/help/',
				'Сообщения за сутки' => 'http://www.balancer.ru/forum/punbb/search.php?action=show_24h',
				'Репутации пользователей' => 'http://www.balancer.ru/users/toprep/',
				'Оценки пользователей' => 'http://www.balancer.ru/tools/votes/',
				'Штрафы пользователей' => 'http://www.balancer.ru/users/warnings/',
				'Инструменты форумов' => 'http://forums.balancer.ru/tools/',
//				'Админка' => 'http://forums.balancer.ru/admin/',
			),
		);

		if(in_array($view->class_name(), array('balancer_board_topic', 'balancer_board_topics_view')))
			$nav_bar['Тема'] = array(
				'PDF-версия' => 'http://www.balancer.ru/2000/01/t'.$view->id().'--.pdf',
				'Версия для печати' => 'http://www.balancer.ru/2000/01/01/printable-65028--.html',
				'Печать текущей страницы' => 'http://www.balancer.ru/1973/10/tpc65028,'.$view->page().'--.html',
				'Блог' => 'http://www.balancer.ru/1973/10/t'.$view->id().'/blog',
				'Все картинки' => 'http://www.balancer.ru/1973/10/t'.$view->id().'/images',
				'Все видео' => 'http://www.balancer.ru/1973/10/t'.$view->id().'/video',
				'Все аттачи (вложения)' => 'http://www.balancer.ru/2000/01/t'.$view->id().'/attaches/',
				'Инструменты' => 'http://www.balancer.ru/forum/tools/topic/'.$view->id().'/',
				'Сбросить кеш темы' => 'http://www.balancer.ru/forum/tools/topic/'.$view->id().'/reload/',
			);

		return array_merge(parent::view_data(), array(
			'search_request_url' => 'http://www.balancer.ru/tools/search/result/',
			'project' => bors_load('balancer_board_project', NULL),
			'copyright_line' => '© Balancer 1998—'.date('Y'),
			'user_bar' => $user_bar,
			'nav_bar' => $nav_bar,
			'access_engine' => 'balancer_board_access_public',
		));
	}

	function page_data()
	{
		return array_merge(parent::page_data(), array(
		));
	}
}
