<?php 


class Session {
    

    public static function init() {
        session_start();
    }

    public static function put($key, $value) {

        $_SESSION[$key] = $value;
    }

    public static function get($key = false) {

        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }

        return null;
    }

    public static function destory() {
        session_destroy();
    }

    public static function forget() {
        session_destroy();
    }

    public static function remove($key){
        if(self::get($key) != null ){
            unset($_SESSION[$key]);
        }
    }

    public static function flush($key, $value = null) {
        if($value == null){
                $data = self::get($key);
                self::remove($key);
               return  $data;     
        }else{
            $_SESSION[$key] = $value;
            $_SESSION['keys'][] = $key;
        }
    }

}


class Status{
	
public static function show($msg = null){
if($msg != null):
?>
<script>    
var html = '<div id="custom-alert" class="custom-alert"><div class="status"></div></div>';
var css ='<style>\
.custom-alert{\
position: fixed;\
top: 0%;\
left: 0%;\
background: #3ac372;\
text-align: center;\
width: 100%;\
z-index: 9999999999;\
color: #fff;\
overflow: hidden;\
height:0px;\
font-size: 2em;\
display: table;}\
.status{\
	vertical-align: middle;\
	display: table-cell;\
}\
</style>';
document.write(css+html);

    $(".custom-alert").animate({
        height: '70px'
    }).css( 'box-shadow', '#8cea1e45 9px 15px 23px 0px');
    $('.status').html("<?=$msg?>");
    setTimeout(function(){
        $(".custom-alert").animate({
            height: '0px'
        }).css( 'box-shadow', '4px 5px 21px 1px #00000000');
        $('.status').html('');
    },2000);
	

</script>
<?php

endif;
}
}
class Storage{
    public static function  upload($files,$file_name = null,$target_dir = null){
        if(empty($files))
                    return null;
        $target_dir = 'storage/'.$target_dir;
        $name = basename($files["name"]);

        if($file_name == null)
            $file_name = basename($files["name"]);
        else{
            $ext = strtolower(pathinfo($name,PATHINFO_EXTENSION));
            $file_name  =  $file_name.'.'.$ext;
           

        }
        $target_file = $target_dir .$file_name;

        $uploadOk = 1;

        
        
                if(!is_dir($target_dir)){
                    mkdir($target_dir);
                }

                if (move_uploaded_file($files["tmp_name"], $target_file)) {
                    return $target_file;
                } 
               
             
        

    return null;
    }
}
class Request{

    public static function method(){
        return $_SERVER['REQUEST_METHOD'];
      }



   public static function input($name = false){
        $method = self::method();
        if($method == "POST"){
           
                    if(!$name){
                        return $_POST;
                    }else{
                        return isset($_POST[$name]) ? $_POST[$name] : null;
                    }
            
        }else if($method == "GET"){
            if(!$name){
                return $_GET;
            }else{
                return isset($_GET[$name]) ? $_GET[$name] : null;
            }
        }

        return null;
    
    }

    public static function file($name = false){
          $method = $_SERVER['REQUEST_METHOD'];
        if($method == "POST"){
            if(!$name){
                return $_FILES;
            }else{
                return $_FILES[$name];
            }
        }
        return null;
    }



    public static function uri(){
        $uri = isset($_GET['uri'])? $_GET['uri'] : '/';
        return  rtrim($uri,'/');
    }
}



class Pass{
	 function __construct() {
        
    }

    public static function set($data, $cost = 12) {

        $options = ['cost' => $cost];
        return password_hash($data, PASSWORD_BCRYPT, $options);
    }

    public static function get($data, $hash) {

        return password_verify($data, $hash);
    }


    public static function getToken($length = 32){
            $token = "";
            $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";

            $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";

            $codeAlphabet.= "0123456789";

            $max = strlen($codeAlphabet); 
       
           for ($i=0; $i < $length; $i++) {
               $token .= $codeAlphabet[rand(0, $max-1)];
           }
       
           return $token;
       }
    
}


// model class start


class Model extends PDO {

