﻿<?php
require_once('init.php');
$title = '司法院線上投票';

if(empty($_GET['id'])){
    die;
}
$stmt = $pdo->prepare("SELECT * FROM event where id =:id");
$stmt->execute(['id' => $_GET['id']]); 
$event = $stmt->fetch();

date_default_timezone_set("Asia/Taipei");
//echo date_default_timezone_get() ."<br>";

$now = time();
//echo $now ."<br>";
//echo strtotime($event['endtime']);
if($now >= strtotime($event['endtime'])){
    echo "<h1 class='text-center'>投票時間已結束</h1>";
    die;
}
if($now < strtotime($event['starttime'])){
    echo "<h1 class='text-center'>投票時間尚未開始</h1>";
    die;
}

if(empty($_COOKIE['vote_user']) || $_COOKIE['vote_user'] != $_GET['id']){
    header('Location: login.php?id='. $_GET['id']);
    die;
}

if(!$event['isbegin']) {
    echo '<h1>未啟動投票</h1>';
    die;
}


$stmt = $pdo->prepare("SELECT * FROM vote where id =:id AND event_id=:event_id");
$stmt->execute(['id' => $_COOKIE['vote_id'],'event_id' => $_GET['id']]); 
$vote = $stmt->fetch();

$stmt = $pdo->prepare("SELECT * FROM item where event_id=:event_id AND isdelete=0 ORDER BY no");
$stmt->execute(['event_id' => $_GET['id']]); 
$items = $stmt->fetchall();

if(!$vote){
    header('Location: login.php?id='. $_GET['id']);
    die;
}
if($vote['isvoted'] == 1){
    header('Location: login.php?id='. $_GET['id'].'&msgjs='.urlencode('您已完成投票，無法重複投票'));
    die;
}

if(!empty($_POST['item'])){
    $stmt1 = $pdo->prepare("SELECT * FROM vote where id =:id AND event_id=:event_id");
    $stmt1->execute(['id' => $_COOKIE['vote_id'],'event_id' => $_GET['id']]); 
    $vote1 = $stmt1->fetch();
    if($vote1['isvoted'] == 1){
        header('Location: login.php?id='. $_GET['id'].'&msgjs='.urlencode('您已完成投票，無法重複投票'));
        die;
    }

	if (is_array($_POST["item"]) and count($_POST["item"]) > 2){
		header('Location: login.php?id='. $_GET['id'].'&msgjs='.urlencode('錯誤，投票數異常'));
        die;
	}
    $pdo->beginTransaction();

    $sql = "UPDATE vote SET isvoted=1 WHERE event_id=:event_id AND id=:id";
    $data = ['event_id'=>$_GET['id'], 'id'=>$_COOKIE['vote_id']];
	$result = $pdo->prepare($sql)->execute($data);
	
	//file_put_contents('php://stderr', print_r($_POST['item'], TRUE));
	if ($event["ismulti"]){
		foreach ($_POST["item"] as $itemid){
			echo "<script>console.log(".$itemid.")</script>";
			$sql2 = "UPDATE item SET votes = votes+1 WHERE id=:id";
			$data2 = ['id'=>$itemid];
			
			$result2 = $pdo->prepare($sql2)->execute($data2);
		}
	}
	else{
		$sql2 = "UPDATE item SET votes = votes+1 WHERE id=:id";
		$data2 = ['id'=>$_POST['item']];

		$result2 = $pdo->prepare($sql2)->execute($data2);
	}

    $comm = $pdo->commit();

    if($comm)
        header('Location: finish.php?id='.$_GET['id']);
    else
        header('Location: index.php?id='. $_GET['id'].'&msg='.urlencode('投票失敗'));
}
?>

<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
    <?php include '_head.php'; ?>
    <style>
        table tr th,table tr td {
            text-align:center;
        }
        table{
            /*font-size:1.4em; !important*/
            font-size: 30px;

        }
        .btn{
            font-size:22px; !important
            
        }
    </style>
</head>
<body>
    <?php include '_nav.php'; ?>
    <div class="container-fluid">
        <div class="row">
            <div class='col-xs-8 col-xs-offset-2'>
                <h1 class='text-center'><?= $event['name'] ?>-候選人名冊</h1>
               
                <?php if($now < strtotime($event['starttime'])){ ?>
                    <h3 class='text-center'>投票時間尚未開始</h3>
                <?php } else if($now >= strtotime($event['endtime'])){ ?>
                    <h3 class='text-center'>投票時間已結束</h3>

                <?php } else{ ?>

                    <form method="post">
                    <table class='table'>
                    <tr>
                        <th style='width:70px;'>圈選</th>
                        <th style='width:70px;'>編號</th>
                        <th>姓名</th>
                        <th>職稱</th>
                        <th>單位</th>
                        <th>備註</th>
                    </tr>
                    <?php foreach($items as $i){ ?>
                        <tr>
                            <td>
                                <label class='text-center' for='r<?= $i['id'] ?>' style='display:block;margin:0'>
                                <!--<input style='-ms-transform:scale(2);-webkit-transform:scale(2);transform:scale(2);' type='radio' name='item' id="r<?= $i['id'] ?>" value="<?= $i['id'] ?>" required>-->
								<?php 
									if ($event["ismulti"]){
										echo "<input class='multi_checkbox' style='-ms-transform:scale(2);-webkit-transform:scale(2);transform:scale(2);' type='checkbox' name='item[]' id='r".$i['id']."' value=".$i['id']." onclick='set_checkbox()'>";
									}
									else{
										echo "<input style='-ms-transform:scale(2);-webkit-transform:scale(2);transform:scale(2);' type='radio' name='item' id='r".$i['id']."' value=".$i['id']."required>";
									}
									echo "</label>";
								?>
                            </td>
                            <td><?= $i['no'] ?></td>
                            <td class='bold' ><?= $i['name'] ?></td>
                            <td><?= $i['title'] ?></td>
                            <td><?= $i['dept'] ?></td>
                            <td><?= $i['comment'] ?></td>
                        </tr>
                    <?php } ?>
                    </table>
					<?php if ($event["ismulti"]){ ?>
						<button type="submit" onclick="return show_comfirm();" class="btn btn-block btn-success">確認投票</button>
					<?php } else { ?>
						<button type="submit" onclick="return confirm('如點選「確認投票」鍵後，就不能再更改圈選結果，請確認後再點選')" class="btn btn-block btn-success">確認投票</button>
					<?php } ?>
                  </form>
                <?php } ?>
                
            </div>
            
        </div>
    </div>
    <?php include '_footer.php'; ?>
    <?php include '_js.php'; ?>
</body>
</html>