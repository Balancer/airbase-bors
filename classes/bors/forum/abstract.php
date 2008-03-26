<?php
	
class forum_abstract extends base_page_db
{

        function main_db_storage(){ return 'punbb'; }

		function template() { return 'templates/forum/_header.html'; }
		
        function cache_life_time()
        {
            $GLOBALS['cms']['cache_disabled'] = true;
            return -1;
        }
}
