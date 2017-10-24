<?php 

namespace Models;

/**
* Product
*/
class Product extends BaseModel
{
	public $tableName='products';
	public function owner(){
		use_model('User');
		$owner = User::findOne($this->created_by);
		return $owner;
	}

	public function getFeatureImage()
    {
        if($this->feature_image) $f = $this->feature_image;
        else $f = 'default.png';
        return get_content_url('products/'.$f);
    }

    public function getFeatureImagePath()
    {
        if(!isset($this->id)) return null;
        if($this->feature_image) return get_content_dir('products/'.$this->feature_image);
        return null;
        
    }
    public function getGallery()
    {
        if(!isset($this->id)) return null;
        return Gallery::where('product_id',$this->id)->get();
    }

    public function updateView()
    {
        if(!_session('view_product_'.$this->id)){
            $this->view++;
            $this->update();
            _session('view_product_'.$this->id,1);
        }
    }

    public function getTime($format = null)
    {
        if(!$format) $format="d/m/Y";
        return date($format,strtotime($this->created_at));
    }
}

 ?>