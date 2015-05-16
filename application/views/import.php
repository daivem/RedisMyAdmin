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
<h2>导入数据</h2>
<form action="" method="post">
	<p>
		<label for="commands">命令行:<br>
			<br>
			<span class="info"> 可接受的命令:<br>
			SET<br>
			HSET<br>
			LPUSH<br>
			RPUSH<br>
			LSET<br>
			SADD<br>
			ZADD </span> </label>
		<textarea name="commands" id="commands" cols="80" rows="20"></textarea>
	</p>
	<p>
		<input type="submit" class="button" value="导入">
	</p>
</form>
</body>
</head>
