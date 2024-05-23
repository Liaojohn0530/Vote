<?php
require_once('init.php');
$title = '管理員後台';

if(empty($_COOKIE['vote_admin'])){
    header('Location: adminlogin.php');
    die;
}

$stmt = $pdo->prepare("SELECT * FROM event where id =:id");
$stmt->execute(['id' => $_GET['id']]); 
$event = $stmt->fetch();

$now = time();

if($event['isbegin'] && $now >= strtotime($event['starttime']) && $now <= strtotime($event['endtime'])) {
    header('Location: admin.php?id='. $_GET['id'].'&msg=已啟動投票且投票時間已開始，不可刪除');
    die;
}

$sql = "UPDATE event SET isdelete=1 WHERE id=:id";
$data = ['id'=>$_GET['id']];

$result = $pdo->prepare($sql)->execute($data);

if($result)
    header('Location: admin.php?id='.$_GET['id'].'&msg=刪除投票活動成功');
else
    header('Location: admin.php?id='. $_GET['id'].'&msg=刪除投票活動失敗');

?>