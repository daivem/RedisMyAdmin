<?php PagerWidget::header(); ?>
<div style="margin:0 auto; width:960px">
	<div style="width:300px; margin:0 auto; padding-top:50px;">
		<form method="POST" action="<?= manager_site_url('login', 'index'); ?>">
			<p>账　号：<input type="text" name="username" /></p>
			<p>密　码：<input type="password" name="password" /></p>
			<?php if ($seccode_enable) { ?>
			<p>
			验证码：<input type="text" id="seccode" name="seccode" />
			</p>
			<p>
	            <img src="about:blank" style="border:0; margin-left:5px;" 
		            onerror="document.getElementById('login_secImgTag').src='<?= manager_site_url('login', 'seccode'); ?>&'+Math.random();" 
		            title="换一个" id="login_secImgTag" 
        		    onClick="document.getElementById('login_secImgTag').src='<?= manager_site_url('login', 'seccode'); ?>&'+Math.random();"  /> 
			</p>
			<?php } ?>
			<p><input type="submit" value="登录" /></p>
			<?php if ($error) {?>
			<p>账号名或密码错！</p>
			<?php }?>
		</form>
	</div>

</div>
<script type="text/javascript">
if ( self != top ) {
	top.location.href = self.location.href;
}
</script>

<?php PagerWidget::footer(); ?>