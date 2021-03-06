﻿<?php
return array(
	//'配置项'=>'配置值'
	'SHOW_PAGE_TRACE'       =>false,//显示页面Trace信息false
    'SESSION_AUTO_START'    => true,//开启SESSION
    'URL_MODEL'             => '1',//URL模式
    'MODULE_ALLOW_LIST'     => array('Report','Record'),//设置允许模块
    'DEFAULT_MODULE'        => 'Report',//设置默认模块设置
    'MODULE_DENY_LIST'      => array('Common','Runtime'), // 禁止访问的模块列表
//     'LAYOUT_ON'            =>true,//开启模板布局
//     'LAYOUT_NAME'          =>'layout',//布局模板文件为：layout
    'URL_CASE_INSENSITIVE'  =>true, //不区分大小写
    'ONLINE'                =>0,                   //是否为线上环境，0：测试环境；1：生产环境
    //修改定界符
    'TMPL_L_DELIM'          =>'<{',
    'TMPL_R_DELIM'          =>'}>',
    //数据库设置（云端）
    'DB_TYPE'               =>'mysql',
    'DB_HOST'               =>'2lbrknae.2248.dnstoo.com',
    'DB_NAME'               =>'xiuli',
    'DB_USER'               =>'xiuli_f',
    'DB_PWD'                =>'yaolihui0506',
    'DB_PORT'               =>'5503',
    'DB_PREFIX'             =>'zt_',
    'DB_CHARSET'            => 'utf8', // 字符集

    //错误设置
    'ERROR_MESSAGE'         =>  '页面错误！请稍后再试～',//错误显示信息,非调试模式有效
    'ERROR_PAGE'            =>  '', // 错误定向页面
    'SHOW_ERROR_MSG'        =>  false,    // 显示错误信息
    'TRACE_MAX_RECORD'      =>  100,    // 每个级别的错误信息 最大记录数
);