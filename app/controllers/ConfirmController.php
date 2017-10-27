<?php 

use Models\UserConfirm;
class ConfirmController extends Controller
{
	protected $expireTime = 864000;
	function __construct()
	{
		Html::title("HoDuong.com");
    }
    
    public function makeConfirm($email,$type=1,$time=null,$option_value='')
    {
        $expireTime = time()+($time?$time:$this->expireTime);
        $expire_date = date("Y-m-d H:i:s",$expireTime);
        if($uc = UserConfirm::where('email',$email)->andWhere('cfm_type',$type)->first()){
            $uc->delete();
        }else{
            $uc = new UserConfirm();
        }
        $token = md5($email.uniqid($type));
        $uc->email = $email;
        $uc->token = $token;
        $uc->expire_date = $expire_date;
        $uc->cfm_type = $type;
        $uc->option_value = $option_value;
        if($uc->insert()) return $token;
        return false;
    }

	public function confirm()
    {
        $req = request();
        if($type = $req->get('type') && $token = $req->get('token')){
            if($ucf = UserConfirm::where('type',$type)->andWhere('token',$token)->first()){
                $ucf->delete();
                if(time()<=strtotime($ucf->expire_date)){
                        _session('token',$token);
                        _session('cf_email',$ucf->email);
                        _session('cf_type',$ucf->cfm_type);                        
                        _session('cf_opt_val',$ucf->option_value);                        
                        $type = $ucf->cfm_type;
                        $redirect_list = array('/','reset-password','verify-account','shop/ordered');
                        if(!isset($redirect_list[$type])) $type = 0;
                        $redirect = $redirect_list[$type];
                        redirect(url($redirect));
                }else{
                    $this->alert('Liên kết đã quá thời hạn');
                }
            }else{
                $this->alert('Liên kết xác thực không hợp lệ');
            }
        }else{
            $this->alert('Truy cập không hợp lệ');
        }
    }

}
 ?>