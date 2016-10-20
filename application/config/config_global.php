<?php

$config['project_name'] = 'RedisMyAdmin';
$config['version'] = '0.4.2';

/**
 * PHP执行时间
 * set_time_limit(xxx);
 * 设置为 -1 则不执行此命令，使用php.ini设置的执行时间
 * 设置为 0 则不限制时间（不建议）
 */
$config['set_time_limit'] = 60;

/**
 * PHP内存限制
 * ini_set('memory_limit', '128M');
 * 设置为空则不执行此命令，使用php.ini中设置的值
 */
$config['memory_limit'] = '1024M';

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
 * 空闲键列表的Key
 */
$config['idle_key'] = '___idle___';

/*
 * 临界点
 * 当KEY数量超过此阈值的时候需要手动刷新
 * 设为0则关闭此功能
 */
$config['db_size_critical'] = 20000;

/*
 * 建树时每次读取的个数
 */
$config['tree_key_page_size'] = 10000;

/*
 * 检索空闲key时每次读取的个数 
 * 如为本机或本地局域网 建议甚至为1000-2000
 * 如为远程redis服务器，建议设置100-500
 */
$config['idle_key_page_size'] = 1000;
