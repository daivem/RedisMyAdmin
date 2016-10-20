<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('show_message') )
{
	/**
	 * 
	 * 快速输出结果
	 * @param $result
	 * @param $msg
	 * @param $data
	 */
	function show_message($result, $msg = '', $data = array())
	{

		$ret_arr = array(
						'result' => $result,
						'msg' => $msg,
						'data' => empty($data) ? (object)array() : $data,
					);

		header( 'Content-type: text/javascript; charset=UTF-8' );
		if (isset( $_REQUEST['callback'] ) ) {
			$response = htmlspecialchars( $_REQUEST['callback'] ).'('.json_encode( $ret_arr ).');';
		} else {
			$response = json_encode( $ret_arr );
		}
		echo $response;
		exit;
	}
}

if ( ! function_exists('get_arg') )
{
	function get_arg($name, $default = NULL, $parser = NULL, $method = '')
	{
		$CI = &get_instance();
		$method = strtolower($method);
		if ( $method == 'get' || $method == 'g' ) {
			$val = $CI -> input -> get($name);
		} elseif ( $method == 'post' || $method == 'p' ) {
			$val = $CI -> input -> post($name);
		} else {
			$val = $CI -> input -> get_post($name);
		}
		if ( FALSE === $val ) {
			$val = $default;
		}
		else if ( NULL !== $parser ) {
			$val = $parser($val);
		}
		else if ( !is_array($val) ) {
			$val = trim($val); 
		}
		return $val;
	}
}

if ( ! function_exists('get_post_arg') )
{
	function get_post_arg($name, $default = NULL, $parser = NULL)
	{
		return get_arg($name, $default, $parser, 'post');
	}
}


if( ! function_exists('manager_site_url') )
{
	/**
	 *
	 * 返回URL
	 * @param $c
	 * @param $m
	 * @param $args
	 */
	function manager_site_url($c = 'index', $m = 'index', $args = ''){
		if ( ( ! empty($args) )
			&& ( is_array($args) )
		){
			$args = http_build_query($args);
		}
		
		$require_args = array(
			'db' => get_arg('db', 0, 'intval'),
			'prefix' => (string)get_arg('prefix', ''),
			'server_id' => get_arg('server_id', 0, 'intval'),
		);
		
		return site_url('c=' . $c . '&m=' . $m . '&' . http_build_query($require_args) . ( ( !empty($args) ) ? '&' . $args : '' ));
	}
}

if ( ! function_exists('get_custom_config') )
{
	function get_custom_config($config_file, $config_item)
	{
		$CI = & get_instance();
		$CI -> load -> config($config_file);
		return $CI -> config -> item($config_item);
	}
}

if (! function_exists('format_html') )
{
	function format_html($str) {
	    if ( version_compare(PHP_VERSION, '5.4.0', 'ge') ) { 
	        return htmlspecialchars($str, ENT_SUBSTITUTE + ENT_QUOTES);
	    } else {
	        return htmlspecialchars($str, ENT_QUOTES);
	    }   	
	}
}


if ( ! function_exists('is_ie') )
{
	function is_ie() {
		if ( ( isset($_SERVER['HTTP_USER_AGENT']) )
			&& ( strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== FALSE )
		){
			return true;
		} else {
			return FALSE;
		}
	}
}

if ( ! function_exists('format_size') )
{
	function format_size($size) {
		$sizes = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');

		if ($size == 0) {
			return '0 B';
		} else {
			return round($size / pow(1024, ($i = floor(log($size, 1024)))), 1).' '.$sizes[$i];
		}
	}
}


if ( ! function_exists('format_ago') )
{
	function format_ago($time, $ago = FALSE) {
		$minute = 60;
		$hour   = $minute * 60;
		$day    = $hour   * 24;

		$when = $time;

		if ($when >= 0)
			$suffix = '之前';
		else {
			$when = -$when;
			$suffix = '之后';
		}

		if ($when > $day) {
			$when = round($when / $day);
			$what = '天';
		} else if ($when > $hour) {
			$when = round($when / $hour);
			$what = '小时';
		} else if ($when > $minute) {
			$when = round($when / $minute);
			$what = '分钟';
		} else {
			$what = '秒';
		}



		if ($ago) {
			return "{$when} {$what} {$suffix}";
		} else {
			return "{$when} {$what}";
		}
	}
}

if ( ! function_exists('str_rand') )
{
	function str_rand($length) {
		$r = '';

		for (; $length > 0; --$length) {
			$r .= chr(rand(32, 126)); //32 - 126是可以被打印出来的accii码的范围
		}

		return $r;
	}
}


if ( ! function_exists('get_cur_url') ) 
{
	function get_cur_url() {
		$nowurl = '';
		if( ! empty($_SERVER['REQUEST_URI']) ) {
			$script_name = $_SERVER['REQUEST_URI'];
			$nowurl = $script_name;
		} else {
			$script_name = $_SERVER['PHP_SELF'];
			if( empty($_SERVER['QUERY_STRING']) ) {
				$nowurl = $script_name;
			} else {
				$nowurl = $script_name . '?' . $_SERVER['QUERY_STRING'];
			}
		}
		return $nowurl;
	}
}