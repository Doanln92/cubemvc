<?php

/**
 * @author Doanln
 * @copyright 2017
 */

/**
 * @class db
 * class tinh
 */ 

class DB{
    /**
     * @var Object $DB doi tuong ket noi csdl
     */  
    protected static $DB;
    
    /**
     * @var String $host 
     */
    protected static $host = 'localhost';
    
    protected static $dbname = 'test';
    
    protected static $username = 'root';
    
    protected static $password = '';
    
    protected static $dbms = 'mysql';
    
    protected static $prefix = '';
    
    protected static $reportLevel = 2;
    
    protected static $error_message = null;
    
    protected static $isConnect = false;
    

    
    
    /**
     * cai dat cac tham so
     * @param mixed $host mang toan bo tham so hoac string host / server / path
     * @param string $dbname
     * @param String $username ten truy cap
     * @param String $password mat khau
     * @param String $dbms he quan tri csdl
     */ 
     
    public static function config($host = 'localhost', $dbname = 'test', $user = 'root', $pass = '', $prefix=null, $dbms='MYSQL') {
        self::$host = $host;
        self::$dbname = $dbname;
        self::$username = $user;
        self::$password = $pass;
        self::$prefix = $prefix;
        self::$dbms = $dbms;
    }
    
    /**
     * cai dat cac tham so
     * @param mixed $host mang toan bo tham so hoac string host / server / path
     * @param string $dbname
     * @param String $username ten truy cap
     * @param String $password mat khau
     * @param String $dbms he quan tri csdl
     */ 
     
    public static function connect($host = null, $dbname = null, $user = null, $pass = null, $prefix=null, $dbms=null) {
        if(self::$isConnect) return true;
        $h = is_null($host)    ? self::$host     : $host;
        $d = is_null($dbname)  ? self::$dbname   : $dbname;
        $u = is_null($user)    ? self::$username : $user;
        $p = is_null($pass)    ? self::$password : $pass;
        $f = is_null($prefix)  ? self::$prefix   : $prefix;
        $m = is_null($dbms)    ? self::$dbms     : $dbms;
        try{
            self::$DB = new PDOdb($h,$d,$u,$p,$f,$m);
            self::$isConnect = true;
        }
        catch(Exception $e){
            self::$error_message = $e->getMessage();
            self::reportError();
        }
    }
    
    
    public static function disconnect()
    {
        if(!self::$isConnect) return false;
        self::$DB->disconnect();
        self::$DB = null;
    }
    
    
    public static function getConnect()
    {
        if(!self::$isConnect) self::connect();
        return self::getPDO();
    }
    
    
    
    
    /**
     * dua cac tham so ve mac dinh
     */ 
    
    public static function reset(){
        try{
            self::$DB->reset();
        }
        catch(Exception $e){
            self::$error_message = $e->getMessage();
            self::reportError();
        }
    }
    
    
    /**
     * lay cac ban ghi trong csdl
     * @param String $tableName      ten bang
     * @param String $select         Danh sach cac cot can select
     * @param Mixed $condition       dieu kien de select (co the su dung string hoac array) bao gom ca where, group by, having, order by, limit
     * 
     * @note cach su dung tham so:
     * uu tin where. nghia la cac KEY binh thuong se duoc hieu la where.
     * vi du trong array('KEY' => 'VAL') se duoc hieu la mot phan cua menh de where: KEY = 'VAL'
     * trong chuoi request KEY=VAL cung tuong tu.
     * ngoai ra con co the su dung cac toan tu so sanh sau KEY.
     * vi du trong mang array('KEY<=' => 'VAL', 'KEY >=' => 'VAL', 'KEY like' => 'VAL', 'KEY notlike' => 'VAL')
     * chu � la not like viet lien
     * voi chuoi cung tuong tu
     * 
     * @return Array | Obj
     */ 
    
    public static function get($tableName,$select='*',$condition=null,$fetch_type=null){
        $rs = null;
        try{
            $rs = self::$DB->get($tableName,$select,$condition,$fetch_type);
        }
        catch(Exception $e){
            self::$error_message = $e->getMessage();
            self::reportError();
        }
        return $rs;
    }
    
    /**
     * lay 1 ban ghi trong csdl
     * @param String $tableName      ten bang
     * @param String $select         Danh sach cac cot can select
     * @param Mixed $condition       dieu kien de select (co the su dung string hoac array) bao gom ca where, group by, having, order by, limit
     * 
     * @note cach su dung tham so: giong ham get
     * 
     * @return Array | Obj
     */ 
    
