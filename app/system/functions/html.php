<?php

/**
 * @author Le Ngoc Doan
 * @copyright 2015
 */



// function e(){
//     if($args = func_get_args()){
//         foreach($args as $arg){
//             echo htmlentities($arg,ENT_HTML5);
//         }
//     }
    
// }

function echoif(){
    if($args = func_get_args()){
        foreach($args as $arg){
            if(is_string($arg) || is_numeric($arg)) echo $arg;
        }
    }
}

function cube_call_func(){
    $args = func_get_args();
    $fun = array_shift($args);
    if(function_exists($fun)){
        return call_user_func_array($fun, $args);
    }
    return null;
}

function cube_get_html($tag,$content=null,$properties=null){
    if(!$tag) return null;
    if(preg_match('/<.*>/',$tag,$m)){
        return $tag;
    }
    $_simple_tags = array('link','input','img','meta');
    $htmltag = '<'.$tag;
    if(is_array($properties)){
        foreach($properties as $p => $v){
            if(($v || $v==0 )&&$v!='')
            $htmltag .= ' '.$p.'="'.$v.'"';
        }
    }
    if(in_array(strtolower($tag),$_simple_tags)){
        $htmltag.=' />';
    }
    else{
        $htmltag .=">";
        if(!is_null($content)) $htmltag.=$content;
        $htmltag .="</$tag>";
    }
    return $htmltag;    
}


function cube_get_select_tag($name='select',$list=null,$default=null,$properties=null){
    $slt = '
    ';
    if(is_array($list)){
        foreach($list as $k => $v){
            $slt .= '
    <option value="'.$k.'"'.(($default==$k)?' selected="selected"':"").'>'.$v.'</option>';
        }
    }
        $slt .= '
';
    $slt = cube_get_html('select',$slt,arr_parse($properties,array('name'=>$name)));
    
    return $slt;
}



/**
 * tra ve the <select>
 * @param String $name
 * @param Array $list danh sach tuy chon co dang array($value=>$text)
 * @param String $default gia tri mac dinh se duc selected neu trong list co gia tri do, neu khong se tra ve gia tri dung dau list
 * @param Strig
 * @param Strig
 * @param Strig
 */ 

