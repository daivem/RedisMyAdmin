
$(function() {
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
	
	
  if (history.replaceState) {
	var params = GetRequest();
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


  $('#type').change(function(e) {
    $('#hkeyp' ).css('display', e.target.value == 'hash' ? 'block' : 'none');
    $('#indexp').css('display', e.target.value == 'list' ? 'block' : 'none');
    $('#scorep').css('display', e.target.value == 'zset' ? 'block' : 'none');
  }).change();


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
});