    public static function getOne($tableName,$select='*',$condition=null,$fetch_type=null){
        $rs = null;
        try{
            $rs = self::$DB->getOne($tableName,$select,$condition,$fetch_type);
        }
        catch(Exception $e){
            self::$error_message = $e->getMessage();
            self::reportError();
        }
        return $rs;
    }
    
    
    /**
     * lay cac ban ghi trong csdl
     * @param String $tableName      ten bang
     * @param String $column         ten cot
     * @param Mixed $condition       dieu kien de select (co the su dung string hoac array) bao gom ca where, group by, having, order by, limit
     * 
     * @note cach su dung tham so: giong het
     * 
     * @return Array | Obj
     */ 
    
    public static function getVal($tableName,$column=null,$condition=null){
        $rs = null;
        try{
            $rs = self::$DB->getVal($tableName,$column,$condition,'assoc');
        }
        catch(Exception $e){
            self::$error_message = $e->getMessage();
            self::reportError();
        }
        return $rs;
    }
    
    /**
     * lay cac ban ghi trong csdl
     * @param String $tableName      ten bang
     * @param Mixed $condition       dieu kien de select (co the su dung string hoac array) bao gom ca where, group by, having, order by, limit
     * 
     * @note cach su dung tham so: giong ham get
     * 
     * @return int
     */ 
    
    public static function count($tableName, $condition = null){
        $rs = null;
        try{
            $rs = self::$DB->count($tableName,$condition);
        }
        catch(Exception $e){
            self::$error_message = $e->getMessage();
            self::reportError();
        }
        return $rs;
    }
    
    /**
     * lay cac ban ghi trong csdl
     * @param String $tableName      ten bang
     * @param Mixed $condition       dieu kien de xoa (co the su dung string hoac array) bao gom ca where, group by, having, order by, limit
     * 
     * @note cach su dung tham so: giong ham bet
     * 
     * @return int
     */ 
    
