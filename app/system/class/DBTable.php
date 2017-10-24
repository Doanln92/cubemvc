<?php

/**
* @author Le Ngoc Doan
*/
class DBTable
{
	protected $tableName = null;

	public function __construct($tableName=null)
	{
		if($tableName) $this->tableName = $tableName;
	}

	public function create()
	{
		return db::createTable('test_user',function($table){
			$table->id('id');
			$table->field('name')->type('varchar')->length(64);
			$table->field('birth_date')->type('date');
			$table->field('gender')->type('int')->length(1);
			$table->collate('utf8_vietnamese_ci');
		});
	}

	public function drop()
	{
		return db::dropTable('twst_user');
	}
}





/**
* DBCreateTable
*/

class DBCreateTable
{
	protected $tableName = null;
	protected $fields = array();
	protected $constrains = array();

	protected $_engin = null;
	protected $_charset = 'utf8';
	protected $_collate = 'utf8_unicode_ci';

	public static $types = array();
	public static $collations = array();
	public function __construct($tableName)
	{
		$this->tableName = $tableName;
		self::setTypesAndCollations();
	}
	public function addField()
	{
		$args = func_get_args();
		$field = null;
		$prs = '';
		foreach($args as $key => $value){
			$prs.= "\$args[$key], ";
		}
		$prs = trim($prs,', ');
		eval("\$field = new DBCreateTableField($prs);");
		$this->fields[] = $field;
		return $field;
	}
	public function field()
	{
		$args = func_get_args();
		$field = null;
		$prs = '';
		foreach($args as $key => $value){
			$prs.= "\$args[$key], ";
		}
		$prs = trim($prs,', ');
		eval("\$field = new DBCreateTableField($prs);");
		$this->fields[] = $field;
		return $field;
	}
	public function id($name)
	{
		if(is_string($name)){
			$field = new DBCreateTableField($name);
			$field->setID();
			$this->fields[] = $field;
			return $field;
		}
		throw new Exception("the name of field must be string!", 1);
	}
	public function unique($name)
	{
		if(is_string($name)){
			$field = new DBCreateTableField($name);
			$field->type('varchar')->unique_key();
			$this->fields[] = $field;
			return $field;
		}
		throw new Exception("the name of field must be string!", 1);
	}
	public static function setTypesAndCollations()
	{
		if(count(self::$types) > 0) return;
		ob_start();
		self::$types = require(RESOURCESDIR.'/sys/SQLDataTypes.php');
		self::$collations = require(RESOURCESDIR.'/sys/SQLCollations.php');
		ob_clean();
	}
	public function toQuery()
	{
		$str = 'CREATE TABLE '.$this->tableName.'(
	';
		$fields = $this->fields;
		$t = count($fields);
		for ($i=0; $i < $t; $i++) { 
			$field = $fields[$i];
			$str.= $field->toString().(($i==$t-1)? '' : ',
	');
		}
		$str.='
)';
		if($this->_engin){
			$str .= " ENGINE = ".$this->_engin;
		}
		if($this->_charset || $this->_collate){
			$str .= " DEFAULT ";
			if($this->_charset){
				$str.= 'CHARSET = '. $this->_charset.' ';
			}
			if($this->_collate){
				$str.= 'COLLATE = '. $this->_collate;
			}

		}
		$str.= ';';
		return $str;
	}

	public function engine($engine=null)
	{
		if(is_string($engine)){
			$engine_list = array('csv'=>'CSV', 'innodb'=>'InnoDB', 'memory'=>'MEMORY', 'myisam'=>'MyISAM', 'mrg_myisam'=>'MRG_MyISAM', 'aria'=>'Aria', 'sequence'=>'SEQUENCE');
			if(in_array($engine, $engine_list)){
				$this->_engin = $engine;
			}
			elseif(isset($engine_list[strtolower($engine)])){
				$this->_engin = $engine_list[strtolower($engine)];
			}
		}
		return $this;
	}

	public function charset($charset=null)
	{
		if($charset){
			$this->_charset = $charset;
		}
		return $this;
	}

	public function collate($collate = null)
	{
		if($collate && self::checkCollation($collate)){
			$this->_collate = $collate;
		}
		return $this;
	}

	public static function getTypes()
	{
		if(count(self::$types) < 1) self::setTypesAndCollations();
		return self::$types;
	}
	public static function getCollations()
	{
		if(count(self::$collations) < 1) self::setTypesAndCollations();
		return self::$collations;
	}
	public static function checkType($type=null)
	{
		if($type){
			try{
				$t = strtoupper($type);
				$types = self::getTypes();
				if(in_array($t, $types['text'])) return 'text';
				if(in_array($t, $types['number'])) return 'number';
				if(in_array($t, $types['datetime'])) return 'datetime';
			}catch(exception $e){
				return false;
			}

		}
		return false;
	}

