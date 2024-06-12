<?php
require_once('Config.php');
$pdo = new PDO('mysql:host='.Config::host.';dbname='.Config::db.';', Config::user, Config::pwd);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


//檢查已結束的投票活動，將投票人的機敏資料刪除(身分證,生日)
$sql = "UPDATE vote set account = '投票活動結束，清除機敏資料', pswd = '投票活動結束，清除機敏資料' WHERE event_id in (select id from event where endtime < NOW())";
$result = $pdo->prepare($sql)->execute();

//機要人員姓名
$keynames = ["BB","CC","莊繡霞","郭岱純","何友倫"];

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