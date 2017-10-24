<?php
/**
* ShoppingController
*/
use Models\Product;

class CartController extends Controller
{
	public $name;
    protected $cart = array();
    protected $model = null;

    public function __construct() {
        $this->name = 'CART'; 
        if($cart = _session($this->name)){
            $this->cart = $cart;
        }
        $this->model = new Product();
    }
    
    public function count(){
        $total = 0;
        if(is_array($this->cart) && count($this->cart)>0){
        	foreach ($this->cart as $value) {
        		$total+=$value;
        	}
        }
        return $total;
    }
    
    
    public function get($item=null){
        if(!is_null($item)) return isset($this->cart[$item])?$this->cart[$item]:0;
        return $this->cart;
    }
    public function getQuantity($item){
        return $this->get($item);
    }
    
    public function addItem($item){
        if(isset($this->cart[$item])){
            if($this->cart[$item]<20){
                $this->cart[$item]++;
                $this->updateCart();
                return true;
            }
        }elseif($this->model->mask()->getVal('id',"id=$item")){
            $this->cart[$item]=1;
            $this->updateCart();
            return true;
        }
        return false;
        
    }
    
    public function updateItem($item, $qty=null){
        if(isset($this->cart[$item]) && is_numeric($qty) & $qty > 0){
            if($qty <= 20){
                $this->cart[$item] = $qty;
                $this->updateCart();
                return true;
            }
        }
        return false;
    }
    
    public function remove($item=null){
        if(!is_null($item)){
            if(isset($this->cart[$item])){
                unset($this->cart[$item]);
                $this->updateCart();
                return true;
            }
            return false;
        }
        $this->cart = array();
        $this->updateCart();
        return true;
    }
    
    
    protected function updateCart(){
        _session($this->name,$this->cart);
    }

    public function totalMoney()
    {
        $total = 0;
        if($this->cart){
            $ids = array_keys($this->cart);
            if($products = Product::where('id',$ids)->get('id,sell_price')){
                foreach ($products as $p) {
                    $total+= ($p->sell_price*$this->cart[$p->id]);
                }
            }
        }
        return $total;
    }
    
}
?>