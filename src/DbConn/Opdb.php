<?php

    namespace App\DbConn;

use PhpOffice\PhpSpreadsheet\Calculation\Logical\Boolean;

    class Opdb{
        protected $error = NULL;
        private $stmt = NULL;
        private $env = '';
        private $conn = NULL;
        protected $response = [];
        
        public function __construct(){
            register_shutdown_function( array( $this, '__destruct' ) );
            
        }

        public function __destruct() {
            if($this->conn != null){
                $this->disconnect();
            }
            return null;
        }
        
        public function __clone(){ }
        public function __wakeup(){ }

        /**
         * Used to get the result of any action to the database
         * 
         * @access public
         * @param string $type 
         * @return mixed array | json
         */
        public function get_response($type = 'json')
        {
            return ($type === 'array')?$this->response:(($type === 'json')?json_encode($this->response,JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT):json_encode([]));
            
        }

        public function get_error(){
            $this->stmt = null;
            return $this->error;
        }

        private function connect() :void
        {
            if ($this->conn == null) {

                if(!empty($_ENV['APP_URL_DEV']) && $_ENV['APP_URL_DEV'] === $_SERVER['SERVER_NAME']){
                    $this->env = '_DEV';
                }elseif(!empty($_ENV['APP_URL_TEST']) && $_ENV['APP_URL_TEST'] === $_SERVER['SERVER_NAME']){
                    $this->env = '_TEST';
                }

                $config_env = array(
                    "hostdb"=>isset($_ENV['DB_HOST'.$this->env])?$_ENV['DB_HOST'.$this->env]:"",
                    "namedb"=>isset($_ENV['DB_DATABASE'.$this->env])?$_ENV['DB_DATABASE'.$this->env]:"",
                    "userdb"=>isset($_ENV['DB_USERNAME'.$this->env])?$_ENV['DB_USERNAME'.$this->env]:"",
                    "passdb"=>isset($_ENV['DB_PASSWORD'.$this->env])?$_ENV['DB_PASSWORD'.$this->env]:"",
                    "charsetdb"=>"utf8",
                    "collatedb"=>"utf8_unicode_ci",
                    "typedb"=>isset($_ENV['DB_CONNECTION'])?$_ENV['DB_CONNECTION']:"mysql"
                );

                $dsn = 'mysql:host='.$config_env['hostdb'].';dbname='.$config_env['namedb'].';charset='.$config_env['charsetdb'];
                $options = array(
                    \PDO::ATTR_PERSISTENT => true,
                    \PDO::ATTR_ERRMODE =>\PDO::ERRMODE_EXCEPTION
                );

                try{
                    $this->conn = new \PDO($dsn,$config_env['userdb'],$config_env['passdb'],$options);
                    $this->conn->exec('set names '.$config_env['charsetdb']);
                    $this->error = null;
                }catch(\PDOException $e){
                    $this->conn = null;
                    $this->error = $e->getMessage();
                }
            }
        }
        
        public function disconnect() :void
        {
            if ($this->conn != null) {
                
                if($this->stmt != null){
                    $this->stmt->closeCursor();
                    $this->stmt = null;
                    $this->error = null;
                }
                
                $this->conn = null;
            }
        }

        public function check_database() :bool
        {
            $this->connect();
            if($this->conn != null){
                $this->disconnect();
                $this->response = array('status'=>'OK');
                return true;
            }else{
                $this->response = array('status'=>'NOK', 'error'=>'Problems with the Database', 'conn_state'=>$this->error);
                return false;
            }
        }

        public function isTable($table = null, $close = false) :bool
        {

            if(!empty(trim($table)) && is_string(trim($table))){
    
                $sql = "SHOW TABLES LIKE '".$table."'";
    
                if($this->exec_sql($sql)){
                
                    $response = ($this->stmt->rowCount() == 1)?true:false;
                    $this->stmt = null;
                    $this->error = null;
                    if($close){ $this->disconnect(); }
                    $this->response = array('status'=>'OK');
                    return $response;
    
                }else{
                    $this->response = array('status'=>'NOK', 'error'=>'the table does not exist');
                    return false;
                }
                
            }else{
                $this->response = array('status'=>'NOK', 'error'=>'Exist some problems with the name of the table.');
                $this->error = 'Exist some problems with the name of the table.';
                return false;
            }
            
        }

        public function hasItems($table,$fields_array,$conditions='',$order='', $limit=0, $start=0, $close = false)
        {
            if(!empty(trim($table)) && is_string($table)){
                if(!empty($fields_array) && is_array($fields_array)){
    
                    $fields = implode(", ", array_values($fields_array));
                    $whereSql = $this->conditionOrder($conditions, $order, $limit, $start);
    
                    $sql = "SELECT ".$fields." FROM ".$table.$whereSql['where'].$whereSql['order'].$whereSql['limit'];

                    if($this->exec_sql($sql)){
                        $response = $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
                        $this->stmt = null;
                        if($close){ $this->disconnect(); }
                        return $response;
                    }else{
                        return false;
                    }
    
                }else{
                    $this->error = 'Los campos para la selección deben ester en un array simple y dicho array no puede estar vacío.';
                    return false;
                }
            }else{
                $this->error = 'El nombre de la tabla debe ser texto y no puede estar vacío.';
                return false;
            }
        }

        public function addItem(string $table, array $data_array, $close = false)
        {
            if(!empty(trim($table)) && is_string($table)){
                if(!empty($data_array) && is_array($data_array)){
                    $sql = "INSERT INTO ".$table." (`".implode("`, `", array_keys($data_array))."`) VALUES ('".implode("', '", array_values($data_array))."')";

                    $this->stmt = $this->exec_sql($sql);
                    if($this->stmt){
                        $lastInsertId = $this->conn->lastInsertId();
                        $this->stmt = null;
                        $this->error = null;
                        if($close){ $this->disconnect(); }
                        return $lastInsertId;
                    }else{
                        return false;
                    }
                }else{
                    $this->error = 'Los campos para adionar no pueden estar vacíos y deben ser un array tipo llave => valor';
                    return false;
                }
            }else{
                $this->error = 'El nombre de la tabla tiene que ser un texto y no puede estar vacío.';
                return false;
            }
        }

        public function updItem(string $table, array $data_array, $conditions, $close = false) :bool
        {
            $colvalSet = '';
            if(!empty(trim($table)) && is_string($table)){
                if(!empty($data_array) && is_array($data_array)){
                    $i = 0;
                    foreach($data_array as $key=>$val){
                        $pre = ($i > 0)?', ':'';
                        $colvalSet .= $pre.$key."='".$val."'";
                        $i++;
                    }
    
                    $whereSql = $this->conditionOrder($conditions);
    
                    $sql = "UPDATE ".$table." SET ".$colvalSet.$whereSql['where'];
                    // var_dump($sql);    
                    if($this->exec_sql($sql)){
                        $this->stmt = null;
                        if($close){ $this->disconnect(); }
                        return true;
                    }else{
                        return false;
                    }
    
                }else{
                    $this->error = 'Los campos para adionar no pueden estar vacíos y deben ser un array tipo llave => valor';
                    return false;
                }
            }else{
                $this->error = 'El nombre de la tabla tiene que ser un texto y no puede estar vacío.';
                return false;
            }
        }

        public function delItem(string $table, $conditions, $close = false) :bool
        {
            if(!empty(trim($table)) && is_string($table)){
    
                $whereSql = $this->conditionOrder($conditions);
    
                $sql = "DELETE FROM ".$table.$whereSql['where'];
    
                if($this->exec_sql($sql)){
                    $this->stmt = null;
                    if($close){ $this->disconnect(); }
                    return true;
                }else{
                    return false;
                }
    
            }else{
                $this->error = 'El nombre de la tabla tiene que ser un texto y no puede estar vacío.';
                return false;
            }
        }

        public function execSql(string $sql_srting, $close = false){

            if($this->exec_sql($sql_srting)){

                if($this->stmt->rowCount() > 0 ){

                    try{
                        // $response = ($this->stmt->rowCount() == 1)?$this->stmt->fetch(\PDO::FETCH_ASSOC):$this->stmt->fetchAll(\PDO::FETCH_ASSOC);
                        $response = $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
                        $this->error = null;
                    }catch(\PDOException $e){
                        $this->error = $e->getMessage();
                        return false;
                    }
                    
                    $this->stmt = null;
                    if($close){ $this->disconnect(); }

                    return $response;

                }else{
                    return true;
                }
                
            }else{
                return false;
            }

        }

        private function exec_sql($sql_srting) :bool
        {
            if(!empty(trim($sql_srting)) && is_string($sql_srting)){
                $this->connect();
                if($this->conn != null){
    
                    try{
    
                        $this->stmt = $this->conn->prepare($sql_srting);
                        $this->stmt->execute();
                        $this->error = null;
                        return true;
    
                    }catch(\PDOException $e){
                        $this->error = $e->getMessage();
                        return false;
                    }
                    
                }else{
                    $this->error = "Don't exist connection with the database";
                    return false;
                }
                
            }else{
                $this->error = (empty(trim($sql_srting)))?'The SQL query cannot be empty':'SQL query must be a string';
                return false;
            }
        }

        private function conditionOrder($condiciones='',$orden='', $limite=0, $start=0)
        {
            $whereSql = '';
            $orderSql = '';
            $limitsql = '';
    
            if(!empty($condiciones) && is_array($condiciones)){
                $whereSql .= ' WHERE ';
                $i = 0;
                foreach($condiciones as $key => $value){
                    $pre = ($i > 0)?' AND ':'';
                    $whereSql .= $pre.$key." = '".$value."'";
                    $i++;
                }
            }elseif (!empty($condiciones) && !is_array($condiciones)){
                $whereSql = ' WHERE '.$condiciones;
            }
    
            if(!empty($orden) && is_array($orden)){
                $orderSql .= ' ORDER BY ';
                $i = 0;
                foreach($orden as $key => $value){
                    $pre = ($i > 0)?',':'';
                    $orderSql .= $pre.$key." ".$value;
                    $i++;
                }
            }elseif (!empty($orden) && !is_array($orden)){
                $orderSql = $orden;
            }
    
            if($limite > 0 && is_int($limite)){
                $limitsql = " LIMIT $start, $limite";
            }
            
            return array('where'=>$whereSql,'order'=>$orderSql, 'limit'=>$limitsql);
        }

        protected function dataToJson($data)
        {
            $response = '{}';
            if(is_array($data)){
                $response = json_encode($data,JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT | JSON_HEX_APOS);
            }
            return $response;
        }

    }

?>