    public static function delete($tableName, $condition = null){
        $rs = null;
        try{
            $rs = self::$DB->delete($tableName,$condition);
        }
        catch(Exception $e){
            self::$error_message = $e->getMessage();
            self::reportError();
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
     
    public static function insert($tableName,$data){
        $rs = null;
        try{
            $rs = self::$DB->insert($tableName,$data);
        }
        catch(Exception $e){
            self::$error_message = $e->getMessage();
            self::reportError();
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
     
    public static function update($tableName,$data, $condition = array()){
        $rs = null;
        try{
            $rs = self::$DB->update($tableName,$data,$condition);
        }
        catch(Exception $e){
            self::$error_message = $e->getMessage();
            self::reportError();
        }
        return $rs;
    }
    
    /**
     * kiwm tra bang co ton tai hay ko
     * @param String $tableName ten bang
     * 
     * @return Bool
     */ 
    
    public static function exists($tableName){
        return self::$DB->exists($tableName);
    }
    
    
    public static function createTable($tableName,$func = null){
        return self::$DB->createTable($tableName,$func);
    }
    public static function alterTable($tableName,$func = null){
        return self::$DB->alterTable($tableName,$func);
    }
    public static function dropTable($tableName){
        return self::$DB->dropTable($tableName,$func);
    }

    public static function renameTable($oldTableName,$newTableName=null)
    {
        if(!$newTableName) $newTableName = $oldTableName;
        eval("\$func = function(\$table){
                \$table->rename('$newTableName');
            };");
        return self::$DB->alterTable($oldTableName, $func);
    }
    
    


    /**
     * thuc thi query
     * @param string
     * 
     * @return int
     */ 
    
    public static function query($query){
        $rs = null;
        try{
            $rs = self::$DB->query($query);
        }
        catch(Exception $e){
            self::$error_message = $e->getMessage();
            self::reportError();
        }
        return $rs;
    }
    /**
     * thuc thi query
     * @param string
     * 
     * @return int
     */ 
    
    public static function exec($query){
        $rs = null;
        try{
            $rs = self::$DB->exec($query);
        }
        catch(Exception $e){
            self::$error_message = $e->getMessage();
            self::reportError();
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
    
    public static function execute($query, $params = array()){
        $stmt = null;
        try{
            $stmt = self::$DB->execute($query,$params);
        }catch(Exception $e){
            $msg = $e->getMessage();
            self::$error_message = $msg;
            self::reportError();
        }
        return $stmt;
    }
    
    
    
    /**
     * ham them dieu kien cho menh de where
     * @param String
     * @param mixed
     * @param String
     * @param String
     */ 
    
    public static function where($prop, $value = PDODBNULL, $operator = '=', $cond = 'AND'){
        return self::$DB->where($prop,$value,$operator,$cond);
    }
    /**
     * ham them dieu kien cho menh de where
     * @param String
     * @param mixed
     * @param String
     */ 
    
    public static function orWhere($prop, $value = PDODBNULL, $operator = '='){
        return self::$DB->orWhere($prop,$value,$operator);
    }
    
    /**
     * ham them dieu kien cho menh de having
     * @param String
     * @param mixed
     * @param String
     * @param String
     */ 
    
    public static function having($prop, $value = PDODBNULL, $operator = '=', $cond = 'AND'){
        return self::$DB->having($prop,$value,$operator,$cond);
    }
    /**
     * ham them dieu kien cho menh de having
     * @param String $prop thuoc tinh hay bieu thuc
     * @param mixed
     * @param String
     */ 
    
    public static function orHaving($prop, $value = PDODBNULL, $operator = '='){
        return self::$DB->orHaving($prop,$value,$operator);
    }
    
    /**
     * This method allows you to concatenate joins for the final SQL statement.
     *
     * @uses dv::join('table1', 'field1 <> field2', 'LEFT')
     *
     * @param string $joinTable The name of the table.
     * @param string $joinCondition the condition.
     * @param string $joinType 'LEFT', 'INNER' etc.
     * 
     * @return PDOObj
     */
    public static function join($joinTable, $joinCondition, $joinType = '')
    {
        return self::$DB->join($joinTable, $joinCondition, $joinType);
    }
    
    public static function leftJoin($joinTable, $joinCondition){
        return self::$DB->leftJoin($joinTable,$joinCondition);
    }
    public static function rightJoin($joinTable, $joinCondition){
        return self::$DB->rightJoin($joinTable,$joinCondition);
    }
    public static function outerJoin($joinTable, $joinCondition){
        return self::$DB->outerJoin($joinTable,$joinCondition);
    }
    public static function innerJoin($joinTable, $joinCondition){
        return self::$DB->innerJoin($joinTable,$joinCondition);
    }
    public static function leftOuterJoin($joinTable, $joinCondition){
        return self::$DB->leftOuterJoin($joinTable,$joinCondition);
    }
    public static function rightOuterJoin($joinTable, $joinCondition){
        return self::$DB->rightOuterJoin($joinTable,$joinCondition);
    }
    
    public static function groupBy($groupByField)
    {
        $groupByField = preg_replace("/[^-a-z0-9\.\(\),_\*]+/i", '', $groupByField);

        return self::$DB->groupBy($groupByField);
    }
    
    public static function orderBy($orderByField, $orderbyDirection = "DESC", $customFieldsOrRegExp = null)
    {
        return self::$DB->orderBy($orderByField, $orderbyDirection, $customFieldsOrRegExp);
    }
    
    
    public static function limit($limit=null, $to = null){
        return self::$DB->limit($limit,$to);
    }
    
    
    
    
    /**
     * ham set dieu kien cho viect truy van
     * @param mixed
     * @return Object 
     */ 
    
    public static function setCondition($args = null){
        return self::$DB->setCondition($args);
    }
    
    
    
    public static function getDBObj(){
        return clone self::$DB;
    }
    
    public static function getStmt(){
        return self::$DB->getStmt();
    }
    public static function setPrefix($prefix=''){
        return self::$DB->setPrefix($prefix);
    }
    
    public static function getPrefix(){
        return self::$DB->getPrefix();
    }
    public static function setFetchType($pft=PDO::FETCH_ASSOC){
        return self::$DB->setFetchType($pft);
    }
    
    public static function getFetchType(){
        return self::$DB->getFetchType();
    }
    public static function getPDO(){
        return self::$DB->getPDO();
    }
    public static function getLastQuery(){
        return self::$DB->getLastQuery();
    }
    
    
    
    
    
    
    
    /**
     * ham hien thi thong bao loi. co the tuy chinh dung code, bo qua, hay van hien thong bao va tiep tuc chay code
     * @param String $message thong bao loi
     * 
     * @return void
     */ 
    
    protected static function reportError($message=null){
        $m = is_null($message) ? self::$error_message : $message;
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
                self::$DB->disconnect();
                die('<br />'.$m."<br />");
        }
    }
    
    
    
    /**
     * thiet lap thong bao loi
     * @param int $level
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
    public static function setDBErrorReportingLevel($level = 0){
        if(is_int($level) && $level >= 0 && $level <= 3){
            return self::$DB->setErrorReportingLevel($level);
        }
        return false;
    }

    public static function quote($str=null)
    {
        return self::$DB->quote($str);
    }
    
    
}

?>