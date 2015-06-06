<?php PagerWidget::header(); ?>
<h2>
	导出数据
	<?= isset($key) ? format_html($key) : ''?>
</h2>
<form action="" method="post">
	<p>
		<label for="type">数据格式:</label>
		<select name="type" id="type">
			<option value="redis" <?= (isset($type) && ($type == 'redis')) ? 'selected="selected"' : ''?>>Redis</option>
			<option value="json"  <?= (isset($type) && ($type == 'json' )) ? 'selected="selected"' : ''?>>JSON</option>
		</select>
	</p>
	<p>
		<input type="submit" class="button" value="导出">
	</p>
</form>

<?php PagerWidget::footer(); ?>
