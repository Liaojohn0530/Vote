<?php
require_once('init.php');
$title = '管理員後台';

if(empty($_COOKIE['vote_admin'])){
    header('Location: adminlogin.php');
}
$sql = "SELECT * FROM event WHERE isdelete = 0 AND unit = '".$_COOKIE['vote_admin_unit']."'";
if($_COOKIE['vote_admin_unit']=='最高權限'){
    $sql = "SELECT * FROM event WHERE isdelete = 0";
}
$stmt = $pdo->query($sql);
$items = $stmt->fetchAll();
$now = time();
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
            <div class='col-xs-4 '>
                <a href='eventadd.php' class='btn btn-primary' target="_blank">新增投票活動</a>
            </div>
            <div class='col-xs-12'>
                <h3 class='text-center'>投票活動列表</h3>
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>編號</th>
                            <th>名稱</th>
                            <th>開始時間</th>
                            <th>結束時間</th>
                            <th>建立單位</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($items as $i){ ?>
                            <tr>
                            <td><?= $i['id'] ?></td>
                            <td><?= $i['name'] ?></td>
                            <td><?= $i['starttime'] ?></td>
                            <td><?= $i['endtime'] ?></td>
                            <td><?= $i['unit'] ?></td>
                            <td>
                                <?php if($i['isbegin'] == 0){ ?>
                                    <a href='open.php?id=<?= $i['id'] ?>' onclick="return confirm('啟動投票後，即不可再編輯候選人、可投票人，且於投票期間不能刪除此筆資料。請確認？')" class='btn btn-warning'>啟動投票</a>
                                <?php } else{ ?>
                                    <a href='#'  class='btn btn-default'>已啟動投票</a>
                                <?php } ?>
                                <a href='itemadd.php?id=<?= $i['id'] ?>' class='btn btn-success'>新增候選人</a>
                                <a href='voteadd.php?id=<?= $i['id'] ?>' class='btn btn-primary'>新增可投票人</a>
                                <a href='index.php?id=<?= $i['id'] ?>' target='_BLANK' class='btn btn-default'>投票網址</a>
                                <a href='result.php?id=<?= $i['id'] ?>' target='_BLANK' class='btn btn-default'>投票結果</a>
                                <?php if(true || $i['isbegin'] == 0 || $now >= strtotime($i['endtime'])){ ?>
                                <a href='delete.php?id=<?= $i['id'] ?>' onclick="return confirm('確定刪除嗎，不可還原?')" class='btn btn-danger'>刪除</a>
                                <?php } ?>
                            </td>
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