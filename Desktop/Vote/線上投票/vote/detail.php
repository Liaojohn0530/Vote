<?php
require_once('init.php');
if(empty($_GET['id']))
    header('Location: index.php?msg=ID為空');
//_detail.php用
$pid = $_GET['id'];

$title = '詳細內容';
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
    </style>
</head>
<body>
    <?php include '_nav.php'; ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-8 col-xs-offset-2">
                <h3 class="text-center">刑事補償事件 - 詳細內容
                <button onclick="window.print();" id="" class="no-print btn btn-sm btn-warning pull-right" >匯出</a>
                <!--<a href="detailexcel.php?id=<?= $_GET['id'] ?>" class="pull-right btn btn-sm btn-primary" target="_blank">匯出</a>-->
                </h3>
            </div>
        </div>
        <?php include '_detail.php'; ?>
    </div>
    <?php include '_footer.php'; ?>
    <?php include '_js.php'; ?>
</body>
</html>