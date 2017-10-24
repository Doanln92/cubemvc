<?php

/**
 * thuc thi file template trong thu muc view
 * @param String $file file hoac danh sach file, ngan cach bang dau phay (,)
 * @param Array $variable mang gom key ki tu la ten cac bien, key phai tuan theo ten chuan
 * @param bool $cache co su d?ng cache hay ko
 * @param int $cache_time
 * @param bool
 * @param bool
 * @return Void
 * $variable la danh sach bien rieng cho template dang thuc thi va khong bat buoc
 */ 

function view($filename=null, $variable=null, $cache=null, $cache_time=null, $pathinfo = false, $query_string=false){
    View::display($filename, $variable, $cache, $cache_time, $pathinfo, $query_string);
}



function get_header($filename=null, $variable=null, $cache=null, $cache_time=null, $pathinfo = false, $query_string=false){
    View::header($filename, $variable, $cache, $cache_time, $pathinfo, $query_string);
}

function get_footer($filename=null, $variable=null, $cache=null, $cache_time=null, $pathinfo = false, $query_string=false){
    View::footer($filename, $variable, $cache, $cache_time, $pathinfo, $query_string);
}

function get_sidebar($filename=null, $variable=null, $cache=null, $cache_time=null, $pathinfo = false, $query_string=false){
    View::sidebar($filename, $variable, $cache, $cache_time, $pathinfo, $query_string);
}



function assignVar($name=null,$val=null){
	View::assign($name,$val);
}









?>