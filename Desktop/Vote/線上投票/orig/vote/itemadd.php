<?php
require_once('init.php');
$title = '管理員後台';

if(empty($_COOKIE['vote_admin'])){
    header('Location: adminlogin.php');
}

$stmt = $pdo->prepare("SELECT * FROM event where id =:id");
$stmt->execute(['id' => $_GET['id']]); 
$event = $stmt->fetch();

$stmt = $pdo->prepare("SELECT * FROM item where event_id =:id AND isdelete=0 ORDER BY no ASC");
$stmt->execute(['id' => $_GET['id']]); 
$items = $stmt->fetchAll();

if(!empty($_POST['action'])){
    if($_POST['action'] == 'add'){
        $sql = "INSERT INTO item (no,dept,name,title,event_id,comment) 
        VALUES (:no,:dept,:name,:title,:event_id,:comment)";
        

        $data = [];
        $data['event_id'] = $_GET['id'];
        foreach($_POST as $key => $value) {
            if($key == 'action')
                continue;
            $data[$key] = $value;
        }

        var_dump($data);
        $result = $pdo->prepare($sql)->execute($data);
        if($result)
            header('Location: itemadd.php?id='.$_GET['id'].'&msg=新增候選人成功');
        else
            header('Location: itemadd.php?id='.$_GET['id'].'&msg=新增候選人失敗');
    
    } else if($_POST['action'] == 'delete'){
        $sql = 'UPDATE item SET isdelete=1 WHERE id=:id';
        $data = [];
        $data['id'] = $_POST['id'];
        $result = $pdo->prepare($sql)->execute($data);
        if($result)
            header('Location: itemadd.php?msg=刪除候選人成功&id='.$_GET['id']);
        else
            header('Location: itemadd.php?msg=刪除候選人失敗&id='.$_GET['id']);
    }
}

?>

<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
    <?php include '_head.php'; ?>
    <style>
    body {
        font-size: 16px;
        font-weight: bold;
    }
    </style>
</head>
<body>
    <?php include '_nav.php'; ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-8 col-xs-offset-2">
                <a href="admin.php" class="btn btn-info pull-left">回上頁</a>
                <h3 class="text-center"><?= $event['name'] ?>-候選人名冊</h3>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-10">
            <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>編號</th>
                            <th>單位</th>
                            <th>姓名</th>
                            <th>職稱</th>
                            <th>備註</th>
                            <?php if(!$event['isbegin']) { ?>
                            <th>操作</th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($items as $i){ ?>
                            <tr>
                            <form method='post'>
                                <input type='hidden' name='id' value='<?= $i['id'] ?>'>
                            <td><input name='no' type='number' value='<?= $i['no'] ?>' required readonly></td>
                            <td><input name='dept' value='<?= $i['dept'] ?>' required readonly></td>
                            <td><input name='name' value='<?= $i['name'] ?>' required readonly></td>
                            <td><input name='title' value='<?= $i['title'] ?>' required readonly></td>
                            <td><input name='comment' value='<?= $i['comment'] ?>' required readonly></td>
                            <?php if(!$event['isbegin']) { ?>
                            <td>
                            <button type='submit' name='action' value='edit' class='hide btn btn-primary'>更新</button>
                            <button type='submit' name='action' value='delete' onclick="return confirm('確定刪除嗎?')" class='btn btn-danger'>刪除</button>
                            </td>
                            <?php } ?>
                            </form>
                            </tr>
                        <?php } ?>
                        <?php if(!$event['isbegin']) { ?>
                            <tr>
                                <form method='post'>
                                
                                <td><input name='no' type='number' value='' required></td>
                                <td><input name='dept' value='' required></td>
                                <td><input name='name' value='' required></td>
                                <td><input name='title' value='' required></td>
                                <td><input name='comment' value='' required></td>
                                <td>
                                    <button type='submit' name='action' value='add' class='btn btn-success'>新增</button>
                                </td>
                                </form>
                            </tr>
                            <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php include '_footer.php'; ?>
    <?php include '_js.php'; ?>
    <script>
        
    </script>
</body>
</html>