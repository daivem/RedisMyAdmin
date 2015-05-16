<!DOCTYPE html>
<html lang=en>
<head>
<meta charset=utf-8>
<?php if (is_ie()){ ?>
<meta http-equiv=X-UA-Compatible content="IE=edge,chrome=1">
<?php } ?>
<meta name=format-detection content="telephone=no">
<title>
<?= $title; ?>
</title>
<link rel=stylesheet href="<?= base_url('static/css/common.css'); ?>" media=all>
<link rel=stylesheet href="<?= base_url('static/css/index.css'); ?>" media=all>
<link rel="shortcut icon" href="<?= base_url('static/images/favicon.png'); ?>">
<script src="<?= base_url('static/js/jquery-1.7.2.min.js'); ?>" type="text/javascript"></script>
<script src="<?= base_url('static/js/index.js'); ?>" type="text/javascript"></script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>

<body>
<div id="sidebar">
	<h1 class="logo"><a href="<?= manager_site_url('overview', 'index'); ?>"><?= $project_name; ?></a></h1>
	<p>
		<select id="server">
			<?php foreach ($server_list as $i => $srv) { ?>
			<option value="<?= $i?>" <?= ($server_id == $i) ? 'selected="selected"' : ''?>>
			<?= isset($srv['name']) ? format_html($srv['name']) : $srv['host'].':'.$srv['port']?>
			</option>
			<?php } ?>
		</select>
		&nbsp;&nbsp;&nbsp;
		DB:
		<select id="redis_db">
			<?php for($i = 0; $i <= 20; $i++){?>
			<option value="<?= $i?>" <?= ($db == $i) ? 'selected="selected"' : ''?>>
			<?= $i; ?>
			</option>
			<?php }?>
		</select>
	</p>
	<p> 
	<a href="<?= manager_site_url('info', 'index'); ?>"><img src="<?= base_url('static/images/info.png'); ?>" width="16" height="16" title="环境信息" alt="环境信息"></a> 
	<a href="<?= manager_site_url('export', 'index'); ?>"><img src="<?= base_url('static/images/export.png'); ?>" width="16" height="16" title="导出所有数据" alt="导出所有数据"></a> 
	<a href="<?= manager_site_url('import', 'index'); ?>"><img src="<?= base_url('static/images/import.png'); ?>" width="16" height="16" title="导入数据" alt="导入数据"></a> 
	<a href="<?= manager_site_url('overview', 'index', 'viewall=1'); ?>"><img src="<?= base_url('static/images/favicon.png'); ?>" width="16" height="16" title="服务器一览表" alt="服务器一览表"></a> 
	<?php if (AUTH) { ?>
	<a href="<?= manager_site_url('login', 'logout'); ?>"><img src="<?= base_url('static/images/logout.png'); ?>" width="16" height="16" title="退出登录" alt="退出登录"></a> &nbsp;&nbsp;&nbsp;<img id="waiting" src='static/images/waiting.gif' class='waiting' style="display:none; width:16px; height:16px;"/> 
	<?php } ?>
	</p>
	<p> <a href="<?= manager_site_url('edit', 'index'); ?>" class="add">新增一个键</a> </p>
	<p> 在当前列表中查找：
		<input type="text" id="filter" size="24" value="关键字" class="info">
	</p>
	<p>
	<form onSubmit="return false">
		前缀筛选：
		<input type="text" name="prefix" id="prefix" value="<?= $prefix; ?>" />
		<input type="submit" onclick="goPrefix();" value="筛选" />
	</form>
	</p>
	<p></p>
	<p></p>
	<p></p>
	<div id="keys">
		<?php if ( $html_key_tree !== FALSE) { 
	echo $html_key_tree;
?>
		<script type="text/javascript">
	bind_tree_event();
</script>
		<?php 
} else {
?>
		<p> 当前数据库的KEY总量为：
			<?= $db_size ?>
			<br />
			到达服务器限制的临界值：
			<?= $db_size_critical; ?>
		</p>
		<input type="button" value="加载数据" onClick="loadTree(this)" />
		<?php } ?>
	</div>
	<!-- #keys -->
	
	<div id="frame">
		<iframe src="<?= format_html($iframe_url)?>" id="iframe" name="iframe" frameborder="0" scrolling="0"></iframe>
	</div>
	<!-- #frame --> 
	
</div>
<!-- #sidebar --> 

<script type="text/javascript">
var _global = _global || {};
<?php
$uri = $_SERVER['QUERY_STRING'];
$uri_arr = explode('&', $uri);
$unset_arr = array('c', 'm', 'key');
foreach($uri_arr as $k => $v) {
	list($_k, $_v) = explode('=', $v);
	if ( in_array($_k, $unset_arr)) {
		unset($uri_arr[$k]);
	}
}

?>
_global.url_params = '<?= implode('&', $uri_arr) ?>';
_global.isLock = false;
var loadTree = function(obj){
	if ( _global.isLock ) {
		return false;	
	} else {
		$(obj).val('加载中..');
		_global.isLock = true;
	}
	
	
	$.ajax({
		type:'GET',
		url:'<?= manager_site_url('index', 'get_key_tree');?>',
		data:{},
		dataType:'jsonp',
		timeout:60000,
		cache:false,
		error:function(json){
			alert('连接服务器超时');
			$(obj).val('加载数据');
			_global.isLock = false;			
		},
		success:function(json){
	
			_global.isLock = false;	
			if (typeof (json) == 'object') {
				if (json.result == 0) {
					$("#keys").html(json.data.html);
					bind_tree_event();
				} else {
					$(obj).val('加载数据');
					alert(json.msg);
					return false;
				}
			} else {
				$(obj).val('加载数据');
				alert('连接服务器出错。');
				return false;
			}
			
			
		}
	});	
}

function GetRequest() { 
   var url = location.search; //获取url中"?"符后的字串 
   var theRequest = new Object(); 
   if (url.indexOf("?") != -1) { 
	  var str = url.substr(1); 
	  strs = str.split("&"); 
	  for(var i = 0; i < strs.length; i ++) { 
		 theRequest[strs[i].split("=")[0]]=unescape(strs[i].split("=")[1]); 
	  } 
   } 
   return theRequest; 
} 	

var goPrefix = function() {
	var prefix = $("#prefix").val();
	var url = '<?= manager_site_url('index', 'index');?>';
	
	var params = GetRequest();
	params['m'] = params['c'];
	params['c'] = 'index';
	var href = location.protocol + '\/\/' + location.host + location.pathname;
	var separator = '?';
	for(var key in params) {
		if ( key == 'prefix' ) {
			continue;	
		}
		href += separator + key + '=' + params[key];
		separator = '&';
	}
	
	href += '&prefix=' + prefix;
	
	top.location.href = href;
}

</script>
</body>
</head>
