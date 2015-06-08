<?php PagerWidget::header(); ?>
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

<?php PagerWidget::footer(); ?>
