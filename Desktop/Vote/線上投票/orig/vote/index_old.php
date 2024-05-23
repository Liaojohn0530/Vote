<?php
require_once('init.php');
$title = '首頁';
$sql = $selectfromCriminalpay."WHERE (crtEng = '{court}' OR crtEngTo = '{court}') {wheredate} {wherecrt} ORDER BY receive_date DESC";
$sql = str_replace('{court}', $_COOKIE['court'], $sql);

if($_COOKIE['court'] == 'TPJ')//司法院能看到所有填報
    $sql = $selectfromCriminalpay."WHERE 1=1 {wheredate} {wherecrt} ORDER BY receive_date DESC";
//補償決定日區間
$wheredate = "";
if(!empty($_GET['startdate']) && !empty($_GET['enddate'])){
    $wheredate = "AND decide_date between '".$_GET['startdate']."' and '".$_GET['enddate']." 23:59:59'";
}
$sql = str_replace('{wheredate}', $wheredate, $sql);
//補償決定日區間
$wherecrt = "";
if(!empty($_GET['crt'])){
    $wherecrt = "AND crtEng = '".$_GET['crt']."' ";
}
$sql = str_replace('{wherecrt}', $wherecrt, $sql);
$stmt = $pdo->query($sql);
$pays = $stmt->fetchAll();
//計算被退回數
$backcount = 0;
if($_COOKIE['court'] == 'TPJ')//司法院不會被退回
    $backcount = 0;
