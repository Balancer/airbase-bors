<?php

class balancer_board_topics_pdf extends bors_object
{
	function auto_objects()
	{
		return array(
			'topic' => 'balancer_board_topic(id)',
		);
	}

	function can_be_empty() { return false; }
	function loaded() { return $this->topic() != NULL; }

	function render_engine() { return $this; }

	function render($object)
	{
		$topic = $object->topic();
		$helper_url = object_load('balancer_board_topics_pdfHelper', $this->id())->url();
		$cover_url = object_load('balancer_board_topics_pdfCover', $this->id())->url();
//		echo $this->url();
		$data = url_parse($this->url());
		$target_dir = dirname($data['local_path']);
		$target_name = basename($data['local_path']);
		mkpath($target_dir);
		system("/usr/local/bin/wkhtmltopdf-amd64 -b --cover $cover_url $helper_url $target_dir/$target_name");

		@header('Content-Type: application/pdf');
		return file_get_contents("$target_dir/$target_name");
//		return 'done';
	}

	function url() { return preg_replace('/^(.+)\.html$/', '$1.'.($this->topic()->modify_time()%10000).'.pdf', $this->topic()->url()); }

//	function total_pages() { return 1; }
}
