<?php

class forum_js_topvisits extends base_js
{
	function local_data()
	{
		$top_ids = $this->db('punbb')->select_array('topic_visits', 'topic_id, SUM(count) as total, MIN(first_visit) AS start, MAX(last_visit) AS stop', array(
			'group' => 'topic_id',
		));

		$top_ids = array_filter($top_ids, create_function('$x', 'return $x["total"]>20 && $x["stop"] > '.(time()-86400*14).';'));
		usort($top_ids, create_function('$x, $y', 'return $x["total"]/($x["stop"]-$x["start"]+1) < $y["total"]/($y["stop"]-$y["start"]+1);'));
		$top_ids = array_slice($top_ids, 0, 20);

		$ids = array();
		foreach($top_ids as $x)
		{
//			echo "{$x['topic_id']}: ".($x["total"]/($x["stop"]-$x["start"]+1)*3600)." ({$x['total']} from ".date('r', $x['start'])." to ".date('r', $x['stop']).")<br/>";
			$ids[] = $x['topic_id'];
		}

		$topics = objects_array('forum_topic',array(
			'id IN' => $ids,
			'by_id' => true,
		));

		$top = array();
		foreach($top_ids as $x)
			$top[] = array(
				'topic' => $topics[$x['topic_id']],
				'total' => $x['total'],
				'start' => $x['start'],
			);
		
		return array('top' => $top);
	}

	function cache_static() { return rand(3600,7200); }
}
