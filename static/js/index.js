
$(function() {
  $('#server').change(function(e) {
	  _change_server();
  });

  $('#redis_db').change(function(e) {
	  _change_server();
  });
  
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

  
  var _change_server = function() {
	  var server = $('#server').val();
	  var db = $('#redis_db').val();
	  
	  var request = GetRequest();
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

});


var bind_tree_event = function() {
	
	
	$('#filter').focus(function() {
		if ($(this).hasClass('info')) {
			$(this).removeClass('info').val('');
		}
	}).keyup(function() {
		var val = $(this).val();
	
		$('li:not(.folder)').each(function(i, el) {
			var key = $('a', el).get(0);
			var key = unescape(key.href.substr(key.href.indexOf('key=') + 4));
		
			if (key.indexOf(val) == -1) {
				$(el).addClass('hidden');
			} else {
				$(el).removeClass('hidden');
			}
		});	
	
		$('li.folder').each(function(i, el) {
			if ($('li:not(.hidden, .folder)', el).length == 0) {
				$(el).addClass('hidden');
			} else {
				$(el).removeClass('hidden');
			}
		});
	});
	
	
	setupTree();
}

function ajaxLoadTree($obj, step) {
	if ( ! step ) {
		step = 1;	
	}
	
	$("#waiting").show();
	$.ajax({
			type: "GET",
			url: "index.php?c=key&key="+$obj.attr("title") + '&' + _global.url_params,
			data: {"step": step},
			dataType: 'json',
			timeout: 120000,
			success: function(json) {
				if ( typeof json == 'object' ) {
					if (json.result == 0) {
						var data = json.data;
						$obj.append(data.html);
						if (( typeof data.isEnd != 'undefined')
							&& ( ! data.isEnd ) 
							&& ( data.step > 0 )
						){
							ajaxLoadTree($obj, data.step);
						} else {
							$("#waiting").hide();
							setupTree();
							//去锁
							_global.isLock = false;
						}
					}
				}
			 	
			},
			error: function(){
				alert('连接服务器超时');
				_global.isLock = false;
			}
	});
}

function reloadTree($jqueryObj) {
    //上锁
    _global.isLock = true;
    var id = $jqueryObj.attr("id");
    $("#" + id + ' > ul').html('');

    ajaxLoadTree($jqueryObj, 1)
}

function refreshTree(obj) {
    var $targetObj = $(obj).parent().parent();
    $targetObj.removeClass('collapsed');
    reloadTree($targetObj);
    return false;
}

function setupTree(){
	$('li.current').parents('li.folder').removeClass('collapsed');

	$('li.folder').unbind('click');
	$('li.folder').click(function(e) {
		//对于A标签不防止重复点击
		//主要用于在ajax加载列表的时候可以点击具体的KEY查看数据
		if (e.target.localName != 'a') {
			//判断锁 防止重复点击
			if ( _global.isLock ) {
				return false;	
			} 
		}
		var t = $(this);
		if(t.hasClass('collapsed')){
            reloadTree($(this));
		}
	
	
    	if ((e.pageY >= t.offset().top) &&
			(e.pageY <= t.offset().top + t.children('div').height())
		) {
      		//e.stopPropagation();
      		//t.toggleClass('collapsed');
			if ( t.hasClass('collapsed') ) {
				t.removeClass('collapsed');
				
			
			} else {
				t.addClass('collapsed');
				var id = t.attr("id");
				$("#" + id + ' > ul').html('');
			}
			
		}
	});

    
	$('#sidebar a:not(.deltree)').click(function(e) {

		e.preventDefault();
		
		var href;
		
		if ((e.currentTarget.href.indexOf('?') == -1) ||
			(e.currentTarget.href.indexOf('?') == (e.currentTarget.href.length - 1))) 
		{
			href = 'overview.php';
		} else {
			href = e.currentTarget.href;
		}
		
		$('#iframe').attr('src', href);
  });
	
	$('a').click(function() {
		$('li.current').removeClass('current');
	});
	
	$('li a').click(function() {
		$(this).parent().addClass('current');
	});
	
	$('.deltree').unbind('click');
	$('.deltree').click(function(e) {
		e.preventDefault();

		if (confirm('确定要删除整个树及其所有的键吗？')) {
    		$.ajax({
				type: "POST",
				url: this.href,
				data: {},
				timeout: 120000,
				success: function(url) {
					top.location.href = url;
				}
			});
    	}
        return false;
	});
}