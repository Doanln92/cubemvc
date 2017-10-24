<?php
// su dung doi tuong bang lam mat na
// require_one TableMask.php 

namespace Models;
use FormData;
class BaseModel
{
	private $tableMask = null;
	
	public $tableName = null;
	
	public $fields = array();
	
	public $idField = 'id';
	// tao doi tuong
	public function __construct($tableName=null){
		if($tableName) $this->tableName = $tableName;
		$mask = new TableMask($this->tableName);
		$this->fields = $mask->fields;
		$this->idField = $mask->idField;
		
	}

	// tao bang mat na de thao tac voi mot bang

	public final function createMask($tableName = null){
		if(!$this->tableMask){
			if($tableName) $tbn = $tableName;
			else $tbn = $this->tableName;
			if(!$tbn) die('ten bang chua dc set');
			$this->tableMask = new TableMask($tbn);
		}
		
	}

	public final function mask(){
		$this->createMask();
		return $this->tableMask;
	}

	public static final function getMask()
	{
		$model = new static();
		return $model->mask();
	}
	

	static function where(){
		//lay mang cac tham so truyen vao 
		$arr = func_get_args();
		// tao ra lop static 
		$model = new static();
		$tb = call_user_func_array(array($model->mask(), 'where'), $arr);

		return $model;
	}

	public static function all(){
		$model = new static();
		return $model->get();
	}

	public static function findOne($id){
		$model = new static();
		$mask = $model->mask();
		// lay ra 1 ban ghi voi id dc chuyen vao
		// su dung doi tuong tableMask va ep kieu ve model
		return $mask->getOne('*',array($mask->idField=>$id),get_class($model));
	}

	public static function find($id){
		$model = new static();
		$mask = $model->mask();
		// lay ra 1 ban ghi voi id dc chuyen vao
		// su dung doi tuong tableMask va ep kieu ve model
		return $mask->getOne('*',array($mask->idField=>$id),get_class($model));
	}



	/**
	 * kiwm tra khong gian ten xem co hop le hay khong
	 * @param string $column ten cot
	 * @param string $value Gia tri can kiem tra
	 * @param string $id (chi them vao neu can kiem tra trong truong hop update)
	 * 
	 * @return boolean
	 */
	 public function checkNamespace($column=null, $value=null,$id=null){
		if(!$column || is_null($value)) return false;
		
		$mask = self::getMask();
		
		$stt = false;
        if(!preg_match('/^([A-Za-z_])+([A-Za-z0-9_\-])*$/',$value)) $stt = false;
        elseif($u = $mask->getOne($mask->idField,array($column=>$value), 'ASSOC')){
            if($id && $u[$mask->idField]==$id) $stt = true;
        }else $stt = true;
        return $stt;
    }

	/**
	 * kiem tra xem co bi trung hay ko
	 * @param string $column ten cot
	 * @param string $value Gia tri can kiem tra
	 * @param string $id (chi them vao neu can kiem tra trong truong hop update)
	 * 
	 * @return boolean
	 */
	 public function checkUnique($column=null, $value=null,$id=null){
		if(!$column || is_null($value)) return false;
		$mask = self::getMask();
		
		$stt = false;
        if($u = $mask->getOne($mask->idField,array($column=>$value), 'ASSOC')){
            if($id && $u[$mask->idField]==$id) $stt = true;
        }else $stt = true;
        return $stt;
	}
	
	/**
     * noi bang
     *
     * @param string 
     * @param string 
     * @param string 
     * 
     * @return Table
     */
    public function join($joinTable, $joinCondition, $joinType = '')
    {
        $this->mask()->join($joinTable,$joinCondition,$joinType);
        return $this;
    }
    

	public function andWhere(){
		// goi toi phuong thuc where cua doi tuong Mask voi tham so cua ham andWhere nay
		$tb = call_user_func_array(array($this->mask(), 'where'), func_get_args());
		return $this;
	}

	public function orWhere(){
		// tuong tu and where
		$tb = call_user_func_array(array($this->mask(), 'orWhere'), func_get_args());
		
		return $this;
	}

	/**
     * ham them dieu kien cho menh de having
     * @param String
     * @param String
     * @param String
     * @param String
     */ 
    
    public function having(){
    	$tb = call_user_func_array(array($this->mask(), 'having'), func_get_args());
		return $this;
    }

	/**
     * ham them dieu kien cho menh de having
     * @param String
     * @param String
     * @param String
     */ 
    
    public function orHaving(){
    	$tb = call_user_func_array(array($this->mask(), 'orHaving'), func_get_args());
		return $this;
    }

    /**
     * 
     * @param String
     */ 
    
    public function groupBy(){
    	$tb = call_user_func_array(array($this->mask(), 'groupBy'), func_get_args());
		return $this;
    }

