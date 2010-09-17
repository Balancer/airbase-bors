<?php

class balancer_akismet extends base_object
{
	private $akismet;

	function __construct()
	{
		require_once(config('akismet_include'));
		$WordPressAPIKey = config('akismet_api_key');
		$MyBlogURL = 'http://balancer.ru/';

		$this->akismet = new Akismet($MyBlogURL ,$WordPressAPIKey);
	}

	static function factory() { return new balancer_akismet(); }

	function classify($object)
	{
		if(!$object)
			return NULL;

		if(!$object->source())
			return NULL;

//		$this->akismet->setCommentAuthor($object->author_name());
//$akismet->setCommentAuthorEmail($post->);
//$akismet->setCommentAuthorURL($url);
		$this->akismet->setCommentContent($object->source());
		$this->akismet->setPermalink($object->url());
		return $this->akismet->isCommentSpam();
	}

	function submit_spam($object)
	{
		if(!$object)
			return NULL;

		if(!$object->source())
			return NULL;

		$this->akismet->setCommentAuthor(object_property($object->owner(), 'title'));
		$this->akismet->setCommentAuthorEmail(object_property($object->owner(), 'email'));
//		$akismet->setCommentAuthorURL($url);
		$this->akismet->setCommentContent($object->source());
		$this->akismet->setPermalink($object->url());

		debug_hidden_log('akismet-submit-spam', "{$object->debug_title()}: {$object->source()}");

		return $this->akismet->submitSpam();
	}

	function submit_ham($object)
	{
		if(!$object)
			return NULL;

		if(!$object->source())
			return NULL;

		$this->akismet->setCommentAuthor(object_property($object->owner(), 'title'));
		$this->akismet->setCommentAuthorEmail(object_property($object->owner(), 'email'));
//		$akismet->setCommentAuthorURL($url);
		$this->akismet->setCommentContent($object->source());
		$this->akismet->setPermalink($object->url());

		debug_hidden_log('akismet-submit-ham', "{$object->debug_title()}: {$object->source()}");

		return $this->akismet->submitHam();
	}
}
