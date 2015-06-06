<?php PagerWidget::header(); ?>
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
?><?php PagerWidget::footer(); ?>

