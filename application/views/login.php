<!DOCTYPE html>
<html lang="en">
<head>
<?php if (is_ie()){ ?>
<meta http-equiv=X-UA-Compatible content="IE=edge,chrome=1">
<?php } ?>
<title><?= $title; ?></title>
<link rel="stylesheet" href="<?= base_url('static/css/common.css'); ?>" media="all">
<link rel="stylesheet" href="<?= base_url('static/css/frame.css'); ?>" media="all">
<link rel="stylesheet" href="<?= base_url('static/css/index.css'); ?>" media="all">
<link rel="shortcut icon" href="<?= base_url('static/images/favicon.png'); ?>">
<script src="<?= base_url('static/js/jquery-1.7.2.min.js'); ?>" type="text/javascript"></script>
<script src="<?= base_url('static/js/index.js'); ?>" type="text/javascript"></script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>

<body>
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