<?php
require_once('init.php');
error_reporting(0);
//ini_set('display_errors', TRUE);
//ini_set('display_startup_errors', TRUE);
//date_default_timezone_set('Asia/Taipei');
//if (PHP_SAPI == 'cli')
//	die('This example should only be run from a Web Browser');
require_once dirname(__FILE__) . '/lib/PHPExcel/PHPExcel.php';

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

//var_dump($pays);

$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("司法院資訊處")
							 ->setLastModifiedBy("司法院資訊處")
							 ->setTitle("刑事補償事件審查控管表")
							 ->setSubject("刑事補償事件審查控管表")
							 ->setDescription("刑事補償事件審查控管表")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("刑事補償事件審查控管表");

$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '填報機關')
            ->setCellValue('B1', '刑補案號')
            ->setCellValue('C1', '收案日')
            ->setCellValue('D1', '補償決定日')
            ->setCellValue('E1', '送達最高檢察署日')
            ->setCellValue('F1', '決定補償確定日')
            ->setCellValue('G1', '登載公報及報紙日')
            ->setCellValue('H1', '終結情形')
            ->setCellValue('I1', '支付補償金日')
            ->setCellValue('J1', '函送高院審核日')
            ->setCellValue('K1', '高院首次函報司法院日')
            ->setCellValue('L1', '高院審查結果')
            ->setCellValue('M1', '收受審核報告日期及結果')
            ->setCellValue('N1', '首次召開求償委員會日')
            ->setCellValue('O1', '求償委員會歷次會議')
            ->setCellValue('P1', '審核求償決議結論');
$r = 2;
foreach ($pays as $p) {
    $check_result = json_decode($p['check_result'], true);
    $check_report = json_decode($p['check_report'], true);
    $committee_result = json_decode($p['committee_result'], true);
    $jud_result = json_decode($p['jud_result'], true);

    $j1 = "";
    $j2 = "";
    $j3 = "";
    $j4 = "";

    if($check_result != null && count($check_result['check_result_date']) != 0){
        for($i=0; $i<count($check_result['check_result_date']); $i++) {
            $j1 = $j1. ($i+1).'. '.$check_result['check_result_date'][$i].$check_result['check_result_text'][$i];
            if($i != count($check_result['check_result_date'])-1)
                $j1 = $j1.PHP_EOL;
        }
    }
    if($check_report != null && count($check_report['check_report_date']) != 0){
        for($i=0; $i<count($check_report['check_report_date']); $i++) {
            $j2 = $j2. ($i+1).'. '.$check_report['check_report_date'][$i].$check_report['check_report_text'][$i];
            if($i != count($check_report['check_report_date'])-1)
                $j2 = $j2.PHP_EOL;
        }
    }
    if($committee_result != null && count($committee_result['committee_result_date']) != 0){
        for($i=0; $i<count($committee_result['committee_result_date']); $i++) {
            $j3 = $j3. ($i+1).'. '.$committee_result['committee_result_date'][$i].$committee_result['committee_result_text'][$i];
            if($i != count($committee_result['committee_result_date'])-1)
                $j3 = $j3.PHP_EOL;
        }
    }
    if($jud_result != null && count($jud_result['jud_result_date']) != 0){
        for($i=0; $i<count($jud_result['jud_result_date']); $i++) {
            $j4 = $j4. ($i+1).'. '.$jud_result['jud_result_date'][$i].$jud_result['jud_result_text'][$i];
            if($i != count($jud_result['jud_result_date'])-1)
                $j4 = $j4.PHP_EOL;
        }
    }
    //var_dump($j1);
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$r, $crtmap[$p['crtEng']])
            ->setCellValue('B'.$r, $p['casenum'])
            ->setCellValue('C'.$r, ltrim($p['receive_date1'],'0'))
            ->setCellValue('D'.$r, ltrim($p['decide_date1'],'0'))
            ->setCellValue('E'.$r, ltrim($p['sendmoj_date1'],'0'))
            ->setCellValue('F'.$r, ltrim($p['sure_date1'],'0'))
            ->setCellValue('G'.$r, ltrim($p['report_date1'],'0'))
            ->setCellValue('H'.$r, $endsituationmap[$p['endsituation']])
            ->setCellValue('I'.$r, ltrim($p['pay_date1'],'0'))
            ->setCellValue('J'.$r, ltrim($p['send_date1'],'0'))
            ->setCellValue('K'.$r, ltrim($p['sendjud_date1'],'0'))
            ->setCellValue('L'.$r, $j1)
            ->setCellValue('M'.$r, $j2)
            ->setCellValue('N'.$r, ltrim($p['committee_date1'],'0'))
            ->setCellValue('O'.$r, $j3)
            ->setCellValue('P'.$r, $j4);
    //$objPHPExcel->getActiveSheet()->getStyle('L'.$r)->getAlignment()->setWrapText(true);
    $r++;
}

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('表1');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
$BStyle = array(
    'borders' => array(
      'allborders' => array(
        'style' => PHPExcel_Style_Border::BORDER_THIN
      )
    )
  );
$objPHPExcel->getDefaultStyle()->applyFromArray($BStyle);

$objPHPExcel->getActiveSheet()->getStyle(
    'A1:' . 
    $objPHPExcel->getActiveSheet()->getHighestColumn() . 
    $objPHPExcel->getActiveSheet()->getHighestRow()
)->applyFromArray($BStyle);

// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="criminalpay.xlsx"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;