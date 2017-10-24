<?php 

/**
* ManagerController
*/

use Extensions\CubePaging;
use Models\User;
use Models\Product;
use Models\Gallery;

class ProductController extends Controller
{
    public function __construct()
    {
        if($user = User::getCurrentLogin()){
            $this->user = $user;
        }else{
            //redirect(get_home_url('login?next='.get_current_url(true,true)));
        }
        $this->setViewPath(App::pathinfo(1));
    }

    public function alert($message="Hello World!", $alert_type='success')
    {
        $this->render('alert',array('message'=>$message,'alert_type'=>$alert_type));
    }

    public function confirmDelete($id = 0, $message="bạn có chắc chắn muốn xóa không")
    {
        $this->render('confirm-delete',array('id' => $id, 'message'=>$message));
    }

    // xoa nhieu san pham

    public function deleteProducts($args = null)
    {
        if(!$args) return false;
        $stt = true;
        $cc = get_controller('CartController');
        if($products = Product::where($args)->get()){
            foreach ($products as $p) {
                $this->deleteGalleries(array('product_id'=>$p->id));
                if($f = $p->getFeatureImagePath()){
                    unlink($f);
                }
                if($p->delete()){
                    $cc->remove($p->id);
                }else{
                    $stt = false;
                }
            }
        }
        return $stt;
    }

    // xoa nhieu bo xuu tap

    public function deleteGalleries($args = null)
    {
        if(!$args) return false;
        $stt = true;
        if($galleries = Gallery::where($args)->get()){
            foreach ($galleries as $gallery) {
                if($f = $gallery->getPath()){
                    unlink($f);
                }
                if(!$gallery->delete()){
                    $stt = false;
                }
            }
        }
        return $stt;
    }

    public function index($value='')
    {
        $products = Product::where('id > 0')->orderBy('id', 'DESC')->limit(3)->get();
        $users = User::where('id > 0')->orderBy('id', 'DESC')->limit(3)->get();
        $this->render('home',array('products'=>$products,'users'=>$users));
    }

    public function products()
    {
        $req = request();
        $keywords = null;
		if($s = $req->get('s')){
			if(str_replace(' ','',$s)!=''){
				$keywords = $this->toArrKeywords($s);
			}
		}
        $prod = new Product;
        if($keywords){
            $prod->andWhere('name','like',$keywords);
        }
        $paging = new CubePaging($prod->count(),10,$req->get('page'));
        if($sb = $req->get('sortby')){
			if($od = strtoupper($req->get('orderby'))){
				if($od == 'DESC'){
					$ob = $od;
				}else{
					$ob = "ASC";
				}
			}else{
				$ob = "ASC";
			}
			$prod->orderBy($sb,$ob);
		}else{
			$prod->orderBy('id','DESC');
		};
        if($keywords){
            $prod->andWhere('name','like',$keywords);
        }
        $products = $prod->limit($paging->getLimitItem())->get();
        $this->render('products',array('products'=>$products,'paging'=>$paging));
    }
    public function userProduct($id)
    {
        $user = User::findOne($id);
        if($user){
            $req = request();
            $paging = new CubePaging($user->countOwnProduct(),10,$req->get('page'));
            $args = array('@limit'=>$paging->getLimitItem());
            $products = $user->getOwnProduct($args);
            $this->render('user-products',array('products'=>$products,'user'=>$user,'paging'=>$paging));
        }else{
            $this->alert('Không có kết quả phủ hợp','danger');
        }
    }

    public function viewProduct($id)
    {
        if($product = Product::findOne($id)){
            $this->render('view-product',array('product'=>$product));
        }else{
            $this->alert('Không có kết quả phủ hợp','danger');
        }
    }

    // them san pham moi

