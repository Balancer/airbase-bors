<?
    show_guest_info();

    function show_guest_info()
    {
		$hts = new DataBaseHTS();
		
		$topics_per_page = 2;

		$out = <<< __EOT__
<div id="main-wide">
<h3>Гостиная@Aviaport.ru</h3>
__EOT__;
		echo ec($out);

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
		
			$go = ec("Перейти к обсуждению");
			$det = ec("подробнее");
			$alt = ec("Гостиная@Aviaport.ru: ").$topic['title'];

			echo <<< __EOT__
<div class="person">
<a title="{$topic['title']}" class="person-photo" href="#"><img src="{$topic['icon_image']}" width="{$topic['icon_width']}" height="{$topic['icon_height']}" alt="" /></a>
<h4><a title="Перейти к обсуждению" href="#">Владимир Путин</a></h4>
<p>{$topic['description']}</p>
<p><a title="$go" href="{$topic['tid']}">$det</a></p>
</div>

__EOT__;
		}		
    }
?>
