$(function($){
	$('#server').change(function(e) {
		_change_server();
  	});

	$('#redis_db').change(function(e) {
		_change_server();
	 });
	 
	var _change_server = function() {
		var server = $('#server').val();
		var db = $('#redis_db').val();
		var request = getRequest();
		request['server_id'] = server;
		request['db'] = db;
		  
		var url = 'http://' + location.host + location.pathname + '?';
		for(var i in request) {
			  
			url += i + ( (typeof request[i] == 'undefined' || request[i] == 'undefined') ? '' : '=' + request[i] );
			url += '&';
		}
		  
		url = url.substr(0, url.length - 1);
		location.href = url;
	}

	if (history.replaceState) {
		var params = getRequest();
		params['m'] = params['c'];
		params['c'] = 'index';
		var href = location.protocol + '\/\/' + location.host + location.pathname;
		var separator = '?';
		for(var key in params) {
			href += separator + key + '=' + params[key];
			separator = '&';
		}
		  
	    window.parent.history.replaceState({}, '', href);
	}
})

var getRequest = function() { 
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


var delTree = function(obj){
	if (confirm('确定要删除整个树及其所有的键吗？')) {
    		$.ajax({
				type: "POST",
				url: obj.href,
				data: {},
				timeout: 120000,
				success: function(url) {
					top.location.href = url;
				}
			});
	}
    return false;
}

var addDiyDom = function(treeId, treeNode) {
	if ( (typeof treeNode['isDir'] != 'undefined')
		&& ( treeNode['isDir'] )
	){
		var childrenCount = treeNode.children.length; 
		var aObj = $("#" + treeNode.tId + '_a');
		var urlExport = _global.baseUrl + '?c=export&m=index&key=' + treeNode.id + ':*&' + _global.urlParams;
		var urlDelete = _global.baseUrl + '?c=delete&m=index&tree=' + treeNode.id + ':&' + _global.urlParams;
		var editStr = '';
		editStr += " <i style='color:#aaa;'>(" + childrenCount + ")</i> ";
		editStr += "<a id='diyBtn1_" +treeNode.id+ "' title='导出当前节点' target='iframe'  href='" + urlExport + "' style='margin:0 0 0 5px;'><img src='static/images/export.png' style='width:10px;height:10px;' /></a>";
		editStr += "<a id='diyBtn2_" +treeNode.id+ "'  title='删除当前节点树' href='" + urlDelete + "' onclick='return delTree(this)' style='margin:0 0 0 5px;'><img src='static/images/delete.png' style='width:10px;height:10px;' /></a>";
		aObj.append(editStr);
	}
}


var redisAdmin = function(options){
	/*
		options = {
			//目标dom节点
			targetDom : string
			//基础URL
			baseUrl : string,
			//附加参数
			extraParams : string,
			//分割符
			seperator: ':'
		}
	 */
	this.treeNodeSetting = {
		pIdKey: 'pId',
		idKey: 'id',
		childKey: 'children',
		countKey: 'count'
	}

	this.formatedData = [];
	this.formatedDataForBuildTree = []
	this.options = options;

	this.keyList = [];

	this.existsNodeKey = {}
}

redisAdmin.prototype.buildTree = function() {
	var $obj = $("#" + this.options.targetDom);
	$obj.html('');

	var setting = {
		data: {
			simpleData: {
				enable: true
			}
		},
		view: {
			addDiyDom: addDiyDom
		}
	};

	_global.ztreeObj = $.fn.zTree.init($obj, setting, this.keyList);
	_global.inited = true;
}

redisAdmin.prototype.loadData = function(prefix) {
	var that = this;

	var _buildParentNood = function(arr) {
		var id = arr.join(that.options.seperator);
		if ( typeof that.existsNodeKey[id] != 'undefined' ) {
			return 
		}
		that.existsNodeKey[id] = 1;
		var name = arr.pop();
		var obj = {
			id: id,
			pId: arr.length > 0 ? arr.join(that.options.seperator) : -1,
			isDir: true,
			name: name
		}
		that.keyList.push(obj)
		if ( arr.length > 0 ) {
			_buildParentNood(arr);
		}
	}

	var _doLoadData = function(prefix, iterator) {
		$.ajax({
			type: "GET",
			url: that.options.baseUrl + "?c=key&key="+ prefix + '&' + that.options.extraParams,
			data: {"iterator": iterator},
			dataType: 'json',
			timeout: 10000,
			success: function(json) {
				if ( typeof json == 'object' ) {
					if (json.result == 0) {
						var data = json.data;
						var tempArr, name;
						for (var i in data['keys'] ) {
							tempArr = data['keys'][i].split(that.options.seperator)
							name = tempArr.pop();
							var obj = {
								id: data['keys'][i],
								pId: tempArr.length > 0 ? tempArr.join(that.options.seperator) : -1,
								name: name,
								isDir: false,
								target: "iframe",
								url: that.options.baseUrl + "?c=view&m=index&key="+ data['keys'][i] + '&' + that.options.extraParams
							}

							that.keyList.push(obj);
							if ( tempArr.length > 0 ) {
								_buildParentNood(tempArr);
							}
						}

						$("#current_loaded").html(that.keyList.length)

						if (( typeof data.iterator != 'undefined')
							&& ( data.iterator > 0 )
						){
							_doLoadData(prefix, data.iterator);
						} else {
							that.buildTree()
							//去锁
							_global.isLock = false;
						}
					} else {
						_global.isLock = false;
						alert('连接服务器时发生错误：' + json.msg);
					}
				} else {
					_global.isLock = false;
					alert('连接服务器出错。');
				}
			 	
			},
			error: function(){
				alert('连接服务器超时');
				_global.isLock = false;
			}
		});
	}

	if ( _global.isLock ) {
		return ;
	}
	_global.isLock = true;
	this.keyList = [{id: -1, pId: 0, name: "Keys"}];

	var iterator = -1;
	_doLoadData(prefix, iterator);
}
