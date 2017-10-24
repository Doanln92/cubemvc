<?php

/**
 * @author Le Ngoc Doan
 * @copyright 2015
 */
/**
 * ham tra ve chuoi da duoc loc dau tieng Viet
 * @param String $str chuoi ki tu
 * 
 */ 
function cleanit($text)
{
	return htmlentities(strip_tags(stripslashes($text)), ENT_COMPAT, "UTF-8");
}
function str_clear($str){
    $f = '/[^A-z0-9\s_\-=<>\[\]\{\}\.\,\"\'\:\;\(\)\~\!\@\#\$\%\^\&\*\r\n\t\`\/]/';
    $r = '';
    $str = preg_replace($f, $r, $str);
    return $str;
}
function str_clean($str){
    $f = '/[^A-z0-9\s_\-\.]/';
    $r = '';
    $str = preg_replace($f, $r, $str);
    return $str;
}
function str_ucln($str){
    $f = '/[^A-z0-9_\-\.]/';
    $r = '';
    $str = preg_replace($f, $r, $str);
    return $str;
}
function str_namespace($str){
    $p = array(". ", ".", ", ", ",", " & ", " ", "&");
    $q = array("-", "-", "-", "-", "-", "-", "-");
    $f = '/([^A-z0-9_\-]|^\-|\-$)/';
    $r = '';
    $str = str_replace($p, $q, vi2en($str));
    $str = preg_replace($f, $r, $str);
    $str = preg_replace($f, $r, $str);
    $str = preg_replace('/(\-\-\-|\-\-)/', '-', $str);
    $str = preg_replace('/[\\\"\']/', '', $str);
    $str = preg_replace($f, $r, $str);
    $str = trim($str);
    return $str;
}
function str_namespace2($str){
    $p = array(". ", ".", ", ", ",", " & ", " ", "&", "=");
    $q = array("_", "_", "_", "_", "_", "_", "_", "_");
    $f = '/([^A-z0-9_]|^_|_$)/';
    $r = '';
    $str = preg_replace('/(___|__)/', '_', preg_replace($f, $r, str_replace($p, $q, vi2en($str))));
    $str = preg_replace('/[\\\"\']/', '', $str);
    $str = preg_replace($f, $r, $str);
    return $str;
}
function str_dirname($str){
    $p = array(". ", ".", ", ", ",", " & ", " ", "&","-");
    $q = array("_", "_", "_", "_", "_", "_", "_","_");
    $f = '/([^A-z0-9_\/]|^_|_$)/';
    $r = '';
    $str = preg_replace('/(___|__)/', '_', preg_replace($f, $r, str_replace($p, $q, vi2en($str))));
    $str = preg_replace('/[\\\"\']/', '', $str);
    $str = preg_replace($f, $r, $str);
    return $str;
}

function str_sclr($str,$char=null){
    $st = vi2js($str);
    $st = preg_replace('/[^A-z0-9_&\-\s\\\t]/','',$st);
    $st = js2vi($st);
    
    $st = ($char!=null)?rtrim($st,$char):rtrim($st);
    return $st;
    
}
function str_stk($value){
    $value = htmlvi($value);
    $vl = preg_replace('/(\-\s|\s\-\s|\s\-)/', '\s', $value);
    $p = $value
        .", ".vi2en($vl);
    $p = str_replace(",,", ",", $p);
    $p = preg_replace('/\,\,/', '\,', $p);
    return $p;
}
function wspl($str, $start = 0, $length = 1000000){
	
    
    $pr = "";
    
	$str = preg_replace('/[\r\n\t]/', '', $str);
    
	$cnt = strip_tags(str_replace("><", "> <", str_replace('  ', ' ', $str)));
	$cnt = htmlvi($cnt);
    if(strlen($str)<=$length) return $cnt;
    $cnt = substr($cnt, $start, $length);
	$cnt = explode(" ", $cnt);
	$p = count($cnt) -1;
	for($i = 0; $i < $p; $i++){
		$pr .= $cnt[$i] . " ";
	}
	return $pr."...";
}


function subword($str, $start = 0, $length = 50000,$moretext='',$keepline=false){
	
    
    $pr = "";
    
	
	$cnt = strip_tags(str_replace("><", "> <", str_replace('  ', ' ', $str)));
	$cnt = preg_replace('/\s{2,}/', ' ', $cnt);
    if(!$keepline)$cnt = preg_replace('/[\r\n\t]/', '',$cnt);
    elseif(is_string($keepline)){
        $cnt = preg_replace('/[\r\n\t]/', ' / ', $cnt);
    }
    $cnt = htmlvi($cnt);
    $p = explode(' ',$cnt);
    $c = count($p);
    $k = $start+$length;
    $s = $start;
    if($length>=$c) return $cnt;
    if($k >= $c){
        $s=0;
        $k=$c-1;
    }
    for($i=$s;$i<$k;$i++){
        $pr.=(($i>$s)?' ':'').$p[$i];
    }
    arr_reset();
    
    
    
    return $pr.$moretext;
}

function clrt($str){
    $f = array('\\');
    $r = array('');
    $str = str_replace($f, $r, $str);
    return $str;
}

function htmlvi($str){
    $f = array('\\','"',"'",'<','>');
    $r = Array('','&quot;','&#039;','&lt;','&gt;');
    $str = str_replace($f, $r, $str);
    return $str;
}
function htmlvi2($str){
    return htmlentities($str);
}
function hsencode($str){
    $f = array('\\','"',"'",'<','>');
    $r = Array('','&quot;','&#039;','&lt;','&gt;');
    $str = str_replace($f, $r, $str);
    return $str;
}
function hsdecode($str){
    $f = array('','"',"'",'<','>');
    $r = Array('\\','&quot;','&#039;','&lt;','&gt;');
    $str = str_replace($r, $f, $str);
    return $str;
}

