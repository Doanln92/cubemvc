<?php
/**
* HomeController
*/

use Models\User;
use Models\Subcriber;
use Models\Category;
use Models\Post;
use Models\Product;
use Models\Gallery;

use Models\Order;


use Models\UserConfirm;

use Extensions\CubePaging;
use Extensions\CubeHtmlMenu;

class HomeController extends Controller
{
    protected $user;
    
	function __construct()
	{
		# code...
        if($user = User::getCurrentLogin()){
			$this->user = $user;
		}else{
			
		}
        Html::title("Thế Giới Vuông");
        
    }

    public function setMenuActiveKey($key = null)
    {
        CubeHtmlMenu::setKey($key);
    }

    public function alert($message="Hello World!", $alert_type='success')
	{
		$this->view('alert',array('message'=>$message,'alert_type'=>$alert_type));
	}

	public function confirmDelete($id = 0, $message="bạn có chắc chắn muốn xóa không")
	{
		$this->view('confirm-delete',array('id' => $id, 'message'=>$message));
	}


    public function index()
    {
        $this->setMenuActiveKey('home');
        Html::title("Thế Giới Vuông");
        $this->useCache();
        if($hc = $this->getControllerCache('home_cats')){
            $home_cats = $hc;
        }
        else{
            $Post = new Post();
            

            // slide random
            $Post->setNickName('p');
            $Post->innerJoin('categories c', 'c.id=p.cat_id');
            $random_posts = $Post->orderBy('RAND()')->limit(20)->get('p.*,c.name cat_name');
            
            //hot news
            $Post->orderBy('view', 'DESC')->orderBy('id','DESC')->limit(5);
            $hotnews = $Post->get();
            


            $home_cats = array();
            if($categories = Category::where('parent_id','')->get()){
                $t = count($categories);
                $cats = array();
                $n = 0;
                $tab = 0;
                $isSingle = false;
                for($i=0; $i < $t; $i++){
                    $cat = $categories[$i];
                    if($i<2){
                        if($t==1){
                            $cats[$n] = array(
                                'type' => 'single',
                                'cat' => $cat
                            );
                        }else{
                            if(!isset($cats[$n])){
                                $cats[$n] = array(
                                    'type' => 'twins',
                                    'cat' => array($cat)
                                );
                            }else{
                                $cats[$n]['cat'][] = $cat;
                            }
                        }
                    }else{
                        if($i%2==0) $n++;
                        if(!$tab && $cat->hasChild()){
                            $tab=$i;
                            if($i%2==1){//pt 4 6 8
                                $cats[$n] = array(
                                    'type' => 'single',
                                    'cat' => $categories[$i-1]
                                );
                                
                                $n++;
                                $cats[$n] = array(
                                    'type' => 'tabs',
                                    'cat' => $cat
                                );
                            }else{
                                $n++;
                                $cats[$n] = array(
                                    'type' => 'tabs',
                                    'cat' => $cat
                                );
                            }
                        }elseif($tab && $i %2 == 1 && $tab==$i-1){
                            $n++;
                            $cats[$n] = array(
                                'type' => 'single',
                                'cat' => $cat
                            );
                        }elseif($i%2==0 && $i==$t-1){
                            $cats[$n] = array(
                                'type' => 'single',
                                'cat' => $cat
                            );
                        }else{
                            if(!isset($cats[$n])){
                                $cats[$n] = array(
                                    'type' => 'twins',
                                    'cat' => array($cat,null)
                                );
                            }else{
                                $cats[$n]['cat'][1] = $cat;
                            }
                        }
                    }
                }
                $home_cats = $cats;
            }
            $this->saveControllerCacheData('home_cats',$home_cats);
        }
        $this->share(compact('home_cats','random_posts','hotnews'));

        $this->view('home');
    }
    public function viewCategory($cat)
    {
        if($category = Category::where('slug',$cat)->first()){
            $this->setMenuActiveKey($cat);
            Html::add_before_title($category->name);

            $req = request();
            $paging = new CubePaging($category->countAllPost(),10,$req->get('page'),10);
            $posts = $category->getAllPost(array('@orderby'=>'id desc','@limit'=>$paging->getLimitItem()));
    
            $this->view('list-news',compact('category', 'posts', 'paging'));
        }
        else{
            $this->view(404);
        }
    }

    public function viewDoubleCategory($cat,$child)
    {
        if($parent = Category::where('slug',$cat)->first()){
            Html::add_before_title($parent->name);
            
            $this->setMenuActiveKey($cat);
            $args = array(
                'parent_id' => $parent->id,
                'slug' =>$child
            );
            if($cate = Category::where($args)->first()){
                Html::add_before_title($cate->name);
                
                $req = request();
                $paging = new CubePaging($cate->countPost(),10,$req->get('page'),10);
                $posts = $cate->getPosts('*',array('@orderby'=>'id desc','@limit'=>$paging->getLimitItem()));
                $this->view('list-news',array('category'=>$cate,'posts'=>$posts,'paging'=>$paging));
            }else{
                $this->view(404);
            }
            
            
        }
        else{
            $this->view(404);
        }
    }
    public function viewPost($postname)
    {
        $post = Post::where('slug',$postname)->first();
        if($post){
            if(!_session('view_post_'.$post->id)){
                $post->updateView();
                _session('view_post_'.$post->id,1);
            }
            $cat = $post->getCategory();
            if($parent = $cat->getParent()){
                $this->setMenuActiveKey($parent->slug);
                Html::add_before_title($parent->name);
            }else{
                $this->setMenuActiveKey($cat->slug);
            }
            Html::add_before_title($cat->name);
            Html::add_before_title($post->title);
            $relative = $post->get('*',"id!=$post->id&cat_id=$post->cat_id&@limit=6");
            view('detail-news',array('post'=>$post,'relative'=>$relative));
        }else{
            $this->view(404);
        }
    }

