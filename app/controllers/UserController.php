<?php
/**
* UserController
*/

use Models\User;
use Models\Category;
use Models\Post;

use Models\UserConfirm;

use Extensions\CubePaging;
use Extensions\CubeHtmlMenu;


class UserController extends Controller
{
    protected $is_login = false;
    protected $model = null;
    public $user = null;
    protected $formData = null;
	function __construct()
	{
        $this->model = new User();
        $this->checkLogin();
        Html::title("HoDuong.com");
    }
    
    public function isLogin()
    {
        return $this->is_login;
    }

    public function checkLogin(){
        $stt = User::checkLogin();
        $this->is_login = $stt;
        if($stt) $this->user = $this->model->getCurrentLogin();
        return $stt;
    }

    public function profile($user = null, $act=null)
    {
        $this->setViewPath('profile');
        if(!$user){
            if(!$this->checkLogin())
                $url = get_home_url('login?next='.get_current_url(true));
            else
                $url = url('profile-action',$this->user->username,'info');
            redirect($url);
        }
        if($u = $this->model->first('*',"username=$user")){
            $this->share('profile',$u);
            if(in_array($act,array('update','account','password'))){
                if(!$this->user || ($this->user->level < 3 && $this->user->id != $u->id)){
                    $this->alert('Bạn không có quyền xem trang này','warning');
                    die;
                }
                
                if($act == 'update'){
                    $this->profileUpdateInfo($u);
                }
                elseif($act == 'account'){
                    $this->profileUpdateAccount($u);
                }
                elseif($act == 'password'){
                    $this->profileUpdatePassword($u);
                }
    
            }
            else{
                $this->profileInfo($u);
            }
        }else{
            $this->alert('Người dùng này ko tồn tại','warning');
        }
        
    }
    
    protected function profileInfo($profile)
    {
        //use_model('Category,Post');
        
        $cate_total = Category::where('created_by',$profile->id)->count();
        $post_total = Post::where('posted_by',$profile->id)->count();
        $this->share(compact('profile','cate_total','post_total'));
        $this->form('info',null,'name,birth_date,gender','form/user');
        
        
    }
    
    protected function profileUpdateInfo($user)
    {
        $data = $user;
        $req = request();
        $errors = array();
        if($req->isPost()){
            $data = $req->post();
            $formData = $user->formData($data);
            $formData->setErrorMessageFile('form/errors');
            $formData->setFieldsText('form/user');
            $validate = array(
                'name' => array('name','len >= 2'),
                'gender' => 'is_numeric',
                'birth_date' => 'is_date'
            );
            $dataAccept = $formData->validate($validate);
            if(count($errors = $formData->getAllMessage()) == 0){
                if($file = $req->file('avatar')){
                    if($file->isImage()){
                        if(count($errors)==0){
                            $file->setFilename(uniqid());
                            if($file->move(get_content_dir('users/avatar/'))){
                                if($user->avatar){
                                    unlink(get_content_dir('users/avatar/'.$user->avatar));
                                }
                                $user->avatar = $file->getUploadedFilename();
                            }else{
                                $errors['avatar'] = "không thể upload avatar";
                            }
                        }
                    }else{
                        $errors['avatar'] = "avatar không hợp lệ";
                    }
                }
            }
            if(count($errors)==0){
                foreach($dataAccept as $key => $value){
                    $user->{$key} = $value;
                }
                if($this->updateUser($user)){
                    $this->alert('Cập nhật thông tin thành công');
                    die;
                }
            }
        }
        $this->share('errors',$errors);
        $list = 'name,birth_date,gender';
        $this->form('update-info',$data,$list,'form/user','Cập nhật');
        
    }
    
    protected function profileUpdateAccount($user)
    {
        $data = $user;
        
        $req = request();
        $errors = array();
        if($req->isPost()){
            
            $data = $req->post();

            $formData = $user->formData($data);
            $formData->setFieldsText('form/user');
            
            $formData->setErrorMessageFile('form/errors');

            $validate = array(
                'username' => 'accessName',
                'email' => 'unique_email',
                'password' => array('md5'=>$user->password)
            );
            
            $dataAccept = $formData->validate($validate,'update');
            
            if(count($errors = $formData->getAllMessage()) == 0){
                unset($dataAccept['password']);
                if($this->updateUser($user,$dataAccept)){
                    $this->alert('Cập nhật thông tin thành công');
                    die;
                }
            }
        }
        $this->share('pagetitle','Cập nhật thông tin tài khoản');
        $this->share('errors',$errors);
        $list = 'username,email,password';
        $this->form('form',$data,$list,'form/user','Cập nhật');
    }
    

    protected function profileUpdatePassword($user)
    {
        $data = $user;
        
        $req = request();
        $errors = array();
        if($req->isPost()){
            
            $data = $req->post();

            $formData = $user->formData($data);
            $formData->setFieldsText('form/user');
            
            $formData->setErrorMessageFile('form/errors');

            $validate = array(
                'oldpassword' => array('md5'=>$user->password),
                'newpassword' => 'len >= 6',
                'confirmpassword' => array('md5'=>md5($req->post('newpassword')))
            );
            
            $formData->validate($validate,'update');
            
            if(count($errors = $formData->getAllMessage()) == 0){
                $user->password = md5($req->post('newpassword'));
                if($this->updateUser($user)){
                    User::logout();
                    $this->alert('Thay đổi mật khẩu thành công! Vui lòng <a href="'.url('login').'">Đăng nhập</a> lại để tiếp tục.');
                    die;
                }
            }
        }

        $this->share('pagetitle','Thay đổi mật khảu');
        $this->share('errors',$errors);
        $list = 'oldpassword,newpassword,confirmpassword';
        $this->form('form',$data,$list,'form/user','Cập nhật');
        
    }

