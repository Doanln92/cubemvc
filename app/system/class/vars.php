<?php

/**
 * @author Le Ngoc Doan
 * @copyright 2016
 */




define('SD_VAR_DEF_VAL','<!----------------------default------------------>');



class cube_vars{
    protected $vars = array();
    protected $delimiter = '.';
    
    /**
     * Khai bao bien dung trong template
     * @param String $key ten bien
     * @param Variable
     */ 
    
    public function set($key = "key", $value=SD_VAR_DEF_VAL,$d=null){
        if(!is_string($d)) $d = $this->delimiter;
        $vars = array(); 
        if(is_string($key)){
            $this->setItem($key,$value);
        }elseif(is_array($key)){
            foreach($key as $k => $v){
                $this->setItem($k,$v);
            }
        }
        return $this;
    }
    
    
    
    /**
     * lay gia bien da duoc khai bao
     * @param String
     */ 
    public function get($key = null,$delimiter=null){
        if(is_null($key)) return $this->vars;
        if(is_string($key)){
            $delimiter = is_string($delimiter)?$delimiter:$this->delimiter;
            $t = $this->vars;
            $d = explode($delimiter,trim($key));
            $p = count($d);
            if(isset($t[$d[0]])){
                $t = $t[$d[0]];
                for($i = 1; $i < $p; $i++){
                    $g = $d[$i];
                    if(is_array($t)&&isset($t[$g])){
                        $t = $t[$g];
                    }else{
                        $t = null;
                        $i+=$p;
                    }
                }
            }
            else $t = null;
            return $t;
        }
        elseif(is_numeric($key)) return isset($this->vars[$key])?$this->vars[$key]:null;
        return $this->vars?true:false;
    }
    /**
     * lay gia bien da duoc khai bao
     * @param String
     */ 
    public function is($key = null,$delimiter=null){
        if(is_null($key)) return $this->vars?true:false;
        if(is_string($key)){
            $s = true;
            $delimiter = is_string($delimiter)?$delimiter:$this->delimiter;
            $t = $this->vars;
            $d = explode($delimiter,strtolower($key));
            $p = count($d);
            if(isset($t[$d[0]])){
                $t = $t[$d[0]];
                for($i = 1; $i < $p; $i++){
                    $g = $d[$i];
                    if(is_array($t)&&isset($t[$g])){
                        $t = $t[$g];
                    }else{
                        $t = null;
                        $s = false;
                        $i+=$p;
                    }
                }
            }
            else $s = false;
            return $s;
        }
        elseif(is_numeric($key)) return isset($this->vars[$key])?true:false;
        return $this->vars?true:false;
    }
    
    /**
     * loai bo bien da duoc khai bao
     * @param String
     */ 
    function remove($key){
        if(is_string($key)){
            $delimiter = $this->delimiter;
            $ks = explode($delimiter,$key);
            $kz = "\$this->vars[\"$ks[0]\"]";
            for($i = 1; $i < count($ks); $i++){
                $kz .= "[\"".$ks[$i]."\"]";
            }
            eval("if(isset($kz)) unset($kz);");
        }
        elseif(is_numeric($key)){
            unset($this->arr[$key]);
            unset($this->data[$key]);
        }
        elseif(is_array($key)){
            $e = "\$a = 1;";
            foreach($key as $k){
                if(is_string($k)){
                    $delimiter = $this->delimiter;
                    $ks = explode($delimiter,$k);
                    $kz = "\$this->vars[\"$ks[0]\"]";
                    for($i = 1; $i < count($ks); $i++){
                        $kz .= "[\"".$ks[$i]."\"]";
                    }
                    $e.="if(isset($kz)) unset($kz);";
                }
            }
            eval($e);
        }
        return $this;
    }

    public function setItem($key=null,$value=SD_VAR_DEF_VAL,$d=null){
        if(!is_string($d)) $d = $this->delimiter;
        if(is_string($key)){
            if($value===SD_VAR_DEF_VAL) return $this;
            $c = explode($d,$key);
            if(count($c)>=1){
                $g = strtolower($c[0]);
                if($g == '') return $this;
                if(count($c)==1){
                    $this->vars[$key] = $value;
                }
                else{
                    $e = "\$this->vars";
                    foreach($c as $kk){
                        $e .= "[\"$kk\"]";
                    }
                    if($e!="\$this->vars") eval($e.=" = \$value;");
                }
            }
        }
        return $this;
    }
}

?>