    public function hotPosts()
    {
        $req = request();
        $Str = new Str();
        $Post = new Post();
        $select = 'posts.*,'.$Post->withDateView(7,'week_view');
        $paging = new CubePaging($Post->count(),10,$req->get('page'),10);
        $args = array('@orderby'=>'week_view desc','@limit'=>$paging->getLimitItem());
        $posts = $Post->get($select,$args);
        $listtitle = "Tin nóng";
        $this->view('list-posts',compact('listtitle','posts','paging','error'));
    }
    public function popularPosts()
    {
        $req = request();
        $Str = new Str();
        $Post = new Post();
        $paging = new CubePaging($Post->count(),10,$req->get('page'),10);
        $posts = $Post->orderBy('view','DESC')->limit($paging->getLimitItem())->get();
        $listtitle = "Xem nhiều nhất";
        $this->view('list-posts',compact('listtitle','posts','paging','error'));
    }

    public function search()
    {
        $req = request();
        $Str = new Str();
        $error = '';
        $products = null;
        $posts = null;
        $s = $req->get('s');
        if($s && str_replace(' ', '', $s) != ''){
            $Post = new Post();
            $Post->prepareSearch($s);

            $paging = new CubePaging($Post->countResult(),10,$req->get('page'),10);
            $args = array('@orderby'=>'id desc','@limit'=>$paging->getLimitItem());
            $posts = $Post->getResult($args);
        }else{
            $error = 'Vui lòng nhập từ khóa';
        }
        $this->view('search',compact('posts','paging','error'));
    }

    public function liveSearch()
    {
        $req = request();
        $Str = new Str();
        $error = '';
        $products = null;
        $posts = array();
        $s = $req->request('query');
        if($s && str_replace(' ', '', $s) != ''){
            $Post = new Post();
            $Post->prepareSearch($s);
            $posts = $Post->getResult(array('@orderby'=>'id desc','@limit'=>5));
        }else{
            $error = 'Vui lòng nhập từ khóa';
        }
        $this->view('ajax-search',compact('posts','error'));
    }

    public function shopping($act=null,$item=null)
    {
        $this->setMenuActiveKey('shop');
        $a = strtolower($act);
        $pc = get_controller('ProductController');
        $pc->setViewPath(null);
        if($a=='category'){
            $this->shoppingCategory($item);
        }elseif($a=='view-product'){
            $this->shoppingProduct($item);
        }elseif($a=='add-product'){
            if($this->user){
                $this->share('formtitle','Thêm sản phẩm');
                $pc->addProduct();
            }else{
                redirect(get_home_url('login?next='.get_current_url(true,true)));
            }
        }elseif($a=='update-product'){
            if($this->user){
                $this->share('formtitle','Cập nhật sản phẩm');
                $pc->updateProduct();
            }else{
                redirect(get_home_url('login?next='.get_current_url(true,true)));
            }
        }elseif($a=='owner'){
            if($item && $owner = User::where('username',$item)->first('id,name,username,avatar')){
                $this->listProduct($owner);
            }else{
                $this->alert('Người dung này không tồn tại!');
            }
            
        }
        elseif($a=='order'){
            $this->cartOrder();
        }elseif ($a=='ordered') {
            $this->shopOrdered();
        }
        else{
            $this->listProduct();
        }
    }
    public function shoppingProduct($item = null)
    {
        if($item && $product = Product::findOne($item)){
            Html::add_before_title($product->name);
            $this->share('pagetitle',$product->name);
            $product->updateView();
            $this->view('view-product',compact('product'));
        }else{
            $this->alert('Sản phẩm không tồn tại','danger');
        }
    }
    public function listProduct($owner=null)
    {
        $args = array('id > 0');
        if($owner){
            $this->share('formtitle','Gian hàng của '.$owner->name);
            $args['created_by'] = $owner->id;
        }else{
            $this->share('formtitle','Mua sắm');
        }
        $req = request();
        $prod = new Product();
        $paging = new CubePaging($prod->count($args),12,$req->get('page'));
        
        $products = $prod->orderBy('id','DESC')->limit($paging->getLimitItem())->get('*',$args);
        $this->render('product-list',compact('products','paging', 'owner'));
    }

