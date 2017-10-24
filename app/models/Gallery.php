<?php

// require_once 'BaseModel file';
namespace Models;

class Gallery extends BaseModel
{
	public $tableName = 'gallery';
	public function __construct()
	{
		parent::__construct('gallery');
	}

	public function getUrl()
	{
		return get_content_url('gallery/'.$this->image);
	}

	public function getPath()
	{
		return get_content_dir('gallery/'.$this->image);
	}


}