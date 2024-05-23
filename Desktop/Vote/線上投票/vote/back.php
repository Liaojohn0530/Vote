<?php
require_once('init.php');

if(empty($_GET['id']) || !isset($_GET['progress'])){
    header('Location: '.$_GET['url'].'&msg=id或progress為空');
    exit;
}
$p = $_GET['progress'];
$sql = "UPDATE Criminalpay SET {@back}={back},{@back_info}='{back_info}' WHERE ID={id}";

$replace = ['back'.$p, 1, 'back'.$p.'_info', $_GET['info'], $_GET['id']];
$sql = str_replace(['{@back}','{back}','{@back_info}','{back_info}','{id}'], $replace, $sql);

$result = $pdo->prepare($sql)->execute();

if($result)
    header('Location: '.$_GET['url'].'&msg=退回成功');
else
    header('Location: '.$_GET['url'].'&msg=退回失敗');
