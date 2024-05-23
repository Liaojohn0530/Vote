<?php
require_once('init.php');

if(empty($_GET['id']))
    header('Location: index.php?msg=ID為空');

$stmt = $pdo->prepare("SELECT * FROM Criminalpay WHERE ID=:id");
$stmt->execute(['id' => $_GET['id']]); 
$pay = $stmt->fetch();
$committee_result = json_decode($pay['committee_result'], true);
//var_dump($committee_result);

if (!empty($_POST)){
    //var_dump($_POST);
    $sql = "UPDATE Criminalpay SET progress=:progress,e__pay2_id=:e__pay2_id,e__pay2_name=:e__pay2_name,e__pay2_title=:e__pay2_title,e__pay2_crt=:e__pay2_crt,e__pay2_dpt=:e__pay2_dpt,e__pay2_time=:e__pay2_time,committee_date=:committee_date,committee_expired=:committee_expired,committee_result=:committee_result,back3=:back3 WHERE ID=:id";

    $date = new DateTime();
    $data = [];
    $data['id'] = $_GET['id'];
    $data['progress'] = 3;
    if(empty($pay['pay2_id'])){
        $data['pay2_id'] = $_COOKIE['id'];
        $data['pay2_name'] = $_COOKIE['name'];
        $data['pay2_title'] = $_COOKIE['title'];
        $data['pay2_crt'] = $_COOKIE['court_name'];
        $data['pay2_dpt'] = $_COOKIE['department'];
        $data['pay2_time'] = $date->format('Y-m-d H:i:s');
        $sql = str_replace('e__', '', $sql);
    } else {
        $data['e_pay2_id'] = $_COOKIE['id'];
        $data['e_pay2_name'] = $_COOKIE['name'];
        $data['e_pay2_title'] = $_COOKIE['title'];
        $data['e_pay2_crt'] = $_COOKIE['court_name'];
        $data['e_pay2_dpt'] = $_COOKIE['department'];
        $data['e_pay2_time'] = $date->format('Y-m-d H:i:s');
        $sql = str_replace('e__', 'e_', $sql);
    }
    

    $data['committee_date'] = $_POST['committee_date'];
    $data['committee_expired'] = $_POST['committee_expired'];
    //清除committee_result_date和committee_result_text都為空的array元素
    foreach($_POST['committee_result_date'] as $k => $v){
        
        if (empty($_POST['committee_result_date'][$k]) && empty($_POST['committee_result_text'][$k])){
            unset($_POST['committee_result_date'][$k]);
            unset($_POST['committee_result_text'][$k]);
        }
    }
    //重設index
    $_POST['committee_result_date'] = array_values($_POST['committee_result_date']);
    $_POST['committee_result_text'] = array_values($_POST['committee_result_text']);
    $data['committee_result'] = json_encode($_POST);

    if($pay['back3'] == 1){ //如果是被退回的，改為已修正退回
        $data['back3'] = 2;
    } else {
        $data['back3'] = $pay['back3'];
    }
    $result = $pdo->prepare($sql)->execute($data);
    if($result)
        header('Location: edit3.php?msg=填報成功&id='.$_GET['id']);
    else
        header('Location: edit3.php?msg=填報失敗&id='.$_GET['id']);
}
//_detail.php用
$pid = $_GET['id'];
$title = '召開求償委員會';
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
            <div class="col-xs-8 col-xs-offset-2">
                <a href="index.php"" class="btn btn-info pull-left">回上頁</a>
                <h3 class="text-center">刑事補償事件 - 補償法院召開求償委員會</h3>
            </div>
        </div>
        <?php if($pay['back3'] == 1){ ?>
            <div class="row">
                <div class="col-xs-12">
                   <p class="text-center red">退回說明：<?= $pay['back3_info'] ?></p>
                </div>
            </div>
        <?php } ?>
        <div class="row">
            <div class="col-xs-8 col-xs-offset-2">
                <form id="edit3form" class="form-horizontal" action="edit3.php?id=<?=$_GET['id']?>" method="POST">
                    <div class="form-group">
                        <label for="committee_date" class="col-xs-3 control-label"><span class="red">*</span>首次召開求償委員會日</label>
                        <div class="col-xs-9">
                            <input type="text" class="form-control hasdp" id="committee_date" name="committee_date" value="<?=$pay['committee_date']?>" required placeholder="首次召開求償委員會日">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="committee_expired" class="col-xs-3 control-label">逾期原因</label>
                        <div class="col-xs-9">
                            <input type="text"" class="form-control" id="committee_expired" name="committee_expired" value="<?=$pay['committee_expired']?>" placeholder="首次召開求償委員會日-逾期原因">
                        </div>
                    </div>
                    <div id="committee_result_list">
                        <div class="form-group">
                            <label style="text-align:left;" class="col-xs-12 control-label"><span class="glyphicon glyphicon-plus committee_result_add" style="cursor:pointer;color:green;"></span>
                            求償委員會歷次會議日期、決議內容及層報日期
                        </label>
                            
                        </div>
                            <?php if($committee_result != null && count($committee_result['committee_result_date']) != 0){ 
                                    for($i=0; $i<count($committee_result['committee_result_date']); $i++) {
                            ?>
                                        <div class="form-group committee_result_div">
                                            <label for="committee_result" class="col-xs-3 control-label"><span class="glyphicon glyphicon-remove committee_result_del" style="cursor:pointer;color:red;"></span></label>
                                            <div class="col-xs-3">
                                                <input type="text" class="form-control committee_result_date hasdp" name="committee_result_date[]" value="<?=$committee_result['committee_result_date'][$i]?>" placeholder="日期">
                                            </div>
                                            <div class="col-xs-6">
                                                <input type="text"" class="form-control committee_result_text" name="committee_result_text[]" value="<?=$committee_result['committee_result_text'][$i]?>" placeholder="內容">
                                            </div>
                                        </div>
                            <?php }} ?>
                        <div class="form-group committee_result_div">
                            <label for="committee_result" class="col-xs-3 control-label"><span class="glyphicon glyphicon-remove committee_result_del" style="cursor:pointer;color:red;"></span></label>
                            <div class="col-xs-3">
                                <input type="text" class="form-control committee_result_date hasdp" name="committee_result_date[]" placeholder="日期">
                            </div>
                            <div class="col-xs-6">
                                <input type="text"" class="form-control committee_result_text" name="committee_result_text[]" placeholder="內容">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-offset-3 col-xs-9">
                            <button type="submit" class="btn btn-primary">填報</button>
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
            var $committee_result_div = $('.committee_result_div').last().clone();
            $(".hasdp").datepickerTW(dpopt);
        }
        { //事件監聽
            //新增審查結果
            $('body').on('click', '.committee_result_add', function(){
                var $div = $committee_result_div.clone();
                $div.find('.committee_result_text').val('');
                $div.find('.committee_result_date').val('');
                $('#committee_result_list').append($div);
                $div.find('.hasdp').datepickerTW(dpopt);
            })
            //刪除審查結果
            $('body').on('click', '.committee_result_del', function(){
                if($('.committee_result_div').length == 1) return;
                $(this).parents('.committee_result_div').remove();
            })
            //日期,修改為民國年
            //$('body').on('change', '.hasdp', function(){
            //    $(this).val(yearsub1911($(this).val()));
            //})
            if($('#committee_date').val())
                //$('#committee_date').trigger("change");
                $('#committee_date').val(yearsub1911($('#committee_date').val()));
            //表單提交, 民國修改回西元年
            $('#edit3form').submit(function(){
                if($('#committee_date').val())
                    $('#committee_date').val(yearadd1911($('#committee_date').val()));
                    //console.log($(this).val());
            })
        }
    </script>
</body>
</html>