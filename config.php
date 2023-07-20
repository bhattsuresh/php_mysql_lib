<?php
define('APP_URL','');
define('ENCRYPT_PRIVATE_KEY',"KEY");
define('ENCRYPT_SALT',"DEMO");


function createDBConnection(){
    return new PDO('mysql:host=localhost;dbname=p2p', 'root', "");
}



function dd($array = null)
{
    if ($array == null) {
        die;
    }
    echo '<pre style="color:red;">';
    print_r($array);
    die;
}

function d($array)
{
    echo '<pre style="color:green;">';
    print_r($array);
}

function url($file = null)
{

    return APP_URL . $file;
}

function redirect($path = false)
{
    if (!$path)
        header('location:' . url());
    else
        header('location:' . url($path));

    exit;
}

function cdate()
{
    return date('Y-m-d');
}



function ctime()
{
    return date('g:i:s');
}

function info($info = null)
{
    if (is_array($info)) {
        echo '<div style="border:1px solid orange;padding:20px;font-size:12px;"><pre>';
        print_r($info);
        echo '</pre></div>';
    } else if (!$info)
        echo '<div style="border:1px solid orange;padding:20px;font-size:25px;">Route or View not found!</div>';
    else
        echo '<div style="border:1px solid orange;padding:20px;font-size:25px;"><pre>' . $info . '</pre></div>';

    die;
}


require_once __DIR__ . '/Classes.php';


SB\Session::init();


