<?php
return array(
	//'配置项'=>'配置值'
	//'MODULE_ALLOW_LIST' => array('Home','Parent','Teacher','Leader'),
	'DEFAULT_MODULE' => 'Member',
	//数据库配置
	'DB_TYPE' => 'mysql',
	'DB_HOST' => '127.0.0.1',
	'DB_NAME' => 'smartmalls',
	'DB_USER' => 'root',
	'DB_PWD'  => 'lizhao1234',  //测试服120
	//'DB_PWD'  => '',
	'DB_PORT' => '3306',
	'DB_PREFIX' => '',
	'DB_CHARSET' => 'utf8',
	'DB_DEBUG' => TRUE,

	'APP_SUB_DOMAIN_DEPLOY' => 1,
	'DEFAULT_PIC' => '',

	'NO_AUTH' => '非法操作！',
	'DEFAULT_PASSWORD' => '888888',



	'MALL_ATTANDANCE_EQUIPMENT_IP'=>'127.0.0.1',//考勤服务地址
	'SOCKETS_IP'=>'129.168.0.1',
	'SOCKET'=>'8885',
	'WORKER_AUTH_TIME'=>'0,0:00-23:59;1,0:00-23:59;2,0:00-23:59;3,0:00-23:59;4,0:00-23:59;5,0:00-23:59;6,0:00-23:59'

);