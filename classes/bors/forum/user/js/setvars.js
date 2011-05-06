{if $me}
top.me_can_move={$me->group()|get:can_move|intval}
top.me_is_coordinator={$me->group()|get:is_coordinator|intval}
{else}
top.me_can_move=0
top.me_is_coordinator=0
{/if}
