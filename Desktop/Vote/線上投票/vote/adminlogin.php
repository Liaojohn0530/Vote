<?php
require_once('init.php');
$title = '管理員登入';



$exp = time() + Config::cookie_exp;

if(!empty($_POST['account'])){
    $adminarr = Config::adminArray;
    if(array_key_exists($_POST['account'], $adminarr)
    && $adminarr[$_POST['account']][0] == $_POST['pswd']){
    //if($_POST['account']==Config::adminaccount && $_POST['pswd']==Config::adminpswd){

        //setcookie("vote_login", 1, $exp);
        setcookie("vote_admin", 1, $exp);
        setcookie("vote_admin_unit", $adminarr[$_POST['account']][1], $exp);
        setcookie("vote_admin_account", $_POST['account'], $exp);

        header('Location: admin.php');
    } else {
        header('Location: adminlogin.php?msg=帳號密碼錯誤');
    }
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
                <form method="post">
                    <div class="form-group">
                      <label for="exampleInputEmail1">帳號</label>
                      <input type="text" name='account' class="form-control" id="exampleInputEmail1" required>
                    </div>
                    <div class="form-group">
                      <label for="exampleInputPassword1">密碼</label>
                      <input type="password" name='pswd' class="form-control" id="exampleInputPassword1" required>
                    </div>
                    
                    <button type="submit" class="btn btn-block btn-primary">管理員登入</button>
                  </form>
            </div>
            
        </div>
    </div>
    <?php include '_footer.php'; ?>
    <?php include '_js.php'; ?>
</body>
</html>