<?php

class balancer_board_admin_main extends base_page
{
	function title() { return ec('Управление форумами'); }
	function nav_name() { return ec('управление'); }
	function config_class() { return 'balancer_board_admin_config'; }

	function local_data()
	{
		$files = glob("/data/backup/punbb-*.sql.gz");
		function rsort_by_mtime($file1, $file2) { return filemtime($file2) - filemtime($file1); }
		usort($files,"rsort_by_mtime");

		return array_merge(parent::local_data(), array(
			'last_punbb_backup_mtime' => filemtime($files[0]),
		));
	}
}
