<?php
require_once('init.php');
$title = '管理員後台';

if(empty($_COOKIE['vote_admin'])){
    header('Location: adminlogin.php');
}

$stmt = $pdo->prepare("SELECT * FROM event where id =:id");
$stmt->execute(['id' => $_GET['id']]); 
$event = $stmt->fetch();

$stmt = $pdo->prepare("SELECT * FROM vote where event_id =:id");
$stmt->execute(['id' => $_GET['id']]); 
$votes = $stmt->fetchAll();

if (isset($_POST['submit'])) {
    $fh = fopen($_FILES['file']['tmp_name'], 'r+');

    $lines = array();
    while( ($row = fgetcsv($fh, 8192)) !== FALSE ) {
        for($i=0; $i < count($row); $i++){
            $row[$i] = iconv('big5','utf-8',$row[$i]); 
        }
        $lines[] = $row;
    }
    array_shift($lines);  
    var_dump($lines);

    $sql = "DELETE FROM vote WHERE event_id=:id";
    $result = $pdo->prepare($sql)->execute(['id' => $_GET['id']]);
    
    foreach($lines as $v){
        $sql = "INSERT INTO vote (votewho,dept,name,title,event_id,account,pswd) 
        VALUES (:votewho,:dept,:name,:title,:event_id,:account,:pswd)";
        
        $data['votewho'] = 0;
        $data['dept'] = $v[0];
        $data['name'] = $v[1];
        $data['title'] = $v[2];
        $data['account'] = $v[3];
        $data['pswd'] = $v[4];
        $data['event_id'] = $_GET['id'];
        
        var_dump($data);
        $result = $pdo->prepare($sql)->execute($data);
        if($result)
            header('Location: voteadd.php?id='.$_GET['id'].'&msg=新增可投票人成功');
        else
            header('Location: voteadd.php?id='.$_GET['id'].'&msg=新增可投票人失敗');
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
                <h3 class="text-center"><?= $event['name'] ?>-可投票人列表</h3>
                <form class="pull-right" method='post' enctype="multipart/form-data">
                    <?php if(!$event['isbegin']) { ?>
                    <label>
                    <input name='file' type='file' required accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                    </label>
                    <button name='submit' type='submit' class='btn btn-primary'>上傳可投票人(會將原有投票人清除)</button>
                    <?php } ?>
                    <a href='example.csv' target='_blank'>csv檔範例下載</a>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-8 col-xs-offset-2">
            <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>單位</th>
                            <th>姓名</th>
                            <th>職稱</th>
                            <th>帳號</th>
                            <th>密碼</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($votes as $i){ ?>
                            <tr>
                                <td><?= $i['dept'] ?></td>
                                <td><?= $i['name'] ?></td>
                                <td><?= $i['title'] ?></td>
                                <td><?= $i['account'] ?></td>
                                <td>不顯示</td>
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