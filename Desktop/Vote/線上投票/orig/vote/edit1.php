<?php
require_once('init.php');

if(empty($_GET['id'])){
    header('Location: index.php?msg=ID為空');
    //exit;
}

$stmt = $pdo->prepare("SELECT * FROM Criminalpay WHERE ID=:id");
$stmt->execute(['id' => $_GET['id']]); 
$pay = $stmt->fetch();
$check_result = json_decode($pay['check_result'], true);
//var_dump($check_result);
if (!empty($_POST)){
    //var_dump($_POST);
    $sql = "UPDATE Criminalpay SET progress=:progress,e_check_id=:e_check_id,e_check_name=:e_check_name,e_check_title=:e_check_title,e_check_crt=:e_check_crt,e_check_dpt=:e_check_dpt,e_check_time=:e_check_time,sendjud_date=:sendjud_date,check_result=:check_result,back1=:back1 WHERE ID=:id";

    $date = new DateTime();
    $data = [];
    $data['id'] = $_GET['id'];
    $data['progress'] = 1;
   
    //首次編輯
    if(empty($pay['check_id'])){
        $data['check_id'] = $_COOKIE['id'];
        $data['check_name'] = $_COOKIE['name'];
        $data['check_title'] = $_COOKIE['title'];
        $data['check_crt'] = $_COOKIE['court_name'];
        $data['check_dpt'] = $_COOKIE['department'];
        $data['check_time'] = $date->format('Y-m-d H:i:s');
        $sql = str_replace('e_', '', $sql);
    } else {
        $data['e_check_id'] = $_COOKIE['id'];
        $data['e_check_name'] = $_COOKIE['name'];
        $data['e_check_title'] = $_COOKIE['title'];
        $data['e_check_crt'] = $_COOKIE['court_name'];
        $data['e_check_dpt'] = $_COOKIE['department'];
        $data['e_check_time'] = $date->format('Y-m-d H:i:s');
    }

    $data['sendjud_date'] = $_POST['sendjud_date'];
    //清除check_result_date和check_result_text都為空的array元素
    foreach($_POST['check_result_date'] as $k => $v){
        
        if (empty($_POST['check_result_date'][$k]) && empty($_POST['check_result_text'][$k])){
            unset($_POST['check_result_date'][$k]);
            unset($_POST['check_result_text'][$k]);
        }
    }
    //重設index
    $_POST['check_result_date'] = array_values($_POST['check_result_date']);
    $_POST['check_result_text'] = array_values($_POST['check_result_text']);
    $data['check_result'] = json_encode($_POST);

    if($pay['back1'] == 1){ //如果是被退回的，改為已修正退回
        $data['back1'] = 2;
    } else {
        $data['back1'] = $pay['back1'];
    }
    $result = $pdo->prepare($sql)->execute($data);
    if($result)
        header('Location: edit1.php?msg=審查成功&id='.$_GET['id']);
    else
        header('Location: edit1.php?msg=審查失敗&id='.$_GET['id']);
}
//_detail.php用
$pid = $_GET['id'];

$title = '高院審查';
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
                <h3 class="text-center">刑事補償事件 - 高院審查</h3>
            </div>
        </div>
        <?php if($pay['back1'] == 1){ ?>
            <div class="row">
                <div class="col-xs-12">
                   <p class="text-center red">退回說明：<?= $pay['back1_info'] ?></p>
                </div>
            </div>
        <?php } ?>
        <div class="row">
            <div class="col-xs-8 col-xs-offset-2">
                <form id="edit1form" class="form-horizontal" action="edit1.php?id=<?=$_GET['id']?>" method="POST">
                    <div class="form-group">
                        <label for="sendjud_date" class="col-xs-3 control-label"><span class="red">*</span>首次函報司法院日</label>
                        <div class="col-xs-9">
                            <input type="text" class="form-control hasdp" id="sendjud_date" name="sendjud_date" value="<?=$pay['sendjud_date']?>" required placeholder="首次函報司法院日">
                        </div>
                    </div>
                    <div id="check_result_list"><span class="glyphicon glyphicon-plus check_result_add" style="cursor:pointer;color:green;"></span>
                            <?php if($check_result != null && count($check_result['check_result_date']) != 0){ 
                                    for($i=0; $i<count($check_result['check_result_date']); $i++) {
                            ?>
                                        <div class="form-group check_result_div">
                                            <label for="check_result" class="col-xs-3 control-label"><span class="glyphicon glyphicon-remove check_result_del" style="cursor:pointer;color:red;"></span> <span class="red">*</span>審查結果</label>
                                            <div class="col-xs-3">
                                                <input type="text" class="form-control check_result_date hasdp" name="check_result_date[]" value="<?=$check_result['check_result_date'][$i]?>" placeholder="審查結果日">
                                            </div>
                                            <div class="col-xs-6">
                                                <input type="text"" class="form-control check_result_text" name="check_result_text[]" value="<?=$check_result['check_result_text'][$i]?>" placeholder="（有違失、無違失）">
                                            </div>
                                        </div>
                            <?php }} ?>
                        <div class="form-group check_result_div">
                            <label for="check_result" class="col-xs-3 control-label"><span class="glyphicon glyphicon-remove check_result_del" style="cursor:pointer;color:red;"></span> <span class="red">*</span>審查結果</label>
                            <div class="col-xs-3">
                                <input type="text" class="form-control check_result_date hasdp" name="check_result_date[]" placeholder="審查結果日">
                            </div>
                            <div class="col-xs-6">
                                <input type="text"" class="form-control check_result_text" name="check_result_text[]" placeholder="（有違失、無違失）">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-offset-3 col-xs-6">
                            <button type="submit" class="btn btn-primary">審查</button>
                        </div>
                        <div class=" col-xs-3">
                            <a id="back0" href="back.php?progress=0&id=<?=$_GET['id']?>&url=<?=$url?>" class="btn btn-primary" style="background-color: rgb(161, 132, 105);">退回填報法院</a>
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
            var $check_result_div = $('.check_result_div').last().clone();
            $(".hasdp").datepickerTW(dpopt);
        }
        { //事件監聽
            //新增審查結果
            $('body').on('click', '.check_result_add', function(){
                var $div = $check_result_div.clone();
                $div.find('.check_result_text').val('');
                $div.find('.check_result_date').val('');
                $('#check_result_list').append($div);
                $div.find('.hasdp').datepickerTW(dpopt);
            })
            //日期,修改為民國年
            //$('body').on('change', '.hasdp', function(){
            //    $(this).val(yearsub1911($(this).val()));
            //})
            if($('#sendjud_date').val())
                //$('#sendjud_date').trigger("change");
                $('#sendjud_date').val(yearsub1911($('#sendjud_date').val()));
            //刪除審查結果
            $('body').on('click', '.check_result_del', function(){
                if($('.check_result_div').length == 1) return;
                $(this).parents('.check_result_div').remove();
            })
            //表單提交, 民國修改回西元年
            $('#edit1form').submit(function(){
                if($('#sendjud_date').val())
                    $('#sendjud_date').val(yearadd1911($('#sendjud_date').val()));
                    //console.log($(this).val());
            })
            //退回
            $('#back0').click(function(){
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