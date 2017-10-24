<?php 

/**
* ManagerController
*/

use Extensions\CubePaging;
use Models\User;
use Models\Product;
use Models\Gallery;

class ManagerController extends Controller
{
	public function __construct()
	{
		# code...
		//use_model('User');
		if($user = User::getCurrentLogin()){
			$this->user = $user;
		}else{
			redirect(get_home_url('login?next='.get_current_url(true,true)));
		}
		
		//use_model('Product,Gallery');
		//use_extension('CubePaging');
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

	public function deleteUsers($args = null)
	{
		if(!$args) return false;
		$stt = true;
		if($users = User::where($args)->get()){
			foreach ($users as $user) {
				if($this->deleteProducts(array('created_by'=>$user->id))){
					if($user->avatar){
						if(file_exists($file = get_content_dir('users/avatar/'.$user->avatar))){
							unlink($file);
						}
					}
					if(!$user->delete()) $stt = false;
				}
				
			}
		}
		return $stt;
	}

	// xoa nhieu san pham

	public function deleteProducts($args = null)
	{
		if(!$args) return false;
		$stt = true;
		if($products = Product::where($args)->get()){
			foreach ($products as $p) {
				$this->deleteGalleries(array('product_id'=>$p->id));
				if($f = $p->getFeatureImagePath()){
					unlink($f);
				}
				if(!$p->delete()){
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


	public function users()
	{
		$req = request();
		$user = User::where('id > 0');
		$paging = new CubePaging($user->count(),10,$req->get('page'));
		$args = array('@limit'=>$paging->getLimitItem());
		$users = $user->get('id,name,email,avatar',$args);
		$this->render('users',array('users'=>$users,'paging'=>$paging));
	}
	public function products()
	{
		$req = request();
		$prod = Product::where('id > 0');
		$paging = new CubePaging($prod->count(),10,$req->get('page'));
		$args = array('@limit'=>$paging->getLimitItem());
		$products = $prod->get('*',$args);
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
		$this->share('pagetitle','Thêm sản phẩm mới');
		$this->saveProduct($product,'insert','Thêm sản phẩm');
	}
	public function updateProduct()
	{
		$req = request();
		if($req->get('id') && $Product = Product::findOne($req->get('id'))){
			$this->share('pagetitle',"Cập nhật thông tin sản phẩm");

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
                	$this->alert($msg);
                	die;
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
				$this->alert('Xóa sản phẩm thành công!');
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


	// them thanh vien moi
	public function addUser()
	{
		$req = request();
		$errors = array();
		$data = $req->post();
		if($req->isPost()){
			$user = new User();
			$count = 0;
			$Str = new Str();
			foreach($data as $name => $value){
				if(in_array($name,$user->fields) && $name!=$user->idField){
					switch ($name) {
						case 'name':

							if($value && !preg_match('/[^A-z\s]/', $Str->vi2en($value))){
								$user->{$name} = $value;
								$count++;
							}else{
								$errors[$name] = "Tên không hợp lệ ";
							}
							break;

						case 'username':
							$value = $Str->getNamespace2($value);
							$data[$name] = $value;
							if($value && User::checkUsername($value)){
								$user->{$name} = $value;
								$count++;
							}else{
								
								$errors[$name] = "Tên đăng nhập không hợp lệ hoạc đã được sử dụng bởi người khác";
							}
							break;

						case 'email':
							if($value && User::checkEmail($value)){
								$user->{$name} = $value;
								$count++;
							}else{
								$errors[$name] = "Email không hợp lệ hoạc đã được sử dụng bởi người khác";
							}
							break;

						case 'password':
							if($value){
								if(strlen($value)>5){
									$user->{$name} = md5($value);
									$count++;
								}else{
									$errors[$name] = "Mật khẩu quá ngắn";
								}
							}else{
								$errors[$name] = "Mật khẩu không được bỏ trống";
							}
							break;

						default:
							# code...
							break;
					}
					
				}
			}
			if($file = $req->file('avatar')){
				if($file->isImage()){
					$file->setFilename(uniqid());
					if($file->move(get_content_dir('users/avatar/'))){
						$user->avatar = $file->getUploadedFilename();
					}else{
						$errors['avatar'] = "không thể upload avatar";
					}
				}else{
					$errors['avatar'] = "avatar không hợp lệ";
				}
			}
			
			
			if($count==4 && count($errors)==0){
				if($user->insert()){
					$msg = "Thêm người dùng thành công!";
					$this->alert($msg);
					die;
				}
			}
		}
			
		$args = array('errors'=>$errors,'form'=>$data,'action'=>'add');
		$this->render('user-form',$args);
	}


	public function updateUser()
	{
		$req = request();
		$errors = array();
		$data = $req->post();
		$user = new User();
		if($req->post('id') && $user = User::findOne($req->post('id'))){
			$count = 0;
			$Str = new Str();
			foreach($data as $name => $value){
				if(in_array($name,$user->fields) && $name!=$user->idField){
					switch ($name) {
						case 'name':
							$user->{$name} = $value;
							if($value && !preg_match('/[^A-z\s]/', $Str->vi2en($value))){
								$count++;
							}else{
								$errors[$name] = "Tên không hợp lệ ";
							}
							break;

						case 'username':
							$value = $Str->getNamespace2($value);
							$user->{$name} = $value;
							if($value && User::checkUsername($value,$user->id)){
								$count++;
							}else{
								
								$errors[$name] = "Tên đăng nhập không hợp lệ hoạc đã được sử dụng bởi người khác";
							}
							break;

						case 'email':
							$user->{$name} = $value;
							if($value && User::checkEmail($value,$user->id)){
								
								$count++;
							}else{
								$errors[$name] = "Email không hợp lệ hoạc đã được sử dụng bởi người khác";
							}
							break;

						case 'password':
							if($value){
								if(strlen($value)>5){
									$user->{$name} = md5($value);
								}else{
									$errors[$name] = "Mật khẩu quá ngắn";
								}
							}
							break;

						default:
							# code...
							break;
					}
					
				}
			}
			if($file = $req->file('avatar')){
				if($file->isImage()){
					$file->setFilename(uniqid());
					if($file->move(get_content_dir('users/avatar/'))){
						if($user->avatar){
							unlink(get_content_dir('users/avatar/'.$user->avatar));
						}
						$user->avatar = $file->getUploadedFilename();
					}else{
						$errors['avatar'] = "không thể upload avatar";
					}
				}else{
					$errors['avatar'] = "avatar không hợp lệ";
				}
			}
			
			
			if($count==3 && count($errors)==0){
				if($user->update()){
					$msg = "cập nhật dùng thành công!";
					$this->alert($msg);
					die;
				}
			}
		}elseif($req->get('id') && $user = User::findOne($req->get('id'))){
			// ko lm gi ca
		}elseif($req->post('id') || $req->get('id')){
			$this->alert("không có người dùng nào phù hợp với thông tin đã cung cấp");
			die;
		}else{
			$this->alert("Vui lòng chọn một người dùng");
			die;
		}
			
		$args = array('errors'=>$errors,'user'=>$user);
		$this->render('user-update',$args);
	}


	public function deleteUser()
	{
		$req = request();
		if($id = $req->post('id')){
			if($this->deleteUsers(array('id'=>$id))){
				$this->alert('Xóa người dùng thành công!');
			}else{
				$this->alert('Đã có lỗi xảy ra. Chưa xóa được người dùng!','danger');
			}
		}elseif($id = $req->get('id')){
			if($user = User::findOne($id)){
				$this->confirmDelete($id,"Bạn có chắc chắn muốn xóa người dùng này không?");
			}else{
				$this->alert('người dùng không tồn tại!','danger');
			}
		}else{
			$this->alert('Vui lòng chọn người dùng!', 'info');
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
}
?>