<?php

namespace Models;

class WebConfig extends BaseModel
{
    function __construct()
    {
        parent::__construct('webconfig');
    }
    
    public function getData($name = null,$key=null)
    {
        if(!is_null($name)){
            if( $data = $this->andWhere('name',$name)->first() ){
                $d = $this->convertData($data->value);
                if(!is_null($key)){
                    if(isset($d[$key])) return $d[$key];
                    else return null;
                }else return $d;
            }
        }elseif ($data = parent::get()) {
            $arr = array();
            foreach ($data as $d) {
                $arr[$d->name] = $this->convertData($d->value);
            }
            return $arr;
        }
        return null;
    }

    function addData($name = null,$key=null, $value=null){
        if(!is_string($name)) return false;
        if($cf = $this->andWhere('name',$name)->first()){
            return $cf->updateValue($key,$value);
        }
        $this->name = $name;
        if(!is_null($key)){
            if(is_array($key))
            $this->value = serialize($key);
            else
            $this->value = serialize(array($key=>$value));
        }
        return $this->insert();
    }
    
    public function updateValue($key = null,$value=null)
    {
        $a = new Arr($this->convertData($this->value));
        if(!is_null($key)){
            if(is_array($key)){
                $a->parse($key);
            }else{
                $a->push($key,$value);
            }
        }
        $this->value = serialize($a->get());
        return $this->update();
    }



    public function convertData($str = null)
    {
        $data = array();

        if($str){
            try{
                if($d = unserialize($str)){
                    $data = $d;
                }
            }catch(Exception $e){
                //
            }
        }
        return $data;
    }
}
