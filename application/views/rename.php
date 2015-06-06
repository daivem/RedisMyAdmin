<?php PagerWidget::header(); ?>
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

<?php PagerWidget::footer(); ?>
