<?php
/**
* AdminController
*/

use Models\User;
use Models\Category;
use Models\Post;
use Models\PostMeta;
use Models\Product;

use Extensions\CubePaging;

class AdminController extends Controller
{
	protected $user;
	function __construct()
	{
		# code...
		if($user = User::getCurrentLogin()){
			$this->user = $user;
		}else{
			redirect(get_home_url('login?next='.get_current_url(true,true)));
		}

		
		$this->setViewPath('dashboard');
	}

	
	public function alert($message="Hello World!", $alert_type='success')
	{
		$this->view('alert',array('message'=>$message,'alert_type'=>$alert_type));
	}

	public function confirmDelete($id = 0, $message="bạn có chắc chắn muốn xóa không")
	{
		$this->view('confirm-delete',array('id' => $id, 'message'=>$message));
	}

	public function dashboard($obj=null,$act=null,$item=null)
	{
		//Html::input('text','s','',null);
		$obj = strtolower($obj);
		$act = strtolower($act);
		if($obj != 'products' && $this->user->level < 1){
			$this->alert("Truy cập bị từ chối",'danger');
			die;
		}
		
		if($obj=='categories')
			$this->categories($act);
		elseif($obj=='posts')
			$this->posts($act);
		elseif($obj=='users')
			$this->users($act);
		elseif($obj=='products')
			$this->products($act,$item);
		elseif ($obj=='update-image')
			$this->updatePostImage();
		else{

			$posts = Post::where('id>0')->orderBy('id', 'DESC')->limit(3)->get();
			$categories = Category::where('id>0')->orderBy('id', 'DESC')->limit(3)->get();
			$users = User::where('id>0')->orderBy('id', 'DESC')->limit(3)->get();
			$products = Product::where('id>0')->orderBy('id', 'DESC')->limit(3)->get();
			
			$this->view('index',compact('posts','categories','users','products'));
		}
	}


	// user
	public function users($act = null)
	{
		$req = request();
		if($act=='add'){
			if($this->user->level < 2){
				$this->alert("Bạn không có quyền xem trang này",'danger');
				die;
			}
			$user = new User();
			$this->share('formtitle',"Thêm người dùng mới");
			$this->saveUser($user,
				explode(',', 'name,birth_date,gender,avatar,level,username,email,password,confirmpassword'),
				$act,'Thêm mới');
		}
		elseif($act=='update'){
			if($req->get('id') && $user = User::findOne($req->get('id'))){
				if($this->user->level < 2 && $this->user->level <= $user->level){
					$this->alert("Bạn không có quyền xem trang này",'danger');
					die;
				}
				$this->share('formtitle',"Cập nhật thông tin người dùng");

				$this->saveUser($user,
					explode(',', 'name,birth_date,gender,avatar,level'),
					$act,'Cập nhật');
			}else{
				$this->alert('Chủ đề không tồn tại','warning');
			}
		}
		elseif($act=='delete'){
			$this->deleteUser();
		}else{
			$this->listUser($act);
		}
	}

	public function listUser($act='members')
	{
		$list = array("members" => 0,"contents" => 1,"managers" => 2, 'boss' => 3);
		$a = strtolower($act);
		if(!isset($list[$a])) $a = 'members';
		
		$lv = $list[$a];
		
		if($this->user->level < $lv){
			$this->alert("Bạn không có quyền xem trang này",'danger');
			die;
		}

		$args = array();

		
		if($lv > 0){
			$args['level'] = $lv;
		}
		$req = request();
		$keywords = null;
		if($s = $req->get('s')){
			if(str_replace(' ','',$s)!=''){
				$keywords = $this->toArrKeywords($s);
			}
		}
		
		$User = new User();
		if($keywords){
			$User->andWhere('name','like',$keywords)->orWhere('email','like',$keywords);
		}
		
		$cate_total = '('.(Category::where('created_by',"@{$User->tableName}.{$User->idField}")
							->getQueryBuilder('COUNT')).') as cate_total';
		
		$post_total = '('.(Post::where('posted_by',"@{$User->tableName}.{$User->idField}")
							->getQueryBuilder('COUNT')).') as post_total';

		$product_total = '('.(Product::where('created_by',"@{$User->tableName}.{$User->idField}")
							->getQueryBuilder('COUNT')).') as product_total';
		$select = "id,name,username,email,avatar,created_at, $cate_total, $post_total, $product_total";

		$paging = new CubePaging($User->count($args),10,$req->get('page'));
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
			$User->orderBy($sb,$ob);
		}else{
			$User->orderBy('id','DESC');
		}
		$cate_total = "(SELECT)";
		if($keywords){
			$User->andWhere('name','like',$keywords)->orWhere('email','like',$keywords);
		}
		