function getSelectTag($name='select',$list=null,$default=null,$properties=null){
    $slt = '
    ';
    if(is_array($list)){
        foreach($list as $k => $v){
            $slt .= '
    <option value="'.$k.'"'.(($default==$k)?' selected="selected"':"").'>'.$v.'</option>';
        }
    }
        $slt .= '
';
    $slt = cube_get_html('select',$slt,arr_parse($properties,array('name'=>$name)));
    
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

function getSelectYear($name='year',$start=2014,$end=2050,$default='year',$properties=null){
    $list = arc_num($start,$end);
    $list = arr_pre_push(array('year'=>get_text('year')),$list);
    return getSelectTag($name,$list,$default,$properties);
}
/**
 * tra ve the <select>
 * @param String $name
 * @param String $default gia tri mac dinh se duc selected neu trong list co gia tri do, neu khong se tra ve gia tri dung dau list
 * @param Strig
 * @param Strig
 * @param Strig
 */ 

function getSelectMonth($name='month',$default='month',$properties=null){
    $list = array("month"=>get_text('month'));
    for($i=1;$i<=12;$i++)
        $list[(($i<10)?"0":"").$i] = (($i<10)?"0":"").$i;
    return getSelectTag($name,$list,$default,$properties);
}

function getSelectDay($name='day',$default='day',$properties=null){
    $list = array("day"=>get_text('day'));
    for($i=1;$i<=31;$i++)
        $list[(($i<10)?"0":"").$i] = (($i<10)?"0":"").$i;
    return getSelectTag($name,$list,$default,$properties=null);
}

function getSelectHour($name='hour',$default='hour',$properties=null){
    $list = array("hour"=>get_text('hour'));
    for($i=0;$i<=23;$i++)
        $list[(($i<10)?"0":"").$i] = (($i<10)?"0":"").$i;
    return getSelectTag($name,$list,$default,$properties);
}

function getSelectMinute($name='minute',$default='minute',$properties=null){
    $list = array("minute"=>get_text('minute'));
    for($i=0;$i<60;$i++)
        $list[(($i<10)?"0":"").$i] = (($i<10)?"0":"").$i;
    return getSelectTag($name,$list,$default,$properties);
}
function getSelectSecond($name='second',$default='second',$properties=null){
    $list = array("second"=>get_text('second'));
    for($i=0;$i<60;$i++)
        $list[(($i<10)?"0":"").$i] = (($i<10)?"0":"").$i;
    return getSelectTag($name,$list,$default,$properties=null);
}
/**
 * tra ve the <select>
 * @param String $name
 * @param int $start so dau
 * @param int $end so cuoi
 * @param String $default gia tri mac dinh se duc selected neu trong list co gia tri do, neu khong se tra ve gia tri dung dau list
 * @param Strig
 * @param Strig
 * @param Strig
 */ 

function getSelectNumber($name='number',$start=1,$end=10,$default='select-number',$properties=null){
    $list = array();
    if($start>$end) 
        for($i=$start;$i>=$end;$i--)
            $list[$i] = $i;
    else
        for($i=$start;$i<=$end;$i++)
            $list[$i] = $i;
    return getSelectTag($name,$list,$default,$properties);
}


function html_create_tag($tag,$content=null,$properties=null){
    if(!$tag) return null;
    if(preg_match('/<.*>/',$tag,$m)){
        return $tag;
    }
    $_simple_tags = array('link','input','img','meta');
    $htmltag = '<'.$tag;
    if(is_array($properties)){
        foreach($properties as $p => $v){
            if(($v || $v==0 )&&$v!='')
            $htmltag .= ' '.$p.'="'.$v.'"';
        }
    }
    if(in_array(strtolower($tag),$_simple_tags)){
        $htmltag.=' />';
    }
    else{
        $htmltag .=">";
        if(!is_null($content)) $htmltag.=$content;
        $htmltag .="</$tag>";
    }
    return $htmltag;    
}

/**
 * tra ve mot the html voi nhung thong tin da cho
 * @param String TagName html's tag name
 * @param Text Noi dung
 * @param Array danh sach thuoc tinh
 */ 
function getHTML($tagNamw="p",$content='',$properties=null){
    if(!$tag) return null;
    if(preg_match('/<.*>/',$tag,$m)){
        return $tag;
    }
    $_simple_tags = array('link','input','img','meta');
    $htmltag = '<'.$tag;
    if(is_array($properties)){
        foreach($properties as $p => $v){
            if(($v || $v==0 )&&$v!='')
            $htmltag .= ' '.$p.'="'.$v.'"';
        }
    }
    if(in_array(strtolower($tag),$_simple_tags)){
        $htmltag.=' />';
    }
    else{
        $htmltag .=">";
        if(!is_null($content)) $htmltag.=$content;
        $htmltag .="</$tag>";
    }
    return $htmltag;
}
function getInputTag($type="text",$namw="inp[]",$value='',$properties=null){
    $tag = "<input type=\"$type\" name=\"$name\" value=\"$value\"";
    if(is_array($properties)){
        foreach($properties as $k => $v){
            $tag.=" ".$k."=\"".$v."\"";
        }
    }
    $tag.=" />";
    return $tag;
}

function getCheckBox($name='checkbox',$check=null,$property=null){
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
function getRadioButtons($name='radio',$list=null,$default=null,$properties=null){
    $slt = '';
    if(is_array($list)){
        foreach($list as $k => $v){
            $slt .= ' <label><input type="radio" name="'.$name.'" value="'.$k.'"'.(($default==$k)?' checked="checked"':"");
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

/**
 * comment loi hoac thu gi do, vua de cho dep trang web vua giup nguoi lap trinh biwd dc loi cua trang web
 * @param string
 */
function htmlCmt($comment){
    return print("<!--  $comment  -->\r\n");
}
/**
 * in ra mot mang dong thoi gi nguyen cau truc mang (hien thu truc tiep tren trang 
 * @param array
 */

function htmlArray($array){
    echo "<pre>";
    print_r($array);
    echo "</pre>";
}
/**
 * comment html mot mang
 * @param array
 */


function htmlArrCmt($array){
   echo "<!--\n\n";
   print_r($array);
   echo "\n\n-->";
}
