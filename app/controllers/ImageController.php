<?php
/**
* ImageController
*/

class ImageController extends Controller
{
	public function __construct() {
        
    }
    
    public function resize($path=null)
    {
        $file = '';
        
        $ext = _request('format');
        
        $width = _request('width');
        
        $height = _request('height');
        
        if((is_string($height) && is_string($width)) && (strtolower($width) == 'auto' && strtolower($height)=='auto') && file_exists(get_public_dir($path))){
            $url = url('public/'.$path);
            redirect($url);
        }
        elseif(file_exists(get_content_dir($p = 'images/'.md5($path.$width.$height).'.jpg'))){
            redirect(get_content_url($p));
        }
        elseif(!is_dir(get_public_dir($path)) && file_exists(get_public_dir($path))){
            include_once LIBCLASSDIR.'image.php';
            $url = get_public_dir($path);
            if($im = image::getsity($url)){
                $img = new image($url);
                $img->RaC($width,$height);
                $img->save(get_content_dir('images/'.md5($path.$width.$height).'.jpg','jpg'));
                $img->show();
                die;
            }
        }
        redirect(get_content_url('images/default.jpg'));
    }
    
    public function getResize($path=null,$width=null,$height=null)
    {
        $file = '';
        
        
        if((is_string($height) && is_string($width)) && (strtolower($width) == 'auto' && strtolower($height)=='auto') && file_exists(get_public_dir($path))){
            $url = url('public/'.$path);
            return ($url);
        }
        elseif(file_exists(get_content_dir($p = 'images/'.md5($path.$width.$height).'.jpg'))){
            return (get_content_url($p));
        }
        elseif(!is_dir(get_public_dir($path)) && file_exists(get_public_dir($path))){
            include_once LIBCLASSDIR.'image.php';
            $url = get_public_dir($path);
            if($im = image::getsity($url)){
                $img = new image($url);
                $img->RaC($width,$height);
                $img->save(get_content_dir('images/'.md5($path.$width.$height).'.jpg'),'jpg');
                return get_content_url('images/'.md5($path.$width.$height).'.jpg');
            }
        }
        return (get_content_url('images/default.jpg'));
    }
}
?>