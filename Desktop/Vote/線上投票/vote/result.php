<?php
require_once('init.php');
$title = '司法院線上投票';

if(empty($_GET['id'])){
    die;
}
if(empty($_COOKIE['vote_admin'])){
    header('Location: adminlogin.php');
}
$stmt = $pdo->prepare("SELECT * FROM event where id =:id");
$stmt->execute(['id' => $_GET['id']]); 
$event = $stmt->fetch();

$now = time();

$stmt = $pdo->prepare("SELECT * FROM vote where event_id=:event_id");
$stmt->execute(['event_id' => $_GET['id']]); 
$votes = $stmt->fetchall();

$stmt = $pdo->prepare("SELECT count(*) AS isvoted FROM vote where isvoted=1 AND event_id=:event_id");
$stmt->execute(['event_id' => $_GET['id']]); 
$isvoted = $stmt->fetch();

$stmt = $pdo->prepare("SELECT * FROM item where event_id=:event_id AND isdelete=0 ORDER BY votes DESC");
$stmt->execute(['event_id' => $_GET['id']]); 
$items = $stmt->fetchall();

$dv = [];
foreach($votes as $v){
    $v['dept'] = trim($v['dept']);
    if(!array_key_exists($v['dept'], $dv)){
        $dv[$v['dept']] = [0,0]; //已投, 總共
    }
    $dv[$v['dept']][1]++;
    if($v['isvoted'] == 1)
    $dv[$v['dept']][0]++;
}
//var_dump($dv);
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
            <div class='col-xs-12'>
                <h3 class='text-center'><?= $event['name'] ?>-投票結果</h3>
                <h3 class='text-center'><span>投票期間：<?= substr($event['starttime'],0,-3) ?> ~ <?= substr($event['endtime'],0,-3) ?></span></h3>
                
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>編號</th>
                            <th>姓名</th>
                            <th>職稱</th>
                            <th>單位</th>
                            <th>得票數</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php 
                    if($now >= strtotime($event['endtime'])){
                        foreach($items as $i){ 
                        
                    ?>
                        <tr>
                            <td><?= $i['no'] ?></td>
                            <td><?= $i['name'] ?></td>
                            <td><?= $i['title'] ?></td>
                            <td><?= $i['dept'] ?></td>
                            <td><?= $i['votes'] ?></td>
                        </tr>
                    <?php }}else{ ?>
                        <tr><td class='text-center' colspan='5'>投票時間未結束，不可查看結果</td></tr>

                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
        <div class='col-xs-4'>
            
                <h4 class='text-center'>可投票人數：<?= count($votes) ?></h4>
                <h4 class='text-center'>已投票人數： <?= $isvoted['isvoted'] ?></h4>
                <h4 class='text-center'>投票率：<?= round(100*$isvoted['isvoted']/count($votes),2) ?>%</h4>
            </div>
            <div class='col-xs-8'>
            <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>單位</th>
                            <th>可投票人數</th>
                            <th>已投票人數</th>
                            
                            <th>投票率</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($dv as $k=>$v){ ?>
                        <tr>
                            <td><?= $k ?></td>
                            <td><?= $v[1] ?></td>
                            <td><?= $v[0] ?></td>
                            
                            <td><?= round(100*$v[0]/$v[1],2) ?>%</td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
            
        </div>
    </div>
    <?php include '_footer.php'; ?>
    <?php include '_js.php'; ?>
</body>
</html>