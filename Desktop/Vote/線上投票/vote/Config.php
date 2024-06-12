<?php

class Config {
    //自訂常量
    const sysname = '司法院線上投票系統';
    const SysId = 'vote'; //AD單一登入,系統代號
    const adurl = 'http://webad.intraj/ssob/getUserInfo.aspx?ver=2&TICKET='; //AD單一登入,取得使用者資訊網址
    const webroot = '/vote/';
    const cookie_exp = 36000; //cookie過期時間 10(小時) *3600

    
    //資料庫連線-正式機
    const host    = '210.69.124.171';
    const user    = 'yida';
    const pwd     = '!QAZ7ujm';
    const db      = 'vote';

    //後台管理員帳密
    //const adminaccount     = 'admin';
    //const adminpswd      = 'tpj666';

    //後台管理員帳密
    const adminArray = [
        //'帳號' => ['密碼','單位']
        'admin' => ['tpj666','人事處'],
        'admin456' => ['tpj666','資訊處'],
    ];
}
