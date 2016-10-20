
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

	$('#type').change(function(e) {
	    $('#hkeyp' ).css('display', e.target.value == 'hash' ? 'block' : 'none');
	    $('#indexp').css('display', e.target.value == 'list' ? 'block' : 'none');
	    $('#scorep').css('display', e.target.value == 'zset' ? 'block' : 'none');
	}).change();

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


    $('.delkey, .delval').click(function(e) {
        e.preventDefault();

        if (confirm($(this).hasClass('delkey') ? '确认要删除这个KEY及其所有的值吗？' : '确认要删除这个值吗？')) {
            $.ajax({
                type: "POST",
                url: this.href,
                data: 'post=1',
                success: function(url) {
                    location.href = url;
                }
            });
        }
    });
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
			timeout: 30000,
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



var redisAdminIdle = function(options){
	/*
		options = {
			//前缀
			prefix : string
			//基础URL
			baseUrl : string,
			//附加参数
			extraParams : string,
			//表格的ID
			tableDom : string,
			//显示tips的Id
			tipsDom : string,
			//读取完数据后的callback
			loadCompleteCallback: func
			//清理完数据后的callback
			clearCompleteCallback: func	
		}
	 */
	this.options = {
		displayPerTime: 500,
		prefix: '',
		baseUrl: '',
		extraParams: '',
		tableDom: '',
		tipsDom: '',
		loadCompleteCallback: null,
		clearCompleteCallback: null
	}

	this.options = $.extend(this.options, options)
	if ( this.options.baseUrl == '' ) {
		alert('options error, baseUrl empty')
		return ;
	}
	if ( this.options.tableBodyDom == '' ) {
		alert('options error, tableBodyDom empty')
		return ;
	}

	this.keyMap = {}
	this.keyCnt = 0;
	if (this.options.tipsDom != '') {
		this.tipDomObj = $("#" + this.options.tipsDom)
	}
	this.tableObj = $("#" + this.options.tableDom);
	if (this.tableObj.length <= 0) {
		alert('没有找到表格标签');
		return ;
	}
	this.tableBodyOby = $("#" + this.options.tableDom + '_body');
	if (this.tableBodyOby.length <= 0) {
		alert('没有找到表格TBody标签');
		return ;
	}
}

redisAdminIdle.prototype.displayData = function(orderBy, displayCnt) {
	function _createTableBody(key, idle_time) {
		alt = ! alt;
		cls = alt ? ' class="alt"' : '';
		return '\
		<tr' + cls + '>\
			<td><div>' + key + '</div></td>\
			<td><div>' + idle_time + '</div></td>\
		</tr>';
	}

	this.tableBodyOby.empty();
	var alt = false;
	displayCnt = displayCnt == null ? -1 : displayCnt;

	if ( orderBy == 'key' ) {
		var keys = [];
		var key;
		for (key in this.keyMap) {
			keys.push(key)
		}

		var currentCnt = 0;
		var i;
		var cnt = 0;
		var _html = [];
		keys = keys.sort()
		for (i in keys) {
			cnt += 1;
			key = keys[i];
			_html.push(_createTableBody(key, this.keyMap[key]))
			if (cnt >= this.options.displayPerTime) {
				this.tableBodyOby.append(_html.join(""));
				cnt = 0;
				_html = [];
			}
			currentCnt += 1;
			if ( (displayCnt != -1) && (currentCnt >= displayCnt) ) {
				break;
			}
		}
		if (cnt > 0) {
			this.tableBodyOby.append(_html.join(""));
			cnt = 0;
			_html = [];
		}
	} else {
		var currentCnt = 0;
		var cnt = 0;
		var _html = [];
		var key;
		for (key in this.keyMap) {
			cnt += 1;
			_html.push(_createTableBody(key, this.keyMap[key]))
			if (cnt >= this.options.displayPerTime) {
				this.tableBodyOby.append(_html.join(""));
				cnt = 0;
				_html = [];
			}
			currentCnt += 1;
			if ( (displayCnt != -1) && (currentCnt >= displayCnt) ) {
				break;
			}
		}
		if (cnt > 0) {
			this.tableBodyOby.append(_html.join(""));
			cnt = 0;
			_html = [];
		}
	}
	this.tableObj.show();
}

