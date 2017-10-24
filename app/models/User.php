<?php

namespace Models;

class User extends BaseModel
{
	public $tableName = 'users';
	public function __construct()
	{
        parent::__construct('users');
    }

    public function insert($data = null) {
        if(!is_array($data) || !isset($data['created_at']) || !$data['created_at'] || !isset($this->created_at)){
            $this->created_at = date('Y-m-d H:i:s');
        }
        if(!is_array($data) || !isset($data['updated_at']) || !$data['updated_at'] || !isset($this->updated_at)){
            $this->updated_at = date('Y-m-d H:i:s');
        }
        parent::insert($data);
    }

    
    public function getAvatar(){
		
        $f = $this->avatar ? $this->avatar : 'profile.png';
        return get_content_url('users/avatar/'.$f);
    }


	public function countCategory()
    {
        $cats = Category::where('created_by',$this->id)->count();
        return $cats;
    }

    public function getCategory()
    {
        $cats = Category::where('created_by',$this->id)->get();
        return $cats;
    }

    public function countPost()
    {
        $posts = Post::where('posted_by',$this->id)->count();
        return $posts;
    }

    public function getPost()
    {
        $posts = Post::where('posted_by',$this->id)->get();
        return $posts;
    }

    public function getOwnProduct($args=null){
		$products = Product::where('created_by', $this->id)->get('*',$args);
		return $products;
	}
	public function countOwnProduct($args=null){
		$products = Product::where('created_by', $this->id)->count($args);
		return $products;
	}

		

    public static function checkLogin(){
        $stt = false;
		$args = null;
		$model = new static();
		$mask = $model->mask();

        if($id = _session('userid')){
            $args = array($mask->idField=>$id);
        }
        elseif($token = _cookie('auth_token')){
            $args = array(
                'auth_token' => $token,
                'token_expire_date >=' => date("Y-m-d H:i:s")
            );
        }
        if($args && $ui = $mask->getOne('id',$args,get_class($model))){
            $stt=true;
            if(!_session('userid')){
                _session('userid',$ui->id);
            }
        }
        return $stt;
    }
    
    public static function login($username=null,$password=null, $keep=null){
		$model = new static();
		$mask = $model->mask();

		$stt = false;
        if(!is_null($username) && !is_null($password)){
			$args = array('password' => md5($password));
			if(!filter_var($username,FILTER_VALIDATE_EMAIL)){
				$args['username'] = $username;
			}else{
				$args['email'] = $username;
			}
            if($u = $mask->getOne('id',$args,get_class($model))){
                _session('userid',$u->id);
                $stt=true;
                if($keep){
                    $auth_token = md5($u->id.'_'.microtime(true));
                    $expire = time()+(3600*24*30);
                    $expire_date = date("Y-m-d H:i:s",$expire);
                    $data = array('auth_token'=>$auth_token, 'token_expire_date'=>$expire_date);
                    if($mask->update($data, 'id='.$u->id)){
                        _cookie('auth_token',$auth_token,$expire);
                    }
                }
            }
        }
        return $stt;
    }
    
    
    
    public static function logout(){
        if(_session('userid')){
            $auth_token = 'No Auth Token';
            $expire = time()-1;
            $expire_date = date("Y-m-d H:i:s",$expire);
            $data = array('auth_token'=>$auth_token, 'token_expire_date'=>$expire_date);
			$model = new static();
			$mask = $model->mask();
            if($mask->update($data, 'id='._session('userid'))){
                //code here
            }
        }
        unset($_SESSION['userid']);
        _cookie('auth_token',0,-3600*24*30);
		return true;
    }

    public static function checkEmail($email=null,$id=null){
		$stt = 0;
		$model = new static();
		$mask = $model->mask();
		
        if(!filter_var($email,FILTER_VALIDATE_EMAIL)) $stt = 0;
        elseif($u = $mask->getOne('id',array('email'=>$email),get_class($model))){
            if($id && $u->id==$id) $stt = 1;
        }
        else $stt = 1;
        return $stt;
    }
    public static function checkUsername($username=null,$id=null){
		$model = new static();
		$mask = $model->mask();
		
		$stt = 0;
        if(!preg_match('/^([a-zA-Z_])+([a-zA-Z0-9_\.])+$/',$username)) $stt = 0;
        elseif($u = $mask->getOne('id',array('username'=>$username), get_class($model))){
            if($id && $u->id==$id) $stt = 1;
        }else $stt = 1;
        return $stt;
    }

    public static function getCurrentLogin()
    {
        if(self::checkLogin()){
            return self::findOne(_session('userid'));
        }
        return null;
    }
}