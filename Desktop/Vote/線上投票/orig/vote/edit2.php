<?php
require_once('init.php');

if(empty($_GET['id']))
    header('Location: index.php?msg=ID為空');

$stmt = $pdo->prepare("SELECT * FROM Criminalpay WHERE ID=:id");
$stmt->execute(['id' => $_GET['id']]); 
$pay = $stmt->fetch();
$check_report = json_decode($pay['check_report'], true);
//var_dump($check_report);

if (!empty($_POST)){
    //var_dump($_POST);
    $sql = "UPDATE Criminalpay SET progress=:progress,e_jud_id=:e_jud_id,e_jud_name=:e_jud_name,e_jud_title=:e_jud_title,e_jud_crt=:e_jud_crt,e_jud_dpt=:e_jud_dpt,e_jud_time=:e_jud_time,check_report=:check_report WHERE ID=:id";

    $date = new DateTime();
    $data = [];
    $data['id'] = $_GET['id'];
    $data['progress'] = 2;
    if(empty($pay['jud_id'])){
        $data['jud_id'] = $_COOKIE['id'];
        $data['jud_name'] = $_COOKIE['name'];
        $data['jud_title'] = $_COOKIE['title'];
        $data['jud_crt'] = $_COOKIE['court_name'];
        $data['jud_dpt'] = $_COOKIE['department'];
        $data['jud_time'] = $date->format('Y-m-d H:i:s');
        $sql = str_replace('e_', '', $sql);
    } else{
        $data['e_jud_id'] = $_COOKIE['id'];
        $data['e_jud_name'] = $_COOKIE['name'];
        $data['e_jud_title'] = $_COOKIE['title'];
        $data['e_jud_crt'] = $_COOKIE['court_name'];
        $data['e_jud_dpt'] = $_COOKIE['department'];
        $data['e_jud_time'] = $date->format('Y-m-d H:i:s');
    }
    

    //清除check_report_date和check_report_text都為空的array元素
    foreach($_POST['check_report_date'] as $k => $v){
        
        if (empty($_POST['check_report_date'][$k]) && empty($_POST['check_report_text'][$k])){
            unset($_POST['check_report_date'][$k]);
            unset($_POST['check_report_text'][$k]);
        }
    }
    //重設index
    $_POST['check_report_date'] = array_values($_POST['check_report_date']);
    $_POST['check_report_text'] = array_values($_POST['check_report_text']);
    $data['check_report'] = json_encode($_POST);

    $result = $pdo->prepare($sql)->execute($data);
    if($result)
        header('Location: edit2.php?msg=審核報告成功&id='.$_GET['id']);
    else
        header('Location: edit2.php?msg=審核報告失敗&id='.$_GET['id']);
}


//_detail.php用
$pid = $_GET['id'];
$title = '司法院收受審核報告';
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
            <div class="col-xs-10 col-xs-offset-1">
                <a href="index.php" class="btn btn-info pull-left">回上頁</a>
                <h3 class="text-center">刑事補償事件 - 司法院收受審核報告</h3>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-10 col-xs-offset-1">
                <form class="form-horizontal" action="edit2.php?id=<?=$_GET['id']?>" method="POST">
                    <div id="check_report_list"><span class="glyphicon glyphicon-plus check_report_add" style="cursor:pointer;color:green;"></span>
                            <?php if($check_report != null && count($check_report['check_report_date']) != 0){ 
                                    for($i=0; $i<count($check_report['check_report_date']); $i++) {
                            ?>
                                        <div class="form-group check_report_div">
                                            <label for="check_report" class="col-xs-2 control-label"><span class="glyphicon glyphicon-remove check_report_del" style="cursor:pointer;color:red;"></span> <span class="red">*</span>收受審核報告</label>
                                            <div class="col-xs-3">
                                                <input type="text" class="form-control check_report_date hasdp" name="check_report_date[]" value="<?=$check_report['check_report_date'][$i]?>" placeholder="收受審核報告日期">
                                            </div>
                                            <div class="col-xs-7">
                                                <input type="text"" class="form-control check_report_text" name="check_report_text[]" value="<?=$check_report['check_report_text'][$i]?>" placeholder="收受審核報告結果（准予備查、發回續查、函送補償法院召開求償委員會）">
                                            </div>
                                        </div>
                            <?php }} ?>
                        <div class="form-group check_report_div">
                            <label for="check_report" class="col-xs-2 control-label"><span class="glyphicon glyphicon-remove check_report_del" style="cursor:pointer;color:red;"></span> <span class="red">*</span>收受審核報告</label>
                            <div class="col-xs-3">
                                <input type="text" class="form-control check_report_date hasdp" name="check_report_date[]" placeholder="收受審核報告日期">
                            </div>
                            <div class="col-xs-7">
                                <input type="text"" class="form-control check_report_text" name="check_report_text[]" placeholder="收受審核報告結果（准予備查、發回續查、函送補償法院召開求償委員會）">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-offset-2 col-xs-6">
                            <button type="submit" class="btn btn-primary">審核報告</button>
                        </div>
                        <div class=" col-xs-4">
                            <a id="back0" href="back.php?progress=0&id=<?=$_GET['id']?>&url=<?=$url?>" class="btn btn-primary" style="background-color: rgb(161, 132, 105);">退回填報法院</a>
                            <a id="back1" href="back.php?progress=1&id=<?=$_GET['id']?>&url=<?=$url?>" class="btn btn-primary" style="background-color: #427e9b;">退回高院</a>
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
            var $check_report_div = $('.check_report_div').last().clone();
            $(".check_report_date").datepickerTW(dpopt);
        }
        { //事件監聽
            //新增審查結果
            $('body').on('click', '.check_report_add', function(){
                var $div = $check_report_div.clone();
                $div.find('.check_report_text').val('');
                $div.find('.check_report_date').val('');
                $('#check_report_list').append($div);
                $div.find('.check_report_date').datepickerTW(dpopt);
            })
            //日期,修改為民國年
            //$('body').on('change', '.hasdp', function(){
            //    $(this).val(yearsub1911($(this).val()));
            //})
            //刪除審查結果
            $('body').on('click', '.check_report_del', function(){
                if($('.check_report_div').length == 1) return;
                $(this).parents('.check_report_div').remove();
            })
            //退回
            $('#back0, #back1').click(function(){
                if (!confirm('確定要退回嗎?'))
                    return false;
                var info = prompt('請輸入退回說明：');
                if(!info)
                    return false;
                $(this).attr('href',  $(this).attr('href')+'&info='+info);
            });
        }
    </script>
</body>
</html>