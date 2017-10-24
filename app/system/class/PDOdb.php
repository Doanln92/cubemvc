<?php

/**
 * @author Doanln
 * @copyright 2017
 */



/**
 * pdo null
 */ 


define('PDODBNULL','<!--?% Doan Dep Trai %?--!>');

class PDOdb{
    protected $pdo;
    protected $_host;
    protected $_name;
    protected $_user;
    protected $_pass;
    protected $_DBMS = 'MYSQL';
    protected $_dbmsList = array('MYSQL', 'MSSQL','OROCLE', 'SQLITE');
    // se duoc nang cap trong cac phien ban sau
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
    
    
    /**
     * cac bien cho thuc thi PDO
     */ 
     
    protected $_fetchType = PDO::FETCH_ASSOC;
    
    
    protected $_stmt;
    
    
    
    protected static $_keywords = array('FROM', 'JOIN', 'WHERE', 'GROUPBY', 'HAVING', 'ORDERBY', 'GROUPBY', 'LIMIT');
    protected static $_oprators = array('=', '>', '>=', '<', '<=', '<>', '!=', 'LIKE', 'NOTLIKE', 'NOT');
    protected static $_valFuncs = array('FIELD', 'INC', 'DEC', 'RAND', 'RANDTEXT');
    protected static $_keyFuncs = array('OR', 'AND' ,'NOT', 'LIKE', 'NOTLIKE');
    
    
    
    protected $_predix = '';
    
    
    
    public $isConnect = false;
    
    protected $error_message = null;
    
    protected static $reportLevel = 0;
    
    
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
    
