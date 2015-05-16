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
<h2>重命名
	<?= format_html($key)?>
</h2>
<form action="" method="post">
	<input type="hidden" name="old" value="<?= format_html($key)?>">
	<p>
		<label for="key">Key:</label>
		<input type="text" name="key" id="key" size="30"value="<?= format_html($key)?>">
	</p>
	<p>
		<input type="submit" class="button" value="重命名">
	</p>
</form>
</body>
</head>
