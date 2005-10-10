<?
    show_guest_info();

    function show_guest_info()
    {
		$hts = new DataBaseHTS();
		
		$topics_per_page = 2;

		foreach($hts->dbh->get_array("
			SELECT 	c.value as tid,
					t.value as title,
					ct.value as create_time,
					d.value as description,
					an.value as author_name,
					aid.value as author
			FROM hts_data_child c
				LEFT JOIN hts_data_create_time ct ON (c.value = ct.id)
				LEFT JOIN hts_data_title t ON (c.value = t.id)
				LEFT JOIN hts_data_description d ON (c.value = d.id)
				LEFT JOIN hts_data_author_name an ON (c.value = an.id)
				LEFT JOIN hts_data_author aid ON (c.value = aid.id)
			WHERE c.id LIKE 'http://{$GLOBALS['cms']['conferences_host']}/{$GLOBALS['cms']['conferences_path']}/guest/' 
			 	AND c.value LIKE 'http://{$GLOBALS['cms']['conferences_host']}/{$GLOBALS['cms']['conferences_path']}/guest/thread%'
			ORDER BY ct.value DESC
			LIMIT $topics_per_page;", false) as $topic)
		{
			include_once("funcs/datetime.php");
		
			$date = short_time($topic['create_time']);

			echo <<< __EOT__
<table>
<tr><td><b>$date</b>&nbsp;<a href="{$topic['tid']}">{$topic['author_name']}</a></b></td></tr>
<tr><td>{$topic['description']}</td></tr>
<tr><td><div class="red-right-link"><a href="{$topic['tid']}#reply_form" style="color: #ff9000;">Задать вопрос</a></div></td></tr>
</table>
__EOT__;
		}		
    }
?>
