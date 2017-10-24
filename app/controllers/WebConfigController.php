<?php 
//use_model('WebConfig');
class WebConfigController extends Controller
{
    protected $cf;
    function __construct(){
        $this->cf = new WebConfig();
    }

    public function viewAll()
    {
        htmlArray($this->cf->getData());
    }
    public function addConfig($name=null,$key=null,$value=null)
    {
        if($this->cf->addData($name,$key,$value)){
            $this->viewAll();
        }else{
            echo 'fail: ';
            
        }
    }
}
