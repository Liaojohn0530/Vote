<?php
require_once('init.php');

if(empty($_GET['id']))
    header('Location: index.php?msg=ID為空');

$stmt = $pdo->prepare("SELECT * FROM Criminalpay WHERE ID=:id");
$stmt->execute(['id' => $_GET['id']]); 
$pay = $stmt->fetch();

if (!empty($_POST)){
    //var_dump($_POST);
    $sql = "UPDATE Criminalpay SET progress=:progress,crtEng=:crtEng,crtEngTo=:crtEngTo,e_pay_id=:e_pay_id,e_pay_name=:e_pay_name,e_pay_title=:e_pay_title,e_pay_crt=:e_pay_crt,e_pay_dpt=:e_pay_dpt,e_pay_time=:e_pay_time,casenum=:casenum,receive_date=:receive_date,decide_date=:decide_date,decide_expired=:decide_expired,sendmoj_date=:sendmoj_date,sure_date=:sure_date,report_date=:report_date,endsituation=:endsituation,pay_date=:pay_date,send_date=:send_date,send_expired=:send_expired,back0=:back0 WHERE ID=:id";
    
    $date = new DateTime();
    $data = [];
    $data['id'] = $_GET['id'];
    $data['progress'] = 0;
    $data['e_pay_id'] = $_COOKIE['id'];
    $data['e_pay_name'] = $_COOKIE['name'];
    $data['e_pay_title'] = $_COOKIE['title'];
    $data['e_pay_crt'] = $_COOKIE['court_name'];
    $data['e_pay_dpt'] = $_COOKIE['department'];
    $data['e_pay_time'] = $date->format('Y-m-d H:i:s');

    foreach($_POST as $key => $value) {
        $data[$key] = $value=='' ? null : $value; //date存''會變1900-01-01
    }
    if($pay['back0'] == 1){ //如果是被退回的，改為已修正退回
        $data['back0'] = 2;
    } else {
        $data['back0'] = $pay['back0'];
    }

    //var_dump($data);
    $result = $pdo->prepare($sql)->execute($data);
    if($result)
        header('Location: edit0.php?msg=編輯填報成功&id='.$_GET['id']);
    else
        header('Location: edit0.php?msg=編輯填報失敗&id='.$_GET['id']);
}
//_detail.php用
$pid = $_GET['id'];

