{form act="add_request" th="Добавить запрос"}
{input name="user_id" value="" label="ID пользователя"}
{dropdown name="rpg_request_id" value="" list=['balancer_board_rpg_request_levelup'=>'Поднятие уровня'] label="Тип запроса"}
{submit label="Добавить"}
{/form}