    protected function updateUser($user,$data=array())
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return call_user_func_array(array($user,'update'),array($data));
    }
    
    public function login()
	{
        $req = request();
        $user = $req->post('username');
        $pass = $req->post('password');
        $rmbm = $req->post('remember');
        $next = $req->request('next')?$req->request('next'):get_home_url();
        $mess = null;
        if($user && $pass){
            if(User::login($user,$pass,$rmbm)){
                redirect($next);
            }else{
                $mess = "Invalid Username or Password";
            }
        }elseif($user && !$pass){
            $mess = "Please enter your password";
        }
        $arr = array(
            'username'=>$user,
            'password'=>$pass,
            'remember'=>$rmbm,
            'message'=>$mess,
            'next' => $next
        );
        $this->view('login',$arr);
    }
    public function logout(){
        if($this->is_login){
            $auth_token = 'No Auth Token';
            $expire = time()-1;
            $expire_date = date("Y-m-d H:i:s",$expire);
            $data = array('auth_token'=>$auth_token, 'expire_date'=>$expire_date);
                    
            if($this->model->update($data, 'id='.$this->user->id)){
                //code here
            }
        }
        unset($_SESSION['userid']);
        _cookie('auth_token',0,-3600*24*30);
        $req = request();
        $next = $req->request('next')?$req->request('next'):get_home_url();
        redirect($next);

    }
    
    public function register()
    {
        if($this->is_login){
            header('location: '.get_home_url('profile/'.User::findOne(_session('userid'))->username));
            die;
        }

        $data = null;
        $req = request();
        $errors = array();
        if($req->isPost()){
            $data = $req->post();
            $user = new User();
            $formData = $user->formData($data);
            $formData->setFieldsText('form/user');
            $formData->setErrorMessageFile('form/errors');
            
            $validate = array(
                'name' => array('name','len >= 2'),
                'gender' => 'is_numeric',
                'birth_date' => 'is_date',
                'username' => array('len > 3','accessName'),
                'email' => 'unique_email',
                'password' => array('len >= 6','@md5'),
                'confirmpassword' => array('md5'=>md5($req->post('password')))
            );
            $dataAccept = $formData->applyAfterValidate($validate);
            if(count($errors = $formData->getAllMessage()) == 0){
                if($file = $req->file('avatar')){
                    if($file->isImage()){
                        if(count($errors)==0){
                            $file->setFilename(uniqid());
                            if($file->move(get_content_dir('users/avatar/'))){
                                
                                $user->avatar = $file->getUploadedFilename();
                            }else{
                                $errors['avatar'] = "không thể upload avatar";
                            }
                        }
                    }else{
                        $errors['avatar'] = "avatar không hợp lệ";
                    }
                }// update avatar
            }
            if(count($errors)==0){
                foreach($dataAccept as $key => $value){
                    $user->{$key} = $value;
                }
                if($user->insert()){
                    $this->alert('Đăng ký tài khoàn thành công! Vui lòng <a href="'.url('login').'">Đăng nhập</a> để tiếp tục.');
                    die;
                }
            }
        }
        $this->share('errors',$errors);
        $list = 'name,birth_date,gender,username,email,password,confirmpassword';
        $this->form('register',$data,$list,'form/user','Đăng ký');
        
    }

    public function forgot()
    {
        $req = request();
        $data = $req->post();
        $errors = array();
        if($email = $req->post('email')){
            $cf = new UserConfirm();
            $formData = $cf->formData($req->post());
            
            if($formData->validate(array('email'=>'email'))){
                if($user = User::where('email',$email)->first()){
                    $uc = get_controller('ConfirmController');
                    $token = $uc->makeConfirm($email,1);                    
                    $link = url('confirm?type=1&token='.$token);
                    $name = $user->name;
                    $message = $this->getView('mails/forgot-password',compact('name','email','link'));
                    $stt = sendmail($email,"Xác thực yêu cầu thay đổi mật khẩu",$message);
                }
                $this->alert('Đã gửi email xác thực. Hãy truy cập email của bạn để xác thực thay đổi mật khẩu');
                die;
            }else{
                $errors['email'] = 'Email khong hợp lệ';
            }
        }
        $formtitle = "Quên mật khẩu";
        $this->share(compact('errors','formtitle'));
        $list = 'email';
        $this->form('form',$data,$list,'form/user','Lấy lại mật khẩu');
    }






    public function resetPassword()
    {
        if(_session('token') && _session('cf_email') && _session('cf_type')==1){
            $req = request();
            $email = _session('cf_email');
            if($user = User::where('email',$email)->first()){
                
                $data = null;
                $errors = array();
                if($req->isPost()){
                    $data = $req->post();
                    $fd = $user->formData($data);
                    $fd->setFieldsText('form/user');
                    
                    $pass = md5($req->post('newpassword'));
                    $validate = array(
                        'newpassword'=>'len > 5',
                        'confirmpassword'=>array('len > 0', 'md5'=>$pass)
                    );
                    $fd->validate($validate);
                    if(count($errors = $fd->getAllMessage())==0){
                        $user->password = $pass;
                        $user->update();
                        _session('token',0);
                        _session('cf_userid',0);
                        _session('cf_type',0);
                        $this->alert('Thay đổi mật khẩu thành công! Vui lòng <a href="'.url('login').'">Đăng nhập</a> để tiếp tục.');
                        die;
                    }
                    

                }
                $this->share('formtitle','Thay đổi mật khảu');
                $this->share('errors',$errors);
                $list = 'newpassword,confirmpassword';
                $this->form('form',$data,$list,'form/user','Thay đổi mật khẩu');
                die;
            }
        }
        $this->alert('Truy cập không hợp lệ');
    }
}
?>