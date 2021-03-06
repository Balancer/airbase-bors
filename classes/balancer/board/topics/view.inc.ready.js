if(window.location.hash && (g=/^#(p\d+)$/.exec(window.location.hash)))
{
	h=g[1];
	if(!$('a[name='+h+']').length)
	{
		if(confirm("Сообщение "+h+" на странице не найдено. Вероятно, оно было перемещено. Перейти к этому сообщению?"))
			window.location = "http://www.wrk.ru/g/"+h+'?'
	}
}

$('.theme_answer_button').click(function(e){
	notice = $('#answer_notice_content').html()
	if(!notice)
		return true;

	url = $(this).attr('href')

	e.preventDefault()

	swal({
		title: "Возможная сложность с выбором темы",
		text: "<div style=\"text-align: left\">" + notice + "<br/>Для ответа в другую тему можете перейти прямо по ссылке выше</div>",
		type: "warning",
		showCancelButton: true,
		allowOutsideClick: true,
		confirmButtonColor: "#CC8",
		confirmButtonText: "Да, ответить в эту тему!",
		cancelButtonText: "Нет, я ещё подумаю",
		allowHTML: true,
	}, function(isConfirm) {
		if (isConfirm) {
			document.location = url
		}
	})

	return false
})
