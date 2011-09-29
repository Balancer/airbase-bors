<?php

// Вывод списка топиков на манер форума

class balancer_board_module_topics extends bors_module
{
	function body_data()
	{
		return array_merge(parent::body_data(), array(
			'topics' => $this->arg('topics'),
			'real_topic_visits' => $this->arg('real_topic_visits'), // Учитывать точное время визита, считать, что не посещалось, если нет в таблице визитов. Иначе — считаются только обновления с последней сессии.
		));
	}
}
