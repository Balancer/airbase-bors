function apfgp(checkbox, post_id) {
	var posts = readCookie('selected_posts', '').split(/,/)
	removeArrayItems(posts, post_id)

	if(checkbox.checked)
		posts.push(post_id)

	createCookie('selected_posts', posts.join(','), 1)
	return true
}
