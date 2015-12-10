<?php
//服务器列表
$config['server_list'] = array(
	array(
		'name' => 'localhost server', 
		'host' => '127.0.0.1',
		'port' => 6379,
		'auth' => FALSE, //如无密码，可不设置此键，或将值设置为FALSE or NULL
	),
	/*
	 * 集群服务器
	 */
	/*
	array(
		'name' => 'cluster server',
		'cluster_list' => array(
			'127.0.0.1:7000',
			'127.0.0.1:7001',
			'127.0.0.1:7002',
			'127.0.0.1:7010',
			'127.0.0.1:7011',
			'127.0.0.1:7012',
		),
	),
	*/
);