		$users = $User->limit($paging->getLimitItem())->get($select,$args);
		$this->render('user-'.$a,compact('users','paging'));
	}

	protected function saveUser($user, $fieldList=array(),$type='insert',$btnSaveText="Add")
	{
        $data = $user;
        $req = request();
        $errors = array();
        if($req->isPost()){
        	$validate = array(
                'name' => array('name','len >= 2'),
                'gender' => 'is_numeric',
                'birth_date' => 'is_date',
                'username' => array('len > 3','accessName'),
                'email' => 'unique_email',
                'password' => array('len >= 6','@md5'),
                'confirmpassword' => array('md5'=>md5($req->post('password')))
            );
            $data = $req->post();
            
            $formData = $user->formData($data);
            $formData->setFieldsText('form/user');
            $formData->setErrorMessageFile('form/errors');
            
            $validateList = array();
            foreach($fieldList as $key){
            	if(isset($validate[$key])){
            		$validateList[$key] = $validate[$key];
            	}
            }

            $dataAccept = $formData->applyAfterValidate($validateList,$type);
            $errors = $formData->getAllMessage();
            if($file = $req->file('avatar')){
                if($file->isImage()){
                    if(count($errors)==0){
                        $file->setFilename(uniqid());
                        if($file->move(get_content_dir('users/avatar/'))){
                            
                            $user->avatar = $file->getUploadedFilename();
                        }else{
                            $errors['avatar'] = "không thể upload avatar";
                        }
                    }
                }else{
                    $errors['avatar'] = "avatar không hợp lệ";
                }
            }// update avatar
        
            if(count($errors)==0){
                foreach($dataAccept as $key => $value){
                    $user->{$key} = $value;
                }
                if($type=='update'){
                	if($user->update()){
                		$this->redirect('dashboard/users');
                    	die;
                	}
                    
                }else{
                    
                	if($user->insert()){
	                    $this->redirect('dashboard/users');
	                    die;
	                }
                }

                
            }
        }
        $this->share('errors',$errors);
        $this->form('form',$data,$fieldList,'form/user',$btnSaveText);
	}


	public function deleteUser()
	{
		$req = request();
		if($id = $req->post('id')){
			if($id == _session('userid')){
				$this->alert('Bạn không thể thực hiện được thao tác này!','danger');
			}
			elseif($this->deleteUsers(array('id'=>$id))){
				$this->redirect('dashboard/users');
			}else{
				$this->alert('Đã có lỗi xảy ra. Chưa xóa được người dùng!','danger');
			}
		}elseif($id = $req->get('id')){
			if($id == _session('userid')){
				$this->alert('Bạn không thể thực hiện được thao tác này!','danger');
			}
			elseif($user = User::findOne($id)){
				if($this->user->level < 2 && $this->user->level <= $user->level){
					$this->alert("Bạn không có quyền xem trang này",'danger');
					die;
				}
				
				$this->confirmDelete($id,"Xóa người dùng này thì tất cả các chủ đề và bài viết của người này cũng sẽ bị xóa.<br>Bạn có chắc chắn muốn xóa người dùng này không?");
			}else{
				$this->alert('người dùng không tồn tại!','danger');
			}
		}else{
			$this->alert('Vui lòng chọn người dùng!', 'info');
		}
	}

	protected function deleteUsers($args=null)
	{
		if(is_null($args)) return false;
		$stt = true;
		if($users = User::where($args)->get()){
			$pc = get_controller('ProductController');
			foreach ($users as $user) {
				if(!$this->deleteCat(array('created_by'=>$user->id))){// xoa chu de
					$stt = false;
				}elseif(!$this->deletePosts(array('posted_by'=>$user->id))){// xoa post
					$stt = false;
				}elseif(!$pc->deleteProducts(array('created_by'=>$user->id))){// xoa san pham
					$stt = false;
				}
				if($stt){
					if($user->avatar){
						unlink(get_content_dir('users/avatar/'.$user->avatar)); // xoa avatar
					}
					if(!$user->delete()){// xoa user
						$stt = false;
					}
				}
			}
		}
		return $stt;
	}

	// end user


	// category
	public function categories($act = null)
	{
		$req = request();
		if($act=='add'){
			$cat = new Category();
			$this->share('formtitle',"Thêm chủ đề mới");
			$this->saveCategory($cat,$act,'Thêm mới');
		}
		elseif($act=='update'){
			if($req->get('id') && $cat = Category::findOne($req->get('id'))){
				$this->share('formtitle',"Cập nhật chủ đề");

				$this->saveCategory($cat,$act,'Cập nhật');
			}else{
				$this->alert('Chủ đề không tồn tại','warning');
			}
		}
		elseif($act=='delete'){
			$this->deleteCategory();
		}
		else{
			$this->listCategories();
		}
	}
	public function listCategories()
	{
		$req = request();
		$keywords = null;
		if($s = $req->get('s')){
			if(str_replace(' ','',$s)!=''){
				$keywords = $this->toArrKeywords($s);
			}
		}
		$Category = new Category();
		if($keywords){
			$Category->andWhere('name','like',$keywords);
		}
		$paging = new CubePaging($Category->count(),10,$req->get('page'),10);
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
			$Category->orderBy($sb,$ob);
		}
		if($keywords){
			$Category->andWhere('name','like',$keywords);
		}
		$categories = $Category->limit($paging->getLimitItem())->get();
		$this->view('category-list',compact('categories','paging'));
	}

	public function saveCategory($cat,$type='insert',$btnSaveText="Add")
	{
		$data = $cat;
        $type = strtolower($type);
        $req = request();
        $errors = array();
        if($req->isPost()){
			$data = $req->post();
			if(!$req->post('slug')) $data['slug'] = $req->post('name'); 
            $formData = $cat->formData($data);
            $formData->setFieldsText('form/manager-category');
			$formData->setErrorMessageFile('form/errors');
			
            $validate = array(
                'name' => 'unique',
                'slug' => array('@Str->getNamespace','namespace')
            );

            if(isset($data['parent_id']) && $data['parent_id'] !='no'){
            	$validate['parent_id'] = array('val != '.($type=='update'?$cat->id:'0'));
            }
            
            $acceptData = $formData->validateAfterApply($validate,$type);
            
            if(count($errors = $formData->getAllMessage()) == 0){
            	if(isset($acceptData['id'])) unset($acceptData['id']);
            	foreach ($acceptData as $key => $value) {
            		$cat->{$key} = $value;
            	}
            	
                if($type=='update'){
                	if($cat->update()){
                		$this->redirect('dashboard/categories');
                    	die;
                	}
                    
                }else{
                    $cat->created_by = _session('userid');
                	
                	if($cat->insert()){
                	    $this->redirect('dashboard/categories');
                	    die;
                	}
                }
            }
        }

        $this->share('errors',$errors);
        $list = 'name,slug,description,parent_id';
        $this->form('category-form',$data,$list,'form/manager-category', $btnSaveText);
	}

	public function deleteCat($args=null)
	{
		if(is_null($args)) return false;
		if(is_numeric($args)) $args = array('id'=>$args);
		$stt = true;
		if($cats = Category::where('id>0')->get('*',$args)){
			foreach ($cats as $c) {
				if($c->hasChild()){
					if(!$this->deleteCat(array('parent_id'=>$c->id))) $stt = false;
				}
				if($c->hasPost()){
					if(!$this->deletePosts(array('cat_id'=>$c->id))) $stt = false;
				}
				if($stt){
					if(!$cat->delete()) $stt = false;
				}
			
			}
		}
		return $stt;
	}

	public function deleteCategory()
	{
		$req = request();
		if($id = $req->post('id')){
			if($this->deleteCat("id=$id")){
				$this->redirect('dashboard/categories');
			}else{
				$this->alert('Đã có lỗi xảy ra. chưa xóa được chủ đề!','danger');
			}
		}elseif($id = $req->get('id')){
			if($cat = Category::findOne($id)){
				$this->confirmDelete($id,"Nếu bạn xóa chủ đề này thì tất cả các chủ đề con và bài viết cũng sẽ bị xóa. <br>Bạn có chắc chắn muốn thực hiên diều này?");
			}else{
				$this->alert('Chủ đề không tồn tại!','danger');
			}
		}else{
			$this->alert('Vui lòng chọn chủ đề!', 'info');
		}
	}
	// end category






	// post

	public function posts($act = null)
	{
		if($act=='add'){
			$post = new Post();
			$this->share('formtitle',"Đăng tin bài mới");
			$this->savePost($post,$act,'Thêm mới');
		}
		elseif($act=='update'){
			$req = request();
			if($req->get('id') && $post = Post::findOne($req->get('id'))){
				$this->share('formtitle',"Cập nhật tin bài");

				$this->savePost($post,$act,'Cập nhật');
			}else{
				$this->alert('bài viết không tồn tại','warning');
			}
		}
		elseif($act=='delete'){
			$this->deletePost();
		}else{
			$this->listPost();
		}
	}

	public function listPost()
	{
		$req = request();
		
		$Post = new Post();
		$args = array();
		if($sb = $req->get('sortby')){
			if($sb!='cate_name') $sb = 'p.'.$sb;
			else{
				$sb = 'c.name';
			}
			if($od = strtoupper($req->get('orderby'))){
				if($od == 'DESC'){
					$ob = $od;
				}else{
					$ob = "ASC";
				}
			}else{
				$ob = "ASC";
			}
			$args['@orderby'] = $sb.' '.$ob;
		}else{
			$args['@orderby'] = 'p.id DESC';
		}
		$keywords = null;
		if($s = $req->get('s')){
			if(str_replace(' ','',$s)!=''){
				$keywords = $s;
			}
		}
		if($keywords){
			$Post->prepareSearch($s);

			$paging = new CubePaging($Post->countResult(),10,$req->get('page'),10);
			$args['@limit']=$paging->getLimitItem();
			$posts = $Post->getResult($args,'p.*, c.name as cate_name');

		}else{
			$Post->setNickName('p');
		
			$args['@join'] = 'categories as c : c.id=p.cat_id : inner';
			
			$paging = new CubePaging($Post->count($args),10,$req->get('page'),10);
			
			$posts = $Post->limit($paging->getLimitItem())->get('p.*, c.name as cate_name',$args);
		}
		$this->view('post-list',array('posts'=>$posts,'paging'=>$paging));
	}
	



	protected function savePost($post,$type='insert',$btnSaveText="Add")
	{
		$data = $post;
        $type = strtolower($type);
        $req = request();
        $errors = array();
        if($req->isPost()){
            $data = $req->post();
            if(isset($data['short_desc']) && !$data['short_desc'] && isset($data['content'])){
            	$data['short_desc'] = Str::short($data['content'],100);
            }
            if(isset($data['slug']) && !$data['slug'] && isset($data['title'])){
            	$data['slug'] = $data['title'];
            }
            

            $formData = $post->formData($data);
            $formData->setFieldsText('form/manager-post');
            $formData->setErrorMessageFile('form/errors');
            $validate = array(
                'title' => 'unique',
                'slug' => array('@Str->getNamespace','namespace'),
                'cat_id' => 'val != no',
                'content' => 'len >= 30'
            );

            $acceptData = $formData->validateAfterApply($validate,$type);
            
            if(count($errors = $formData->getAllMessage()) == 0){
            	if($file = $req->file('image')){
					if($file->isImage()){
						$file->setFilename(uniqid());
						if($file->move(get_content_dir('posts/'))){
							if($type=='update' && $imgPath = $post->getFeatureImagePath()){
								unlink($imgPath);
							}
							$post->image = $file->getUploadedFilename();
						}else{
							$errors['image'] = "không thể upload file đính kèm";
						}
					}else{
						$errors['image'] = "File ảnh đính kèm không hợp lệ";
					}
				}
            }

            if(count($errors)==0){

            	if(isset($acceptData['id'])) unset($acceptData['id']);
            	foreach ($acceptData as $key => $value) {
            		$post->{$key} = $value;
            	}
            	$stt = false;
                if($type=='update'){
                	if($post->update()){
                		$stt = true;
                	}
                    
                }else{
                    $post->posted_by = _session('userid');
                	$post->post_time = date('Y-m-d H:i:s');
                	if($post->insert()){
                	    $stt = true;
                	}
				}
				
				if($stt){
					if(isset($post->title)) $this->savePostMeta($post->id,'keywords',' '.$this->toKeywords($post->title).',');
					$this->redirect('dashboard/posts');
				}
            }
            $data = arr_parse($data,$acceptData);
        }

        $this->share('errors',$errors);
        $list = 'title,slug,cat_id,content,short_desc';
        $this->form('post-form',$data,$list,'form/manager-post', $btnSaveText);
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
	
	public function savePostMeta($post_id,$name='meta',$content='')
	{
		$pm = PostMeta::where('post_id',$post_id)->andWhere('name',$name)->first();
		if(!$pm){
			$pm = new PostMeta();
		}
		$pm->name = $name;
		$pm->post_id = $post_id;
		$pm->content = is_array($content)?serialize($content):$content;
		if(!isset($pm->id)) return $pm->insert();
		return $pm->update();
	}


	

	public function deletePost()
	{
		$req = request();
		if($id = $req->post('id')){
			if($this->deletePosts("id=$id")){
				$this->redirect('dashboard/posts');
			}else{
				$this->alert('Đã có lỗi xảy ra. Chưa xóa được bài viết!','danger');
			}
		}elseif($id = $req->get('id')){
			if($cat = Post::findOne($id)){
				$this->confirmDelete($id,"Bạn có chắc chắn muốn thực hiên diều này?");
			}else{
				$this->alert('Bài viết không tồn tại!','danger');
			}
		}else{
			$this->alert('Vui lòng chọn bài viết!', 'info');
		}
	}

	public function deletePosts($args=null)
	{
		if(!$args) return false;
		$stt = true;
		if($posts = Post::where($args)->get()){
			foreach($posts as $post){
				if($img = $post->getFeatureImagePath()){
					unlink($img);
				}
				if(!$post->delete()){
					$stt = false;
				}
			}
		}
		return $stt;
	}

	public function updatePostImage()
	{
		if($posts = Post::all()){
			$n = 2;
			include_once LIBCLASSDIR.'image.php';
			foreach ($posts as $post) {
				if($imgPath = $post->getFeatureImagePath()){
				
				$im = image::getsity($imgPath);
				$img = new image($imgPath);
				$file = uniqid($post->slug).'.'.$im['type'];
				if($img->save(get_content_dir('posts/'.$file),$im['type'])){
					$post->image = $file;
					$post->update();
				}
				}
		
			}
		}
	}

	public function updateAllPostKeywords()
	{
		$n = 0;
		$s = 0;
		if($posts = Post::all()){
			foreach($posts as $p){
				if($this->savePostMeta($p->id,'keywords',' '.$this->toKeywords($p->title).',')){
					$s++;
				}
				$n++;
			}
			
		}
		echo "dã cập nhật thành công $s/$n tin bài";
	}

	// end post


	public function products($act=null,$item=null)
	{
		$a = strtolower($act);
		$ctrl = get_controller('ProductController');
		if($a == 'add'){
			$ctrl->addProduct($item);
		}
		elseif($a == 'update'){
			$ctrl->updateProduct($item);
		}
		elseif($a == 'user-product'){
			$ctrl->userProduct($item);
		}
		elseif($a == 'view-product'){
			$ctrl->viewProduct($item);
		}
		elseif($a == 'delete'){
			$ctrl->deleteProduct($item);
		}
		elseif($a == 'delete-gallery'){
			$ctrl->deleteGallery($item);
		}
		elseif($a == 'view'){
			$ctrl->viewProduct($item);
		}
		else{
			$ctrl->products();
		}
	}
}
?>