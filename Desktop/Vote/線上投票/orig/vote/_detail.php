<?php
$stmt = $pdo->prepare($selectfromCriminalpay."WHERE ID=:id");
$stmt->execute(['id' => $pid]); 
$pay = $stmt->fetch();
$check_result = json_decode($pay['check_result'], true);
$check_report = json_decode($pay['check_report'], true);
$committee_result = json_decode($pay['committee_result'], true);
$jud_result = json_decode($pay['jud_result'], true);
//var_dump($pay);

?>
<div class="row">
            <div class="col-xs-8 col-xs-offset-2">
                <table class='table'>
                    <tr>
                        <th class="progress0">填報法院</th>
                        <td class="<?= $pay['progress']==0?"progessing":"" ?>"><?= $crtmap[$pay['crtEng']] ?></td>
                    </tr>
                    <tr>
                        <th class="progress0">刑補案號</th>
                        <td class="<?= $pay['progress']==0?"progessing":"" ?>"><?= $pay['casenum'] ?></td>
                    </tr>
                    <tr>
                        <th class="progress0">收案日</th>
                        <td class="<?= $pay['progress']==0?"progessing":"" ?>"><?= ltrim($pay['receive_date1'],'0') ?></td>
                    </tr>
                    <tr>
                        <th class="progress0">補償決定日</th>
                        <td class="<?= $pay['progress']==0?"progessing":"" ?>"><?= ltrim($pay['decide_date1'],'0') ?>
                            <?= empty($pay['decide_expired'])?"":"<br>逾期原因: ".$pay['decide_expired'] ?>
                        </td>
                    </tr>
                    <tr>
                        <th class="progress0">送達最高檢察署日</th>
                        <td class="<?= $pay['progress']==0?"progessing":"" ?>"><?= ltrim($pay['sendmoj_date1'],'0') ?></td>
                    </tr>
                    <tr>
                        <th class="progress0">決定補償確定日</th>
                        <td class="<?= $pay['progress']==0?"progessing":"" ?>"><?= ltrim($pay['sure_date1'],'0') ?></td>
                    </tr>
                    <tr>
                        <th class="progress0">登載公報及報紙日</th>
                        <td class="<?= $pay['progress']==0?"progessing":"" ?>"><?= ltrim($pay['report_date1'],'0') ?></td>
                    </tr>
                    <tr>
                        <th class="progress0">終結情形</th>
                        <td class="<?= $pay['progress']==0?"progessing":"" ?>"><?= $endsituationmap[$pay['endsituation']] ?></td>
                    </tr>
                    <tr>
                        <th class="progress0">支付補償金日</th>
                        <td class="<?= $pay['progress']==0?"progessing":"" ?>"><?= ltrim($pay['pay_date1'],'0') ?></td>
                    </tr>
                    <tr>
                        <th class="progress0">函送<?= $crtmap[$pay['crtEngTo']] ?>審核日</th>
                        <td class="<?= $pay['progress']==0?"progessing":"" ?>"><?= ltrim($pay['send_date1'],'0') ?>
                            <?= empty($pay['send_expired'])?"":"<br>逾期原因: ".$pay['send_expired'] ?>
                        </td>
                    </tr>
                    <tr>
                        <th class="progress1">高院首次函報司法院日</th>
                        <td class="<?= $pay['progress']==1?"progessing":"" ?>"><?= ltrim($pay['sendjud_date1'],'0') ?></td>
                    </tr>
                    <tr>
                        <th class="progress1">高院審查結果</th>
                        <td class="<?= $pay['progress']==1?"progessing":"" ?>">
                        <?php if($check_result != null && count($check_result['check_result_date']) != 0){ 
                                for($i=0; $i<count($check_result['check_result_date']); $i++) {
                        ?>
                                    <p><?=($i+1).'. '?><?=$check_result['check_result_date'][$i]?> <?=$check_result['check_result_text'][$i]?></p>
                        <?php }} ?>
                        </td>
                    </tr>
                    <tr>
                        <th class="progress2">司法院收受審核報告日期及結果</th>
                        <td class="<?= $pay['progress']==2?"progessing":"" ?>">
                        <?php if($check_report != null && count($check_report['check_report_date']) != 0){ 
                                for($i=0; $i<count($check_report['check_report_date']); $i++) {
                        ?>
                                    <p><?=($i+1).'. '?><?=$check_report['check_report_date'][$i]?> <?=$check_report['check_report_text'][$i]?></p>
                        <?php }} ?>
                        </td>
                    </tr>
                    <tr>
                        <th class="progress3">補償法院首次召開求償委員會日期</th>
                        <td class="<?= $pay['progress']==3?"progessing":"" ?>"><?= ltrim($pay['committee_date1'],'0') ?>
                            <?= empty($pay['committee_expired'])?"":"<br>逾期原因: ".$pay['committee_expired'] ?>
                        </td>
                    </tr>
                    <tr>
                        <th class="progress3">求償委員會歷次會議日期、決議內容及層報日期</th>
                        <td class="<?= $pay['progress']==3?"progessing":"" ?>">
                        <?php if($committee_result != null && count($committee_result['committee_result_date']) != 0){ 
                                for($i=0; $i<count($committee_result['committee_result_date']); $i++) {
                        ?>
                                    <p><?=($i+1).'. '?><?=$committee_result['committee_result_date'][$i]?> <?=$committee_result['committee_result_text'][$i]?></p>
                        <?php }} ?>
                        </td>
                    </tr>
                    <tr>
                        <th class="progress4">司法院審核求償決議結論</th>
                        <td class="<?= $pay['progress']==4?"progessing":"" ?>">
                        <?php if($jud_result != null && count($jud_result['jud_result_date']) != 0){ 
                                for($i=0; $i<count($jud_result['jud_result_date']); $i++) {
                        ?>
                                    <p><?=($i+1).'. '?><?=$jud_result['jud_result_date'][$i]?> <?=$jud_result['jud_result_text'][$i]?></p>
                        <?php }} ?>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="no-print col-xs-8 col-xs-offset-2">
                <table class='table'>
                    <tr>
                        <th>進度</th>
                        <td>首次填寫人</td>
                        <td>最後編輯人</td>
                    </tr>
                    <?php
                    $d1 = new DateTime($pay['pay_time']);
                    $d1 = ltrim($d1->sub(new DateInterval('P1911Y'))->format('Y-m-d H:i'),'0');
                    $d2 = new DateTime($pay['e_pay_time']);
                    $d2 = ltrim($d2->sub(new DateInterval('P1911Y'))->format('Y-m-d H:i'),'0');
                    $d3 = new DateTime($pay['check_time']);
                    $d3 = ltrim($d3->sub(new DateInterval('P1911Y'))->format('Y-m-d H:i'),'0');
                    $d4 = new DateTime($pay['e_check_time']);
                    $d4 = ltrim($d4->sub(new DateInterval('P1911Y'))->format('Y-m-d H:i'),'0');
                    $d5 = new DateTime($pay['jud_time']);
                    $d5 = ltrim($d5->sub(new DateInterval('P1911Y'))->format('Y-m-d H:i'),'0');
                    $d6 = new DateTime($pay['e_jud_time']);
                    $d6 = ltrim($d6->sub(new DateInterval('P1911Y'))->format('Y-m-d H:i'),'0');
                    $d7 = new DateTime($pay['pay2_time']);
                    $d7 = ltrim($d7->sub(new DateInterval('P1911Y'))->format('Y-m-d H:i'),'0');
                    $d8 = new DateTime($pay['e_pay2_time']);
                    $d8 = ltrim($d8->sub(new DateInterval('P1911Y'))->format('Y-m-d H:i'),'0');
                    $d9 = new DateTime($pay['jud2_time']);
                    $d9 = ltrim($d9->sub(new DateInterval('P1911Y'))->format('Y-m-d H:i'),'0');
                    $d10 = new DateTime($pay['e_jud2_time']);
                    $d10 = ltrim($d10->sub(new DateInterval('P1911Y'))->format('Y-m-d H:i'),'0');
                    ?>
                    <tr>
                        <th class="progress0">受理刑補聲請法院填報 @0</th>
                        <td>
                            <?= empty($pay['pay_id'])?
                            "":$pay['pay_name'].$pay['pay_title'].'('.$pay['pay_crt'].' '.$pay['pay_dpt'].')<br>
                            '.$d1 ?>
                        </td>
                        <td>
                            <?= empty($pay['e_pay_id'])?
                            "":$pay['e_pay_name'].$pay['e_pay_title'].'('.$pay['e_pay_crt'].' '.$pay['e_pay_dpt'].')<br>
                            '.$d2 ?>
                        </td>
                    </tr>
                    <tr>
                        <th class="progress1">高院審查 @1</th>
                        <td>
                            <?= empty($pay['check_id'])?
                            "":$pay['check_name'].$pay['check_title'].'('.$pay['check_crt'].' '.$pay['check_dpt'].')<br>
                            '.$d3 ?>
                        </td>
                        <td>
                            <?= empty($pay['e_check_id'])?
                            "":$pay['e_check_name'].$pay['e_check_title'].'('.$pay['e_check_crt'].' '.$pay['e_check_dpt'].')<br>
                            '.$d4 ?>
                        </td>
                    </tr>
                    <tr>
                        <th class="progress2">司法院收受審核報告 @2</th>
                        <td>
                            <?= empty($pay['jud_id'])?
                            "":$pay['jud_name'].$pay['jud_title'].'('.$pay['jud_crt'].' '.$pay['jud_dpt'].')<br>
                            '.$d5 ?>
                        </td>
                        <td>
                            <?= empty($pay['e_jud_id'])?
                            "":$pay['e_jud_name'].$pay['e_jud_title'].'('.$pay['e_jud_crt'].' '.$pay['e_jud_dpt'].')<br>
                            '.$d6 ?>
                        </td>
                    </tr>
                    <tr>
                        <th class="progress3">補償法院召開求償委員會 @3</th>
                        <td>
                            <?= empty($pay['pay2_id'])?
                            "":$pay['pay2_name'].$pay['pay2_title'].'('.$pay['pay2_crt'].' '.$pay['pay2_dpt'].')<br>
                            '.$d7 ?>
                        </td>
                        <td>
                            <?= empty($pay['e_pay2_id'])?
                            "":$pay['e_pay2_name'].$pay['e_pay2_title'].'('.$pay['e_pay2_crt'].' '.$pay['e_pay2_dpt'].')<br>
                            '.$d8 ?>
                        </td>
                    </tr>
                    <tr>
                        <th class="progress4">司法院審核求償決議結論 @4</th>
                        <td>
                            <?= empty($pay['jud2_id'])?
                            "":$pay['jud2_name'].$pay['jud2_title'].'('.$pay['jud2_crt'].' '.$pay['jud2_dpt'].')<br>
                            '.$d9 ?>
                        </td>
                        <td>
                            <?= empty($pay['e_jud2_id'])?
                            "":$pay['e_jud2_name'].$pay['e_jud2_title'].'('.$pay['e_jud2_crt'].' '.$pay['e_jud2_dpt'].')<br>
                            '.$d10 ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>