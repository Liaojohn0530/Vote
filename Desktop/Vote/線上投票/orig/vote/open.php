<?php
require_once('init.php');
$title = '管理員後台';

if(empty($_COOKIE['vote_admin'])){
    header('Location: adminlogin.php');
}

$sql = "UPDATE event SET isbegin=1 WHERE id=:id";
$data = ['id'=>$_GET['id']];

$result = $pdo->prepare($sql)->execute($data);

if($result)
    header('Location: admin.php?id='.$_GET['id'].'&msg=啟動投票成功');
else
    header('Location: admin.php?id='. $_GET['id'].'&msg=啟動投票失敗');

?>