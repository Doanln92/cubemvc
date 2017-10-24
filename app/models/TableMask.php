<?php 
/**
 * @author Le Ngoc Doan
 * @copyright 2017
 * @version 1.0
 */
namespace Models;
use PDO;
use DB;

/**
* TableMask
*/
class TableMask
{
    protected $conn;

    public $name;
    public $tableName;
    public $nickName = null;
    public $fields = array();
    public $idField = '';
    public $fieldTypes = array();
    protected $accessName = null;


    /**
     * @var Array $_join mang cac bang muon joind rong 1 lan thucc thi se dc reset moi khi thuc thi xong
     */
    
    protected $_join = array();
    
    /**
     * @var Array $_where mang cac dieu kien trong menh de where
     */
     
    protected $_where = array();
    
    /**
     * @var Array $_having mang cac dieu kien trong menh de having
     */
     
    protected $_having = array();
    
    /**
     * @var Array $_groupby mang cac field trong menh de group by
     */
     
    protected $_groupby = array();
    
    /**
     * @var Array $_groupby mang cac field trong menh de order by
     */
     
    protected $_orderby = array();
    
    /**
     * @var Mixed $_limit bien limit co 3 gia tri la null, int, array
     */
     
    protected $_limit = null;
    
    protected $_conditionQuery = '';
    
    protected $_query = '';
    
    protected $_params = array();
    
    protected $_paramKeys = array();
    

    protected $_prefix = '';

    public $queryBuilder = '';


    
    /**
     * cac bien cho thuc thi PDO
     */ 
     
    
    
    protected $_stmt;
    
    
    
    protected static $_keywords = array('FROM', 'JOIN', 'WHERE', 'GROUPBY', 'HAVING', 'ORDERBY', 'GROUPBY', 'LIMIT');
    protected static $_oprators = array('=', '>', '>=', '<', '<=', '<>', '!=', 'LIKE', 'NOTLIKE', 'NOT');
    protected static $_valFuncs = array('FIELD', 'INC', 'DEC', 'RAND', 'RANDTEXT');
    protected static $_keyFuncs = array('OR', 'AND' ,'NOT', 'LIKE', 'NOTLIKE');
    
    
    public $isConnect = false;
    
    protected $error_message = null;
    
    protected static $reportLevel = 0;
    
    
    /**
     * ham khoi tao bang de thao tac voi mysql
     * @param String $tableName ten bang, co the bao gom mat na viet sau dau cach
     * @param String $nickName mat na cua bang
     * 
     * 
     * @date 2017-09-19
     * 
     * @author Doanln
     */ 
    
    public function __construct($tableName='table',$nickName='') {
        
        $this->connect();
        $a = explode('--', preg_replace('/\s+/', '--', trim($tableName)));
        $this->name = $tableName;
        $this->tableName = $a[0];
        if(count($a)>1){
            $this->nickName = $a[count($a)-1];
        }
        if(preg_match('/^[A-z]+\w*$/',$nickName)){
            $this->nickName = $nickName;
        }
        $this->accessName = $this->tableName.($this->nickName?' '.$this->nickName:'');
        $p = $this->_prefix;
        $fields = array();
        $types = array();
        $stmt = $this->query("DESCRIBE {$p}{$a[0]}");
        if($tableFields = $stmt->fetchAll(PDO::FETCH_ASSOC)){
            foreach($tableFields as $column){
                $f = $column['Field'];
                if($column['Key']=='PRI'){
                    $this->idField = $f;
                }
                $fields[] = $f;
                $b = explode('(', $column['Type']);
                $types[$f] = $b[0];
            }
        }
        $this->fields = $fields;
        $this->fieldTypes = $types;
    }
    
    /**
     * thiwt lap mat danh cho ten bang
     * @param String $nickName
     * @date 2017-08-20
     * 
     * @return TableMask Object
     */ 
    

    public function setNickName($nickName='')
    {
        if(preg_match('/^[A-z]+\w*$/',$nickName)){
            $this->nickName = $nickName;
        }
        $this->accessName = $this->tableName.($this->nickName?' '.$this->nickName:'');
        return $this;
    }


    /**
     * ham khoi tao doi tuong pdodb
     * @param String $host
     * @param String $dbname ten csdl
     * @param String $user ten nguoi dung
     * @param String $pass mat khau de truy cap csdl
     * 
     * @date 2017-07-15
     * 
     * @author Doanln
     */ 
    
