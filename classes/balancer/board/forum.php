<?php

class balancer_board_forum extends forum_forum
{
	function class_title() { return ec('Форум'); }

	function class_title_dp() { return ec('форуму'); }
	function class_title_vp() { return ec('форум'); }
	function class_title_tpm() { return ec('форумами'); }

	function extends_class_name() { return 'forum_forum'; }

	function last_topics($limit)
	{
		return bors_find_all('balancer_board_topic', array(
			'forum_id' => $this->id(),
			'order' => '-last_post_create_time',
			'limit' => $limit,
		));
	}

	function full_name($forums = NULL, $cats = NULL)
	{
		$result = array();
		$current_forum = $this;
		do {
			$result[] = $current_forum->nav_name();
			if($parent = $current_forum->parent_forum_id())
				$current_forum = $forums ? $forums[$parent] : bors_load('balancer_board_forum', $parent);
		} while($parent);

		$cat = $cats ? $cats[$current_forum->category_id()] : $current_forum->category();

		return join(' « ', $result).' « '.$cat->full_name();
	}

	function image_code($geo = '64x64(up,crop)')
	{
		if($i = $this->image())
			return $i->thumbnail($geo)->html_code();

		return NULL;
	}

	function item_list_admin_fields()
	{
		return array(
			'image_code()' => '',
			'admin()->imaged_titled_link()' => ec('форум'),
			'num_topics' => ec('Число тем'),
			'num_posts' => ec('Число сообщений'),
			'description' => ec('Описание'),
			'redirect_url' => ec('Перенаправление'),
			'id' => 'ID',
		);
	}

	function editor_fields_list()
	{
		return array(
			ec('Название') => 'title',
			ec('Описание') => 'description',
			ec('Родительский форум') => array('property' => 'parent_forum_id', 'class' => 'balancer_board_forum', 'have_null' => true),
			ec('Категория') => array('property' => 'category_id', 'class' => 'balancer_board_category'),
			ec('Порядок сортировки') => 'sort_order',
			ec("Теги\nчерез запятую") => 'keywords_string',
			ec('Адрес перенаправления') => 'redirect_url',
			ec('Открытый доступ') => 'is_public',
			'Изображение' => 'image_id',
		);
	}

	function admin_url()
	{
		return 'http://forums.balancer.ru/_bors/admin/edit-smart/?object='.$this->internal_uri_ascii(); 
	}

	function admin_parent_url() { return 'http://forums.balancer.ru/admin/forums/'; }

    function upload_image_file2($file, $data)
    {
		var_dump($file, $data);
        $id = intval($data['object_id']);
        $image = bors_load('balancer_board_image', $id);

//        foreach(bors_find_all('bors_image_thumb', array("id LIKE '$id%'")) as $thumbnail)
//            $thumbnail->delete();

//        @unlink($image->file_name_with_path());
  //      if($image->full_file_name())
//            @unlink($image->full_file_name());
//        @rmdir($image->image_dir());

        $file['no_subdirs'] = true;
        echo '+1';
        $image->upload($file, $image->relative_path());
         exit();
    }

    function upload_image_file(&$file, &$data)
    {
        if(!$file['tmp_name'])
            return;

        $img = object_new_instance('balancer_board_image');
        $img->upload($file, 'images/forums/logos');

        $this->set_image_id($img->id(), true);
        unset($data['default_image_id']);
//        var_dump($img->id()); exit();
    }

}
