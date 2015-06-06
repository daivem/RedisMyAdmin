<?php PagerWidget::header(); ?>
<h2><?= $title ?></h2>
<?php if ($can_reset) { ?>
<p> 
	<a href="<?= manager_site_url('info', 'index', 'reset=1'); ?>" class="reset" onclick="return confirm('确认要重置统计吗？')">重置统计</a> 
</p>
<?php } ?>
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

<?php PagerWidget::footer(); ?>
