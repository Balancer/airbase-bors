{if $me}
top.me_can_move={$me->group()|get:can_move}
top.me_is_coordinator={$me->group()|get:is_coordinator}
{else}
top.me_can_move=0
top.me_is_coordinator=0
{/if}
