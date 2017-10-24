<?php

namespace Models;

use Str;
class Post extends BaseModel
{
    public $tableName = 'posts';
    protected $searchParamters = array();
	public function __construct()
	{
		parent::__construct('posts');
    }
    public function updateView()
    {
        $this->view++;
        $this->update();
        $pv = new PostView();
        $pv->updateView($this->id);
    }
    public function delete()
    {
        $meta = PostMeta::getMask();
        $meta->where('post_id',$this->id);
        $view = PostView::getMask();
        $view->where('post_id',$this->id);
        if($meta->delete() && $view->delete()){
            return $this->delete();
        }
        return false;
    }
    public function getCategory($select='*', $args = null)
    {
        return Category::findOne($this->cat_id);
	}
	
	public function getOwner()
	{
		return User::findOne($this->posted_by);
    }
    public function getTime($format = null)
    {
        if(!$format) $format="d/m/Y";
        return date($format,strtotime($this->post_time));
    }
    public function getFeatureImage($width='auto',$heigt='auto')
    {
        if($this->image) $f = $this->image;
        else $f = 'default.png';
        if($width == 'auto' && $heigt == 'auto'){
            return get_content_url('posts/'.$f);
        }
        $img=get_controller('ImageController');
        return $img->getResize('contents/posts/'.$f,$width,$heigt);
    }

    public function getFeatureImagePath()
    {
        if($this->image) return get_content_dir('posts/'.$this->image);
        return null;
        
    }

    public function getUrl()
    {
        return get_home_url('chi-tiet/'.$this->slug.'.html');
    }


    public function withDateView($dayView=7, $mask='date_view')
    {
        $p = DB_PREFIX;
        $select = "(SELECT SUM(pv.view_total) FROM {$p}post_views as pv WHERE pv.post_id={$this->tableName}.id AND pv.view_date>DATE_SUB(NOW(), INTERVAL $dayView DAY) GROUP BY pv.post_id) AS $mask";
        return $select;
    }
    public function prepareSearch($query)
    {
        $Str = new Str();
        $keywords = null;
        if(preg_match('/(^\"[^\"]*+\"$|^\'[^\']*+\'$)/',$query)){
            $query = substr($query,1,strlen($query)-2);
            $keywords = array($query,$Str->clearUnicode($query));
        }else{
            $k1 = $query;
            $k2 = $Str->clearUnicode($query); // khu dau tieng viet
            $arr1 = explode(' ', preg_replace('/\s+/',' ',$query));
            $arr2 = explode(' ', preg_replace('/\s+/',' ',$k2));
            $keywords = array($k1,$k2);
            if(count($arr1)>1){
                $keywords = array_merge($keywords,$arr1);
            }

            if(count($arr2)>1){
                $keywords = array_merge($keywords,$arr2);
            }
        }
        for($i=0;$i<count($keywords);$i++){
            if($i>1){
                $keywords[$i] = ' '.$keywords[$i].',';
            }
        }
        $args = array(
            'pm.content like' => $keywords,
            'pm.name' => 'keywords',
            '@join' => 'post_meta pm : pm.post_id=p.id : inner, users u : u.id = p.posted_by : inner, categories c : c.id = p.cat_id : inner'
        );
        $this->setNickname('p');
        $this->searchParamters = $args;
        return $this->andWhere($args);
    }
    function countResult($args=null){
        if(!$args)
            $args = $this->searchParamters;
        return self::where($args)->setNickname('p')->count();
    }

    function getResult($args=null,$select='p.*,c.name as cat_name, u.name as author, pm.content as keywords'){
        $posts = self::where($this->searchParamters)->setNickname('p')->get($select,$args);
        return $posts;
        //
    }



}