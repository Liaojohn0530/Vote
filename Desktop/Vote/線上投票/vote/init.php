<?php
require_once('Config.php');
$pdo = new PDO('mysql:host='.Config::host.';dbname='.Config::db.';', Config::user, Config::pwd);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


//機要人員姓名
$keynames = ["BB","CC","陳姿穎","郭柏鴻","何友倫"];

//登入驗證
$nologin = ['login','adminlogin'];
function prefixwebroot(&$item, $key){
    $item =  Config::webroot . $item . '.php';
}
array_walk($nologin, 'prefixwebroot');
//var_dump($_SERVER['PHP_SELF']);
$islogin = false;
$isadmin = false;
if(false && !in_array($_SERVER['PHP_SELF'], $nologin)){//如果需要登入
    
    if(empty($_COOKIE['vote_login'])){
        header('Location: login.php');
        exit;
    } else{
        $islogin = true;
        if(empty($_COOKIE['vote_admin'])){
            $isadmin = true;
        }
    }
}


//full url
function furl($s, $use_forwarded_host = false) {
    $ssl      = ( ! empty( $s['HTTPS'] ) && $s['HTTPS'] == 'on' );
    $sp       = strtolower( $s['SERVER_PROTOCOL'] );
    $protocol = substr( $sp, 0, strpos( $sp, '/' ) ) . ( ( $ssl ) ? 's' : '' );
    $port     = $s['SERVER_PORT'];
    $port     = ( ( ! $ssl && $port=='80' ) || ( $ssl && $port=='443' ) ) ? '' : ':'.$port;
    $host     = ( $use_forwarded_host && isset( $s['HTTP_X_FORWARDED_HOST'] ) ) ? $s['HTTP_X_FORWARDED_HOST'] : ( isset( $s['HTTP_HOST'] ) ? $s['HTTP_HOST'] : null );
    $host     = isset( $host ) ? $host : $s['SERVER_NAME'] . $port;
    return $protocol . '://' . $host . $s['REQUEST_URI'];
}
$url = furl($_SERVER);