    public function __construct() {
        try {
            parent::__construct(DSN, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
        } catch (Exception $e) {
            exit('<br><h2><br><center>!Config Error.<br><small style="color:gray">Setup your config.php file and enjoy :) </small></center></h2>');
        }
    }

    /**
     * Create table
     * @param string $table A name of table to insert into
     * @param string $data An associative array
     */
    function create_table($table, $data) {
	
        $sql = "CREATE TABLE IF NOT EXISTS $table (";
        $num = count($data);
        for ($i = 0; $i < $num - 1; $i++):
            $sql .= $data[$i] . ", ";
        endfor;
        $sql .= $data[$num - 1] . ");";

        return $this->exec($sql);
    }

    /**
     * insert
     * @param string $table A name of table to insert into
     * @param string $data An associative array
     */
    public function add($table, $data) {
        ksort($data);

        $fieldNames = implode('`, `', array_keys($data));
        $fieldValues = ':' . implode(', :', array_keys($data));
        $sth = $this->prepare("INSERT INTO `$table` (`$fieldNames`) VALUES ($fieldValues)");

        foreach ($data as $key => $value) {
            $sth->bindValue(":$key", $value);
        }

        $s = $sth->execute();
        return $s;
    }

    /**
     * update
     * @param string $table A name of table to insert into
     * @param string $data An associative array
     * @param string $where the WHERE query part
     */
    public function modify($table, $data,$where) {
        ksort($data);

        $fieldDetails = NULL;
        foreach ($data as $key => $value) {
            $fieldDetails .= "`$key`=:$key,";
        }
        $fieldDetails = rtrim($fieldDetails, ',');

        $sth = $this->prepare("UPDATE `$table` SET $fieldDetails  $where");

        foreach ($data as $key => $value) {
            $sth->bindValue(":$key", $value);
        }

        $sth->execute();
    }

    /**
     * Fetch row
     * @param string $table A name of table to get all data
     * @param string $cols the WHERE query part
     */
    public function fetch_some($table, $cols, $where, $operator) {
        ksort($where);
                $fields = '';
                $count = count($where);
                $i = 0;
                foreach($where as $key=>$val):
                
                   if($i<$count-1){
                   $fields .= $key.' '.$operator.' :'. $key.', ' ;
                   }else{
                       $fields .= $key.' '.$operator.' :'. $key;
                   } $i++;
                endforeach;
              
                $pre = $this->prepare("SELECT $cols FROM $table WHERE $fields");
                foreach ($where as $key => $value):
                    
                     $pre->bindValue(":$key", $value);
                 endforeach;
                 $pre->execute();
                 return $pre->fetch(PDO::FETCH_ASSOC); 
         
    }
    
    
    /**
     * Fetch row
     * @param string $table A name of table to get all data
     * @param string $cols the WHERE query part
     */
    public function fetch_row($table, $cols = '*', $where = false, $operator = '=') {
        if(!$where){
            
        $pre = $this->prepare("SELECT $cols FROM $table");
        $pre->execute();
        return $pre->fetch(PDO::FETCH_ASSOC); 
        }else{
            if(!is_array($where)){
                
               $pre = $this->prepare("SELECT $cols FROM $table $where");
               $pre->execute();
               return $pre->fetch(PDO::FETCH_ASSOC);  
            }else{
                
               return $this->fetch_some($table, $cols, $where, $operator);
            }
        }
    }
    
    /**
     * Fetch rows
     * @param string $table A name of table to get all data
     * @param string $cols the WHERE query part
     */
    public function fetch_rows($table, $cols = '*',$where = false) {
        if(!$where){
            $pre = $this->prepare("SELECT $cols FROM $table");
            
        }else{
          
            $pre = $this->prepare("SELECT $cols FROM $table $where");
        }
        $pre->execute();
        return $pre->fetchAll(PDO::FETCH_OBJ);
      
    }
    
    public function fetch_one_assoc($table,$cols = '*',$where = false) {
        if(!$where){
            $pre = $this->prepare("SELECT $cols FROM $table");
            
        }else{
          
            $pre = $this->prepare("SELECT $cols FROM $table $where");
        }
        $pre->execute();
        return $pre->fetch(PDO::FETCH_ASSOC);
      
    }
    public function fetch_one_object($table,$cols = '*',$where = false) {
        if(!$where){
            $pre = $this->prepare("SELECT $cols FROM $table");
            
        }else{
          
            $pre = $this->prepare("SELECT $cols FROM $table $where");
        }
        $pre->execute();
        return $pre->fetch(PDO::FETCH_OBJ);
      
    }
    
    public function fetch_all_assoc($table,$cols = '*',$where = false) {
        if(!$where){
            $pre = $this->prepare("SELECT $cols FROM $table");
            
        }else{
          
            $pre = $this->prepare("SELECT $cols FROM $table $where");
        }
        $pre->execute();
        return $pre->fetchAll(PDO::FETCH_ASSOC);
      
    }
    public function fetch_all_object($table,$cols = '*',$where = false) {
        if(!$where){
            $pre = $this->prepare("SELECT $cols FROM $table");
            
        }else{
     
            $pre = $this->prepare("SELECT $cols FROM $table $where");
        }
        $pre->execute();
        return $pre->fetchAll(PDO::FETCH_OBJ);
      
    }
    

    /**
     * Fetch type
     * @param string $table A name of table to get all data
     * @param string $where the WHERE query part
     */
    public function fetch_type($table, $type = PDO::FETCH_OBJ, $limit = false,$cols = '*',$where = 1) {
      
        $pre = $this->prepare("SELECT $cols FROM $table $where");
        $pre->execute();
        if(!$limit){
          
        return $pre->fetchAll($type);
        }else{
           return $pre->fetch($type); 
        }
    }
    
    
    public function fetch_sql($sql,$type = PDO::FETCH_OBJ) {
        $pre = $this->prepare($sql);
        $pre->execute();
        return $pre->fetchAll($type);
      }
    
    
    public function delete_row($table,$where,$operator = '=') {
     
        ksort($where);
                $fields = '';
                $count = count($where);
                $i = 0;
                foreach($where as $key=>$val):
                
                   if($i<$count-1){
                   $fields .= $key.' '.$operator.' ? AND ' ;
                   }else{
                       $fields .= $key.' '.$operator.' ?';
                   } $i++;
                endforeach;
              
                $pre = $this->prepare("DELETE FROM $table WHERE $fields");
                foreach ($where as $key => $value):
                     $a[] = $value;
                 endforeach;
                 
            return   $pre->execute($a);
         
       }


       protected function deleteData($table,$where) {
     
        $sql = "DELETE FROM $table $where";
        return  $this->exec($sql);
         
       }

       
	   public function customeDate($date=false) {
		   $date=date_create("$date");
			return date_format($date,"dS-M-Y");
			
       }
       
       public function get_json($table){
        $rows = $this->fetch_all_assoc($table);


        $out = "";
        
        foreach($rows as $row) {
            
            $cols = array_keys($row);
             if ($out != "") {
                $out .= ",";
                }
            foreach($cols as $i=>$col){
           
                if($i==0){
            $out .= '{"'.$col.'":"'  . $row[$col] . '",';
            }
            else{
            $out .= '"'.$col.'":"'  . $row[$col] . '",';
            }	
        
            if($i==count($cols)-1){
            $out .= '"'.$col.'":"'. $row[$col]     . '"}';
        }
            }
        }
        
        $out ='{"records":['.$out.']}';
            
        
        return  $out;
       }
}






class DB extends Model{
	private $api = null;
    protected $table = null;
    protected $select = null;
    protected $where = null;
    protected $order = null;
    protected $group = null;
    protected $limit = null;
    protected $join = '';




