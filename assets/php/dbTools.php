<?php
include 'cfg.php';

error_reporting(0);

class dbTools {

    /** 
     * sql connection
    *
    * @var object
    */
    private $_connection;
    
    public function __construct() {
        $this->_connection = mysql_connect(_DBHOST, _UNAME, _PASS);
        if ($this->_connection) {
            if (!mysql_select_db(_DB, $this->_connection)) {
                die("Cannot connect to database server : " . "Contact your administrator !<br/>");
            }
        } else {
            die("Cannot connect to database server : " . "Contact your administrator !<br/>");
        }
    }

    /**
     * Genereate an insert sql query
     * 
     * <pre>
     * $table = table name<br/>
     * $value  = array("column_name"=>$val) <br/>or $value["column_name"] = $val;
     * 
     * example:<br/>
     * $tool                = new dbTool();<br/>
     * $data["id"]          = "USER01";<br/>
     * $data["username"]    = "xander";<br/>
     * $data["pass"]        = "nothing";<br/>
     * $tool->insert("user_table", $data);<br/>
     * </pre>
     * @access	public
     * @param 	string  the table to insert data into
     * @param 	array   an associative array of insert values
     * @return 	void
     */
    public function insert($table = "", $value = array()) {
        $val = "";
        $col = "";
        if (count($value) != 0) {
            foreach ($value as $column => $value) {
                $col .= "," . $column;
                
                //don't add single quote if value = NOW() or value = CURRENT_DATE()
                if($value != 'NOW()' && $value != 'CURRENT_DATE()' && $value != 'LAST_INSERT_ID()'){
                    $val .= ",'" . $value . "'";
                }else{
                    $val .= ", " . $value;
                }
            }
            $col = "(" . substr($col, 1) . ")";
            $val = "(" . substr($val, 1) . ")";
        }
        $query = "insert into {$table}{$col} values{$val}";
        return $this->query($query, 1);
    }

    /**
     * Delete something from database
     * 
     * example:<br/>
     * <pre>
     * $tool    = new dbTool();<br/>
     * $where   = "id = 'user01'";<br/>
     * $tool->delete("user_table", $where);<br/>
     * </pre>
     * @access	public
     * @param 	string  the table to delete from
     * @param 	string  the where clause
     * @return 	void
     */
    public function delete($table = "", $where = "") {
        if($table != "" && $where != ""){
            $query = "delete from {$table} where ".$where;
            return $this->query($query, 1);
        }
    }

    /**
     * Generate an update sql query
     *
     * example:<br/>
     * <pre>
     * $tool                = new dbTool();<br/>
     * $where               = "id = 'USER01'";<br/>
     * $data["username"]    = "xander";<br/>
     * $data["pass"]        = "new_pass";<br/>
     * $tool->update("user_table", $data, $where);<br/>
     * </pre>
     *     
     * @access	public
     * @param	string the table to retrieve the results from
     * @param	array an associative array of update values
     * @param	string the where clause
     * @return	void
     */
    public function update($table = "", $value = array(), $where = "") {
        $set = "";
        if (!empty($value)) {
            foreach ($value as $column => $val) {
                if($val != 'NOW()' && $val != 'CURRENT_DATE()'){
                    $set .= ",$column = '$val'";
                }else{
                    $set .= ",$column = $val";
                }
                
            }
            $set = substr($set, 1);
        }
        $query = "update {$table} set {$set} where {$where}";
        return $this->query($query, 1);
    }

    /**
     * Generate select query<br/>
     * select $column from $table where $where<br/>
     * <br/>
     * example:<br/>
     * <pre>
     * $tool    = new dbTool();<br/>
     * $where   = "id = 'USER01'";<br/>
     * $column  = array('username', 'birth', 'last_login'); <===== just select this column<br/>
     * $column  = ''; <<=== select all column <br/>
     * $tool->getAll("user_table", $column, $where);<br/>
     * </pre>
     * 
     * @access	public
     * @param	string	the table to retrieve the results from
     * @param	array	an associative array of select column
     * @param	mixed	the where clause
     * @return	object
     */
    public function getAll($table = "", $column = array(), $where = "") {
        $col = "";
        $cond = "";
        if (!empty($column) && $column != "" && count($column) != 0) {
            foreach ($column as $dt) {
                $col .= ", " . $dt;
            }
            $col = substr($col, 1);
        } else {
            $col = "*";
        }

        if (!empty($where)) {
            $cond = " where " . $where;
        }

        $query = "select {$col} from {$table} " . $cond;
        return $this->query($query);
    }
    
