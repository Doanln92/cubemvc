<?php

// require_once 'BaseModel file';
namespace Models;

class Category extends BaseModel
{
	public $tableName = 'categories';
	public function __construct()
	{
		parent::__construct('categories');
	}
	
    
	public function getOwner()
	{
		
		return User::findOne($this->created_by);
	}
	public function getParent($select='*', $args = null)
	{
		return self::getMask()->where('id',$this->parent_id)->getOne($select,$args,__CLASS__);
	}

	public function getParentName(Type $var = null)
	{
		if($cat = $this->getParent()){
			return $cat->name;
		}
		return null;
	}

	public function getChildren($select='*', $args = null)
    {
        return self::getMask()->where('parent_id',$this->id)->get($select,$args,__CLASS__);
	}
	public function hasChild($args = null)
    {
        return self::getMask()->where('parent_id',$this->id)->count($args);
	}
	
	public function getPosts($select='*', $args = null)
	{
		return Post::where('cat_id',$this->id)->get($select,$args);
	}

	public function hasPost($args = null)
	{
		use_model('Post');
		return (Post::where('cat_id',$this->id)->count($args)>0?true:false);
	}

	public function countPost($args = null)
	{
		use_model('Post');
		return Post::where('cat_id',$this->id)->count($args);
	}
	
	public function countAllPost($args = null)
	{
		$Post = Post::where($args);
		$Post->andWhere('p.cat_id',$this->id);
		$Post->setNickName('p');
		if($this->hasChild()){
			$Post->leftJoin($this->tableName.' c','c.id=p.cat_id');
			$Post->orWhere('c.parent_id',$this->id);
		}
		return $Post->count();
	}

	public function getAllPost($args = null)
	{
		
		$Post = new Post();
		$Post->setNickName('p');
		$Post->andWhere('p.cat_id',$this->id);
		if($this->hasChild()){
			$Post->leftJoin($this->tableName.' c','c.id=p.cat_id');
			$Post->orWhere('c.parent_id',$this->id);
		}
		$posts = $Post->get('p.*',$args);
		return $posts;
	}

	public function getUrl()
	{
		$path = null;
		if($p = $this->getParent()){
			$path .= $p->slug.'/';
		}
		$path .= $this->slug.'.chn';
		return get_home_url($path);

	}

	public function deletePosts($callback=null)
	{
		$stt = true;
		if($posts = Post::where('cat_id',$this->id)->get()){
			if(is_callable($callback)){
				foreach($posts as $post){
					if($post->delete()){
						$a = call_user_func_array($callback,array($post));
					}else{
						$stt = false;
					}
				}
			}
			else{
				foreach($posts as $post){
					if(!$post->delete()){
						$stt = false;
					}
				}
			}
		}
		return $stt;
	}

	public function deleteChildren($callback=null)
	{
		$stt = true;
		if($children = Category::where('parent_id',$this->id)->get()){
			if(is_callable($callback)){
				foreach($children as $child){
					if($child->delete()){
						$a = call_user_func_array($callback,array($child));
					}else{
						$stt = false;
					}
				}
			}
			else{
				foreach($children as $child){
					if(!$child->delete()){
						$stt = false;
					}
				}
			}
		}
		return $stt;
	}
	
}