    public function __construct(){
        parent::__construct();
    }




    public static function table($name = null){
        $self = new static;
        
        if(!$name){
            info('Pass argument on table `table name` ');
        }
        else{
            $self->table = $name;
        }

        return $self;
   }


   public function setApi($name){
        $this->table = $name;
        $this->api = true;
   }

   /**
    *======================================
    *Insert Function
    *======================================
    */

    public function insert($data){
        return $this->add($this->table,$data);
    }

	public function insertGetId($data){
        if($this->add($this->table,$data)){
			return $this->lastInsertId();
		}
		return false;
    }



   public function select($column = '*'){
       $select = ''; 
      
       if(is_array($column)){
           $count = count($column);
        foreach($column as $i=>$col):
            if($i<$count-1){
                $select .= $col.', ';
            }else{
                $select .= $col;
            }
        endforeach;
        $this->select .= $select.', ';
       }else{
        $this->select .= $column.', ';
        
       }

       
        return $this;
    }

 
    public function where($first, $second, $third = '',$flag=null){
     
        if(!$this->where){
            $this->where = 'WHERE '; 
        }else{
            $this->where .= ' AND '; 
        }

        if($flag != null){
            if($third == ''){
                $this->where .=  $first .' = '. "$second";
            }else{
                $this->where .=  $first .' '. $second .' '. "$third";
            }
         }else{

        if($third == ''){
            $this->where .=  $first .' = '. "'$second'";
        }else{
            $this->where .=  $first .' '. $second .' '. "'$third'";
        }
    }
       return $this;
    }


