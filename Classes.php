<?php 
namespace SB;

class Email{
    public static function send($to,$subject=null,$message=null,$from = null,$cc = null){
if(!$subject)
    $subject = "No Mail subject given.";

if($message)
    $message = 'No message body given.';

$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

if($from)
    $headers .= 'From: <'.$from.'>' . "\r\n";
if($cc)
$headers .= 'Cc: '.$cc. "\r\n";

return mail($to,$subject,$message,$headers);

}
 
}


class Response {
    
    public static function json($req = []){
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET");
        header("Allow: GET, POST, OPTIONS, PUT, DELETE");
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Access-Control-Allow-Origin");
        header('Content-type:Application/json');
        echo json_encode($req);
    }
}

class Session {
    

    public static function init() {
        session_start();
    }

    public static function put($key, $value) {

        $_SESSION[$key] = $value;
    }

     public static function all() {

        if (isset($_SESSION)) {
            return $_SESSION;
        }

        return null;
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

 public static function get($name = false){
       
            if(!$name){
                return $_REQUEST;
            }else{
                return isset($_REQUEST[$name]) ? $_REQUEST[$name] : null;
            }
        

        return null;
    
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


    public static function curlGet($url){

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15); 
        curl_setopt($ch, CURLOPT_HEADER, 0);
        
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }


    public static function curlPost($url,$data){

       if(!is_array($data) && 'object'== gettype($data))
            $data = json_decode(json_encode($data),1);


        $params = '';
    foreach($data as $key=>$value):
                $params .= $key.'='.$value.'&';
    endforeach;            
         
    $params = rtrim($params, '&');
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15); 
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, count($data)); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params); 
  
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}




class Password{

    private $privateKey;
    private $salt;

  function __construct() {
    $this->privateKey = ENCRYPT_PRIVATE_KEY??'DEMOPVTKEY';
    $this->salt = ENCRYPT_SALT??'DEMOSALT';
  }


    public function encrypt($string,$salt=""){

        $encryptMethod  = "AES-256-CBC";
        
        $key     = hash('sha256', $this->privateKey);
        $ivalue  = substr(hash('sha256', $this->salt.$salt), 0, 16); // sha256 is hash_hmac_algo
        $result      = openssl_encrypt($string, $encryptMethod, $key, 0, $ivalue);
        return $output = base64_encode($result);  // output is a encripted value
    }

