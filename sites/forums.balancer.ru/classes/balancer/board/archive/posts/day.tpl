<div class="container-fluid">
	<div class="row-fluid">
		<div class="span9">

{$this->layout()->mod('pagination')}

{foreach $posts as $p}
{if $use_bootstrap}
	{$p->view()->template('bootstrap')->html()}
{else}
	{module class="balancer_board_module_post" post=$p}
{/if}
{/foreach}

{$this->layout()->mod('pagination')}

		</div>
	</div>
</div>
