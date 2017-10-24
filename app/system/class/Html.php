<?php

/**
 * @author Doanln
 * @copyright 2017
 */


define('DEF_TAG_PROP','0000000000000000000000--00000000000000000000000');

class Html{
    protected static $_vars = array();
    protected static $_head = array();
    protected static $_title = null;
    protected static $_description = null;
    protected static $_image_src = null;
    protected static $_meta = array();
    protected static $_prepend_body = array();
    protected static $_append_body = array();
    
    protected static $head_tags = array('link','script','meta','style');
    protected static $head_meta = array();
    protected static $_simple_tags = array('link','input','img','meta','hr','br');
    
    
    
    public static function get_title(){
        return self::$_title;
    }
    public static function set_title($string = null){
        self::$_title = $string;
    }
    
    public static function add_before_title($string = null, $delimiter = ' | '){
        self::$_title = $string . $delimiter . self::$_title;
    }
    
    public static function add_after_title($string = null, $delimiter = ' | '){
        self::$_title = self::$_title . $delimiter . $string;
    }
    
    public static function title($string = null){
        if(!is_null($string)) self::$_title = $string;
        else echo strip_tags(self::$_title);
    }
    
    
    
    public static function get_description(){
        return self::$_description;
    }
    public static function set_description($string = null){
        self::$_description = $string;
    }
    
    public static function description($string = null){
        if(!is_null($string)) self::$_description = $string;
        else echo strip_tags(self::$_description);
    }
    
    public static function get_image_src(){
        return self::$_image_src;
    }
    public static function set_image_src($string = null){
        self::$_image_src = $string;
    }
    
    public static function image_src($string = null){
        if(!is_null($string)) self::$_image_src = $string;
        else echo self::$_image_src;
    }
    

    public static function createTag($tag,$content=null,$properties=null){
        if(!$tag) return null;
        if(preg_match('/<.*>/',$tag,$m)){
            return $tag;
        }
        $htmltag = '<'.$tag;
        if(is_array($properties)){
            foreach($properties as $p => $v){
                if($v && $v==DEF_TAG_PROP){
                    $htmltag .= ' '.$p;
                }
                elseif(($v || $v==0 )&&$v!=''){
                    $htmltag .= ' '.$p.'="'.$v.'"';
                }
            }
        }
        if(in_array(strtolower($tag),self::$_simple_tags)){
            $htmltag.=' />';
        }
        else{
            $htmltag .=">";
            if(!is_null($content)) $htmltag.=$content;
            $htmltag .="</$tag>";
        }
        return $htmltag;    
    }
    
    public static function input($type='text',$name=null,$val=null,$data=array(),$properties=array())
    {
        //$properties['name'] = $name;
        $inp = null;
        $t = strtolower($type);
        if($t=='select'){
            $inp = self::getSelectTag($name,$data,$val,$properties);
        }elseif($t=='checkbox'){
            $inp = self::getCheckBox($name,$val,$properties);
        }elseif($t=='radio'){
            $inp = self::getRadioButtons($name,$data,$val,$properties);
        }elseif($t=='textarea'){
            $inp = self::createTag('textarea',$val,arr_parse(array('name'=>$name),$properties));
        }else{
            $inp = self::createTag('input',$val,arr_parse(array('type' => $type, 'name'=>$name, 'value' => $val),$properties));
        }

        return $inp;

    }

    /**
    * tra ve the <select>
    * @param string $name
    * @param array $list danh sach tuy chon co dang array($value=>$text)
    * @param string $default gia tri mac dinh se duc selected neu trong list co gia tri do, neu khong se tra ve gia tri dung dau list
    * @param array $properties danh sach tuy chon co dang array($value=>$text)
    */ 

    public static function getSelectTag($name='select',$list=null,$default=null,$properties=null){
        $slt = '
        ';
        if(!is_array($list) && !is_object($list) && is_callable($list)) $list = call_user_func($list);
        if(is_array($list) || is_object($list)){
            foreach($list as $k => $v){
                $slt .= '
        <option value="'.$k.'"'.(($default==$k)?' selected="selected"':"").'>'.$v.'</option>';
            }
        }
            $slt .= '
    ';
    $slt = self::createTag('select',$slt,arr_parse(array('name'=>$name),$properties));
        
        return $slt;
    }
    /**
    * tra ve the <select>
    * @param String $name
    * @param int $start nam bat dau
    * @param int $end Nam ket thuc
    * @param String $default gia tri mac dinh se duc selected neu trong list co gia tri do, neu khong se tra ve gia tri dung dau list
    * @param Strig
    * @param Strig
    * @param Strig
    */ 

    public static function getSelectNumber($name='number',$start=1,$end=10,$default='select-number',$properties=null){
        $list = array();
        if($start>$end) 
            for($i=$start;$i>=$end;$i--)
                $list[$i] = $i;
        else
            for($i=$start;$i<=$end;$i++)
                $list[$i] = $i;
        return self::getSelectTag($name,$list,$default,$properties);
    }


    public static function getCheckBox($name='checkbox',$check=null,$property=null){
        if(!is_array($property)) $property = array();
        if($check&&strtolower($check)!='off') $property['checked'] = "checked";
        $tag = "<input type=\"checkbox\" name=\"$name\"";
        if(is_array($property)){
            foreach($property as $n=>$v){
                if(!is_null($v)){
                    $tag .=" ".$n.="=\"".$v."\"";
                }
            }
        }
        $tag .= " />";
        return $tag;
    }
    public static function getRadioButtons($name='radio',$list=null,$default=null,$properties=null){
        $slt = '';
        if(!is_array($list) && !is_object($list) && is_callable($list)) $list = call_user_func($list);
        if(is_array($list) || is_object($list)){
            foreach($list as $k => $v){
                $slt .= ' <label class="inp-label"><input type="radio" name="'.$name.'" value="'.$k.'"'.(($default==$k)?' checked="checked"':"");
                if(is_array($properties)){
                    foreach($properties as $key => $val){
                        $slt .= ' '.$key.'="'.$val.'"';
                    }
                }
                $slt .='> <span>'.$v.'</span></label>';
            }
        }
        return $slt;
    }
    
}
?>