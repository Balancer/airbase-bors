<?php

class balancer_board_topics_users extends balancer_board_page
{
	function title() { return 'Статистика участников темы ' . $this->topic()->title(); }

	var $nav_name = 'участнки';

	function auto_objects()
	{
		return array_merge(parent::auto_objects(), array(
			'topic' => 'balancer_board_topic(id)',
		));
	}

	function body_data()
	{
		$users = bors_find_all('balancer_board_user', array(
			'*set' => 'count(*) as num_posts',
			'inner_join' => [
				'balancer_board_posts_pure ON balancer_board_posts_pure.owner_id = balancer_board_user.id',
				'balancer_board_topic ON balancer_board_posts_pure.topic_id = balancer_board_topic.id',
			],
			'topic_id' => $this->id(),
			'group' => 'balancer_board_posts_pure.owner_id',
			'by_id' => true,
		));

		include_once("inc/datetime.php");
		foreach($users as $uid => $u)
		{
			if($v = balancer_board_topics_visit::last_topic_user_visit($uid, $this->id()))
			{
				$users[$uid]->set_attr('last_topic_visit', airbase_time($v->modify_time()));
				$users[$uid]->set_attr('last_topic_visit_time', $v->modify_time()*10);
			}
			else
			{
				$users[$uid]->set_attr('last_topic_visit', '');
				$users[$uid]->set_attr('last_topic_visit_time', $users[$uid]->last_visit_time());
			}
		}

		uasort($users, function($x, $y) {
			return $x->last_topic_visit_time() - $y->last_topic_visit_time();
		});

		return array_merge(parent::body_data(), [
			'users' => $users,
		]);
	}
}