    /**
     * Generate single select query<br/>
     * select * from $table where primary_key = $id<br/>
     * example:<br/>
     * <pre>
     * $tool    = new dbTool();<br/>
     * $result  = $tool->getByPK("user_table", $userID);<br/>
     * </pre>
     * 
     * @access	public
     * @param	string	the table to retrieve the results from
     * @param	mixed	the primary_key
     * @return	object
     */
    public function getByPK($table = "", $id = ""){
        if($table != ""){
            $colName    = $this->getPrimaryColumn($table);
            if($colName != ""){
                $qr         = "select * from $table where $colName = '$id'";
                $rr         = $this->query($qr);
                if(count($rr) != 0){
                    return $rr;
                }else{
                    return null;
                }
            }else{
                return null;
            }
        }else{
            return null;
        }
    }
    
    /**
     * Generate an update sql query by primary key
     *
     * example:<br/>
     * <pre>
     * $tool                = new dbTool();<br/>
     * $id                  = 'USER01';<br/>
     * $data["username"]    = "xander";<br/>
     * $data["pass"]        = "new_pas";<br/>
     * $tool->updateByPK("user_table", $data, $id);<br/>
     * </pre>
     *     
     * @access	public
     * @param	string the table to retrieve the results from
     * @param	array an associative array of update values
     * @param	mixed the primary key id
     * @return	void
     */
    public function updateByPK($table = "", $data = array(), $id = ""){
        $column = $this->getPrimaryColumn($table);
        $where  = "$column = '$id'";
        return $this->update($table, $data, $where);
    }
    
    /**
     * Delete a row from database by primary key id
     * 
     * example:<br/>
     * <pre>
     * $tool    = new dbTool();<br/>
     * $id      = 'user01';<br/>
     * $tool->deleteByPK("user_table", $id);<br/>
     * </pre>
     * @access	public
     * @param 	string  the table to delete from
     * @param 	string  the primary key id
     * @return 	void
     */
    public function deleteByPK($table = "", $id = ""){
        $column = $this->getPrimaryColumn($table);
        $where  = "$column = '$id'";
        return $this->delete($table, $where);
    }
    
    /**
     * Just a query helper
     *
     * example:<br/>
     * <pre>
     * $tool    = new dbTool();<br/>
     * $query   = "select * from some_table";<br/>
     * $tool->query($query);<br/>
     * </pre>
     * 
     * @access	public
     * @param	string	An SQL query string
     * @return	mixed
     */
    public function query($query = "", $t = 0) {
        $connection = $this->_connection;
        $result = mysql_query($query, $connection) or die("Cannot connect to database server, Contact your administrator !<br/>");
        if ($result) {
            if($t == 0){
                return $this->sqlToArray($result);
            }else{
                return $result;
            }
        } else {
            "Contact your administrator !<br/>";
        }
    }
    
    /**
     * Convert mysql query result to php array
     * 
     * @access	private
     * @param	mysql_result	MySql query result
     * @return	array
     */
    private function sqlToArray($result){
        $res = array();
        while($rw   = mysql_fetch_array($result)){
            $res[]  = $rw;
        }
        return $res;
    }
    
    /**
     * get primary key column name
     * 
     * @access	private
     * @param	String	Table name
     * @return	String
     */
    private function getPrimaryColumn($table = ""){
        $colName    = "";
        if($table != ""){
            $query  = "show KEYS from $table WHERE Key_name = 'PRIMARY'";
            $res    = $this->query($query);
            if(count($res) != 0){
                $colName    = $res[0]['Column_name'];
            }
        }
        
        return $colName;
    }
    
    /*
     * Get the autoincrement primary key used for an insert operation
     * 
     * @access public
     * @return int
     * 
     */
    public function getLastPK(){
//        $query  = "select last_insert_id() as id";
//        $result = $this->sqlToArray($this->query($query));
        return mysql_insert_id();//$result[0]['id'];
    }
    public function destroy() {
        return mysql_close($this->_connection);
    }
}

?>