redisAdminIdle.prototype.showTips = function(tipContent) {
	if (this.tipDomObj.length > 0) {
		this.tipDomObj.html(tipContent)
	}
}

redisAdminIdle.prototype.loadDataComplete = function() {
	if (typeof this.options.loadCompleteCallback == 'function' ) {
		this.options.loadCompleteCallback(this);
	} else {
		this.displayData();
	}
}


redisAdminIdle.prototype.loadData = function(idle_time){
	var that = this;
	that.keyCnt = 0;
	var requestTimes = 0;
	var _doLoadData = function(prefix, iterator, idle_time) {
		$.ajax({
			type: "GET",
			url: that.options.baseUrl + "?c=clear_idle_key&m=view_idle_key_list&" + that.options.extraParams,
			data: {
				iterator: iterator,
				idle_time: idle_time,
				key_prefix: prefix
			},
			dataType: 'json',
			timeout: 30000,
			success: function(json) {
				if ( typeof json == 'object' ) {
					if (json.result == 0) {
						if (_global.forceStop) {
							that.showTips('用户中止操作');
							//去锁
							_global.isLock = false;
							return ;
						}

						var data = json.data;
						var tempObj, name;
						that.keyCnt += data['idle_keys'].length;
						for (var i in data['idle_keys'] ) {
							tempObj = data['idle_keys'][i]
							that.keyMap[tempObj['k']] = tempObj['t'];
						}

						requestTimes += 1;
						that.showTips('已经完成' + requestTimes + '次请求，找到吻合条件数据:' + that.keyCnt);

						if (( typeof data.iterator != 'undefined')
							&& ( data.iterator > 0 )
						){
							_doLoadData(prefix, data.iterator, idle_time);
						} else {
							that.loadDataComplete();
							//去锁
							_global.isLock = false;
							return ;
						}
					} else {
						_global.isLock = false;
						alert('连接服务器时发生错误：' + json.msg);
						return ;
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

	var iterator = -1;
	_doLoadData(this.options.prefix, iterator, idle_time);
}

redisAdminIdle.prototype.clearDataComplete = function(){
	if (typeof this.options.clearCompleteCallback == 'function' ) {
		this.options.clearCompleteCallback(this);
	} else {
		alert('清理完毕');
	}	
}


redisAdminIdle.prototype.clearData = function(idle_time){
	var that = this;
	that.keyCnt = 0;
	var _doclearData = function(prefix, iterator, idle_time) {
		$.ajax({
			type: "GET",
			url: that.options.baseUrl + "?c=clear_idle_key&m=clear_keys&" + that.options.extraParams,
			data: {
				iterator: iterator,
				idle_time: idle_time,
				key_prefix: prefix
			},
			dataType: 'json',
			timeout: 10000,
			success: function(json) {
				if ( typeof json == 'object' ) {
					if (json.result == 0) {
						if (_global.forceStop) {
							that.showTips('用户中止操作');
							//去锁
							_global.isLock = false;
							return ;
						}

						var data = json.data;
						that.keyCnt += data['affected'];

						that.showTips('已清理Key数量:' + that.keyCnt);

						if (( typeof data.iterator != 'undefined')
							&& ( data.iterator > 0 )
						){
							_doclearData(prefix, data.iterator, idle_time);
						} else {
							that.clearDataComplete();
							//去锁
							_global.isLock = false;
							return ;
						}
					} else {
						_global.isLock = false;
						alert('连接服务器时发生错误：' + json.msg);
						return ;
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

	var iterator = -1;
	_doclearData(this.options.prefix, iterator, idle_time);
}