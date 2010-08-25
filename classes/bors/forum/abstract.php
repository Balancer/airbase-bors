<?php
	
class forum_abstract extends base_page_db
{

	function main_db() { return config('punbb.database', 'punbb'); }

		function template() { return 'forum/_header.html'; }
		
        function cache_life_time()
        {
            $GLOBALS['cms']['cache_disabled'] = true;
            return -1;
        }
}