	public static function checkCollation($collate=null)
	{
		if($collate){
			try{
				$t = strtolower($collate);
				$collates = self::getCollations();
				if(in_array($t, $collates)) return true;
			}catch(exception $e){
				return false;
			}

		}
		return false;
	}

}



/**
* DBCreateTableField
*/
class DBCreateTableField
{
	protected $_name = null;
	protected $_type = 'int';
	protected $_length = null;
	protected $_attributes = null;
	protected $_default = PDODBNULL;
	protected $_collate = null;
	protected $_null = 'NOT';
	protected $_AI = null;
	protected $_key = null;

	public function __construct($name)
	{
		$argsName = array('name','type','length','attributes','default','collate','null','ai','key');
		$fi = array();
		if(is_string($name)){
			$fi['name'] = $name;
			$args = func_get_args();
			$t = count($args);
			for($i = 1; $i < $t; $i++){
				if(isset($argsName[$i])){
					$fi[$argsName[$i]] = $args[$i];
				}
			}
		}elseif(is_array($name)){
			foreach ($name as $key => $value) {
				if(is_numeric($key)){
					if(isset($argsName[$key])){
						$fi[$argsName[$key]] = $value;
					}
				}elseif(is_string($k = strtolower($key))){
					if(in_array($k, $argsName)){
						$fi[$k] = $value;
					}
				}
			}
		}
		if(count($fi) > 0 && isset($fi['name'])){
			foreach ($fi as $key => $value) {
				switch ($key) {
					case 'name':
						$this->name($value);
						break;
					case 'type':
						$this->type($value);
						break;
					case 'length':
						$this->length($value);
						break;
					case 'attributes':
						$this->attr($value);
						break;
					case 'default':
						$this->setDefault($value);
						break;
					case 'collate':
						$this->collate($value);
						break;
					case 'null':
						if($value && strtoupper($value)!='NOT'){
							$this->setNull();
						}
						break;
					case 'ai':
						if($value){
							$this->auto_increment();
						}
						break;
					case 'key':
						$this->setKey($value);
						break;
					
					default:
						# code...
						break;
				}
			}
		}else{
			die('thieu thong tin truong trong viect tao bang');
		}
	}

	public function name($name=null)
	{
		if(is_string($name)){
			$this->_name = $name;
		}
		return $this;
	}
	public function type($type=null)
	{
		if(is_string($type)){
			$type = strtoupper($type);
			if(DBCreateTable::checkType($type)) $this->_type = $type;
		}
		return $this;
	}
	public function length($length=null)
	{
		if(is_string($length) || is_int($length)){
			$this->_length = $length;
		}
		return $this;
	}
	public function attr($attr=null)
	{
		$this->_attributes = $attr;
		return $this;
	}
	public function setDefault($val = PDODBNULL)
	{
		if($val != PDODBNULL){
			$this->_default = $val;
		}
		return $this;
	}
	public function collate($collate=null)
	{
		if($collate && DBCreateTable::checkCollation($collate)){
			$this->_collate = $collate;
		}
		return $this;
	}
	public function nl()
	{
		$this->_null = null;
		return $this;
	}
	public function not_null()
	{
		$this->_null = 'NOT';
		return $this;
	}
	public function auto_increment()
	{
		$this->_AI = true;
	}

	public function setKey($KeyType=null)
	{
		if($KeyType){
			$k = strtoupper($KeyType);
			$keys = array('PRIMARY','UNIQUE','INDEX','FULLTEXT','SPATIAL');
			if(in_array($k, $keys)){
				$this->_key = $k;
			}
		}
		return $this;
	}

	public function set_key($KeyType=null)
	{
		$this->setKey($KeyType);
		return $this;
	}

	public function setPrimaryKey()
	{
		$this->setKey('primary');
		return $this;
	}
	public function PrimaryKey()
	{
		$this->setKey('primary');
		return $this;
	}
	public function primary_key()
	{
		$this->setKey('primary');
		return $this;
	}

	public function setUniqueKey()
	{
		$this->setKey('Unique');
		return $this;
	}
	public function UniqueKey()
	{
		$this->setKey('Unique');
		return $this;
	}
	public function unique_key()
	{
		$this->setKey('Unique');
		return $this;
	}

	public function setIndexKey()
	{
		$this->setKey('Index');
		return $this;
	}
	public function IndexKey()
	{
		$this->setKey('Index');
		return $this;
	}
	public function index_key()
	{
		$this->setKey('Index');
		return $this;
	}

