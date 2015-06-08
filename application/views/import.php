<?php PagerWidget::header(); ?>
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

<?php PagerWidget::footer(); ?>
