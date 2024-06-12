<?php
require_once('init.php');
$title = '管理員後台';

if(empty($_COOKIE['vote_admin'])){
    header('Location: login.php');
}

if (!empty($_POST))
{
    $sql = "INSERT INTO event (name,starttime,endtime,unit, ismulti) 
    VALUES (:name,:starttime,:endtime,:unit, :ismulti)";
    
    $date = new DateTime();

    $data = [];
    foreach($_POST as $key => $value) {
        $data[$key] = $value;
    }

    //var_dump($data);
    $result = $pdo->prepare($sql)->execute($data);
    if($result)
        header('Location: admin.php?msg=新增活動成功');
    else
        header('Location: eventadd.php?msg=新增活動失敗');
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
                <h3 class="text-center">新增投票活動</h3>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-8 col-xs-offset-2">
                <form id="addform" class="form-horizontal" method="post">
                    
                    <div class="form-group">
                        <label for="casenum" class="col-xs-3 control-label"><span class="red">*</span>投票名稱</label>
                        <div class="col-xs-9">
                            <input type="text" class="form-control" id="casenum" name="name" placeholder="" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="receive_date" class="col-xs-3 control-label">開始時間</label>
                        <div class="col-xs-9">
                            <input type="datetime-local" class="form-control" id="receive_date" name="starttime" placeholder="" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="receive_date1" class="col-xs-3 control-label">結束時間</label>
                        <div class="col-xs-9">
                            <input type="datetime-local" class="form-control" id="receive_date1" name="endtime" placeholder="" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="casenum" class="col-xs-3 control-label"><span class="red"></span>建立單位</label>
                        <div class="col-xs-9">
                            <input type="text" class="form-control" id="unit" name="unit" readonly value='<?= $_COOKIE['vote_admin_unit'] ?>'>
                        </div>
                    </div>
					<div class="form-group">
                        <label for="votetype" class="col-xs-3 control-label"><span class="red"></span>投票類型</label>
                        <div class="radio col-xs-9">
							<div class="col-xs-6">
								<label><input type="radio" name="ismulti" value=0 required>一人一票</label>
							</div>
							<div class="col-xs-6">
								<label><input type="radio" name="ismulti" value=1 checked required>一人兩票</label>
							</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-offset-3 col-xs-9">
                            <button type="submit" class="btn btn-success">新增</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php include '_footer.php'; ?>
    <?php include '_js.php'; ?>
    <script>
        
    </script>
</body>
</html>