$title = '編輯填報';
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
                <a href="index.php" class="btn btn-info pull-left">回上頁</a>
                <h3 class="text-center">刑事補償事件 - 編輯填報</h3>
            </div>
        </div>
        <?php if($pay['back0'] == 1){ ?>
            <div class="row">
                <div class="col-xs-12">
                   <p class="text-center red">退回說明：<?= $pay['back0_info'] ?></p>
                </div>
            </div>
        <?php } ?>
        <div class="row">
            <div class="col-xs-8 col-xs-offset-2">
                <form id="edit0form" class="form-horizontal" action="edit0.php?id=<?=$_GET['id']?>" method="POST">
                <div class="form-group">
                        <label for="" class="col-xs-3 control-label">法院別</label>
                        <div class="col-xs-9">
                            <input type="text" class="form-control" id="crtEng" name="crtEng" value="<?=$crtmap[$pay['crtEng']]?>" readonly>
                            <input type="hidden" class="form-control" id="crtEng" name="crtEng" value="<?=$pay['crtEng']?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="casenum" class="col-xs-3 control-label"><span class="red">*</span>刑補案號</label>
                        <div class="col-xs-9">
                            <input type="text" value="<?=$pay['casenum']?>" class="form-control" id="casenum" name="casenum" placeholder="刑補案號" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="receive_date" class="col-xs-3 control-label">收案日</label>
                        <div class="col-xs-9">
                            <input type="text" value="<?=$pay['receive_date']?>" class="form-control hasdp" id="receive_date" name="receive_date" placeholder="收案日">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="decide_date" class="col-xs-3 control-label">補償決定日</label>
                        <div class="col-xs-9">
                            <input type="text" value="<?=$pay['decide_date']?>" class="form-control hasdp" id="decide_date" name="decide_date" placeholder="補償決定日">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="decide_expired" class="col-xs-3 control-label">補償決定日-逾期原因</label>
                        <div class="col-xs-9">
                            <input type="text" value="<?=$pay['decide_expired']?>" class="form-control" id="decide_expired" name="decide_expired" placeholder="補償決定日-逾期原因">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="sendmoj_date" class="col-xs-3 control-label">送達最高檢察署日</label>
                        <div class="col-xs-9">
                            <input type="text" value="<?=$pay['sendmoj_date']?>" class="form-control hasdp" id="sendmoj_date" name="sendmoj_date" placeholder="送達最高檢察署日">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="sure_date" class="col-xs-3 control-label">決定補償確定日</label>
                        <div class="col-xs-9">
                            <input type="text" value="<?=$pay['sure_date']?>" class="form-control hasdp" id="sure_date" name="sure_date" placeholder="決定補償確定日">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="report_date" class="col-xs-3 control-label">登載公報及報紙日</label>
                        <div class="col-xs-9">
                            <input type="text" value="<?=$pay['report_date']?>" class="form-control hasdp" id="report_date" name="report_date" placeholder="登載公報及報紙日">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="endsituation" class="col-xs-3 control-label"><span class="red">*</span>終結情形</label>
                        <div class="col-xs-9">
                            <select class="form-control" id="endsituation" name="endsituation" required>
                                <option value="">請選擇</option>
                                <?php 
                                foreach ($endsituationmap as $k => $v) {
                                ?>
                                    <option <?= $pay['endsituation']==$k?"selected":"" ?> value="<?= $k ?>"><?= $v ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="pay_date" class="col-xs-3 control-label">支付補償金日</label>
                        <div class="col-xs-9">
                            <input type="text" value="<?=$pay['pay_date']?>" class="form-control hasdp" id="pay_date" name="pay_date" placeholder="支付補償金日">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="send_date" class="col-xs-3 control-label">函送高院審核日</label>
                        <div class="col-xs-9">
                            <input type="text" value="<?=$pay['send_date']?>" class="form-control hasdp" id="send_date" name="send_date" placeholder="函送高院審核日">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="send_expired" class="col-xs-3 control-label">函送高院審核日-逾期原因</label>
                        <div class="col-xs-9">
                            <input type="text" value="<?=$pay['send_expired']?>" class="form-control" id="send_expired" name="send_expired" placeholder="函送高院審核日-逾期原因">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="crtEngTo" class="col-xs-3 control-label">函送高院</label>
                        <div class="col-xs-9">
                            <select class="form-control" id="crtEngTo" name="crtEngTo" required>
                                <option <?= $pay['crtEngTo']=="TPH"?"selected":"" ?> value="TPH">臺灣高等法院</option>
                                <option <?= $pay['crtEngTo']=="KMH"?"selected":"" ?> value="KMH">福建高等法院金門高分院</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-xs-offset-3 col-xs-9">
                            <button type="submit" class="btn btn-success">編輯填報</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php include '_detail.php'; ?>
    </div>
    <?php include '_footer.php'; ?>
    <?php include '_js.php'; ?>
    <script>
        { //main
            $(".hasdp").datepickerTW(dpopt);
        }
        { //事件監聽
            //日期,修改為民國年
            //$('body').on('change', '.hasdp', function(){
            //    $(this).val(yearsub1911($(this).val()));
            //})
            $('.hasdp').each(function() {
                if($(this).val())
                    //$(this).trigger("change");
                    $(this).val(yearsub1911($(this).val()));
            });
            //表單提交, 民國修改回西元年
            $('#edit0form').submit(function(){
                $('.hasdp').each(function() {
                    if($(this).val())
                        $(this).val(yearadd1911($(this).val()));
                });
            })
        }
    </script>
</body>
</html>