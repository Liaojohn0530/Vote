<?php
require_once('init.php');

if(empty($_GET['id']))
    header('Location: index.php?msg=ID為空');

$stmt = $pdo->prepare("SELECT * FROM Criminalpay WHERE ID=:id");
$stmt->execute(['id' => $_GET['id']]); 
$pay = $stmt->fetch();
$jud_result = json_decode($pay['jud_result'], true);
//var_dump($jud_result);

if (!empty($_POST)){
    //var_dump($_POST);
    $sql = "UPDATE Criminalpay SET progress=:progress,e_jud2_id=:e_jud2_id,e_jud2_name=:e_jud2_name,e_jud2_title=:e_jud2_title,e_jud2_crt=:e_jud2_crt,e_jud2_dpt=:e_jud2_dpt,e_jud2_time=:e_jud2_time,jud_result=:jud_result WHERE ID=:id";

    $date = new DateTime();
    $data = [];
    $data['id'] = $_GET['id'];
    $data['progress'] = 4;
    if(empty($pay['jud2_id'])){
        $data['jud2_id'] = $_COOKIE['id'];
        $data['jud2_name'] = $_COOKIE['name'];
        $data['jud2_title'] = $_COOKIE['title'];
        $data['jud2_crt'] = $_COOKIE['court_name'];
        $data['jud2_dpt'] = $_COOKIE['department'];
        $data['jud2_time'] = $date->format('Y-m-d H:i:s');
        $sql = str_replace('e_', '', $sql);
    } else {
        $data['e_jud2_id'] = $_COOKIE['id'];
        $data['e_jud2_name'] = $_COOKIE['name'];
        $data['e_jud2_title'] = $_COOKIE['title'];
        $data['e_jud2_crt'] = $_COOKIE['court_name'];
        $data['e_jud2_dpt'] = $_COOKIE['department'];
        $data['e_jud2_time'] = $date->format('Y-m-d H:i:s');
    }
    

    //清除jud_result_date和jud_result_text都為空的array元素
    foreach($_POST['jud_result_date'] as $k => $v){
        
        if (empty($_POST['jud_result_date'][$k]) && empty($_POST['jud_result_text'][$k])){
            unset($_POST['jud_result_date'][$k]);
            unset($_POST['jud_result_text'][$k]);
        }
    }
    //重設index
    $_POST['jud_result_date'] = array_values($_POST['jud_result_date']);
    $_POST['jud_result_text'] = array_values($_POST['jud_result_text']);
    $data['jud_result'] = json_encode($_POST);

    $result = $pdo->prepare($sql)->execute($data);
    if($result)
        header('Location: edit4.php?msg=審核求償決議結論成功&id='.$_GET['id']);
    else
        header('Location: edit4.php?msg=審核求償決議結論失敗&id='.$_GET['id']);
}


//_detail.php用
$pid = $_GET['id'];
$title = '司法院審核求償決議結論';
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
                <h3 class="text-center">刑事補償事件 - 司法院審核求償決議結論</h3>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-10 col-xs-offset-1">
                <form class="form-horizontal" action="edit4.php?id=<?=$_GET['id']?>" method="POST">
                    <div id="jud_result_list"><span class="glyphicon glyphicon-plus jud_result_add" style="cursor:pointer;color:green;"></span>
                            <?php if($jud_result != null && count($jud_result['jud_result_date']) != 0){ 
                                    for($i=0; $i<count($jud_result['jud_result_date']); $i++) {
                            ?>
                                        <div class="form-group jud_result_div">
                                            <label for="jud_result" class="col-xs-2 control-label"><span class="glyphicon glyphicon-remove jud_result_del" style="cursor:pointer;color:red;"></span>審核求償決議結論</label>
                                            <div class="col-xs-3">
                                                <input type="text" class="form-control jud_result_date" name="jud_result_date[]" value="<?=$jud_result['jud_result_date'][$i]?>" placeholder="審核求償決議結論日期">
                                            </div>
                                            <div class="col-xs-7">
                                                <input type="text"" class="form-control jud_result_text" name="jud_result_text[]" value="<?=$jud_result['jud_result_text'][$i]?>" placeholder="審核求償決議結論">
                                            </div>
                                        </div>
                            <?php }} ?>
                        <div class="form-group jud_result_div">
                            <label for="jud_result" class="col-xs-2 control-label"><span class="glyphicon glyphicon-remove jud_result_del" style="cursor:pointer;color:red;"></span>審核求償決議結論</label>
                            <div class="col-xs-3">
                                <input type="text" class="form-control jud_result_date" name="jud_result_date[]" placeholder="審核求償決議結論日期">
                            </div>
                            <div class="col-xs-7">
                                <input type="text"" class="form-control jud_result_text" name="jud_result_text[]" placeholder="審核求償決議結論">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-xs-offset-2 col-xs-6">
                            <button type="submit" class="btn btn-primary">審核求償決議結論</button>
                        </div>
                        <div class=" col-xs-4">
                            <a id="back3" href="back.php?progress=3&id=<?=$_GET['id']?>&url=<?=$url?>" class="btn btn-primary" style="background-color: rgb(161, 132, 105);">退回補償法院</a>
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
            var $jud_result_div = $('.jud_result_div').last().clone();
            $(".jud_result_date").datepickerTW(dpopt);
        }
        { //事件監聽
            //新增審查結果
            $('body').on('click', '.jud_result_add', function(){
                var $div = $jud_result_div.clone();
                $div.find('.jud_result_text').val('');
                $div.find('.jud_result_date').val('');
                $('#jud_result_list').append($div);
                $div.find('.jud_result_date').datepickerTW(dpopt);
            })
            //日期,修改為民國年
            //$('body').on('change', '.hasDatepicker', function(){
            //    $(this).val(yearsub1911($(this).val()));
            //})
            // $(".hasDatepicker").each(function(){
            //     if($(this).val())
            //         $(this).trigger("change");
            // });
            //刪除審查結果
            $('body').on('click', '.jud_result_del', function(){
                if($('.jud_result_div').length == 1) return;
                $(this).parents('.jud_result_div').remove();
            })
            //退回
            $('#back3').click(function(){
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