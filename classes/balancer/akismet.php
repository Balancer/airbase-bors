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
}
