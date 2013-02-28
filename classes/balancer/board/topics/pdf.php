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
	function is_loaded() { return $this->topic() != NULL; }

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
//		header("X-PDF-INFO: /usr/local/bin/wkhtmltopdf-amd64 --cover $cover_url $helper_url $target_dir/$target_name");

		$bin = config('bin.wkhtmltopdf', '/usr/bin/wkhtmltopdf');

		if($args = config('bin.wkhtmltopdf.args'))
			$bin .= " $args ";

		$log_put = config('debug_hidden_log_dir').'/pdf-maker.log';

		debug_hidden_log('--pdf', "$bin cover $cover_url $helper_url $target_dir/$target_name");
		system("$bin cover $cover_url $helper_url $target_dir/$target_name &> $log_put");

//		$pdf = file_get_contents("$target_dir/$target_name");
//		unlink("$target_dir/$target_name");
//		return $pdf;

		return NULL;
	}

	function pre_show()
	{
//		@header('Content-Type: application/pdf');

		$this->render($this);

		@header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
		@header('Cache-Control: no-store, no-cache, must-revalidate'); 
		@header('Cache-Control: post-check=0, pre-check=0', false); 
		@header('Pragma: no-cache');

//		$pdf = file_get_contents("$target_dir/$target_name");
//		unlink("$target_dir/$target_name");
//		return $pdf;

		return go($this->url(), true);
	}

	function url() { return preg_replace('/^(.+)\.html$/', '$1.'.($this->topic()->modify_time()%10000).'.pdf', $this->topic()->url()); }

//	function total_pages() { return 1; }
}
