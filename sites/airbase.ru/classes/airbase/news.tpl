{foreach $this->news() as $x}
<div class="post airbase-news">
<h2><a href="{$x.url}">{$x.title}</a></h2>
{$x.content}
</div>
{/foreach}