	public function setID()
	{
		$this->primary_key();
		$this->auto_increment();
		$this->length(11);
		return $this;
	}

	public function set_id()
	{
		return $this->setID();
	}

	public function id()
	{
		return $this->setID();
	}

	public function toString()
	{
		$name = $this->_name;
		$type = $this->_type;
		$length = $this->_length;
		$attributes = $this->_attributes;
		$default = $this->_default;
		$collate = $this->_collate;
		$null = $this->_null;
		$AI = $this->_AI;
		$key = $this->_key;
		
		$str = $name . ' ';
		$str.= $type;
		$str.= is_null($length)?' ' : '('.$length.') ';
		$str.= ($attributes)? $attributes.' ' : '' ;
		$str.= ($default!=PDODBNULL)?'DEFAULT '.$default.' ': '';
		$str.= $null?$null.' NULL ' : '' ;
		$str.= $AI?'auto_increment ' : '' ;
		$str.= $key?$key.' KEY ' : '' ;

		$str = trim($str);
		return $str;

	}
}


class DBAlterTable{
	protected $tableName = null;
	protected $commands = array();
	public function __construct($tableName)
	{
		$this->tableName = $tableName;
	}

	public function addColumn($columnName)
	{
		$field = new DBCreateTableField($columnName);
		$this->commands[] = array('add',$field);
		return $field;
	}
	
	public function dropColumn($columnName)
	{
		$this->commands[] = array('drop_column',$columnName);
		return $this;
	}

	public function addPrimaryKeys($columns)
	{
		$this->commands[] = array('add_primary_key',$columns);
		return $this;
	}

	public function dropPrimaryKeys()
	{
		$this->commands[] = array('drop_primary_key');
		return $this;
	}

	public function addUniqueKeys($columns)
	{
		$this->commands[] = array('add_unique_key',$columns);
		return $this;
	}

	public function dropUniqueKeys()
	{
		$this->commands[] = array('drop_unique_key');
		return $this;
	}

	

	public function modify($columnName)
	{
		$field = new DBCreateTableField($tableName);
		$this->commands[] = array('modify',$field);
		return $field;
	}
	public function addConstraint($constraintName)
	{
		$constraint = new DBConstraint($constraintName);
		$this->commands[] = array('add_constraint',$constraint);
		return $constraint;
	}
	public function dropConstraint($constraintName)
	{
		$this->commands[] = array('drop_constraint',$constraintName);
		return $this;
	}


	public function rename($newName)
	{
		$this->commands[] = array('rename_to',$newName);
		return $this;
	}
	public function toQuery()
	{
		$str = 'ALTER TABLE '.$this->tableName.'
	';
		$t = count($this->commands);
		for($i = 0; $i < $t; $i++){
			$cms = $this->commands[$i];
			$cm = $cms[0];
			$command = strtoupper(str_replace('_', ' ', $cm));
			if($cm == 'add' || $cm == 'modify' || $cm == 'add_constraint'){
				$str.=$command.' '.$cms[1]->toString();
			}elseif($cm == 'add_primary_key' || $cm == 'add_unique_key'){
				$str.=$command.' ('.$cms[1].')';
			}elseif($cm == 'drop_primary_key'){
				$str.=$command;
			}elseif($cm == 'drop_column' || $cm == 'drop_constraint'){
				$str.=$command.' '.$cms[1];
			}else{
				$str.=$command.' '.$cms[1];
			}
			if($i<$t-1){
				$str.=',
	';
			}else{
				$str.=';
';
			}
		}
		$str.='COMMIT;';
		return $str;
	}
}

/**
* 
*/
class DBConstraint
{
	protected $constraintName;
	protected $type = null;
	protected $val = null;
	public function __construct($constraintName)
	{
		$this->constraintName = $constraintName;
	}
	public function primaryKeys($columns)
	{
		$this->type = 'PRIMARY KEY';
		$this->val = '('.$columns.')';
	}
	public function foreignKey($column,$tbRef,$tbRefColumn=null)
	{
		$rk = ($tbRefColumn)?$tbRefColumn:$column;
		$this->type = "FOREIGN KEY";
		$this->val = '('.$column.') REFERENCES '.$tbRef.'('.$rk.')';
	}
	public function unique($columns)
	{
		$this->type = 'UNIQUE';
		$this->val = '('.db::quote($columns).')';
	}
	public function check($condition)
	{
		$this->type = 'CHECK';
		$this->val = '('.$condition.')';
	}
	public function toString()
	{
		return $this->type.' '.$this->val;
	}
}

?>