	public function orderBy($field,$direct=null){
		$tb = call_user_func_array(array($this->mask(), 'orderBy'), func_get_args());
		return $this;
	}

	/**
     * set tham so limit
     * @param String | int | array
     * @param String | int (optional)
     */ 
    


	public function limit(){
    	$tb = call_user_func_array(array($this->mask(), 'limit'), func_get_args());
		return $this;
    }

	/**
	 * @param String $select  Ten cot
	 * @param Mixed $condition Dieu kien
	 * @return Object
	 */ 
	public function get($select='*', $condition = null){
		return $this->mask()->get($select, $condition, get_class($this));
	}

	public function first($select='*', $condition = null){
		//su dung bang mat na de lay ket qua
		return $this->mask()->getOne($select, $condition, get_class($this));
	}

	public function count($condition = null){
		//su dung bang mat na de lay ket qua
		return $this->mask()->count($condition);
	}

	/**
	 * @param Array $data  mang data
	 * 
	 * @return int | false
	 */ 
	
	public function insert($data = null)
	{
		 $insertData = array();
		 if(is_array($data)) $d = $data;
		 else $d = array();
		 $tb = $this->mask();
		 $columns = $tb->getColumns();
		 foreach ($columns as $col) {
			if(isset($this->{$col})){//neu co du lieu cua doi tuong
				$insertData[$col] = $this->{$col};
			}
			if(isset($d[$col])){//neu co du lieu cua data truyen vao
				$insertData[$col] = $d[$col];
			}
		}
		unset($insertData[$tb->idField]);
		if($id = $tb->insert($insertData)){
			$this->{$tb->idField} = $id;
			if(count($d)>0){
				foreach ($columns as $col) {
					if($col!=$tb->idField) $this->{$col} = $insertData[$col];
				}
			}
			return true;
		}
		return false;
	}
	
	/**
	 * @param Array $data mang data
	 * @return boolean
	 */ 
	
	

	public function update($data=null)
	{
		$updateData = array();
		if(is_array($data)) $d = $data;
		else $d = array();
		$tb = $this->mask();
		$columns = $tb->getColumns();
		foreach ($columns as $col) {
			if(isset($this->{$col})){
				$updateData[$col] = $this->{$col};
			}
			if(isset($d[$col])){
				$updateData[$col] = $d[$col];
			}
		}
		unset($updateData[$tb->idField]);
		if($tb->update($updateData,array($tb->idField=>$this->id))){
			if(count($d)>0){
				foreach ($columns as $col) {
					if($col != $tb->idField)$this->{$col} = $updateData[$col];
				}
			}
			return true;
		}
		return false;
	}

	/**
	 * xoa ban ghi hien tai
	 * @return boolean
	 */ 
	

	public function delete(){
		// goi den phuong thuc delete cua tableMask voi id cua man ghi hien hanh
		$mask = $this->mask();
		return $mask->delete(array($mask->idField=>$this->id));
		// $query = "DELETE FROM $this->tableName WHERE id='$this->id'";
		// $conn = self::getConnect()->prepare($query);
	}
	public function getQueryBuilder($type='SELECT', $args = null, $condition = null)
	{
		return call_user_func_array(array($this->mask(),'getQueryBuilder'),func_get_args());
	}


	public function setNickName($value='')
	{
		$this->mask()->setNickName($value);
		return $this;
	}

	/**
     * noi bang
     *
     * @param string 
     * @param string 
     * 
     * @return Table
     */
    
    public function leftJoin($joinTable, $joinCondition){
        return $this->join($joinTable,$joinCondition, 'left');
    }
    public function rightJoin($joinTable, $joinCondition){
        return $this->join($joinTable,$joinCondition, 'right');
    }
    public function outerJoin($joinTable, $joinCondition){
        return $this->join($joinTable,$joinCondition, 'outer');
    }
    public function innerJoin($joinTable, $joinCondition){
        return $this->join($joinTable,$joinCondition, 'inner');
    }
    public function leftOuterJoin($joinTable, $joinCondition){
        return $this->join($joinTable,$joinCondition, 'left outer');
    }
    public function rightOuterJoin($joinTable, $joinCondition){
        return $this->join($joinTable,$joinCondition, 'right outer');
    }
    
    public function getQuery($args = null)
	{
		return $this->mask()->getQuery($args);
	}


	public function getConnect()
	{
		return DB::getConnect();
	}

	/**
	* convert object to array
	* @param Object
	*/ 
	public static function convertObject($d) {
		if (is_object($d)) {
			$d = get_object_vars($d);
		}

		if (is_array($d)) {
			return array_map(__METHOD__, $d);
		}
		else {
			return $d;
		}
	}

	public function formData($form=null)
	{
		$id = null;
		if(isset($this->{$this->idField})) $id = $this->{$this->idField};
		$fd = new FormData($form);
		$fd->setTable($this->tableName,$id);
		return $fd;
	}

}

