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

	function render_dompdf($html, $target_file)
	{
		$dompdf = new \Dompdf\Dompdf();
		$dompdf->loadHtml($html);

		//$dompdf->setPaper('A4', 'landscape');
		// Render the HTML as PDF
		$dompdf->render();
		// Output the generated PDF to Browser
		file_put_contents($target_file, $dompdf->output());
	}

	function render_knp($html, $target_file, $cover_url)
	{
		$bin = config('bin.wkhtmltopdf', COMPOSER_ROOT.'/vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64');
		$snappy = new \Knp\Snappy\Pdf($bin);

		$snappy->setOption('disable-javascript', true);
		$snappy->setOption('no-background', true);
		$snappy->setOption('cover', $cover_url);
		$snappy->setOption('print-media-type', true);
		$snappy->setOption('encoding', 'utf-8');

		$snappy->generateFromHtml($html, $target_file);
	}

	function render_legacy($url, $target_file)
	{
		$bin = config('bin.wkhtmltopdf', COMPOSER_ROOT.'/vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64');

		if($args = config('bin.wkhtmltopdf.args'))
			$bin .= " $args ";

		$log_put = config('debug_hidden_log_dir').'/pdf-maker.log';

//		bors_debug::syslog('--pdf', "$bin cover $cover_url $helper_url $target_file");
		system("$bin cover $cover_url $helper_url $target_dir/$target_name &> $log_put");

//		$pdf = file_get_contents("$target_dir/$target_name");
//		unlink("$target_dir/$target_name");
//		return $pdf;
	}

	function render($object)
	{
		\Tracy\Debugger::$strictMode = TRUE;
		\Tracy\Debugger::enable(\Tracy\Debugger::DEVELOPMENT);

		$topic = $object->topic();
		$helper_url = bors_load('balancer_board_topics_pdfHelper', $this->id())->url();
		$cover_url = bors_load('balancer_board_topics_pdfCover', $this->id())->url();
//		echo $this->url();
		$data = url_parse($this->url());
		$target_dir = preg_replace('!/htdocs/!', '/htdocs/cache-static/', dirname($data['local_path']));
		$target_name = basename($data['local_path']);
		mkpath($target_dir);
//		header("X-PDF-INFO: /usr/local/bin/wkhtmltopdf-amd64 --cover $cover_url $helper_url $target_dir/$target_name");

		$target_file = "$target_dir/$target_name";

		if(file_exists($target_file) && filesize($target_file))
			return go($data['uri']);

		$html = file_get_contents($helper_url);

//		echo "Loaded:".strlen($html);

//		$this->render_legacy($helper_url, $target_file);
//		$this->render_dompdf($html, $target_file);
//		$this->render_knp($html, $target_file, $cover_url);

		if(!file_exists($target_file) || !filesize($target_file))
			return false;

		return NULL;
	}

	function pre_show()
	{
//		@header('Content-Type: application/pdf');

		$ret = $this->render($this);

		if($ret === false)
			return bors_message("Ошибка создания PDF.");

		if($ret === true)
			return true;

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
