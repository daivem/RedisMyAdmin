<?php PagerWidget::header(); ?>

<div id="sidebar">
	<h1 class="logo"><a href="<?= manager_site_url('overview', 'index'); ?>"><?= $project_name; ?></a></h1>
	<p>
		<select id="server">
			<?php foreach ($server_list as $i => $srv) { ?>
			<option value="<?= $i?>" <?= (SERVER_ID == $i) ? 'selected="selected"' : ''?>>
			<?= isset($srv['name']) ? format_html($srv['name']) : $srv['host'].':'.$srv['port']?>
			</option>
			<?php } ?>
		</select>
		<?php if ( ! CLUSTER_MODE ) { ?>
		&nbsp;&nbsp;&nbsp;
		DB:
		<select id="redis_db">
			<?php for($i = 0; $i <= 20; $i++){?>
			<option value="<?= $i?>" <?= (CURRENT_DB == $i) ? 'selected="selected"' : ''?>>
			<?= $i; ?>
			</option>
			<?php }?>
		</select>
		<?php } else { ?>
		<input type="hidden" id="redis_db" value="0" />
		<?php } ?>
	</p>
	<p> 
		<a target="iframe" href="<?= manager_site_url('overview', 'index'); ?>"><img src="<?= base_url('static/images/calendar.gif'); ?>" width="16" height="16" title="概况" alt="概况"></a> 
		<a target="iframe" href="<?= manager_site_url('info', 'index'); ?>"><img src="<?= base_url('static/images/info.png'); ?>" width="16" height="16" title="详细信息" alt="详细信息"></a> 
		<a target="iframe" href="<?= manager_site_url('export', 'index'); ?>"><img src="<?= base_url('static/images/export.png'); ?>" width="16" height="16" title="导出所有数据" alt="导出所有数据"></a> 
		<a target="iframe" href="<?= manager_site_url('import', 'index'); ?>"><img src="<?= base_url('static/images/import.png'); ?>" width="16" height="16" title="导入数据" alt="导入数据"></a> 
		<a target="iframe" href="<?= manager_site_url('overview', 'index', 'viewall=1'); ?>"><img src="<?= base_url('static/images/favicon.png'); ?>" width="16" height="16" title="服务器一览表" alt="服务器一览表"></a> 	
		<!-- <a target="iframe" href="<?= manager_site_url('idle', 'index'); ?>"><img src="<?= base_url('static/images/zoom.png'); ?>" width="16" height="16" title="空闲key列表" alt="空闲key列表"></a>  -->
		<a target="iframe" href="<?= manager_site_url('clear_idle_key', 'index'); ?>"><img src="<?= base_url('static/images/zoom.png'); ?>" width="16" height="16" title="空闲key列表" alt="检索/删除 空闲key"></a> 
	<?php if (AUTH) { ?>
		<a href="<?= manager_site_url('login', 'logout'); ?>"><img src="<?= base_url('static/images/logout.png'); ?>" width="16" height="16" title="退出登录" alt="退出登录"></a> &nbsp;&nbsp;&nbsp;<img id="waiting" src='static/images/waiting.gif' class='waiting' style="display:none; width:16px; height:16px;"/> 
	<?php } ?>
	</p>
	<p> 
		<a target="iframe" href="<?= manager_site_url('edit', 'index'); ?>" class="add">新增一个键</a> 
	</p>
	<div>
	<form onSubmit="return false">
		查看指定Key：
		<input type="text" name="viewKey" id="viewKey" value="<?= isset($_GET['key']) ? $_GET['key'] : ''; ?>" />
		<input type="submit" onclick="doViewKey();" value="查看" />
	</form>
	</div>
	<p></p>
	<div>
	<form onSubmit="return false">
		筛选Key前缀：
		<input type="text" name="prefix" id="prefix" value="<?= $prefix; ?>" />
		<input type="submit" onclick="doViewPrefix();" value="筛选" />
	</form>
	</div>
	<div id="keys_tree" class="ztree">
	<?php 
		if ( ! $over_critical ) { 
	?>
	<script type="text/javascript">
		$(function($){
			startLoadData();
		})
	</script>

	<?php 
		} else {
	?>
		<p> 当前数据库的KEY总量为：
			<?= $db_size ?>
			<br />
			到达服务器限制的阈值：
			<?= $db_size_critical; ?>
		</p>
		<input type="button" id="btn_load_data" value="加载数据" onClick="startLoadData()" />
		<?php } ?>
		<div id="current_loaded"></div>
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
$uri_arr = array();
if ( ! empty($uri) ) {
	$uri_arr = explode('&', $uri);
	$unset_arr = array('c', 'm', 'key');
	foreach($uri_arr as $k => $v) {
		list($_k, $_v) = explode('=', $v);
		if ( in_array($_k, $unset_arr)) {
			unset($uri_arr[$k]);
		}
	}
}
?>
_global.urlParams = '<?= implode('&', $uri_arr) ?>';
_global.isLock = false;
_global.baseUrl = '<?= base_url(); ?>';
_global.inited = false;


var control = new redisAdmin({
	targetDom: 'keys_tree',
	baseUrl: '<?= base_url(); ?>',
	extraParams : _global.urlParams,
	seperator: '<?= SEPERATOR ?>'
})

function startLoadData() {
	if ( $("#btn_load_data").length > 0 ) {
		$("#btn_load_data").val('加载中..');
	}
	control.loadData('<?= KEY_PREFIX ?>');
}


var doViewKey = function() {
	var key = $("#viewKey").val();
	var url = '<?= manager_site_url('index', 'view');?>';
	
	url += '&key=' + key;
	
	top.location.href = url;
}

var doViewPrefix = function() {
	var prefix = $("#prefix").val();
	var url = '<?= manager_site_url('index', 'index');?>';
	
	url += '&prefix=' + prefix;
	
	top.location.href = url;
}

</script>

<?php PagerWidget::footer(); ?>