    public function connect() {
        $this->isConnect = false;
        try{
            $this->conn = DB::getConnect();
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES,TRUE);
            $this->_prefix = DB::getPrefix();
            $this->isConnect = true;
        }
        catch(exception $e){
            $this->error_message = "\nCannot connect to Database \n <!-- ".$e->getMessage().' -->';
            $this->reportError();
        }
        return $this->isConnect;
    }
    
    /**
     * lay ra ten cac cot trong bang
     * 
     * @return array
     */ 
    
    public function getColumns()
    {
        $p = $this->_prefix;
        $fields = array();
        $stmt = $this->query("DESCRIBE {$p}{$this->tableName}");
        if($tableFields = $stmt->fetchAll(PDO::FETCH_ASSOC)){
            foreach($tableFields as $column){
                $f = $column['Field'];
                $fields[] = $f;
            }
        }
        return $fields;
    }

        /**
     * lay cac ban ghi trong csdl
     * @param String $select         Danh sach cac cot can select
     * @param Mixed $condition       dieu kien de select (co the su dung string hoac array) bao gom ca where, group by, having, order by, limit
     * 
     * 
     * 
     * @return Array | Obj
     */ 
    
    
    public function get($select='*',$condition=null,$fetch_type=null){
        $rs = null;
        $tableName = $this->accessName;
        if(is_array($select)) $select = implode(', ', $select);
        
        try{
            $fetch = PDO::FETCH_ASSOC;
            $ft = null;
            if($fetch_type){
                if(class_exists($fetch_type)){
                    $fetch = PDO::FETCH_CLASS;
                    $ft = $fetch_type;
                }
                else{
                    $fetch = $this->getFetchType($fetch_type);
                }
                
            }
            
            $query = "SELECT $select FROM {$this->_prefix}{$tableName} ".$this->buildCondition($condition);
            $this->_query = $query;

            $this->createQueryBuilder($query,$this->_params);
            
            $stmt = $this->conn->prepare($query);
            if($stmt->execute($this->_params)){
                if($ft){
                    $rs = $stmt->fetchAll($fetch,$ft);
                }else{
                    $rs = $stmt->fetchAll($fetch);
                }
                $stmt->closeCursor();
            }
            $this->reset();
        }catch(PDOException $e){
            $msg = $e->getMessage();
            $this->error_message = $msg;
            $this->reportError();
        }
        
        
        return $rs;
    }
    
    /**
     * lay 1 ban ghi trong csdl
     * @param String $select         Danh sach cac cot can select
     * @param Mixed $condition       dieu kien de select (co the su dung string hoac array) bao gom ca where, group by, having, order by, limit
     * 
     * 
     * 
     * @return Array | Obj
     */ 
    
    public function getOne($select='*',$condition=null,$fetch_type=null){
        $rs = null;
        if(is_array($select)) $select = implode(', ', $select);
        try{
            $tableName = $this->accessName;
            $fetch = PDO::FETCH_ASSOC;
            $ft = null;
            if($fetch_type){
                if(class_exists($fetch_type)){
                    $fetch = PDO::FETCH_CLASS;
                    $ft = $fetch_type;
                }
                else{
                    $fetch = $this->getFetchType($fetch_type);
                }
                
            }
            $this->limit(1);
            $query = "SELECT $select FROM {$this->_prefix}{$this->accessName} ".$this->buildCondition($condition);
            $this->_query = $query;
            $this->createQueryBuilder($query,$this->_params);
            
            $stmt = $this->conn->prepare($query);
            if($stmt->execute($this->_params)){
                if($ft){
                    if($rs1 = $stmt->fetchAll($fetch,$ft)){
                        
                        if(isset($rs1[0])) $rs = $rs1[0];
                    }
                }else{
                    $rs = $stmt->fetch($fetch);
                }
                $stmt->closeCursor();
            }
            $this->reset();
        }catch(PDOException $e){
            $msg = $e->getMessage();
            $this->error_message = $msg;
            $this->reportError();
        }
        
        return $rs;
    }
    
    
    /**
     * lay cac ban ghi trong csdl
     * @param String $column         ten cot
     * @param Mixed $condition       dieu kien de select (co the su dung string hoac array) bao gom ca where, group by, having, order by, limit
     * 
     * 
     * @return Array | Obj
     */ 
    
    public function getVal($column=null,$condition=null){
        $rs = null;
        if($rss = $this->getOne($column,$condition,'assoc')){
            $ex = explode(' ',trim($column));
            $key = $ex[count($ex)-1];
            if(isset($rss[$key])){
                $rs = $rss[$key];
            }else{
                foreach($rss as $v){
                    return $v;
                }
            }
        }
        return $rs;
    }
    
    /**
     * lay so cac ban ghi trong csdl
     * @param Mixed $condition       dieu kien de select (co the su dung string hoac array) bao gom ca where, group by, having, order by, limit
     * 
     * 
     * @return int
     */ 
    
    public function count($condition = null){
        $count = 0;
        $rs = $this->getVal('COUNT(1) AS total',$condition);
        if(is_numeric($rs)) $count = $rs;
        return $count;
    }
    
    /**
     * lay cac ban ghi trong csdl
     * @param Mixed $condition       dieu kien de xoa (co the su dung string hoac array) bao gom ca where, group by, having, order by, limit
     * 
     * @return int
     */ 
    
    public function delete($condition = null){
        $rs = false;
        $this->conn->beginTransaction();
        try{
            $query = "DELETE FROM {$this->_prefix}{$this->accessName} ".$this->buildCondition($condition); 
            $this->_query = $query;
            $this->createQueryBuilder($query,$this->_params);
            $stmt = $this->conn->prepare($query);
            if($stmt->execute($this->_params)){
                $rs = $stmt->rowCount();
                if($rs===0) $rs = true;
            }
            $this->conn->commit();
            $stmt->closeCursor();
            $this->reset();
        }catch(PDOException $e){
            $this->conn->rollback();
            $msg = $e->getMessage();
            $this->error_message = $msg;
            $this->reportError();
        }
        return $rs;
    }
    
    /**
     * them du lieu vao bang
     * @param Array $data          Du lieu can chen
     * 
     * @return int
     */
     
    public function insert($data){
        $rs = false;
        if($d = $this->parseData($data)){
            $this->conn->beginTransaction();
            try{
                $query = $this->buildUpdateDataQuery($d); 
                $this->_query = $query;
                $this->createQueryBuilder($query,$this->_params);
                $stmt = $this->conn->prepare($query);
                if($stmt->execute($this->_params)){
                    $rs = $this->conn->lastInsertId($this->idField);
                }
                $this->conn->commit();
                $stmt->closeCursor();
                $this->reset();
            }catch(PDOException $e){
                $this->conn->rollback();
                $msg = $e->getMessage();
                $this->error_message = $msg;
                $this->reportError();
            }
        }
        return $rs;
    }
    /**
     * update du lieu vao bang
     * @param Array $condition     dieu kien update
     * 
     * @return int
     */
     
    public function update($data, $condition = array()){
        $rs = false;
        if($d = $this->parseData($data)){
            $this->conn->beginTransaction();
            try{
                $query = $this->buildUpdateDataQuery($d, "UPDATE"). "  ".$this->buildCondition($condition); 
                $this->_query = $query;
                $this->createQueryBuilder($query,$this->_params);
                $stmt = $this->conn->prepare($query);
                if($stmt->execute($this->_params)){
                    $this->rowCount = $stmt->rowCount();
                    $rs = true;
                }
                $this->conn->commit();
                $stmt->closeCursor();
                $this->reset();
            }catch(PDOException $e){
                $this->conn->rollback();
                $msg = $e->getMessage();
                $this->error_message = $msg;
                $this->reportError();
            }
        }
        
        return $rs;
    }


    public function getQueryBuilder($type='select',$args=null,$condition=null)
    {
        $queryBuilder = '';
        switch(strtoupper($type)){
            case 'IBSERT':
                $query = $this->buildUpdateDataQuery($this->parseData($data)); 
                $queryBuilder = $this->createQueryBuilder($query,$this->_params);
                    
            break;
            case 'UPDATE':
                $query = $this->buildUpdateDataQuery($this->parseData($data),'UPDATE'). "  ".$this->buildCondition($condition); 
                $queryBuilder = $this->createQueryBuilder($query,$this->_params);
            break;
            case 'COUNT':
                //
                $query = "SELECT COUNT(1) FROM {$this->_prefix}{$this->accessName} ".$this->buildCondition($args);
                $queryBuilder = $this->createQueryBuilder($query,$this->_params);
            break;
            case 'DELETE':
                $query = "DELETE FROM {$this->_prefix}{$this->accessName} ".$this->buildCondition($args); 
                $queryBuilder = $this->createQueryBuilder($query,$this->_params);
            break;
            default:
                if(!$args) $args = '*';
                if(is_array($args)) $args = implode(', ', $args);
                $query = "SELECT $args FROM {$this->_prefix}{$this->accessName} ".$this->buildCondition($condition);
                $queryBuilder = $this->createQueryBuilder($query,$this->_params);
            break;
            
        }
        $this->reset();
        return $queryBuilder;
    }
    /**
     * kiwm tra bang co ton tai hay ko
     * @param String $tableName ten bang
     * 
     * @return Bool
     */ 
    
    public function exists(){
        try{
            $stmt = $this->conn->query("SELECT 1 FROM {$this->_prefix}{$this->accessName} limit 1");
            return true;
        }catch(Exception $e){
            return false;
        }
    }
    

    public function query($query){
        $rs = 0;
        try{
            $this->_stmt = $this->conn->query($query);
            return $this->_stmt;
        }catch(exception $e){
            $msg = $e->getMessage();
            $this->error_message = $msg;
            $this->reportError();
        }
        return $rs;
    }
    /**
     * thuc thi query
     * @param string
     * 
     * @return int
     */ 
    
    public function exec($query){
        $rs = null;
        try{
            $this->_stmt = $this->conn->exec($query);
            return $this->_stmt;
        }catch(Exception $e){
            $msg = $e->getMessage();
            $this->error_message = $msg;
            $this->reportError();
        }
        return $rs;
    }
    
    /**
     * ham thuc thi chuoi truy van voi tham so truyen vao
     * @param String $query        Chuoi truy van
     * @param Array $params        Tham so
     * 
     * @return PDOStatement Obj
     */ 
    
    public function execute($query, $params = array()){
        $stm = null;
        try{
            $this->createQueryBuilder($query,$params);
            $stmt = $this->conn->prepare($query);
            if($stmt->execute($params)){
                $this->_stmt = $stmt;
                $stm = $stmt;
            }
        }catch(PDOException $e){
            $msg = $e->getMessage();
            $this->error_message = $msg;
            $this->reportError();
        }
        
        return $stm;
    }
    
    
    
    public function getStmt(){
        return $this->_stmt;
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
        $allowedTypes = array('LEFT', 'RIGHT', 'OUTER', 'INNER', 'LEFT OUTER', 'RIGHT OUTER');
        $joinType = strtoupper(trim($joinType));

        if ($joinType && !in_array($joinType, $allowedTypes)) {
            return $this;
        }
        $this->_join[] = Array($this->_prefix.$joinTable, $joinCondition, $joinType);
        return $this;
    }
    
    
    
   


    /**
     * ham them dieu kien cho menh de where
     * @param String
     * @param String
     * @param String
     * @param String
     */ 
    
    public function where(){
        // tham so truyen vao
        $args = func_get_args();
        $t = count($args);
        $prop = null;
        $value = PDODBNULL;
        $operator = '=';
        $cond = 'AND';
        if($t == 0) return $this;
        $prop = $args[0];
        if(!$prop) return $this;
        if($t==2){
            $value = $args[1];
        }
        if($t>=3){
            $value = $args[2];
            $operator = $args[1];
            if($t>3) $cond = $args[3];
        }
        if($value==PDODBNULL){
            if(is_array($prop)){
                $this->buildConditionByArray($prop);
            }
            elseif($w = $this->buildSingleWhereParamString($prop)){

                $this->_where[] = $w;
            }
        }else{
            if($w = $this->parseWherePar($prop,$value,$operator)){
                $this->_where[] = array($w,$cond);
            }
            
        }
        return $this;
    }

    public function orWhere(){
        $args = func_get_args();
        $t = count($args);
        $prop = null;
        $value = PDODBNULL;
        $operator = '=';
        if($t == 0) return $this;
        $prop = $args[0];
        if($t==2){
            $value = $args[1];
        }
        if($t>=3){
            $value = $args[2];
            $operator = $args[1];
        }
        return $this->where($prop,$operator,$value,'OR');
    }
    
    /**
     * ham them dieu kien cho menh de having
     * @param String
     * @param String
     * @param String
     * @param String
     */ 
    
    public function having(){
        $args = func_get_args();
        $t = count($args);
        $prop = null;
        $value = PDODBNULL;
        $operator = '=';
        $cond = 'AND';
        if($t == 0) return $this;
        $prop = $args[0];
        if($t==2){
            $value = $args[1];
        }
        if($t>=3){
            $value = $args[2];
            $operator = $args[1];
            if($t>3) $cond = $args[3];
        }
        if($value==PDODBNULL){
            if(is_array($prop)){
                foreach($prop as $key => $val){
                    if($w = $this->addConditionParam($key,$val)){
                        $this->_having[] = $w;
                    }
                }
            }
            elseif($h = $this->buildSingleWhereParamString($prop)){
                $this->_having[] = $h;
            }
        }else{
            if($w = $this->parseWherePar($prop,$value,$operator)){
                $this->_having[] = $w;
            }
        }
        return $this;
    }
    /**
     * ham them dieu kien cho menh de having
     * @param String $prop thuoc tinh hay bieu thuc
     * @param String
     * @param String
     * @param String
     */ 
    
    public function orHaving(){
        $args = func_get_args();
        $t = count($args);
        $prop = null;
        $value = PDODBNULL;
        $operator = '=';
        if($t == 0) return $this;
        $prop = $args[0];
        if($t==2){
            $value = $args[1];
        }
        if($t>=3){
            $value = $args[2];
            $operator = $args[1];
            if($t>3) $cond = $args[3];
        }
        return $this->having($prop,$operator,$value,'OR');
    }
    
    public function groupBy($groupByField)
    {
        $groupByField = preg_replace("/[^-a-z0-9\.\(\),_\*]+/i", '', $groupByField);

        $this->_groupby[] = $groupByField;
        return $this;
    }
    
    public function orderBy($orderByField, $orderbyDirection = "DESC", $customFieldsOrRegExp = null)
    {
        $this->_orderby[] = array($orderByField, $orderbyDirection, $customFieldsOrRegExp);
    }
    
    
    public function limit($limit=null, $to = null){
        $lm = null;
        if(is_null($limit)) $lm = null;
        if(is_array($limit)){
            if(isset($limit['limit'])){
                $lm = $limit['limit'];
                if(is_array($lm)){
                    if(isset($lm[0])&&isset($lm[1]))
                        $lm = array($lm[0],$lm[1]);
                    elseif(isset($lm[0]))
                        $lm = $lm[0];
                }
            }
            elseif(isset($limit[0])&&isset($limit[1]))
                $lm = array($limit[0], $limit[1]);
            elseif(isset($limit[0]))
                $lm = $limit[0];
        }
        elseif(is_numeric($limit)){
            $lm = $limit;
            if(is_numeric($to)){
                $lm = array($limit,$to);
            }
        }
        elseif(count(explode(',',$limit))==2){
            $e = explode(',',str_replace(' ','',$limit));
            $lm = $e;
        }
        if(!$lm) $lm = null;
        $this->_limit = $lm;
        return $this;
    }
    


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
    
    



    /**
     * ham set dieu kien cho viect truy van
     * @param mixed
     * @return Object 
     */ 
    
    public function setCondition($args = null){
        $this->buildCondition($args);
        return $this;
    }
    


    /**
     * ham hien thi thong bao loi. co the tuy chinh dung code, bo qua, hay van hien thong bao va tiep tuc chay code
     * @param String $message thong bao loi
     * 
     * 
     * 
     * @return void
     */ 
    
    protected function reportError($message=null){
        $m = is_null($message) ? $this->error_message : $message;
        switch(self::$reportLevel){
            case 0:
                //nothing
            break;
            
            case 1:
                echo '<br />'.$m."<br />";
            break;
            
            case 2:
                throw new Exception($m);
            break;
            
            default:
                $this->conn = null;
                die('<br />'.$m."<br />");
        }
    }
    
    /**
     * thiet lap thong bao loi
     * @param int $level
     * 
     * 
     * @return bool
     */ 

    public static function setErrorReportingLevel($level = 0){
        if(is_int($level) && $level >= 0 && $level <= 3){
            self::$reportLevel = $level;
            return true;
        }
        return false;
    }
    
    /**
     * dua cac tham so ve mac dinh
     */ 
    
    public function reset(){
        $this->_conditionQuery = null;
        $this->_where = array();
        $this->_having = array();
        $this->_join = array();
        $this->_groupby = array();
        $this->_orderby = array();
        $this->_limit = null;
        $this->_params = array();
        $this->_paramKeys = array();
        $this->_stmt = null;
    }
    
    
    
    public function getFetchType($ft=null){
        $supportType = array('obj' => PDO::FETCH_OBJ, 'object' => PDO::FETCH_OBJ, 'array' => PDO::FETCH_ASSOC, 'class' => PDO::FETCH_CLASS, 'assoc' => PDO::FETCH_ASSOC);
        $f = strtolower($ft);
        if($f && isset($supportType[$f]))
            return $supportType[$f];
        return $supportType['assoc'];
    }
    
    


    /**
     * ham validate data field cua bang
     * kiem tra xem cac du lieu co khop voi cac cot cua bang hay ko
     * 
     * @param Array $data        mang du lieu
     */
    
    protected function parseData($data){
        $d = array();
        if(is_string($data)){
            try{
                parse_str($data,$arr);
                $d = $arr;
            }catch(Exception $e){
                //del lam gi ca
            }
        }elseif(is_array($data) || is_object($data)){
            $d = $data;
        }
        if(!$d) return null;
        $r = array();
        foreach($d as $f => $v){
            if(in_array($f, $this->fields)) $r[$f] = $v;
        }
        unset($r[$this->idField]);
        return $r;
    }



    public function createQueryBuilder($query,$param)
    {
        if(is_array($param)){
            foreach($param as $name => $value){
                $query = str_replace($name,$this->quote($value),$query);
            }
        }
        $this->queryBuilder = $query;
        return $query;
    }
    /**
     * TAO QUERY VA SET THAM SO DE UPDATE DU LIEU
     * @param Array  $data         du lie
     * @param String $type         Kieu update du lieu (INSERT / UPDATE)
     */ 
     protected function buildUpdateDataQuery($data,$type="INSERT"){
        $t = strtoupper($type);
        if(!in_array($t, explode(' ', 'INSERT UPDATE'))) $t = "INSERT";
        $fields = "";
        $values = "";
        $updQuery = "";
        if($t == 'INSERT'){
            $fields.="(";
            $values = " VALUES(";
            foreach($data as $f => $v){
                
                $name = $this->addParam($f,$v);
                $fields .= $f. ", ";
                $values .= $name. ", ";
                
            }
            $fields = rtrim($fields,', ').')';
            $values = rtrim($values,', ').')';

            $updQuery = "INSERT INTO {$this->_prefix}{$this->accessName}{$fields}{$values}";
        }else{
            $fields.=" SET ";
            foreach($data as $f => $v){
                $name = $this->addParam($f,$v);
                $fields .= $f . ' = '.$name.', ';
            }
            $fields = rtrim($fields,', ');
            $updQuery = "UPDATE {$this->_prefix}{$this->accessName}{$fields}";
        }
        return $updQuery;
    }
    
    




    /**
     * CAC PHUONG THUC BEN DUOI DUNG DE CHAY HE THOMG.
     * KHONG CAN XEM DOAM NAY!
     * ###########################################################################################################
     */







    
    
    /**
     * Tao cac menh de dieu kien de thuc thi select, updatem delete
     * @param Mixed $condition
     * 
     * 
     * @return String $_conditionQuery
     */ 
    
    protected function buildCondition($args = null){
        try{
            if(is_string($args)){
                $this->buildConditionByString($args);
            }elseif(is_array($args)){
                $this->buildConditionByArray($args);
            }
            $query = $this->buildConditionQuery();
        }catch(Exception $e){
            $this->error_message = $e->getMessage();
            $this->reportError();
        }
        return $query;
    }


    public function getQuery($args = null)
    {
        try{
            if(is_string($args)){
                $this->buildConditionByString($args);
            }elseif(is_array($args)){
                $this->buildConditionByArray($args);
            }
            $query = $this->buildConditionQuery();
        }catch(Exception $e){
            $this->error_message = $e->getMessage();
            $this->reportError();
        }
        return $query;
    }
    /**
     * Tao cac menh de dieu kien de thuc thi select, updatem delete
     * @param Array $array       
     * 
     * @note cach su dung tham so:
     * uu tin where. nghia la cac KEY binh thuong se duoc hieu la where.
     * vi du trong array('KEY' => 'VAL') se duoc hieu la mot phan cua menh de where: KEY = 'VAL'
     * @return String $conditionQuery
     */ 
    
    protected function buildConditionByArray($args){
        if(is_array($args)){
            foreach($args as $key => $val){
                if(is_numeric($key)){
                    $this->buildConditionByString($val);
                }elseif(substr($key,0,1)=='@'){
                    $this->builClauseParams(substr($key,1),$val);
                }else{
                    if($w = $this->addConditionParam($key,$val)){
                        $this->_where[] = $w;
                    }
                }
            }
        }
        //todo
    }
    
    
    /**
     * build where, join, having, orderby, groupby, limit
     * @param string
     * 
     * @return void
     */
    
    protected function buildConditionByString($args){
        $as = explode('&',$args);
        foreach($as as $part){
            
            if(preg_match('/(^\{|^\[)/si', trim($part), $m)){
                $p = trim($this->callTBMethod($part));
                $cond = "AND";
                if(trim(strtolower(substr($p,0,3)))=='or' || trim(strtolower(substr($p,0,4)))=='and'){
                    if(trim(strtolower(substr($p,0,3)))=='or'){
                        $cond = "OR";
                        $p = substr($p,4);
                    }else{
                        $p = substr($p,5);
                    }
                
                }else{
                    $p = ltrim($p);
                }
                $this->_where[] = array($p,$cond);
            }elseif(preg_match('/^@/si', trim($part), $m)){
                $part = trim($part);
                $p = '/^@([A-z0-9_]*)?\((.*)?\)$/si';
                if(preg_match_all($p,$part,$m)){
                    $func = $m[1][0];
                    $par = $m[2][0];
                    $this->builClauseParams($func,$par);
                }else{
                    $p = '/^@([A-z0-9_]*)?=(.*)?$/si';
                    if(preg_match_all($p,$part,$m)){
                        $func = $m[1][0];
                        $par = $m[2][0];
                        $this->builClauseParams($func,$par);
                    }
                }
            }elseif($w = $this->buildSingleWhereParamString($part)){
                $this->_where[] = $w;
            }
        }
        //todo
    }
    /**
     * them dieu kien va tjam so cho cac menh de dieu kien where , having
     * @param mixed $prop Tham so string ten cot, hoac bieu thuc hoac mang cac ten cot hoac bieu thuc kem gia tri
     * @param mixed $value Gia tri So hoac chuoi
     * @param string $operator Toan tu so sanh
     * @param string $cond Toan tu noi
     *
     * @return array Mang where
     */
    protected function addConditionParam($prop,$val=null,$operator='=',$cond='AND'){
        $o = $operator;
        $f = $prop;
        $p = '/(^|and\s|or\s)(.*)?\s{0,}(\!=|<=|>=|<>|not\slike|\snotlike|\!)$/si';
        $p2 = '/(^|and\s|or\s)(.*)?\s{0,}(<|>|=|\slike|\snot|\!)$/si';
        $m = null;
        if(preg_match_all($p,trim($prop),$m1)){
            $m = $m1;
        }elseif(preg_match_all($p2,trim($prop),$m2)){
            $m = $m2;
        }
        if($m){
            $op = strtoupper(trim($m[1][0]));
            if($op == 'AND' || $op == 'OR') $cond = $op;
            $f = $m[2][0];
            if($m[3][0]) $o = $m[3][0];
        }
        if($w = $this->parseWherePar($f,$val,$o)){
            return array($w,$cond);
        }
        return false;
        
    }
    
    
    protected function buildConditionParamByString($args=null){
        $return = array();
        $as = explode(',',$args);
        foreach($as as $part){
            $s = substr($part, 0, 1);
            $e = substr($part, strlen($part) - 1, 1);
            if(($s=='[' && $e==']') || ($s=='{' && $e=='}')){
                $p = trim($this->callTBMethod($part));
                $cond = "AND";
                if(trim(strtolower(substr($p,0,3)))=='or' || trim(strtolower(substr($p,0,4)))=='and'){
                    if(trim(strtolower(substr($p,0,3)))=='or'){
                        $cond = "OR";
                        $p = substr($p,3);
                    }else{
                        $p = substr($p,4);
                    }
                
                }else{
                    $p = ltrim($p);
                }
                $return[] = array($p,$cond);
            }elseif(strpos($part,'@')==0){
                $part = trim($part);
                $p = '/^@([A-z0-9_]*)?\((.*)?\)$/si';
                preg_match_all($p,$part,$m);
                if($m){
                    $func = $m[1][0];
                    $par = $m[2][0];
                    $this->builClauseParams($func,$par);
                }elseif(preg_match_all('/^@([A-z0-9_]*)?=(.*)?$/si',$part,$mm)){
                    $func = $mm[1][0];
                    $par = $mm[2][0];
                    $this->builClauseParams($func,$par);
                }
            }else{
                if($q = $this->buildSingleWhereParamString($part)){
                    $return[] = $q;
                }
            }
        }
        return $return;
    }
    
    
    protected function setWhereParamByString($where){
        if($where = $this->buildConditionParamByString($where)){
            foreach($where as $w){
                $this->_where[] = $w;
            }
        }
    }
    protected function setHavingParamByString($having){
        if($cond = $this->buildConditionParamByString($having)){
            foreach($cond as $w){
                $this->_having[] = $w;
            }
        }
    }
    protected function buildSingleWhereParamString($where){
        $w = $where;
        $cond = "AND";
        if(trim(strtolower(substr($w,0,3)))=='or' || trim(strtolower(substr($w,0,4)))=='and'){
            if(trim(strtolower(substr($w,0,3)))=='or'){
                $cond = "OR";
                $w = substr($w,3);
            }else{
                $w = substr($w,4);
            }
        
        }else{
            $w = ltrim($w);
        }
        if($c = $this->buildSingleWhereParam($w)){
            return array($c,$cond);
        }
        return false;

    }
    protected function builClauseParams($clause,$param=null){
        if(in_array(strtoupper($clause), self::$_keywords)){
            $f = strtolower($clause);
            switch($f){
                case 'join':
                    $this->__bindJoin($param);
                break;
                case 'where':
                    $this->setWhereParamByString($param);
                break;
                case 'groupby':
                    $this->__bindGroupBy($param);
                break;
                case 'having':
                    $this->setHavingParamByString($param);
                break;
                case 'orderby':
                    $this->__buildOrderBy($param);
                break;
                case 'limit':
                    $this->limit($param);
                default:
                    //TODO 
                break;
            }
        }
    }
    
    protected function buildConditionQuery(){
        $query = "";
        
        if($this->_join){
            foreach($this->_join as $join){
                $query .= ' '.$join[2].' JOIN '.$join[0]. ' ON ' .$join[1];
            }
        }
        if($this->_where){
            $query .= " WHERE";
            foreach($this->_where as $i => $where){
                $query .= " ".(($i>0)?$where[1] . ' ' : '').$where[0];
            }
        }
        
        if($this->_groupby){
            $query .= " GROUP BY ".implode(', ',$this->_groupby);
        }
        if($this->_having){
            $query .= " HAVING";
            foreach($this->_having as $i => $having){
                $query .= " ".(($i>0)?$having[1] . ' ' : '').$having[0];
            }
        }
        if($this->_orderby){
            $query .= " ORDER BY";
            foreach($this->_orderby as $i => $orderby){
                $query .= " ".(($i>0)? ', ' : '').$orderby[0]. ' '. $orderby[1];
            }
        }
        
        if($lm = $this->_limit){
            $l = null;
            if(is_numeric($lm) || (is_string($lm) && preg_match('/^[0-9]{1,}(\s{0,}\,{0,1}[0-9]{1,}|$)/si',trim($lm)))) $l = $lm;
            elseif(is_array($lm) && isset($lm[0]) && isset($lm[1])) $l = $lm[0].', '.$lm[1];
            
            if(!is_null($l)) $query .= " LIMIT $l";
        }
        
        $this->_conditionQuery = $query;
        
        
        return $query;
    }
    
    
    public function getLastQuery(){
        return $this->_query;
    }
    
    
    
    protected function callTBMethod($args){
        $af = array('[', '{', '}', ']');
        $ar = array('self::___bindOr(<!%', 'self::___bindAnd(<!%', '%!>)', '%!>)');
        $c = str_replace($af,$ar,$args);
        $c = str_replace(array('<!%','%!>'),array('[',']'),$c);
        $p = '/([^\[\,]*)?(\!=|<=|>=|<>|\snotlike|\snot\slike)([^\]\,]*)?/si';
        $p = '/([^\[\,]*)?(<|>|=|\slike|\snot)([^\]\,]*)?/si';
        
        if(preg_match_all($p,$c,$m)){
            foreach($m[0] as $f){
                $c = str_replace($f, "\"".$this->buildSingleWhereParam($f)."\"", $c);
            }
        }
        elseif(preg_match_all($p2,$c,$m)){
            foreach($m[0] as $f){
                $c = str_replace($f, "\"".$this->buildSingleWhereParam($f)."\"", $c);
            }
        }
        eval("\$s = $c;");
        
        return $s;
    }
    
    protected function buildSingleWhereParam($str){
        $p = '/(.*)?\s{0,}(\!=|<=|>=|<>|\snot\slike|\snotlike|\!)\s{0,}(.*)?/si';
        $p2 = '/(.*)?\s{0,}(<|>|=|\slike|\snot|\!)\s{0,}(.*)?/si';
        $o = "=";
        $f = "";
        $val = null;
        if(preg_match_all($p,trim($str),$m)){
            $f = $m[1][0];
            $o = $m[2][0];
            $val = trim($m[3][0]);
        }
        elseif(preg_match_all($p2,trim($str),$m)){
            $f = $m[1][0];
            $o = $m[2][0];
            $val = trim($m[3][0]);
        }
        $s = $this->parseWherePar($f,$val,$o);
        return $s;
    }
    
    
    
    /**
     * them du lieu vao params
     * @param String $name
     * @param Mixed $val
     * 
     * @return String $named
     */ 
    
    protected function addParam($name, $val){
        $name = preg_replace('/[\+\-\*\/\.]/si','_', str_replace(' ', '', $name));
        $n = $name;
        if(isset($this->_paramKeysp[$name])){
            $n = $name.'_'.($this->_paramKeysp[$name]+1).'_s';
            $this->_paramKeysp[$name]++;
        }else{
            $this->_paramKeysp[$name]=1;
            $n.='_s';
        }
        $n = ":".$n;
        $this->_params[$n] = $this->fixVarType($val);
        return $n;
    }
    
    protected function parseWherePar($field = null, $val = null, $operator = '='){
        $o = strtoupper(trim($operator));
        if(!in_array($o,self::$_oprators)) $o = '=';
        $d = "";
        $s = "";
        $f = $field;
        $vs = array();
        if(!is_null($val)){
            if(is_array($val)){
                $vs = $val;
            }else{
                $p = '/^\[(.+)\]$/si';
                if(preg_match($p,$val,$m)){
                    $vs = explode(',',$m[1]);
                }else{
                    $vs[] = $val;
                }
                
            }
        }
        
        if(count($vs)==0) return false;
        if(count($vs)<2){
            if(substr($vs[0],0,1)=='@'){
                $vas = explode(':',substr($vs[0],1));
                if(count($vas)>1 && strtolower($vas[0])=="field"){
                    $d = $vas[1];
                    if($o == 'LIKE' || $o == 'NOTLIKE'){
                        $s = $f . ' '.(($o == 'NOTLIKE')?"NOT LIKE":"LIKE")
                            ."CONCAT('%', ".$this->quote($d).", '%')";
                    }else{
                        $s = $f. ' '.$o . ' ' . $d;
                    }
                }else{
                    $s = $f. ' '.$o . ' '.substr($vs[0],1);
                }
            }else{
                $d = $vs[0];
                
                if($o == 'LIKE' || $o == 'NOTLIKE'){
                    $named = $this->addParam($f,'%'.$d.'%');
                    $s = $f. ' '. (($o == 'NOTLIKE')?"NOT LIKE":"LIKE") . ' '
                        .$named;
                }else{
                    $named = $this->addParam($f,$d);
                    $s = $f. ' '. $o . " ".$named;
                }
            }
        }else{
            $s = "(";
            $n = 0;
            foreach($vs as $v){
                $op = ($n>0)?" OR ":"";
                $vl = trim($v);
                if(substr($vl,0,1)=='@'){
                    $vas = explode(':',substr($vl,1));
                    if(count($vas) > 1 && strtolower($vas[0])=="field"){
                        $d = $vas[1];
                        if($o == 'LIKE' || $o == 'NOTLIKE'){
                            $s .= $op . $f . ' '.(($o == 'NOTLIKE')?"NOT LIKE":"LIKE"). ' '
                                ."CONCAT('%', ".$this->quote($d).", '%')";
                        }else{
                            $s .= $op . $f. ' '.$o . ' ' . $this->quote($d);
                        }
                    }else{
                        $s .= $op . $f. ' '.$o . ' '.$this->quote(substr($val,1));
                    }
                }elseif($val!==''){
                    $d = $vl;
                    
                    if($o == 'LIKE' || $o == 'NOTLIKE'){
                        $named = $this->addParam($f,'%'.$d.'%');
                        $s .= $op . $f. ' '. (($o == 'NOTLIKE')?"NOT LIKE":"LIKE")." "
                            .$named;
                    }else{
                        $named = $this->addParam($f,$d);
                        $s .= $op . $f. ' '. $o . " ".$named;
                    }
                }
                $n++;
            }
            $s.=")";
        }
        
        return $s;
    }
    
    protected function fixVarType($str){
        $s = $str;
        if(is_numeric($str)){
            eval("\$s = $str;");
        }
        return $s;
    }

    
    /**
     * kiem tra va lay ve cac tham so trong phep so sanh don
     * 
     * @uses $this->getParamOperator('a>=2') hoac $this->getParamOperator('a like 2') van van
     * 
     * @param String $str phep truy van don cua where hoac having
     * 
     * @return Array mang co 3 index: 0 - field, 1 - value, 2 - operato
     */ 
    
    protected function getParamOprator($str){
        $p = '/^(.*)?\s{0,}(\!=|<=|>=|<|>|=|\slike\s|\snotlike\s|\snot\s)\s{0,}(.*)?$/si';
        preg_match_all($p,trim($str),$m);
        $arr = null; 
        if($m){
            $f = trim($m[1][0]);
            $o = trim($m[2][0]);
            $v = trim($m[3][0]);
            $arr = array($f,$v,$o);
        }
        return $arr;
    }
    
    public function quote($str=''){
        if(is_string($str) || is_numeric($str)) return $this->conn->quote($str);
        return "''";
    }
    
    protected function __bindJoin($args){
        if(is_string($args) && count(explode(':', $args))>=2){
            $this->__bindJoinStr($args);
        }elseif(is_array($args)){
            $via = false;
            $kis = false;
            foreach($args as $key => $val){
                if(!is_numeric($key)) $kis = true;
                if(is_array($val)) $via = true;
                break;
            }
            if(!$kis){
                if($via){
                    foreach($args as $key => $val){
                        if(is_string($val)) $this->__bindJoinStr($val);
                        elseif(is_array($val)) $this->__bindJoinArr($val);
                    }
                }elseif(isset($args[0])&& count(explode(':', $args))>=2){
                    foreach($args as $val){
                         $this->__bindJoinStr($val);
                    }
                }elseif(isset($args[0]) && isset($args[0])){
                    $this->join($args[0], $args[1], isset($args[2])?$args[2]:"");
                }
            }else{
                $this->__bindJoinArr($args);
            }
        }
    }
    protected function __bindJoinStr($args){
        $b = false;
        if(is_string($args) && count(explode(':', $args))>=2){
            $b = true;
            $a = explode(',',$args);
            foreach($a as $join){
                $ja = explode(':',trim($join));
                $type = '';
                if(count($ja)>1){
                    if(isset($ja[2])){
                        $type = trim($ja[2]);
                    }
                    $this->join(trim($ja[0]), trim($ja[1]), $type);
                }
            }
        }
        return $b;
    }
    protected function __bindJoinArr($args){
        if(is_array($args)){
            $kin = false;
            $via = false;
            foreach($args as $key => $val){
                if(is_numeric($key)) $kin = true;
                if(is_array($val)) $via = true;
                break;
            }
            if(!$kin){
                if(!$via && isset($args['table']) && isset($args['condition'])){
                    $this->join($args['table'], $args['condition'], isset($args['type'])?$args['type']:"");
                }
            }elseif($via){
                foreach($args as $row){
                    $this->__bindJoinArr($row);
                }
            }else{
                if(count(explode(':',$args[0]))>2){
                    foreach($args as $row){
                        $this->__bindJoinStr($row);
                    }
                }
                elseif(isset($args[0])&&isset($args[1])){
                    $this->join($args[0], $args[1], isset($args[2])?$args[2]:"");
                }
            }
        }
    }
    
    
    
    
    protected function __bindGroupBy($args){
        if(is_string($args)){
            $this->groupBy($args);
        }elseif(is_array($args)){
            foreach($args as $v){
                $this->groupBy($v);
            }
        }
    }
    
    protected function __buildOrderBy($field){
        if(is_string($field)){
            $fs = explode(',',$field);
            foreach($fs as $fd){
                $d = "ASC";
                $ord = explode('--', preg_replace('/\s+/','--',trim($fd)));
                $f = $ord[0];
                if(isset($ord[1])){
                    $d = $ord[1];
                }
                $d = strtoupper($d);
                if($d!='DESC') $d = 'ASC';
                $this->orderBy($f,$d);
            }
        }
        elseif(is_array($field)){
            foreach($field as $c => $v){
                if(is_numeric($c) && is_array($v)){
                    foreach($v as $b => $n){
                        if(is_string($b)){
                            $n = strtoupper($n);
                            if($n!='DESC') $n = 'ASC';
                            $this->orderBy($b,$n);
                        }
                    }
                }else{
                    $v = strtoupper($v);
                    if($v!='DESC') $v = 'ASC';
                    $this->orderBy($c,$v);
                }
            }
        }
    }

}

