<?php

class balancer_board_avatar extends base_object_db
{
	function storage_engine() { return 'bors_storage_mysql'; }
	function db_name() { return 'AB_FORUMS'; }
	function table_name() { return 'avatars'; }
	function table_fields()
	{
		return array(
			'id',
			'owner_id' => 'user_id',
			'image_class_name',
			'image_id',
			'image_original_url',
			'image_file',
			'image_html',
			'title',
			'signature',
			'signature_html',
			'create_time',
		);
	}

	function auto_objects()
	{
		return array_merge(parent::auto_objects(), array(
			'owner' => 'balancer_board_user(owner_id)',
		));
	}

	function auto_targets()
	{
		return array_merge(parent::auto_targets(), array(
			'image_raw' => 'image_class_name(image_id)',
		));
	}

	function image()
	{
		$image = $this->image_raw();
		if($image)
			return $image;

		$image = $this->owner()->old_avatar_image();
		if($image)
		{
			$this->set_image_class_name($image->class_name(), true);
			$this->set_image_id($image->id(), true);
			$this->set_image_original_url($image->full_url(), true);
			$this->set_image_file($image->full_file_name(), true);
		}

		return $image;
	}

	static function make($user_id)
	{
		if(!$user_id)
			return NULL;

		// Попробуем найти уже зарегистрированный аватар пользователя.
		$avatar = bors_find_first('balancer_board_avatar', array(
			'owner_id' => $user_id,
			'order' => '-create_time',
		));

		if($avatar)
			return $avatar;

		$owner = bors_load('balancer_board_user', $user_id);
		if(!$owner)
			return NULL; //TODO: вот тут и нужно приделывать граватары всякие

		$image = $owner->old_avatar_image();
		if(!$image)
			return NULL;

		// Всё, картинка есть, можно регистровать новый аватар
		return object_new_instance('balancer_board_avatar', array(
			'owner_id' => $user_id,
			'image_class_name' => $image->class_name(),
			'image_id' => $image->id(),
			'image_original_url' => $image->full_url(),
			'image_file' => $image->full_file_name(),
//			'image_html',
			'title' => $owner->title(),
			'signature' => $owner->signature(),
//			'signature_html',
//			'create_time',
		));
	}
}