else if(in_array($_COOKIE['court'], ['TPH','KMH'])){//高院計算back1欄位
    foreach($pays as $v)
        if($v['back1'] == 1)
            $backcount++;
} else { //地院計算back0,back3欄位
    foreach($pays as $v){
        if($v['back0'] == 1)
            $backcount++;
        if($v['back3'] == 1)
            $backcount++;
    }
}
//只顯示被退回的案件
if(isset($_GET['isback']) && $_GET['isback']==1){
    $temp = $pays;
    $pays = [];
    if($_COOKIE['court'] == 'TPJ'){//司法院不會被退回
    }
    else if(in_array($_COOKIE['court'], ['TPH','KMH'])){//高院
        foreach($temp as $v)
            if($v['back1'] == 1)
                $pays[] = $v;
    } else { //地院
        foreach($temp as $v){
            if($v['back0'] == 1 || $v['back3'] == 1)
                $pays[] = $v;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-Hant-TW">

<head>
    <?php include '_head.php'; ?>
    <style>
    body {
        font-weight: bold;
        font-size: 14px;
    }
    .jsondetail {
        display:none;
    }
	@media print
	{    
		.no-print, .no-print *
		{
			display: none !important;
		}
		.printtitle {
			display: block !important;
		}
		th.sorting::after,.dataTables_length,.dataTables_filter,.dataTables_info,.dataTables_paginate {
			display: none !important;
		}
		table,th,td,tr,.table>tbody>tr>td,.table>thead>tr>th{
			border: 1px solid black !important;
		}
	}
	table,th,td,tr,.table>tbody>tr>td,.table>thead>tr>th{
			border: 1px solid black !important;
		}
    </style>
</head>

<body>
    <?php include '_nav.php'; ?>
    <div class="container-fluid">
		<h2 class="printtitle text-center hide">刑事補償事件審查控管</h2>
        <div class="row no-print">
            <div class="col-xs-12">
                <a id="addpay" class="btn btn-success pull-left" href="add.php">填報</a>
                <!--<a id="printtable" href="indexexcel.php?crt=<?= empty($_GET['crt'])?"":$_GET['crt'] ?>&startdate=<?= empty($_GET['startdate'])?"":$_GET['startdate'] ?>&enddate=<?= empty($_GET['enddate'])?"":$_GET['enddate'] ?>" class="btn btn-sm btn-warning pull-right" target="_blank">匯出</a>-->
                <button onclick="window.print();" id="" class="btn btn-sm btn-warning pull-right" >匯出</a>
				<button id="changedetail" show="0" class="btn btn-sm btn-info pull-right">切換詳細</button>
                
                <?php if(in_array($_COOKIE['court'], ['TPJ','TPH','KMH'])){ //司法院、高院才顯示?>
                <form id="searchform" class="form-inline text-center" method="GET">
                    <input type="hidden" name="isback" value="<?=(isset($_GET['isback'])&&$_GET['isback']==1)?1:0?>">
                    <div class="form-group">
                        <label for="">補償法院：</label>
                        <select name="crt" class="form-control">
                            <option value="">全部</option>
                            <?php
                            $startdate = "";
                            $enddate = "";
                            //西元修改為民國
                            if(!empty($_GET['startdate'])){
                                $date = new DateTime($_GET['startdate']);
                                $date->modify('-1911 year');
                                $startdate = ltrim($date->format('Y-m-d'),'0');
                            }
                            if(!empty($_GET['enddate'])){
                                $date = new DateTime($_GET['enddate']);
                                $date->modify('-1911 year');
                                $enddate = ltrim($date->format('Y-m-d'),'0');
                            }
                            foreach ($crtmap as $k => $v) {
                            ?>
                                <option value="<?= $k ?>" <?= isset($_GET['crt'])&&$_GET['crt']==$k?"selected":"" ?>><?= $v ?></option>
                            <?php } ?>
                          </select>
                    </div>
                    <div class="form-group">
                        <label for="">  補償決定日區間：</label>
                        <input type="text" class="hasdp form-control" name="startdate" id=""
                            placeholder="起始日期" value="<?= $startdate ?>">~
                            <input type="text" class="hasdp form-control" name="enddate" id=""
                            placeholder="結束日期" value="<?= $enddate ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">查詢</button>
                </form>
                <?php } ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="">
                    <table id="paytable" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th class="tableexport-ignore text-center progress0" colspan="10">受理刑補聲請法院 @0</th>
                                <th class="tableexport-ignore text-center progress1" colspan="2">臺高院、金門高分院 @1</th>
                                <th class="tableexport-ignore text-center progress2" colspan="1">司法院 @2</th>
                                <th class="tableexport-ignore text-center progress3" colspan="2">補償法院 @3</th>
                                <th class="tableexport-ignore text-center progress4" colspan="1">司法院 @4</th>
                                <th class="tableexport-ignore no-print" rowspan="2">操作</th>
                                <th style="display:none;"></th>
                            </tr>
                            <tr>
                                <th>填報機關</th>
                                <th>刑補案號</th>
                                <th>收案日</th>
                                <th>補償決定日</th>
                                <th>送達最高檢察署日</th>
                                <th>決定補償確定日</th>
                                <th>登載公報及報紙日</th>
                                <th>終結情形</th>
                                <th>支付補償金日</th>
                                <th>函送高院審核日</th>

                                <th>高院首次函報司法院日</th>
                                <th>高院審查結果</th>

                                <th>收受審核報告日期及結果</th>

                                <th>首次召開求償委員會日</th>
                                <th>求償委員會歷次會議</th>

                                <th>審核求償決議結論</th>
                                <th style="display:none;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            foreach ($pays as $p) {
                                $check_result = json_decode($p['check_result'], true);
                                $check_report = json_decode($p['check_report'], true);
                                $committee_result = json_decode($p['committee_result'], true);
                                $jud_result = json_decode($p['jud_result'], true);
                            ?>
                            <tr>
                                <td class="<?= $p['progress']==0?"progessing":"" ?>"><?= $crtmap[$p['crtEng']] ?></td>
                                <td class="<?= $p['progress']==0?"progessing":"" ?>"><?= $p['casenum'] ?></td>
                                <td class="tableexport-string <?= $p['progress']==0?"progessing":"" ?>">
                                    <?= ltrim($p['receive_date1'],'0') ?></td>
                                <td class="tableexport-string <?= $p['progress']==0?"progessing":"" ?>">
                                    <?= ltrim($p['decide_date1'],'0') ?>
                                    <?= empty($p['decide_expired'])?'':'逾期原因:'.$p['decide_expired'] ?>
                                    </td>
                                <td class="tableexport-string <?= $p['progress']==0?"progessing":"" ?>">
                                    <?= ltrim($p['sendmoj_date1'],'0') ?></td>
                                <td class="tableexport-string <?= $p['progress']==0?"progessing":"" ?>"><?= ltrim($p['sure_date1'],'0') ?>
                                </td>
                                <td class="tableexport-string <?= $p['progress']==0?"progessing":"" ?>">
                                    <?= ltrim($p['report_date1'],'0') ?></td>
                                <td class="<?= $p['progress']==0?"progessing":"" ?>">
                                    <?= $endsituationmap[$p['endsituation']] ?></td>
                                <td class="tableexport-string <?= $p['progress']==0?"progessing":"" ?>"><?= ltrim($p['pay_date1'],'0') ?>
                                </td>
                                <td class="tableexport-string <?= $p['progress']==0?"progessing":"" ?>"><?= ltrim($p['send_date1'],'0') ?>
                                <?= empty($p['send_expired'])?'':'逾期原因:'.$p['send_expired'] ?>
                                </td>

                                <td class="tableexport-string <?= $p['progress']==1?"progessing":"" ?>">
                                    <?= ltrim($p['sendjud_date1'],'0') ?></td>
                                <td class="<?= $p['progress']==1?"progessing":"" ?>">
                                
                                <?php if($check_result != null && count($check_result['check_result_date']) != 0){ ?>
                                    <div class="jsondetail">
                                <?php for($i=0; $i<count($check_result['check_result_date']); $i++) {
                                ?>
                                            <p><?=($i+1).'. '?><?=$check_result['check_result_date'][$i]?> <?=$check_result['check_result_text'][$i]?></p>
                                <?php } ?>
                                </div><button class="showjson btn btn-xs btn-default">顯示</button>
                                <?php } ?>
                                </td>
                                <td class="<?= $p['progress']==2?"progessing":"" ?>">
                                
                                <?php if($check_report != null && count($check_report['check_report_date']) != 0){ ?>
                                    <div class="jsondetail">
                                <?php for($i=0; $i<count($check_report['check_report_date']); $i++) {
                                ?>
                                            <p><?=($i+1).'. '?><?=$check_report['check_report_date'][$i]?> <?=$check_report['check_report_text'][$i]?></p>
                                <?php } ?>
                                </div><button class="showjson btn btn-xs btn-default">顯示</button>
                                <?php } ?>
                                </td>
                                <td class="tableexport-string <?= $p['progress']==3?"progessing":"" ?>">
                                    <?= ltrim($p['committee_date1'],'0') ?>
                                    <?= empty($p['committee_expired'])?'':'逾期原因:'.$p['committee_expired'] ?>
                                    </td>
                                <td class="<?= $p['progress']==3?"progessing":"" ?>">
                                
                                <?php if($committee_result != null && count($committee_result['committee_result_date']) != 0){ ?>
                                    <div class="jsondetail">
                                <?php for($i=0; $i<count($committee_result['committee_result_date']); $i++) {
                                ?>
                                            <p><?=($i+1).'. '?><?=$committee_result['committee_result_date'][$i]?> <?=$committee_result['committee_result_text'][$i]?></p>
                                <?php } ?>
                                </div><button class="showjson btn btn-xs btn-default">顯示</button>
                                <?php } ?>
                                </td>
                                <td class="<?= $p['progress']==4?"progessing":"" ?>">
                                
                                <?php if($jud_result != null && count($jud_result['jud_result_date']) != 0){ ?>
                                    <div class="jsondetail">
                                <?php for($i=0; $i<count($jud_result['jud_result_date']); $i++) {
                                ?>
                                            <p><?=($i+1).'. '?><?=$jud_result['jud_result_date'][$i]?> <?=$jud_result['jud_result_text'][$i]?></p>
                                <?php } ?>
                                </div><button class="showjson btn btn-xs btn-default">顯示</button>
                                <?php } ?>
                                </td>
                                <td class="tableexport-ignore no-print">
                                    <a class="btn btn-xs btn-success" href="detail.php?id=<?=$p['ID']?>">詳細</a>
                                    <?php if(((in_array($p['progress'], [0,1,2,3,4])||$p['back0']==1) && $p['crtEng'] == $_COOKIE['court'])||'TPJ' == $_COOKIE['court']){ ?>
                                    <a class="btn btn-xs btn-success progress0"
                                        href="edit0.php?id=<?=$p['ID']?>"><?=$p['back0']==1?"<span class='backmark'>退</span>":""?>編輯填報0</a>
                                    <?php } ?>
                                    <?php if(in_array($p['progress'], [0]) && $p['back0']==0 && $p['crtEng'] == $_COOKIE['court']){ ?>
                                    <a class="btn btn-xs btn-danger delete_pay"
                                        href="delete.php?id=<?=$p['ID']?>">刪除</a>
                                    <?php } ?>
                                    <?php if(((in_array($p['progress'], [0,1,2,3,4])||$p['back1']==1) && $p['crtEngTo'] == $_COOKIE['court'])||'TPJ' == $_COOKIE['court']){ ?>
                                    <a class="btn btn-xs btn-warning progress1" href="edit1.php?id=<?=$p['ID']?>"><?=$p['back1']==1?"<span class='backmark'>退</span>":""?>高院審查1</a>
                                    <?php } ?>
                                    <?php if((in_array($p['progress'], [1,2,3,4]) && 'TPJ' == $_COOKIE['court'])||'TPJ' == $_COOKIE['court']){ ?>
                                    <a class="btn btn-xs btn-info progress2" href="edit2.php?id=<?=$p['ID']?>">收受審核報告2</a>
                                    <?php } ?>
                                    <?php if(((in_array($p['progress'], [2,3,4,1])||$p['back3']==1) && $p['crtEng'] == $_COOKIE['court'])||'TPJ' == $_COOKIE['court']){ ?>
                                    <a class="btn btn-xs btn-warning progress3" href="edit3.php?id=<?=$p['ID']?>"><?=$p['back3']==1?"<span class='backmark'>退</span>":""?>召開求償委員會3</a>
                                    <?php } ?>
                                    <?php if((in_array($p['progress'], [3,4,2]) && 'TPJ' == $_COOKIE['court'])||'TPJ' == $_COOKIE['court']){ ?>
                                    <a class="btn btn-xs btn-warning progress4" href="edit4.php?id=<?=$p['ID']?>">審核求償決議結論4</a>
                                    <?php } ?>
                                </td>
                                <td style='display:none;'>
                                <?php 
                                for($i = 0; $i <= $p['progress']; $i++){
                                    echo '@'.$i;
                                }
                                ?>
                                </td>
                            </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php include '_footer.php'; ?>
    <?php include '_js.php'; ?>
    <script src='js/xlsx.full.min.js' type="text/javascript"></script>
    <script src='js/FileSaver.min.js' type="text/javascript"></script>
    <script src='js/tableexport.min.js' type="text/javascript"></script>
    <script>
        $(".hasdp").datepickerTW(dpopt);
        //日期,修改為民國年 
        //$('body').on('change', '.hasdp', function(){
        //    $(this).val(yearsub1911($(this).val()));
        //})
        //表單提交, 交民國修改回西元年
        $('#searchform').submit(function(){
            $(".hasdp").each(function(){
                if($(this).val())
                    $(this).val(yearadd1911($(this).val()));
                    //console.log($(this).val());
            });
        })
        $('#paytable').DataTable({
            "order": [[2, "desc"]],
            responsive: true,
			"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            "language": {
                "lengthMenu": "顯示 _MENU_ 筆事件",
                "zeroRecords": "未搜尋到任何結果",
                "emptyTable": "無任何紀錄",
                "info": " _PAGE_ / _PAGES_ 頁, 共_TOTAL_筆",
                "infoEmpty": "無任何結果可顯示",
                "infoFiltered": "(從 _MAX_ 筆事件過濾)",
                "search": "搜尋:",
                "paginate": {
                    "first": "首頁",
                    "last": "末頁",
                    "next": "下頁",
                    "previous": "上頁"
                },
            }
        });
        $('body').on('click', '.delete_pay', function () {
            if (!confirm('確定要刪除嗎? 無法復原操作'))
                return false;
        });
        //顯示詳細
        $('body').on('click', '.showjson', function () {
            $(this).prev('.jsondetail').show();
            $(this).hide();
        });
        //切換顯示詳細
        $('#changedetail').click(function(){
            if($(this).attr('show') == '1'){
                $(this).attr('show', '0');
                $('.jsondetail').hide();
                $('.showjson').show();
            } else {
                $(this).attr('show', '1');
                $('.jsondetail').show();
                $('.showjson').hide();
            }
        });
        var table = TableExport(document.getElementById("paytable"), {
                headers: true,                      // (Boolean), display table headers (th or td elements) in the <thead>, (default: true)
                footers: true,                      // (Boolean), display table footers (th or td elements) in the <tfoot>, (default: false)
                formats: ["xlsx"],    // (String[]), filetype(s) for the export, (default: ['xlsx', 'csv', 'txt'])
                filename: '刑事補償事件審查控管表',   // (id, String), filename for the downloaded file, (default: 'id')
                bootstrap: false,                   // (Boolean), style buttons using bootstrap, (default: true)
                exportButtons: false,                // (Boolean), automatically generate the built-in export buttons for each of the specified formats (default: true)
                position: "bottom",                 // (top, bottom), position of the caption element relative to table, (default: 'bottom')
                ignoreRows: null,                   // (Number, Number[]), row indices to exclude from the exported file(s) (default: null)
                ignoreCols: null,                   // (Number, Number[]), column indices to exclude from the exported file(s) (default: null)
                trimWhitespace: true,               // (Boolean), remove all leading/trailing newlines, spaces, and tabs from cell text in the exported file(s) (default: false)
                RTL: false,                         // (Boolean), set direction of the worksheet to right-to-left (default: false)
                sheetname: "工作表1"                    // (id, String), sheet name for the exported spreadsheet, (default: 'id')
            });
            //$('button.xlsx').text('匯出').addClass('btn btn-sm btn-warning pull-right').insertAfter("#addpay");
            //$('button.xlsx').last().hide();
    </script>
</body>

</html>