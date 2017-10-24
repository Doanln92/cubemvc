<?php

namespace Extensions;
use Models\User;

class CubeHtmlMenu
{
    protected $menu_id = "";
    protected $menu_class = "";
    protected $menu_item_id = "";
    protected $menu_item_class = "";
    protected $menu_link_id = "";
    protected $menu_link_class = "";
    protected $submenu_id = "";
    protected $submenu_class = "";
    protected static $active_key = null;
    public function __construct($args=null) {
        $this->setOpt($args);
    }
    
    public function setOpt($args=null){
        if(is_array($args)){
            if(isset($args['menu_id'])) $this->menu_id = $args['menu_id'];
            if(isset($args['menu_class'])) $this->menu_class = $args['menu_class'];
            if(isset($args['menu_item_id'])) $this->menu_item_id = $args['menu_item_id'];
            if(isset($args['menu_item_class'])) $this->menu_item_class = $args['menu_item_class'];
            
            if(isset($args['menu_link_id'])) $this->menu_link_id = $args['menu_link_id'];
            if(isset($args['menu_link_class'])) $this->menu_link_class = $args['menu_link_class'];
            
            if(isset($args['submenu_id'])) $this->submenu_id = $args['submenu_id'];
            if(isset($args['submenu_class'])) $this->submenu_class = $args['submenu_class'];
        }
    }

    
    public function create($args,$url=null,$path_pos=0,$options = null){
        if(is_array($options)) $this->setOpt($options);
        $str_menu = "<ul".($this->menu_id?" id=\"$this->menu_id\"":"").($this->menu_class?" class=\"$this->menu_class\"":"").">";
        if(is_array($args)){
            foreach($args as $menu){
                $u = $url;
                $p = $path_pos;
                $ca = "";
                if(isset($menu['path'])){
                    $u = cube_join_url($url,$menu['path']);
                    if(is_int($path_pos) && $path_pos > 0){
                        $p++;
                        if(strtolower($menu['path']) == \App::lower_pathinfo($path_pos)) $ca="active";
                    }
                
                }
                elseif(isset($menu['request'])){
                    $u = reqURL($url,$menu['request'],isset($menu['request_val'])?$menu['request_val']:$menu['text']);
                }
                $c = "";
                $sm="";
                if($this->menu_item_class) $c.= $this->menu_item_class." ";
                if(isset($menu['class'])) $c.= $menu['class']." ";
                if(isset($menu['submenu'])){
                    $s = $menu['submenu'];
                    $c.="has-child ";
                    $mn = new static();
                    $mn->setOpt(array(
                        'menu_class' => 'sub-menu'
                    ));
                    $sm = $mn->create($s,$u,$p);
                }
                $c.=$ca;
                $class = $c != "" ? " class=\"$c\"":"";
                $str_menu .= "\n<li".(isset($menu['id'])?"  id=\"$menu[id]\"":"").($class!=""?$class:"").">"
                           . "<a href=\"$u\" title=\"".$menu['text']."\""
                           .(($ca || $this->menu_link_class)?" class=\"$ca $this->menu_link_class\"":"")
                           .">$menu[text]</a>"
                           . $sm
                           ."</li>";
            }
            $str_menu.="\n</ul>";
            return $str_menu;
        }
    }



    public static function getMenuList($args=null){
        if(is_array($args)){
            $c = new \Arr($args);
            $list = array();
            $menu_form = array('slug','request','name','title','text','id','class','icon','type','description','submenu');
            switch(strtolower($c->e('type'))){
                case 'list':
                    $list = $c->e('list');
                break;
                case 'category':
                    $Cat = get_model('Category');

                    $categories = $Cat::where('parent_id','')->get('*',$c->e('args'));
                    if($categories){
                        foreach($categories as $cat){
                            $b = array();
                            $b['type'] = 'cat';
                            $b['path'] = $cat->slug;
                            $b['active_key'] = $cat->slug;
                            $b['text'] = $cat->name;
                            $b['title'] = $cat->name;
                            $b['request'] = $cat->slug;
                            if($children = $cat->getChildren()){
                                $submenu = array();
                                foreach($children as $i => $c){
                                    $submenu[$i] = array();
                                    $submenu[$i]['type'] = 'cat';
                                    $submenu[$i]['path'] = $c->slug;
                                    $submenu[$i]['slug'] = $c->slug;
                                    $submenu[$i]['active_key'] = $c->slug;
                                    $submenu[$i]['text'] = $c->name;
                                    $submenu[$i]['title'] = $c->name;
                                    
                                }
                                $b['submenu'] = $submenu;
                            }
                            $list[] = $b;
                        }
                        
                    }
                break;
                case 'json':
                    //$li = new Arr();
                    $list = \App::data()->json('menus/'.$c->e('args.file'));
                break;
                case 'defined': 
                    $list = call_user_func($c->e('func'), $c->e('args'));
                break;
                default:
                    $list = null;
                break;
            }
            return $list;
        }
        return null;
    }