    public function addProduct()
    {
        $product = new Product();
        //$this->share('formtitle','Thêm sản phẩm mới');
        $this->saveProduct($product,'insert','Thêm sản phẩm');
    }
    public function updateProduct()
    {
        $req = request();
        if($req->get('id') && $Product = Product::findOne($req->get('id'))){
            //$this->share('formtitle',"Cập nhật thông tin sản phẩm");

            $this->saveProduct($Product,'update','Cập nhật');
        }else{
            $this->alert('Sản phẩm không tồn tại','warning');
        }
    }
    public function saveProduct($product,$type='insert',$btnSaveText="Add")
    {
        $this->share('product', $product);
        $data = $product;
        $type = strtolower($type);
        $req = request();
        $errors = array();
        if($req->isPost()){
            $data = $req->post();
        
            $formData = $product->formData($data);
            $formData->setFieldsText('form/product');
            $validate = array(
                'name' => array('len > 0','unique'),
                'detail' => 'len >= 15',
                'sell_price' => array('is_numeric','val > 0')
            );

            $acceptData = $formData->validate($validate,$type);
            
            if(count($errors = $formData->getAllMessage()) == 0){
                if($file = $req->file('feature_image')){
                    if($file->isImage()){
                        $file->setFilename(uniqid());
                        if($file->move(get_content_dir('products/'))){
                            if($type=='update' && $imgPath = $product->getFeatureImagePath()){
                                unlink($imgPath);
                            }
                            $product->feature_image = $file->getUploadedFilename();
                        }else{
                            $errors['feature_image'] = "không thể upload file đính kèm";
                        }
                    }else{
                        $errors['feature_image'] = "File ảnh đính kèm không hợp lệ";
                    }
                }
            }
            if(count($errors)==0){

                if(isset($acceptData['id'])) unset($acceptData['id']);
                foreach ($acceptData as $key => $value) {
                    $product->{$key} = $value;
                }
                $stt = false;
                $msg = '';
                if($type=='update'){
                    if($product->update()){
                        $stt = true;
                        $msg = 'Cập nhật sản phẩm thành công';
                    }
                    
                }else{
                    $product->created_by = _session('userid');
                    if($product->insert()){
                        $stt = true;
                        $msg = 'Đăng sản phẩm thành công';
                    }
                }
                if($stt){
                    $max = $req->post('gallery_total');
                    if(!$max) $max = 1;
                    $texts = $req->post('gallery_text');
                    $links = $req->post('gallery_link');
                    $gallery = new Gallery();
                    $gallery->product_id = $product->id;
                    for($i=0; $i < $max; $i++){
                        if($file = $req->file('gallery_file_'.$i)){
                            if($file->isImage()){
                                $file->setFilename(uniqid());
                                if($file->move(get_content_dir('gallery/'))){
                                    $gallery->text = isset($texts[$i])?$texts[$i]:'';
                                    $gallery->link = isset($links[$i])?$links[$i]:'';
                                    $gallery->image = $file->getUploadedFilename();
                                    $gallery->insert();
                                    $msg.='<br>File '.$file->getOriginalFilename().' đã được thêm vào gallery';
                                }else{
                                    $msg.='<br>File '.$file->getOriginalFilename().' upload chưa thành công';
                                }
                            }else{
                                $msg.='<br>File '.$file->getOriginalFilename().' không hợp lệ';
                            }
                        }
                    }
                }
                if($stt){
                    $this->redirect('dashboard/products');
                }
                
            }
            $data = arr_parse($data,$acceptData);
        }

        $this->share('errors',$errors);
        $list = 'name,detail,feature_image,sell_price';
        $this->form('product-form',$data,$list,'form/product', $btnSaveText);
    }


    public function deleteProduct()
    {
        $req = request();
        if($id = $req->post('id')){
            if($this->deleteProducts(array('id'=>$id))){
                $this->redirect('dashboard/products');
            }else{
                $this->alert('Đã có lỗi xảy ra. Chưa xóa được sản phẩm!','danger');
            }
        }elseif($id = $req->get('id')){
            if($cat = Product::findOne($id)){
                $this->confirmDelete($id,"Bạn có chắc chắn muốn xóa sản phẩm này không?");
            }else{
                $this->alert('sản phẩm không tồn tại!','danger');
            }
        }else{
            $this->alert('Vui lòng chọn sản phẩm!', 'info');
        }
    }


    // xóa galery

    public function deleteGallery($id=null)
    {
        if(!$id){
            $stt = 0;
            $r = request();
            if($r->post('id') && $ga = Gallery::findOne($r->post('id'))){
                if($path = $ga->getPath()){
                    unlink($path);
                }
                if($ga->delete()){
                    $stt = $r->post('id');
                }
            }
            echo $stt;
            die;
        }
        $stt = false;
        if(Gallery::findOne($id)){
            $stt = $this->deleteGalleries("id = $id");
        }
        if(_request('next')) redirect(_request('next'));
        if($stt){
            $this->alert("Xóa ảnh trong Gallery thành công!");
        }else{
            $this->alert("Chưa xóa ảnh trong Gallery",'warning');
        }
    }




    public function toArrKeywords($text = '')
	{
		$Str = new Str();
		$k1 = $text;
		$k2 = $Str->clearUnicode($text); // khu dau tieng viet
		$arr1 = explode(' ', preg_replace('/\s+/',' ',$text));
		$arr2 = explode(' ', preg_replace('/\s+/',' ',$k2));
		$keywords = array($k1,$k2);
		if(count($arr1)>1){
			$keywords = array_merge($keywords,$arr1);
		}

		if(count($arr2)>1){
			$keywords = array_merge($keywords,$arr2);
		}
		return $keywords;
	}
	
	public function toKeywords($text = '')
	{
		return implode(', ', $this->toArrKeywords($text));
	}
	
}
?>