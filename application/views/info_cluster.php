<!DOCTYPE html>
<html lang=en>
<head>
<meta charset=utf-8>
<?php if (is_ie()){ ?>
<meta http-equiv=X-UA-Compatible content="IE=edge,chrome=1">
<?php } ?>
<title><?= $title ?></title>
<link rel=stylesheet href="<?= base_url('static/css/common.css'); ?>" media=all>
<link rel=stylesheet href="<?= base_url('static/css/frame.css'); ?>" media=all>
<link rel="shortcut icon" href="<?= base_url('static/images/favicon.png'); ?>">
<script src="<?= base_url('static/js/jquery-1.7.2.min.js'); ?>" type="text/javascript"></script>
<script src="<?= base_url('static/js/frame.js'); ?>" type="text/javascript"></script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>

<body>
<?php
	foreach($infos as $name => $info) {
?>
<div class="server">
	<h2><?= $name ?></h2>
	<table>
		<tr>
			<th><div>Key</div></th>
			<th><div>Value</div></th>
		</tr>
		<?php
			$alt = FALSE;
			foreach ($info as $key => $value) {
				if ($key == 'allocation_stats') { 
					$value = str_replace(',', ",\n", $value);
				}
	
	  ?>
		<tr <?= $alt ? 'class="alt"' : ''?>>
			<td><div>
					<?= format_html($key)?>
				</div></td>
			<td><div>
					<?= nl2br(format_html($value))?>
				</div></td>
		</tr>
		<?php
			$alt = !$alt;
			}
	
		?>
	</table>
</div>
<?php		
	}
?>

</body>
</head>