    public static function setKey($key = null)
    {
        if(is_string($key)) self::$active_key = $key;
    }


    public function render($args,$url=null,$active_key=null,$options = null){
        if(!$active_key) $active_key = self::$active_key;
        if(is_array($options)) $this->setOpt($options);
        $str_menu = "<ul".($this->menu_id?" id=\"$this->menu_id\"":"").($this->menu_class?" class=\"$this->menu_class\"":"").">";
        if(is_array($args)){
            foreach($args as $menu){
                $u = $url;
                $ca = "";
                $ua = $u;
                if(isset($menu['path'])){
                    $u = cube_join_url($url,$menu['path']);
                    $ua = $u;
                    if(isset($menu['type']) && $menu['type']=='cat') $ua.='.chn';
                    if(isset($menu['active_key']) && $active_key && strtolower($menu['active_key']) == strtolower($active_key)) $ca="active";
                }
                elseif(isset($menu['request'])){
                    $u = reqURL($url,$menu['request'],isset($menu['request_val'])?$menu['request_val']:$menu['text']);
                    $ua = $u;
                }
                $c = "";
                $sm="";
                if($this->menu_item_class) $c.= $this->menu_item_class." ";
                if(isset($menu['class'])) $c.= $menu['class']." ";
                if(isset($menu['submenu'])){
                    $s = $menu['submenu'];
                    $c.="has-child ";
                    $mn = new static();
                    $mn->setOpt(array(
                        'menu_class' => 'sub-menu'
                    ));
                    $p = isset($menu['submenu_active_key'])?$menu['submenu_active_key']:null;
                    $sm = $mn->render($s,$u,$p);
                }
                $c.=$ca;
                $class = $c != "" ? " class=\"$c\"":"";
                $str_menu .= "\n<li".(isset($menu['id'])?"  id=\"$menu[id]\"":"").($class!=""?$class:"").">"
                           . "<a href=\"$ua\" title=\"".$menu['title']."\""
                           .(($ca || $this->menu_link_class)?" class=\"$ca $this->menu_link_class\"":"")
                           .">$menu[text]</a>"
                           . $sm
                           ."</li>";
            }
            $str_menu.="\n</ul>";
            return $str_menu;
        }
    }

    public static function parseItem($item=null){
        if(!is_array($item)) return $item;
        $it = new \Arr($item);
        if(!$it->e('item_type')) return $item;
        switch($it->e('item_type')){
            case 'defined':
            $item = null;
                if($it->e('call')&&function_exists($it->e('func'))){
                    $args = $it->e('args');
                    $item = call_user_func($it->e('func'), $it->e('args'));
                }
            break;
        }
        return $item;
    }

    public static function createMenu($args,$url=null,$path_pos=0,$options = null)
    {
        $list = self::getMenuList($args);
        $menu = new static($options);
        echo $menu->create($list,$url,$path_pos,$options);
    }
    public function createHomeMenu($args,$url=null,$active_key=null,$options = null){
        $list = self::getMenuList($args);
        array_unshift($list,array('type'=>'custom','active_key'=>'home','text'=>'Trang chủ','title'=>'Trang chủ','path'=>'','id'=>'home-item'));
        $shop = array('type'=>'custom','active_key'=>'shop','text'=>'Cửa hàng','title'=>'Cửa hàng','path'=>'shop','id'=>'shop-item');
        if($u = User::getCurrentLogin()){
            $shop['submenu'] = array(
                array('type'=>'custom','active_key'=>'shop-add-product','text'=>'Thêm sản phẩm','title'=>'Thêm sản phẩm','path'=>'add-product','id'=>'shop-add-product'),
                array('type'=>'custom','active_key'=>'shop-owner-product','text'=>'Gian hàng của bạn','title'=>'Gian hàng của bạn','path'=>'owner/'.$u->username,'id'=>'shop-owner')
            );
        }
        array_push($list,$shop);
        
        $cart = get_controller('CartController');
        array_push($list,array('type'=>'custom','active_key'=>'cart','text'=>'
            <i class="fa fa-shopping-cart"></i> (<span class="cart-item-total">'.$cart->count().'</span>)
            ',
            'title'=>'Giỏ hàng','path'=>'cart','id'=>'cart-item','class' => 'cart-item right'));
        
        $menu = new static($options);
        echo $menu->render($list,$url,$active_key,$options);
        
        
    }






}
