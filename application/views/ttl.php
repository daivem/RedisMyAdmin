<!DOCTYPE html>
<html lang=en>
<head>
<meta charset=utf-8>
<?php if (is_ie()){ ?>
<meta http-equiv=X-UA-Compatible content="IE=edge,chrome=1">
<?php } ?>
<title><?= $title; ?></title>
<link rel=stylesheet href="<?= base_url('static/css/common.css'); ?>" media=all>
<link rel=stylesheet href="<?= base_url('static/css/frame.css'); ?>" media=all>
<link rel="shortcut icon" href="<?= base_url('static/images/favicon.png'); ?>">
<script src="<?= base_url('static/js/jquery-1.7.2.min.js'); ?>" type="text/javascript"></script>
<script src="<?= base_url('static/js/frame.js'); ?>" type="text/javascript"></script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>

<body>
<h2>修改生存期TTL</h2>
<form action="" method="post">
	<p>
		<label for="key">键:</label>
		<input type="text" name="key" id="key" size="30" readonly value="<?= format_html($key); ?>">
	</p>
	<p>
		<label for="ttl"><abbr title="键的生存期TTL">生存期(秒)</abbr>:</label>
		<input type="text" name="ttl" id="ttl" size="30" <?= format_html($key); ?>>
		<span class="info">(输入-1可移除生存期)</span> </p>
	<p>
		<input type="submit" class="button" value="修改">
	</p>
</form>
</body>
</head>