    public function whereOr($first, $second, $third = ''){
     
        if(!$this->where){
            $this->where = 'WHERE '; 
        }else{
            $this->where .= ' OR '; 
        }

        if($third == ''){
            $this->where .=  $first .' = '. "'$second'";
        }else{
            $this->where .=  $first .' '. $second .' '. "'$third'";
        }
       return $this;
    }



   public function get(){
     
    $this->select = rtrim( $this->select,', ');
    if($this->select == null){
        $this->select = '*';
    }
   
    if(!$this->where)
     $data = $this->fetch_all_object($this->table,$this->select ,$this->join.$this->group.$this->order.$this->limit);
    else   
     $data = $this->fetch_all_object($this->table,$this->select,$this->join.$this->where.$this->group.$this->order.$this->limit); 
    

    return $data;
   }




   public function first(){
    $this->select = rtrim( $this->select,', ');
    if($this->select == null){
        $this->select='*';
    }
    if(!$this->where)
        $data = $this->fetch_one_object($this->table,$this->select ,$this->join.$this->group.$this->order.$this->limit);
    else  
        $data = $this->fetch_one_object($this->table,$this->select,$this->join.$this->where.$this->group.$this->order.$this->limit); 
  
    return $data;
   }


   public function count($column = '*'){
    
    if(!$this->where)
        $data = $this->fetch_one_object($this->table,'COUNT('.$column.') AS total'); 
    else 
        $data = $this->fetch_one_object($this->table,'COUNT('.$column.') AS total',$this->where); 

        return $data->total;
    
   }


   public function limit($offset, $count = null){
     if(!$count){
        $this->limit = " LIMIT 0, $offset ";
     }else{
        $this->limit = " LIMIT  $offset, $count ";
     }
    
    return $this;
   }

   public function orderBy($order, $val = "ASC"){
    
       $this->order = " ORDER BY $order $val ";
    
   
   return $this;
  }


  public function groupBy($column){
    
    $this->group = " GROUP BY $column ";
 

        return $this;
    }


    public function join($table, $first, $second, $third = null){
        if($third == null) 
            $this->join .= ' JOIN '.$table.' ON '. $first .' = ' .$second.' ';
        else
            $this->join .= ' JOIN '.$table.' ON '. $first .' '. $second . ' ' .$third.' ';

        return $this;    
    }


    public function leftJoin($table, $first, $second, $third = null){
        if($third == null) 
            $this->join .= ' LEFT JOIN '.$table.' ON '. $first .' = ' .$second.' ';
        else
            $this->join .= ' LEFT JOIN '.$table.' ON '. $first .' '. $second . ' ' .$third.' ';
            
        return $this;    
    }


    public function rightJoin($table, $first, $second, $third = null){
        if($third == null) 
            $this->join .= ' RIGHT JOIN '.$table.' ON '. $first .' = ' .$second.' ';
        else
            $this->join .= ' RIGHT JOIN '.$table.' ON '. $first .' '. $second . ' ' .$third.' ';
            
        return $this;    
    }

    
    public function fullJoin($table, $first, $second, $third = null){
        if($third == null) 
            $this->join .= ' FULL OUTER JOIN '.$table.' ON '. $first .' = ' .$second.' ';
        else
            $this->join .= ' FULL OUTER JOIN '.$table.' ON '. $first .' '. $second . ' ' .$third.' ';
            
        return $this;    
    }


    public function delete(){
     
        $this->deleteData($this->table,$this->where);
     
    
           
        }



    public function  update($data){
          return  $this->modify($this->table, $data, $this->where);
        }
		
	
	public function create($data){
		return $this->create_table($this->table,$data);
	}	
		


}

