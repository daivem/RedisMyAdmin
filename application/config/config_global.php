<?php

$config['project_name'] = 'RedisMyAdmin';
$config['version'] = '0.3.3';

/*
 * 建树时的分隔符
 * 官方推荐使用冒号“:”
 */
$config['seperator'] = ':';

/*
 * KEY的最大长度
 */
$config['max_key_len'] = 200;


/*
 * 快速模式
 * （开启后不显示单个KEY的Item总数）
 */
$config['faster_model'] = 1;


$config['idle_key'] = '___idle___';

/*
 * 建树时的一个标志
 */
$config['tree_end_sign'] = '__rda__';

/*
 * 临界点
 * 当KEY数量超过此阀值的时候需要手动刷新
 * 设为0则关闭此功能
 */
$config['db_size_critical'] = 10000;

/*
 * 建树时每次读取的个数
 */
$config['tree_key_page_size'] = 1000;

