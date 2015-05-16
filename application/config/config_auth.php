<?php

/*
 * 是否开启登录校验
 */
$config['auth'] = FALSE;

/*
 * 可登录用户 
 */
$config['auth_user'] = array(
	// username => password
	'admin' => 'admin',
);

/*
 * 登录是否开启验证码
 */
$config['seccode_enable'] = FALSE;

/*
 * 验证码语言 en=英文  cn=中文
 */
$config['seccode_lang'] = 'en';

/*
 * 验证码字数 默认为4, 
 * 填0则随机3-6个
 * 调整此处注意调整图片尺寸
 */
$config['seccode_len'] = 4;

/*
 * 验证码配置
 */
$config['seccode_config'] = array(
		//图片宽度 默认150
		'width' => 150,
		
		//图片高度 默认60
		'height' => 60,
		
		//文字颜色 如#FF0000
		'textcolor' => '',
		
		//文字大小 默认25
		'textfontsize' => 25,
		
		//背景颜色
		'bgcolor' => '',
		
		//噪点个数 0 - 100
		'noisepoint' => 0,
		
		//干扰线条数 0-10
		'noiseline' => 0,
		
		//文字扭曲 0关闭 1开启 默认1
		'distortion' => 1,
		
		//字体文件路径
		'fontpath' =>  APPPATH .'../static/font',
		
		//编码
		'charset' => 'utf-8',
		
		//文字颜色都随机 0 每个文字颜色相同 1 每个文字颜色随机
		'contentrandomcolor' => 1,
);
