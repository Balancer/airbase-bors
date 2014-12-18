$('.theme_answer_button').click(function(e){
	notice = $('#answer_notice_content').html()
	if(!notice)
		return true;

	url = $(this).attr('href')

	e.preventDefault()

	swal({
		title: "Возможная сложность с выбором темы",
		text: notice + "<br/><br/>Для ответа в другую тему можете перейти прямо по ссылке выше",
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