    public function cart($act=null,$item=null,$val=null)
    {
        $cart = get_controller('CartController');
        $a = strtolower($act);
        $req = request();
        if($a=='add'){
            $status = $cart->addItem($item);
        }elseif($a == 'update'){
            $status = $cart->updateItem($item,$val);
        }elseif($a == 'remove'){
            $status = $cart->remove($item);
        }else{
            $this->viewCart($cart);
            die;
        }
        echo json_encode(array('status' => $status, 'item'=>$item,'count'=>$cart->count(),'money'=>$cart->totalMoney()));
    }
    public function viewCart($cart)
    {
        $this->setMenuActiveKey('cart');
        $product_cart = $cart->get();
        $product_ids = array_keys($product_cart);
        $req = request();
        if(count($product_ids)>0){
            $prods = Product::where('id', $product_ids)->count();
            $paging = new CubePaging($prods,10,$req->get('page'));
            $products = Product::where('id', $product_ids)->orderBy('id','DESC')->limit($paging->getLimitItem())->get('*');

        }else{
            $products = null;
        }
        $this->view('cart',compact('products','product_cart','cart','paging'));
    }

    public function cartOrder()
    {
        $data = array();
        $errors = array();
        $req = request();
        $cart = get_controller('CartController');
        $product_cart = $cart->get();
        $product_ids = array_keys($product_cart);
        $req = request();
        if(count($product_ids)>0){
            $products = Product::where('id', $product_ids)->orderBy('sell_price','DESC')->get('*');

        }else{
            $products = null;
        }
        
        if($req->post('cart_verify')){
            $order = new Order();
            $data = $req->post();
            $formData = $order->formData($data);
            $formData->setFieldsText('form/order');
            $formData->setErrorMessageFile('form/errors');
            $validate = array(
                'email' => 'email',
                'name' => "name",
                'gender' => 'is_numeric',
                'phonenumber' => 'is_numeric',
                'address' => 'len >= 10'
            );

            $acceptData = $formData->validate($validate);
            if(count($errors = $formData->getAllMessage())==0){
                $list = array();
                if($products){
                    foreach($products as $p){
                        $list[] = array('product_id' => $p->id,'order_price' => $p->sell_price, 'qtt'=>$product_cart[$p->id]);
                    }
                }
                $order->list_info = serialize($list);
                $order->total_product = $cart->count();
                $order->total_money = $cart->totalMoney();
                $order->verify = 0;
                if($order->insert($acceptData)){
                    $email = $req->post('email');
                    $name = $req->post('name');
                    $uc = get_controller('ConfirmController');
                    $token = $uc->makeConfirm($email,3,3600*24,$order->id);
                    $link = url('confirm?type=3&token='.$token);
                    $message = $this->getView('mails/confirm-order',compact('name','email','link'));
                    $stt = sendmail($email,"Xác thực đơn hàng",$message);
                    unset($_SESSION['security']);
                    $cart->remove();
                    $this->alert('Đặt hàng thành công! Vui truy cập tải khoản email để xác thực đơn hàng của bạn!');
                    die;
                }
            }
        }elseif($req->post('security')||_session('security')){
            if($req->post('security'))_session('security',$req->post('security'));
            if($this->user){
                $data['name'] = $this->user->name;
                $data['email'] = $this->user->email;
                $data['gender'] = $this->user->gender;
            }
        }
        $this->share(compact('errors','cart','products'));
        $this->share('formtitle',"Đặt hàng");
        $list = '*';
        $this->form('order-form',$data,$list,'form/order', "Order");
    }

    public function shopOrdered()
    {
        if(_session('token') && _session('cf_email') && _session('cf_type')==3){
            $email = _session('cf_email');
            $id = _session('cf_opt_val');
            if($id && $order = Order::findOne($id)){
                $order->verify = 1;
                $order->update();
                $this->alert("Chúc mừng $order->name đã đặt hàng thành công! Chúng tôi sẽ giao hàng cho bạn tròng vòng 48 tiếng!");
                die;
            }
            
    
        }
        $this->alert('Truy cập không hợp lệ');
    }




    



    public function subcribe()
    {
        $data = array();
        $errors = array();
        $req = request();
        if($req->isPost()){
            $subcriber = new Subcriber();
            $data = $req->post();
            $formData = $subcriber->formData($data);
            $formData->setFieldsText('form/user');
            $validate = array(
                'email' => 'email'
            );

            $acceptData = $formData->validate($validate);
            if(count($errors = $formData->getAllMessage())==0){
                $email = $req->post('email');
                if(Subcriber::where('email',$email)->count()>0){
                    $this->alert('Đăng ký theo dõi thành công! Cảm ơn đã ủng hộ Thế Giới Vuông!');
                    die;
                }
                if($subcriber->insert($acceptData)){
                    
                    $link = get_home_url();
                    $message = $this->getView('mails/subcribed',compact('email','link'));
                    $stt = sendmail($email,"Cảm ơn đã ủng hộ Thế Giới Vuông",$message);
                    $this->alert('Đăng ký theo dõi thành công! Cảm ơn đã ủng hộ Thế Giới Vuông!');
                    die;
                }
            }
        }
        $this->share(compact('errors'));
        $this->share('formtitle',"Subcribe");
        $list = 'email';
        $this->form('form',$data,$list,'form/user', "Subcribe");
    }

}
