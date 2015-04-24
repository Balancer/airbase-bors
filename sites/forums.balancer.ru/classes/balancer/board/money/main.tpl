{form subaction="move" th="Перевод средств другому пользователю (комиссия ☼1)"}
{input name="amount" value="" th="Количество"}
{select2 name="target_user_id" th="Пользователь" main_class="balancer_board_user" value="" title_field="username" order="username" search_fields="username"}
{submit value="Отправить" type="button" css_class="btn"}
{/form}

{form subaction="award" th="Выдать пользователю поощрительный балл (снять штраф, 1 балл стоит ☼500)"}
{input name="amount" value="" th="Количество поощрительных баллов"}
{textarea name="comment" value="" th="Комментарий" rows="2"}
{select2 name="target_user_id" th="Пользователь" main_class="balancer_board_user" value="" title_field="username" order="username" search_fields="username"}
{submit value="Добавить" type="button" css_class="btn"}
{/form}
