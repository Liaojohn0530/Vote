<?php
require_once('init.php');
$title = '填報';

if (!empty($_POST))
{
    $sql = "INSERT INTO Criminalpay (progress,crtEng,crtEngTo,pay_id,pay_name,pay_title,pay_crt,pay_dpt,pay_time,casenum,receive_date,decide_date,decide_expired,sendmoj_date,sure_date,report_date,endsituation,pay_date,send_date,send_expired) 
    VALUES (:progress,:crtEng,:crtEngTo,:pay_id,:pay_name,:pay_title,:pay_crt,:pay_dpt,:pay_time,:casenum,:receive_date,:decide_date,:decide_expired,:sendmoj_date,:sure_date,:report_date,:endsituation,:pay_date,:send_date,:send_expired)";
    
    $date = new DateTime();

    $data = [];
    $data['progress'] = 0;
    $data['pay_id'] = $_COOKIE['id'];
    $data['pay_name'] = $_COOKIE['name'];
    $data['pay_title'] = $_COOKIE['title'];
    $data['pay_crt'] = $_COOKIE['court_name'];
    $data['pay_dpt'] = $_COOKIE['department'];
    $data['pay_time'] = $date->format('Y-m-d H:i:s');

    foreach($_POST as $key => $value) {
        $data[$key] = $value=='' ? null : $value; //date存''會變1900-01-01
    }
    //var_dump($data);
    $result = $pdo->prepare($sql)->execute($data);
    if($result)
        header('Location: index.php?msg=填報成功');
    else
        header('Location: add.php?msg=填報失敗');
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
                <a href="index.php" class="btn btn-info pull-left">回上頁</a>
                <h3 class="text-center">刑事補償事件 - 填報</h3>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-8 col-xs-offset-2">
                <form id="addform" class="form-horizontal" action="add.php" method="post">
                    <div class="form-group">
                        <label for="" class="col-xs-3 control-label">法院別</label>
                        <div class="col-xs-9">
                            <input type="text" class="form-control" id="crtEng" name="crtEng" value="<?=$_COOKIE['court_name']?>" readonly>
                            <input type="hidden" class="form-control" id="crtEng" name="crtEng" value="<?=$_COOKIE['court']?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="casenum" class="col-xs-3 control-label"><span class="red">*</span>刑補案號</label>
                        <div class="col-xs-9">
                            <input type="text" class="form-control" id="casenum" name="casenum" placeholder="刑補案號" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="receive_date" class="col-xs-3 control-label">收案日</label>
                        <div class="col-xs-9">
                            <input type="text" class="form-control hasdp" id="receive_date" name="receive_date" placeholder="收案日">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="decide_date" class="col-xs-3 control-label">補償決定日</label>
                        <div class="col-xs-9">
                            <input type="text" class="form-control hasdp" id="decide_date" name="decide_date" placeholder="補償決定日">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="decide_expired" class="col-xs-3 control-label">補償決定日-逾期原因</label>
                        <div class="col-xs-9">
                            <input type="text" class="form-control" id="decide_expired" name="decide_expired" placeholder="補償決定日-逾期原因">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="sendmoj_date" class="col-xs-3 control-label">送達最高檢察署日</label>
                        <div class="col-xs-9">
                            <input type="text" class="form-control hasdp" id="sendmoj_date" name="sendmoj_date" placeholder="送達最高檢察署日">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="sure_date" class="col-xs-3 control-label">決定補償確定日</label>
                        <div class="col-xs-9">
                            <input type="text" class="form-control hasdp" id="sure_date" name="sure_date" placeholder="決定補償確定日">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="report_date" class="col-xs-3 control-label">登載公報及報紙日</label>
                        <div class="col-xs-9">
                            <input type="text" class="form-control hasdp" id="report_date" name="report_date" placeholder="登載公報及報紙日">
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
                                    <option value="<?= $k ?>"><?= $v ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="pay_date" class="col-xs-3 control-label">支付補償金日</label>
                        <div class="col-xs-9">
                            <input type="text" class="form-control hasdp" id="pay_date" name="pay_date" placeholder="支付補償金日">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="send_date" class="col-xs-3 control-label">函送高院審核日</label>
                        <div class="col-xs-9">
                            <input type="text" class="form-control hasdp" id="send_date" name="send_date" placeholder="函送高院審核日">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="send_expired" class="col-xs-3 control-label">函送高院審核日-逾期原因</label>
                        <div class="col-xs-9">
                            <input type="text" class="form-control" id="send_expired" name="send_expired" placeholder="函送高院審核日-逾期原因">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="crtEngTo" class="col-xs-3 control-label">函送高院</label>
                        <div class="col-xs-9">
                            <select class="form-control" id="crtEngTo" name="crtEngTo" required>
                                <option value="TPH">臺灣高等法院</option>
                                <option value="KMH">福建高等法院金門高分院</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-xs-offset-3 col-xs-9">
                            <button type="submit" class="btn btn-success">填報</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php include '_footer.php'; ?>
    <?php include '_js.php'; ?>
    <script>
        $(".hasdp").datepickerTW(dpopt);
        //日期,修改為民國年 
        //$('body').on('change', '.hasdp', function(){
        //    $(this).val(yearsub1911($(this).val()));
        //})
        //表單提交, 交民國修改回西元年
        $('#addform').submit(function(){
            $(".hasdp").each(function(){
                if($(this).val())
                    $(this).val(yearadd1911($(this).val()));
                    //console.log($(this).val());
            });
        })
    </script>
</body>
</html>