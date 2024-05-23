<?php
require_once('init.php');
$title = '司法院線上投票-登入';
if(empty($_GET['id'])){
    die;
}

$stmt = $pdo->prepare("SELECT * FROM event where id =:id");
$stmt->execute(['id' => $_GET['id']]); 
$event = $stmt->fetch();

$exp = time() + Config::cookie_exp;

if(!empty($_POST['account'])){
    $stmt = $pdo->prepare("SELECT * FROM vote where event_id =:id AND account=:account");
    $stmt->execute(['id' => $_GET['id'],'account' => $_POST['account']]); 
    $v = $stmt->fetch();
    
    if(!$v){
        header('Location: login.php?id='.$_GET['id'].'&msg=帳號密碼錯誤（請輸入正確資料）或未具投票權資格（如有疑義，請洽主辦單位）');
        die;
    }
    if($v['pswd'] != $_POST['pswd']){
        header('Location: login.php?id='.$_GET['id'].'&msg=帳號密碼錯誤（請輸入正確資料）或未具投票權資格（如有疑義，請洽主辦單位）');
        die;
    }
    if($v['isvoted'] == 1){
        header('Location: login.php?id='.$_GET['id'].'&msgjs=您已完成投票，無法重複投票');
        die;
    }
    setcookie("vote_user", $_GET['id'], $exp);
    setcookie("vote_id", $v['id'], $exp);
    setcookie("vote_votename", $v['name'], $exp);

    header('Location: index.php?id='.$_GET['id']);
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
            <div class='col-xs-6 col-xs-offset-3'>
                <h2 class='text-center'><?= $event['name'] ?></h2>
		<h3 class='text-center'><span>(投票期間：<?= substr($event['starttime'],0,-3) ?> ~ <?= substr($event['endtime'],0,-3) ?>)</span></h3>
                <form method="post">
                    <div class="form-group">
                      <label for="exampleInputEmail1">帳號</label>
                      <input type="text" name='account' class="form-control" id="exampleInputEmail1" required>
                    </div>
                    <div class="form-group">
                      <label for="exampleInputPassword1">密碼</label>
                      <input type="password" name='pswd' class="form-control" id="exampleInputPassword1" required>
                    </div>
                    
                    <button type="submit" class="btn btn-block btn-primary">投票登入</button>
                  </form>
            </div>
            <div id='loginlaert' class='col-xs-6 col-xs-offset-3'>
                <br>
            </div>
        </div>
    </div>
    <?php include '_footer.php'; ?>
    <?php include '_js.php'; ?>
    <script>
        $('#loginlaert').append($('#alertmsg'));
    </script>
</body>
</html>