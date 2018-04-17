<?php
return array(
    //'配置项'=>'配置值'
    'TMPL_PARSE_STRING' => array(
        '__CSS__'       => __ROOT__ . '/Public/css/',
        '__JS__'        => __ROOT__ . '/Public/js/',
        '__IMG__'       => __ROOT__ . '/Public/images/',
        '__IMAGES__'    => __ROOT__ . '/Public/images/Record',
        '__UPLOAD__'    => __ROOT__ . '/Upload/'
    ),
    'DEFAULT_THEME'     => 'default',//开启模板
    'PRODUCT'           => 'Record',//定义产品编号
    'QC_TESTER'=>array(
        'yaolihui',
        'fanqiao',
        'wangchenzi',
        'menghuihui',
        'lixm',
        'qinzx',
    ),
    'KH_QUARTER'=>'2018Q1-Q2',//考核周期
    'DB_HOST' => '192.168.155.54',
    'DB_NAME' => 'zentao171226',
    'DB_USER' => 'root',
    'DB_PWD' => 'chexian',
    'DB_PORT' => '3306',

);