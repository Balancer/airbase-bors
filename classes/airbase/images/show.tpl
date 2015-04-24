<a href="{$this->image_url()}"><img src="{$this->image_thumb_url('800x600')}" /></a><br/>
{if $image}
image_id={$image->id()}
{/if}

{if $objects}
<h2>Изображение используется в сообщениях:</h2>
<ul>
{foreach $objects as $x}
<li>{$x->titled_link()}</li>
{/foreach}
</ul>
{/if}