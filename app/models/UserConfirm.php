<?php

namespace Models;

class UserConfirm extends BaseModel{
    public $tableName = 'user_confirm';
	public function __construct()
	{
        parent::__construct('user_confirm');
    }
}