    public function decrypt($stringEncrypt,$salt=""){
        
            $encryptMethod  = "AES-256-CBC";

            $key    = hash('sha256', $this->privateKey);
            $ivalue = substr(hash('sha256', $this->salt.$salt), 0, 16); // sha256 is hash_hmac_algo
            return $output = openssl_decrypt(base64_decode($stringEncrypt), $encryptMethod, $key, 0, $ivalue);
        
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


// modelDB class start


class Model{
    public $pdo = null;    
    public function __construct($db='LOCAL') {
        $this->pdo = createDBConnection();
    }

    public function closeDBConnection(){
        $this->pdo = null;
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

        $res =  $this->pdo->exec($sql);
        $this->closeDBConnection();
        return $res;
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
        $sth = $this->pdo->prepare("INSERT INTO `$table` (`$fieldNames`) VALUES ($fieldValues)");

        foreach ($data as $key => $value) {
            $sth->bindValue(":$key", $value);
        }

         $res = $sth->execute();
        $this->closeDBConnection();
        return $res;
    }

    /**
     * update
     * @param string $table A name of table to insert into
     * @param string $data An associative array
     * @param string $where the WHERE query part
     */
    public function modify($table, $data,$where,$whereData=array()) {
        ksort($data);

        $fieldDetails = NULL;
        foreach ($data as $key => $value) {
            $fieldDetails .= "`$key`=:$key,";
        }

        $fieldDetails = rtrim($fieldDetails, ',');

        $sth = $this->pdo->prepare("UPDATE `$table` SET $fieldDetails  $where");

        foreach ($data as $key => $value) {
            $sth->bindValue(":$key", $value);
        }

        foreach ($whereData as $k => $val) {
            $sth->bindValue(":$k", $val);
        }

        $res = $sth->execute();
        $this->closeDBConnection();
        return $res;
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
              
                $pre = $this->pdo->prepare("SELECT $cols FROM $table WHERE $fields");
                foreach ($where as $key => $value):
                    
                     $pre->bindValue(":$key", $value);
                 endforeach;
                 $pre->execute();
                 return $pre->fetch(\PDO::FETCH_ASSOC); 
         
    }
    
    
    /**
     * Fetch row
     * @param string $table A name of table to get all data
     * @param string $cols the WHERE query part
     */
    public function fetch_row($table, $cols = '*', $where = false, $operator = '=') {
        if(!$where){
            
        $pre = $this->pdo->prepare("SELECT $cols FROM $table");
        $pre->execute();
        return $pre->fetch(\PDO::FETCH_ASSOC); 
        }else{
            if(!is_array($where)){
                
               $pre = $this->pdo->prepare("SELECT $cols FROM $table $where");
               $pre->execute();
               return $pre->fetch(\PDO::FETCH_ASSOC);  
            }else{
                
               return $this->pdo->fetch_some($table, $cols, $where, $operator);
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
            $pre = $this->pdo->prepare("SELECT $cols FROM $table");
            
        }else{
          
            $pre = $this->pdo->prepare("SELECT $cols FROM $table $where");
        }
        $pre->execute();
        return $pre->fetchAll(\PDO::FETCH_OBJ);
      
    }
    
    public function fetch_one_assoc($table,$cols = '*',$where = false,$whereData = array()) {
        if(!$where){
            $pre = $this->pdo->prepare("SELECT $cols FROM $table");
            
        }else{
          
            $pre = $this->pdo->prepare("SELECT $cols FROM $table $where");
            foreach ($whereData as $key => $value):
                     $pre->bindValue(":$key", $value);
            endforeach;
        }
        $pre->execute();
    

        $data = $pre->fetch(\PDO::FETCH_ASSOC);
        $this->closeDBConnection();
        return $data;
      
    }





    public function fetch_one_object($table,$cols = '*',$where = false,$whereData=array()) {
        if(!$where){
            $pre = $this->pdo->prepare("SELECT $cols FROM $table");
            
        }else{
            $pre = $this->pdo->prepare("SELECT $cols FROM $table $where");
            foreach ($whereData as $key => $value):
                     $pre->bindValue(":$key", $value);
            endforeach;
        }
        $pre->execute();

        $data = $pre->fetch(\PDO::FETCH_OBJ);
        $this->closeDBConnection();
        return $data;
      
    }


    
    public function fetch_all_assoc($table,$cols = '*',$where = false,$whereData=array()) {
        if(!$where){
            $pre = $this->pdo->prepare("SELECT $cols FROM $table");
            
        }else{
          
            $pre = $this->pdo->prepare("SELECT $cols FROM $table $where");
            foreach ($whereData as $key => $value):
                     $pre->bindValue(":$key", $value);
            endforeach;
        }
        $pre->execute();
        $data = $pre->fetchAll(\PDO::FETCH_ASSOC);
        $this->closeDBConnection();
        return $data;
      
    }
    public function fetch_all_object($table,$cols = '*',$where = false,$whereData=array()) {
        if(!$where){
            $pre = $this->pdo->prepare("SELECT $cols FROM $table");
            
        }else{
     
            $pre = $this->pdo->prepare("SELECT $cols FROM $table $where");
            foreach ($whereData as $key => $value):

                if(is_int($value))
                    $param = PDO::PARAM_INT;
                elseif(is_bool($value))
                    $param = PDO::PARAM_BOOL;
                elseif(is_null($value))
                    $param = PDO::PARAM_NULL;
                elseif(is_string($value))
                    $param = PDO::PARAM_STR;
                else
                    $param = FALSE;
                    
                if($param)
                    $pre->bindValue(":$key",$value,$param);
                else
                    $pre->bindValue(":$key",$value);
            endforeach;
        }
        $pre->execute();
        
        $data =  $pre->fetchAll(\PDO::FETCH_OBJ);

        $this->closeDBConnection();
        return $data;
    }
    

    /**
     * Fetch type
     * @param string $table A name of table to get all data
     * @param string $where the WHERE query part
     */
    public function fetch_type($table, $type = \PDO::FETCH_OBJ, $limit = false,$cols = '*',$where = 1) {
      
        $pre = $this->pdo->prepare("SELECT $cols FROM $table $where");
        $pre->execute();
        if(!$limit){
          
        return $pre->fetchAll($type);
        }else{
           return $pre->fetch($type); 
        }
    }
    
    
    public function fetch_sql($sql,$type = \PDO::FETCH_OBJ) {
        $pre = $this->pdo->prepare($sql);
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
              
                $pre = $this->pdo->prepare("DELETE FROM $table WHERE $fields");
                foreach ($where as $key => $value):
                     $a[] = $value;
                 endforeach;
                 
           $res = $pre->execute($a);
            $this->closeDBConnection();
        return $res;
         
       }


       protected function deleteData($table,$where) {
     
        $sql = "DELETE FROM $table $where";
        return  $this->pdo->exec($sql);
         
       }

       
       public function customeDate($date=false) {
           $date=date_create("$date");
            return date_format($date,"dS-M-Y");
            
       }
       
       public function get_json($table){
        $rows = $this->pdo->fetch_all_assoc($table);


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



        public function get_sql_qry($table,$cols = '*',$where = false){
            return "SELECT $cols FROM $table $where";
        }

}








class DB extends Model{
    private $api = null;
    protected $table = null;
    protected $select = null;
    protected $where = null;
    protected $whereData = [];
    protected $whereOrLike = null;
    protected $order = null;
    protected $group = null;
    protected $limit = null;
    protected $join = '';





    public function __construct($db='LOCAL'){
        parent::__construct($db);
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
            return $this->pdo->lastInsertId();
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

        $key = str_replace('.', '_', $first);
       
            if($third == ''){
                $this->where .=  $first .' = '. ":$key";
                $this->whereData[$key] = $second;
            }else{
                $this->where .=  $first .' '. $second .' '. ":$key";
                
                $this->whereData[$key] = $third;
            }
        
       return $this;
    }

    public function whereIn($first, $second = array()){
        
        $value = "";
            $cnt = count($second);
            foreach ($second as $key => $sec) {
                if(is_numeric($sec))
                    $value .=  $sec;
                else
                    $value .=  "'".$sec."'";

                if($key < $cnt-1){
                    $value .=",";
                }
            }


        if(!$this->where){
            $this->where = 'WHERE '; 
        }else{
            $this->where .= ' AND '; 
        }

  
        $this->where .=  $first ." IN($value)";
        

       return $this;
    }





    public function whereOr($first, $second, $third = ''){
     
        if(!$this->where){
            $this->where = 'WHERE '; 
        }else{
            $this->where .= ' OR '; 
        }

        $key = str_replace('.', '_', $first);

        if($third == ''){
                $this->where .=  $first .' = '. ":$key";
                $this->whereData[$key] = $second;
            }else{
                $this->where .=  $first .' '. $second .' '. ":$key";
                
                $this->whereData[$key] = $third;
        }
       return $this;
    } 

    public function whereLike($col, $val){
     
        if(!$this->whereOrLike){
            $this->whereOrLike = ' AND ('; 
        }else{
            $this->whereOrLike .= ' OR '; 
        }

        $key = str_replace('.', '_', $col);
       
        $this->whereOrLike .=  $col .' LIKE '. ":$key";
        $this->whereData[$key] = $val;
        
       return $this;
    }



   public function get($type = 'obj'){
     if($this->select != "" && $this->select != null)
        $this->select = rtrim( $this->select,', ');

    if($this->select == null){
        $this->select = '*';
    }

    if($this->whereOrLike != null && $this->where != null){
        $this->whereOrLike .= ') '; 
        $this->where .= $this->whereOrLike;
    }

    if($type == 'obj'):
   
        if(!$this->where)
         $data = $this->fetch_all_object($this->table,$this->select ,$this->join.$this->group.$this->order.$this->limit);
        else   
         $data = $this->fetch_all_object($this->table,$this->select,$this->join.$this->where.$this->group.$this->order.$this->limit,$this->whereData); 
        
    else:

        if(!$this->where)
         $data = $this->fetch_all_assoc($this->table,$this->select ,$this->join.$this->group.$this->order.$this->limit);
        else   
         $data = $this->fetch_all_assoc($this->table,$this->select,$this->join.$this->where.$this->group.$this->order.$this->limit,$this->whereData); 

    endif;

    return $data;
   }


   public function getSql($flag = false)
    {
        $cols = $this->select;
        $table = $this->table;
        $whr2 = $this->whereOrLike != null ? $this->whereOrLike.')':'';
         $whr =  $this->where . $whr2;
        
        $where = $this->join . $whr . $this->group . $this->order . $this->limit;
        $cols = rtrim($cols, ', ');

        if($cols == null){
            $cols = '*';
        }

        if($flag){
            foreach ($this->whereData as $key => $value) {
                $val = is_numeric($value) ? $value : "'$value'";
               $where = str_replace(":$key", $val, $where);
            }
        }

    
        return "SELECT $cols FROM $table $where";
    }

   

  
   public function sql_qry_get($column = '*'){
    
        return  $this->get_sql_qry($this->table,$this->select,$this->join.$this->where.$this->group.$this->order.$this->limit); 
   }
   
   public function fetch_by_sql($sql){
        $data = $this->fetch_sql($sql); 
        return count($data) == 1 ? $data[0] : $data;
   }
   
   

   public function first($type='obj'){
      if($this->select != "" && $this->select != null)
            $this->select = rtrim( $this->select,', ');

    if($this->select == null){
        $this->select='*';
    }

    if($type == 'obj'):
        if(!$this->where)
            $data = $this->fetch_one_object($this->table,$this->select ,$this->join.$this->group.$this->order.$this->limit);
        else  
            $data = $this->fetch_one_object($this->table,$this->select,$this->join.$this->where.$this->group.$this->order.$this->limit,$this->whereData);

     else:
             if(!$this->where)
            $data = $this->fetch_one_assoc($this->table,$this->select ,$this->join.$this->group.$this->order.$this->limit);
        else  
            $data = $this->fetch_one_assoc($this->table,$this->select,$this->join.$this->where.$this->group.$this->order.$this->limit,$this->whereData);
     endif;
  
    return $data;
   }



   public function count($column = '*'){
    $whr = '';
    if($this->whereOrLike != null && $this->where != null){
            
            $whr = $this->where . $this->whereOrLike. ') ';
    }else if ($this->where != null) {
        $whr = $whr = $this->where;
    }
    
    if(!$this->where)
        $data = $this->fetch_one_object($this->table,'COUNT('.$column.') AS total'); 
    else 
        $data = $this->fetch_one_object($this->table,'COUNT('.$column.') AS total',$this->join.$whr.$this->group.$this->order.$this->limit,$this->whereData); 

        return $data->total;
    
   }


    public function sum($column=null){
        if(!$column)
            return 0;


    $whr = '';
    if($this->whereOrLike != null && $this->where != null){
            
            $whr = $this->where . $this->whereOrLike. ') ';
    }else if ($this->where != null) {
        $whr = $whr = $this->where;
    }
    
    if(!$this->where)
        $data = $this->fetch_one_object($this->table,'SUM('.$column.') AS total'); 
    else 
        $data = $this->fetch_one_object($this->table,'SUM('.$column.') AS total',$this->join.$whr.$this->group.$this->order.$this->limit,$this->whereData); 

        return $data->total;
    
   }

   public function countGroup($column = '*'){
    
    if(!$this->where)
        $data = $this->fetch_all_object($this->table,'COUNT('.$column.') AS total'); 
    else 
        $data = $this->fetch_all_object($this->table,'COUNT('.$column.') AS total',$this->join.$this->where.$this->group.$this->order.$this->limit,$this->whereData); 

        return count($data);
    
   }


   public function sql_qry($column = '*'){
    
        return  $this->get_sql_qry($this->table,'COUNT('.$column.') AS total',$this->join.$this->where.$this->group.$this->order.$this->limit); 

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


    public function join($table, $first, $second, $third = null, $fourth=null){
        if($third == null and $fourth == null) 
            $this->join .= ' JOIN '.$table.' ON '. $first .' = ' .$second.' ';
        else if($fourth == null)
            $this->join .= ' JOIN '.$table.' ON '. $first .' '. $second . ' ' .$third.' ';
        else
            $this->join .= ' JOIN '.$table.' ON '. $first .' = '. $second . ' AND ' .$third.' = '.$fourth.' ';
        return $this;   
    }


    public function leftJoin($table, $first, $second, $third = null, $fourth=null){
        if($third == null and $fourth == null) 
            $this->join .= ' LEFT JOIN '.$table.' ON '. $first .' = ' .$second.' ';
        else if($fourth == null)
            $this->join .= ' LEFT JOIN '.$table.' ON '. $first .' '. $second . ' ' .$third.' ';
        else
            $this->join .= ' LEFT JOIN '.$table.' ON '. $first .' = '. $second . ' AND ' .$third.' = '.$fourth.' ';
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
        return  $this->modify($this->table, $data, $this->where,$this->whereData);
    }
        
    
    public function create($data){
        return $this->create_table($this->table,$data);
    }   
        


}





class Router{
    private $url = "";
    private $routes = array();
    private $methods = array();
    private $request_methods = array();
    function __construct(){

        // $url = $_SERVER['REQUEST_URI'];
        $route = isset($_REQUEST['route']) ? $_REQUEST['route'] : "" ;
          $this->url = '/'.$route;
       /* $d = explode('index.php', $url);
        if(count($d)>1)
            $this->url = end($d);

        if(strpos($this->url,'?') >=0){
            $exd = explode('?', $this->url);
            $this->url = current($exd);
        }*/
    }


    function get($url, $run){
        

        array_push($this->routes, $url);
        array_push($this->methods, $run);
        array_push($this->request_methods,'GET');
        
    }

    function post($url, $run){
        array_push($this->routes, $url);
        array_push($this->methods, $run);
        array_push($this->request_methods,'POST');
         
    }

    

    public function setup(){
        if(!in_array($this->url, $this->routes)){
              Response::json(array('status'=>false,'message'=>'Route not found!','code'=>404,'data'=>array()));
              exit;
            }else{
                foreach($this->methods as $key => $value) {
                    if($this->url == $this->routes[$key]){
                        if($_SERVER["REQUEST_METHOD"] != $this->request_methods[$key]){
                            $res = array('status'=>false,'message'=>'405 Method Not Allowed!','code'=>405,'data'=>array());
                            Response::json($res);
                            break;
                        }

                            
                        if(is_callable($value)){
                            Response::json($value());
                                break;
                        }else{
                            $exCls = explode('@', $value);
                            $obj = new $exCls[0];
                            $req = $_POST;
                            if(empty($req))
                                $req = json_decode(file_get_contents('php://input'), true);
                            $res = $obj->{$exCls[1]}($req);
                            Response::json($res);
                        }               
                    }
                            

                }
            }
    }
}


