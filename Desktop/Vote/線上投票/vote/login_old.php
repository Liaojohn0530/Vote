<?php
require_once('init.php');
$title = '登入';

$exp = time() + Config::cookie_exp;

//有傳入ticket, 登入
if(!empty($_GET['TICKET'])){
    $url = Config::adurl . $_GET['TICKET'];
    $xml = simplexml_load_string(file_get_contents($url));
    //var_dump($xml); //$xml->id
    
    if(empty($xml->id)){
        header('Location: login.php?msg=單一登入失敗：ID為空');
    } elseif($xml->SysId != Config::SysId){
        header('Location: login.php?msg=單一登入失敗：系統代號不同');
    } elseif(!array_key_exists((string)$xml->court, $crtmap)){
        header('Location: login.php?msg=單一登入失敗：法院代號不存在於資料庫');
    }
    setcookie("id", $xml->id, $exp);
    setcookie("name", $xml->name, $exp);
    setcookie("court", $xml->court, $exp);
    setcookie("title", $xml->title, $exp);
    setcookie("department", $xml->department, $exp);
    setcookie("company", $xml->company, $exp);
    setcookie("court_name", $crtmap[(string)$xml->court], $exp);

    header('Location: index.php');
}
//測試登入用
if(!empty($_GET['court']) && array_key_exists($_GET['court'], $crtmap)){
    
    $court_name = $crtmap[$_GET['court']];
    setcookie("id", $_GET['court'].'test', $exp);
    setcookie("name", $court_name.'測試人員', $exp);
    setcookie("court", $_GET['court'], $exp);
    setcookie("title", '科長', $exp);
    setcookie("department", '刑事廳', $exp);
    setcookie("company", $court_name, $exp);
    setcookie("court_name", $court_name, $exp);

    header('Location: index.php');
}
?>
<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
    <?php include '_head.php'; ?>
</head>
<body>
    <?php include '_nav.php'; ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-8 col-xs-offset-2">
                <h3 class="text-center">
                    <a href='http://webad.intraj/ssoa/getTicket.aspx?sysid=criminalpay' class='btn btn-lg btn-block btn-success'>您已登出，點擊單一登入</a>
                </h3>
            </div>
            <div class="row">
                <div class="col-xs-4">
                    <h3 class="text-center">
                        <a href='login.php?court=TPD' class='btn btn-lg btn-block btn-info'>測試登入-地院</a>
                    </h3>
                </div>
                <div class="col-xs-4">
                    <h3 class="text-center">
                        <a href='login.php?court=TPH' class='btn btn-lg btn-block btn-warning'>測試登入-高院</a>
                    </h3>
                </div>
                <div class="col-xs-4">
                    <h3 class="text-center">
                        <a href='login.php?court=TPJ' class='btn btn-lg btn-block btn-primary'>測試登入-司法院</a>
                    </h3>
                </div>
            </div>
        </div>
    </div>
    <?php include '_footer.php'; ?>
    <?php include '_js.php'; ?>
</body>
</html>