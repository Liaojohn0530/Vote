<?php
require_once('init.php');
error_reporting(0);
require_once dirname(__FILE__) . '/lib/PHPExcel/PHPExcel.php';

$stmt = $pdo->prepare($selectfromCriminalpay."WHERE ID=:id");
$stmt->execute(['id' => $_GET['id']]); 
$pay = $stmt->fetch();
$check_result = json_decode($pay['check_result'], true);
$check_report = json_decode($pay['check_report'], true);
$committee_result = json_decode($pay['committee_result'], true);
$jud_result = json_decode($pay['jud_result'], true);
//var_dump($pay);

$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator("司法院資訊處")
							 ->setLastModifiedBy("司法院資訊處")
							 ->setTitle("刑事補償事件審查控管表")
							 ->setSubject("刑事補償事件審查控管表")
							 ->setDescription("刑事補償事件審查控管表")
							 ->setKeywords("office 2007 openxml php")
                             ->setCategory("刑事補償事件審查控管表");
$objPHPExcel->setActiveSheetIndex(0)
->setCellValue('A1', '填報機關')
->setCellValue('A2', '刑補案號')
->setCellValue('A3', '收案日')
->setCellValue('A4', '補償決定日')
->setCellValue('A5', '送達最高檢察署日')
->setCellValue('A6', '決定補償確定日')
->setCellValue('A7', '登載公報及報紙日')
->setCellValue('A8', '終結情形')
->setCellValue('A9', '支付補償金日')
->setCellValue('A10', '函送'.$crtmap[$pay['crtEngTo']].'審核日')
->setCellValue('A11', '高院首次函報司法院日')
->setCellValue('A12', '高院審查結果')
->setCellValue('A13', '收受審核報告日期及結果')
->setCellValue('A14', '首次召開求償委員會日')
->setCellValue('A15', '求償委員會歷次會議')
->setCellValue('A16', '審核求償決議結論');

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

$objPHPExcel->setActiveSheetIndex(0)
->setCellValue('B1', $crtmap[$pay['crtEng']])
->setCellValue('B2', $pay['casenum'])
->setCellValue('B3', ltrim($pay['receive_date1'],'0'))
->setCellValue('B4', ltrim($pay['decide_date1'],'0').(empty($pay['decide_expired'])?"":PHP_EOL."逾期原因: ".$pay['decide_expired']))
->setCellValue('B5', ltrim($pay['sendmoj_date1'],'0'))
->setCellValue('B6', ltrim($pay['sure_date1'],'0'))
->setCellValue('B7', ltrim($pay['report_date1'],'0'))
->setCellValue('B8', $endsituationmap[$pay['endsituation']])
->setCellValue('B9', ltrim($pay['pay_date1'],'0'))
->setCellValue('B10', ltrim($pay['send_date1'],'0').(empty($pay['send_expired'])?"":PHP_EOL."逾期原因: ".$pay['send_expired']))
->setCellValue('B11', ltrim($pay['sendjud_date1'],'0'))
->setCellValue('B12', $j1)
->setCellValue('B13', $j2)
->setCellValue('B14', ltrim($pay['committee_date1'],'0').(empty($pay['committee_expired'])?"":PHP_EOL."逾期原因: ".$pay['committee_expired']))
->setCellValue('B15', $j3)
->setCellValue('B16', $j4);


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