/**
 * Ham kiem tra ngay thang co hop le hay khong
 * @param day : Ngay
 * @param month : Thang
 * @param year : Nam
 * tra ve true neu dung du kien ngay thang va false neu sai
 */
function is_date($day, $month=null, $year=null){
    $stt = true;
    if(is_null($month)){
        if(preg_match('/\d{4}\-\d{1,2}\-\d{1,2}/',$day)){
            $arr = explode('-',$day);
            
            return call_user_func_array(__FUNCTION__,array($arr[2],$arr[1],$arr[0]));
        }else{
            return false;
        }    
    }
    switch($day){
        case 31:
            if($month==2||$month==4||$month==6||$month==9||$month==11) $stt = false;
        break;
        case 30:
            if($month == 2) $stt = false;
        break;
        case 29:
            if(($year % 4 != 0 || ($year%4==0&&$year%100==0))||$year % 4 == 0 && $year%100!=0){
                $stt = false;
            }
        break;
        
    }
	return $stt;
}


function is_gender($gender){
	$stt = ($gender == "male" || $gender == "female")? true:false;
	return $stt;
}


function is_email($email){
	$tm = '/^[_A-z0-9\-]+(\.[_A-z0-9\-]+)*@[A-z0-9\-]+(\.[A-z0-9\-]+)*(\.[A-z]{2,4})$/';
	$t = false;
	if(preg_match($tm, $email)) $t = true;
	return $t;
}

function subtext($str, $start = 0, $length = 1000000){
	$cnt = htmlvi(strip_tags(str_replace("><", "> <", preg_replace('/[\r\n\t]/', '', $str))));
    if(strlen($cnt)<=$length) return $cnt;
    $pr = "";
    
    $cnt = substr($cnt, $start, $length);
	$cnt = explode(" ", $cnt);
	$p = count($cnt) -1;
	for($i = 0; $i < $p; $i++){
		$pr .= $cnt[$i] . " ";
	}
	return $pr."...";
}

function html2text($html){
    $str = str_replace('\\','',$html);
    $str = strip_tags($str);
    return $str;
}
function h2t($str){
    $txt = _lang('text');
    $img = $txt['image'];
    $str1 = str_replace('\\', '', $str);
    
    $str1 = preg_replace('/<(img|IMG) (.*)>/', "[".$img."]",$str1);
    $str1 = preg_replace('/<(BR|br|Br|bR)(\s\/|\/)?>/', '
', $str1);
    
    $str2 = strip_tags($str1);
    $str3 = preg_replace('/(\r\n\r\n|\n\n\n\n|\r\r\r\r)/','
', $str2);
    $str3 = preg_replace('/(\r\n\r\n|\n\n\n\n|\r\r\r\r)/','

', $str3);
    $str3 = htmlvi($str3);
    return $str3;
}


function text2html($str){
    return preg_replace('/\n/','<br />', trim($str));
}

function getLink($text){
    if(preg_match('/^([Hh]{1}[tT]{2}[Pp]{1}[Ss]{0,1}|ftp)\:\/\//', $text)) $str = "<a href=\"$text\">$text</a>";
    else $str = $text;
    return $str;
}
 
/**
 * @param String
 * @param String
 * @param Int
 * @param String
 */
function eval_str($text=null,$data=null,$char_type=0,$char_start='$'){
    $type = array(
        0 => array('start' => '{', 'end' => '}'),
        1 => array('start' => '[', 'end' => ']'),
        2 => array('start' => '(', 'end' => ')'),
        3 => array('start' => '/*', 'end' => '*/'),
        5 => array('start' => '<', 'end' => '>')
    );
    $chars = array('$','@','%','','*','sd:');
    if(!is_string($text) && !is_array($data)) return $text;
    $start = '{';
    $end = '}';
    $char = '$';
    if(isset($type[$char_type])){
        $ty = $type[$char_type];
        $start = $ty['start'];
        $end = $ty['end'];
    }elseif(is_string($char_type)){
        $start = $char_type;
        if(strlen($char_type)>1){
            $end = '';
            $n = strlen($char_type)-1;
            for($i=$n; $i >= 0; $i--){
                $end.=substr($char_type,$i,1);
            }
        }else{
            $end = $start;
        }
    }
    if(in_array($char_start,$chars)){
        $char = $char_start;
    }elseif($char_start&&isset($chars[$char_start])){
        $char = $chars[$char_start];
    }elseif($char_start){
        $char = $char_start;
    }
    $find = array();
    $replace = array();
    $find2 = array();
    $replace2 = array();
    
    foreach($data as $name => $val){
        if(is_array($val)) continue;
        $find[] = $start.$char.$name.$end;
        $replace[] = $val;
        
        
        $find2[] = '['.$name.']';
        $replace2[] = $val;
        
        
        $find2[] = '{$'.$name.'}';
        $replace2[] = $val;
        $find2[] = '{\$'.$name.'}';
        $replace2[] = $val;
        
        $find2[] = '{'.$name.'}';
        $replace2[] = $val;
    }
    
    
    $txt = str_replace($find,$replace,$text);
    
    //$txt = str_replace($find2,$replace2,$txt);
    
    return $txt;
}


?>