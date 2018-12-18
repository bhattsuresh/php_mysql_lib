<?php

    /* app name*/
    
    define('APP_NAME','YOUR APP NAME');
   
    /* Base url*/
	
   define('APP_URL','http://localhost/');
    
    /* local Time zone */
    
     define('TIME_ZONE','Asia/Calcutta');
    
	/* App Host */

	define('DB_HOST','localhost');
    
   define('DB_NAME','DBNAME');
	
    define('DB_USER','USERNAME');

    define('DB_PASS','PASSWORD');
	

    define('DB_DRIVER','mysql');
	
	
	
	
	
	
	
	
	

	$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS,DB_NAME);
	if (!$conn) {
		die("Connection failed: " . mysqli_connect_error());
	}
	
	function getValue($data){
		global $conn;
		return mysqli_real_escape_string($conn, $data);
	}
	
	
	
	
	
	
	
	
	
/**
* do not change following
*/
define('DSN',DB_DRIVER.':host='.DB_HOST.';dbname='.DB_NAME);
    
date_default_timezone_set(TIME_ZONE);

// some default functions...

function dd($array = null){
    if($array == null){
        die; 
    }
   echo '<pre style="color:red;">';
   print_r($array);
   die;
}

function d($array){
    echo '<pre style="color:green;">';
    print_r($array);
    
 }
 
 function url($file = null){
    
    return APP_URL.$file;
}

function redirect($path=false){
    if(!$path)
        header('location:'.url());
    else
        header('location:'.url($path)); 
		
	exit;
}

function cdate(){
    return date('Y-m-d');
}



function ctime(){
    return date('g:i:s');
}

function info($info = null){
   if(is_array($info)){
    echo '<div style="border:1px solid orange;padding:20px;font-size:12px;"><pre>' ;
    print_r($info); 
    echo '</pre></div>' ;

   }else if(!$info)
       echo '<div style="border:1px solid orange;padding:20px;font-size:25px;">Route or View not found!</div>';
    else
        echo '<div style="border:1px solid orange;padding:20px;font-size:25px;"><pre>'.$info.'</pre></div>';

    die;
}


require_once __DIR__.'/Classes.php';
Session::init();
