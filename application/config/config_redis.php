<?php
//服务器列表
$config['server_list'] = array(
	array(
		'name' => 'localhost server', 
		'host' => '127.0.0.1',
		'port' => 6379,
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
