<?php

class balancer_board_user_unittest extends PHPUnit_Framework_TestCase
{
    public function test_balancer_board_user()
    {
		$user = bors_load('balancer_board_user', 10000); // Грузим Balancer'а
        $this->assertNotNull($user);

        $this->assertEquals($user->title(), 'Balancer');

		// Предупреждений у Balancer'а должно быть 0.
		$this->assertEquals(0, $user->warnings_in(25));

		// Ищем последний штраф за постинг
		$post = bors_load('balancer_board_post', 2197802); // id от балды. Нам нужен только class_id, но почему бы не проверить и загрузку post'а?
        $this->assertNotNull($post);

		$class_id = $post->class_id();
        $this->assertGreaterThan(0, $class_id);

		// Собственно, загрузка штрафа.
		$warn = bors_find_first('airbase_user_warning', array('order' => '-create_time', 'warn_class_id' => $class_id));
        $this->assertNotNull($warn);

		$user = $warn->user();
        $this->assertNotNull($user);

		$post = $warn->target();
        $this->assertNotNull($post);

		$topic = $post->topic();
        $this->assertNotNull($topic);

		$forum = $topic->forum();
        $this->assertNotNull($forum);
		$warns_in_forum = $user->warnings_in($forum->id());
        $this->assertGreaterThan(0, $warns_in_forum);
    }
}
