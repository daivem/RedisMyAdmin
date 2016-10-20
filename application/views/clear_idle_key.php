<?php PagerWidget::header(); ?>
<h2>
<?= $title ?>
</h2>
<form id="id_form_idle" onSubmit="return false;">
	<div>
	当前db的Key总数为：<?= $db_size ?>
	<?php if ( $db_size >= 100000 ) { ?>
	数据较多，生成报表会较久
	<?php } ?>
	</div>
	<div>
	</div>
	<div>
			<lable>空闲时间(秒)：</lable>
			<input type="text" name="idle_time" id="id_idle_time" placeholder="" value="6">
			1天[86400] 3天[259200] 7天[604800] 30天[2592000]
	</div>
	<div>
			<lable>Key前缀过滤：</lable>
			<input type="text" name="key_prefix" id="id_key_prefix" placeholder="">
	</div>
	<div>
		<button id="id_search">检索</button>
		<button id="id_search_stop">停止检索</button>
		<button id="id_clear_idle_key" style="display:none;">删除所有满足条件的空闲Key</button>
	</div>
</form>
<p id="id_tips"></p>
<hr />
<div id="id_warning" style="display:none">
	<div>
		数据较多，只列出前5000条数据
	</div>
	<div>
		<button id="id_display_all">列出所有数据</button>
		（注：数据多，加载会比较慢，浏览器可能假死）
	</div>
</div>
<table id="id_list_table" style="display:none">
	<thead>
		<tr>
			<th>Key</th>
			<th>空闲时间(秒)</th>
		</tr>
	</thead>
	<tbody id="id_list_table_body">

	</tbody>
</table>
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

$(function(){
	function loadCompleteCallback(obj){
		if (obj.keyCnt > 5000) {
			$("#id_warning").show();
			obj.displayData(null, 5000)
		} else {
			obj.displayData(null, null);
		}
		$("#id_clear_idle_key").show();
	}

	function clearCompleteCallback(obj){
		alert('清理完毕by callback')
	}


	var control;
	$("#id_search").click(function(){
		var idle_time = parseInt($("#id_idle_time").val());
		var key_prefix = $("#id_key_prefix").val();
		if ( isNaN(idle_time) || (idle_time < 5) ) {
			return false;
		}


		control = new redisAdminIdle({
			prefix: key_prefix,
			tipsDom: 'id_tips',
			targetDom: 'keys_tree',
			baseUrl: '<?= base_url(); ?>',
			extraParams : _global.urlParams,
			tableDom: 'id_list_table',
			loadCompleteCallback: loadCompleteCallback
		})

		$("#id_warning").hide();
		$("#id_list_table").hide();
		$("#id_clear_idle_key").hide();
		_global.forceStop = false;
		control.loadData(idle_time)
	})

	$("#id_search_stop").click(function(){
		_global.forceStop = true;
	})
	
	$("#id_display_all").click(function(){
		control.displayData(null, null);
	})

	$("#id_clear_idle_key").click(function(){
		var idle_time = parseInt($("#id_idle_time").val());
		var key_prefix = $("#id_key_prefix").val();
		if ( isNaN(idle_time) || (idle_time < 5) ) {
			return false;
		}
		if (confirm('确认要按此设置清除空闲key吗？')) {
			control = new redisAdminIdle({
				prefix: key_prefix,
				tipsDom: 'id_tips',
				targetDom: 'keys_tree',
				baseUrl: '<?= base_url(); ?>',
				extraParams : _global.urlParams,
				tableDom: 'id_list_table',
				clearCompleteCallback: clearCompleteCallback
			})
			control.clearData(idle_time);
		} 
	})
})


</script>
<?php PagerWidget::footer(); ?>