    public function __construct($host = 'localhost', $dbname = 'test', $user = 'root', $pass = '',$prefix = '', $dbms='MYSQL') {
        if(is_array($host)){
            $h = isset($host['host'])?$host['host']:'localhost';
            foreach($host as $key => $value){
                $$key = $value;
            }
            $host = $h;
        }
        $this->_host = $host;
        $this->_name = $dbname;
        $this->_user = $user;
        $this->_pass = $pass;
        $this->_prefix = $prefix;
        $this->_DBMS = $dbms;
        $this->connect();
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
    
    public function connect($host = null, $dbname = null, $user = null, $pass = null, $prefix=null, $dbms=null) {
        
        $h = is_null($host)    ? $this->_host : $host;
        $d = is_null($dbname)  ? $this->_name : $dbname;
        $u = is_null($user)    ? $this->_user : $user;
        $p = is_null($pass)    ? $this->_pass : $pass;
        $f = is_null($prefix)  ? $this->_prefix: $prefix;
        $m = is_null($dbms)    ? $this->_DBMS : $dbms;
        
        
        $this->isConnect = false;
        try{
            $this->pdo = new PDO('mysql:host='.$h.';dbname='.$d.";charset=utf8",$u,$p);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->isConnect = true;
            if($f){
                $this->setPrefix($f);
            }
        }
        catch(exception $e){
            $this->error_message = "\nCannot connect to Database \n <!-- ".$e->getMessage().' -->';
            $this->reportError();
        }
        return $this->isConnect;
    }
    
    /**
     * dong ket noi
     */ 
    
    public function disconnect(){
        $this->pdo = null;
    }
    
    public function __destruct(){
        $this->pdo = null;
    }
    
    /**
     * t?o b?n sao pdo
     */ 
    
    public function copy(){
        return clone $this;
    }
    
    /**
     * thiet lap prefix cho cac bang
     * @param String
     * 
     * @return Object PDODB
     */ 
    
    public function setPrefix($prefix=''){
        if(preg_match('/^[A-z_]+\w*/',$prefix)){
            $this->_predix = $prefix;
        }
        return $this;
    }
    
    /**
     * lay thong tin prefix
     * @return String
     */ 
    
    public function getPrefix(){
        return $this->_predix;
    }
    
    
    /**
     * thiet lap kieu fetch cho cac bang
     * @param String
     * 
     * @return Object PDODB
     */ 
    
    
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
                $this->pdo = null;
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
     * lay cac ban ghi trong csdl
     * @param String $tableName      ten bang
     * @param String $select         Danh sach cac cot can select
     * @param Mixed $condition       dieu kien de select (co the su dung string hoac array) bao gom ca where, group by, having, order by, limit
     * 
     * 
     * 
     * @return Array | Obj
     */ 
    
    
    public function get($tableName,$select='*',$condition=null,$fetch_type=null){
        $rs = null;
        if(is_array($select)) $select = implode(', ', $select);
        
        try{
            $fetch = $this->_fetchType;
            $ft = null;
            if($fetch_type){
                if(class_exists($fetch_type)){
                    $fetch = PDO::FETCH_CLASS;
                    $ft = $fetch_type;
                }
                else{
                    $fetch = $this->getFetchType($fetch_type);
                }
                
            }else{
                $fetch = PDO::FETCH_ASSOC;
            }
            
            $query = "SELECT $select FROM {$this->_predix}{$tableName} ".$this->buildCondition($condition);
            $this->_query = $query;
            $stmt = $this->pdo->prepare($query);
            if($stmt->execute($this->_params)){
                $this->_stmt = $stmt;
                if($ft){
                    $rs = $stmt->fetchAll($fetch,$ft);
                }else{
                    $rs = $stmt->fetchAll($fetch);
                }
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
     * @param String $tableName      ten bang
     * @param String $select         Danh sach cac cot can select
     * @param Mixed $condition       dieu kien de select (co the su dung string hoac array) bao gom ca where, group by, having, order by, limit
     * 
     * 
     * 
     * @return Array | Obj
     */ 
    
    public function getOne($tableName,$select='*',$condition=null,$fetch_type=null){
        $rs = null;
        if(is_array($select)) $select = implode(', ', $select);
        try{
            $fetch = $this->_fetchType;
            $ft = null;
            if($fetch_type){
                if(class_exists($fetch_type)){
                    $fetch = PDO::FETCH_CLASS;
                    $ft = $fetch_type;
                }
                else{
                    $fetch = $this->getFetchType($fetch_type);
                }
                
            }else{
                $fetch = PDO::FETCH_ASSOC;
            }
            $this->limit(1);
            $query = "SELECT $select FROM {$this->_predix}{$tableName} ".$this->buildCondition($condition);
            $this->_query = $query;
            $stmt = $this->pdo->prepare($query);
            if($stmt->execute($this->_params)){
                $this->_stmt = $stmt;
                if($ft){
                    if($rs1 = $stmt->fetchAll($fetch,$ft)){
                        if(isset($rs1[0])) $rs = $rs1[0];
                    }
                }else{
                    $rs = $stmt->fetch($fetch);
                }
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
     * @param String $tableName      ten bang
     * @param String $column         ten cot
     * @param Mixed $condition       dieu kien de select (co the su dung string hoac array) bao gom ca where, group by, having, order by, limit
     * 
     * 
     * @return Array | Obj
     */ 
    
    public function getVal($tableName,$column=null,$condition=null){
        $rs = null;
        if($rss = $this->getOne($tableName,$column,$condition,'assoc')){
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
     * lay cac ban ghi trong csdl
     * @param String $tableName      ten bang
     * @param Mixed $condition       dieu kien de select (co the su dung string hoac array) bao gom ca where, group by, having, order by, limit
     * 
     * 
     * @return int
     */ 
    
    public function count($tableName, $condition = null){
        $count = 0;
        $rs = $this->getVal($tableName,'COUNT(1) AS total',$condition);
        if(is_numeric($rs)) $count = $rs;
        return $count;
    }
    
    /**
     * lay cac ban ghi trong csdl
     * @param String $tableName      ten bang
     * @param Mixed $condition       dieu kien de xoa (co the su dung string hoac array) bao gom ca where, group by, having, order by, limit
     * 
     * @return int
     */ 
    
    public function delete($tableName, $condition = null){
        $rs = 0;
        try{
            $query = "Delete FROM {$this->_predix}{$tableName} ".$this->buildCondition($condition); 
            $this->_query = $query;
            $stmt = $this->pdo->prepare($query);
            if($stmt->execute($this->_params)){
                $this->_stmt = $stmt;
                $rs = $stmt->rowCount();
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
     * them du lieu vao bang
     * @param String $tableName    ten bang
     * @param Array $data          Du lieu can chen
     * 
     * @return int
     */
     
    public function insert($tableName,$data){
        $rs = 0;
        try{
            $query = $this->buildUpdateDataQuery($tableName,$data); 
            $this->_query = $query;
            $stmt = $this->pdo->prepare($query);
            if($stmt->execute($this->_params)){
                $this->_stmt = $stmt;
                $rs = $stmt->rowCount();
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
     * update du lieu vao bang
     * @param String $tableName    ten bang
     * @param Array $condition     dieu kien update
     * 
     * @return int
     */
     
    public function update($tableName,$data, $condition = array()){
        $rs = 0;
        try{
            $query = $this->buildUpdateDataQuery($tableName,$data, "UPDATE"). "  ".$this->buildCondition($condition); 
            $this->_query = $query;
            $stmt = $this->pdo->prepare($query);
            if($stmt->execute($this->_params)){
                $this->_stmt = $stmt;
                $rs = $stmt->rowCount();
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
     * kiwm tra bang co ton tai hay ko
     * @param String $tableName ten bang
     * 
     * @return Bool
     */ 
    
    public function exists($tableName){
        try{
            $stmt = $this->pdo->query("SELECT 1 FROM {$this->_predix}{$tableName} limit 1");
            return true;
        }catch(Exception $e){
            return false;
        }
    }
    

     /**
     * Tao bang moi
     * @param String $tableName ten bang
     * 
     * @return Bool
     */ 
    
    public function createTable($tableName=null,$func=null)
    {
        $stt = false;
        if(is_string($tableName) && is_object($func)){
            if(!$this->exists($tableName)){
                $table = new DBCreateTable($this->_predix.$tableName);
                $a = call_user_func_array($func, array($table));
                try{
                    if($this->execute($table->toQuery())){
                        $stt = true;
                    }    
                }catch(Exception $e){
                    $msg = $e->getMessage();
                    $this->error_message = $msg;
                    $this->reportError();
                }
            }
        }
        return $stt;
    }

    public function dropTable($tableName)
    {
        $stt = false;
        if(is_string($tableName) && $this->exists($tableName)){
            if($this->execute("DROP TABLE {$this->_predix}{$tableName}")){
                $stt = true;
            }
        }
        return $stt;
    }

    public function alterTable($tableName=null,$func=null)
    {
        $stt = false;
        try{
            if(is_string($tableName) && is_object($func)){
                if($this->exists($tableName)){
                    $table = new DBAlterTable($this->_predix.$tableName);
                    $a = call_user_func_array($func, array($table));
                    if($this->execute($table->toQuery())){
                        $stt = true;
                    }
                }
            }
        }catch(exception $e){
            $msg = $e->getMessage();
            $this->error_message = $msg;
            $this->reportError();
        }
        return $stt;
    }

    /**
     * thuc thi query
     * @param string
     * 
     * @return stmt
     */ 
    
    public function query($query){
        $rs = 0;
        try{
            $this->_stmt = $this->pdo->query($query);
            return $this->_stmt;
        }catch(PDOException $e){
            $msg = $e->getMessage();
            $this->error_message = $msg;
            $this->reportError();
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
            $this->_stmt = $this->pdo->exec($query);
            return $this->_stmt;
        }catch(PDOException $e){
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
            $stmt = $this->pdo->prepare($query);
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
    
    
    public function getPDO(){
        return $this->pdo;
    }
    
    public function getStmt(){
        return $this->_stmt;
    }
    
    
    /**
     * ham them dieu kien cho menh de where
     * @param String
     * @param String
     * @param String
     * @param String
     */ 
    
    public function where($prop, $value = PDODBNULL, $operator = '=', $cond = 'AND'){
        if($value==PDODBNULL){
            if(is_array($prop)){
                $this->buildConditionByArray($prop);
            }
            else{
                $this->_where[] = $this->buildSingleWhereParamString($prop);
            }
        }else{
            $this->_where[] = $this->addConditionParam($prop,$value,$operator,$cond);
        }
        return $this;
    }
    /**
     * ham them dieu kien cho menh de where
     * @param String
     * @param String
     * @param String
     * @param String
     */ 
    
    public function orWhere($prop, $value = PDODBNULL, $operator = '='){
        return $this->where($prop,$value,$operator,'OR');
    }
    
    /**
     * ham them dieu kien cho menh de having
     * @param String
     * @param String
     * @param String
     * @param String
     */ 
    
    public function having($prop, $value = PDODBNULL, $operator = '=', $cond = 'AND'){
        if($value==PDODBNULL){
            if(is_array($prop)){
                foreach($prop as $key => $val){
                    $this->_having[] = $this->addConditionParam($key,$val);
                }
            }
            else{
                $this->_having[] = $this->buildSingleWhereParamString($prop);
            }
        }else{
            $this->_having = $this->addConditionParam($prop,$value,$operator,$cond);
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
    
    public function orHaving($prop, $value = PDODBNULL, $operator = '='){
        return $this->having($prop,$value,$operator,'OR');
    }
    
    /**
     * This method allows you to concatenate joins for the final SQL statement.
     *
     * @uses Table->join('table1', 'field1 <> field2', 'LEFT')
     *
     * @param string $joinTable The name of the table.
     * @param string $joinCondition the condition.
     * @param string $joinType 'LEFT', 'INNER' etc.
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
        $this->_join[] = Array($this->_predix.$joinTable, $joinCondition, $joinType);
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
     * CAC PHUONG THUC BEN DUOI DUNG DE CHAY HE THOMG.
     * KHONG CAN XEM DOAM NAY!
     * ###########################################################################################################
     */







    /**
     * TAO QUERY VA SET THAM SO DE UPDATE DU LIEU
     * @param String $tableName    Ten bang
     * @param Array  $data         du lie
     * @param String $type         Kieu update du lieu (INSERT / UPDATE)
     */ 
    protected function buildUpdateDataQuery($tableName,$data,$type="INSERT"){
        $t = strtoupper($type);
        if(!in_array($t, explode(' ', 'INSERT UPDATE'))) $t = "INSERT";
        $fields = "";
        $values = "";
        $updQuery = "";
        if($t == 'INSERT'){
            $fields.="(";
            $values = " VALUES(";
            $i = 0;
            foreach($data as $f => $v){
                $c = $i == 0 ? "": ", ";
                $name = $this->addParam($f,$v);
                $fields .= $c . $f;
                $values .= $c.$name;
                $i++;
            }
            $fields.=")";
            $values.=")";
            $updQuery = "INSERT INTO {$this->_predix}{$tableName}{$fields}{$values}";
        }else{
            $fields.=" SET";
            $i = 0;
            foreach($data as $f => $v){
                $c = $i == 0 ? " ": ", ";
                $name = $this->addParam($f,$v);
                $fields .= $c . $f . ' = '.$name;
                $i++;
            }
            $updQuery = "UPDATE {$this->_predix}{$tableName}{$fields}";
        }
        return $updQuery;
    }
    
    
    
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
                    $this->_where[] = $this->addConditionParam($key,$val);
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
            }else{
                $this->_where[] = $this->buildSingleWhereParamString($part);
            }
        }
        //todo
    }
    
    protected function addConditionParam($prop,$val=null,$operato='=',$cond='AND'){
        $o = $operato;
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
        return array($this->parseWherePar($f,$val,$o),$cond);
    }
    
    
    protected function buildConditionParamByString($args=null){
        $return = array();
        $as = explode(',',$args);
        foreach($as as $part){
            if(strpos($part,'{')==0 || strpos($part,'[')==0){
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
                $return[] = $this->buildSingleWhereParamString($part);
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
        
        return array($this->buildSingleWhereParam($w),$cond);
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
            $n = $name.'_'.($this->_paramKeysp[$name]+1);
            $this->_paramKeysp[$name]++;
        }else{
            $this->_paramKeysp[$name]=1;
        }
        $n = ":".$n;
        $this->_params[$n] = $this->fixVarTipe($val);
        return $n;
    }
    
    protected function parseWherePar($field = null, $val = null, $operatur = '='){
        $o = strtoupper(trim($operatur));
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
        if(count($vs)<2){
            if(substr($vs[0],0,1)=='@'){
                $vas = explode(':',substr($vs[0],1));
                if(count($vas)>1 && strtolower($vas[0])=="field"){
                    $d = $vas[1];
                    if($o == 'LIKE' || $o == 'NOTLIKE'){
                        $s = $f . ' '.(($o == 'NOTLIKE')?"NOT LIKE":"LIKE")
                            ."CONCAT('%', ".$this->quote($d).", '%')";
                    }else{
                        $s = $f. ' '.$o . ' ' . $this->quote($d);
                    }
                }else{
                    $s = $f. ' '.$o . ' '.$this->quote(substr($val,1));
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
    
    protected function fixVarTipe($str){
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
    
    public function quote($str){
        return $this->pdo->quote($str);
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
                elseif(isset($args[0])&&isset($args[2])){
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

?>